<?php

function getCoinMarketCap($lStart, $lNumRecords=200)
{
    $sURL = "https://pro-api.coinmarketcap.com/v1/cryptocurrency/listings/latest?start=".$lStart."&limit=".$lNumRecords;

    $headers = [
      'Accepts: application/json',
      'X-CMC_PRO_API_KEY: '.CMC_KEY
    ];


    $curl = curl_init(); // Get cURL resource
    // Set cURL options
    curl_setopt_array($curl, array(
      CURLOPT_URL => $sURL,            // set the request URL
      CURLOPT_HTTPHEADER => $headers,     // set the headers 
      CURLOPT_RETURNTRANSFER => 1         // ask for raw response instead of bool
    ));

    $response = curl_exec($curl); // Send the request, save the response
    curl_close($curl); // Close request

    return $response;
}

function cmcDexSanityTestBaseToken($tokenAddress)
{
    $base='https://pro-api.coinmarketcap.com/v4/dex/spot-pairs/latest';
    $token=strtolower(trim($tokenAddress));

    $out=[
        'base_asset_results'=>null,
        'quote_asset_results'=>null,
        'errors'=>[]
    ];

    $do=function($field,$token) use ($base)
    {
        $qs=http_build_query([
            'network_slug'=>'base',
            $field=>$token
        ]);
        $url=$base.'?'.$qs;

        $ch=curl_init($url);
        curl_setopt_array($ch,[
            CURLOPT_RETURNTRANSFER=>true,
            CURLOPT_HTTPHEADER=>[
                'Accept: application/json',
                'X-CMC_PRO_API_KEY: '.CMC_DEX_KEY
            ],
            CURLOPT_TIMEOUT=>10
        ]);
        $raw=curl_exec($ch);
        $err=curl_error($ch);
        $code=curl_getinfo($ch,CURLINFO_HTTP_CODE);
        curl_close($ch);

        if($err){ return ['ok'=>false,'error'=>'curl:'.$err]; }
        if($code<200||$code>=300){ return ['ok'=>false,'error'=>'http '.$code]; }

        $j=json_decode($raw,true);
        if($j===null){ return ['ok'=>false,'error'=>'json_decode']; }

        return ['ok'=>true,'data'=>$j['data']??[]];
    };

    $r1=$do('base_asset_contract_address',$token);
    if($r1['ok']){ $out['base_asset_results']=$r1['data']; }
    else{ $out['errors'][]='base_asset:'.$r1['error']; }

    $r2=$do('quote_asset_contract_address',$token);
    if($r2['ok']){ $out['quote_asset_results']=$r2['data']; }
    else{ $out['errors'][]='quote_asset:'.$r2['error']; }

    return $out;
}


