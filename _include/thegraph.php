<?php

function getPools(string $sNetwork, $startDate = null, $bDebug = false)
{
    global $arrSubgraph, $connTRADING;

    $dNow = date("Y-m-d H:i:00");

    $first = 1000;
    $sURL = sprintf(
        'https://gateway.thegraph.com/api/%s/subgraphs/id/%s',
        GRAPH_API_KEY,
        $arrSubgraph[$sNetwork]
    );

    // Handle start date parameter
    if ($startDate !== null)
    {
        if (is_string($startDate))
        {
            // If string, convert to timestamp
            $lTimeStart = strtotime($startDate);
            if ($lTimeStart === false)
            {
                throw new InvalidArgumentException("Invalid date format: $startDate");
            }
        } elseif (is_numeric($startDate))
        {
            // If numeric, treat as timestamp
            $lTimeStart = (int)$startDate;
        } else
        {
            throw new InvalidArgumentException("Start date must be a string or timestamp");
        }
        // Use the provided start date for database insert (with :00 seconds)
        $dInsertDate = date("Y-m-d H:i:00", $lTimeStart);
    } else
    {
        // Default: 1 hour ago
        $lTimeStart = time() - (1 * 60 * 60);
        // Use current time for database insert
        $dInsertDate = $dNow;
    }

    $gql = <<<'GQL'
    query($first: Int!, $lTimeStart: Int!) 
    {
        poolHourDatas
        (
            first: $first
            where: { periodStartUnix_gte: $lTimeStart, feesUSD_gt: 1 }
            orderBy: feesUSD
            orderDirection: desc
        )
        {
            periodStartUnix
            volumeUSD
            feesUSD
            txCount
            pool
            {
                id
                token0 { id symbol }
                token1 { id symbol }
                feeTier
                totalValueLockedUSD
            }
        }
    }
    GQL;

    $payload = json_encode([
        'query'     => $gql,
        'variables' => [
            'first' => $first,
            'lTimeStart' => $lTimeStart
        ]
    ]);

    $ch = curl_init($sURL);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
        CURLOPT_POSTFIELDS     => $payload,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT        => 20,
    ]);

    $response = curl_exec($ch);

    if ($response === false)
    {
        throw new RuntimeException('Curl error: ' . curl_error($ch));
    }

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200)
    {
        throw new RuntimeException("Graph returned HTTP $httpCode: $response");
    }

    $arrData = json_decode($response, true);

    if ($bDebug || is_array(@$arrData["data"]["poolHourDatas"]) == false)
    {
        echo "Response: $response\n";
    }

    // Initialize bulk insert variables
    $lBulkCount = 0;
    $sSQLbase = "REPLACE INTO crypto.uniswaphourly 
        (HRsAddress, HRdDate, HRsNetwork, HRnFees, HRnVolume, HRnLocked, HRnCount, HRnRatio) 
        VALUES ";
    $sSQLvalues = "";

    foreach ($arrData["data"]["poolHourDatas"] as $arrCurrent)
    {
        $dDate = date("Y-m-d H:i:s", $arrCurrent["periodStartUnix"]);
        $pVolumeUSD = $arrCurrent["volumeUSD"];
        $pFeesUSD = $arrCurrent["feesUSD"];
        $lTXcount = $arrCurrent["txCount"];
        $lTotalValueLockedUSD = $arrCurrent["pool"]["totalValueLockedUSD"];
        $pPercent = $arrCurrent["pool"]["feeTier"] / 100 / 100;
        $sAddress = $arrCurrent["pool"]["id"];
        $sToken0symbol = $arrCurrent["pool"]["token0"]["symbol"];
        $sToken0address = $arrCurrent["pool"]["token0"]["id"];
        $sToken1symbol = $arrCurrent["pool"]["token1"]["symbol"];
        $sToken1address = $arrCurrent["pool"]["token1"]["id"];

        $pRatio = 0;
        if ($lTotalValueLockedUSD > 0)
        {
            $pRatio = $pFeesUSD / $lTotalValueLockedUSD * 100 * 10;
        }

        if ($pRatio > 9999)
        {
            $pRatio = 9999;
        }

        $sSQL = "SELECT COUNT(*) as lCount FROM uniswappools WHERE POLsAddress = ".SQLit($sAddress);
        $result = $connTRADING->query($sSQL);

        $lCount = 0;
        while ($row = $result->fetch_assoc())
        {
            $lCount = $row["lCount"];
        }

        if ($lCount == 0)
        {
            $sSQL = "INSERT INTO uniswappools (POLsAddress, POLsNetwork, POLsSymbol1, POLsSymbol2, ".
            "POLnPercent, POLdAdded, POLsSymbol1address, POLsSymbol2address) ".
            " VALUES (".
            SQLit($sAddress).
            ", ".SQLit($sNetwork).
            ", ".SQLit($sToken0symbol).
            ", ".SQLit($sToken1symbol).
            ", ".($pPercent).
            ", '".date("Y-m-d H:i:00")."' ".
            ", ".SQLit($sToken0address).
            ", ".SQLit($sToken1address).
            ")";
            $connTRADING->query($sSQL);
        }

        // Add to bulk insert values
        $sSQLvalues .= " (".
            SQLit($sAddress).", ".
            SQLit($dInsertDate).", ".
            SQLit($sNetwork).", ".
            ($pFeesUSD).", ".
            ($pVolumeUSD).", ".
            ($lTotalValueLockedUSD).", ".
            ($lTXcount).", ".
            ($pRatio).
            "), ";

        $lBulkCount++;

        // Execute bulk insert every 1,000 records
        if ($lBulkCount >= 100)
        {
            // Remove trailing comma and space
            $sSQLvalues = substr($sSQLvalues, 0, strlen($sSQLvalues) - 2);
            $sSQLinsert = $sSQLbase.$sSQLvalues;

            echo "Inserting ".$lBulkCount." Records...\n";

            if (!$result = getData($sSQLinsert, $connTRADING))
            {
                echo "ERROR: ".$sSQLinsert."<br>".mysqli_error($connTRADING);
                die();
            }

            // Reset for next batch
            $lBulkCount = 0;
            $sSQLvalues = "";
        }
    }

    // Execute any remaining records
    if ($sSQLvalues != "")
    {
        echo "Inserting Remaining Records...\n";

        $sSQLvalues = substr($sSQLvalues, 0, strlen($sSQLvalues) - 2);
        $sSQLinsert = $sSQLbase.$sSQLvalues;

        if (!$result = getData($sSQLinsert, $connTRADING))
        {
            echo "ERROR: ".$sSQLinsert."<br>".mysqli_error($connTRADING);
            die();
        }
    }

    return true;
}

