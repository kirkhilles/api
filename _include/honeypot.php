<?php

function checkTokenRiskUnified($token, $network)
{
    $chainMap = [
        'ethereum' => 1,
        'bnb' => 56,
        'bsc' => 56,
        'base' => 8453,
        'polygon' => 137,
        'arbitrum one' => 42161,
        'arbitrum' => 42161,
        'optimism' => 10
    ];

    $n = strtolower(trim($network));
    if(!isset($chainMap[$n])) {
        return ['ok'=>false,'source'=>null,'network'=>$network,'chain_id'=>null,'data'=>null,'summary'=>null,'error'=>'Unknown network "'.$network.'"','http_code'=>null];
    }
    $chainId = $chainMap[$n];

    if(!preg_match('/^0x[a-fA-F0-9]{40}$/', $token)) {
        return ['ok'=>false,'source'=>null,'network'=>$network,'chain_id'=>$chainId,'data'=>null,'summary'=>null,'error'=>'Invalid token address','http_code'=>null];
    }

    // 1) Try Honeypot.is first
    $hp = callHoneypot($token, $chainId);
    if($hp['ok']) {
        return [
            'ok'=>true,
            'source'=>'honeypot',
            'network'=>$network,
            'chain_id'=>$chainId,
            'data'=>$hp['data'],
            'summary'=>summarizeFromHoneypot($hp['data']),
            'error'=>null,
            'http_code'=>$hp['http_code']
        ];
    }

    // 2) Fallback to GoPlus
    $gp = callGoPlus($token, $chainId);
    if($gp['ok']) {
        return [
            'ok'=>true,
            'source'=>'goplus',
            'network'=>$network,
            'chain_id'=>$chainId,
            'data'=>$gp['data'],
            'summary'=>summarizeFromGoPlus($gp['data'], $token),
            'error'=>null,
            'http_code'=>$gp['http_code']
        ];
    }

    // 3) Both failed
    return [
        'ok'=>false,
        'source'=>null,
        'network'=>$network,
        'chain_id'=>$chainId,
        'data'=>['honeypot_error'=>$hp['error'],'goplus_error'=>$gp['error']],
        'summary'=>null,
        'error'=>'Both Honeypot and GoPlus failed',
        'http_code'=>null
    ];
}

function isHoneypot($token, $network)
{
    $risk = checkTokenRiskUnified($token, $network);

    // Default: unknown = 50
    $score = 50;

    if ($risk['ok'] && $risk['summary']) {
        $s = $risk['summary'];

        // If either source says it's a honeypot or can't sell, max risk
        if (
            (isset($s['is_honeypot']) && $s['is_honeypot'] === true) ||
            (isset($s['can_sell']) && $s['can_sell'] === false)
        ) {
            $score = 100;
        } else {
            // If both taxes are low and can_sell is true, low risk
            $buyTax  = isset($s['buy_tax'])  ? floatval($s['buy_tax'])  : null;
            $sellTax = isset($s['sell_tax']) ? floatval($s['sell_tax']) : null;

            if ($s['can_sell'] === true && $buyTax !== null && $sellTax !== null && $buyTax < 10 && $sellTax < 10) {
                $score = 0;
            } elseif ($s['can_sell'] === true && ($buyTax > 20 || $sellTax > 20)) {
                $score = 60;
            } elseif ($s['can_sell'] === true && ($buyTax > 10 || $sellTax > 10)) {
                $score = 30;
            } else {
                $score = 50;
            }
        }
    } elseif (!$risk['ok'] && isset($risk['data'])) {
        // If both APIs failed, return 50 (unknown)
        $score = 50;
    }

    return $score;
}

function callHoneypot($token, $chainId)
{
    $qs = http_build_query(['address'=>strtolower($token), 'chainID'=>$chainId]);
    $url = 'https://api.honeypot.is/v2/IsHoneypot?'.$qs;

    $headers = ['Accept: application/json', 'User-Agent: token-risk/1.0'];
    $hpKey = getenv('HONEYPOT_API_KEY');
    if($hpKey && $hpKey!=='') {
        $headers[] = 'X-API-KEY: '.$hpKey;
    }

    $res = httpGet($url, $headers);
    if($res['err']!==null) {
        return ['ok'=>false,'data'=>null,'error'=>'cURL: '.$res['err'],'http_code'=>null];
    }
    $code = $res['code'];
    $json = json_decode($res['body'], true);
    if($code>=200 && $code<300 && is_array($json)) {
        return ['ok'=>true,'data'=>$json,'error'=>null,'http_code'=>$code];
    }
    return ['ok'=>false,'data'=>null,'error'=>'HP HTTP '.$code.' '.substr($res['body'],0,200),'http_code'=>$code];
}