function getCmcDexDataByToken($network, $tokenAddress, array $opts=[])
{
    $base='https://pro-api.coinmarketcap.com/v4/dex';
    $net=strtolower(trim($network));
    $token=strtolower(trim($tokenAddress));

    // Options
    $onlyUniswapV3=($opts['only_uniswap_v3']??true); // default: only Uniswap v3 pools
    $tradeLimit=intval($opts['trade_limit']??100);
    $ohlcvPeriod=strtolower($opts['ohlcv_period']??'hourly'); // 'hourly'|'daily'
    $ohlcvCount=intval($opts['ohlcv_count']??48);
    $timeout=intval($opts['timeout_sec']??12);

    // ---- small internal GET helper
    $get=function($path, array $params) use ($base,$timeout)
    {
        $qs=http_build_query($params);
        $url=$base.$path.'?'.$qs;

        $ch=curl_init($url);
        curl_setopt_array($ch,[
            CURLOPT_RETURNTRANSFER=>true,
            CURLOPT_FOLLOWLOCATION=>true,
            CURLOPT_CONNECTTIMEOUT=>$timeout,
            CURLOPT_TIMEOUT=>$timeout,
            CURLOPT_HTTPHEADER=>[
                'Accept: application/json',
                'X-CMC_PRO_API_KEY: '.CMC_DEX_KEY
            ]
        ]);
        $raw=curl_exec($ch);
        $err=curl_error($ch);
        $code=curl_getinfo($ch,CURLINFO_HTTP_CODE);
        curl_close($ch);

        if($err){ return ['ok'=>false,'code'=>$code,'error'=>'curl:'.$err,'raw'=>null]; }
        if($code<200||$code>=300){ return ['ok'=>false,'code'=>$code,'error'=>'http:'.$code,'raw'=>$raw]; }

        $j=json_decode($raw,true);
        if($j===null){ return ['ok'=>false,'code'=>$code,'error'=>'json_decode','raw'=>$raw]; }

        return ['ok'=>true,'code'=>$code,'data'=>$j];
    };

    // 1) Discover all spot pairs that include this token on the network
    //    (some datasets match when token is base or quote).
    $spotParams=[
        'network_slug'=>$net,
        'contract_address'=>$token
    ];
    if($onlyUniswapV3){
        // Commonly accepted filter key in examples; safe to include.
        $spotParams['dex_slug']='uniswapv3';
    }

    $pairsResp=$get('/spot-pairs/latest',$spotParams);
    if(!$pairsResp['ok']){
        return [
            'network'=>$net,
            'token'=>$token,
            'pairs'=>[],
            'errors'=>['spot_pairs:'.$pairsResp['error']]
        ];
    }

    $pairsList=$pairsResp['data']['data']??[];
    if(!is_array($pairsList)){$pairsList=[];}

    // 2) For each pair, pull quotes, ohlcv latest, trades, ohlcv historical
    $out=[
        'network'=>$net,
        'token'=>$token,
        'pairs'=>[],
        'errors'=>[]
    ];

    foreach($pairsList as $p){
        // Defensive reads (fields vary slightly across integrations)
        $pairAddr=$p['pair_address']??($p['pool_address']??null);
        if(!$pairAddr){ continue; }

        // Build a record
        $rec=[
            'pairAddress'=>$pairAddr,
            'dex_slug'=>$p['dex_slug']??null,
            'base_token'=>$p['base_token']??null,
            'quote_token'=>$p['quote_token']??null,
            'quotes'=>null,
            'ohlcv_latest'=>null,
            'trades'=>null,
            'ohlcv_history'=>null,
            'errors'=>[]
        ];

        $pairParams=[
            'network_slug'=>$net,
            'pair_address'=>$pairAddr,
            'contract_address'=>$pairAddr // include both keys for compatibility
        ];

        // Quotes
        $r=$get('/pairs/quotes/latest',$pairParams);
        if($r['ok']){ $rec['quotes']=$r['data']['data']??null; }
        else{ $rec['errors'][]='quotes_latest:'.$r['error']; }

        // OHLCV latest
        $r=$get('/pairs/ohlcv/latest',$pairParams+['time_period'=>$ohlcvPeriod]);
        if($r['ok']){ $rec['ohlcv_latest']=$r['data']['data']??null; }
        else{ $rec['errors'][]='ohlcv_latest:'.$r['error']; }

        // Trades latest
        $r=$get('/pairs/trade/latest',$pairParams+['limit'=>$tradeLimit]);
        if($r['ok']){ $rec['trades']=$r['data']['data']??null; }
        else{ $rec['errors'][]='trade_latest:'.$r['error']; }

        // OHLCV historical
        $histParams=$pairParams+['time_period'=>$ohlcvPeriod,'count'=>$ohlcvCount];
        $r=$get('/pairs/ohlcv/historical',$histParams);
        if($r['ok']){ $rec['ohlcv_history']=$r['data']['data']??null; }
        else{ $rec['errors'][]='ohlcv_historical:'.$r['error']; }

        $out['pairs'][]=$rec;
    }

    return $out;
}