function getPoolsPast24Hours(string $sNetwork)
{
    // Loop from 24 hours ago up to 1 hour ago (not including the current hour)
    for ($i = 24; $i >= 1; $i--)
    {
        // Calculate the start date for this hour (set minutes and seconds to 00)
        $timestamp = strtotime("-$i hour");
        $startDate = date("Y-m-d H:00:00", $timestamp);

        try
        {
            getPools($sNetwork, $startDate);
            // Optional: echo progress
            // echo "Processed $sNetwork at $startDate\n";
        } catch (Exception $e)
        {
            // Optional: log or echo errors
            // echo "Error for $sNetwork at $startDate: " . $e->getMessage() . "\n";
        }
    }
}

function getPoolsForHours(string $sNetwork, $startDate = null, int $hours = 24)
{
    // Determine the starting timestamp
    if ($startDate !== null)
    {
        if (is_string($startDate))
        {
            $startTimestamp = strtotime($startDate);
            if ($startTimestamp === false)
            {
                throw new InvalidArgumentException("Invalid date format: $startDate");
            }
        } elseif (is_numeric($startDate))
        {
            $startTimestamp = (int)$startDate;
        } else
        {
            throw new InvalidArgumentException("Start date must be a string or timestamp");
        }
    } else
    {
        // Default: $hours ago from now
        $startTimestamp = strtotime("-$hours hour");
    }

    // Loop for each hour
    for ($i = 0; $i < $hours; $i++)
    {
        $timestamp = $startTimestamp + ($i * 3600);
        $hourDate = date("Y-m-d H:00:00", $timestamp);

        try
        {
            getPools($sNetwork, $hourDate);
            // Optional: echo progress
            // echo "Processed $sNetwork at $hourDate\n";
        } catch (Exception $e)
        {
            // Optional: log or echo errors
            // echo "Error for $sNetwork at $hourDate: " . $e->getMessage() . "\n";
        }
    }
}