function callGoPlus($token, $chainId)
{
    $url = 'https://api.gopluslabs.io/api/v1/token_security/'.$chainId.'?contract_addresses='.strtolower($token);

    $headers = ['Accept: application/json', 'User-Agent: token-risk/1.0'];
    $gpKey = getenv('GOPLUS_API_KEY');
    if($gpKey && $gpKey!=='') {
        $headers[] = 'Authorization: Bearer '.$gpKey;
    }

    $res = httpGet($url, $headers);
    if($res['err']!==null) {
        return ['ok'=>false,'data'=>null,'error'=>'cURL: '.$res['err'],'http_code'=>null];
    }
    $code = $res['code'];
    $json = json_decode($res['body'], true);

    if($code>=200 && $code<300 && is_array($json)) {
        // GoPlus wraps results; extract token key if present
        return ['ok'=>true,'data'=>$json,'error'=>null,'http_code'=>$code];
    }
    return ['ok'=>false,'data'=>null,'error'=>'GP HTTP '.$code.' '.substr($res['body'],0,200),'http_code'=>$code];
}

function httpGet($url, $headers)
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_CONNECTTIMEOUT => 8,
        CURLOPT_HTTPHEADER => $headers
    ]);
    $body = curl_exec($ch);
    $err  = curl_error($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return ['body'=>$body, 'err'=>$err?:null, 'code'=>$code?:0];
}

/* ---------- Normalizers ---------- */

function summarizeFromHoneypot(array $hp)
{
    // Honeypot payload fields can vary; we read defensively.
    $dexType = $hp['honeypotResult']['router']['name'] ?? ($hp['pair']['type'] ?? null);
    $isHp    = $hp['honeypotResult']['isHoneypot'] ?? null;

    // Taxes & gas (if simulated)
    $buyTax  = $hp['simulationResult']['buyTax']  ?? ($hp['tax']['buy'] ?? null);
    $sellTax = $hp['simulationResult']['sellTax'] ?? ($hp['tax']['sell'] ?? null);

    // Practical “can I sell?” heuristic from sim fields
    $canSell = null;
    if(isset($hp['simulationResult']['canSell'])) {
        $canSell = (bool)$hp['simulationResult']['canSell'];
    } elseif(isset($hp['honeypotResult']['isHoneypot'])) {
        $canSell = !$hp['honeypotResult']['isHoneypot'];
    }

    return [
        'source'=>'honeypot',
        'is_honeypot'=>$isHp,
        'buy_tax'=>$buyTax,
        'sell_tax'=>$sellTax,
        'liquidity_type'=>$dexType, // e.g., "UniswapV3" if provided
        'can_sell'=>$canSell,
        'notes'=>'Simulation-based result'
    ];
}

function summarizeFromGoPlus(array $gp, $token)
{
    // GoPlus returns data keyed by lowercase contract in data.result or data.<chain>
    $lower = strtolower($token);
    $node = null;

    // Common container shapes in GoPlus
    if(isset($gp['result']) && is_array($gp['result'])) {
        // sometimes result is an array of objects
        if(isset($gp['result'][$lower])) {
            $node = $gp['result'][$lower];
        } elseif(is_array($gp['result']) && count($gp['result'])===1) {
            $node = array_values($gp['result'])[0];
        }
    } elseif(isset($gp['data']) && is_array($gp['data'])) {
        if(isset($gp['data'][$lower])) {
            $node = $gp['data'][$lower];
        } elseif(is_array($gp['data']) && count($gp['data'])===1) {
            $node = array_values($gp['data'])[0];
        }
    }

    $isHp    = isset($node['is_honeypot']) ? ( (string)$node['is_honeypot'] === '1' ) : null;
    $buyTax  = $node['buy_tax']  ?? null;
    $sellTax = $node['sell_tax'] ?? null;

    // DEX info (if present) often under 'dex' or 'liquidity'
    $liquidityType = null;
    if(isset($node['dex']['liquidity_type'])) {
        $liquidityType = $node['dex']['liquidity_type'];  // e.g., "UniV3"
    } elseif(isset($node['liquidity_type'])) {
        $liquidityType = $node['liquidity_type'];
    }

    // GoPlus has no live sim, so we infer can_sell:
    // if flagged honeypot OR sell_tax >= 50% OR transfer_pausable/blacklist -> can_sell=false (heuristic)
    $canSell = null;
    if($isHp===true) {
        $canSell = false;
    } else {
        $pausable   = isset($node['transfer_pausable']) && ((string)$node['transfer_pausable']==='1');
        $blacklisted= isset($node['is_blacklisted']) && ((string)$node['is_blacklisted']==='1');
        if($pausable || $blacklisted) {
            $canSell = false;
        } elseif(is_numeric($sellTax) && (float)$sellTax>=50.0) {
            $canSell = false;
        }
    }

    return [
        'source'=>'goplus',
        'is_honeypot'=>$isHp,
        'buy_tax'=>$buyTax,
        'sell_tax'=>$sellTax,
        'liquidity_type'=>$liquidityType, // e.g., "UniV3"
        'can_sell'=>$canSell,
        'notes'=>'Static contract analysis (broad EVM coverage)'
    ];
}

function checkValues($a, $b)
{
    // Ensure inputs are within 0–100
    if($a < 0 || $a > 100 || $b < 0 || $b > 100) {
        throw new InvalidArgumentException("Values must be between 0 and 100");
    }

    if($a > 50 || $b > 50) {
        return true;
    }
    return false;
}