function getCmcDexDataByPair($network, $pairAddress, array $opts=[])
{
    $base='https://pro-api.coinmarketcap.com/v4/dex';
    $net=strtolower(trim($network));
    $addr=trim($pairAddress);

    // Options
    $tradeLimit=intval($opts['trade_limit']??100);              // recent trades to request (API usually caps ~100)
    $ohlcvPeriod=strtolower($opts['ohlcv_period']??'hourly');   // 'hourly'|'daily'
    $ohlcvCount=intval($opts['ohlcv_count']??48);               // bars to request for historical
    $timeout=intval($opts['timeout_sec']??12);

    $out=[
        'pairAddress'=>$addr,
        'network'=>$net,
        'quotes'=>null,
        'ohlcv_latest'=>null,
        'trades'=>null,
        'ohlcv_history'=>null,
        'errors'=>[]
    ];

    // ---- small internal GET helper
    $get=function($path, array $params) use ($base,$timeout)
    {
        $qs=http_build_query($params);
        $url=$base.$path.'?'.$qs;

        $ch=curl_init($url);
        curl_setopt_array($ch,[
            CURLOPT_RETURNTRANSFER=>true,
            CURLOPT_FOLLOWLOCATION=>true,
            CURLOPT_CONNECTTIMEOUT=>$timeout,
            CURLOPT_TIMEOUT=>$timeout,
            CURLOPT_HTTPHEADER=>[
                'Accept: application/json',
                'X-CMC_PRO_API_KEY: '.CMC_DEX_KEY
            ]
        ]);
        $raw=curl_exec($ch);
        $err=curl_error($ch);
        $code=curl_getinfo($ch,CURLINFO_HTTP_CODE);
        curl_close($ch);

        if($err){ return ['ok'=>false,'code'=>$code,'error'=>'curl:'.$err,'raw'=>null]; }
        if($code<200||$code>=300){ return ['ok'=>false,'code'=>$code,'error'=>'http:'.$code,'raw'=>$raw]; }

        $j=json_decode($raw,true);
        if($j===null){ return ['ok'=>false,'code'=>$code,'error'=>'json_decode','raw'=>$raw]; }

        return ['ok'=>true,'code'=>$code,'data'=>$j];
    };

    // Shared params (be generous with both keys that CMC samples use)
    $pairParams=[
        'network_slug'=>$net,
        'pair_address'=>$addr,
        'contract_address'=>$addr
    ];

    // 1) Quotes (price, liquidity, 24h vol, tokens)
    $r=$get('/pairs/quotes/latest',$pairParams);
    if($r['ok'])
    {
        // normalize to first item if data is an array
        $data=$r['data']['data']??null;
        if(is_array($data))
        {
            $out['quotes']=$data[0]??$data;
        }
        else
        {
            $out['quotes']=$data;
        }
    }
    else
    {
        $out['errors'][]='quotes_latest:'.$r['error'];
    }

    // 2) OHLCV latest (intra-day snapshot)
    $r=$get('/pairs/ohlcv/latest',$pairParams+['time_period'=>$ohlcvPeriod]);
    if($r['ok'])
    {
        $out['ohlcv_latest']=$r['data']['data']??null;
    }
    else
    {
        $out['errors'][]='ohlcv_latest:'.$r['error'];
    }

    // 3) Trades (most recent)
    $r=$get('/pairs/trade/latest',$pairParams+['limit'=>$tradeLimit]);
    if($r['ok'])
    {
        $out['trades']=$r['data']['data']??null;
    }
    else
    {
        $out['errors'][]='trade_latest:'.$r['error'];
    }

    // 4) OHLCV historical (short lookback)
    // You can also pass explicit ISO timestamps via time_start/time_end
    $histParams=$pairParams+[
        'time_period'=>$ohlcvPeriod,
        'count'=>$ohlcvCount
    ];
    $r=$get('/pairs/ohlcv/historical',$histParams);
    if($r['ok'])
    {
        $out['ohlcv_history']=$r['data']['data']??null;
    }
    else
    {
        $out['errors'][]='ohlcv_historical:'.$r['error'];
    }

    return $out;
}