function importTradingHistoryForPools($sAddress = '')
{
    global $connTRADING;

    // Get all pools that haven't been imported yet
    $sSQL = "SELECT POLsAddress, POLsNetwork FROM uniswappools WHERE POLbImported = 0";
    if ($sAddress != "")
    {
        $sSQL = "SELECT POLsAddress, POLsNetwork FROM uniswappools WHERE POLbImported = 0 AND POLsAddress = " . SQLit($sAddress) . " LIMIT 1";
    }
    $result = getData($sSQL, $connTRADING);

    if (!$result)
    {
        echo "ERROR: " . $sSQL . "<br>" . mysqli_error($connTRADING);
        die;
    }

    $processedCount = 0;
    $errorCount = 0;

    while ($row = getResultB($result))
    {
        $poolAddress = $row["POLsAddress"];
        $network = $row["POLsNetwork"];

        try
        {
            echo "Processing pool: $poolAddress on $network...\n";

            // Get trading history for this pool
            $historyResult = getTradingHistoryByAddress($network, $poolAddress);

            if ($historyResult['success'] && !empty($historyResult['history']))
            {
                // Insert each day's data into uniswapdaily table
                foreach ($historyResult['history'] as $dayData)
                {
                    // Calculate ratio (fees to TVL ratio)
                    $ratio = 0;
                    if ($dayData['tvl_usd'] > 0)
                    {
                        $ratio = ($dayData['fees_usd'] / $dayData['tvl_usd']) * 100 * 10;
                    }
                    if ($ratio > 9999)
                    {
                        $ratio = 9999;
                    }

                    // Check if this record already exists
                    $checkSQL = "SELECT COUNT(*) as lCount FROM uniswapdaily 
                                WHERE HRsAddress = " . SQLit($poolAddress) . " 
                                AND HRdDate = " . SQLit($dayData['date']) . " 
                                AND HRsNetwork = " . SQLit($network);

                    $checkResult = getData($checkSQL, $connTRADING);
                    if (!$checkResult)
                    {
                        echo "ERROR checking existing record: " . $checkSQL . "<br>" . mysqli_error($connTRADING);
                        continue;
                    }

                    $checkRow = getResultB($checkResult);
                    $existingCount = $checkRow["lCount"];

                    // Only insert if record doesn't exist
                    if ($existingCount == 0)
                    {
                        $insertSQL = "INSERT INTO uniswapdaily 
                            (HRdDate, HRsAddress, HRsNetwork, HRnFees, HRnVolume, HRnLocked, HRnCount, HRnRatio) 
                            VALUES (
                                " . SQLit($dayData['date']) . ",
                                " . SQLit($poolAddress) . ",
                                " . SQLit($network) . ",
                                " . floatval($dayData['fees_usd']) . ",
                                " . floatval($dayData['volume_usd']) . ",
                                " . floatval($dayData['tvl_usd']) . ",
                                " . intval($dayData['tx_count']) . ",
                                " . floatval($ratio) . "
                            )";

                        if (!getData($insertSQL, $connTRADING))
                        {
                            echo "ERROR inserting daily data: " . $insertSQL . "<br>" . mysqli_error($connTRADING);
                            continue;
                        }
                    }
                }

                // Mark pool as imported
                $updateSQL = "UPDATE uniswappools 
                             SET POLbImported = 1 
                             WHERE POLsAddress = " . SQLit($poolAddress) . " 
                             AND POLsNetwork = " . SQLit($network);

                if (!getData($updateSQL, $connTRADING))
                {
                    echo "ERROR updating import status: " . $updateSQL . "<br>" . mysqli_error($connTRADING);
                } else
                {
                    $processedCount++;
                    echo "Successfully imported " . count($historyResult['history']) . " days for $poolAddress\n";
                }

            } else
            {
                echo "No trading history found for $poolAddress on $network\n";

                // Still mark as imported to avoid retrying
                $updateSQL = "UPDATE uniswappools 
                             SET POLbImported = 1 
                             WHERE POLsAddress = " . SQLit($poolAddress) . " 
                             AND POLsNetwork = " . SQLit($network);
                getData($updateSQL, $connTRADING);
            }

        } catch (Exception $e)
        {
            echo "ERROR processing $poolAddress on $network: " . $e->getMessage() . "\n";
            $errorCount++;
        }

        // Add small delay to avoid rate limiting
        usleep(100000); // 100ms delay
    }

    $sSQL = "UPDATE uniswappools
        SET POLnDayCount = (SELECT COUNT(*) FROM uniswapdaily WHERE HRsAddress = POLsAddress)
        WHERE POLnDayCount IS NULL OR POLnDayCount = 0";
    getData($sSQL, $connTRADING);

    echo "\nImport completed. Processed: $processedCount pools. Errors: $errorCount\n";
    return ['processed' => $processedCount, 'errors' => $errorCount];
}

