<?php

function get_coingecko_price($tokenAddress, $network = 'bnb')
    {
    static $cache = [];

    $networks = get_network_config();
    if (!isset($networks[$network]))
        {
        return null;
    }

    $platform = $networks[$network]['coingecko_platform'];
    $normalizedAddress = strtolower($tokenAddress);
    $cacheKey = strtolower($network . ':' . $normalizedAddress);

    if (array_key_exists($cacheKey, $cache))
        {
        return $cache[$cacheKey];
    }

    $price = get_coingecko_price_simple($platform, $normalizedAddress);

    if ($price === null)
        {
        $price = get_coingecko_price_from_contract($platform, $normalizedAddress);
    }

    if ($price !== null)
        {
        $cache[$cacheKey] = $price;
    }

    return $price;
}

function get_coingecko_price_simple($platform, $tokenAddress)
    {
    if (!$platform || !$tokenAddress)
        {
        return null;
    }

    $url = "https://api.coingecko.com/api/v3/simple/token_price/{$platform}?contract_addresses={$tokenAddress}&vs_currencies=usd&x_cg_demo_api_key=" . COINGECKO_KEY;

    // Retry logic for rate limiting - CoinGecko may return empty data instead of error codes
    $maxRetries = 3;
    $baseDelay = 1000000; // 1 second in microseconds

    for ($attempt = 1; $attempt <= $maxRetries; $attempt++)
        {
        $data = coingecko_fetch_json($url);

        print_r($data); // Debugging line to see the response

        if ($data && isset($data[$tokenAddress]['usd']))
            {
            return (float)$data[$tokenAddress]['usd'];
        }

        // If we got data but it's empty/null, this might be rate limiting
        // Wait before retrying
        if ($attempt < $maxRetries)
       
           {
            $delay = $baseDelay * $attempt * 5; // Progressive delay: 1s, 2s, 3s
            error_log("CoinGecko rate limit detected for {$tokenAddress} on {$platform}, attempt {$attempt}/{$maxRetries}, retrying in " . ($delay / 1000000) . "s");
            usleep($delay);
        } else
            {
            error_log("CoinGecko failed to return price for {$tokenAddress} on {$platform} after {$maxRetries} attempts");
        }
    }

    return null;
}

function get_coingecko_price_from_contract($platform, $tokenAddress)
    {
    if (!$platform || !$tokenAddress)
        {
        return null;
    }

    $url = sprintf(
        'https://api.coingecko.com/api/v3/coins/%s/contract/%s?localization=false&tickers=false&market_data=true&community_data=false&developer_data=false&sparkline=false&x_cg_demo_api_key=%s',
        rawurlencode($platform),
        rawurlencode($tokenAddress),
        COINGECKO_KEY
    );

    // Retry logic for rate limiting - CoinGecko may return empty data instead of error codes
    $maxRetries = 3;
    $baseDelay = 1000000; // 1 second in microseconds

    for ($attempt = 1; $attempt <= $maxRetries; $attempt++)
        {
        $data = coingecko_fetch_json($url);

        if ($data && isset($data['market_data']['current_price']['usd']) && is_numeric($data['market_data']['current_price']['usd']))
            {
            return (float)$data['market_data']['current_price']['usd'];
        }

        // If we got data but it's empty/null, this might be rate limiting
        // Wait before retrying
        if ($attempt < $maxRetries)
            {
            $delay = $baseDelay * $attempt; // Progressive delay: 1s, 2s, 3s
            error_log("CoinGecko contract rate limit detected for {$tokenAddress} on {$platform}, attempt {$attempt}/{$maxRetries}, retrying in " . ($delay / 1000000) . "s");
            usleep($delay);
        } else
            {
            error_log("CoinGecko contract API failed to return price for {$tokenAddress} on {$platform} after {$maxRetries} attempts");
        }
    }

    return null;
}

function get_token_price_usd($tokenAddress, $symbol, $network = 'bnb')
{
    $symbol = strtoupper(trim($symbol));
    
    // Known stablecoins - always $1.00
    $stablecoins = ['USDT', 'USDC', 'BUSD', 'DAI', 'TUSD', 'USDD', 'FRAX'];
    if (in_array($symbol, $stablecoins))
        {
        return 1.0;
    }
    
    // Fetch price from CoinGecko API
    $price = get_coingecko_price($tokenAddress, $network);
    
    if ($price !== null)
        {
        return $price;
    }
    
    // If CoinGecko fails, return null
    return null;
}