function getCmcTopFeeGenerators(array $networks = ['ethereum', 'base', 'arbitrum'], int $limit = 50, array $opts = [])
{
    $base = 'https://pro-api.coinmarketcap.com/v4/dex';
    $timeout = intval($opts['timeout_sec'] ?? 15);
    $onlyUniswapV3 = ($opts['only_uniswap_v3'] ?? true);
    $minVolume24h = floatval($opts['min_volume_24h'] ?? 10000); // Minimum $10k daily volume
    
    // Internal GET helper
    $get = function($path, array $params) use ($base, $timeout) {
        $qs = http_build_query($params);
        $url = $base . $path . '?' . $qs;
        
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_CONNECTTIMEOUT => $timeout,
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'X-CMC_PRO_API_KEY: ' . CMC_DEX_KEY
            ]
        ]);
        
        $raw = curl_exec($ch);
        $err = curl_error($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($err) { 
            return ['ok' => false, 'error' => 'curl:' . $err, 'data' => null]; 
        }
        if ($code < 200 || $code >= 300) { 
            return ['ok' => false, 'error' => 'http:' . $code, 'data' => null]; 
        }
        
        $j = json_decode($raw, true);
        if ($j === null) { 
            return ['ok' => false, 'error' => 'json_decode', 'data' => null]; 
        }
        
        return ['ok' => true, 'data' => $j];
    };
    
    $allPairs = [];
    $errors = [];
    
    foreach ($networks as $network) {
        $networkSlug = strtolower(trim($network));
        
        // Get spot pairs for this network
        $params = [
            'network_slug' => $networkSlug,
            'limit' => $limit * 2 // Get more to filter later
        ];
        
        if ($onlyUniswapV3) {
            $params['dex_slug'] = 'uniswapv3';
        }
        
        $pairsResp = $get('/spot-pairs/latest', $params);
        
        if (!$pairsResp['ok']) {
            $errors[] = "Network $networkSlug: " . $pairsResp['error'];
            continue;
        }
        
        $pairs = $pairsResp['data']['data'] ?? [];
        if (!is_array($pairs)) {
            continue;
        }
        
        foreach ($pairs as $pair) {
            $pairAddr = $pair['pair_address'] ?? ($pair['pool_address'] ?? null);
            if (!$pairAddr) continue;
            
            // Get detailed quotes for this pair
            $quoteParams = [
                'network_slug' => $networkSlug,
                'pair_address' => $pairAddr
            ];
            
            $quoteResp = $get('/pairs/quotes/latest', $quoteParams);
            
            if (!$quoteResp['ok']) {
                continue;
            }
            
            $quoteData = $quoteResp['data']['data'] ?? null;
            if (is_array($quoteData)) {
                $quoteData = $quoteData[0] ?? $quoteData;
            }
            
            if (!$quoteData) continue;
            
            // Extract key metrics
            $volume24h = floatval($quoteData['volume_24h'] ?? 0);
            $liquidity = floatval($quoteData['liquidity'] ?? 0);
            $price = floatval($quoteData['price'] ?? 0);
            
            // Skip low volume pairs
            if ($volume24h < $minVolume24h) {
                continue;
            }
            
            // Calculate estimated fees (assuming 0.3% for Uniswap V3 as default)
            $feeTier = floatval($pair['fee_tier'] ?? 0.003); // Default 0.3%
            $estimatedFees24h = $volume24h * $feeTier;
            
            // Get OHLCV data for additional metrics
            $ohlcvResp = $get('/pairs/ohlcv/latest', $quoteParams + ['time_period' => 'hourly']);
            $ohlcvData = null;
            if ($ohlcvResp['ok']) {
                $ohlcvData = $ohlcvResp['data']['data'] ?? null;
            }
            
            $pairRecord = [
                'network' => $networkSlug,
                'pair_address' => $pairAddr,
                'dex_slug' => $pair['dex_slug'] ?? 'unknown',
                'base_token' => [
                    'symbol' => $pair['base_token']['symbol'] ?? 'UNKNOWN',
                    'name' => $pair['base_token']['name'] ?? '',
                    'address' => $pair['base_token']['contract_address'] ?? ''
                ],
                'quote_token' => [
                    'symbol' => $pair['quote_token']['symbol'] ?? 'UNKNOWN',
                    'name' => $pair['quote_token']['name'] ?? '',
                    'address' => $pair['quote_token']['contract_address'] ?? ''
                ],
                'metrics' => [
                    'price_usd' => $price,
                    'volume_24h' => $volume24h,
                    'liquidity' => $liquidity,
                    'fee_tier' => $feeTier,
                    'estimated_fees_24h' => $estimatedFees24h,
                    'fee_to_liquidity_ratio' => $liquidity > 0 ? ($estimatedFees24h / $liquidity * 100) : 0,
                    'price_change_24h' => floatval($quoteData['price_change_24h'] ?? 0),
                    'volume_change_24h' => floatval($quoteData['volume_change_24h'] ?? 0)
                ],
                'ohlcv_latest' => $ohlcvData,
                'last_updated' => $quoteData['last_updated'] ?? date('c'),
                'pair_created_at' => $pair['pair_created_at'] ?? null
            ];
            
            $allPairs[] = $pairRecord;
        }
        
        // Add small delay between network requests
        usleep(200000); // 200ms
    }
    
    // Sort by estimated fees (highest first)
    usort($allPairs, function($a, $b) {
        return $b['metrics']['estimated_fees_24h'] <=> $a['metrics']['estimated_fees_24h'];
    });
    
    // Return top results
    $topPairs = array_slice($allPairs, 0, $limit);
    
    return [
        'success' => true,
        'total_pairs_found' => count($allPairs),
        'pairs_returned' => count($topPairs),
        'networks_searched' => $networks,
        'filters_applied' => [
            'min_volume_24h' => $minVolume24h,
            'only_uniswap_v3' => $onlyUniswapV3
        ],
        'pairs' => $topPairs,
        'errors' => $errors,
        'generated_at' => date('c')
    ];
}