function getTradingHistoryByAddress(string $network, string $contractAddress)
{
    $days = 1000;

    $arrSubgraph = [
        'Base'          => 'HMuAwufqZ1YCRmzL2SfHTVkzZovC9VL2UAKhjvRqKiR1',
        'Ethereum'      => '5zvR82QoaXYFyDEKLZ9t6v9adgnptxYpKpSbxtgVENFV',
        'BNB'           => 'G5MUbSBM7Nsrm9tH2tGQUiAF4SZDGf2qeo1xPLYjKr7K',
        'Polygon'       => '3hCPRGf4z88VC5rsBKU5AA9FBBq5nF3jbKJG7VZCbhjm',
        'Optimism'      => 'Cghf4LfVqPiFw6fp6Y5X5Ubc8UpmUhSfJL82zwiBFLaj',
        'Arbitrum One'  => 'FbCGRftH4a3yZugY7TnbYgPJVEv2LvMT6oF1fxPe9aJM',
    ];

    if (!isset($arrSubgraph[$network]))
    {
        throw new InvalidArgumentException("Unsupported network: $network");
    }

    $subgraphId = $arrSubgraph[$network];
    $address = strtolower($contractAddress);

    // Calculate the timestamp for $days ago (start of day UTC)
    $fromTimestamp = strtotime(date('Y-m-d 00:00:00', strtotime("-$days days UTC")));

    $gql = <<<'GQL'
query($address: String!, $from: Int!, $first: Int!) {
  poolDayDatas(
    first: $first
    orderBy: date
    orderDirection: desc
    where: { pool: $address, date_gte: $from }
  ) {
    date
    volumeUSD
    feesUSD
    txCount
    tvlUSD
    pool {
      id
      token0 { symbol }
      token1 { symbol }
      token0Price
      token1Price
      feeTier
    }
  }
}
GQL;

    $url = "https://gateway.thegraph.com/api/" . GRAPH_API_KEY . "/subgraphs/id/" . $subgraphId;
    $payload = json_encode([
        'query' => $gql,
        'variables' => [
            'address' => $address,
            'from' => $fromTimestamp,
            'first' => $days
        ]
    ]);

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
        CURLOPT_POSTFIELDS     => $payload,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT        => 20,
    ]);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response === false || $httpCode !== 200)
    {
        return [
            'success' => false,
            'error' => 'GraphQL error or HTTP ' . $httpCode,
            'history' => []
        ];
    }

    $data = json_decode($response, true);
    if (!isset($data['data']['poolDayDatas']))
    {
        return [
            'success' => false,
            'error' => 'No data returned',
            'history' => []
        ];
    }

    $history = [];
    foreach ($data['data']['poolDayDatas'] as $row)
    {
        $history[] = [
            'date' => date('Y-m-d', $row['date']),
            'volume_usd' => $row['volumeUSD'],
            'fees_usd' => $row['feesUSD'],
            'tx_count' => $row['txCount'],
            'tvl_usd' => $row['tvlUSD'],
            'token0' => $row['pool']['token0']['symbol'],
            'token1' => $row['pool']['token1']['symbol'],
            'token0_price' => $row['pool']['token0Price'],
            'token1_price' => $row['pool']['token1Price'],
            'fee_tier' => $row['pool']['feeTier'],
        ];
    }

    return [
        'success' => true,
        'history' => $history,
        'address' => $contractAddress,
        'network' => $network
    ];
}
