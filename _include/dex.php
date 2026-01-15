<?php

function is_valid_eth_address($address)
{
    // Ethereum addresses are 42 characters (0x + 40 hex chars)
    if (strlen($address) !== 42)
    {
        return false;
    }

    // Must start with 0x
    if (substr($address, 0, 2) !== '0x')
    {
        return false;
    }

    // Must be valid hex characters
    if (!ctype_xdigit(substr($address, 2)))
    {
        return false;
    }

    return true;
}

function getDexPrice($contractAddress)
{
    $contractAddress = strtolower(trim($contractAddress));

    // Build Dex Screener API URL
    $url = "https://api.dexscreener.com/latest/dex/tokens/" . urlencode($contractAddress);

    // Make HTTP request to Dex Screener API
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Accept: application/json',
            'User-Agent: PHP-DexScreener-Client/1.0'
        ],
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => 5
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    $arrData = json_decode($response, true);

    $bestPair = null;
    $highestLiquidity = 0;

    $arrUniswap = array();
    $pLargestLiquidity = 0;
    $pPriceToUse = 0;
    $arrReturn = array();
    foreach ($arrData['pairs'] as $pair)
    {
        //print_r($pair);

        $pLiquidity = floatval($pair['liquidity']['usd'] ?? 0);
        $pPrice = floatval($pair['priceUsd'] ?? 0);
        $sCurrentAddress = $pair["baseToken"]["address"];
        $sCurrentSymbol = $pair["baseToken"]["symbol"];
        $sCurrentName = $pair["baseToken"]["name"];

        //if ($pair['dexId'] == "uniswap" && $pair["labels"][0] == "v3" && strtolower($pair["baseToken"]["address"]) == strtolower($contractAddress))
        if ($pair['dexId'] == "uniswap" && strtolower($sCurrentAddress) == strtolower($contractAddress))
        {
            if ($pLiquidity > $pLargestLiquidity)
            {
                $arrReturn["price"] = $pPrice;
                $arrReturn["symbol"] = $sCurrentSymbol;
                $arrReturn["name"] = $sCurrentName;
                $arrReturn["liquidity"] = $pLiquidity;
            }

            //echo $pLiquidity." | ".$pPrice.chr(10);
        }
    }

    return $arrReturn;

}