function getCmcDexNetworkSlugs()
{
    $url = "https://pro-api.coinmarketcap.com/v4/dex/networks/list";

    $headers = [
        "Accepts: application/json",
        "X-CMC_PRO_API_KEY: " . CMC_DEX_KEY
    ];

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_RETURNTRANSFER => true
    ]);

    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpcode !== 200 || $response === false) {
        return [];
    }

    $data = json_decode($response, true);

    print_r($data); die;

    if (!isset($data['data']) || !is_array($data['data'])) {
        return [];
    }

    $slugs = [];
    foreach ($data['data'] as $network) {
        if (isset($network['network_slug'])) {
            $slugs[] = $network['network_slug'];
        }
    }

    return $slugs;
}

function getCMCpairs($networkSlug='base', $limit=5)
{
    $url = "https://pro-api.coinmarketcap.com/v4/dex/spot-pairs/latest?network_slug=".urlencode($networkSlug)."&dex_slug=uniswap-v3-base&limit=".intval($limit);

    $headers = [
        "Accepts: application/json",
        "X-CMC_PRO_API_KEY: " . CMC_DEX_KEY
    ];

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 15
    ]);

    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpcode !== 200 || $response === false) {
        return [
            'success' => false,
            'error' => "HTTP $httpcode",
            'pairs' => []
        ];
    }

    $data = json_decode($response, true);

    print_r($data); die;

}
