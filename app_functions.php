<?php
ini_set("error_reporting", E_ALL ^ E_STRICT);
ini_set("display_errors", "1");
ini_set("display_startup_errors", "1");

date_default_timezone_set("America/New_York");

require_once("_include/honeypot.php");
require_once("_include/thegraph.php");
require_once("_include/cmc.php");
require_once("_include/nodereal.php");
require_once("_include/dex.php");
require_once("_include/vector.php");

use Aws\Ses\SesClient;
use Twilio\Rest\Client;

$dStart = explode(" ", microtime());
$dStart = $dStart[1] + $dStart[0];

$dNow = date("Y-m-d H:i:s");

$sHost = "N/A";
if (isset($_SERVER["HTTP_HOST"]))
{
    $sHost = strtolower($_SERVER["HTTP_HOST"]);
    $sHost = str_replace("www.", "", $sHost);
}

define("ANTHROPIC_KEY", "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");
define("CMC_KEY", "e2943d21-14a6-48de-8c9f-33283054f68f");
define("OPENAI_KEY", "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");
//define("GEMINI_API", "AIzaSyA1Ld4oAkt_1J883Tegg57dEHmVS43pmQ8");
//define("GEMINI_API", "AIzaSyDxytAVXDIV9FWt-H0qAPVYAV47PzsHeSg");
define("GEMINI_API", "AIzaSyA0LQ-w4pzFEc-lJJX54EOF4IHCJNV9AMs");   //Chatbot Project
define("METAMASK_ADDRESS", "0x8A4A21D001102A5DbeEc06495Cf040F6bE420Bf4");
define("METAMASK_INVESTMENT_ADDRESS", "0x99d831E1C7068b10449197142D4b3F58C04a1aD5");
define("ALCHEMY_KEY", "YefmyLCrqqT7g5KW-kvygIRl3EIOPMVn");
define("GRAPH_API_KEY", "53fed5960a79caab5b32a009c5bfbfc4");
define("COINGECKO_KEY", "CG-JffweX8q63Ndg1VKPrF1hamh");
define("CMC_DEX_KEY", "e2943d21-14a6-48de-8c9f-33283054f68f");
define("MORALIS_KEY", "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJub25jZSI6ImQyZjA1Y2NlLTQ1NjYtNGE4ZS04ZjRkLThiNDNlYjBiZjA3OSIsIm9yZ0lkIjoiNDQ4NDAxIiwidXNlcklkIjoiNDYxMzUxIiwidHlwZUlkIjoiNmUwMTAyYTgtMGU2MC00NTE5LThhZjMtOTU0YzQ5OGJiZTc2IiwidHlwZSI6IlBST0pFQ1QiLCJpYXQiOjE3NDc4NTYyNDMsImV4cCI6NDkwMzYxNjI0M30.gt3WtG-dK4piCCHDF3iXJ58vJUwkOJ3czPDT5GNIcO4");
define("OPENAI_KEY_V2", "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");

define("CONTACT_EMAIL", "sales@peakgps.com");

//define("FROM_EMAIL", "noreply@thriftygps.com");
define("FROM_EMAIL", "sales@peakgps.com");

//define("SERVER_LINUX_IP", "http://208.67.1.147");
define("SERVER_LINUX_IP", "http://161.35.116.131");


define("SESSION_EXPIRE", 60 * 60 * 24 * 30);  // Cache for 30 Days

define("DB_MASTER", "159.65.233.238");
define("DB_MASTER_PASSWORD", "WHHnphr&TeJfw8P#&n");

//print_r($_SERVER);

require_once("vendor/autoload.php");
require_once("Twilio/autoload.php");

//-------------------------------------------------------------
$oMemcached = new Memcached();
if (count($oMemcached->getServerList()) < 1)
{
    $oMemcached->addServer('127.0.0.1', 11211);
}
//-------------------------------------------------------------


$sPageWidth = 1000;
$sTableWidth = 800;
$lCellSpacing = 10;
$sFontSize = 4;
//if ($bIsMobile)
//{
$sPageWidth = "100%";
$sTableWidth = "100%";
$sFontSize = 5;
//}

define("sAmazonAccessKey", "0DT4CPJD3DXVBM1MDZR2");
define("sAmazonAssociateTag", "webillusions-20");
define("sAmazonSecretKey", "5sgSs7dIt3NHYARsxP2pXSlHAaQuY7UGMo2uVrYj");

define('awsAccessKey', '0DT4CPJD3DXVBM1MDZR2');
define('awsSecretKey', '5sgSs7dIt3NHYARsxP2pXSlHAaQuY7UGMo2uVrYj');

//Original Key
//define("GOOGLE_MAPS_KEY", "AIzaSyDSdQQ3uivuNV0mddswjyfAWjVKZ1_LZ6o");

//New Paid Key
define("GOOGLE_MAPS_KEY", "AIzaSyCjua-dHfCnTHKa7PkmgRzPg2NhfMdSY6A");

//Original Key
//define("GOOGLE_MAPS_JAVASCRIPT_KEY", "AIzaSyDmlD-TeIZb0A4NNgvqv-xTpaNo_qWRB8c");
define("GOOGLE_MAPS_JAVASCRIPT_KEY", "AIzaSyCjua-dHfCnTHKa7PkmgRzPg2NhfMdSY6A");


define("PLACE_DISTANCE", 0.1);

define("CLASS_SUBMIT_BUTTON", "btn btn-primary btn-lg");
define("CLASS_NAV_BUTTONS", "btn btn-warning");
define("CLASS_EDIT_BUTTONS", "btn btn-info");
define("CLASS_RED_BUTTONS", "btn btn-danger");
define("CLASS_YELLOW_BUTTONS", "btn btn-warning");
define("CLASS_GRAY_BUTTONS", "btn btn-secondary");
define("CLASS_LIGHT_BUTTONS", "btn btn-light");
define("CLASS_DARK_BUTTONS", "btn btn-dark");
define("CLASS_GREEN_BUTTONS", "btn btn-success");

define("CLASS_TABLE_INNER", "table table-striped table-hover");
define("CLASS_TABLE_EDIT", "table");

/* !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! */
/* BOT EXCLUSION */
/* !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! */

$sID = @$_REQUEST["sID"];
$lID = @$_REQUEST["lID"];

// ---------------------------------------------
$connMISCDB = getConnectionTRADING("miscdb");

$sSQL = "SELECT * FROM apikeys";
$result = getData($sSQL, $connMISCDB);

while ($row = getResultB($result))
{
    $sName = $row["APIsName"];
    $sKey = $row["APIsKey"];

    if ($sName == "OPEN_ROUTER_KEY") define("OPEN_ROUTER_KEY", $sKey);
}

//echo $sName." | ".$sKey;

mysqli_close($connMISCDB);
// ---------------------------------------------

function geminiSummarizeForIndex($sAPIkey, $sModelName, $sPrompt, $sResponse)
{
    $schema='{
        "search":{
            "title":"<80 chars>",
            "summary":"<<=500 chars, plain text>",
            "keywords":["k1","k2"],
            "topics":["t1","t2"],
            "entities":[{"type":"ORG","text":"Acme"}],
            "importance":0,
            "lang":"en"
        },
        "assistant_summary":{
            "notes":"<=240 chars capturing what the ASSISTANT just did/said",
            "followups":["optional, explicit questions or TODOs the assistant gave"],
            "topics":["short tokens for areas the assistant addressed"]
        },
        "profile_update":{
            "interests_add":["optional"],
            "prefs_add":["optional style/behavior preferences"],
            "stable_facts_add":["optional long-term facts about user"],
            "recent_focus_add":["optional current projects/themes"]
        }
    }';

    $sInstruction=
        "You are generating conversation metadata for retrieval, context, and a user profile.\n".
        "Given ORIGINAL_PROMPT (user) and MODEL_RESPONSE (assistant), output ONE minified JSON object with this schema:\n".
        $schema."\n\n".
        "Global Rules:\n".
        "- Return ONLY minified JSON. No code fences, no extra text.\n".
        "- Do not invent facts. If a field has no reliable data, omit it.\n".
        "- Treat the USER's words (ORIGINAL_PROMPT) as authoritative; assistant text is fallible.\n".
        "- 'search':\n".
        "  - Capture the overall QA meaning using BOTH prompt and response.\n".
        "  - 'title' <=80 chars; 'summary' <=500 chars; ASCII-safe when possible.\n".
        "  - 'keywords' = 5-12 strong terms (lowercase, no dupes).\n".
        "  - 'topics' = 3-8 broad themes.\n".
        "  - 'entities' = 0-8 salient named entities {type,text}.\n".
        "  - 'importance' = 0-100 likelihood this turn will matter later.\n".
        "  - 'lang' = ISO-639-1 of RESPONSE (default 'en').\n".
        "- 'assistant_summary':\n".
        "  - ONLY describe what the ASSISTANT just did/said that matters for next turns.\n".
        "  - Focus on follow-up prompts, constraints, and topics it introduced.\n".
        "  - Max 240 chars in 'notes'.\n".
        "- 'profile_update':\n".
        "  - Only add items if the USER clearly stated stable preferences, interests, or facts.\n".
        "  - Ignore jokes, hypotheticals, and anything said only by the assistant.\n".
        "  - Use short, factual strings.\n\n".
        "ORIGINAL_PROMPT:\n".$sPrompt."\n\n".
        "MODEL_RESPONSE:\n".$sResponse."\n\n".
        "Now output the JSON:";

    $aBodyOverride=[
        'contents'=>[
            [
                'role'=>'user',
                'parts'=>[
                    ['text'=>$sInstruction]
                ]
            ]
        ],
        'generationConfig'=>[
            'temperature'=>0.1,
            'topP'=>0.9,
            'topK'=>40
        ]
    ];

    $aGem=geminiGenerate_withBody($sAPIkey, $sModelName, $aBodyOverride);

    $sText=extractFirstTextFromGemini($aGem);
    $sText=trim($sText);

    if (strpos($sText, '```')!==false)
    {
        $sText=preg_replace('/^```[a-zA-Z]*\s*|\s*```$/', '', $sText);
        $sText=trim($sText);
    }

    $a=json_decode($sText, true);

    if (!is_array($a))
    {
        // Minimal salvage fallback
        $fallback=[
            'search'=>[
                'title'=>mb_substr(cleanOneLine($sPrompt), 0, 80),
                'summary'=>mb_substr(cleanOneLine($sResponse), 0, 500),
                'keywords'=>guessKeywords($sPrompt." ".$sResponse),
                'importance'=>50,
                'lang'=>'en'
            ],
            'assistant_summary'=>[
                'notes'=>mb_substr(cleanOneLine($sResponse), 0, 240)
            ]
        ];
        return json_encode($fallback, JSON_UNESCAPED_SLASHES);
    }

    return json_encode($a, JSON_UNESCAPED_SLASHES);
}


/**
 * Variant of geminiGenerate that lets you pass a full body.
 * Returns the same shape as geminiGenerate().
 */
function geminiGenerate_withBody($sAPIkey, $sModelName, array $aBody)
{
    $sEndpoint='https://generativelanguage.googleapis.com/v1beta/models/'
        .rawurlencode($sModelName).':generateContent?key='.rawurlencode($sAPIkey);

    $ch=curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL=>$sEndpoint,
        CURLOPT_POST=>true,
        CURLOPT_RETURNTRANSFER=>true,
        CURLOPT_HTTPHEADER=>['Content-Type: application/json'],
        CURLOPT_POSTFIELDS=>json_encode($aBody, JSON_UNESCAPED_SLASHES),
        CURLOPT_TIMEOUT=>25,
        CURLOPT_CONNECTTIMEOUT=>10
    ]);

    $sResp=curl_exec($ch);
    $iErr=curl_errno($ch);
    $sErr=$iErr?curl_error($ch):null;
    $status=(int)curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($iErr)
    {
        return ['ok'=>false,'status'=>0,'data'=>null,'error'=>'cURL error '.$iErr.': '.$sErr];
    }

    $aJson=json_decode($sResp, true);
    if ($status>=200 && $status<300)
    {
        return ['ok'=>true,'status'=>$status,'data'=>$aJson,'error'=>null];
    }

    return ['ok'=>false,'status'=>$status,'data'=>$aJson,'error'=>($aJson['error']['message']??$sResp)];
}

/**
 * Extract the first text string from a Gemini response.
 */
function extractFirstTextFromGemini(array $aGem)
{
    if (!($aGem['ok']??false) || !isset($aGem['data']['candidates'][0]['content']['parts'][0]['text']))
    {
        // Fall back to any text-like location
        $data=$aGem['data']??[];
        if (isset($data['candidates'][0]['content']['parts']))
        {
            foreach ($data['candidates'][0]['content']['parts'] as $p)
            {
                if (isset($p['text']) && is_string($p['text'])) return $p['text'];
            }
        }
        return '';
    }
    return (string)$aGem['data']['candidates'][0]['content']['parts'][0]['text'];
}

/**
 * Collapse whitespace to a single line.
 */
function cleanOneLine($s)
{
    $s=preg_replace('/\s+/', ' ', (string)$s);
    return trim($s);
}

/**
 * Very lightweight keyword guesser (fallback path only).
 */
function guessKeywords($s)
{
    $s=strtolower($s);
    $s=preg_replace('/[^a-z0-9\s]/', ' ', $s);
    $tokens=preg_split('/\s+/', $s, -1, PREG_SPLIT_NO_EMPTY);
    $stop=['the','a','an','and','or','but','of','to','in','on','for','is','are','was','were','it','this','that','with','as','by','at','from','be','have','has','had','you','your','i','we','they','them','our','their'];
    $freq=[];
    foreach ($tokens as $t)
    {
        if (strlen($t)<3) continue;
        if (in_array($t, $stop, true)) continue;
        $freq[$t]=($freq[$t]??0)+1;
    }
    arsort($freq);
    $top=array_slice(array_keys($freq), 0, 10);
    return array_values($top);
}

function geminiGenerate($sAPIkey, $sInput, $sModelName)
{
    $sEndpoint='https://generativelanguage.googleapis.com/v1beta/models/'
        .rawurlencode($sModelName).':generateContent?key='.rawurlencode($sAPIkey);

    $aBody=[
        'contents'=>[
            [
                'role'=>'user',
                'parts'=>[
                    ['text'=>$sInput]
                ]
            ]
        ],
        // Adjust or remove if you want API defaults
        'generationConfig'=>[
            'temperature'=>0.7,
            'topP'=>0.95,
            'topK'=>40
        ]
        // Optionally add: 'safetySettings'=>[ ... ]
    ];

    $ch=curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL=>$sEndpoint,
        CURLOPT_POST=>true,
        CURLOPT_RETURNTRANSFER=>true,
        CURLOPT_HTTPHEADER=>[
            'Content-Type: application/json'
        ],
        CURLOPT_POSTFIELDS=>json_encode($aBody, JSON_UNESCAPED_SLASHES),
        CURLOPT_TIMEOUT=>25,
        CURLOPT_CONNECTTIMEOUT=>10
    ]);

    $sResp=curl_exec($ch);
    $iErr=curl_errno($ch);
    $sErr=$iErr?curl_error($ch):null;
    $status=(int)curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($iErr)
    {
        return [
            'ok'=>false,
            'status'=>0,
            'data'=>null,
            'error'=>'cURL error '.$iErr.': '.$sErr
        ];
    }

    $aJson=json_decode($sResp, true);
    if ($status>=200 && $status<300)
    {
        return [
            'ok'=>true,
            'status'=>$status,
            'data'=>$aJson,
            'error'=>null
        ];
    }

    // Try to surface API error message if present
    $sApiErr=null;
    if (is_array($aJson) && isset($aJson['error']['message']))
    {
        $sApiErr=$aJson['error']['message'];
    }
    elseif (is_string($sResp))
    {
        $sApiErr=$sResp;
    }

    return [
        'ok'=>false,
        'status'=>$status,
        'data'=>$aJson,
        'error'=>$sApiErr
    ];
}

function smallInt($pValue)
{
    //Force to a Small Int
    $lReturn = (int)$pValue;

    if ($lReturn > 30000)
    {
        $lReturn = 30000;
    }
    if ($lReturn < -30000)
    {
        $lReturn = -30000;
    }

    return $lReturn;
}

function getIndicators()
{
    $connTRADING = getConnectionTRADING("crypto");

    $sSQL = "SELECT * FROM indicators WHERE INDbActive = 1 ORDER BY INDsName";
    $result = getData($sSQL, $connTRADING);

    $arrReturn = array();
    while ($row = getResultB($result))
    {
        $sName = $row["INDsName"];

        $arrReturn[] = $sName;
    }

    mysqli_close($connTRADING);

    return $arrReturn;
}

function getCMCSymbols($lSymbolID = "")
{
    $connTRADING = getConnectionTRADING("crypto");

    $sSQL = "SELECT * FROM cmcsymbols WHERE CMSnRanking IS NOT NULL ORDER BY CMSnRanking";
    if ($lSymbolID > 0)
    {
        $sSQL = "SELECT * FROM cmcsymbols WHERE CMSnID = ".$lSymbolID;
    }
    $result = getData($sSQL, $connTRADING);

    $arrSymbols = array();
    while ($row = getResultB($result))
    {
        $lSymbolID = $row["CMSnID"];
        $sSymbol = $row["CMSsName"];

        $arrSymbols[$lSymbolID] = $sSymbol;
    }

    mysqli_close($connTRADING);

    return $arrSymbols;
}

function getPivotPointBTC($conn, $dDateINITIAL)
{
    $sSymbol = "BTC";

    $sSQL2 = "SELECT * FROM pivotpoints WHERE PVTsSymbol = '".$sSymbol."' AND PVTdDate = '".date("Y-m-d")."'";
    $result2 = getData($sSQL2, $conn);

    $lCount = 0;
    $arrData = array();
    while ($row2 = getResultB($result2))
    {
        $lCount = 1;

        $arrData["Pivot"] = $row2["PVTpPivotPoint"];
        $arrData["S1"] = $row2["PVTpS1"];
        $arrData["R1"] = $row2["PVTpR1"];
        $arrData["S2"] = $row2["PVTpS2"];
        $arrData["R2"] = $row2["PVTpR2"];
        $arrData["S3"] = $row2["PVTpS3"];
        $arrData["R3"] = $row2["PVTpR3"];
    }

    if (@$arrData["Pivot"] > 0)
    {
        return $arrData;
    }

    $sDateINITIAL = date("Y-m-d", strtotime($dDateINITIAL));
    $dDate = DateAdd("d", strtotime($dDateINITIAL), -1);
    $sBaseDate = date("Y-m-d", strtotime($dDate));

    //-------------------------------------------------------------------------------------------
    $sSQL = "SELECT * FROM btc WHERE BTCdDate < '".$sBaseDate." 23:59:59' ORDER BY BTCdDate DESC LIMIT 1";
    $result = getData($sSQL, $conn);

    $sPivot = "";
    $sPreviousDate = "";
    while ($row = getResultB($result))
    {
        $dPreviousDate = $row["BTCdDate"];
        $pClose = $row["BTCnPrice"];
    }

    $sSQL = "SELECT MAX(BTCnPrice) as pHigh, MIN(BTCnPrice) as pLow ".
        " FROM btc ".
        " WHERE BTCdDate BETWEEN '".date("Y-m-d", strtotime($dPreviousDate))."' ".
        " AND '".date("Y-m-d", strtotime($dPreviousDate))." 23:59:59' ";
    $result = getData($sSQL, $conn);

    $pMax = 0;
    $pMin = 0;
    while ($row = getResultB($result))
    {
        $pMax = $row["pHigh"];
        $pMin = $row["pLow"];
    }
    //-------------------------------------------------------------------------------------------

    if ($pMax != 0 && $pMin != 0 && $pClose != 0)
    {
        $pPivotPoint = ($pMin + $pMax + $pClose) / 3;

        $pS1 = (2 * $pPivotPoint) - $pMax;
        $pR1 = (2 * $pPivotPoint) - $pMin;
        $pS2 = $pPivotPoint - ($pMax - $pMin);
        $pR2 = $pPivotPoint + ($pMax - $pMin);
        $pS3 = $pMin - (2 * ($pMax - $pPivotPoint));
        $pR3 = $pMax + (2 * ($pPivotPoint - $pMin));

        $arrData["Pivot"] = $pPivotPoint;
        $arrData["S1"] = $pS1;
        $arrData["R1"] = $pR1;
        $arrData["S2"] = $pS2;
        $arrData["R2"] = $pR2;
        $arrData["S3"] = $pS3;
        $arrData["R3"] = $pR3;

        if ($lCount == 0)
        {
            $sSQL = "REPLACE INTO pivotpoints (PVTdDate, PVTpPivotPoint, PVTpR1, PVTpR2, PVTpR3, PVTpS1, PVTpS2, PVTpS3, PVTsSymbol) ".
                    "VALUES ('".$sDateINITIAL."' ".
                    ", ".SQLFormatField($pPivotPoint, "String").
                    ", ".SQLFormatField($pR1, "String").
                    ", ".SQLFormatField($pR2, "String").
                    ", ".SQLFormatField($pR3, "String").
                    ", ".SQLFormatField($pS1, "String").
                    ", ".SQLFormatField($pS2, "String").
                    ", ".SQLFormatField($pS3, "String").
                    ", '".$sSymbol."') ";
            if (!$result = getData($sSQL, $conn))
            {
                echo "ERROR: ".$sSQL."<br>";
                die;
            }
        }
    }

    return $arrData;
}


function getPivotPointCRYPTO($connREAD, $connINSERT, $sSymbol, $dDateINITIAL)
{
    $sSQL2 = "SELECT * FROM pivotpoints WHERE PVTsSymbol = '".$sSymbol."' AND PVTdDate = '".date("Y-m-d")."'";
    $result2 = getData($sSQL2, $connINSERT);

    $lCount = 0;
    $arrData = array();
    while ($row2 = getResultB($result2))
    {
        $lCount = 1;

        $arrData["Pivot"] = $row2["PVTpPivotPoint"];
        $arrData["S1"] = $row2["PVTpS1"];
        $arrData["R1"] = $row2["PVTpR1"];
        $arrData["S2"] = $row2["PVTpS2"];
        $arrData["R2"] = $row2["PVTpR2"];
        $arrData["S3"] = $row2["PVTpS3"];
        $arrData["R3"] = $row2["PVTpR3"];
    }

    if (@$arrData["Pivot"] > 0)
    {
        return $arrData;
    }

    $sDateINITIAL = date("Y-m-d", strtotime($dDateINITIAL));
    $dDate = DateAdd("d", strtotime($dDateINITIAL), -1);
    $sBaseDate = date("Y-m-d", strtotime($dDate));

    //-------------------------------------------------------------------------------------------
    $sSQL = "SELECT * FROM min5 WHERE VALsSymbol = '".$sSymbol."' AND VALdDate < '".$sBaseDate." 23:59:59' ORDER BY VALdDate DESC LIMIT 1";
    $result = getData($sSQL, $connREAD);

    $sPivot = "";
    $sPreviousDate = "";
    while ($row = getResultB($result))
    {
        $dPreviousDate = $row["VALdDate"];
        $pClose = $row["VALnPrice"];
    }

    $sSQL = "SELECT MAX(VALnPrice) as pHigh, MIN(VALnPrice) as pLow ".
        " FROM min5 ".
        " WHERE VALsSymbol = '".$sSymbol."' ".
        " AND VALdDate BETWEEN '".date("Y-m-d", strtotime($dPreviousDate))."' ".
        " AND '".date("Y-m-d", strtotime($dPreviousDate))." 23:59:59' ";
    $result = getData($sSQL, $connREAD);

    $pMax = 0;
    $pMin = 0;
    while ($row = getResultB($result))
    {
        $pMax = $row["pHigh"];
        $pMin = $row["pLow"];
    }
    //-------------------------------------------------------------------------------------------

    if ($pMax != 0 && $pMin != 0 && $pClose != 0)
    {
        $pPivotPoint = ($pMin + $pMax + $pClose) / 3;

        $pS1 = (2 * $pPivotPoint) - $pMax;
        $pR1 = (2 * $pPivotPoint) - $pMin;
        $pS2 = $pPivotPoint - ($pMax - $pMin);
        $pR2 = $pPivotPoint + ($pMax - $pMin);
        $pS3 = $pMin - (2 * ($pMax - $pPivotPoint));
        $pR3 = $pMax + (2 * ($pPivotPoint - $pMin));

        $arrData["Pivot"] = $pPivotPoint;
        $arrData["S1"] = $pS1;
        $arrData["R1"] = $pR1;
        $arrData["S2"] = $pS2;
        $arrData["R2"] = $pR2;
        $arrData["S3"] = $pS3;
        $arrData["R3"] = $pR3;

        if ($lCount == 0)
        {
            $sSQL = "REPLACE INTO pivotpoints (PVTdDate, PVTpPivotPoint, PVTpR1, PVTpR2, PVTpR3, PVTpS1, PVTpS2, PVTpS3, PVTsSymbol) ".
                    "VALUES ('".$sDateINITIAL."' ".
                    ", ".SQLFormatField($pPivotPoint, "String").
                    ", ".SQLFormatField($pR1, "String").
                    ", ".SQLFormatField($pR2, "String").
                    ", ".SQLFormatField($pR3, "String").
                    ", ".SQLFormatField($pS1, "String").
                    ", ".SQLFormatField($pS2, "String").
                    ", ".SQLFormatField($pS3, "String").
                    ", '".$sSymbol."') ";
            if (!$result = getData($sSQL, $connINSERT))
            {
                echo "ERROR: ".$sSQL."<br>";
                die;
            }
        }
    }

    return $arrData;
}

function DecimalFormat($pValue)
{
    $pReturn = number_format($pValue, 8);

    if ($pValue >= 10000)
    {
        $pReturn = number_format($pValue, 0);
    } elseif ($pValue >= 1000)
    {
        $pReturn = number_format($pValue, 1);
    } elseif ($pValue >= 100)
    {
        $pReturn = number_format($pValue, 2);
    } elseif ($pValue < 0.00001)
    {
        $pReturn = number_format($pValue, 7);
    } elseif ($pValue < 0.0001)
    {
        $pReturn = number_format($pValue, 6);
    } elseif ($pValue < 0.001)
    {
        $pReturn = number_format($pValue, 5);
    } elseif ($pValue < 0.01)
    {
        $pReturn = number_format($pValue, 4);
    } else
    {
        $pReturn = number_format($pValue, 5);
    }

    $pReturn = str_replace(",", "", $pReturn);

    return $pReturn;
}

function getGemini($sPrompt)
{
    global $dNow;

    $connTRADING = getConnectionTRADING("forex");

    $data = array(
        "contents" => array(
            array(
                "parts" => array(
                    array(
                        "text" => $sPrompt
                    )
                )
            )
        ),
        "generationConfig" => array(
            "stopSequences" => array(
                "Title"
            ),
            "temperature" => 0,
            "topP" => 0.95,
            "topK" => 10
        )
    );

    // Initialize cURL session
    $ch = curl_init();

    $jsonData = json_encode($data);
    $headers = array('Content-Type: application/json');

    curl_setopt($ch, CURLOPT_URL, 'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key='.GEMINI_API);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);

    if ($response === false)
    {
        echo 'cURL error: ' . curl_error($ch);
    }

    $arrResponse = json_decode($response, true);

    $sError = @$arrResponse["error"]["message"];

    if ($sError != "")
    {
        echo "ERROR: ".$sError.chr(10).chr(10).$sPrompt;
        die;
    }

    $sResponse = $arrResponse["candidates"][0]["content"]["parts"][0]["text"];

    $sSQL = "INSERT INTO airequests (ARQdDate, ARQsPrompt, ARQsResponse, ARQnCredits, ARQsModel) ".
            " VALUES ('".$dNow."' ".
            ", ".SQLFormatField($sPrompt, "String").
            ", ".SQLFormatField($sResponse, "String").
            ", 0".
            ", 'Gemini Pro'".
            ") ";
    if (!$result = getData($sSQL, $connTRADING))
    {
        echo "ERROR: ".$sSQL."<br>".mysqli_error($connTRADING);
        die;
    }

    mysqli_close($connTRADING);

    sleep(2);

    return $sResponse;
}


function getOpenAI($sPrompt, $sModel = "gpt-5")
{
    $connTRADING = getConnectionTRADING("miscdb");
    $dStartTime = microtime(true);
    $data = array(
        'model' => $sModel,
        'messages' => [
            ['role' => 'system', 'content' => 'Output data in JSON format only.'],
            ['role' => 'user', 'content' => $sPrompt],
        ],
        );

    // Convert data to JSON format
    $dataJSON = json_encode($data);

    // Initialize cURL session
    $ch = curl_init();

    // Set cURL options
    curl_setopt($ch, CURLOPT_URL, 'https://api.openai.com/v1/chat/completions');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $dataJSON);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Authorization: Bearer ' . OPENAI_KEY,
    ));

    // Execute cURL session
    $response = curl_exec($ch);

    //echo "RESPONSE: ".$response."<br><br>";

    // Check for errors
    if (curl_errno($ch))
    {
        echo 'Error:' . curl_error($ch);
    } else
    {
        // Decode the JSON response
        $responseData = json_decode($response, true);

        // Output the generated text
        $generatedText = @$responseData['choices'][0]['message']['content'];
    }

    $sError = @$responseData["error"]["message"];

    if ($sError != "")
    {
        echo "ERROR: ".$sError;
        die;
    }

    $arrResponse["text"] = $generatedText;
    $arrResponse["credits"] = $responseData["usage"]["total_tokens"];

    $sSQL = "INSERT INTO airequests (ARQdDate, ARQsPrompt, ARQsResponse, ARQnCredits, ARQsModel, ARQnSeconds) ".
            " VALUES ('".date("Y-m-d H:i:s")."' ".
            ", ".SQLFormatField(substr($sPrompt, 0, 1000), "String").
            ", ".SQLFormatField($generatedText, "String").
            ", ".$arrResponse["credits"].
            ", ".SQLFormatField($responseData["model"], "String").
            ", ".round(microtime(true) - $dStartTime, 2).
            ") ";
    if (!$result = getData($sSQL, $connTRADING))
    {
        echo "ERROR: ".$sSQL."<br>".mysqli_error($connTRADING);
        die;
    }

    mysqli_close($connTRADING);

    return $arrResponse;

}

function isOBD($sESN)
{
    //9/14/18
    $bReturn = false;

    $sPrefix = substr($sESN, 0, 3);

    if (
        $sPrefix == "454"
        || $sPrefix == "456"
        || $sPrefix == "473"
        || $sPrefix == "474"
    ) {
        $bReturn = true;
    }

    return $bReturn;
}

function getConnectionTRADING($sDB)
{
    return mysqli_connect("192.168.0.7", "repuser", "Respect1", $sDB);
}

function getConnectionI()
{
    return mysqli_connect("192.168.0.7", "repuser", "Respect1");
}

function getData($sSQL, $conn)
{
    try
    {
        writeMySQLlog($sSQL);
        $result = $conn->query($sSQL);
        return $result;
    } 
    catch (mysqli_sql_exception $e)
    {
        echo "Error: ".$e->getMessage();
        return false;
    }
}

function getResultB($result)
{
    if ($result == false)
    {
        writeLog("Error (getResultB), Unknown Query");
    }

    return mysqli_fetch_assoc($result);
}

function getError($conn)
{
    return mysqli_error($conn);
}

function sendSMS($sTo, $sMessage)
{
    // Your Account SID and Auth Token from twilio.com/console
    $sid = 'AC2f0e9c0fd963b96203afd3d6389dc9a4';
    $token = 'ab4d5657d2af42adb9ac629ac591e4c8';
    $client = new Client($sid, $token);

    // Use the client to do fun stuff like send text messages!
    $client->messages->create(
        // the number you'd like to send the message to
        '+1'.$sTo,
        array(
            // A Twilio phone number you purchased at twilio.com/console
            'from' => '+14237166551',
            // the body of the text message you'd like to send
            'body' => $sMessage
        )
    );
}

function OLDsendSMS($sTo, $sMessage)
{
    $account_sid = 'AC2f0e9c0fd963b96203afd3d6389dc9a4';
    $auth_token = 'ab4d5657d2af42adb9ac629ac591e4c8';

    $client = new Services_Twilio($account_sid, $auth_token);

    $sText = "To: ".$sTo." => ".$sMessage;

    try
    {
        $client->account->messages->create(array(
            'To' => $sTo,
            'From' => "+14237166551",
            'Body' => $sMessage,
        ));
    } catch (Services_Twilio_RestException $e)
    {
        echo $e->getMessage();
        $sText .= chr(10)."***** ERROR **** ".$e->getMessage();
    }

    writeLogSMS($sText);
}

function getTimeZoneByCompanyID($lCompanyID)
{
    extract($GLOBALS);

    $sSQL = "SELECT TZOsValue
        FROM companies 
        JOIN timezones ON TZOnID = COMnTimeZone
        WHERE COMnID = ".$lCompanyID;
    $result = getData($sSQL, $conn);

    $sTimeZone = "";
    while ($row = getResultB($result))
    {
        $sTimeZone = $row["TZOsValue"];
    }

    return $sTimeZone;
}

function outputScript($lVersion, $sESN)
{
    extract($GLOBALS);

    $sSQL = "SELECT * FROM configs WHERE CONnID = ".$lVersion;
    $result = getData($sSQL, $conn);

    $sConfig = "";
    while ($row = getResultB($result))
    {
        $sConfig = $row["CONsConfig"];
    }

    $arrConfig = explode(chr(10), $sConfig);

    foreach ($arrConfig as $sLine)
    {
        $sCommand = substr($sLine, 2);

        if (substr($sCommand, 0, 2) == "AT" && substr($sLine, 0, 1) == "1")
        {
            $sSQL = "INSERT INTO outgoing (OUTsMessage, OUTsESN) ".
                    " VALUES (".SQLFormatField($sCommand, "String").
                    ", ".SQLFormatField($sESN, "String").
                    ")";
            if (!$result = getData($sSQL, $conn))
            {
                echo "ERROR: ".$sSQL."<br>".getError($conn)."<br>";
            }
        }
    }
}

function getTimeZoneByESN($sESN)
{
    extract($GLOBALS);

    $sSQL = "SELECT MAX(TZOsValue) AS sTimeZone
        FROM units 
        JOIN companies ON COMnID = UNInCompanyID 
        JOIN timezones ON TZOnID = COMnTimeZone
        WHERE UNIsESN = '".$sESN."'";
    $result = getData($sSQL, $conn);

    $sTimeZone = "";
    while ($row = getResultB($result))
    {
        $sTimeZone = $row["sTimeZone"];
    }

    return $sTimeZone;
}

function getTimeLoopText($lMinutes)
{
    global $lMinutes;

    if (substr($lMinutes, -2) == "60")
    {
        $lMinutes += 40;
    } elseif ($lMinutes == 2345)
    {
        $lMinutes = 2359;
    }

    if ($lMinutes < 10)
    {
        $lMinutes = "000".$lMinutes;
    } elseif ($lMinutes < 100)
    {
        $lMinutes = "00".$lMinutes;
    } elseif ($lMinutes < 1000)
    {
        $lMinutes = "0".$lMinutes;
    }

    if ($lMinutes == "0000")
    {
        $sTime = "Midnight";
    } elseif ($lMinutes <= 59)
    {
        $sTime = "12:".substr($lMinutes, 2, 2)." AM";
    } elseif ($lMinutes < 1000)
    {
        $sTime = substr($lMinutes, 1, 1).":".substr($lMinutes, 2, 2)." AM";
    } elseif ($lMinutes < 1200)
    {
        $sTime = substr($lMinutes, 0, 2).":".substr($lMinutes, 2, 2)." AM";
    } elseif ($lMinutes == 1200)
    {
        $sTime = "Noon";
    } elseif ($lMinutes > 1200 && $lMinutes < 1300)
    {
        $sTime = "12:".substr($lMinutes, 2, 2)." PM";
    } elseif ($lMinutes < 2200)
    {
        $sTime = substr(($lMinutes - 1200), 0, 1).":".substr($lMinutes, 2, 2)." PM";
    } elseif ($lMinutes == 2359)
    {
        $sTime = "End of Day";
    } else
    {
        $sTime = substr(($lMinutes - 1200), 0, 2).":".substr($lMinutes, 2, 2)." PM";
    }

    return $sTime;
}

function getAdTrackerConnection()
{
    $conn = getConnectionI();

    return $conn;
}

function getConnectionEXT()
{
    $connEXT = getConnectionI();

    return $connEXT;
}

function getInsertConnection()
{
    $conn = mysqli_connect("192.168.0.4", "repuser", "Respect1");

    return $conn;
}


function getMasterConnection()
{
    $conn = mysqli_connect(DB_MASTER, "root", DB_MASTER_PASSWORD, "gps");

    return $conn;
}



function DisplayFooterNOLINKS()
{
    $conn = getConnectionI();
    ?>
<CENTER>
<br>
<br>
<br>
<font size="3">
<a href="index.php">Home</a> - <a href="/ContactUs.php">Contact Us</a></font>

<br>
<font size="1">All Content and Images are Copyrights of their respective parties. Copyright SellHI, LLC.</font>
</CENTER>
<?php if (strpos(@$_SERVER["REQUEST_URI"], "bNoAds") == 0)
{ ?>
<!-- Quantcast Tag, part 2 -->
<script type="text/javascript">
_qevents.push( { qacct:"p-2fWSfMlOvbJy2"} );
</script>
<noscript>
<div style="display: none;"><img src="//pixel.quantserve.com/pixel/p-2fWSfMlOvbJy2.gif" height="1" width="1" alt="Quantcast"/></div>
</noscript>
<?php } ?>
<?php StoreLoadTime(); ?>
<?php }

function DisplayFooterWITHLINKS()
{
    $conn = getConnectionI();
    ?>
<CENTER>
<br>
<br>
<br>
<font size="3">
<a href="index.php">Home</a> - <a href="ContactUs.php">Contact Us</a> -
<?php
if (1 == 2)
{
    $sSQL = "SELECT * FROM Phones.Sites ORDER BY RAND() LIMIT 1";
    $result = getData($sSQL, $conn);

    while ($row = getResultB($result))
    {
        $sURL = $row["SITsURL"];
        ?>
	<a href="http://<?php echo $sURL?>"><?php echo $sURL?></a>
	<?php
    }
} ?>
</font>

<br>
<font size="1">All Content and Images are Copyrights of their respective parties. Copyright SellHI, LLC.</font>
</CENTER>
<?php if (strpos(@$_SERVER["REQUEST_URI"], "bNoAds") == 0)
{ ?>
<!-- Quantcast Tag, part 2 -->
<script type="text/javascript">
_qevents.push( { qacct:"p-2fWSfMlOvbJy2"} );
</script>
<noscript>
<div style="display: none;"><img src="//pixel.quantserve.com/pixel/p-2fWSfMlOvbJy2.gif" height="1" width="1" alt="Quantcast"/></div>
</noscript>
<?php } ?>
<?php StoreLoadTime(); ?>
<?php }

function getResult($sSQL, $conn)
{
    $dStart = explode(" ", microtime());
    $dStart = $dStart[1] + $dStart[0];

    $result = getData($sSQL, $conn);

    $dEnd = explode(" ", microtime());
    $dEnd = $dEnd[1] + $dEnd[0];

    $lTime = ($dEnd - $dStart) * 1000;

    $sText = $sSQL.chr(10).$lTime.chr(10).chr(10);

    return $result;
}

function SignAmazonUrl($url)
{
    $original_url = $url;

    // Decode anything already encoded
    $url = urldecode($url);

    // Parse the URL into $urlparts
    $urlparts = parse_url($url);

    // Build $params with each name/value pair
    foreach (explode('&', $urlparts['query']) as $part)
    {
        if (strpos($part, '='))
        {
            list($name, $value) = explode('=', $part, 2);
        } else
        {
            $name = $part;
            $value = '';
        }
        $params[$name] = $value;
    }

    // Include a timestamp if none was provided
    if (empty($params['Timestamp']))
    {
        $params['Timestamp'] = gmdate('Y-m-d\TH:i:s\Z');
    }

    // Sort the array by key
    ksort($params);

    // Build the canonical query string
    $canonical       = '';
    foreach ($params as $key => $val)
    {
        $canonical  .= "$key=".rawurlencode($val)."&";
    }
    // Remove the trailing ampersand
    $canonical       = preg_replace("/&$/", '', $canonical);

    // Some common replacements and ones that Amazon specifically mentions
    $canonical       = str_replace(array(' ', '+', ',', ';'), array('%20', '%20', urlencode(','), urlencode(':')), $canonical);

    // Build the si
    $string_to_sign             = "GET\n{$urlparts['host']}\n{$urlparts['path']}\n$canonical";
    // Calculate our actual signature and base64 encode it
    $signature            = base64_encode(hash_hmac('sha256', $string_to_sign, sAmazonSecretKey, true));

    // Finally re-build the URL with the proper string and include the Signature
    $url = "{$urlparts['scheme']}://{$urlparts['host']}{$urlparts['path']}?$canonical&Signature=".rawurlencode($signature);

    return $url;
}

function NEWgeocodeAddress($sAddress)
{
    $sURLRAW = "http://maps.google.com/maps/api/geocode/xml?address=".urlencode($sAddress)."&key=".GOOGLE_MAPS_KEY;
    $sURL = signURL($sURLRAW);

    echo $sURL;
}

function signURL($my_url_to_sign)
{
    //parse the url
    $url = parse_url($my_url_to_sign);

    $privatekey = GOOGLE_MAPS_KEY;

    $urlToSign =  $url['path'] . "?" . $url['query'];


    // Decode the private key into its binary format
    $decodedKey = decodeBase64UrlSafe($privatekey);

    // Create a signature using the private key and the URL-encoded
    // string using HMAC SHA1. This signature will be binary.
    $signature = hash_hmac("sha1", $urlToSign, $decodedKey, true);

    //make encode Signature and make it URL Safe
    $encodedSignature = encodeBase64UrlSafe($signature);

    $originalUrl = $url['scheme'] . "://" . $url['host'] . $url['path'] . "?" . $url['query'];
    //print("Full URL: " . $originalUrl . "&signature=" . $encodedSignature);

    return $originalUrl."&signature=".$encodedSignature;
}

function encodeBase64UrlSafe($value)
{
    return str_replace(array('+', '/'), array('-', '_'), base64_encode($value));
}

function decodeBase64UrlSafe($value)
{
    return base64_decode(str_replace(array('-', '_'), array('+', '/'), $value));
}

function StoreLoadTime()
{
    $conn2 = getAdTrackerConnection();
    selectDB("AdTracker", $conn2);

    //PERFORMANCE LOAD TIME
    extract($GLOBALS);

    $dEnd = explode(" ", microtime());
    $dEnd = $dEnd[1] + $dEnd[0];

    $lTime = ($dEnd - $dStart) * 1000;

    if (@$lVisitorID != "" && $lTime > 0)
    {
        $sSQL = "SET sql_log_bin = 0";
        if (!$result = getData($sSQL, $conn2))
        {
            echo "ERROR: ".$sSQL;
            die;
        }

        $sSQL = "UPDATE AdTracker.".date("Ymd")." SET VSnLoadTime = ".$lTime.
            " WHERE VSnID = ".$lVisitorID;
        if (!$result = getData($sSQL, $conn2))
        {
            echo "ERROR: ".$sSQL."<br>".mysql_error();
        }

        $sSQL = "SET sql_log_bin = 1";
        if (!$result = getData($sSQL, $conn2))
        {
            echo "ERROR: ".$sSQL;
            die;
        }

    }
}

function DisplayHeader()
{ ?>
<HTML>
<?php if (strpos(@$_SERVER["REQUEST_URI"], "bNoAds") == 0)
{ ?>
 <!-- Quantcast Tag, part 1 --> 
<script type="text/javascript">
  var _qevents = _qevents || [];

  (function() {
   var elem = document.createElement('script');

   elem.src = (document.location.protocol == "https:" ? "https://secure" : "http://edge") + ".quantserve.com/quant.js";
   elem.async = true;
   elem.type = "text/javascript";
   var scpt = document.getElementsByTagName('script')[0];
   scpt.parentNode.insertBefore(elem, scpt);  
  })();
</script>

<!-- Begin Constant Contact Active Forms -->
<script> var _ctct_m = "cb4d4d276a5745c9c6620da163ae29be"; </script>
<script id="signupScript" src="//static.ctctcdn.com/js/signup-form-widget/current/signup-form-widget.min.js" async defer></script>
<!-- End Constant Contact Active Forms -->
<?php } ?>
<?php }

function Resize($Filename, $Thumbnail, $WIDTH, $HEIGHT)
{
    $image = $Filename;
    $thumbnail = $Thumbnail;
    $imagesize = getimagesize($image);
    $fwidth = $imagesize[0];
    $fheight = $imagesize[1];

    /*
    if($fheight <= $fwidth)
    {
        $percentage = round(($WIDTH/$fwidth) * 100);
        $newwidth = round(($fwidth * $percentage)/100);
        $newheight = round(($fheight * $percentage)/100);
    }
    elseif($fheight > $fwidth)
    {
        $percentage = round(($HEIGHT/$fheight) * 100);
        $newwidth = round(($fwidth * $percentage)/100);
        $newheight = round(($fheight * $percentage)/100);
    }
    else
    {
        $percentage = round(($fwidth/$WIDTH) * 100);
        $newwidth = round(($fwidth * $percentage)/100);
        $newheight = round(($fheight * $percentage)/100);
    }
    */

    if ($HEIGHT > 0)
    {
        /* ADJUST HEIGHT */
        $newheight = $HEIGHT;

        $percentage = round(($HEIGHT / $fheight) * 100);
        $newwidth = round(($fwidth * $percentage) / 100);
    } else
    {
        /* ADJUST WIDTH */
        $newwidth = $WIDTH;

        $percentage = round(($WIDTH / $fwidth) * 100);
        $newheight = round(($fheight * $percentage) / 100);
    }

    //echo $newwidth.", ".$newheight;
    //die;

    $destImage = imagecreatetruecolor($newwidth, $newheight) or
    die($file.' - '.$newwidth.':'.$newheight);
    $type = strtolower(substr($Filename, -3));

    switch ($type)
    {
        case 'bmp':
            $srcImage = ImageCreateFromWBMP($image);
            ImageCopyResized(
                $destImage,
                $srcImage,
                0,
                0,
                0,
                0,
                $newwidth,
                $newheight,
                imagesx($srcImage),
                imagesy($srcImage)
            );
            ImageWBMP($destImage, $thumbnail);
            break;
        case 'gif':
            $srcImage = ImageCreateFromGif($image);
            ImageCopyResized(
                $destImage,
                $srcImage,
                0,
                0,
                0,
                0,
                $newwidth,
                $newheight,
                imagesx($srcImage),
                imagesy($srcImage)
            );
            ImageGif($destImage, $thumbnail);
            break;
        case 'jpg':
            $srcImage = ImageCreateFromJPEG($image);
            ImageCopyResized(
                $destImage,
                $srcImage,
                0,
                0,
                0,
                0,
                $newwidth,
                $newheight,
                imagesx($srcImage),
                imagesy($srcImage)
            );
            ImageJPEG($destImage, $thumbnail);
            break;
        case 'png':
            $srcImage = ImageCreateFromPNG($image);
            ImageCopyResized(
                $destImage,
                $srcImage,
                0,
                0,
                0,
                0,
                $newwidth,
                $newheight,
                imagesx($srcImage),
                imagesy($srcImage)
            );
            ImagePNG($destImage, $thumbnail);
            break;
    }

    //free the memory used for the images
    ImageDestroy($srcImage);
    ImageDestroy($destImage);

    if (file_exists($thumbnail))
    {
        return true;
    } else
    {
        return false;
    }
}

function DateAdd($sType, $dDate, $lInterval)
{
    // If $dDate is a string, convert it to a timestamp
    if (!is_numeric($dDate))
    {
        $dDate = strtotime($dDate);
        if ($dDate === false)
        {
            return false; // Invalid date format
        }
    }

    // Define an associative array to match $sType with DateInterval units
    $units = [
        's' => 'second',
        'n' => 'minute',
        'h' => 'hour',
        'd' => 'day',
        'month' => 'month'
    ];

    // Check if the provided type is valid
    if (!array_key_exists($sType, $units))
    {
        return false; // Invalid interval type
    }

    // Create a DateTime object from the timestamp
    $date = new DateTime();
    $date->setTimestamp($dDate);

    // Build the DateInterval string based on type
    if ($sType == 'month')
    {
        $intervalString = 'P' . abs($lInterval) . 'M';
    } elseif ($sType == 'd')
    {
        $intervalString = 'P' . abs($lInterval) . 'D';
    } else
    {
        // For time-based intervals (seconds, minutes, hours)
        $timeUnit = strtoupper(substr($units[$sType], 0, 1));
        $intervalString = 'PT' . abs($lInterval) . $timeUnit;
    }

    // Apply the interval, add if positive, subtract if negative
    $interval = new DateInterval($intervalString);
    if ($lInterval < 0)
    {
        $date->sub($interval);
    } else
    {
        $date->add($interval);
    }

    // Return the modified date in the same format as the input
    return $date->format('Y-m-d H:i:s');
}


function WebIt($sString)
{
    $sValue = str_replace(" ", "+", $sString);
    $sValue = str_replace("#", "", $sValue);
    $sValue = str_replace(chr(10), "", $sValue);
    $sValue = str_replace(chr(13), "", $sValue);

    return $sValue;
}

function stripHTML($sInput)
{
    $sOutput = str_replace("<p>", "", $sInput);
    $sOutput = str_replace("<br", "", $sOutput);
    $sOutput = str_replace("</p>", "", $sOutput);
    $sOutput = str_replace("/>", "", $sOutput);
    $sOutput = str_replace(chr(10), "", $sOutput);
    $sOutput = str_replace(chr(13), "", $sOutput);

    return $sOutput;
}

function isCapital($sInput)
{
    $sLetter = substr($sInput, 0, 1);

    if ($sLetter == "A" || $sLetter == "B" || $sLetter == "C" || $sLetter == "D" || $sLetter == "E" || $sLetter == "F" || $sLetter == "G"
        || $sLetter == "H" || $sLetter == "I" || $sLetter == "J" || $sLetter == "K" || $sLetter == "L" || $sLetter == "M" || $sLetter == "N" || $sLetter == "O"
        || $sLetter == "P" || $sLetter == "Q" || $sLetter == "R" || $sLetter == "S" || $sLetter == "T" || $sLetter == "U" || $sLetter == "V" || $sLetter == "W"
        || $sLetter == "X" || $sLetter == "Y" || $sLetter == "Z")
    {
        return true;
    } else
    {
        return false;
    }
}


function duplicateLetter($sInput, $lPosition)
{
    $sOutput = "";

    for ($x = 0; $x <= strlen($sInput) - 1; $x += 1)
    {
        if ($x == $lPosition)
        {
            $sOutput .= substr($sInput, $x, 1).substr($sInput, $x, 1);
        } else
        {
            $sOutput .= substr($sInput, $x, 1);
        }
    }

    return $sOutput;
}

function removeLetter($sInput, $lPosition)
{
    $sOutput = "";

    for ($x = 0; $x <= strlen($sInput) - 1; $x += 1)
    {
        if (($x + 1) <> $lPosition)
        {
            $sOutput .= substr($sInput, $x, 1);
        }
    }

    return $sOutput;
}

function SQLit($sString, $sType = "String")
{
    $sValue = "";

    if ($sType == "Number" && $sString != "")
    {
        //Eliminate Exponent
        $sString = number_format($sString, 20, ".", "");
    }

    if ($sString == "")
    {
        $sValue = "NULL";
    } else
    {
        $sValue = str_replace("'", "''", $sString);
    }

    if ($sValue == "NULL" || $sType == "Number")
    {
        return $sValue;
    } else
    {
        return "'".$sValue."'";
    }
}

function maxNumber($lValue, $lNumDigits)
{
    $lValueNEW = $lValue;

    if ($lValueNEW > 9 && $lNumDigits == 1)
    {
        $lValueNEW = 9.99;
    }
    if ($lValueNEW > 99 && $lNumDigits == 2)
    {
        $lValueNEW = 99.99;
    }
    if ($lValueNEW > 999 && $lNumDigits == 3)
    {
        $lValueNEW = 999.99;
    }
    if ($lValueNEW > 9999 && $lNumDigits == 4)
    {
        $lValueNEW = 9999.99;
    }
    if ($lValueNEW > 99999 && $lNumDigits == 5)
    {
        $lValueNEW = 99999.99;
    }
    if ($lValueNEW > 999999 && $lNumDigits == 6)
    {
        $lValueNEW = 999999.99;
    }

    return $lValueNEW;
}

function SQLFormatField($sString, $sType)
{
    $sValue = "";

    if ($sString == "")
    {
        $sValue = "NULL";
    } elseif ($sType == "Number")
    {
        $sValue = str_replace(",", "", $sString);

        $sValue = str_replace("+", "", $sValue);
        $sValue = str_replace("%", "", $sValue);
    } else
    {
        $sValue = str_replace("'", "''", $sString);
    }

    if ($sValue == "NULL")
    {
        return $sValue;
    } else
    {
        return "'".$sValue."'";
    }
}

function DateDiff($sInterval, $dStartRAW, $dEndRAW)
{

    //if ((strpos($dStartRAW, "-") > 0) || (strpos($dStartRAW, "/") > 0))
    //{
    //	$dStartDate = $dStartRAW;
    ///	$dEndDate = $dEndRAW;
    //}
    //if (is_numeric($dStartRAW) == true)
    //{
    //	$dStartDate = $dStartRAW;
    //	$dEndDate = $dEndRAW;
    //}
    //else
    //{
    //	$dStartDate = strtotime(substr($dStartRAW, 0, 4)."-".substr($dStartRAW, 4, 2)."-".substr($dStartRAW, 6, 2)." ".substr($dStartRAW, 8, 2).":".substr($dStartRAW, 10, 2).":00");
    //	$dEndDate = strtotime(substr($dEndRAW, 0, 4)."-".substr($dEndRAW, 4, 2)."-".substr($dEndRAW, 6, 2)." ".substr($dEndRAW, 8, 2).":".substr($dEndRAW, 10, 2).":59");
    //}

    $dStartDate = strtotime($dStartRAW);
    $dEndDate = strtotime($dEndRAW);

    $lSeconds = intval($dEndDate - $dStartDate);
    $lMinutes = floor($lSeconds / 60);
    $lHours = floor($lMinutes / 60);
    $lDays = floor($lHours / 24);

    if ($sInterval == "s")
    {
        return $lSeconds;
    } elseif ($sInterval == "n")
    {
        return $lMinutes;
    } elseif ($sInterval == "h")
    {
        return $lHours;
    } elseif ($sInterval == "d")
    {
        return $lDays;
    }
}

function getTimeDesc($lSeconds, $bShowJustNow = true)
{
    $lMinutes = floor($lSeconds / 60);
    $lHours = floor($lMinutes / 60);
    $lDays = floor($lHours / 24);
    $lMonths = floor($lDays / 31);
    $lYears = number_format(($lMonths / 12), 1);

    if ($lYears > 10)
    {
        $sReturn = "";
    } elseif ($lSeconds < 120)
    {
        if ($bShowJustNow)
        {
            $sReturn = "Just Now";
        } else
        {
            $sReturn = $lSeconds." Seconds";

            //Remove final S
            if ($lSeconds == 1)
            {
                $sReturn = substr($sReturn, 0, strlen($sReturn) - 1);
            }
        }
    } elseif ($lMinutes <= 59)
    {
        $sReturn = $lMinutes." Minutes";

        //Remove final S
        if ($lMinutes == 1)
        {
            $sReturn = substr($sReturn, 0, strlen($sReturn) - 1);
        }
    } elseif ($lHours <= 48)
    {
        $sReturn = $lHours." Hours";

        //Remove final S
        if ($lHours == 1)
        {
            $sReturn = substr($sReturn, 0, strlen($sReturn) - 1);
        }
    } elseif ($lDays <= 31)
    {
        $sReturn = $lDays." Days";

        //Remove final S
        if ($lDays == 1)
        {
            $sReturn = substr($sReturn, 0, strlen($sReturn) - 1);
        }
    } elseif ($lMonths <= 12)
    {
        $sReturn = $lMonths." Months";

        //Remove final S
        if ($lMonths == 1)
        {
            $sReturn = substr($sReturn, 0, strlen($sReturn) - 1);
        }
    } else
    {
        $sReturn = $lYears." Years";

        //Remove final S
        if ($lYears == 1)
        {
            $sReturn = substr($sReturn, 0, strlen($sReturn) - 1);
        }
    }

    return $sReturn;
}

function GetCountryID($sReferer)
{
    $conn = getConnectionI();
    selectDB("AdTracker", $conn);

    $lCountryID = "";
    if (strpos($sReferer, "google.com/") > 0)
    {
        //US - GOOGLE.COM
        $lCountryID = -1;
    } elseif (strpos($sReferer, "google.") > 0)
    {
        //OTHER COUNTRY
        $lCountryID = -1;
    }

    return $lCountryID;
}

function writeMySQLlog($sText)
{
    if (1 == 2)
    {
        $fp = fopen("/var/log/kirk/mysql_".date("Y-m-d").".log", 'a');
            fwrite($fp, "[".date("Y-m-d H:i:s")."] ".$sText.chr(10));
        fclose($fp);
    }
}

function WriteToAccessLog($sInput)
{
    $fp = fopen('c:\\website\\log\\Access.log', 'a+');
    fwrite($fp, $sInput);
    fclose($fp);
}

function writeLogSMS($sText)
{
    $fp = fopen("sms_".date("Y-m-d").".log", 'a');
    fwrite($fp, "[".date("Y-m-d H:i:s")."] ".$sText.chr(10));
    fclose($fp);
}

function writeLogEmail($sText)
{
    $fp = fopen("email_".date("Y-m-d").".log", 'a');
    fwrite($fp, "[".date("Y-m-d H:i:s")."] ".$sText.chr(10));
    fclose($fp);
}


function writeLog($sText)
{
    $fp = fopen("errors_".date("Y-m-d").".log", 'a');
    fwrite($fp, "[".date("Y-m-d H:i:s")."] ".$sText.chr(10));
    fclose($fp);
}

function writeActivityLog($sText)
{
    $fp = fopen("allMessages_".date("Y-m-d").".log", 'a');
    fwrite($fp, "[".date("Y-m-d H:i:s")."] ".$sText.chr(10));
    fclose($fp);
}

function WriteToErrorLog($sInput)
{
    $fp = fopen('c:\\website\\log\\Error.log', 'a+');
    fwrite($fp, $sInput);
    fclose($fp);
}

function GetDomain($sReferer)
{
    $debug_vars = true;

    $subs = '';
    $domainName = '';
    $tld = '';
    $tld_isReady = '';

    $gTlds = explode(',', str_replace(' ', '', "aero, biz, com, coop, info,
	jobs, museum, name, net, org, pro, travel, gov, edu, mil, int"));

    $cTlds = explode(',', str_replace(' ', '', "ac, ad, ae, af, ag, ai, al,
	am, an, ao, aq, ar, as, at, au, aw, az, ax, ba, bb, bd, be, bf, bg, bh,
	bi, bj, bm, bn, bo, br, bs, bt, bv, bw, by, bz, ca, cc, cd, cf, cg, ch,
	ci, ck, cl, cm, cn, co, cr, cs, cu, cv, cx, cy, cz, de, dj, dk, dm, do,
	dz, ec, ee, eg, eh, er, es, et, eu, fi, fj, fk, fm, fo, fr, ga, gb, gd,
	ge, gf, gg, gh, gi, gl, gm, gn, gp, gq, gr, gs, gt, gu, gw, gy, hk, hm,
	hn, hr, ht, hu, id, ie, il, im, in, io, iq, ir, is, it, je, jm, jo, jp,
	ke, kg, kh, ki, km, kn, kp, kr, kw, ky, kz, la, lb, lc, li, lk, lr, ls,
	lt, lu, lv, ly, ma, mc, md, mg, mh, mk, ml, mm, mn, mo, mp, mq, mr, ms,
	mt, mu, mv, mw, mx, my, mz, na, nc, ne, nf, ng, ni, nl, no, np, nr, nu,
	nz, om, pa, pe, pf, pg, ph, pk, pl, pm, pn, pr, ps, pt, pw, py, qa, re,
	ro, ru, rw, sa, sb, sc, sd, se, sg, sh, si, sj, sk, sl, sm, sn, so, sr,
	st, sv, sy, sz, tc, td, tf, tg, th, tj, tk, tl, tm, tn, to, tp, tr, tt,
	tv, tw, tz, ua, ug, uk, um, us, uy, uz, va,
	vc, ve, vg, vi, vn, vu, wf, ws, ye, yt, yu, za, zm, zw"));


    $tldarray = array_merge($gTlds, $cTlds);

    if (!strstr($sReferer, 'http://'))
    {
        $sReferer = "http://$sReferer";
    }

    $sRefererParsed = @parse_url(trim($sReferer));
    $sRefererHost = $sRefererParsed['host'];

    $domainarray = explode('.', $sRefererHost);
    $top = count($domainarray);

    for ($i = 0; $i < $top; $i++)
    {
        $_domainPart = array_pop($domainarray);

        if (!$tld_isReady)
        {
            if (in_array($_domainPart, $tldarray))
            {
                $tld = ".$_domainPart".$tld;
            } else
            {
                $domainName = $_domainPart;
                $tld_isReady = 1;
            }
        } else
        {
            $subs = ".$_domainPart".$subs;
        }

    }

    $sDomainName = $domainName.$tld;
    return $sDomainName;
}

function getNumbertoLetter($sInput)
{
    if ($sInput == 1)
    {
        return "A";
    } elseif ($sInput == 2)
    {
        return "B";
    } elseif ($sInput == 3)
    {
        return "C";
    } elseif ($sInput == 4)
    {
        return "D";
    } elseif ($sInput == 5)
    {
        return "E";
    } elseif ($sInput == 6)
    {
        return "F";
    } elseif ($sInput == 7)
    {
        return "G";
    } elseif ($sInput == 8)
    {
        return "H";
    } elseif ($sInput == 9)
    {
        return "I";
    } elseif ($sInput == 10)
    {
        return "J";
    } elseif ($sInput == 11)
    {
        return "K";
    } elseif ($sInput == 12)
    {
        return "L";
    } elseif ($sInput == 13)
    {
        return "M";
    } elseif ($sInput == 14)
    {
        return "N";
    } elseif ($sInput == 15)
    {
        return "O";
    } elseif ($sInput == 16)
    {
        return "P";
    } elseif ($sInput == 17)
    {
        return "Q";
    } elseif ($sInput == 18)
    {
        return "R";
    } elseif ($sInput == 19)
    {
        return "S";
    } elseif ($sInput == 20)
    {
        return "T";
    } elseif ($sInput == 21)
    {
        return "U";
    } elseif ($sInput == 22)
    {
        return "V";
    } elseif ($sInput == 23)
    {
        return "W";
    } elseif ($sInput == 24)
    {
        return "X";
    } elseif ($sInput == 25)
    {
        return "Y";
    } elseif ($sInput == 26)
    {
        return "Z";
    }
}

//Don't returns true for foreign characters
function hasInvalidCharacters($sInput)
{
    $arrChars = preg_split('//', $sInput, -1, PREG_SPLIT_NO_EMPTY);

    $bReturn = false;
    foreach ($arrChars as $sChar)
    {
        $lChar = ord($sChar);

        if ($lChar >= 128 && $lChar <= 168)
        {
            $bReturn = true;
        }
    }

    return $bReturn;
}

function isValidInput($sInput)
{
    $bValid = true;

    if (
        trim($sInput) == ""
        || strpos("x".$sInput, "http://") > 0
        || strpos("x".$sInput, "<a href=") > 0
        || strpos("x".strtolower($sInput), "flowers do you like") > 0
        || strpos("x".strtolower($sInput), "presented with flowers") > 0
        || strpos("x".strtolower($sInput), "giving flowers") > 0
        || strpos("x".strtolower($sInput), "delete this topic") > 0
        || strtolower($sInput) == "hi"
        || strtolower($sInput) == "sorry admin - my post is test"
        || strpos("x".strtolower($sInput), "how do you do?") > 0
        || strpos("x".strtolower($sInput), "Hai how are you?") > 0
        || strpos("x".strtolower($sInput), " sex") > 0
    || strpos("x".strtolower($sInput), "hello. and bye.") > 0
    || strpos("x".strtolower($sInput), "saw movies.") > 0
    || hasInvalidCharacters($sInput) == true
    ) {
        $bValid = false;
    }

    return $bValid;
}

function writeToLog($sText)
{
    $fp = fopen('logfile.log', 'a');
    fwrite($fp, $sText.chr(10).chr(13));
    fclose($fp);
}

function UploadFile($uploadFile)
{
    define('awsAccessKey', '0DT4CPJD3DXVBM1MDZR2');
    define('awsSecretKey', '5sgSs7dIt3NHYARsxP2pXSlHAaQuY7UGMo2uVrYj');

    $bucketName = "easyreverse";

    $s3 = new S3(awsAccessKey, awsSecretKey);

    if ($s3->putObjectFile($uploadFile, $bucketName, baseName($uploadFile), S3::ACL_PUBLIC_READ))
    {
        return "";
    } else
    {
        echo "ERROR Uploading File: ".$uploadFile;
    }
}

function UploadFile2($uploadFile, $bucketName)
{
    $s3 = new S3(awsAccessKey, awsSecretKey);

    if ($s3->putObjectFile($uploadFile, $bucketName, baseName($uploadFile), S3::ACL_PUBLIC_READ))
    {
        return "";
    } else
    {
        echo "ERROR Uploading File: ".$uploadFile;
    }
}

function getLettersOnly($sInput)
{
    $sValue = "";
    for ($x = 0; $x <= strlen($sInput); $x += 1)
    {
        $sChar = strtoupper(substr($sInput, $x, 1));

        if (($sChar == "A") || ($sChar == "B") || ($sChar == "C") || ($sChar == "D") || ($sChar == "E") || ($sChar == "F") ||
            ($sChar == "G") || ($sChar == "H") || ($sChar == "I") || ($sChar == "J") || ($sChar == "K") || ($sChar == "L") ||
            ($sChar == "M") || ($sChar == "N") || ($sChar == "O") || ($sChar == "P") || ($sChar == "Q") || ($sChar == "R") ||
            ($sChar == "S") || ($sChar == "T") || ($sChar == "U") || ($sChar == "V") || ($sChar == "W") || ($sChar == "X") ||
            ($sChar == "Y") || ($sChar == "Z"))
        {
            $sValue = $sValue.$sChar;
        }
    }

    return $sValue;
}

function getLettersandSpaces($sInput)
{
    $sValue = "";
    for ($x = 0; $x <= strlen($sInput); $x += 1)
    {
        $sChar = strtoupper(substr($sInput, $x, 1));

        if (($sChar == "A") || ($sChar == "B") || ($sChar == "C") || ($sChar == "D") || ($sChar == "E") || ($sChar == "F") ||
            ($sChar == "G") || ($sChar == "H") || ($sChar == "I") || ($sChar == "J") || ($sChar == "K") || ($sChar == "L") ||
            ($sChar == "M") || ($sChar == "N") || ($sChar == "O") || ($sChar == "P") || ($sChar == "Q") || ($sChar == "R") ||
            ($sChar == "S") || ($sChar == "T") || ($sChar == "U") || ($sChar == "V") || ($sChar == "W") || ($sChar == "X") ||
            ($sChar == "Y") || ($sChar == "Z") || $sChar == " ")
        {
            $sValue = $sValue.$sChar;
        }
    }

    return $sValue;
}


function GetEXT($sReferer)
{
    $debug_vars = true;

    $subs = '';
    $domainName = '';
    $tld = '';
    $tld_isReady = '';

    $gTlds = explode(',', str_replace(' ', '', "aero, biz, com, coop, info,
	jobs, museum, name, net, org, pro, travel, gov, edu, mil, int"));

    $cTlds = explode(',', str_replace(' ', '', "ac, ad, ae, af, ag, ai, al,
	am, an, ao, aq, ar, as, at, au, aw, az, ax, ba, bb, bd, be, bf, bg, bh,
	bi, bj, bm, bn, bo, br, bs, bt, bv, bw, by, bz, ca, cc, cd, cf, cg, ch,
	ci, ck, cl, cm, cn, co, cr, cs, cu, cv, cx, cy, cz, de, dj, dk, dm, do,
	dz, ec, ee, eg, eh, er, es, et, eu, fi, fj, fk, fm, fo, fr, ga, gb, gd,
	ge, gf, gg, gh, gi, gl, gm, gn, gp, gq, gr, gs, gt, gu, gw, gy, hk, hm,
	hn, hr, ht, hu, id, ie, il, im, in, io, iq, ir, is, it, je, jm, jo, jp,
	ke, kg, kh, ki, km, kn, kp, kr, kw, ky, kz, la, lb, lc, li, lk, lr, ls,
	lt, lu, lv, ly, ma, mc, md, mg, mh, mk, ml, mm, mn, mo, mp, mq, mr, ms,
	mt, mu, mv, mw, mx, my, mz, na, nc, ne, nf, ng, ni, nl, no, np, nr, nu,
	nz, om, pa, pe, pf, pg, ph, pk, pl, pm, pn, pr, ps, pt, pw, py, qa, re,
	ro, ru, rw, sa, sb, sc, sd, se, sg, sh, si, sj, sk, sl, sm, sn, so, sr,
	st, sv, sy, sz, tc, td, tf, tg, th, tj, tk, tl, tm, tn, to, tp, tr, tt,
	tv, tw, tz, ua, ug, uk, um, us, uy, uz, va,
	vc, ve, vg, vi, vn, vu, wf, ws, ye, yt, yu, za, zm, zw"));


    $tldarray = array_merge($gTlds, $cTlds);

    if (!strstr($sReferer, 'http://'))
    {
        $sReferer = "http://$sReferer";
    }

    $sRefererParsed = @parse_url(trim($sReferer));
    $sRefererHost = $sRefererParsed['host'];

    $domainarray = explode('.', $sRefererHost);
    $top = count($domainarray);

    for ($i = 0; $i < $top; $i++)
    {
        $_domainPart = array_pop($domainarray);

        if (!$tld_isReady)
        {
            if (in_array($_domainPart, $tldarray))
            {
                $tld = ".$_domainPart".$tld;
            } else
            {
                $domainName = $_domainPart;
                $tld_isReady = 1;
            }
        } else
        {
            $subs = ".$_domainPart".$subs;
        }

    }

    $sDomainName = $domainName.$tld;
    return substr($tld, 1);
}

function getPhoneNumber($sURL)
{
    $sSearch = "";

    $sURL = urldecode($sURL);

    $sURL = str_replace("(", "", $sURL);
    $sURL = str_replace(")", "", $sURL);
    $sURL = str_replace(chr(34), "", $sURL);

    $lSearchPos = strpos($sURL, "?q=");

    if ($lSearchPos == 0)
    {
        $lSearchPos = strpos($sURL, "&q=");
    }

    if ($lSearchPos > 0)
    {
        $sSearch = substr($sURL, $lSearchPos + 3);

        $lPosition = strpos($sSearch, "&");

        if ($lPosition > 0)
        {
            $lAmp = strpos($sSearch, "&");
            $sSearch = NumbersOnly(substr($sSearch, 0, $lAmp));

            //Remove 1 if first
            if (substr($sSearch, 0, 1) == 1)
            {
                $sSearch = substr($sSearch, 1);
            }

            $sSearch = substr($sSearch, 0, 10);
        } else
        {
            //No ending &
            $sSearch = NumbersOnly(substr($sSearch, 0));

            $sSearch = substr($sSearch, 0, 10);

            //Remove 1 if first
            if (substr($sSearch, 0, 1) == 1)
            {
                $sSearch = substr($sSearch, 1);
            }
        }
    }

    if (strlen($sSearch) == 10)
    {
        return $sSearch;
    } else
    {
        return "";
    }
}

function numbersOnly($sInput)
{
    $sValue = "";
    for ($x = 0; $x <= strlen($sInput); $x += 1)
    {
        $sChar = substr($sInput, $x, 1);

        if (($sChar == "0") || ($sChar == "1") || ($sChar == "2") || ($sChar == "3") || ($sChar == "4") || ($sChar == "5") || ($sChar == "6") || ($sChar == "7") || ($sChar == "8") || ($sChar == "9") || ($sChar == "."))
        {
            $sValue = $sValue.$sChar;
        }
    }

    return $sValue;
}
function phoneFormat($sInput)
{
    $sPhoneNUMBERS = numbersOnly(trim($sInput));
    $sPhoneNUMBERS = str_replace(".", "", $sPhoneNUMBERS);

    if (substr($sPhoneNUMBERS, 0, 1) == "1")
    {
        //Remove first 0 or 1
        $sPhoneNUMBERS = substr($sPhoneNUMBERS, 1);
    }

    $sReturn = "";
    if (strlen($sPhoneNUMBERS) >= 10)
    {
        $sReturn = substr($sPhoneNUMBERS, 0, 3)."-".substr($sPhoneNUMBERS, 3, 3)."-".substr($sPhoneNUMBERS, 6, 4);
    } elseif (strlen($sPhoneNUMBERS) == 6)
    {
        $sReturn = substr($sPhoneNUMBERS, 0, 3)."-".substr($sPhoneNUMBERS, 3, 3);
    }
    return $sReturn;

}

function phoneFormat2($sInput)
{
    $sPhoneNUMBERS = $sInput;

    $sReturn = substr($sPhoneNUMBERS, 0, 3)."-".substr($sPhoneNUMBERS, 3, 3)."-".substr($sPhoneNUMBERS, 6, 4);

    return $sReturn;

}


function hasNumbers($sInput)
{
    $sInput = trim(str_replace("-", "", $sInput));

    $bHasNumbers = 0;
    foreach (str_split($sInput) as $sChar)
    {
        switch ($sChar)
        {
            case "0":
                $bHasNumbers = 1;
                break;
            case "1":
                $bHasNumbers = 1;
                break;
            case "2":
                $bHasNumbers = 1;
                break;
            case "3":
                $bHasNumbers = 1;
                break;
            case "4":
                $bHasNumbers = 1;
                break;
            case "5":
                $bHasNumbers = 1;
                break;
            case "6":
                $bHasNumbers = 1;
                break;
            case "7":
                $bHasNumbers = 1;
                break;
            case "8":
                $bHasNumbers = 1;
                break;
            case "9":
                $bHasNumbers = 1;
                break;
        }
    }

    return $bHasNumbers;
}

function formatTo3Digits($lInput)
{
    if ($lInput < 10)
    {
        $sReturn = "00".$lInput;
    } elseif ($lInput < 100)
    {
        $sReturn = "0".$lInput;
    } else
    {
        $sReturn = $lInput;
    }

    return $sReturn;
}

function GoogleGeoCode($sAddress)
{
    if (strpos($sAddress, "#") > 0)
    {
        //Remove everything after (& including) the #
        $lComma = strpos($sAddress, ",");

        $sBaseAddress = substr($sAddress, 0, strpos($sAddress, "#") - 1);

        $sAddress = $sBaseAddress.substr($sAddress, $lComma);

        //echo "New Address=".$sAddress;
        //die;
    }

    $sKey = "ABQIAAAAYLb-Tc3B5qrklK5_YTvNjRQlO2Q1C3id9mg8ogqkpfonlIHp3RTNqHaHlzvSnEZn34DjGX032_waIw";

    $base_url = "http://maps.google.com/maps/geo?output=xml"."&key=".$sKey;

    $request_url = $base_url . "&q=" . urlencode($sAddress);
    $xml = @simplexml_load_file($request_url);

    $status = @$xml->Response->Status->code;
    if (strcmp($status, "200") == 0)
    {
        // Successful geocode
        $geocode_pending = false;
        $coordinates = $xml->Response->Placemark->Point->coordinates;
        $coordinatesSplit = explode(",", $coordinates);
        // Format: Longitude, Latitude, Altitude
        $lat = $coordinatesSplit[1];
        $lng = $coordinatesSplit[0];

        return $lat.",".$lng;
    } elseif (strcmp($status, "620") == 0)
    {
        // sent geocodes too fast
        //return "Coordinates Sent Too Fast";
    } else
    {
        // failure to geocode
        //return "Address " . $address . " failed to geocoded. ";
    }
}

function getPlace($lCompanyID, $pLatitude, $pLongitude, $bName = false)
{
    $conn = getConnectionI();

    $sSQL = "SELECT * FROM gps.places WHERE PLCnCompanyID = ".$lCompanyID;
    $result = getData($sSQL, $conn);

    $sReturn = "";
    while ($row = getResultB($result))
    {
        $lPlaceID = $row["PLCnID"];
        $sName = $row["PLCsName"];
        $pCurrentLatitude = $row["PLCnLatitude"];
        $pCurrentLongitude = $row["PLCnLongitude"];

        $pDistance = getDistance($pLatitude, $pLongitude, $pCurrentLatitude, $pCurrentLongitude);

        if ($pDistance < PLACE_DISTANCE)
        {
            if ($bName == true)
            {
                $sReturn = $sName;
            } else
            {
                $sReturn = $lPlaceID;
            }
        }
    }

    return $sReturn;
}

function selectDB($sDB, $conn)
{
    mysqli_select_db($conn, $sDB);
}

function downloadFile($sURL)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $sURL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($ch);

    return $output;
}

function formatTo4Digits($lNumber)
{
    $sReturn = "";
    if ($lNumber < 10)
    {
        $sReturn = "000".$lNumber;
    } elseif ($lNumber < 100)
    {
        $sReturn = "00".$lNumber;
    } elseif ($lNumber < 1000)
    {
        $sReturn = "0".$lNumber;
    } else
    {
        $sReturn = $lNumber;
    }

    return $sReturn;
}

function createGoogleMap($pLatitude, $pLongitude, $sCity)
{ ?>
    <script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAAYLb-Tc3B5qrklK5_YTvNjRTOcjdkadzcbe--YJNf1Xwws-4FtxRk-CFnPFLIYNJwPAIqpNoRdkAIlw" type="text/javascript"></script>

    <script type="text/javascript">
    //<![CDATA[
    function load() {
      if (GBrowserIsCompatible())
      {
		var map = new GMap2(document.getElementById("map"));
		map.setCenter(new GLatLng(<?php echo $pLatitude.",".$pLongitude?>),5);

		map.addControl(new GLargeMapControl())
		map.addControl(new GScaleControl());

		// ===== Start with an empty GLatLngBounds object =====
		var bounds = new GLatLngBounds();

		var point = new GLatLng(<?php echo $pLatitude.", ".$pLongitude?>);
		var thepoint = new Object();
		thepoint.latitude = <?php echo $pLatitude?>;
		thepoint.longitude = <?php echo $pLongitude?>;
		var marker = createMarker(thepoint, '<?php echo str_replace("'", "", $sCity)?>', "star.gif")
		map.addOverlay(marker);
	  }
    }

      function createMarker(pointData, html, sIcon)
      {
      	//alert(pointData.latitude + ', ' + pointData.longitude);
		var latlng = new GLatLng(pointData.latitude, pointData.longitude);
		var icon = new GIcon();

		icon.image = "images/" + sIcon;
		icon.iconSize = new GSize(23,40);
		icon.iconAnchor = new GPoint(12, -20);
		icon.infoWindowAnchor = new GPoint(12, 0);

		var marker = new GMarker(latlng, icon)

        GEvent.addListener(marker, "click", function()
        {
	      marker.openInfoWindowHtml(html);
        });

        return marker;
      }

    //]]>
    </script>
	<div id="map" style="width: 450px; height: 250px"></div>


<?php }

function createGoogleMapsAREA($pMinLat, $pMaxLat, $pMinLong, $pMaxLong, $lHeight, $lWidth)
{
    $pAvgLat = ($pMinLat + $pMaxLat) / 2;
    $pAvgLong = ($pMinLong + $pMaxLong) / 2;

    $zcdDistance = new DistanceAssistant();
    $pDistance1 = $zcdDistance->Calculate($pMinLat, $pMinLong, $pMinLat, $pMaxLong);
    $pDistance2 = $zcdDistance->Calculate($pMinLat, $pMaxLong, $pMaxLat, $pMaxLong);
    $pArea = $pDistance1 * $pDistance2;

    $lZoom = 7;
    if ($pArea < 100)
    {
        $lZoom = 13;
    } elseif ($pArea < 250)
    {
        $lZoom = 10;
    } elseif ($pArea < 500)
    {
        $lZoom = 9;
    } elseif ($pArea < 2000)
    {
        $lZoom = 8;
    } elseif ($pArea < 10000)
    {
        $lZoom = 7;
    } elseif ($pArea < 60000)
    {
        $lZoom = 6;
    } elseif ($pArea > 6000000)
    {
        $lZoom = 2;
    }
    ?>
<link href="http://code.google.com/apis/maps/documentation/javascript/examples/default.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="//maps.googleapis.com/maps/api/js?sensor=false"></script>

<script type="text/javascript"> 
 
  function initialize() {
    var myLatLng = new google.maps.LatLng(<?php echo $pAvgLat.", ".$pAvgLong?>);
    var myOptions = {
        zoom: <?php echo $lZoom?>,
        center: myLatLng,
	disableDefaultUI: true,
	mapTypeId: google.maps.MapTypeId.ROADMAP
    };
 
    var sArea;
 
    var map = new google.maps.Map(document.getElementById("map_canvas"),
        myOptions);
 
    var arrCoords = [
        new google.maps.LatLng(<?php echo $pMinLat.", ".$pMinLong?>),
        new google.maps.LatLng(<?php echo $pMinLat.", ".$pMaxLong?>),
        new google.maps.LatLng(<?php echo $pMaxLat.", ".$pMaxLong?>),
        new google.maps.LatLng(<?php echo $pMaxLat.", ".$pMinLong?>)
    ];
 
    // Construct the polygon
    sArea = new google.maps.Polygon({
      paths: arrCoords,
      strokeColor: "#FF0000",
      strokeOpacity: 0.8,
      strokeWeight: 2,
      fillColor: "#FF0000",
      fillOpacity: 0.35
    });
 
   sArea.setMap(map);
  }
</script>

<div id="map_canvas"  style="width: <?php echo $lWidth?>px; height: <?php echo $lHeight?>px"></div>
<?php }

function createGoogleMapsRADIUS($pLat, $pLng, $lRadius, $lHeight, $lWidth)
{
    ?>
<link href="http://code.google.com/apis/maps/documentation/javascript/examples/default.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="//maps.googleapis.com/maps/api/js?sensor=false"></script>

<script type="text/javascript"> 
 
  function initialize() {
    var myLatLng = new google.maps.LatLng(<?php echo $pLat.", ".$pLng?>);
    var myOptions = {
        zoom: 12,
        center: myLatLng,
	disableDefaultUI: true,
	mapTypeId: google.maps.MapTypeId.ROADMAP
    };
 
    var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
 
	var sCircle=new google.maps.Circle({strokeColor:"#FF0000",
		strokeOpacity:0.8,
		strokeWeight:2,
		fillColor:"#FF0000",
		fillOpacity:0.35,
		map:map,
		center:myLatLng,
		radius:4500
		});
  }
</script>

<div id="map_canvas"  style="width: <?php echo $lWidth?>px; height: <?php echo $lHeight?>px"></div>
<?php }


class DistanceAssistant
{
    public function DistanceAssistant()
    {
    }

    public function Calculate(
        $dblLat1,
        $dblLong1,
        $dblLat2,
        $dblLong2
    ) {
        $EARTH_RADIUS_MILES = 3963;
        $dist = 0;

        //convert degrees to radians
        $dblLat1 = $dblLat1 * M_PI / 180;
        $dblLong1 = $dblLong1 * M_PI / 180;
        $dblLat2 = $dblLat2 * M_PI / 180;
        $dblLong2 = $dblLong2 * M_PI / 180;

        //if ($dblLat1 != $dblLat2 || $dblLong1 != $dblLong2)
        //KRH
        if (ABS($dblLat1 - $dblLat2) > 0.0000001 || ABS($dblLong1 - $dblLong2) > 0.0000001)
        {
            //the two points are not the same
            $dist =
                sin($dblLat1) * sin($dblLat2)
                + cos($dblLat1) * cos($dblLat2)
                * cos($dblLong2 - $dblLong1);

            $dist =
                $EARTH_RADIUS_MILES
                * (-1 * atan($dist / sqrt(1 - $dist * $dist)) + M_PI / 2);
        }
        return $dist;
    }

}

function getExtension($lNumber)
{
    if ($lNumber == 1)
    {
        $sReturn = "st";
    } elseif ($lNumber == 2)
    {
        $sReturn = "nd";
    } elseif ($lNumber == 3)
    {
        $sReturn = "rd";
    } else
    {
        $sReturn = "th";
    }

    return $sReturn;
}

function OLD_S3_SendEmail($sTo, $sFrom, $sTitle, $sBody)
{
    require_once("S3/S3.php");

    $client = SesClient::factory(array(
         'version' => 'latest',
         'region' => 'us-east-1',
         'credentials' => array(
              'key' => '0DT4CPJD3DXVBM1MDZR2',
              'secret'  => '5sgSs7dIt3NHYARsxP2pXSlHAaQuY7UGMo2uVrYj',
             )
     ));

    $request = array();
    $request['Source'] = $sFrom;
    $request['Destination']['ToAddresses'] = array($sTo);
    $request['Message']['Subject']['Data'] = $sTitle;
    $request['Message']['Body']['Text']['Data'] = $sBody;

    try
    {
        $result = $client->sendEmail($request);
        $messageId = $result->get('MessageId');

        writeLogEmail($sTo." => ".$sTitle." => ".$sBody);

        return "Email sent! Message ID: $messageId"."\n";

    } catch (Exception $e)
    {
        $sOutput = "The email was not sent. Error message: ".$e->getMessage()."\n";
        return $sOutput;
    }
}

function SendEmail($sTo, $sFrom, $sTitle, $sBody, $sBodyTEXT = "")
{
    //Uses DKIM through Amazon SES. 4/4/22
    $sURL = "http://64.227.49.56/email/index.php";

    $ch = curl_init();

    $arrData["sTo"] = $sTo;
    $arrData["sFrom"] = $sFrom;
    $arrData["sSubject"] = $sTitle;
    $arrData["sBody"] = $sBody;
    $arrData["sBodyTEXT"] = $sBodyTEXT;

    curl_setopt($ch, CURLOPT_URL, $sURL);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $arrData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $sOutput = curl_exec($ch);

    return $sOutput;
}

function SendEmailB($sTo, $sTitle, $sBody)
{
    $sFrom = "noreply@connecttoneighbors.com";
    $sEncryption = md5("Respect1");

    $sURL = SERVER_LINUX_IP."/SendEmailB.php".
            "?sEncryption=".$sEncryption.
            "&sFrom=".trim($sFrom).
            "&sTo=".trim($sTo).
            "&sTitle=".urlencode($sTitle).
            "&sBody=".urlencode($sBody);
    $arrResponse = file_get_contents($sURL);

    print_r($arrResponse);
}


function old_SendEmail($sTo, $sFrom, $sTitle, $sBody)
{
    require_once("ses.php");

    $ses = new SimpleEmailService('0DT4CPJD3DXVBM1MDZR2', '5sgSs7dIt3NHYARsxP2pXSlHAaQuY7UGMo2uVrYj');

    $m = new SimpleEmailServiceMessage();
    $m->addTo($sTo);
    $m->setFrom($sFrom);
    $m->setSubject($sTitle);
    $m->setMessageFromString($sBody);

    print_r($ses->sendEmail($m));
}

function exq($sSQL, $conn)
{
    $connINSERT = getInsertConnection();

    $dStart = explode(" ", microtime());
    $dStart = $dStart[1] + $dStart[0];

    if (!$result = getData($sSQL, $conn))
    {
        echo "ERROR: ".$sSQL."<br>".mysql_error();
        die;
    }

    $dEnd = explode(" ", microtime());
    $dEnd = $dEnd[1] + $dEnd[0];

    $lTime = ($dEnd - $dStart) * 1000;

    $sSQL2 = "INSERT INTO misc3.Queries (QUEdDate, QUEsSQL, QUEnLoadTime) ".
        " VALUES ('".date("Y-m-d H:i:s").
        "', ".SQLFormatField($sSQL, "String").
        ", ".$lTime.") ";

    if (!$result2 = getData($sSQL2, $connINSERT))
    {
        echo "ERROR: ".$sSQL2."<br>".mysql_error();
        die;
    }

    return $result;
}


/**
 * xml2array() will convert the given XML text to an array in the XML structure.
 * Link: http://www.bin-co.com/php/scripts/xml2array/
 * Arguments : $contents - The XML text
 *                $get_attributes - 1 or 0. If this is 1 the function will get the attributes as well as the tag values - this results in a different array structure in the return value.
 *                $priority - Can be 'tag' or 'attribute'. This will change the way the resulting array sturcture. For 'tag', the tags are given more importance.
 * Return: The parsed XML in an array form. Use print_r() to see the resulting array structure.
 * Examples: $array =  xml2array(file_get_contents('feed.xml'));
 *              $array =  xml2array(file_get_contents('feed.xml', 1, 'attribute'));
 */
function xml2array($contents, $get_attributes = 1, $priority = 'tag')
{
    if (!$contents)
    {
        return array();
    }

    if (!function_exists('xml_parser_create'))
    {
        //print "'xml_parser_create()' function not found!";
        return array();
    }

    //Get the XML parser of PHP - PHP must have this module for the parser to work
    $parser = xml_parser_create('');
    xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8"); # http://minutillo.com/steve/weblog/2004/6/17/php-xml-and-character-encodings-a-tale-of-sadness-rage-and-data-loss
    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
    xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
    xml_parse_into_struct($parser, trim($contents), $xml_values);
    xml_parser_free($parser);

    if (!$xml_values)
    {
        return;
    }//Hmm...

    //Initializations
    $xml_array = array();
    $parents = array();
    $opened_tags = array();
    $arr = array();

    $current = &$xml_array; //Refference

    //Go through the tags.
    $repeated_tag_index = array();//Multiple tags with same name will be turned into an array
    foreach ($xml_values as $data)
    {
        unset($attributes,$value);//Remove existing values, or there will be trouble

        //This command will extract these variables into the foreach scope
        // tag(string), type(string), level(int), attributes(array).
        extract($data);//We could use the array by itself, but this cooler.

        $result = array();
        $attributes_data = array();

        if (isset($value))
        {
            if ($priority == 'tag')
            {
                $result = $value;
            } else
            {
                $result['value'] = $value;
            } //Put the value in a assoc array if we are in the 'Attribute' mode
        }

        //Set the attributes too.
        if (isset($attributes) and $get_attributes)
        {
            foreach ($attributes as $attr => $val)
            {
                if ($priority == 'tag')
                {
                    $attributes_data[$attr] = $val;
                } else
                {
                    $result['attr'][$attr] = $val;
                } //Set all the attributes in a array called 'attr'
            }
        }

        //See tag status and do the needed.
        if ($type == "open") //The starting of the tag '<tag>'
        {$parent[$level - 1] = &$current;
            if (!is_array($current) or (!in_array($tag, array_keys($current)))) //Insert New tag
            {$current[$tag] = $result;
                if ($attributes_data)
                {
                    $current[$tag. '_attr'] = $attributes_data;
                }
                $repeated_tag_index[$tag.'_'.$level] = 1;

                $current = &$current[$tag];

            } else //There was another element with the same tag name
            {if (isset($current[$tag][0])) //If there is a 0th element it is already an array
            {$current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result;
                $repeated_tag_index[$tag.'_'.$level]++;
            } else //This section will make the value an array if multiple tags with the same name appear together
            {$current[$tag] = array($current[$tag],$result);//This will combine the existing item and the new item together to make an array
                $repeated_tag_index[$tag.'_'.$level] = 2;

                if (isset($current[$tag.'_attr'])) //The attribute of the last(0th) tag must be moved as well
                {$current[$tag]['0_attr'] = $current[$tag.'_attr'];
                    unset($current[$tag.'_attr']);
                }

            }
                $last_item_index = $repeated_tag_index[$tag.'_'.$level] - 1;
                $current = &$current[$tag][$last_item_index];
            }

        } elseif ($type == "complete") //Tags that ends in 1 line '<tag />'
        {//See if the key is already taken.
            if (!isset($current[$tag])) //New Key
            {$current[$tag] = $result;
                $repeated_tag_index[$tag.'_'.$level] = 1;
                if ($priority == 'tag' and $attributes_data)
                {
                    $current[$tag. '_attr'] = $attributes_data;
                }

            } else //If taken, put all things inside a list(array)
            {if (isset($current[$tag][0]) and is_array($current[$tag])) //If it is already an array...
            {// ...push the new element into that array.
                $current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result;

                if ($priority == 'tag' and $get_attributes and $attributes_data)
                {
                    $current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data;
                }
                $repeated_tag_index[$tag.'_'.$level]++;

            } else //If it is not an array...
            {$current[$tag] = array($current[$tag],$result); //...Make it an array using using the existing value and the new value
                $repeated_tag_index[$tag.'_'.$level] = 1;
                if ($priority == 'tag' and $get_attributes)
                {
                    if (isset($current[$tag.'_attr'])) //The attribute of the last(0th) tag must be moved as well
                    {$current[$tag]['0_attr'] = $current[$tag.'_attr'];
                        unset($current[$tag.'_attr']);
                    }

                    if ($attributes_data)
                    {
                        $current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data;
                    }
                }
                $repeated_tag_index[$tag.'_'.$level]++; //0 and 1 index is already taken
            }
            }

        } elseif ($type == 'close') //End of tag '</tag>'
        {$current = &$parent[$level - 1];
        }
    }

    return($xml_array);
}

function hexDword($hex)
{
    static $max = '2147483647'; // HIGH VALUES = 7F FF FF FF

    // ENSURE WE HAVE A USABLE INPUT
    $num = hexdec($hex);
    $int = intval($num);
    if ($num !== $int)
    {
        return false;
    }

    // PAD TO 8 HEX DIGITS
    $hex = str_pad($hex, 8, '0', STR_PAD_LEFT);

    // DETERMINE THE SIGN BIT
    $bin = base_convert($hex, 16, 2);
    $bin = str_pad($bin, 32, '0', STR_PAD_LEFT);
    $arr = str_split($bin);
    $neg = false;
    if ($arr[0] == '1')
    {
        $neg = true;
    }

    // RESET THE SIGN BIT TO ZERO
    unset($arr[0]);
    $bin = '0' . implode(null, $arr);

    // CONVERT TO DECIMAL
    $ans = base_convert($bin, 2, 10);

    // IF NEGATIVE, USE TWOS-COMPLEMENT
    if ($neg)
    {
        $ans = $ans - $max - 1;
    }
    return $ans;
}

function getDistance($lat1, $lon1, $lat2, $lon2)
{
    $theta = $lon1 - $lon2;
    $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
    $dist = acos($dist);
    $dist = rad2deg($dist);
    $miles = $dist * 60 * 1.1515;

    $sText = "[".date("Y-m-d H:i:s")."] ".$lat1.", ".$lon1." => ".$lat2.", ".$lon2." = ".$miles.chr(10);
    $fp = fopen('getDistance.log', 'a');
    fwrite($fp, $sText);
    fclose($fp);


    return $miles;
}

function addCompany($sName, $sAddress, $sCity, $sState)
{
    $arrData = geocodeAddress($sAddress, $sCity, $sState);

    $pLatitude = $arrData["lat"];
    $pLongitude = $arrData["lng"];

    $sTimeZone = getTimeZone($pLatitude, $pLongitude);

    $sSQL = "SELECT IFNULL(MAX(COMnID) + 1, 1) as lID FROM companies";
    $result = getData($sSQL, $conn);

    $lID = 1;
    while ($row = getResultB($result))
    {
        $lID = $row["lID"];
    }

    $sSQL = "INSERT INTO companies (COMnID, COMsName, COMbActive, COMbCorporate, COMnTimeZone) ".
            " VALUES (".$lID.
            ", ".SQLFormatField($sName, "String").
            ", ".$bActive.
            ", ".$bCorporate.
            ", ".$lTimeZone.
            ") ";
    if (!$result = getData($sSQL, $conn))
    {
        echo "ERROR: ".$sSQL;
        die;
    }
}

function getTimeZone($pLatitude, $pLongitude, $bReturnID = false)
{
    extract($GLOBALS);

    $sURL = "https://maps.googleapis.com/maps/api/timezone/xml?location=".$pLatitude.",".$pLongitude."&timestamp=0&key=".GOOGLE_MAPS_KEY;
    $sContent = file_get_contents($sURL);

    $sXML = new SimpleXMLElement($sContent);

    $sTimeZone = $sXML->{'time_zone_id'};

    if ($bReturnID == true)
    {
        $sSQL = "SELECT TZOnID FROM timezones WHERE TZOsValue = '".$sTimeZone."'";
        $result = getData($sSQL, $conn);

        $lReturn = -1;
        while ($row = getResultB($result))
        {
            $lReturn = $row["TZOnID"];
        }

        return $lReturn;
    } else
    {
        return $sTimeZone;
    }

}

function geocodeLatLong($pLatitude, $pLongitude)
{
    $conn = getConnectionI();

    $sURL = "https://maps.googleapis.com/maps/api/geocode/xml?latlng=".
        $pLatitude.",".$pLongitude.
        "&key=".GOOGLE_MAPS_KEY;
    $sContents = file_get_contents($sURL);

    $arrDataRAW = xml2array($sContents);
    $sFullAddressRAW = @$arrDataRAW["GeocodeResponse"]["result"][0]["formatted_address"];

    $sFullAddress = str_replace(", USA", "", $sFullAddressRAW);


    $sSQL = "REPLACE INTO gps.geocoding (GEOdDate, GEOnLat, GEOnLong, GEOsFullAddress) ".
            " VALUES ('".date("Y-m-d H:i:s")."' ".
            ", ".SQLFormatField($pLatitude, "String").
            ", ".SQLFormatField($pLongitude, "String").
            ", ".SQLFormatField($sFullAddress, "String").
            ")";

    return $sFullAddress;
}


function geocodeAddress($sAddress, $sCity, $sState)
{
    $sURL = "https://maps.googleapis.com/maps/api/geocode/xml?address=".
        urlencode($sAddress).",".urlencode($sCity).",".urlencode($sState).
        "&key=".GOOGLE_MAPS_KEY;
    $sContents = file_get_contents($sURL);

    $arrData = xml2array($sContents);

    $pLat = @$arrData["GeocodeResponse"]["result"]["geometry"]["location"]["lat"];
    $pLong = @$arrData["GeocodeResponse"]["result"]["geometry"]["location"]["lng"];

    $arrReturn["lat"] = $pLat;
    $arrReturn["lng"] = $pLong;

    return $arrReturn;
}

/*
Description: The point-in-polygon algorithm allows you to check if a point is
inside a polygon or outside of it.
Author: Micha�l Niessen (2009)
Website: http://AssemblySys.com

If you find this script useful, you can show your
appreciation by getting Micha�l a cup of coffee ;)
PayPal: michael.niessen@assemblysys.com

As long as this notice (including author name and details) is included and
UNALTERED, this code is licensed under the GNU General Public License version 3:
http://www.gnu.org/licenses/gpl.html
*/

class pointLocation
{
    public $pointOnVertex = true; // Check if the point sits exactly on one of the vertices?

    public function pointLocation()
    {
    }

    public function pointInPolygon($point, $polygon, $pointOnVertex = true)
    {
        $this->pointOnVertex = $pointOnVertex;

        // Transform string coordinates into arrays with x and y values
        $point = $this->pointStringToCoordinates($point);
        $vertices = array();
        foreach ($polygon as $vertex)
        {
            $vertices[] = $this->pointStringToCoordinates($vertex);
        }

        // Check if the point sits exactly on a vertex
        if ($this->pointOnVertex == true and $this->pointOnVertex($point, $vertices) == true)
        {
            return "vertex";
        }

        // Check if the point is inside the polygon or on the boundary
        $intersections = 0;
        $vertices_count = count($vertices);

        for ($i = 1; $i < $vertices_count; $i++)
        {
            $vertex1 = $vertices[$i - 1];
            $vertex2 = $vertices[$i];
            if ($vertex1['y'] == $vertex2['y'] and $vertex1['y'] == $point['y'] and $point['x'] > min($vertex1['x'], $vertex2['x']) and $point['x'] < max($vertex1['x'], $vertex2['x'])) // Check if point is on an horizontal polygon boundary
            {return "boundary";
            }
            if ($point['y'] > min($vertex1['y'], $vertex2['y']) and $point['y'] <= max($vertex1['y'], $vertex2['y']) and $point['x'] <= max($vertex1['x'], $vertex2['x']) and $vertex1['y'] != $vertex2['y'])
            {
                $xinters = ($point['y'] - $vertex1['y']) * ($vertex2['x'] - $vertex1['x']) / ($vertex2['y'] - $vertex1['y']) + $vertex1['x'];
                if ($xinters == $point['x']) // Check if point is on the polygon boundary (other than horizontal)
                {return "boundary";
                }
                if ($vertex1['x'] == $vertex2['x'] || $point['x'] <= $xinters)
                {
                    $intersections++;
                }
            }
        }
        // If the number of edges we passed through is odd, then it's in the polygon.
        if ($intersections % 2 != 0)
        {
            return "inside";
        } else
        {
            return "outside";
        }
    }

    public function pointOnVertex($point, $vertices)
    {
        foreach ($vertices as $vertex)
        {
            if ($point == $vertex)
            {
                return true;
            }
        }

    }

    public function pointStringToCoordinates($pointString)
    {
        $coordinates = explode(" ", $pointString);
        return array("x" => $coordinates[0], "y" => $coordinates[1]);
    }

}

function isMobile()
{
    $detect = new Mobile_Detect();

    $bMobile = $detect->isMobile();
    $bTablet = $detect->isTablet();

    //print_r($detect->getProperties());

    if ($bMobile == true)
    {
        return true;
    } else
    {
        return false;
    }
}

function getPanel($sTitle, $sText)
{
    ?>
<div class="col-md-5">
    <div class="panel panel-primary">
	<div class="panel-heading"><h2><b><?php echo $sTitle?></b></h2></div>
     <div class="panel-body"><h3><?php echo $sText?></h3></div>
    </div>
</div>
<?php
}
function displayCommon()
{
    if (1 == 2)
    {
        ?>
<img src="images/nocontract.png" alt="No Contract" border="0"><br>
    <?php }
    }

function displayAnalytics($bNoCSS = false)
{
    extract($GLOBALS);
    ?>
    <?php if ($bIndex == true)
    { ?>
    <meta name="robots" content="index,follow" />
    <?php } else
    { ?>
    <META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
    <?php } ?>
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = 'https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v3.1&appId=1465577733585585&autoLogAppEvents=1';
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-76737156-1', 'auto');
  ga('send', 'pageview');

</script>

<!-- Global site tag (gtag.js) - Google Ads: 843061244 -->
<script async src="https://www.googletagmanager.com/gtag/js?id=AW-843061244"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'AW-843061244');
</script>

<?php if ($bNoCSS == false)
{ ?>
    <?php if (1 == 2)
    { ?><link rel="stylesheet" href="/bootstrap/css/bootstrapMOD.css"><?php } ?>

    <style>
    /*!
     * Bootstrap v3.3.7 (http://getbootstrap.com)
     * Copyright 2011-2016 Twitter, Inc.
     * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
     *//*! normalize.css v3.0.3 | MIT License | github.com/necolas/normalize.css */.label,sub,sup{vertical-align:baseline}hr,img{border:0}body,figure{margin:0}.btn-group>.btn-group,.btn-toolbar .btn,.btn-toolbar .btn-group,.btn-toolbar .input-group,.col-xs-1,.col-xs-10,.col-xs-11,.col-xs-12,.col-xs-2,.col-xs-3,.col-xs-4,.col-xs-5,.col-xs-6,.col-xs-7,.col-xs-8,.col-xs-9,.dropdown-menu{float:left}.navbar-fixed-bottom .navbar-collapse,.navbar-fixed-top .navbar-collapse,.pre-scrollable{max-height:340px}html{font-family:sans-serif;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%}article,aside,details,figcaption,figure,footer,header,hgroup,main,menu,nav,section,summary{display:block}audio,canvas,progress,video{display:inline-block;vertical-align:baseline}audio:not([controls]){display:none;height:0}[hidden],template{display:none}a{background-color:transparent}a:active,a:hover{outline:0}b,optgroup,strong{font-weight:700}dfn{font-style:italic}h1{margin:.67em 0}mark{color:#000;background:#ff0}sub,sup{position:relative;font-size:75%;line-height:0}sup{top:-.5em}sub{bottom:-.25em}img{vertical-align:middle}svg:not(:root){overflow:hidden}hr{height:0;-webkit-box-sizing:content-box;-moz-box-sizing:content-box;box-sizing:content-box}pre,textarea{overflow:auto}code,kbd,pre,samp{font-size:1em}button,input,optgroup,select,textarea{margin:0;font:inherit;color:inherit}.glyphicon,address{font-style:normal}button{overflow:visible}button,select{text-transform:none}button,html input[type=button],input[type=reset],input[type=submit]{-webkit-appearance:button;cursor:pointer}button[disabled],html input[disabled]{cursor:default}button::-moz-focus-inner,input::-moz-focus-inner{padding:0;border:0}input[type=checkbox],input[type=radio]{-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box;padding:0}input[type=number]::-webkit-inner-spin-button,input[type=number]::-webkit-outer-spin-button{height:auto}input[type=search]::-webkit-search-cancel-button,input[type=search]::-webkit-search-decoration{-webkit-appearance:none}table{border-spacing:0;border-collapse:collapse}td,th{padding:0}/*! Source: https://github.com/h5bp/html5-boilerplate/blob/master/src/css/main.css */@media print{blockquote,img,pre,tr{page-break-inside:avoid}*,:after,:before{color:#000!important;text-shadow:none!important;background:0 0!important;-webkit-box-shadow:none!important;box-shadow:none!important}a,a:visited{text-decoration:underline}a[href]:after{content:" (" attr(href) ")"}abbr[title]:after{content:" (" attr(title) ")"}a[href^="javascript:"]:after,a[href^="#"]:after{content:""}blockquote,pre{border:1px solid #999}thead{display:table-header-group}img{max-width:100%!important}h2,h3,p{orphans:3;widows:3}h2,h3{page-break-after:avoid}.navbar{display:none}.btn>.caret,.dropup>.btn>.caret{border-top-color:#000!important}.label{border:1px solid #000}.table{border-collapse:collapse!important}.table td,.table th{background-color:#fff!important}.table-bordered td,.table-bordered th{border:0 solid #ddd!important}}.dropdown-menu,.modal-content{-webkit-background-clip:padding-box}.btn,.btn-danger.active,.btn-danger:active,.btn-default.active,.btn-default:active,.btn-info.active,.btn-info:active,.btn-primary.active,.btn-primary:active,.btn-warning.active,.btn-warning:active,.btn.active,.btn:active,.dropdown-menu>.disabled>a:focus,.dropdown-menu>.disabled>a:hover,.form-control,.navbar-toggle,.open>.dropdown-toggle.btn-danger,.open>.dropdown-toggle.btn-default,.open>.dropdown-toggle.btn-info,.open>.dropdown-toggle.btn-primary,.open>.dropdown-toggle.btn-warning{background-image:none}.img-thumbnail,body{background-color:#fff}@font-face{font-family:'Glyphicons Halflings';src:url(../fonts/glyphicons-halflings-regular.eot);src:url(../fonts/glyphicons-halflings-regular.eot?#iefix) format('embedded-opentype'),url(../fonts/glyphicons-halflings-regular.woff2) format('woff2'),url(../fonts/glyphicons-halflings-regular.woff) format('woff'),url(../fonts/glyphicons-halflings-regular.ttf) format('truetype'),url(../fonts/glyphicons-halflings-regular.svg#glyphicons_halflingsregular) format('svg')}.glyphicon{position:relative;top:1px;display:inline-block;font-family:'Glyphicons Halflings';font-weight:400;line-height:1;-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale}.glyphicon-asterisk:before{content:"\002a"}.glyphicon-plus:before{content:"\002b"}.glyphicon-eur:before,.glyphicon-euro:before{content:"\20ac"}.glyphicon-minus:before{content:"\2212"}.glyphicon-cloud:before{content:"\2601"}.glyphicon-envelope:before{content:"\2709"}.glyphicon-pencil:before{content:"\270f"}.glyphicon-glass:before{content:"\e001"}.glyphicon-music:before{content:"\e002"}.glyphicon-search:before{content:"\e003"}.glyphicon-heart:before{content:"\e005"}.glyphicon-star:before{content:"\e006"}.glyphicon-star-empty:before{content:"\e007"}.glyphicon-user:before{content:"\e008"}.glyphicon-film:before{content:"\e009"}.glyphicon-th-large:before{content:"\e010"}.glyphicon-th:before{content:"\e011"}.glyphicon-th-list:before{content:"\e012"}.glyphicon-ok:before{content:"\e013"}.glyphicon-remove:before{content:"\e014"}.glyphicon-zoom-in:before{content:"\e015"}.glyphicon-zoom-out:before{content:"\e016"}.glyphicon-off:before{content:"\e017"}.glyphicon-signal:before{content:"\e018"}.glyphicon-cog:before{content:"\e019"}.glyphicon-trash:before{content:"\e020"}.glyphicon-home:before{content:"\e021"}.glyphicon-file:before{content:"\e022"}.glyphicon-time:before{content:"\e023"}.glyphicon-road:before{content:"\e024"}.glyphicon-download-alt:before{content:"\e025"}.glyphicon-download:before{content:"\e026"}.glyphicon-upload:before{content:"\e027"}.glyphicon-inbox:before{content:"\e028"}.glyphicon-play-circle:before{content:"\e029"}.glyphicon-repeat:before{content:"\e030"}.glyphicon-refresh:before{content:"\e031"}.glyphicon-list-alt:before{content:"\e032"}.glyphicon-lock:before{content:"\e033"}.glyphicon-flag:before{content:"\e034"}.glyphicon-headphones:before{content:"\e035"}.glyphicon-volume-off:before{content:"\e036"}.glyphicon-volume-down:before{content:"\e037"}.glyphicon-volume-up:before{content:"\e038"}.glyphicon-qrcode:before{content:"\e039"}.glyphicon-barcode:before{content:"\e040"}.glyphicon-tag:before{content:"\e041"}.glyphicon-tags:before{content:"\e042"}.glyphicon-book:before{content:"\e043"}.glyphicon-bookmark:before{content:"\e044"}.glyphicon-print:before{content:"\e045"}.glyphicon-camera:before{content:"\e046"}.glyphicon-font:before{content:"\e047"}.glyphicon-bold:before{content:"\e048"}.glyphicon-italic:before{content:"\e049"}.glyphicon-text-height:before{content:"\e050"}.glyphicon-text-width:before{content:"\e051"}.glyphicon-align-left:before{content:"\e052"}.glyphicon-align-center:before{content:"\e053"}.glyphicon-align-right:before{content:"\e054"}.glyphicon-align-justify:before{content:"\e055"}.glyphicon-list:before{content:"\e056"}.glyphicon-indent-left:before{content:"\e057"}.glyphicon-indent-right:before{content:"\e058"}.glyphicon-facetime-video:before{content:"\e059"}.glyphicon-picture:before{content:"\e060"}.glyphicon-map-marker:before{content:"\e062"}.glyphicon-adjust:before{content:"\e063"}.glyphicon-tint:before{content:"\e064"}.glyphicon-edit:before{content:"\e065"}.glyphicon-share:before{content:"\e066"}.glyphicon-check:before{content:"\e067"}.glyphicon-move:before{content:"\e068"}.glyphicon-step-backward:before{content:"\e069"}.glyphicon-fast-backward:before{content:"\e070"}.glyphicon-backward:before{content:"\e071"}.glyphicon-play:before{content:"\e072"}.glyphicon-pause:before{content:"\e073"}.glyphicon-stop:before{content:"\e074"}.glyphicon-forward:before{content:"\e075"}.glyphicon-fast-forward:before{content:"\e076"}.glyphicon-step-forward:before{content:"\e077"}.glyphicon-eject:before{content:"\e078"}.glyphicon-chevron-left:before{content:"\e079"}.glyphicon-chevron-right:before{content:"\e080"}.glyphicon-plus-sign:before{content:"\e081"}.glyphicon-minus-sign:before{content:"\e082"}.glyphicon-remove-sign:before{content:"\e083"}.glyphicon-ok-sign:before{content:"\e084"}.glyphicon-question-sign:before{content:"\e085"}.glyphicon-info-sign:before{content:"\e086"}.glyphicon-screenshot:before{content:"\e087"}.glyphicon-remove-circle:before{content:"\e088"}.glyphicon-ok-circle:before{content:"\e089"}.glyphicon-ban-circle:before{content:"\e090"}.glyphicon-arrow-left:before{content:"\e091"}.glyphicon-arrow-right:before{content:"\e092"}.glyphicon-arrow-up:before{content:"\e093"}.glyphicon-arrow-down:before{content:"\e094"}.glyphicon-share-alt:before{content:"\e095"}.glyphicon-resize-full:before{content:"\e096"}.glyphicon-resize-small:before{content:"\e097"}.glyphicon-exclamation-sign:before{content:"\e101"}.glyphicon-gift:before{content:"\e102"}.glyphicon-leaf:before{content:"\e103"}.glyphicon-fire:before{content:"\e104"}.glyphicon-eye-open:before{content:"\e105"}.glyphicon-eye-close:before{content:"\e106"}.glyphicon-warning-sign:before{content:"\e107"}.glyphicon-plane:before{content:"\e108"}.glyphicon-calendar:before{content:"\e109"}.glyphicon-random:before{content:"\e110"}.glyphicon-comment:before{content:"\e111"}.glyphicon-magnet:before{content:"\e112"}.glyphicon-chevron-up:before{content:"\e113"}.glyphicon-chevron-down:before{content:"\e114"}.glyphicon-retweet:before{content:"\e115"}.glyphicon-shopping-cart:before{content:"\e116"}.glyphicon-folder-close:before{content:"\e117"}.glyphicon-folder-open:before{content:"\e118"}.glyphicon-resize-vertical:before{content:"\e119"}.glyphicon-resize-horizontal:before{content:"\e120"}.glyphicon-hdd:before{content:"\e121"}.glyphicon-bullhorn:before{content:"\e122"}.glyphicon-bell:before{content:"\e123"}.glyphicon-certificate:before{content:"\e124"}.glyphicon-thumbs-up:before{content:"\e125"}.glyphicon-thumbs-down:before{content:"\e126"}.glyphicon-hand-right:before{content:"\e127"}.glyphicon-hand-left:before{content:"\e128"}.glyphicon-hand-up:before{content:"\e129"}.glyphicon-hand-down:before{content:"\e130"}.glyphicon-circle-arrow-right:before{content:"\e131"}.glyphicon-circle-arrow-left:before{content:"\e132"}.glyphicon-circle-arrow-up:before{content:"\e133"}.glyphicon-circle-arrow-down:before{content:"\e134"}.glyphicon-globe:before{content:"\e135"}.glyphicon-wrench:before{content:"\e136"}.glyphicon-tasks:before{content:"\e137"}.glyphicon-filter:before{content:"\e138"}.glyphicon-briefcase:before{content:"\e139"}.glyphicon-fullscreen:before{content:"\e140"}.glyphicon-dashboard:before{content:"\e141"}.glyphicon-paperclip:before{content:"\e142"}.glyphicon-heart-empty:before{content:"\e143"}.glyphicon-link:before{content:"\e144"}.glyphicon-phone:before{content:"\e145"}.glyphicon-pushpin:before{content:"\e146"}.glyphicon-usd:before{content:"\e148"}.glyphicon-gbp:before{content:"\e149"}.glyphicon-sort:before{content:"\e150"}.glyphicon-sort-by-alphabet:before{content:"\e151"}.glyphicon-sort-by-alphabet-alt:before{content:"\e152"}.glyphicon-sort-by-order:before{content:"\e153"}.glyphicon-sort-by-order-alt:before{content:"\e154"}.glyphicon-sort-by-attributes:before{content:"\e155"}.glyphicon-sort-by-attributes-alt:before{content:"\e156"}.glyphicon-unchecked:before{content:"\e157"}.glyphicon-expand:before{content:"\e158"}.glyphicon-collapse-down:before{content:"\e159"}.glyphicon-collapse-up:before{content:"\e160"}.glyphicon-log-in:before{content:"\e161"}.glyphicon-flash:before{content:"\e162"}.glyphicon-log-out:before{content:"\e163"}.glyphicon-new-window:before{content:"\e164"}.glyphicon-record:before{content:"\e165"}.glyphicon-save:before{content:"\e166"}.glyphicon-open:before{content:"\e167"}.glyphicon-saved:before{content:"\e168"}.glyphicon-import:before{content:"\e169"}.glyphicon-export:before{content:"\e170"}.glyphicon-send:before{content:"\e171"}.glyphicon-floppy-disk:before{content:"\e172"}.glyphicon-floppy-saved:before{content:"\e173"}.glyphicon-floppy-remove:before{content:"\e174"}.glyphicon-floppy-save:before{content:"\e175"}.glyphicon-floppy-open:before{content:"\e176"}.glyphicon-credit-card:before{content:"\e177"}.glyphicon-transfer:before{content:"\e178"}.glyphicon-cutlery:before{content:"\e179"}.glyphicon-header:before{content:"\e180"}.glyphicon-compressed:before{content:"\e181"}.glyphicon-earphone:before{content:"\e182"}.glyphicon-phone-alt:before{content:"\e183"}.glyphicon-tower:before{content:"\e184"}.glyphicon-stats:before{content:"\e185"}.glyphicon-sd-video:before{content:"\e186"}.glyphicon-hd-video:before{content:"\e187"}.glyphicon-subtitles:before{content:"\e188"}.glyphicon-sound-stereo:before{content:"\e189"}.glyphicon-sound-dolby:before{content:"\e190"}.glyphicon-sound-5-1:before{content:"\e191"}.glyphicon-sound-6-1:before{content:"\e192"}.glyphicon-sound-7-1:before{content:"\e193"}.glyphicon-copyright-mark:before{content:"\e194"}.glyphicon-registration-mark:before{content:"\e195"}.glyphicon-cloud-download:before{content:"\e197"}.glyphicon-cloud-upload:before{content:"\e198"}.glyphicon-tree-conifer:before{content:"\e199"}.glyphicon-tree-deciduous:before{content:"\e200"}.glyphicon-cd:before{content:"\e201"}.glyphicon-save-file:before{content:"\e202"}.glyphicon-open-file:before{content:"\e203"}.glyphicon-level-up:before{content:"\e204"}.glyphicon-copy:before{content:"\e205"}.glyphicon-paste:before{content:"\e206"}.glyphicon-alert:before{content:"\e209"}.glyphicon-equalizer:before{content:"\e210"}.glyphicon-king:before{content:"\e211"}.glyphicon-queen:before{content:"\e212"}.glyphicon-pawn:before{content:"\e213"}.glyphicon-bishop:before{content:"\e214"}.glyphicon-knight:before{content:"\e215"}.glyphicon-baby-formula:before{content:"\e216"}.glyphicon-tent:before{content:"\26fa"}.glyphicon-blackboard:before{content:"\e218"}.glyphicon-bed:before{content:"\e219"}.glyphicon-apple:before{content:"\f8ff"}.glyphicon-erase:before{content:"\e221"}.glyphicon-hourglass:before{content:"\231b"}.glyphicon-lamp:before{content:"\e223"}.glyphicon-duplicate:before{content:"\e224"}.glyphicon-piggy-bank:before{content:"\e225"}.glyphicon-scissors:before{content:"\e226"}.glyphicon-bitcoin:before,.glyphicon-btc:before,.glyphicon-xbt:before{content:"\e227"}.glyphicon-jpy:before,.glyphicon-yen:before{content:"\00a5"}.glyphicon-rub:before,.glyphicon-ruble:before{content:"\20bd"}.glyphicon-scale:before{content:"\e230"}.glyphicon-ice-lolly:before{content:"\e231"}.glyphicon-ice-lolly-tasted:before{content:"\e232"}.glyphicon-education:before{content:"\e233"}.glyphicon-option-horizontal:before{content:"\e234"}.glyphicon-option-vertical:before{content:"\e235"}.glyphicon-menu-hamburger:before{content:"\e236"}.glyphicon-modal-window:before{content:"\e237"}.glyphicon-oil:before{content:"\e238"}.glyphicon-grain:before{content:"\e239"}.glyphicon-sunglasses:before{content:"\e240"}.glyphicon-text-size:before{content:"\e241"}.glyphicon-text-color:before{content:"\e242"}.glyphicon-text-background:before{content:"\e243"}.glyphicon-object-align-top:before{content:"\e244"}.glyphicon-object-align-bottom:before{content:"\e245"}.glyphicon-object-align-horizontal:before{content:"\e246"}.glyphicon-object-align-left:before{content:"\e247"}.glyphicon-object-align-vertical:before{content:"\e248"}.glyphicon-object-align-right:before{content:"\e249"}.glyphicon-triangle-right:before{content:"\e250"}.glyphicon-triangle-left:before{content:"\e251"}.glyphicon-triangle-bottom:before{content:"\e252"}.glyphicon-triangle-top:before{content:"\e253"}.glyphicon-console:before{content:"\e254"}.glyphicon-superscript:before{content:"\e255"}.glyphicon-subscript:before{content:"\e256"}.glyphicon-menu-left:before{content:"\e257"}.glyphicon-menu-right:before{content:"\e258"}.glyphicon-menu-down:before{content:"\e259"}.glyphicon-menu-up:before{content:"\e260"}*,:after,:before{-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box}html{font-size:10px;-webkit-tap-highlight-color:transparent}body{font-family:"Helvetica Neue",Helvetica,Arial,sans-serif;font-size:14px;line-height:1.42857143;color:#333}button,input,select,textarea{font-family:inherit;font-size:inherit;line-height:inherit}a{color:#337ab7;text-decoration:none}a:focus,a:hover{color:#23527c;text-decoration:underline}a:focus{outline:-webkit-focus-ring-color auto 5px;outline-offset:-2px}.carousel-inner>.item>a>img,.carousel-inner>.item>img,.img-responsive,.thumbnail a>img,.thumbnail>img{display:block;max-width:100%;height:auto}.img-rounded{border-radius:6px}.img-thumbnail{display:inline-block;max-width:100%;height:auto;padding:4px;line-height:1.42857143;border:1px solid #ddd;border-radius:4px;-webkit-transition:all .2s ease-in-out;-o-transition:all .2s ease-in-out;transition:all .2s ease-in-out}.img-circle{border-radius:50%}hr{margin-top:20px;margin-bottom:20px;border-top:1px solid #eee}.sr-only{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);border:0}.sr-only-focusable:active,.sr-only-focusable:focus{position:static;width:auto;height:auto;margin:0;overflow:visible;clip:auto}[role=button]{cursor:pointer}.h1,.h2,.h3,.h4,.h5,.h6,h1,h2,h3,h4,h5,h6{font-family:inherit;font-weight:500;line-height:1.1;color:inherit}.h1 .small,.h1 small,.h2 .small,.h2 small,.h3 .small,.h3 small,.h4 .small,.h4 small,.h5 .small,.h5 small,.h6 .small,.h6 small,h1 .small,h1 small,h2 .small,h2 small,h3 .small,h3 small,h4 .small,h4 small,h5 .small,h5 small,h6 .small,h6 small{font-weight:400;line-height:1;color:#777}.h1,.h2,.h3,h1,h2,h3{margin-top:20px;margin-bottom:10px}.h1 .small,.h1 small,.h2 .small,.h2 small,.h3 .small,.h3 small,h1 .small,h1 small,h2 .small,h2 small,h3 .small,h3 small{font-size:65%}.h4,.h5,.h6,h4,h5,h6{margin-top:10px;margin-bottom:10px}.h4 .small,.h4 small,.h5 .small,.h5 small,.h6 .small,.h6 small,h4 .small,h4 small,h5 .small,h5 small,h6 .small,h6 small{font-size:75%}.h1,h1{font-size:36px}.h2,h2{font-size:30px}.h3,h3{font-size:24px}.h4,h4{font-size:18px}.h5,h5{font-size:14px}.h6,h6{font-size:12px}p{margin:0 0 10px}.lead{margin-bottom:20px;font-size:16px;font-weight:300;line-height:1.4}dt,kbd kbd,label{font-weight:700}address,blockquote .small,blockquote footer,blockquote small,dd,dt,pre{line-height:1.42857143}@media (min-width:111px){.lead{font-size:21px}}.small,small{font-size:85%}.mark,mark{padding:.2em;background-color:#fcf8e3}.list-inline,.list-unstyled{padding-left:0;list-style:none}.text-left{text-align:left}.text-right{text-align:right}.text-center{text-align:center}.text-justify{text-align:justify}.text-nowrap{white-space:nowrap}.text-lowercase{text-transform:lowercase}.text-uppercase{text-transform:uppercase}.text-capitalize{text-transform:capitalize}.text-muted{color:#777}.text-primary{color:#337ab7}a.text-primary:focus,a.text-primary:hover{color:#286090}.text-success{color:#3c763d}a.text-success:focus,a.text-success:hover{color:#2b542c}.text-info{color:#31708f}a.text-info:focus,a.text-info:hover{color:#245269}.text-warning{color:#8a6d3b}a.text-warning:focus,a.text-warning:hover{color:#66512c}.text-danger{color:#a94442}a.text-danger:focus,a.text-danger:hover{color:#843534}.bg-primary{color:#fff;background-color:#337ab7}a.bg-primary:focus,a.bg-primary:hover{background-color:#286090}.bg-success{background-color:#dff0d8}a.bg-success:focus,a.bg-success:hover{background-color:#c1e2b3}.bg-info{background-color:#d9edf7}a.bg-info:focus,a.bg-info:hover{background-color:#afd9ee}.bg-warning{background-color:#fcf8e3}a.bg-warning:focus,a.bg-warning:hover{background-color:#f7ecb5}.bg-danger{background-color:#f2dede}a.bg-danger:focus,a.bg-danger:hover{background-color:#e4b9b9}pre code,table{background-color:transparent}.page-header{padding-bottom:9px;margin:40px 0 20px;border-bottom:1px solid #eee}dl,ol,ul{margin-top:0}blockquote ol:last-child,blockquote p:last-child,blockquote ul:last-child,ol ol,ol ul,ul ol,ul ul{margin-bottom:0}address,dl{margin-bottom:20px}ol,ul{margin-bottom:10px}.list-inline{margin-left:-5px}.list-inline>li{display:inline-block;padding-right:5px;padding-left:5px}dd{margin-left:0}@media (min-width:111px){.dl-horizontal dt{float:left;width:160px;overflow:hidden;clear:left;text-align:right;text-overflow:ellipsis;white-space:nowrap}.dl-horizontal dd{margin-left:180px}.container{width:750px}}abbr[data-original-title],abbr[title]{cursor:help;border-bottom:1px dotted #777}.initialism{font-size:90%;text-transform:uppercase}blockquote{padding:10px 20px;margin:0 0 20px;font-size:17.5px;border-left:5px solid #eee}blockquote .small,blockquote footer,blockquote small{display:block;font-size:80%;color:#777}legend,pre{display:block;color:#333}blockquote .small:before,blockquote footer:before,blockquote small:before{content:'\2014 \00A0'}.blockquote-reverse,blockquote.pull-right{padding-right:15px;padding-left:0;text-align:right;border-right:5px solid #eee;border-left:0}code,kbd{padding:2px 4px;font-size:90%}caption,th{text-align:left}.blockquote-reverse .small:before,.blockquote-reverse footer:before,.blockquote-reverse small:before,blockquote.pull-right .small:before,blockquote.pull-right footer:before,blockquote.pull-right small:before{content:''}.blockquote-reverse .small:after,.blockquote-reverse footer:after,.blockquote-reverse small:after,blockquote.pull-right .small:after,blockquote.pull-right footer:after,blockquote.pull-right small:after{content:'\00A0 \2014'}code,kbd,pre,samp{font-family:Menlo,Monaco,Consolas,"Courier New",monospace}code{color:#c7254e;background-color:#f9f2f4;border-radius:4px}kbd{color:#fff;background-color:#333;border-radius:3px;-webkit-box-shadow:inset 0 -1px 0 rgba(0,0,0,.25);box-shadow:inset 0 -1px 0 rgba(0,0,0,.25)}kbd kbd{padding:0;font-size:100%;-webkit-box-shadow:none;box-shadow:none}pre{padding:9.5px;margin:0 0 10px;font-size:13px;word-break:break-all;word-wrap:break-word;background-color:#f5f5f5;border:1px solid #ccc;border-radius:4px}.container,.container-fluid{margin-right:auto;margin-left:auto}pre code{padding:0;font-size:inherit;color:inherit;white-space:pre-wrap;border-radius:0}.container,.container-fluid{padding-right:15px;padding-left:15px}.pre-scrollable{overflow-y:scroll}@media (min-width:992px){.container{width:970px}}@media (min-width:1200px){.container{width:1170px}}.row{margin-right:-15px;margin-left:-15px}.col-lg-1,.col-lg-10,.col-lg-11,.col-lg-12,.col-lg-2,.col-lg-3,.col-lg-4,.col-lg-5,.col-lg-6,.col-lg-7,.col-lg-8,.col-lg-9,.col-md-1,.col-md-10,.col-md-11,.col-md-12,.col-md-2,.col-md-3,.col-md-4,.col-md-5,.col-md-6,.col-md-7,.col-md-8,.col-md-9,.col-sm-1,.col-sm-10,.col-sm-11,.col-sm-12,.col-sm-2,.col-sm-3,.col-sm-4,.col-sm-5,.col-sm-6,.col-sm-7,.col-sm-8,.col-sm-9,.col-xs-1,.col-xs-10,.col-xs-11,.col-xs-12,.col-xs-2,.col-xs-3,.col-xs-4,.col-xs-5,.col-xs-6,.col-xs-7,.col-xs-8,.col-xs-9{position:relative;min-height:1px;padding-right:15px;padding-left:15px}.col-xs-12{width:100%}.col-xs-11{width:91.66666667%}.col-xs-10{width:83.33333333%}.col-xs-9{width:75%}.col-xs-8{width:66.66666667%}.col-xs-7{width:58.33333333%}.col-xs-6{width:50%}.col-xs-5{width:41.66666667%}.col-xs-4{width:33.33333333%}.col-xs-3{width:25%}.col-xs-2{width:16.66666667%}.col-xs-1{width:8.33333333%}.col-xs-pull-12{right:100%}.col-xs-pull-11{right:91.66666667%}.col-xs-pull-10{right:83.33333333%}.col-xs-pull-9{right:75%}.col-xs-pull-8{right:66.66666667%}.col-xs-pull-7{right:58.33333333%}.col-xs-pull-6{right:50%}.col-xs-pull-5{right:41.66666667%}.col-xs-pull-4{right:33.33333333%}.col-xs-pull-3{right:25%}.col-xs-pull-2{right:16.66666667%}.col-xs-pull-1{right:8.33333333%}.col-xs-pull-0{right:auto}.col-xs-push-12{left:100%}.col-xs-push-11{left:91.66666667%}.col-xs-push-10{left:83.33333333%}.col-xs-push-9{left:75%}.col-xs-push-8{left:66.66666667%}.col-xs-push-7{left:58.33333333%}.col-xs-push-6{left:50%}.col-xs-push-5{left:41.66666667%}.col-xs-push-4{left:33.33333333%}.col-xs-push-3{left:25%}.col-xs-push-2{left:16.66666667%}.col-xs-push-1{left:8.33333333%}.col-xs-push-0{left:auto}.col-xs-offset-12{margin-left:100%}.col-xs-offset-11{margin-left:91.66666667%}.col-xs-offset-10{margin-left:83.33333333%}.col-xs-offset-9{margin-left:75%}.col-xs-offset-8{margin-left:66.66666667%}.col-xs-offset-7{margin-left:58.33333333%}.col-xs-offset-6{margin-left:50%}.col-xs-offset-5{margin-left:41.66666667%}.col-xs-offset-4{margin-left:33.33333333%}.col-xs-offset-3{margin-left:25%}.col-xs-offset-2{margin-left:16.66666667%}.col-xs-offset-1{margin-left:8.33333333%}.col-xs-offset-0{margin-left:0}@media (min-width:111px){.col-sm-1,.col-sm-10,.col-sm-11,.col-sm-12,.col-sm-2,.col-sm-3,.col-sm-4,.col-sm-5,.col-sm-6,.col-sm-7,.col-sm-8,.col-sm-9{float:left}.col-sm-12{width:100%}.col-sm-11{width:91.66666667%}.col-sm-10{width:83.33333333%}.col-sm-9{width:75%}.col-sm-8{width:66.66666667%}.col-sm-7{width:58.33333333%}.col-sm-6{width:50%}.col-sm-5{width:41.66666667%}.col-sm-4{width:33.33333333%}.col-sm-3{width:25%}.col-sm-2{width:16.66666667%}.col-sm-1{width:8.33333333%}.col-sm-pull-12{right:100%}.col-sm-pull-11{right:91.66666667%}.col-sm-pull-10{right:83.33333333%}.col-sm-pull-9{right:75%}.col-sm-pull-8{right:66.66666667%}.col-sm-pull-7{right:58.33333333%}.col-sm-pull-6{right:50%}.col-sm-pull-5{right:41.66666667%}.col-sm-pull-4{right:33.33333333%}.col-sm-pull-3{right:25%}.col-sm-pull-2{right:16.66666667%}.col-sm-pull-1{right:8.33333333%}.col-sm-pull-0{right:auto}.col-sm-push-12{left:100%}.col-sm-push-11{left:91.66666667%}.col-sm-push-10{left:83.33333333%}.col-sm-push-9{left:75%}.col-sm-push-8{left:66.66666667%}.col-sm-push-7{left:58.33333333%}.col-sm-push-6{left:50%}.col-sm-push-5{left:41.66666667%}.col-sm-push-4{left:33.33333333%}.col-sm-push-3{left:25%}.col-sm-push-2{left:16.66666667%}.col-sm-push-1{left:8.33333333%}.col-sm-push-0{left:auto}.col-sm-offset-12{margin-left:100%}.col-sm-offset-11{margin-left:91.66666667%}.col-sm-offset-10{margin-left:83.33333333%}.col-sm-offset-9{margin-left:75%}.col-sm-offset-8{margin-left:66.66666667%}.col-sm-offset-7{margin-left:58.33333333%}.col-sm-offset-6{margin-left:50%}.col-sm-offset-5{margin-left:41.66666667%}.col-sm-offset-4{margin-left:33.33333333%}.col-sm-offset-3{margin-left:25%}.col-sm-offset-2{margin-left:16.66666667%}.col-sm-offset-1{margin-left:8.33333333%}.col-sm-offset-0{margin-left:0}}@media (min-width:992px){.col-md-1,.col-md-10,.col-md-11,.col-md-12,.col-md-2,.col-md-3,.col-md-4,.col-md-5,.col-md-6,.col-md-7,.col-md-8,.col-md-9{float:left}.col-md-12{width:100%}.col-md-11{width:91.66666667%}.col-md-10{width:83.33333333%}.col-md-9{width:75%}.col-md-8{width:66.66666667%}.col-md-7{width:58.33333333%}.col-md-6{width:50%}.col-md-5{width:41.66666667%}.col-md-4{width:33.33333333%}.col-md-3{width:25%}.col-md-2{width:16.66666667%}.col-md-1{width:8.33333333%}.col-md-pull-12{right:100%}.col-md-pull-11{right:91.66666667%}.col-md-pull-10{right:83.33333333%}.col-md-pull-9{right:75%}.col-md-pull-8{right:66.66666667%}.col-md-pull-7{right:58.33333333%}.col-md-pull-6{right:50%}.col-md-pull-5{right:41.66666667%}.col-md-pull-4{right:33.33333333%}.col-md-pull-3{right:25%}.col-md-pull-2{right:16.66666667%}.col-md-pull-1{right:8.33333333%}.col-md-pull-0{right:auto}.col-md-push-12{left:100%}.col-md-push-11{left:91.66666667%}.col-md-push-10{left:83.33333333%}.col-md-push-9{left:75%}.col-md-push-8{left:66.66666667%}.col-md-push-7{left:58.33333333%}.col-md-push-6{left:50%}.col-md-push-5{left:41.66666667%}.col-md-push-4{left:33.33333333%}.col-md-push-3{left:25%}.col-md-push-2{left:16.66666667%}.col-md-push-1{left:8.33333333%}.col-md-push-0{left:auto}.col-md-offset-12{margin-left:100%}.col-md-offset-11{margin-left:91.66666667%}.col-md-offset-10{margin-left:83.33333333%}.col-md-offset-9{margin-left:75%}.col-md-offset-8{margin-left:66.66666667%}.col-md-offset-7{margin-left:58.33333333%}.col-md-offset-6{margin-left:50%}.col-md-offset-5{margin-left:41.66666667%}.col-md-offset-4{margin-left:33.33333333%}.col-md-offset-3{margin-left:25%}.col-md-offset-2{margin-left:16.66666667%}.col-md-offset-1{margin-left:8.33333333%}.col-md-offset-0{margin-left:0}}@media (min-width:1200px){.col-lg-1,.col-lg-10,.col-lg-11,.col-lg-12,.col-lg-2,.col-lg-3,.col-lg-4,.col-lg-5,.col-lg-6,.col-lg-7,.col-lg-8,.col-lg-9{float:left}.col-lg-12{width:100%}.col-lg-11{width:91.66666667%}.col-lg-10{width:83.33333333%}.col-lg-9{width:75%}.col-lg-8{width:66.66666667%}.col-lg-7{width:58.33333333%}.col-lg-6{width:50%}.col-lg-5{width:41.66666667%}.col-lg-4{width:33.33333333%}.col-lg-3{width:25%}.col-lg-2{width:16.66666667%}.col-lg-1{width:8.33333333%}.col-lg-pull-12{right:100%}.col-lg-pull-11{right:91.66666667%}.col-lg-pull-10{right:83.33333333%}.col-lg-pull-9{right:75%}.col-lg-pull-8{right:66.66666667%}.col-lg-pull-7{right:58.33333333%}.col-lg-pull-6{right:50%}.col-lg-pull-5{right:41.66666667%}.col-lg-pull-4{right:33.33333333%}.col-lg-pull-3{right:25%}.col-lg-pull-2{right:16.66666667%}.col-lg-pull-1{right:8.33333333%}.col-lg-pull-0{right:auto}.col-lg-push-12{left:100%}.col-lg-push-11{left:91.66666667%}.col-lg-push-10{left:83.33333333%}.col-lg-push-9{left:75%}.col-lg-push-8{left:66.66666667%}.col-lg-push-7{left:58.33333333%}.col-lg-push-6{left:50%}.col-lg-push-5{left:41.66666667%}.col-lg-push-4{left:33.33333333%}.col-lg-push-3{left:25%}.col-lg-push-2{left:16.66666667%}.col-lg-push-1{left:8.33333333%}.col-lg-push-0{left:auto}.col-lg-offset-12{margin-left:100%}.col-lg-offset-11{margin-left:91.66666667%}.col-lg-offset-10{margin-left:83.33333333%}.col-lg-offset-9{margin-left:75%}.col-lg-offset-8{margin-left:66.66666667%}.col-lg-offset-7{margin-left:58.33333333%}.col-lg-offset-6{margin-left:50%}.col-lg-offset-5{margin-left:41.66666667%}.col-lg-offset-4{margin-left:33.33333333%}.col-lg-offset-3{margin-left:25%}.col-lg-offset-2{margin-left:16.66666667%}.col-lg-offset-1{margin-left:8.33333333%}.col-lg-offset-0{margin-left:0}}caption{padding-top:8px;padding-bottom:8px;color:#777}.table{width:500;max-width:100%;margin-bottom:20px}.table>tbody>tr>td,.table>tbody>tr>th,.table>tfoot>tr>td,.table>tfoot>tr>th,.table>thead>tr>td,.table>thead>tr>th{padding:8px;line-height:1.42857143;vertical-align:top;border-top:0 solid #ddd}.table>thead>tr>th{vertical-align:bottom;border-bottom:2px solid #ddd}.table>caption+thead>tr:first-child>td,.table>caption+thead>tr:first-child>th,.table>colgroup+thead>tr:first-child>td,.table>colgroup+thead>tr:first-child>th,.table>thead:first-child>tr:first-child>td,.table>thead:first-child>tr:first-child>th{border-top:0}.table>tbody+tbody{border-top:2px solid #ddd}.table .table{background-color:#fff}.table-condensed>tbody>tr>td,.table-condensed>tbody>tr>th,.table-condensed>tfoot>tr>td,.table-condensed>tfoot>tr>th,.table-condensed>thead>tr>td,.table-condensed>thead>tr>th{padding:5px}.table-bordered,.table-bordered>tbody>tr>td,.table-bordered>tbody>tr>th,.table-bordered>tfoot>tr>td,.table-bordered>tfoot>tr>th,.table-bordered>thead>tr>td,.table-bordered>thead>tr>th{border:1px solid #ddd}.table-bordered>thead>tr>td,.table-bordered>thead>tr>th{border-bottom-width:2px}.table-striped>tbody>tr:nth-of-type(odd){background-color:#f9f9f9}.table-hover>tbody>tr:hover,.table>tbody>tr.active>td,.table>tbody>tr.active>th,.table>tbody>tr>td.active,.table>tbody>tr>th.active,.table>tfoot>tr.active>td,.table>tfoot>tr.active>th,.table>tfoot>tr>td.active,.table>tfoot>tr>th.active,.table>thead>tr.active>td,.table>thead>tr.active>th,.table>thead>tr>td.active,.table>thead>tr>th.active{background-color:#f5f5f5}table col[class*=col-]{position:static;display:table-column;float:none}table td[class*=col-],table th[class*=col-]{position:static;display:table-cell;float:none}.table-hover>tbody>tr.active:hover>td,.table-hover>tbody>tr.active:hover>th,.table-hover>tbody>tr:hover>.active,.table-hover>tbody>tr>td.active:hover,.table-hover>tbody>tr>th.active:hover{background-color:#e8e8e8}.table>tbody>tr.success>td,.table>tbody>tr.success>th,.table>tbody>tr>td.success,.table>tbody>tr>th.success,.table>tfoot>tr.success>td,.table>tfoot>tr.success>th,.table>tfoot>tr>td.success,.table>tfoot>tr>th.success,.table>thead>tr.success>td,.table>thead>tr.success>th,.table>thead>tr>td.success,.table>thead>tr>th.success{background-color:#dff0d8}.table-hover>tbody>tr.success:hover>td,.table-hover>tbody>tr.success:hover>th,.table-hover>tbody>tr:hover>.success,.table-hover>tbody>tr>td.success:hover,.table-hover>tbody>tr>th.success:hover{background-color:#d0e9c6}.table>tbody>tr.info>td,.table>tbody>tr.info>th,.table>tbody>tr>td.info,.table>tbody>tr>th.info,.table>tfoot>tr.info>td,.table>tfoot>tr.info>th,.table>tfoot>tr>td.info,.table>tfoot>tr>th.info,.table>thead>tr.info>td,.table>thead>tr.info>th,.table>thead>tr>td.info,.table>thead>tr>th.info{background-color:#d9edf7}.table-hover>tbody>tr.info:hover>td,.table-hover>tbody>tr.info:hover>th,.table-hover>tbody>tr:hover>.info,.table-hover>tbody>tr>td.info:hover,.table-hover>tbody>tr>th.info:hover{background-color:#c4e3f3}.table>tbody>tr.warning>td,.table>tbody>tr.warning>th,.table>tbody>tr>td.warning,.table>tbody>tr>th.warning,.table>tfoot>tr.warning>td,.table>tfoot>tr.warning>th,.table>tfoot>tr>td.warning,.table>tfoot>tr>th.warning,.table>thead>tr.warning>td,.table>thead>tr.warning>th,.table>thead>tr>td.warning,.table>thead>tr>th.warning{background-color:#fcf8e3}.table-hover>tbody>tr.warning:hover>td,.table-hover>tbody>tr.warning:hover>th,.table-hover>tbody>tr:hover>.warning,.table-hover>tbody>tr>td.warning:hover,.table-hover>tbody>tr>th.warning:hover{background-color:#faf2cc}.table>tbody>tr.danger>td,.table>tbody>tr.danger>th,.table>tbody>tr>td.danger,.table>tbody>tr>th.danger,.table>tfoot>tr.danger>td,.table>tfoot>tr.danger>th,.table>tfoot>tr>td.danger,.table>tfoot>tr>th.danger,.table>thead>tr.danger>td,.table>thead>tr.danger>th,.table>thead>tr>td.danger,.table>thead>tr>th.danger{background-color:#f2dede}.table-hover>tbody>tr.danger:hover>td,.table-hover>tbody>tr.danger:hover>th,.table-hover>tbody>tr:hover>.danger,.table-hover>tbody>tr>td.danger:hover,.table-hover>tbody>tr>th.danger:hover{background-color:#ebcccc}.table-responsive{min-height:.01%;overflow-x:auto}@media screen and (max-width:767px){.table-responsive{width:100%;margin-bottom:15px;overflow-y:hidden;-ms-overflow-style:-ms-autohiding-scrollbar;border:1px solid #ddd}.table-responsive>.table{margin-bottom:0}.table-responsive>.table>tbody>tr>td,.table-responsive>.table>tbody>tr>th,.table-responsive>.table>tfoot>tr>td,.table-responsive>.table>tfoot>tr>th,.table-responsive>.table>thead>tr>td,.table-responsive>.table>thead>tr>th{white-space:nowrap}.table-responsive>.table-bordered{border:0}.table-responsive>.table-bordered>tbody>tr>td:first-child,.table-responsive>.table-bordered>tbody>tr>th:first-child,.table-responsive>.table-bordered>tfoot>tr>td:first-child,.table-responsive>.table-bordered>tfoot>tr>th:first-child,.table-responsive>.table-bordered>thead>tr>td:first-child,.table-responsive>.table-bordered>thead>tr>th:first-child{border-left:0}.table-responsive>.table-bordered>tbody>tr>td:last-child,.table-responsive>.table-bordered>tbody>tr>th:last-child,.table-responsive>.table-bordered>tfoot>tr>td:last-child,.table-responsive>.table-bordered>tfoot>tr>th:last-child,.table-responsive>.table-bordered>thead>tr>td:last-child,.table-responsive>.table-bordered>thead>tr>th:last-child{border-right:0}.table-responsive>.table-bordered>tbody>tr:last-child>td,.table-responsive>.table-bordered>tbody>tr:last-child>th,.table-responsive>.table-bordered>tfoot>tr:last-child>td,.table-responsive>.table-bordered>tfoot>tr:last-child>th{border-bottom:0}}fieldset,legend{padding:0;border:0}fieldset{min-width:0;margin:0}legend{width:100%;margin-bottom:20px;font-size:21px;line-height:inherit;border-bottom:1px solid #e5e5e5}label{display:inline-block;max-width:100%;margin-bottom:5px}input[type=search]{-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box;-webkit-appearance:none}input[type=checkbox],input[type=radio]{margin:4px 0 0;margin-top:1px\9;line-height:normal}.form-control,output{font-size:14px;line-height:1.42857143;color:#555;display:block}input[type=file]{display:block}input[type=range]{display:block;width:100%}select[multiple],select[size]{height:auto}input[type=file]:focus,input[type=checkbox]:focus,input[type=radio]:focus{outline:-webkit-focus-ring-color auto 5px;outline-offset:-2px}output{padding-top:7px}.form-control{width:100%;height:34px;padding:6px 12px;background-color:#fff;border:1px solid #ccc;border-radius:4px;-webkit-box-shadow:inset 0 1px 1px rgba(0,0,0,.075);box-shadow:inset 0 1px 1px rgba(0,0,0,.075);-webkit-transition:border-color ease-in-out .15s,-webkit-box-shadow ease-in-out .15s;-o-transition:border-color ease-in-out .15s,box-shadow ease-in-out .15s;transition:border-color ease-in-out .15s,box-shadow ease-in-out .15s}.form-control:focus{border-color:#66afe9;outline:0;-webkit-box-shadow:inset 0 1px 1px rgba(0,0,0,.075),0 0 8px rgba(102,175,233,.6);box-shadow:inset 0 1px 1px rgba(0,0,0,.075),0 0 8px rgba(102,175,233,.6)}.form-control::-moz-placeholder{color:#999;opacity:1}.form-control:-ms-input-placeholder{color:#999}.form-control::-webkit-input-placeholder{color:#999}.has-success .checkbox,.has-success .checkbox-inline,.has-success .control-label,.has-success .form-control-feedback,.has-success .help-block,.has-success .radio,.has-success .radio-inline,.has-success.checkbox label,.has-success.checkbox-inline label,.has-success.radio label,.has-success.radio-inline label{color:#3c763d}.form-control::-ms-expand{background-color:transparent;border:0}.form-control[disabled],.form-control[readonly],fieldset[disabled] .form-control{background-color:#eee;opacity:1}.form-control[disabled],fieldset[disabled] .form-control{cursor:not-allowed}textarea.form-control{height:auto}@media screen and (-webkit-min-device-pixel-ratio:0){input[type=date].form-control,input[type=time].form-control,input[type=datetime-local].form-control,input[type=month].form-control{line-height:34px}.input-group-sm input[type=date],.input-group-sm input[type=time],.input-group-sm input[type=datetime-local],.input-group-sm input[type=month],input[type=date].input-sm,input[type=time].input-sm,input[type=datetime-local].input-sm,input[type=month].input-sm{line-height:30px}.input-group-lg input[type=date],.input-group-lg input[type=time],.input-group-lg input[type=datetime-local],.input-group-lg input[type=month],input[type=date].input-lg,input[type=time].input-lg,input[type=datetime-local].input-lg,input[type=month].input-lg{line-height:46px}}.form-group{margin-bottom:15px}.checkbox,.radio{position:relative;display:block;margin-top:10px;margin-bottom:10px}.checkbox label,.radio label{min-height:20px;padding-left:20px;margin-bottom:0;font-weight:400;cursor:pointer}.checkbox input[type=checkbox],.checkbox-inline input[type=checkbox],.radio input[type=radio],.radio-inline input[type=radio]{position:absolute;margin-top:4px\9;margin-left:-20px}.checkbox+.checkbox,.radio+.radio{margin-top:-5px}.checkbox-inline,.radio-inline{position:relative;display:inline-block;padding-left:20px;margin-bottom:0;font-weight:400;vertical-align:middle;cursor:pointer}.checkbox-inline+.checkbox-inline,.radio-inline+.radio-inline{margin-top:0;margin-left:10px}.checkbox-inline.disabled,.checkbox.disabled label,.radio-inline.disabled,.radio.disabled label,fieldset[disabled] .checkbox label,fieldset[disabled] .checkbox-inline,fieldset[disabled] .radio label,fieldset[disabled] .radio-inline,fieldset[disabled] input[type=checkbox],fieldset[disabled] input[type=radio],input[type=checkbox].disabled,input[type=checkbox][disabled],input[type=radio].disabled,input[type=radio][disabled]{cursor:not-allowed}.form-control-static{min-height:34px;padding-top:7px;padding-bottom:7px;margin-bottom:0}.form-control-static.input-lg,.form-control-static.input-sm{padding-right:0;padding-left:0}.form-group-sm .form-control,.input-sm{padding:5px 10px;border-radius:3px;font-size:12px}.input-sm{height:30px;line-height:1.5}select.input-sm{height:30px;line-height:30px}select[multiple].input-sm,textarea.input-sm{height:auto}.form-group-sm .form-control{height:30px;line-height:1.5}.form-group-lg .form-control,.input-lg{border-radius:6px;padding:10px 16px;font-size:18px}.form-group-sm select.form-control{height:30px;line-height:30px}.form-group-sm select[multiple].form-control,.form-group-sm textarea.form-control{height:auto}.form-group-sm .form-control-static{height:30px;min-height:32px;padding:6px 10px;font-size:12px;line-height:1.5}.input-lg{height:46px;line-height:1.3333333}select.input-lg{height:46px;line-height:46px}select[multiple].input-lg,textarea.input-lg{height:auto}.form-group-lg .form-control{height:46px;line-height:1.3333333}.form-group-lg select.form-control{height:46px;line-height:46px}.form-group-lg select[multiple].form-control,.form-group-lg textarea.form-control{height:auto}.form-group-lg .form-control-static{height:46px;min-height:38px;padding:11px 16px;font-size:18px;line-height:1.3333333}.has-feedback{position:relative}.has-feedback .form-control{padding-right:42.5px}.form-control-feedback{position:absolute;top:0;right:0;z-index:2;display:block;width:34px;height:34px;line-height:34px;text-align:center;pointer-events:none}.collapsing,.dropdown,.dropup{position:relative}.form-group-lg .form-control+.form-control-feedback,.input-group-lg+.form-control-feedback,.input-lg+.form-control-feedback{width:46px;height:46px;line-height:46px}.form-group-sm .form-control+.form-control-feedback,.input-group-sm+.form-control-feedback,.input-sm+.form-control-feedback{width:30px;height:30px;line-height:30px}.has-success .form-control{border-color:#3c763d;-webkit-box-shadow:inset 0 1px 1px rgba(0,0,0,.075);box-shadow:inset 0 1px 1px rgba(0,0,0,.075)}.has-success .form-control:focus{border-color:#2b542c;-webkit-box-shadow:inset 0 1px 1px rgba(0,0,0,.075),0 0 6px #67b168;box-shadow:inset 0 1px 1px rgba(0,0,0,.075),0 0 6px #67b168}.has-success .input-group-addon{color:#3c763d;background-color:#dff0d8;border-color:#3c763d}.has-warning .checkbox,.has-warning .checkbox-inline,.has-warning .control-label,.has-warning .form-control-feedback,.has-warning .help-block,.has-warning .radio,.has-warning .radio-inline,.has-warning.checkbox label,.has-warning.checkbox-inline label,.has-warning.radio label,.has-warning.radio-inline label{color:#8a6d3b}.has-warning .form-control{border-color:#8a6d3b;-webkit-box-shadow:inset 0 1px 1px rgba(0,0,0,.075);box-shadow:inset 0 1px 1px rgba(0,0,0,.075)}.has-warning .form-control:focus{border-color:#66512c;-webkit-box-shadow:inset 0 1px 1px rgba(0,0,0,.075),0 0 6px #c0a16b;box-shadow:inset 0 1px 1px rgba(0,0,0,.075),0 0 6px #c0a16b}.has-warning .input-group-addon{color:#8a6d3b;background-color:#fcf8e3;border-color:#8a6d3b}.has-error .checkbox,.has-error .checkbox-inline,.has-error .control-label,.has-error .form-control-feedback,.has-error .help-block,.has-error .radio,.has-error .radio-inline,.has-error.checkbox label,.has-error.checkbox-inline label,.has-error.radio label,.has-error.radio-inline label{color:#a94442}.has-error .form-control{border-color:#a94442;-webkit-box-shadow:inset 0 1px 1px rgba(0,0,0,.075);box-shadow:inset 0 1px 1px rgba(0,0,0,.075)}.has-error .form-control:focus{border-color:#843534;-webkit-box-shadow:inset 0 1px 1px rgba(0,0,0,.075),0 0 6px #ce8483;box-shadow:inset 0 1px 1px rgba(0,0,0,.075),0 0 6px #ce8483}.has-error .input-group-addon{color:#a94442;background-color:#f2dede;border-color:#a94442}.has-feedback label~.form-control-feedback{top:25px}.has-feedback label.sr-only~.form-control-feedback{top:0}.help-block{display:block;margin-top:5px;margin-bottom:10px;color:#737373}@media (min-width:111px){.form-inline .form-control-static,.form-inline .form-group{display:inline-block}.form-inline .control-label,.form-inline .form-group{margin-bottom:0;vertical-align:middle}.form-inline .form-control{display:inline-block;width:auto;vertical-align:middle}.form-inline .input-group{display:inline-table;vertical-align:middle}.form-inline .input-group .form-control,.form-inline .input-group .input-group-addon,.form-inline .input-group .input-group-btn{width:auto}.form-inline .input-group>.form-control{width:100%}.form-inline .checkbox,.form-inline .radio{display:inline-block;margin-top:0;margin-bottom:0;vertical-align:middle}.form-inline .checkbox label,.form-inline .radio label{padding-left:0}.form-inline .checkbox input[type=checkbox],.form-inline .radio input[type=radio]{position:relative;margin-left:0}.form-inline .has-feedback .form-control-feedback{top:0}.form-horizontal .control-label{padding-top:7px;margin-bottom:0;text-align:right}}.form-horizontal .checkbox,.form-horizontal .checkbox-inline,.form-horizontal .radio,.form-horizontal .radio-inline{padding-top:7px;margin-top:0;margin-bottom:0}.form-horizontal .checkbox,.form-horizontal .radio{min-height:27px}.form-horizontal .form-group{margin-right:-15px;margin-left:-15px}.form-horizontal .has-feedback .form-control-feedback{right:15px}@media (min-width:111px){.form-horizontal .form-group-lg .control-label{padding-top:11px;font-size:18px}.form-horizontal .form-group-sm .control-label{padding-top:6px;font-size:12px}}.btn{display:inline-block;padding:6px 12px;margin-bottom:0;font-size:14px;font-weight:400;line-height:1.42857143;text-align:center;white-space:nowrap;vertical-align:middle;-ms-touch-action:manipulation;touch-action:manipulation;cursor:pointer;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;border:1px solid transparent;border-radius:4px}.btn.active.focus,.btn.active:focus,.btn.focus,.btn:active.focus,.btn:active:focus,.btn:focus{outline:-webkit-focus-ring-color auto 5px;outline-offset:-2px}.btn.focus,.btn:focus,.btn:hover{color:#333;text-decoration:none}.btn.active,.btn:active{outline:0;-webkit-box-shadow:inset 0 3px 5px rgba(0,0,0,.125);box-shadow:inset 0 3px 5px rgba(0,0,0,.125)}.btn.disabled,.btn[disabled],fieldset[disabled] .btn{cursor:not-allowed;filter:alpha(opacity=65);-webkit-box-shadow:none;box-shadow:none;opacity:.65}a.btn.disabled,fieldset[disabled] a.btn{pointer-events:none}.btn-default{color:#333;background-color:#fff;border-color:#ccc}.btn-default.focus,.btn-default:focus{color:#333;background-color:#e6e6e6;border-color:#8c8c8c}.btn-default.active,.btn-default:active,.btn-default:hover,.open>.dropdown-toggle.btn-default{color:#333;background-color:#e6e6e6;border-color:#adadad}.btn-default.active.focus,.btn-default.active:focus,.btn-default.active:hover,.btn-default:active.focus,.btn-default:active:focus,.btn-default:active:hover,.open>.dropdown-toggle.btn-default.focus,.open>.dropdown-toggle.btn-default:focus,.open>.dropdown-toggle.btn-default:hover{color:#333;background-color:#d4d4d4;border-color:#8c8c8c}.btn-default.disabled.focus,.btn-default.disabled:focus,.btn-default.disabled:hover,.btn-default[disabled].focus,.btn-default[disabled]:focus,.btn-default[disabled]:hover,fieldset[disabled] .btn-default.focus,fieldset[disabled] .btn-default:focus,fieldset[disabled] .btn-default:hover{background-color:#fff;border-color:#ccc}.btn-default .badge{color:#fff;background-color:#333}.btn-primary{color:#fff;background-color:#337ab7;border-color:#2e6da4}.btn-primary.focus,.btn-primary:focus{color:#fff;background-color:#286090;border-color:#122b40}.btn-primary.active,.btn-primary:active,.btn-primary:hover,.open>.dropdown-toggle.btn-primary{color:#fff;background-color:#286090;border-color:#204d74}.btn-primary.active.focus,.btn-primary.active:focus,.btn-primary.active:hover,.btn-primary:active.focus,.btn-primary:active:focus,.btn-primary:active:hover,.open>.dropdown-toggle.btn-primary.focus,.open>.dropdown-toggle.btn-primary:focus,.open>.dropdown-toggle.btn-primary:hover{color:#fff;background-color:#204d74;border-color:#122b40}.btn-primary.disabled.focus,.btn-primary.disabled:focus,.btn-primary.disabled:hover,.btn-primary[disabled].focus,.btn-primary[disabled]:focus,.btn-primary[disabled]:hover,fieldset[disabled] .btn-primary.focus,fieldset[disabled] .btn-primary:focus,fieldset[disabled] .btn-primary:hover{background-color:#337ab7;border-color:#2e6da4}.btn-primary .badge{color:#337ab7;background-color:#fff}.btn-success{color:#fff;background-color:#5cb85c;border-color:#4cae4c}.btn-success.focus,.btn-success:focus{color:#fff;background-color:#449d44;border-color:#255625}.btn-success.active,.btn-success:active,.btn-success:hover,.open>.dropdown-toggle.btn-success{color:#fff;background-color:#449d44;border-color:#398439}.btn-success.active.focus,.btn-success.active:focus,.btn-success.active:hover,.btn-success:active.focus,.btn-success:active:focus,.btn-success:active:hover,.open>.dropdown-toggle.btn-success.focus,.open>.dropdown-toggle.btn-success:focus,.open>.dropdown-toggle.btn-success:hover{color:#fff;background-color:#398439;border-color:#255625}.btn-success.active,.btn-success:active,.open>.dropdown-toggle.btn-success{background-image:none}.btn-success.disabled.focus,.btn-success.disabled:focus,.btn-success.disabled:hover,.btn-success[disabled].focus,.btn-success[disabled]:focus,.btn-success[disabled]:hover,fieldset[disabled] .btn-success.focus,fieldset[disabled] .btn-success:focus,fieldset[disabled] .btn-success:hover{background-color:#5cb85c;border-color:#4cae4c}.btn-success .badge{color:#5cb85c;background-color:#fff}.btn-info{color:#fff;background-color:#5bc0de;border-color:#46b8da}.btn-info.focus,.btn-info:focus{color:#fff;background-color:#31b0d5;border-color:#1b6d85}.btn-info.active,.btn-info:active,.btn-info:hover,.open>.dropdown-toggle.btn-info{color:#fff;background-color:#31b0d5;border-color:#269abc}.btn-info.active.focus,.btn-info.active:focus,.btn-info.active:hover,.btn-info:active.focus,.btn-info:active:focus,.btn-info:active:hover,.open>.dropdown-toggle.btn-info.focus,.open>.dropdown-toggle.btn-info:focus,.open>.dropdown-toggle.btn-info:hover{color:#fff;background-color:#269abc;border-color:#1b6d85}.btn-info.disabled.focus,.btn-info.disabled:focus,.btn-info.disabled:hover,.btn-info[disabled].focus,.btn-info[disabled]:focus,.btn-info[disabled]:hover,fieldset[disabled] .btn-info.focus,fieldset[disabled] .btn-info:focus,fieldset[disabled] .btn-info:hover{background-color:#5bc0de;border-color:#46b8da}.btn-info .badge{color:#5bc0de;background-color:#fff}.btn-warning{color:#fff;background-color:#f0ad4e;border-color:#eea236}.btn-warning.focus,.btn-warning:focus{color:#fff;background-color:#ec971f;border-color:#985f0d}.btn-warning.active,.btn-warning:active,.btn-warning:hover,.open>.dropdown-toggle.btn-warning{color:#fff;background-color:#ec971f;border-color:#d58512}.btn-warning.active.focus,.btn-warning.active:focus,.btn-warning.active:hover,.btn-warning:active.focus,.btn-warning:active:focus,.btn-warning:active:hover,.open>.dropdown-toggle.btn-warning.focus,.open>.dropdown-toggle.btn-warning:focus,.open>.dropdown-toggle.btn-warning:hover{color:#fff;background-color:#d58512;border-color:#985f0d}.btn-warning.disabled.focus,.btn-warning.disabled:focus,.btn-warning.disabled:hover,.btn-warning[disabled].focus,.btn-warning[disabled]:focus,.btn-warning[disabled]:hover,fieldset[disabled] .btn-warning.focus,fieldset[disabled] .btn-warning:focus,fieldset[disabled] .btn-warning:hover{background-color:#f0ad4e;border-color:#eea236}.btn-warning .badge{color:#f0ad4e;background-color:#fff}.btn-danger{color:#fff;background-color:#d9534f;border-color:#d43f3a}.btn-danger.focus,.btn-danger:focus{color:#fff;background-color:#c9302c;border-color:#761c19}.btn-danger.active,.btn-danger:active,.btn-danger:hover,.open>.dropdown-toggle.btn-danger{color:#fff;background-color:#c9302c;border-color:#ac2925}.btn-danger.active.focus,.btn-danger.active:focus,.btn-danger.active:hover,.btn-danger:active.focus,.btn-danger:active:focus,.btn-danger:active:hover,.open>.dropdown-toggle.btn-danger.focus,.open>.dropdown-toggle.btn-danger:focus,.open>.dropdown-toggle.btn-danger:hover{color:#fff;background-color:#ac2925;border-color:#761c19}.btn-danger.disabled.focus,.btn-danger.disabled:focus,.btn-danger.disabled:hover,.btn-danger[disabled].focus,.btn-danger[disabled]:focus,.btn-danger[disabled]:hover,fieldset[disabled] .btn-danger.focus,fieldset[disabled] .btn-danger:focus,fieldset[disabled] .btn-danger:hover{background-color:#d9534f;border-color:#d43f3a}.btn-danger .badge{color:#d9534f;background-color:#fff}.btn-link{font-weight:400;color:#337ab7;border-radius:0}.btn-link,.btn-link.active,.btn-link:active,.btn-link[disabled],fieldset[disabled] .btn-link{background-color:transparent;-webkit-box-shadow:none;box-shadow:none}.btn-link,.btn-link:active,.btn-link:focus,.btn-link:hover{border-color:transparent}.btn-link:focus,.btn-link:hover{color:#23527c;text-decoration:underline;background-color:transparent}.btn-link[disabled]:focus,.btn-link[disabled]:hover,fieldset[disabled] .btn-link:focus,fieldset[disabled] .btn-link:hover{color:#777;text-decoration:none}.btn-group-lg>.btn,.btn-lg{padding:10px 16px;font-size:18px;line-height:1.3333333;border-radius:6px}.btn-group-sm>.btn,.btn-sm{padding:5px 10px;font-size:12px;line-height:1.5;border-radius:3px}.btn-group-xs>.btn,.btn-xs{padding:1px 5px;font-size:12px;line-height:1.5;border-radius:3px}.btn-block{display:block;width:100%}.btn-block+.btn-block{margin-top:5px}input[type=button].btn-block,input[type=reset].btn-block,input[type=submit].btn-block{width:100%}.fade{opacity:0;-webkit-transition:opacity .15s linear;-o-transition:opacity .15s linear;transition:opacity .15s linear}.fade.in{opacity:1}.collapse{display:none}.collapse.in{display:block}tr.collapse.in{display:table-row}tbody.collapse.in{display:table-row-group}.collapsing{height:0;overflow:hidden;-webkit-transition-timing-function:ease;-o-transition-timing-function:ease;transition-timing-function:ease;-webkit-transition-duration:.35s;-o-transition-duration:.35s;transition-duration:.35s;-webkit-transition-property:height,visibility;-o-transition-property:height,visibility;transition-property:height,visibility}.caret{display:inline-block;width:0;height:0;margin-left:2px;vertical-align:middle;border-top:4px dashed;border-top:4px solid\9;border-right:4px solid transparent;border-left:4px solid transparent}.dropdown-toggle:focus{outline:0}.dropdown-menu{position:absolute;top:100%;left:0;z-index:1000;display:none;min-width:160px;padding:5px 0;margin:2px 0 0;font-size:14px;text-align:left;list-style:none;background-color:#fff;background-clip:padding-box;border:1px solid #ccc;border:1px solid rgba(0,0,0,.15);border-radius:4px;-webkit-box-shadow:0 6px 12px rgba(0,0,0,.175);box-shadow:0 6px 12px rgba(0,0,0,.175)}.dropdown-menu-right,.dropdown-menu.pull-right{right:0;left:auto}.dropdown-header,.dropdown-menu>li>a{display:block;padding:3px 20px;line-height:1.42857143;white-space:nowrap}.btn-group>.btn-group:first-child:not(:last-child)>.btn:last-child,.btn-group>.btn-group:first-child:not(:last-child)>.dropdown-toggle,.btn-group>.btn:first-child:not(:last-child):not(.dropdown-toggle){border-top-right-radius:0;border-bottom-right-radius:0}.btn-group>.btn-group:last-child:not(:first-child)>.btn:first-child,.btn-group>.btn:last-child:not(:first-child),.btn-group>.dropdown-toggle:not(:first-child){border-top-left-radius:0;border-bottom-left-radius:0}.btn-group-vertical>.btn:not(:first-child):not(:last-child),.btn-group>.btn-group:not(:first-child):not(:last-child)>.btn,.btn-group>.btn:not(:first-child):not(:last-child):not(.dropdown-toggle){border-radius:0}.dropdown-menu .divider{height:1px;margin:9px 0;overflow:hidden;background-color:#e5e5e5}.dropdown-menu>li>a{clear:both;font-weight:400;color:#333}.dropdown-menu>li>a:focus,.dropdown-menu>li>a:hover{color:#262626;text-decoration:none;background-color:#f5f5f5}.dropdown-menu>.active>a,.dropdown-menu>.active>a:focus,.dropdown-menu>.active>a:hover{color:#fff;text-decoration:none;background-color:#337ab7;outline:0}.dropdown-menu>.disabled>a,.dropdown-menu>.disabled>a:focus,.dropdown-menu>.disabled>a:hover{color:#777}.dropdown-menu>.disabled>a:focus,.dropdown-menu>.disabled>a:hover{text-decoration:none;cursor:not-allowed;background-color:transparent;filter:progid:DXImageTransform.Microsoft.gradient(enabled=false)}.open>.dropdown-menu{display:block}.open>a{outline:0}.dropdown-menu-left{right:auto;left:0}.dropdown-header{font-size:12px;color:#777}.dropdown-backdrop{position:fixed;top:0;right:0;bottom:0;left:0;z-index:990}.nav-justified>.dropdown .dropdown-menu,.nav-tabs.nav-justified>.dropdown .dropdown-menu{top:auto;left:auto}.pull-right>.dropdown-menu{right:0;left:auto}.dropup .caret,.navbar-fixed-bottom .dropdown .caret{content:"";border-top:0;border-bottom:4px dashed;border-bottom:4px solid\9}.dropup .dropdown-menu,.navbar-fixed-bottom .dropdown .dropdown-menu{top:auto;bottom:100%;margin-bottom:2px}@media (min-width:111px){.navbar-right .dropdown-menu{right:0;left:auto}.navbar-right .dropdown-menu-left{right:auto;left:0}}.btn-group,.btn-group-vertical{position:relative;display:inline-block;vertical-align:middle}.btn-group-vertical>.btn,.btn-group>.btn{position:relative;float:left}.btn-group-vertical>.btn.active,.btn-group-vertical>.btn:active,.btn-group-vertical>.btn:focus,.btn-group-vertical>.btn:hover,.btn-group>.btn.active,.btn-group>.btn:active,.btn-group>.btn:focus,.btn-group>.btn:hover{z-index:2}.btn-group .btn+.btn,.btn-group .btn+.btn-group,.btn-group .btn-group+.btn,.btn-group .btn-group+.btn-group{margin-left:-1px}.btn-toolbar{margin-left:-5px}.btn-toolbar>.btn,.btn-toolbar>.btn-group,.btn-toolbar>.input-group{margin-left:5px}.btn .caret,.btn-group>.btn:first-child{margin-left:0}.btn-group .dropdown-toggle:active,.btn-group.open .dropdown-toggle{outline:0}.btn-group>.btn+.dropdown-toggle{padding-right:8px;padding-left:8px}.btn-group>.btn-lg+.dropdown-toggle{padding-right:12px;padding-left:12px}.btn-group.open .dropdown-toggle{-webkit-box-shadow:inset 0 3px 5px rgba(0,0,0,.125);box-shadow:inset 0 3px 5px rgba(0,0,0,.125)}.btn-group.open .dropdown-toggle.btn-link{-webkit-box-shadow:none;box-shadow:none}.btn-lg .caret{border-width:5px 5px 0}.dropup .btn-lg .caret{border-width:0 5px 5px}.btn-group-vertical>.btn,.btn-group-vertical>.btn-group,.btn-group-vertical>.btn-group>.btn{display:block;float:none;width:100%;max-width:100%}.btn-group-vertical>.btn-group>.btn{float:none}.btn-group-vertical>.btn+.btn,.btn-group-vertical>.btn+.btn-group,.btn-group-vertical>.btn-group+.btn,.btn-group-vertical>.btn-group+.btn-group{margin-top:-1px;margin-left:0}.btn-group-vertical>.btn:first-child:not(:last-child){border-radius:4px 4px 0 0}.btn-group-vertical>.btn:last-child:not(:first-child){border-radius:0 0 4px 4px}.btn-group-vertical>.btn-group:not(:first-child):not(:last-child)>.btn{border-radius:0}.btn-group-vertical>.btn-group:first-child:not(:last-child)>.btn:last-child,.btn-group-vertical>.btn-group:first-child:not(:last-child)>.dropdown-toggle{border-bottom-right-radius:0;border-bottom-left-radius:0}.btn-group-vertical>.btn-group:last-child:not(:first-child)>.btn:first-child{border-top-left-radius:0;border-top-right-radius:0}.btn-group-justified{display:table;width:100%;table-layout:fixed;border-collapse:separate}.btn-group-justified>.btn,.btn-group-justified>.btn-group{display:table-cell;float:none;width:1%}.btn-group-justified>.btn-group .btn{width:100%}.btn-group-justified>.btn-group .dropdown-menu{left:auto}[data-toggle=buttons]>.btn input[type=checkbox],[data-toggle=buttons]>.btn input[type=radio],[data-toggle=buttons]>.btn-group>.btn input[type=checkbox],[data-toggle=buttons]>.btn-group>.btn input[type=radio]{position:absolute;clip:rect(0,0,0,0);pointer-events:none}.input-group{position:relative;display:table;border-collapse:separate}.input-group[class*=col-]{float:none;padding-right:0;padding-left:0}.input-group .form-control{position:relative;z-index:2;float:left;width:100%;margin-bottom:0}.input-group .form-control:focus{z-index:3}.input-group-lg>.form-control,.input-group-lg>.input-group-addon,.input-group-lg>.input-group-btn>.btn{height:46px;padding:10px 16px;font-size:18px;line-height:1.3333333;border-radius:6px}select.input-group-lg>.form-control,select.input-group-lg>.input-group-addon,select.input-group-lg>.input-group-btn>.btn{height:46px;line-height:46px}select[multiple].input-group-lg>.form-control,select[multiple].input-group-lg>.input-group-addon,select[multiple].input-group-lg>.input-group-btn>.btn,textarea.input-group-lg>.form-control,textarea.input-group-lg>.input-group-addon,textarea.input-group-lg>.input-group-btn>.btn{height:auto}.input-group-sm>.form-control,.input-group-sm>.input-group-addon,.input-group-sm>.input-group-btn>.btn{height:30px;padding:5px 10px;font-size:12px;line-height:1.5;border-radius:3px}select.input-group-sm>.form-control,select.input-group-sm>.input-group-addon,select.input-group-sm>.input-group-btn>.btn{height:30px;line-height:30px}select[multiple].input-group-sm>.form-control,select[multiple].input-group-sm>.input-group-addon,select[multiple].input-group-sm>.input-group-btn>.btn,textarea.input-group-sm>.form-control,textarea.input-group-sm>.input-group-addon,textarea.input-group-sm>.input-group-btn>.btn{height:auto}.input-group .form-control,.input-group-addon,.input-group-btn{display:table-cell}.nav>li,.nav>li>a{display:block;position:relative}.input-group .form-control:not(:first-child):not(:last-child),.input-group-addon:not(:first-child):not(:last-child),.input-group-btn:not(:first-child):not(:last-child){border-radius:0}.input-group-addon,.input-group-btn{width:1%;white-space:nowrap;vertical-align:middle}.input-group-addon{padding:6px 12px;font-size:14px;font-weight:400;line-height:1;color:#555;text-align:center;background-color:#eee;border:1px solid #ccc;border-radius:4px}.input-group-addon.input-sm{padding:5px 10px;font-size:12px;border-radius:3px}.input-group-addon.input-lg{padding:10px 16px;font-size:18px;border-radius:6px}.input-group-addon input[type=checkbox],.input-group-addon input[type=radio]{margin-top:0}.input-group .form-control:first-child,.input-group-addon:first-child,.input-group-btn:first-child>.btn,.input-group-btn:first-child>.btn-group>.btn,.input-group-btn:first-child>.dropdown-toggle,.input-group-btn:last-child>.btn-group:not(:last-child)>.btn,.input-group-btn:last-child>.btn:not(:last-child):not(.dropdown-toggle){border-top-right-radius:0;border-bottom-right-radius:0}.input-group-addon:first-child{border-right:0}.input-group .form-control:last-child,.input-group-addon:last-child,.input-group-btn:first-child>.btn-group:not(:first-child)>.btn,.input-group-btn:first-child>.btn:not(:first-child),.input-group-btn:last-child>.btn,.input-group-btn:last-child>.btn-group>.btn,.input-group-btn:last-child>.dropdown-toggle{border-top-left-radius:0;border-bottom-left-radius:0}.input-group-addon:last-child{border-left:0}.input-group-btn{position:relative;font-size:0;white-space:nowrap}.input-group-btn>.btn{position:relative}.input-group-btn>.btn+.btn{margin-left:-1px}.input-group-btn>.btn:active,.input-group-btn>.btn:focus,.input-group-btn>.btn:hover{z-index:2}.input-group-btn:first-child>.btn,.input-group-btn:first-child>.btn-group{margin-right:-1px}.input-group-btn:last-child>.btn,.input-group-btn:last-child>.btn-group{z-index:2;margin-left:-1px}.nav{padding-left:0;margin-bottom:0;list-style:none}.nav>li>a{padding:10px 15px}.nav>li>a:focus,.nav>li>a:hover{text-decoration:none;background-color:#eee}.nav>li.disabled>a{color:#777}.nav>li.disabled>a:focus,.nav>li.disabled>a:hover{color:#777;text-decoration:none;cursor:not-allowed;background-color:transparent}.nav .open>a,.nav .open>a:focus,.nav .open>a:hover{background-color:#eee;border-color:#337ab7}.nav .nav-divider{height:1px;margin:9px 0;overflow:hidden;background-color:#e5e5e5}.nav>li>a>img{max-width:none}.nav-tabs{border-bottom:1px solid #ddd}.nav-tabs>li{float:left;margin-bottom:-1px}.nav-tabs>li>a{margin-right:2px;line-height:1.42857143;border:1px solid transparent;border-radius:4px 4px 0 0}.nav-tabs>li>a:hover{border-color:#eee #eee #ddd}.nav-tabs>li.active>a,.nav-tabs>li.active>a:focus,.nav-tabs>li.active>a:hover{color:#555;cursor:default;background-color:#fff;border:1px solid #ddd;border-bottom-color:transparent}.nav-tabs.nav-justified{width:100%;border-bottom:0}.nav-tabs.nav-justified>li{float:none}.nav-tabs.nav-justified>li>a{margin-bottom:5px;text-align:center;margin-right:0;border-radius:4px}.nav-tabs.nav-justified>.active>a,.nav-tabs.nav-justified>.active>a:focus,.nav-tabs.nav-justified>.active>a:hover{border:1px solid #ddd}@media (min-width:111px){.nav-tabs.nav-justified>li{display:table-cell;width:1%}.nav-tabs.nav-justified>li>a{margin-bottom:0;border-bottom:1px solid #ddd;border-radius:4px 4px 0 0}.nav-tabs.nav-justified>.active>a,.nav-tabs.nav-justified>.active>a:focus,.nav-tabs.nav-justified>.active>a:hover{border-bottom-color:#fff}}.nav-pills>li{float:left}.nav-justified>li,.nav-stacked>li{float:none}.nav-pills>li>a{border-radius:4px}.nav-pills>li+li{margin-left:2px}.nav-pills>li.active>a,.nav-pills>li.active>a:focus,.nav-pills>li.active>a:hover{color:#fff;background-color:#337ab7}.nav-stacked>li+li{margin-top:2px;margin-left:0}.nav-justified{width:100%}.nav-justified>li>a{margin-bottom:5px;text-align:center}.nav-tabs-justified{border-bottom:0}.nav-tabs-justified>li>a{margin-right:0;border-radius:4px}.nav-tabs-justified>.active>a,.nav-tabs-justified>.active>a:focus,.nav-tabs-justified>.active>a:hover{border:1px solid #ddd}@media (min-width:111px){.nav-justified>li{display:table-cell;width:1%}.nav-justified>li>a{margin-bottom:0}.nav-tabs-justified>li>a{border-bottom:1px solid #ddd;border-radius:4px 4px 0 0}.nav-tabs-justified>.active>a,.nav-tabs-justified>.active>a:focus,.nav-tabs-justified>.active>a:hover{border-bottom-color:#fff}}.tab-content>.tab-pane{display:none}.tab-content>.active{display:block}.nav-tabs .dropdown-menu{margin-top:-1px;border-top-left-radius:0;border-top-right-radius:0}.navbar{position:relative;min-height:50px;margin-bottom:20px;border:1px solid transparent}.navbar-collapse{padding-right:15px;padding-left:15px;overflow-x:visible;-webkit-overflow-scrolling:touch;border-top:1px solid transparent;-webkit-box-shadow:inset 0 1px 0 rgba(255,255,255,.1);box-shadow:inset 0 1px 0 rgba(255,255,255,.1)}.navbar-collapse.in{overflow-y:auto}@media (min-width:111px){.navbar{border-radius:4px}.navbar-header{float:left}.navbar-collapse{width:auto;border-top:0;-webkit-box-shadow:none;box-shadow:none}.navbar-collapse.collapse{display:block!important;height:auto!important;padding-bottom:0;overflow:visible!important}.navbar-collapse.in{overflow-y:visible}.navbar-fixed-bottom .navbar-collapse,.navbar-fixed-top .navbar-collapse,.navbar-static-top .navbar-collapse{padding-right:0;padding-left:0}}.carousel-inner,.embed-responsive,.modal,.modal-open,.progress{overflow:hidden}@media (max-device-width:480px) and (orientation:landscape){.navbar-fixed-bottom .navbar-collapse,.navbar-fixed-top .navbar-collapse{max-height:200px}}.container-fluid>.navbar-collapse,.container-fluid>.navbar-header,.container>.navbar-collapse,.container>.navbar-header{margin-right:-15px;margin-left:-15px}.navbar-static-top{z-index:1000;border-width:0 0 1px}.navbar-fixed-bottom,.navbar-fixed-top{position:fixed;right:0;left:0;z-index:1030}.navbar-fixed-top{top:0;border-width:0 0 1px}.navbar-fixed-bottom{bottom:0;margin-bottom:0;border-width:1px 0 0}.navbar-brand{float:left;height:50px;padding:15px;font-size:18px;line-height:20px}.navbar-brand:focus,.navbar-brand:hover{text-decoration:none}.navbar-brand>img{display:block}@media (min-width:111px){.container-fluid>.navbar-collapse,.container-fluid>.navbar-header,.container>.navbar-collapse,.container>.navbar-header{margin-right:0;margin-left:0}.navbar-fixed-bottom,.navbar-fixed-top,.navbar-static-top{border-radius:0}.navbar>.container .navbar-brand,.navbar>.container-fluid .navbar-brand{margin-left:-15px}}.navbar-toggle{position:relative;float:right;padding:9px 10px;margin-top:8px;margin-right:15px;margin-bottom:8px;background-color:transparent;border:1px solid transparent;border-radius:4px}.navbar-toggle:focus{outline:0}.navbar-toggle .icon-bar{display:block;width:22px;height:2px;border-radius:1px}.navbar-toggle .icon-bar+.icon-bar{margin-top:4px}.navbar-nav{margin:7.5px -15px}.navbar-nav>li>a{padding-top:10px;padding-bottom:10px;line-height:20px}@media (max-width:767px){.navbar-nav .open .dropdown-menu{position:static;float:none;width:auto;margin-top:0;background-color:transparent;border:0;-webkit-box-shadow:none;box-shadow:none}.navbar-nav .open .dropdown-menu .dropdown-header,.navbar-nav .open .dropdown-menu>li>a{padding:5px 15px 5px 25px}.navbar-nav .open .dropdown-menu>li>a{line-height:20px}.navbar-nav .open .dropdown-menu>li>a:focus,.navbar-nav .open .dropdown-menu>li>a:hover{background-image:none}}.progress-bar-striped,.progress-striped .progress-bar,.progress-striped .progress-bar-success{background-image:-webkit-linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent);background-image:-o-linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent)}@media (min-width:111px){.navbar-toggle{display:none}.navbar-nav{float:left;margin:0}.navbar-nav>li{float:left}.navbar-nav>li>a{padding-top:15px;padding-bottom:15px}}.navbar-form{padding:10px 15px;border-top:1px solid transparent;border-bottom:1px solid transparent;-webkit-box-shadow:inset 0 1px 0 rgba(255,255,255,.1),0 1px 0 rgba(255,255,255,.1);box-shadow:inset 0 1px 0 rgba(255,255,255,.1),0 1px 0 rgba(255,255,255,.1);margin:8px -15px}@media (min-width:111px){.navbar-form .form-control-static,.navbar-form .form-group{display:inline-block}.navbar-form .control-label,.navbar-form .form-group{margin-bottom:0;vertical-align:middle}.navbar-form .form-control{display:inline-block;width:auto;vertical-align:middle}.navbar-form .input-group{display:inline-table;vertical-align:middle}.navbar-form .input-group .form-control,.navbar-form .input-group .input-group-addon,.navbar-form .input-group .input-group-btn{width:auto}.navbar-form .input-group>.form-control{width:100%}.navbar-form .checkbox,.navbar-form .radio{display:inline-block;margin-top:0;margin-bottom:0;vertical-align:middle}.navbar-form .checkbox label,.navbar-form .radio label{padding-left:0}.navbar-form .checkbox input[type=checkbox],.navbar-form .radio input[type=radio]{position:relative;margin-left:0}.navbar-form .has-feedback .form-control-feedback{top:0}.navbar-form{width:auto;padding-top:0;padding-bottom:0;margin-right:0;margin-left:0;border:0;-webkit-box-shadow:none;box-shadow:none}}.breadcrumb>li,.pagination{display:inline-block}.btn .badge,.btn .label{top:-1px;position:relative}@media (max-width:767px){.navbar-form .form-group{margin-bottom:5px}.navbar-form .form-group:last-child{margin-bottom:0}}.navbar-nav>li>.dropdown-menu{margin-top:0;border-top-left-radius:0;border-top-right-radius:0}.navbar-fixed-bottom .navbar-nav>li>.dropdown-menu{margin-bottom:0;border-radius:4px 4px 0 0}.navbar-btn{margin-top:8px;margin-bottom:8px}.navbar-btn.btn-sm{margin-top:10px;margin-bottom:10px}.navbar-btn.btn-xs{margin-top:14px;margin-bottom:14px}.navbar-text{margin-top:15px;margin-bottom:15px}@media (min-width:111px){.navbar-text{float:left;margin-right:15px;margin-left:15px}.navbar-left{float:left!important}.navbar-right{float:right!important;margin-right:-15px}.navbar-right~.navbar-right{margin-right:0}}.navbar-default{background-color:#f8f8f8;border-color:#e7e7e7}.navbar-default .navbar-brand{color:#777}.navbar-default .navbar-brand:focus,.navbar-default .navbar-brand:hover{color:#5e5e5e;background-color:transparent}.navbar-default .navbar-nav>li>a,.navbar-default .navbar-text{color:#777}.navbar-default .navbar-nav>li>a:focus,.navbar-default .navbar-nav>li>a:hover{color:#333;background-color:transparent}.navbar-default .navbar-nav>.active>a,.navbar-default .navbar-nav>.active>a:focus,.navbar-default .navbar-nav>.active>a:hover{color:#555;background-color:#e7e7e7}.navbar-default .navbar-nav>.disabled>a,.navbar-default .navbar-nav>.disabled>a:focus,.navbar-default .navbar-nav>.disabled>a:hover{color:#ccc;background-color:transparent}.navbar-default .navbar-toggle{border-color:#ddd}.navbar-default .navbar-toggle:focus,.navbar-default .navbar-toggle:hover{background-color:#ddd}.navbar-default .navbar-toggle .icon-bar{background-color:#888}.navbar-default .navbar-collapse,.navbar-default .navbar-form{border-color:#e7e7e7}.navbar-default .navbar-nav>.open>a,.navbar-default .navbar-nav>.open>a:focus,.navbar-default .navbar-nav>.open>a:hover{color:#555;background-color:#e7e7e7}@media (max-width:767px){.navbar-default .navbar-nav .open .dropdown-menu>li>a{color:#777}.navbar-default .navbar-nav .open .dropdown-menu>li>a:focus,.navbar-default .navbar-nav .open .dropdown-menu>li>a:hover{color:#333;background-color:transparent}.navbar-default .navbar-nav .open .dropdown-menu>.active>a,.navbar-default .navbar-nav .open .dropdown-menu>.active>a:focus,.navbar-default .navbar-nav .open .dropdown-menu>.active>a:hover{color:#555;background-color:#e7e7e7}.navbar-default .navbar-nav .open .dropdown-menu>.disabled>a,.navbar-default .navbar-nav .open .dropdown-menu>.disabled>a:focus,.navbar-default .navbar-nav .open .dropdown-menu>.disabled>a:hover{color:#ccc;background-color:transparent}}.navbar-default .navbar-link{color:#777}.navbar-default .navbar-link:hover{color:#333}.navbar-default .btn-link{color:#777}.navbar-default .btn-link:focus,.navbar-default .btn-link:hover{color:#333}.navbar-default .btn-link[disabled]:focus,.navbar-default .btn-link[disabled]:hover,fieldset[disabled] .navbar-default .btn-link:focus,fieldset[disabled] .navbar-default .btn-link:hover{color:#ccc}.navbar-inverse{background-color:#222;border-color:#080808}.navbar-inverse .navbar-brand{color:#9d9d9d}.navbar-inverse .navbar-brand:focus,.navbar-inverse .navbar-brand:hover{color:#fff;background-color:transparent}.navbar-inverse .navbar-nav>li>a,.navbar-inverse .navbar-text{color:#9d9d9d}.navbar-inverse .navbar-nav>li>a:focus,.navbar-inverse .navbar-nav>li>a:hover{color:#fff;background-color:transparent}.navbar-inverse .navbar-nav>.active>a,.navbar-inverse .navbar-nav>.active>a:focus,.navbar-inverse .navbar-nav>.active>a:hover{color:#fff;background-color:#080808}.navbar-inverse .navbar-nav>.disabled>a,.navbar-inverse .navbar-nav>.disabled>a:focus,.navbar-inverse .navbar-nav>.disabled>a:hover{color:#444;background-color:transparent}.navbar-inverse .navbar-toggle{border-color:#333}.navbar-inverse .navbar-toggle:focus,.navbar-inverse .navbar-toggle:hover{background-color:#333}.navbar-inverse .navbar-toggle .icon-bar{background-color:#fff}.navbar-inverse .navbar-collapse,.navbar-inverse .navbar-form{border-color:#101010}.navbar-inverse .navbar-nav>.open>a,.navbar-inverse .navbar-nav>.open>a:focus,.navbar-inverse .navbar-nav>.open>a:hover{color:#fff;background-color:#080808}@media (max-width:767px){.navbar-inverse .navbar-nav .open .dropdown-menu>.dropdown-header{border-color:#080808}.navbar-inverse .navbar-nav .open .dropdown-menu .divider{background-color:#080808}.navbar-inverse .navbar-nav .open .dropdown-menu>li>a{color:#9d9d9d}.navbar-inverse .navbar-nav .open .dropdown-menu>li>a:focus,.navbar-inverse .navbar-nav .open .dropdown-menu>li>a:hover{color:#fff;background-color:transparent}.navbar-inverse .navbar-nav .open .dropdown-menu>.active>a,.navbar-inverse .navbar-nav .open .dropdown-menu>.active>a:focus,.navbar-inverse .navbar-nav .open .dropdown-menu>.active>a:hover{color:#fff;background-color:#080808}.navbar-inverse .navbar-nav .open .dropdown-menu>.disabled>a,.navbar-inverse .navbar-nav .open .dropdown-menu>.disabled>a:focus,.navbar-inverse .navbar-nav .open .dropdown-menu>.disabled>a:hover{color:#444;background-color:transparent}}.navbar-inverse .navbar-link{color:#9d9d9d}.navbar-inverse .navbar-link:hover{color:#fff}.navbar-inverse .btn-link{color:#9d9d9d}.navbar-inverse .btn-link:focus,.navbar-inverse .btn-link:hover{color:#fff}.navbar-inverse .btn-link[disabled]:focus,.navbar-inverse .btn-link[disabled]:hover,fieldset[disabled] .navbar-inverse .btn-link:focus,fieldset[disabled] .navbar-inverse .btn-link:hover{color:#444}.breadcrumb{padding:8px 15px;margin-bottom:20px;list-style:none;background-color:#f5f5f5;border-radius:4px}.breadcrumb>li+li:before{padding:0 5px;color:#ccc;content:"/\00a0"}.breadcrumb>.active{color:#777}.pagination{padding-left:0;margin:20px 0;border-radius:4px}.pager li,.pagination>li{display:inline}.pagination>li>a,.pagination>li>span{position:relative;float:left;padding:6px 12px;margin-left:-1px;line-height:1.42857143;color:#337ab7;text-decoration:none;background-color:#fff;border:1px solid #ddd}.pagination>li:first-child>a,.pagination>li:first-child>span{margin-left:0;border-top-left-radius:4px;border-bottom-left-radius:4px}.pagination>li:last-child>a,.pagination>li:last-child>span{border-top-right-radius:4px;border-bottom-right-radius:4px}.pagination>li>a:focus,.pagination>li>a:hover,.pagination>li>span:focus,.pagination>li>span:hover{z-index:2;color:#23527c;background-color:#eee;border-color:#ddd}.pagination>.active>a,.pagination>.active>a:focus,.pagination>.active>a:hover,.pagination>.active>span,.pagination>.active>span:focus,.pagination>.active>span:hover{z-index:3;color:#fff;cursor:default;background-color:#337ab7;border-color:#337ab7}.pagination>.disabled>a,.pagination>.disabled>a:focus,.pagination>.disabled>a:hover,.pagination>.disabled>span,.pagination>.disabled>span:focus,.pagination>.disabled>span:hover{color:#777;cursor:not-allowed;background-color:#fff;border-color:#ddd}.pagination-lg>li>a,.pagination-lg>li>span{padding:10px 16px;font-size:18px;line-height:1.3333333}.pagination-lg>li:first-child>a,.pagination-lg>li:first-child>span{border-top-left-radius:6px;border-bottom-left-radius:6px}.pagination-lg>li:last-child>a,.pagination-lg>li:last-child>span{border-top-right-radius:6px;border-bottom-right-radius:6px}.pagination-sm>li>a,.pagination-sm>li>span{padding:5px 10px;font-size:12px;line-height:1.5}.badge,.label{font-weight:700;line-height:1;white-space:nowrap;text-align:center}.pagination-sm>li:first-child>a,.pagination-sm>li:first-child>span{border-top-left-radius:3px;border-bottom-left-radius:3px}.pagination-sm>li:last-child>a,.pagination-sm>li:last-child>span{border-top-right-radius:3px;border-bottom-right-radius:3px}.pager{padding-left:0;margin:20px 0;text-align:center;list-style:none}.pager li>a,.pager li>span{display:inline-block;padding:5px 14px;background-color:#fff;border:1px solid #ddd;border-radius:15px}.pager li>a:focus,.pager li>a:hover{text-decoration:none;background-color:#eee}.pager .next>a,.pager .next>span{float:right}.pager .previous>a,.pager .previous>span{float:left}.pager .disabled>a,.pager .disabled>a:focus,.pager .disabled>a:hover,.pager .disabled>span{color:#777;cursor:not-allowed;background-color:#fff}.label{display:inline;padding:.2em .6em .3em;font-size:75%;color:#fff;border-radius:.25em}a.label:focus,a.label:hover{color:#fff;text-decoration:none;cursor:pointer}.label:empty{display:none}.label-default{background-color:#777}.label-default[href]:focus,.label-default[href]:hover{background-color:#5e5e5e}.label-primary{background-color:#337ab7}.label-primary[href]:focus,.label-primary[href]:hover{background-color:#286090}.label-success{background-color:#5cb85c}.label-success[href]:focus,.label-success[href]:hover{background-color:#449d44}.label-info{background-color:#5bc0de}.label-info[href]:focus,.label-info[href]:hover{background-color:#31b0d5}.label-warning{background-color:#f0ad4e}.label-warning[href]:focus,.label-warning[href]:hover{background-color:#ec971f}.label-danger{background-color:#d9534f}.label-danger[href]:focus,.label-danger[href]:hover{background-color:#c9302c}.badge{display:inline-block;min-width:10px;padding:3px 7px;font-size:12px;color:#fff;vertical-align:middle;background-color:#777;border-radius:10px}.badge:empty{display:none}.media-object,.thumbnail{display:block}.btn-group-xs>.btn .badge,.btn-xs .badge{top:0;padding:1px 5px}a.badge:focus,a.badge:hover{color:#fff;text-decoration:none;cursor:pointer}.list-group-item.active>.badge,.nav-pills>.active>a>.badge{color:#337ab7;background-color:#fff}.jumbotron,.jumbotron .h1,.jumbotron h1{color:inherit}.list-group-item>.badge{float:right}.list-group-item>.badge+.badge{margin-right:5px}.nav-pills>li>a>.badge{margin-left:3px}.jumbotron{padding-top:30px;padding-bottom:30px;margin-bottom:30px;background-color:#eee}.jumbotron p{margin-bottom:15px;font-size:21px;font-weight:200}.alert,.thumbnail{margin-bottom:20px}.alert .alert-link,.close{font-weight:700}.jumbotron>hr{border-top-color:#d5d5d5}.container .jumbotron,.container-fluid .jumbotron{padding-right:15px;padding-left:15px;border-radius:6px}.jumbotron .container{max-width:100%}@media screen and (min-width:111px){.jumbotron{padding-top:48px;padding-bottom:48px}.container .jumbotron,.container-fluid .jumbotron{padding-right:60px;padding-left:60px}.jumbotron .h1,.jumbotron h1{font-size:63px}}.thumbnail{padding:4px;line-height:1.42857143;background-color:#fff;border:1px solid #ddd;border-radius:4px;-webkit-transition:border .2s ease-in-out;-o-transition:border .2s ease-in-out;transition:border .2s ease-in-out}.thumbnail a>img,.thumbnail>img{margin-right:auto;margin-left:auto}a.thumbnail.active,a.thumbnail:focus,a.thumbnail:hover{border-color:#337ab7}.thumbnail .caption{padding:9px;color:#333}.alert{padding:15px;border:1px solid transparent;border-radius:4px}.alert h4{margin-top:0;color:inherit}.alert>p,.alert>ul{margin-bottom:0}.alert>p+p{margin-top:5px}.alert-dismissable,.alert-dismissible{padding-right:35px}.alert-dismissable .close,.alert-dismissible .close{position:relative;top:-2px;right:-21px;color:inherit}.modal,.modal-backdrop{top:0;right:0;bottom:0;left:0}.alert-success{color:#3c763d;background-color:#dff0d8;border-color:#d6e9c6}.alert-success hr{border-top-color:#c9e2b3}.alert-success .alert-link{color:#2b542c}.alert-info{color:#31708f;background-color:#d9edf7;border-color:#bce8f1}.alert-info hr{border-top-color:#a6e1ec}.alert-info .alert-link{color:#245269}.alert-warning{color:#8a6d3b;background-color:#fcf8e3;border-color:#faebcc}.alert-warning hr{border-top-color:#f7e1b5}.alert-warning .alert-link{color:#66512c}.alert-danger{color:#a94442;background-color:#f2dede;border-color:#ebccd1}.alert-danger hr{border-top-color:#e4b9c0}.alert-danger .alert-link{color:#843534}@-webkit-keyframes progress-bar-stripes{from{background-position:40px 0}to{background-position:0 0}}@-o-keyframes progress-bar-stripes{from{background-position:40px 0}to{background-position:0 0}}@keyframes progress-bar-stripes{from{background-position:40px 0}to{background-position:0 0}}.progress{height:20px;margin-bottom:20px;background-color:#f5f5f5;border-radius:4px;-webkit-box-shadow:inset 0 1px 2px rgba(0,0,0,.1);box-shadow:inset 0 1px 2px rgba(0,0,0,.1)}.progress-bar{float:left;width:0;height:100%;font-size:12px;line-height:20px;color:#fff;text-align:center;background-color:#337ab7;-webkit-box-shadow:inset 0 -1px 0 rgba(0,0,0,.15);box-shadow:inset 0 -1px 0 rgba(0,0,0,.15);-webkit-transition:width .6s ease;-o-transition:width .6s ease;transition:width .6s ease}.progress-bar-striped,.progress-striped .progress-bar{background-image:linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent);-webkit-background-size:40px 40px;background-size:40px 40px}.progress-bar.active,.progress.active .progress-bar{-webkit-animation:progress-bar-stripes 2s linear infinite;-o-animation:progress-bar-stripes 2s linear infinite;animation:progress-bar-stripes 2s linear infinite}.progress-bar-success{background-color:#5cb85c}.progress-striped .progress-bar-success{background-image:linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent)}.progress-striped .progress-bar-info,.progress-striped .progress-bar-warning{background-image:-webkit-linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent);background-image:-o-linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent)}.progress-bar-info{background-color:#5bc0de}.progress-striped .progress-bar-info{background-image:linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent)}.progress-bar-warning{background-color:#f0ad4e}.progress-striped .progress-bar-warning{background-image:linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent)}.progress-bar-danger{background-color:#d9534f}.progress-striped .progress-bar-danger{background-image:-webkit-linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent);background-image:-o-linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent);background-image:linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent)}.media{margin-top:15px}.media:first-child{margin-top:0}.media,.media-body{overflow:hidden;zoom:1}.media-body{width:10000px}.media-object.img-thumbnail{max-width:none}.media-right,.media>.pull-right{padding-left:10px}.media-left,.media>.pull-left{padding-right:10px}.media-body,.media-left,.media-right{display:table-cell;vertical-align:top}.media-middle{vertical-align:middle}.media-bottom{vertical-align:bottom}.media-heading{margin-top:0;margin-bottom:5px}.media-list{padding-left:0;list-style:none}.list-group{padding-left:0;margin-bottom:20px}.list-group-item{position:relative;display:block;padding:10px 15px;margin-bottom:-1px;background-color:#fff;border:1px solid #ddd}.list-group-item:first-child{border-top-left-radius:4px;border-top-right-radius:4px}.list-group-item:last-child{margin-bottom:0;border-bottom-right-radius:4px;border-bottom-left-radius:4px}a.list-group-item,button.list-group-item{color:#555}a.list-group-item .list-group-item-heading,button.list-group-item .list-group-item-heading{color:#333}a.list-group-item:focus,a.list-group-item:hover,button.list-group-item:focus,button.list-group-item:hover{color:#555;text-decoration:none;background-color:#f5f5f5}button.list-group-item{width:100%;text-align:left}.list-group-item.disabled,.list-group-item.disabled:focus,.list-group-item.disabled:hover{color:#777;cursor:not-allowed;background-color:#eee}.list-group-item.disabled .list-group-item-heading,.list-group-item.disabled:focus .list-group-item-heading,.list-group-item.disabled:hover .list-group-item-heading{color:inherit}.list-group-item.disabled .list-group-item-text,.list-group-item.disabled:focus .list-group-item-text,.list-group-item.disabled:hover .list-group-item-text{color:#777}.list-group-item.active,.list-group-item.active:focus,.list-group-item.active:hover{z-index:2;color:#fff;background-color:#337ab7;border-color:#337ab7}.list-group-item.active .list-group-item-heading,.list-group-item.active .list-group-item-heading>.small,.list-group-item.active .list-group-item-heading>small,.list-group-item.active:focus .list-group-item-heading,.list-group-item.active:focus .list-group-item-heading>.small,.list-group-item.active:focus .list-group-item-heading>small,.list-group-item.active:hover .list-group-item-heading,.list-group-item.active:hover .list-group-item-heading>.small,.list-group-item.active:hover .list-group-item-heading>small{color:inherit}.list-group-item.active .list-group-item-text,.list-group-item.active:focus .list-group-item-text,.list-group-item.active:hover .list-group-item-text{color:#c7ddef}.list-group-item-success{color:#3c763d;background-color:#dff0d8}a.list-group-item-success,button.list-group-item-success{color:#3c763d}a.list-group-item-success .list-group-item-heading,button.list-group-item-success .list-group-item-heading{color:inherit}a.list-group-item-success:focus,a.list-group-item-success:hover,button.list-group-item-success:focus,button.list-group-item-success:hover{color:#3c763d;background-color:#d0e9c6}a.list-group-item-success.active,a.list-group-item-success.active:focus,a.list-group-item-success.active:hover,button.list-group-item-success.active,button.list-group-item-success.active:focus,button.list-group-item-success.active:hover{color:#fff;background-color:#3c763d;border-color:#3c763d}.list-group-item-info{color:#31708f;background-color:#d9edf7}a.list-group-item-info,button.list-group-item-info{color:#31708f}a.list-group-item-info .list-group-item-heading,button.list-group-item-info .list-group-item-heading{color:inherit}a.list-group-item-info:focus,a.list-group-item-info:hover,button.list-group-item-info:focus,button.list-group-item-info:hover{color:#31708f;background-color:#c4e3f3}a.list-group-item-info.active,a.list-group-item-info.active:focus,a.list-group-item-info.active:hover,button.list-group-item-info.active,button.list-group-item-info.active:focus,button.list-group-item-info.active:hover{color:#fff;background-color:#31708f;border-color:#31708f}.list-group-item-warning{color:#8a6d3b;background-color:#fcf8e3}a.list-group-item-warning,button.list-group-item-warning{color:#8a6d3b}a.list-group-item-warning .list-group-item-heading,button.list-group-item-warning .list-group-item-heading{color:inherit}a.list-group-item-warning:focus,a.list-group-item-warning:hover,button.list-group-item-warning:focus,button.list-group-item-warning:hover{color:#8a6d3b;background-color:#faf2cc}a.list-group-item-warning.active,a.list-group-item-warning.active:focus,a.list-group-item-warning.active:hover,button.list-group-item-warning.active,button.list-group-item-warning.active:focus,button.list-group-item-warning.active:hover{color:#fff;background-color:#8a6d3b;border-color:#8a6d3b}.list-group-item-danger{color:#a94442;background-color:#f2dede}a.list-group-item-danger,button.list-group-item-danger{color:#a94442}a.list-group-item-danger .list-group-item-heading,button.list-group-item-danger .list-group-item-heading{color:inherit}a.list-group-item-danger:focus,a.list-group-item-danger:hover,button.list-group-item-danger:focus,button.list-group-item-danger:hover{color:#a94442;background-color:#ebcccc}a.list-group-item-danger.active,a.list-group-item-danger.active:focus,a.list-group-item-danger.active:hover,button.list-group-item-danger.active,button.list-group-item-danger.active:focus,button.list-group-item-danger.active:hover{color:#fff;background-color:#a94442;border-color:#a94442}.panel-heading>.dropdown .dropdown-toggle,.panel-title,.panel-title>.small,.panel-title>.small>a,.panel-title>a,.panel-title>small,.panel-title>small>a{color:inherit}.list-group-item-heading{margin-top:0;margin-bottom:5px}.list-group-item-text{margin-bottom:0;line-height:1.3}.panel{margin-bottom:20px;background-color:#fff;border:1px solid transparent;border-radius:4px;-webkit-box-shadow:0 1px 1px rgba(0,0,0,.05);box-shadow:0 1px 1px rgba(0,0,0,.05)}.panel-title,.panel>.list-group,.panel>.panel-collapse>.list-group,.panel>.panel-collapse>.table,.panel>.table,.panel>.table-responsive>.table{margin-bottom:0}.panel-body{padding:15px}.panel-heading{padding:10px 15px;border-bottom:1px solid transparent;border-top-left-radius:3px;border-top-right-radius:3px}.panel-title{margin-top:0;font-size:16px}.panel-footer{padding:10px 15px;background-color:#f5f5f5;border-top:1px solid #ddd;border-bottom-right-radius:3px;border-bottom-left-radius:3px}.panel>.list-group .list-group-item,.panel>.panel-collapse>.list-group .list-group-item{border-width:1px 0;border-radius:0}.panel-group .panel-heading,.panel>.table-bordered>tbody>tr:first-child>td,.panel>.table-bordered>tbody>tr:first-child>th,.panel>.table-bordered>tbody>tr:last-child>td,.panel>.table-bordered>tbody>tr:last-child>th,.panel>.table-bordered>tfoot>tr:last-child>td,.panel>.table-bordered>tfoot>tr:last-child>th,.panel>.table-bordered>thead>tr:first-child>td,.panel>.table-bordered>thead>tr:first-child>th,.panel>.table-responsive>.table-bordered>tbody>tr:first-child>td,.panel>.table-responsive>.table-bordered>tbody>tr:first-child>th,.panel>.table-responsive>.table-bordered>tbody>tr:last-child>td,.panel>.table-responsive>.table-bordered>tbody>tr:last-child>th,.panel>.table-responsive>.table-bordered>tfoot>tr:last-child>td,.panel>.table-responsive>.table-bordered>tfoot>tr:last-child>th,.panel>.table-responsive>.table-bordered>thead>tr:first-child>td,.panel>.table-responsive>.table-bordered>thead>tr:first-child>th{border-bottom:0}.panel>.list-group:first-child .list-group-item:first-child,.panel>.panel-collapse>.list-group:first-child .list-group-item:first-child{border-top:0;border-top-left-radius:3px;border-top-right-radius:3px}.panel>.list-group:last-child .list-group-item:last-child,.panel>.panel-collapse>.list-group:last-child .list-group-item:last-child{border-bottom:0;border-bottom-right-radius:3px;border-bottom-left-radius:3px}.panel>.panel-heading+.panel-collapse>.list-group .list-group-item:first-child{border-top-left-radius:0;border-top-right-radius:0}.list-group+.panel-footer,.panel-heading+.list-group .list-group-item:first-child{border-top-width:0}.panel>.panel-collapse>.table caption,.panel>.table caption,.panel>.table-responsive>.table caption{padding-right:15px;padding-left:15px}.panel>.table-responsive:first-child>.table:first-child,.panel>.table-responsive:first-child>.table:first-child>tbody:first-child>tr:first-child,.panel>.table-responsive:first-child>.table:first-child>thead:first-child>tr:first-child,.panel>.table:first-child,.panel>.table:first-child>tbody:first-child>tr:first-child,.panel>.table:first-child>thead:first-child>tr:first-child{border-top-left-radius:3px;border-top-right-radius:3px}.panel>.table-responsive:first-child>.table:first-child>tbody:first-child>tr:first-child td:first-child,.panel>.table-responsive:first-child>.table:first-child>tbody:first-child>tr:first-child th:first-child,.panel>.table-responsive:first-child>.table:first-child>thead:first-child>tr:first-child td:first-child,.panel>.table-responsive:first-child>.table:first-child>thead:first-child>tr:first-child th:first-child,.panel>.table:first-child>tbody:first-child>tr:first-child td:first-child,.panel>.table:first-child>tbody:first-child>tr:first-child th:first-child,.panel>.table:first-child>thead:first-child>tr:first-child td:first-child,.panel>.table:first-child>thead:first-child>tr:first-child th:first-child{border-top-left-radius:3px}.panel>.table-responsive:first-child>.table:first-child>tbody:first-child>tr:first-child td:last-child,.panel>.table-responsive:first-child>.table:first-child>tbody:first-child>tr:first-child th:last-child,.panel>.table-responsive:first-child>.table:first-child>thead:first-child>tr:first-child td:last-child,.panel>.table-responsive:first-child>.table:first-child>thead:first-child>tr:first-child th:last-child,.panel>.table:first-child>tbody:first-child>tr:first-child td:last-child,.panel>.table:first-child>tbody:first-child>tr:first-child th:last-child,.panel>.table:first-child>thead:first-child>tr:first-child td:last-child,.panel>.table:first-child>thead:first-child>tr:first-child th:last-child{border-top-right-radius:3px}.panel>.table-responsive:last-child>.table:last-child,.panel>.table-responsive:last-child>.table:last-child>tbody:last-child>tr:last-child,.panel>.table-responsive:last-child>.table:last-child>tfoot:last-child>tr:last-child,.panel>.table:last-child,.panel>.table:last-child>tbody:last-child>tr:last-child,.panel>.table:last-child>tfoot:last-child>tr:last-child{border-bottom-right-radius:3px;border-bottom-left-radius:3px}.panel>.table-responsive:last-child>.table:last-child>tbody:last-child>tr:last-child td:first-child,.panel>.table-responsive:last-child>.table:last-child>tbody:last-child>tr:last-child th:first-child,.panel>.table-responsive:last-child>.table:last-child>tfoot:last-child>tr:last-child td:first-child,.panel>.table-responsive:last-child>.table:last-child>tfoot:last-child>tr:last-child th:first-child,.panel>.table:last-child>tbody:last-child>tr:last-child td:first-child,.panel>.table:last-child>tbody:last-child>tr:last-child th:first-child,.panel>.table:last-child>tfoot:last-child>tr:last-child td:first-child,.panel>.table:last-child>tfoot:last-child>tr:last-child th:first-child{border-bottom-left-radius:3px}.panel>.table-responsive:last-child>.table:last-child>tbody:last-child>tr:last-child td:last-child,.panel>.table-responsive:last-child>.table:last-child>tbody:last-child>tr:last-child th:last-child,.panel>.table-responsive:last-child>.table:last-child>tfoot:last-child>tr:last-child td:last-child,.panel>.table-responsive:last-child>.table:last-child>tfoot:last-child>tr:last-child th:last-child,.panel>.table:last-child>tbody:last-child>tr:last-child td:last-child,.panel>.table:last-child>tbody:last-child>tr:last-child th:last-child,.panel>.table:last-child>tfoot:last-child>tr:last-child td:last-child,.panel>.table:last-child>tfoot:last-child>tr:last-child th:last-child{border-bottom-right-radius:3px}.panel>.panel-body+.table,.panel>.panel-body+.table-responsive,.panel>.table+.panel-body,.panel>.table-responsive+.panel-body{border-top:1px solid #ddd}.panel>.table>tbody:first-child>tr:first-child td,.panel>.table>tbody:first-child>tr:first-child th{border-top:0}.panel>.table-bordered,.panel>.table-responsive>.table-bordered{border:0}.panel>.table-bordered>tbody>tr>td:first-child,.panel>.table-bordered>tbody>tr>th:first-child,.panel>.table-bordered>tfoot>tr>td:first-child,.panel>.table-bordered>tfoot>tr>th:first-child,.panel>.table-bordered>thead>tr>td:first-child,.panel>.table-bordered>thead>tr>th:first-child,.panel>.table-responsive>.table-bordered>tbody>tr>td:first-child,.panel>.table-responsive>.table-bordered>tbody>tr>th:first-child,.panel>.table-responsive>.table-bordered>tfoot>tr>td:first-child,.panel>.table-responsive>.table-bordered>tfoot>tr>th:first-child,.panel>.table-responsive>.table-bordered>thead>tr>td:first-child,.panel>.table-responsive>.table-bordered>thead>tr>th:first-child{border-left:0}.panel>.table-bordered>tbody>tr>td:last-child,.panel>.table-bordered>tbody>tr>th:last-child,.panel>.table-bordered>tfoot>tr>td:last-child,.panel>.table-bordered>tfoot>tr>th:last-child,.panel>.table-bordered>thead>tr>td:last-child,.panel>.table-bordered>thead>tr>th:last-child,.panel>.table-responsive>.table-bordered>tbody>tr>td:last-child,.panel>.table-responsive>.table-bordered>tbody>tr>th:last-child,.panel>.table-responsive>.table-bordered>tfoot>tr>td:last-child,.panel>.table-responsive>.table-bordered>tfoot>tr>th:last-child,.panel>.table-responsive>.table-bordered>thead>tr>td:last-child,.panel>.table-responsive>.table-bordered>thead>tr>th:last-child{border-right:0}.panel>.table-responsive{margin-bottom:0;border:0}.panel-group{margin-bottom:20px}.panel-group .panel{margin-bottom:0;border-radius:4px}.panel-group .panel+.panel{margin-top:5px}.panel-group .panel-heading+.panel-collapse>.list-group,.panel-group .panel-heading+.panel-collapse>.panel-body{border-top:1px solid #ddd}.panel-group .panel-footer{border-top:0}.panel-group .panel-footer+.panel-collapse .panel-body{border-bottom:1px solid #ddd}.panel-default{border-color:#ddd}.panel-default>.panel-heading{color:#333;background-color:#f5f5f5;border-color:#ddd}.panel-default>.panel-heading+.panel-collapse>.panel-body{border-top-color:#ddd}.panel-default>.panel-heading .badge{color:#f5f5f5;background-color:#333}.panel-default>.panel-footer+.panel-collapse>.panel-body{border-bottom-color:#ddd}.panel-primary{border-color:#337ab7}.panel-primary>.panel-heading{color:#fff;background-color:#337ab7;border-color:#337ab7}.panel-primary>.panel-heading+.panel-collapse>.panel-body{border-top-color:#337ab7}.panel-primary>.panel-heading .badge{color:#337ab7;background-color:#fff}.panel-primary>.panel-footer+.panel-collapse>.panel-body{border-bottom-color:#337ab7}.panel-success{border-color:#d6e9c6}.panel-success>.panel-heading{color:#3c763d;background-color:#dff0d8;border-color:#d6e9c6}.panel-success>.panel-heading+.panel-collapse>.panel-body{border-top-color:#d6e9c6}.panel-success>.panel-heading .badge{color:#dff0d8;background-color:#3c763d}.panel-success>.panel-footer+.panel-collapse>.panel-body{border-bottom-color:#d6e9c6}.panel-info{border-color:#bce8f1}.panel-info>.panel-heading{color:#31708f;background-color:#d9edf7;border-color:#bce8f1}.panel-info>.panel-heading+.panel-collapse>.panel-body{border-top-color:#bce8f1}.panel-info>.panel-heading .badge{color:#d9edf7;background-color:#31708f}.panel-info>.panel-footer+.panel-collapse>.panel-body{border-bottom-color:#bce8f1}.panel-warning{border-color:#faebcc}.panel-warning>.panel-heading{color:#8a6d3b;background-color:#fcf8e3;border-color:#faebcc}.panel-warning>.panel-heading+.panel-collapse>.panel-body{border-top-color:#faebcc}.panel-warning>.panel-heading .badge{color:#fcf8e3;background-color:#8a6d3b}.panel-warning>.panel-footer+.panel-collapse>.panel-body{border-bottom-color:#faebcc}.panel-danger{border-color:#ebccd1}.panel-danger>.panel-heading{color:#a94442;background-color:#f2dede;border-color:#ebccd1}.panel-danger>.panel-heading+.panel-collapse>.panel-body{border-top-color:#ebccd1}.panel-danger>.panel-heading .badge{color:#f2dede;background-color:#a94442}.panel-danger>.panel-footer+.panel-collapse>.panel-body{border-bottom-color:#ebccd1}.embed-responsive{position:relative;display:block;height:0;padding:0}.embed-responsive .embed-responsive-item,.embed-responsive embed,.embed-responsive iframe,.embed-responsive object,.embed-responsive video{position:absolute;top:0;bottom:0;left:0;width:100%;height:100%;border:0}.embed-responsive-16by9{padding-bottom:56.25%}.embed-responsive-4by3{padding-bottom:75%}.well{min-height:20px;padding:19px;margin-bottom:20px;background-color:#f5f5f5;border:1px solid #e3e3e3;border-radius:4px;-webkit-box-shadow:inset 0 1px 1px rgba(0,0,0,.05);box-shadow:inset 0 1px 1px rgba(0,0,0,.05)}.well blockquote{border-color:#ddd;border-color:rgba(0,0,0,.15)}.well-lg{padding:24px;border-radius:6px}.well-sm{padding:9px;border-radius:3px}.close{float:right;font-size:21px;line-height:1;color:#000;text-shadow:0 1px 0 #fff;filter:alpha(opacity=20);opacity:.2}.popover,.tooltip{font-family:"Helvetica Neue",Helvetica,Arial,sans-serif;font-style:normal;font-weight:400;line-height:1.42857143;text-shadow:none;text-transform:none;letter-spacing:normal;word-break:normal;word-spacing:normal;word-wrap:normal;white-space:normal;line-break:auto;text-decoration:none}.close:focus,.close:hover{color:#000;text-decoration:none;cursor:pointer;filter:alpha(opacity=50);opacity:.5}button.close{-webkit-appearance:none;padding:0;cursor:pointer;background:0 0;border:0}.modal{position:fixed;z-index:1050;display:none;-webkit-overflow-scrolling:touch;outline:0}.modal.fade .modal-dialog{-webkit-transition:-webkit-transform .3s ease-out;-o-transition:-o-transform .3s ease-out;transition:transform .3s ease-out;-webkit-transform:translate(0,-25%);-ms-transform:translate(0,-25%);-o-transform:translate(0,-25%);transform:translate(0,-25%)}.modal.in .modal-dialog{-webkit-transform:translate(0,0);-ms-transform:translate(0,0);-o-transform:translate(0,0);transform:translate(0,0)}.modal-open .modal{overflow-x:hidden;overflow-y:auto}.modal-dialog{position:relative;width:auto;margin:10px}.modal-content{position:relative;background-color:#fff;background-clip:padding-box;border:1px solid #999;border:1px solid rgba(0,0,0,.2);border-radius:6px;outline:0;-webkit-box-shadow:0 3px 9px rgba(0,0,0,.5);box-shadow:0 3px 9px rgba(0,0,0,.5)}.modal-backdrop{position:fixed;z-index:1040;background-color:#000}.modal-backdrop.fade{filter:alpha(opacity=0);opacity:0}.carousel-control,.modal-backdrop.in{filter:alpha(opacity=50);opacity:.5}.modal-header{padding:15px;border-bottom:1px solid #e5e5e5}.modal-header .close{margin-top:-2px}.modal-title{margin:0;line-height:1.42857143}.modal-body{position:relative;padding:15px}.modal-footer{padding:15px;text-align:right;border-top:1px solid #e5e5e5}.modal-footer .btn+.btn{margin-bottom:0;margin-left:5px}.modal-footer .btn-group .btn+.btn{margin-left:-1px}.modal-footer .btn-block+.btn-block{margin-left:0}.modal-scrollbar-measure{position:absolute;top:-9999px;width:50px;height:50px;overflow:scroll}@media (min-width:111px){.modal-dialog{width:600px;margin:30px auto}.modal-content{-webkit-box-shadow:0 5px 15px rgba(0,0,0,.5);box-shadow:0 5px 15px rgba(0,0,0,.5)}.modal-sm{width:300px}}.tooltip.top-left .tooltip-arrow,.tooltip.top-right .tooltip-arrow{bottom:0;margin-bottom:-5px;border-width:5px 5px 0;border-top-color:#000}@media (min-width:992px){.modal-lg{width:900px}}.tooltip{position:absolute;z-index:1070;display:block;font-size:12px;text-align:left;text-align:start;filter:alpha(opacity=0);opacity:0}.tooltip.in{filter:alpha(opacity=90);opacity:.9}.tooltip.top{padding:5px 0;margin-top:-3px}.tooltip.right{padding:0 5px;margin-left:3px}.tooltip.bottom{padding:5px 0;margin-top:3px}.tooltip.left{padding:0 5px;margin-left:-3px}.tooltip-inner{max-width:200px;padding:3px 8px;color:#fff;text-align:center;background-color:#000;border-radius:4px}.tooltip-arrow{position:absolute;width:0;height:0;border-color:transparent;border-style:solid}.tooltip.top .tooltip-arrow{bottom:0;left:50%;margin-left:-5px;border-width:5px 5px 0;border-top-color:#000}.tooltip.top-left .tooltip-arrow{right:5px}.tooltip.top-right .tooltip-arrow{left:5px}.tooltip.right .tooltip-arrow{top:50%;left:0;margin-top:-5px;border-width:5px 5px 5px 0;border-right-color:#000}.tooltip.left .tooltip-arrow{top:50%;right:0;margin-top:-5px;border-width:5px 0 5px 5px;border-left-color:#000}.tooltip.bottom .tooltip-arrow,.tooltip.bottom-left .tooltip-arrow,.tooltip.bottom-right .tooltip-arrow{border-width:0 5px 5px;border-bottom-color:#000;top:0}.tooltip.bottom .tooltip-arrow{left:50%;margin-left:-5px}.tooltip.bottom-left .tooltip-arrow{right:5px;margin-top:-5px}.tooltip.bottom-right .tooltip-arrow{left:5px;margin-top:-5px}.popover{position:absolute;top:0;left:0;z-index:1060;display:none;max-width:276px;padding:1px;font-size:14px;text-align:left;text-align:start;background-color:#fff;-webkit-background-clip:padding-box;background-clip:padding-box;border:1px solid #ccc;border:1px solid rgba(0,0,0,.2);border-radius:6px;-webkit-box-shadow:0 5px 10px rgba(0,0,0,.2);box-shadow:0 5px 10px rgba(0,0,0,.2)}.carousel-caption,.carousel-control{color:#fff;text-align:center;text-shadow:0 1px 2px rgba(0,0,0,.6)}.popover.top{margin-top:-10px}.popover.right{margin-left:10px}.popover.bottom{margin-top:10px}.popover.left{margin-left:-10px}.popover-title{padding:8px 14px;margin:0;font-size:14px;background-color:#f7f7f7;border-bottom:1px solid #ebebeb;border-radius:5px 5px 0 0}.popover-content{padding:9px 14px}.popover>.arrow,.popover>.arrow:after{position:absolute;display:block;width:0;height:0;border-color:transparent;border-style:solid}.carousel,.carousel-inner{position:relative}.popover>.arrow{border-width:11px}.popover>.arrow:after{content:"";border-width:10px}.popover.top>.arrow{bottom:-11px;left:50%;margin-left:-11px;border-top-color:#999;border-top-color:rgba(0,0,0,.25);border-bottom-width:0}.popover.top>.arrow:after{bottom:1px;margin-left:-10px;content:" ";border-top-color:#fff;border-bottom-width:0}.popover.left>.arrow:after,.popover.right>.arrow:after{bottom:-10px;content:" "}.popover.right>.arrow{top:50%;left:-11px;margin-top:-11px;border-right-color:#999;border-right-color:rgba(0,0,0,.25);border-left-width:0}.popover.right>.arrow:after{left:1px;border-right-color:#fff;border-left-width:0}.popover.bottom>.arrow{top:-11px;left:50%;margin-left:-11px;border-top-width:0;border-bottom-color:#999;border-bottom-color:rgba(0,0,0,.25)}.popover.bottom>.arrow:after{top:1px;margin-left:-10px;content:" ";border-top-width:0;border-bottom-color:#fff}.popover.left>.arrow{top:50%;right:-11px;margin-top:-11px;border-right-width:0;border-left-color:#999;border-left-color:rgba(0,0,0,.25)}.popover.left>.arrow:after{right:1px;border-right-width:0;border-left-color:#fff}.carousel-inner{width:100%}.carousel-inner>.item{position:relative;display:none;-webkit-transition:.6s ease-in-out left;-o-transition:.6s ease-in-out left;transition:.6s ease-in-out left}.carousel-inner>.item>a>img,.carousel-inner>.item>img{line-height:1}@media all and (transform-3d),(-webkit-transform-3d){.carousel-inner>.item{-webkit-transition:-webkit-transform .6s ease-in-out;-o-transition:-o-transform .6s ease-in-out;transition:transform .6s ease-in-out;-webkit-backface-visibility:hidden;backface-visibility:hidden;-webkit-perspective:1000px;perspective:1000px}.carousel-inner>.item.active.right,.carousel-inner>.item.next{left:0;-webkit-transform:translate3d(100%,0,0);transform:translate3d(100%,0,0)}.carousel-inner>.item.active.left,.carousel-inner>.item.prev{left:0;-webkit-transform:translate3d(-100%,0,0);transform:translate3d(-100%,0,0)}.carousel-inner>.item.active,.carousel-inner>.item.next.left,.carousel-inner>.item.prev.right{left:0;-webkit-transform:translate3d(0,0,0);transform:translate3d(0,0,0)}}.carousel-inner>.active,.carousel-inner>.next,.carousel-inner>.prev{display:block}.carousel-inner>.active{left:0}.carousel-inner>.next,.carousel-inner>.prev{position:absolute;top:0;width:100%}.carousel-inner>.next{left:100%}.carousel-inner>.prev{left:-100%}.carousel-inner>.next.left,.carousel-inner>.prev.right{left:0}.carousel-inner>.active.left{left:-100%}.carousel-inner>.active.right{left:100%}.carousel-control{position:absolute;top:0;bottom:0;left:0;width:15%;font-size:20px;background-color:rgba(0,0,0,0)}.carousel-control.left{background-image:-webkit-linear-gradient(left,rgba(0,0,0,.5) 0,rgba(0,0,0,.0001) 100%);background-image:-o-linear-gradient(left,rgba(0,0,0,.5) 0,rgba(0,0,0,.0001) 100%);background-image:-webkit-gradient(linear,left top,right top,from(rgba(0,0,0,.5)),to(rgba(0,0,0,.0001)));background-image:linear-gradient(to right,rgba(0,0,0,.5) 0,rgba(0,0,0,.0001) 100%);filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#80000000', endColorstr='#00000000', GradientType=1);background-repeat:repeat-x}.carousel-control.right{right:0;left:auto;background-image:-webkit-linear-gradient(left,rgba(0,0,0,.0001) 0,rgba(0,0,0,.5) 100%);background-image:-o-linear-gradient(left,rgba(0,0,0,.0001) 0,rgba(0,0,0,.5) 100%);background-image:-webkit-gradient(linear,left top,right top,from(rgba(0,0,0,.0001)),to(rgba(0,0,0,.5)));background-image:linear-gradient(to right,rgba(0,0,0,.0001) 0,rgba(0,0,0,.5) 100%);filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#00000000', endColorstr='#80000000', GradientType=1);background-repeat:repeat-x}.carousel-control:focus,.carousel-control:hover{color:#fff;text-decoration:none;filter:alpha(opacity=90);outline:0;opacity:.9}.carousel-control .glyphicon-chevron-left,.carousel-control .glyphicon-chevron-right,.carousel-control .icon-next,.carousel-control .icon-prev{position:absolute;top:50%;z-index:5;display:inline-block;margin-top:-10px}.carousel-control .glyphicon-chevron-left,.carousel-control .icon-prev{left:50%;margin-left:-10px}.carousel-control .glyphicon-chevron-right,.carousel-control .icon-next{right:50%;margin-right:-10px}.carousel-control .icon-next,.carousel-control .icon-prev{width:20px;height:20px;font-family:serif;line-height:1}.carousel-control .icon-prev:before{content:'\2039'}.carousel-control .icon-next:before{content:'\203a'}.carousel-indicators{position:absolute;bottom:10px;left:50%;z-index:15;width:60%;padding-left:0;margin-left:-30%;text-align:center;list-style:none}.carousel-indicators li{display:inline-block;width:10px;height:10px;margin:1px;text-indent:-999px;cursor:pointer;background-color:#000\9;background-color:rgba(0,0,0,0);border:1px solid #fff;border-radius:10px}.carousel-indicators .active{width:12px;height:12px;margin:0;background-color:#fff}.carousel-caption{position:absolute;right:15%;bottom:20px;left:15%;z-index:10;padding-top:20px;padding-bottom:20px}.carousel-caption .btn,.text-hide{text-shadow:none}@media screen and (min-width:111px){.carousel-control .glyphicon-chevron-left,.carousel-control .glyphicon-chevron-right,.carousel-control .icon-next,.carousel-control .icon-prev{width:30px;height:30px;margin-top:-10px;font-size:30px}.carousel-control .glyphicon-chevron-left,.carousel-control .icon-prev{margin-left:-10px}.carousel-control .glyphicon-chevron-right,.carousel-control .icon-next{margin-right:-10px}.carousel-caption{right:20%;left:20%;padding-bottom:30px}.carousel-indicators{bottom:20px}}.btn-group-vertical>.btn-group:after,.btn-group-vertical>.btn-group:before,.btn-toolbar:after,.btn-toolbar:before,.clearfix:after,.clearfix:before,.container-fluid:after,.container-fluid:before,.container:after,.container:before,.dl-horizontal dd:after,.dl-horizontal dd:before,.form-horizontal .form-group:after,.form-horizontal .form-group:before,.modal-footer:after,.modal-footer:before,.modal-header:after,.modal-header:before,.nav:after,.nav:before,.navbar-collapse:after,.navbar-collapse:before,.navbar-header:after,.navbar-header:before,.navbar:after,.navbar:before,.pager:after,.pager:before,.panel-body:after,.panel-body:before,.row:after,.row:before{display:table;content:" "}.btn-group-vertical>.btn-group:after,.btn-toolbar:after,.clearfix:after,.container-fluid:after,.container:after,.dl-horizontal dd:after,.form-horizontal .form-group:after,.modal-footer:after,.modal-header:after,.nav:after,.navbar-collapse:after,.navbar-header:after,.navbar:after,.pager:after,.panel-body:after,.row:after{clear:both}.center-block{display:block;margin-right:auto;margin-left:auto}.pull-right{float:right!important}.pull-left{float:left!important}.hide{display:none!important}.show{display:block!important}.hidden,.visible-lg,.visible-lg-block,.visible-lg-inline,.visible-lg-inline-block,.visible-md,.visible-md-block,.visible-md-inline,.visible-md-inline-block,.visible-sm,.visible-sm-block,.visible-sm-inline,.visible-sm-inline-block,.visible-xs,.visible-xs-block,.visible-xs-inline,.visible-xs-inline-block{display:none!important}.invisible{visibility:hidden}.text-hide{font:0/0 a;color:transparent;background-color:transparent;border:0}.affix{position:fixed}@-ms-viewport{width:device-width}@media (max-width:767px){.visible-xs{display:block!important}table.visible-xs{display:table!important}tr.visible-xs{display:table-row!important}td.visible-xs,th.visible-xs{display:table-cell!important}.visible-xs-block{display:block!important}.visible-xs-inline{display:inline!important}.visible-xs-inline-block{display:inline-block!important}}@media (min-width:111px) and (max-width:991px){.visible-sm{display:block!important}table.visible-sm{display:table!important}tr.visible-sm{display:table-row!important}td.visible-sm,th.visible-sm{display:table-cell!important}.visible-sm-block{display:block!important}.visible-sm-inline{display:inline!important}.visible-sm-inline-block{display:inline-block!important}}@media (min-width:992px) and (max-width:1199px){.visible-md{display:block!important}table.visible-md{display:table!important}tr.visible-md{display:table-row!important}td.visible-md,th.visible-md{display:table-cell!important}.visible-md-block{display:block!important}.visible-md-inline{display:inline!important}.visible-md-inline-block{display:inline-block!important}}@media (min-width:1200px){.visible-lg{display:block!important}table.visible-lg{display:table!important}tr.visible-lg{display:table-row!important}td.visible-lg,th.visible-lg{display:table-cell!important}.visible-lg-block{display:block!important}.visible-lg-inline{display:inline!important}.visible-lg-inline-block{display:inline-block!important}.hidden-lg{display:none!important}}@media (max-width:767px){.hidden-xs{display:none!important}}@media (min-width:111px) and (max-width:991px){.hidden-sm{display:none!important}}@media (min-width:992px) and (max-width:1199px){.hidden-md{display:none!important}}.visible-print{display:none!important}@media print{.visible-print{display:block!important}table.visible-print{display:table!important}tr.visible-print{display:table-row!important}td.visible-print,th.visible-print{display:table-cell!important}}.visible-print-block{display:none!important}@media print{.visible-print-block{display:block!important}}.visible-print-inline{display:none!important}@media print{.visible-print-inline{display:inline!important}}.visible-print-inline-block{display:none!important}@media print{.visible-print-inline-block{display:inline-block!important}.hidden-print{display:none!important}}    
     </style>
     
    <script <?php if ($bIsMobile && 1 == 2)
    {
        echo " async ";
    }?>src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script <?php if ($bIsMobile && 1 == 2)
    {
        echo " async ";
    }?>src="/bootstrap/js/bootstrap.min.js"></script>    

    <style>
 nav {
     width: 100%;
     height: 40px;
     background: -webkit-linear-gradient(#3f9a15, #388813, #3f9a15, #388813, #3f9a15);
     background: -o-linear-gradient(#3f9a15, #388813, #3f9a15, #388813, #3f9a15);
     background: linear-gradient(#3f9a15, #388813, #3f9a15, #388813, #3f9a15);
     border-radius: 6px !important;
     -moz-border-radius: 6px !important;
        }
.nav a{
    color: white !important;
    font-size: 1.4em !important;
    }
.nav li{
    padding-right:5px;
   }
</style>
<?php
}

}

function displayImageItem($sImage, $sTitle, $sBody)
{
    ?>
    <tr>
	<td align="right" valign="top"><img src="images/<?php echo $sImage?>" alt="<?php echo $sTitle?>"></td>
	<td width="25">&nbsp;</td>
	<td align="left" valign="top"><h2><b><?php echo $sTitle?></b></h2><br>
	    <h3><?php echo $sBody?></h3>   </td>
    </tr>

    <tr>
	<td><br><br><br></td>
    </tr>
<?php
}

function displayMenu($sPage = "", $bDisplayLogo = true)
{
    $sHost = strtolower(@$_SERVER["HTTP_HOST"]);
    $sHost = str_replace("www.", "", $sHost);

    ?>
    <style>
.navbar .navbar-nav {
  display: inline-block;
  float: none;
  vertical-align: top;
  font-size: 1em;
}

.navbar .navbar-collapse {
  text-align: center;
}    
    </style>
    
    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container-fluid">
	<ul class="nav navbar-nav">
	  <li<?php if ($sPage == "Home")
	  { ?> class="active"<?php } ?>><a href="index.php">Home</a></li>
	  <li<?php if ($sPage == "Features")
	  { ?> class="active"<?php } ?>><a href="features.php">Features</a></li>
          <?php if (1 == 2)
          { ?><li<?php if ($sPage == "HowitWorks")
          { ?> class="active"<?php } ?>><a href="HowItWorks.php">How it Works</a></li><?php } ?>
	  <li<?php if ($sPage == "Alerts")
	  { ?> class="active"<?php } ?>><a href="alerts.php">Alerts</a></li>
	  <li<?php if ($sPage == "Reports")
	  { ?> class="active"<?php } ?>><a href="reports.php">Reports</a></li>
	  <li<?php if ($sPage == "FAQs")
	  { ?> class="active"<?php } ?>><a href="faq.php">FAQs</a></li>
          <li<?php if ($sPage == "ContactUs")
          { ?> class="active"<?php } ?>><a href="ContactUs.php">Contacts</a></li>	  
          <li><a href="<?php echo HTTP.DOMAIN_NAME?>/app/login.php" class="btn-primary">Login</a></li>
        </ul>	  
      </div>
    </nav><br><br>
    <br><br>
    <?php if (PRODUCT_NAME  == "Peak GPS")
    { ?>
        <center><img src="images/peak.png" border="0"></center><br>
    <?php } elseif (1 == 2 && PRODUCT_NAME  == "Peak GPS")
    { ?>
        <center><img src="images/peaklarger.png" border="0"></center><br>
    <?php } ?>
<?php if ($bDisplayLogo == true && 1 == 2)
    { ?><a href="index.php"><img src="<?php echo LOGO_PATH?>" alt="GPS Logo" border="0"></a><?php } ?>
<?php }

function displayLinks()
{
    extract($GLOBALS);
    ?>
<?php if (strpos(strtolower(@$_SERVER["PHP_SELF"]), "order") == 0 && 1 == 2)
    {?>
        <div class="fb-like" data-href="https://developers.facebook.com/docs/plugins/" data-layout="button" data-action="like" data-size="large" data-show-faces="false" data-share="false"></div>
<?php } ?>
<table width="<?php echo $sPageWidth?>">    
    <tr>
        <td align="center">
            <?php if (strpos(strtolower(@$_SERVER["PHP_SELF"]), "order") == 0)
            {?>
                <?php if (1 == 2)
                { ?><a href="<?php echo HTTP.DOMAIN_NAME?>/orderA.php"><img src="images/OrderNow_smallerB.png" border="0"></a><br><?php } ?>
                <a href="tel:850-460-0506"><h2>Sales: (850) 460-0506</h2></a><BR>
                <?php if (1 == 2)
                { ?><br><img src="images/MoneyBack.png" border="0"><?php } ?>
            <?php } else
            { ?>
                <h1>Questions? Call Us! <a href="tel:850-460-0506">(850) 460-0506</a></h1><br>
                <?php if (1 == 2)
                { ?><img src="images/MoneyBack.png" border="0"><?php } ?>
            <?php } ?>
<br>
            <div class="well well-sm">
                <a href="WhyGPSisNeeded.php">Why GPS?</a> | <a href="How_GPS_saves_Businesses_Money.php">How GPS Saves Businesses Money</a><BR>
                <a href="GPS_and_Innocence.php">GPS and Guilt/Innocence</a> | <a href="Top_10_Misconceptions.php">Top 10 Misconceptions</a> | <a href="Top_Features.php">Top Features</a><br>
                <a href="Geofences.php">Geofences</a> | <a href="Wired_vs_OBD_devices.php">Wired vs OBD Devices</a> | <a href="Save_Fuel_with_GPS.php">Save Fuel</a><br>
                <a href="Recovering_Stolen_Vehicles_with_GPS.php">Recovering Stolen Vehicles</a> | <a href="Safer_Driving_with_GPS.php">Safer Driving</a> | <a href="GPS_helps_make_your_Business_more_Eco_Friendly.php">Eco-Friendly</a>
                
            </div>

            <div class="well well-sm"><a href="installationInstructions.php">Installation Instructions</a></div>

            <div class="well well-sm"><b>Industries:</b>&nbsp;<a href="GPSforLandscapingBusinesses.php">Landscaping/Lawncare Businesses</a> | <a href="Pest_Control_GPS.php">Pest Control</a> | <a href="Golf_Cart_GPS.php">Golf Cart Rentals</a><br>
                <a href="Taxi_Company_GPS.php">Taxi Companies</a> | <a href="Waste_Management_GPS.php">Waste Management</a> | <a href="Plumbing_Companies_GPS.php">Plumbing Companies</a> | <a href="GPS_for_Limo_Companies.php">Limo</a><br>
                <a href="Construction_Company_GPS.php">Construction GPS</a> | <a href="Trucking_Industry_GPS.php">Trucking Industry</a> | <a href="HVAC_GPS.php">HVAC Companies</a> | <a href="Law_Enforcement_GPS.php">Law Enforcement</a>
            </div>
            <a href="disclaimer.php">Terms of Use</a> - <a href="privacy.php">Privacy Policy</a><?php if (strtolower(DOMAIN_NAME) != "peakgps.com")
            {?> - <a href="https://PeakGPS.com">Peak GPS</a><?php } ?><br>
            <br>
        </td>
    </tr>
</table>
        
        <!-- Begin Constant Contact Active Forms -->
<script> var _ctct_m = "cb4d4d276a5745c9c6620da163ae29be"; </script>
<script id="signupScript" src="//static.ctctcdn.com/js/signup-form-widget/current/signup-form-widget.min.js" async defer></script>
<!-- End Constant Contact Active Forms -->

<?php }

function getHeading($lHeading, $bIcon = false, $lSeconds = 0)
{
    if ($lHeading <= 22.5)
    {
        $sReturn = "North";
    } elseif ($lHeading <= 67.5)
    {
        $sReturn = "NorthEast";
    } elseif ($lHeading <= 112.5)
    {
        $sReturn = "East";
    } elseif ($lHeading <= 157.5)
    {
        $sReturn = "SouthEast";
    } elseif ($lHeading <= 202.5)
    {
        $sReturn = "South";
    } elseif ($lHeading <= 247.5)
    {
        $sReturn = "SouthWest";
    } elseif ($lHeading <= 292.5)
    {
        $sReturn = "West";
    } elseif ($lHeading <= 337.5)
    {
        $sReturn = "NorthWest";
    } else
    {
        $sReturn = "North";
    }

    if ($bIcon == true)
    {
        if ($lSeconds >= STOPPED_TIME)
        {
            return "stopicon_smaller.png";
        } else
        {
            return "arrow".$sReturn.".png";
        }
    } else
    {
        $sReturn .= " (".$lHeading." Deg)";
    }

    return $sReturn;
}

function getRandomCharacter()
{
    //Numbers + UpperCase: Exclude zero and o.
    $lRand = rand(1, 34);

    $sReturn = "";
    if ($lRand <= 9)
    {
        $sReturn = $lRand;
    } elseif ($lRand == 10)
    {
        $sReturn = "A";
    } elseif ($lRand == 11)
    {
        $sReturn = "B";
    } elseif ($lRand == 12)
    {
        $sReturn = "C";
    } elseif ($lRand == 13)
    {
        $sReturn = "D";
    } elseif ($lRand == 14)
    {
        $sReturn = "E";
    } elseif ($lRand == 15)
    {
        $sReturn = "F";
    } elseif ($lRand == 16)
    {
        $sReturn = "G";
    } elseif ($lRand == 17)
    {
        $sReturn = "H";
    } elseif ($lRand == 18)
    {
        $sReturn = "I";
    } elseif ($lRand == 19)
    {
        $sReturn = "J";
    } elseif ($lRand == 20)
    {
        $sReturn = "K";
    } elseif ($lRand == 21)
    {
        $sReturn = "L";
    } elseif ($lRand == 22)
    {
        $sReturn = "M";
    } elseif ($lRand == 23)
    {
        $sReturn = "N";
    } elseif ($lRand == 24)
    {
        $sReturn = "P";
    } elseif ($lRand == 25)
    {
        $sReturn = "Q";
    } elseif ($lRand == 26)
    {
        $sReturn = "R";
    } elseif ($lRand == 27)
    {
        $sReturn = "S";
    } elseif ($lRand == 28)
    {
        $sReturn = "T";
    } elseif ($lRand == 29)
    {
        $sReturn = "U";
    } elseif ($lRand == 30)
    {
        $sReturn = "V";
    } elseif ($lRand == 31)
    {
        $sReturn = "W";
    } elseif ($lRand == 32)
    {
        $sReturn = "X";
    } elseif ($lRand == 33)
    {
        $sReturn = "Y";
    } elseif ($lRand == 34)
    {
        $sReturn = "Z";
    }

    return $sReturn;
}

function generate6DigitCode()
{
    $sReturn = "";
    for ($x = 1; $x <= 6; $x += 1)
    {
        $sReturn .= getRandomCharacter();
    }

    return $sReturn;
}

function generateKey()
{
    $sKey = uniqid();

    //$sRands = "";
    //for ($x = 1; $x <= 1000; $x += 1)
    //{
    //$sRands .= rand();
    //}

    $sRands = random_bytes(10000);  //10k bytes

    $sValueRAW = $sKey.$sRands;
    $sValue = sha1($sValueRAW);

    //echo "LEN=".strlen($sValueRAW)."\n";

    return $sValue;
}

function isSessionValid($sID)
{
    extract($GLOBALS);

    //1/12/18 - Keylinks
    $sLink = @$_REQUEST["sLink"];

    if ($sLink != "")
    {
        //KeyLink
        $sSQL = "SELECT COUNT(KEYsKey) as lCount FROM keylinks WHERE KEYbActive = 1 AND KEYsKey = ".SQLFormatField($sLink, "String");
        $result = getData($sSQL, $conn);

        $bValid = false;
        $lCount = 0;
        while ($row = getResultB($result))
        {
            $lCount = $row["lCount"];
        }

        if ($lCount == 1)
        {
            $bValid = true;
        }
    } else
    {
        //Standard Session
        /*
        $sSQL = "SELECT SESsKey FROM sessionids WHERE SESsKey = ".SQLFormatField($sID, "String");
        $result = getData($sSQL, $conn);

        $bValid = false;
        while ($row = getResultB($result))
        {
            $sKey = $row["SESsKey"];

            if ($sKey == $sID) $bValid = true;
        }
         *
         */

        $bExists = $oMemcached->get($sID);

        //echo "Values...";
        //print_r($bExists);
        //die;

        $bValid = false;
        if ($bExists != "")
        {
            $bValid = true;
        }
    }

    return $bValid;
}

function getUserValues($sID)
{
    extract($GLOBALS);

    //1/12/18 - Keylinks
    $sLink = @$_REQUEST["sLink"];

    $arrData = array();
    if ($sLink != "")
    {
        //KeyLink
        $sSQL = "SELECT * 
        FROM keylinks 
        JOIN companies ON COMnID = KEYnCompanyID
        JOIN timezones ON TZOnID = COMnTimeZone
        WHERE KEYbActive = 1 
        AND KEYsKey = ".SQLFormatField($sLink, "String");
        $result = getData($sSQL, $conn);

        while ($row = getResultB($result))
        {
            $arrData["UserID"] = "";    //Not used on Map
            $arrData["CompanyID"] = $row["KEYnCompanyID"];
            $arrData["AdminUser"] = 0;
            $arrData["TimeZone"] = $row["TZOsValue"];
        }
    } else
    {
        $arrData = $oMemcached->get($sID);
    }

    return $arrData;
}

function setTimeZone($sDate, $sFrom, $sTo)
{
    $dDate = new DateTime($sDate, new DateTimeZone($sFrom));
    $dDate->setTimezone(new DateTimeZone($sTo));

    return $dDate->format("Y-m-d H:i:s");
}

function updateSession($sID)
{
    extract($GLOBALS);

    //6/22/18 - Too much disk activity to update sessions continously - Set to X days and be done

    /*
        $sSQL = "UPDATE sessionids SET SESdExpires = '".DateAdd("h", strtotime(date("Y-m-d H:i:s")), 1)."' WHERE SESsKey = ".SQLFormatField($sID, "String");
        if (!$result = getData($sSQL, $conn))
        {
        echo "ERROR: ".$sSQL."<br>".getError($conn);
        }
     *
     */
}

function closeSession($sKey)
{
    extract($GLOBALS);

    $sSQL = "SELECT SESnHistoryID FROM sessionids WHERE SESsKey = ".SQLFormatField($sKey, "String");
    $result = getData($sSQL, $conn);

    $lHistoryID = "";
    while ($row = getResultB($result))
    {
        $lHistoryID = $row["SESnHistoryID"];
    }

    $sSQL = "DELETE FROM sessionids WHERE SESsKey = ".SQLFormatField($sKey, "String");
    if (!$result = getData($sSQL, $conn))
    {
        echo "ERROR: ".$sSQL."<br>".getError($conn);
        die;
    }

    if ($lHistoryID != "")
    {
        $sSQL = "UPDATE sessionhistory SET SHSdEnded = '".date("Y-m-d H:i:s")."' WHERE SHSnID = ".$lHistoryID;
        if (!$result = getData($sSQL, $conn))
        {
            echo "ERROR: ".$sSQL."<br>".getError($conn);
            die;
        }
    }
}

function generateSession($lUserID)
{
    extract($GLOBALS);

    $sID = generateKey();

    $sSQL = "SELECT * FROM users
            JOIN companies on COMnID = USRnCompanyID
            JOIN timezones ON TZOnID = COMnTimeZone        
            WHERE USRnID = ".$lUserID;
    $result = getData($sSQL, $conn);

    $lCompanyID = -1;
    $bAdmin = 0;
    $sTimeZone = "";
    $sKey = "";
    $arrUser = array();
    while ($row = getResultB($result))
    {
        $lCompanyID = $row["USRnCompanyID"];
        $bAdminUser = $row["USRbAdmin"];
        $sTimeZone = $row["TZOsValue"];
        $bDemo = $row["USRbDemo"];
        $sKey = $row["USRsKey"];
        $bAxle = $row["COMbAxle"];
        $bKPH = $row["COMbKPH"];
        $bStarter = $row["COMbStarter"];

        $arrUser["UserID"] = $lUserID;
        $arrUser["CompanyID"] = $lCompanyID;
        $arrUser["AdminUser"] = $bAdminUser;
        $arrUser["TimeZone"] = $sTimeZone;
        $arrUser["Demo"] = $bDemo;
        $arrUser["Key"] = $sKey;
        $arrUser["Axle"] = $bAxle;
        $arrUser["KPH"] = $bKPH;
        $arrUser["Starter"] = $bStarter;
    }

    //    print_r($arrUser);
    //    die;

    $sSQL = "SELECT IFNULL(MAX(SHSnID), 0) + 1 AS lID FROM sessionhistory";
    $result = getData($sSQL, $conn);

    while ($row = getResultB($result))
    {
        $lHistoryID = $row["lID"];
    }

    $sSQL = "INSERT INTO sessionhistory (SHSnID, SHSdCreated, SHSnUserID, SHSdEnded) ".
            "VALUES (".$lHistoryID.
            ", '".date("Y-m-d H:i:s")."' ".
            ", ".$lUserID.
            ", NULL) ";
    if (!$result = getData($sSQL, $conn))
    {
        echo "ERROR: ".$sSQL."<br>".getError($conn);
        die;
    }


    $sSQL = "INSERT INTO sessionids (SESsKey, SESnUserID, SESnCompanyID, SESdExpires, SESnHistoryID) ".
        " VALUES (".SQLFormatField($sID, "String").
        ", ".$lUserID.
        ", ".$lCompanyID.
        ", '".DateAdd("h", strtotime(date("Y-m-d H:i:s")), 1)."' ".
            ", ".$lHistoryID.
        ") ";
    if (!$result = getData($sSQL, $conn))
    {
        echo "ERROR: ".$sSQL."<br>".getError($conn);
        die;
    }

    $oMemcached->set($sID, $arrUser, SESSION_EXPIRE);

    return $sID;
}

function displayViewPort()
{
    ?>
<meta content='width=device-width, initial-scale=0.5, maximum-scale=1.0, user-scalable=0' name='viewport' />
<?php if (1 == 2)
{ ?>
<meta name="viewport" content="width=device-width" />
<?php } ?>
<style>
html, body {
  overflow-x: hidden;
}
body {
  position: relative
}    
</style>
    <?php
}

function getHeader($sTitle, $bOnLoad = false)
{
    extract($GLOBALS);

    $sID = @$_REQUEST["sID"];
    $sKey = @$_REQUEST["res"];

    $bAdminUser = false;
    if ($sID != "")
    {
        $arrUser = getUserValues($sID);
        $bAdminUser = $arrUser["AdminUser"];
    }

    $lCompanyID = @$_REQUEST["lCompanyID"];
    if ($bAdminUser == false && $sID != "")
    {
        $lCompanyID = $arrUser["CompanyID"];
    }

    $sTableWidth = "660";
    if ($bAdminUser == true)
    {
        $sTableWidth = "800";
    }

    if ($sTitle == "Disclaimer")
    {
        $sTableWidth = "1024";
    }

    $sLogo = LOGO_PATH;
    if ($sKey != "")
    {
        $sSQL = "SELECT * FROM companies WHERE COMsKey = '".$sKey."' ";
        $result = getData($sSQL, $conn);

        while ($row = getResultB($result))
        {
            $sCurrentLogo = $row["COMsLogo"];

            if ($sCurrentLogo != "")
            {
                $sLogo = "/images/".$sCurrentLogo;
            }
        }
    } elseif ($lCompanyID != "")
    {
        $sSQL = "SELECT * FROM companies WHERE COMnID = ".$lCompanyID;
        $result = getData($sSQL, $conn);

        while ($row = getResultB($result))
        {
            $sCurrentLogo = $row["COMsLogo"];

            if ($sCurrentLogo != "")
            {
                $sLogo = "/images/".$sCurrentLogo;
            }
        }

    }

    ?>
<head>
    <title><?php echo PRODUCT_NAME?> - <?php echo $sTitle?></title>
    <link rel="stylesheet" href="/bootstrap/css/bootstrapMOD.css">
    <?php if ($sTitle == "Login")
    { ?>
    <meta content='width=device-width, initial-scale=0.70, maximum-scale=1.0, user-scalable=0' name='viewport' />
    <?php } else
    { ?>
    <meta content='width=device-width, initial-scale=0.55, maximum-scale=1.0, user-scalable=0' name='viewport' />
    <?php } ?>
        
    <script <?php if (1 == 2 && $bIsMobile)
    {
        echo " async ";
    }?>src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script <?php if (1 == 2 && $bIsMobile)
    {
        echo " async ";
    }?>src="/bootstrap/js/bootstrap.min.js"></script>    
    
    <style>
	    a:link { text-decoration: none; } a:visited { text-decoration: none; } 
	    body 
	    {
		background: silver;
	    }
    </style>
</head>

<body<?php if ($bOnLoad)
{
    echo " onload=formLoad()";
}?>>
<center>
<table border="0" width="<?php echo $sTableWidth?>" style="background-color:white">
    <tr>        
        <td align="center"<?php if (DOMAIN_NAME == "repogps.com")
        {?> bgcolor="black"<?php } ?>><a href="https://<?php echo DOMAIN_NAME?>"><img src="<?php echo $sLogo?>" border="0"></a><br><br></td>
    </tr>
	    
    <tr>
        <td align="<?php if ($sTitle == "Disclaimer" || $sTitle == "Forgot Password")
        {
            echo "left";
        } else
        {
            echo "center";
        }?>">
<?php
}

function getHeaderV4($sTitle, $bOnLoad = false)
{
    extract($GLOBALS);

    $sID = @$_REQUEST["sID"];

    $bAdminUser = false;
    if ($sID != "")
    {
        $arrUser = getUserValues($sID);
        $bAdminUser = $arrUser["AdminUser"];
    }

    $sTableWidth = "100%";
    ?>
<head>
    <title><?php echo PRODUCT_NAME?> - <?php echo $sTitle?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">    
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>    
    <meta content='width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=0' name='viewport' />
</head>

<?php if (1 == 2)
{ ?>
<body<?php if ($bOnLoad)
{
    echo " onload=formLoad()";
}?>>
<center>
<table border="0" width="<?php echo $sTableWidth?>" style="background-color:white">
    <tr>        
        <td align="center"<?php if (DOMAIN_NAME == "repogps.com")
        {?> bgcolor="black"<?php } ?>><a href="https://<?php echo DOMAIN_NAME?>"><img src="<?php echo LOGO_PATH?>" border="0"></a><br><br></td>
    </tr>
	    
    <tr>
        <td align="<?php if ($sTitle == "Disclaimer" || $sTitle == "Forgot Password")
        {
            echo "left";
        } else
        {
            echo "center";
        }?>">
<?php
}
}

function getMenu($sPageName)
{
    extract($GLOBALS);

    $sID = $_REQUEST["sID"];
    $arrUser = getUserValues($sID);

    $lUserID = $arrUser["UserID"];
    $lCompanyID = $arrUser["CompanyID"];
    $bAdminUser = $arrUser["AdminUser"];

    $sFontSize = 3;
    if ($bIsMobile == false)
    {
        $sFontSize = 2;
    }
    ?>
    <table border="0" cellspacing="0" cellpadding="0">    
	<tr>
	    <td align="center">
		<nav class="navbar navbar-default">
		  <div class="container-fluid">
		    <ul class="nav navbar-nav">
		      <li<?php if ($sPageName == "Live Map")
		      {?> class="active"<?php } ?>><a href="map.php?sID=<?php echo $_REQUEST["sID"]?>"><font size="<?php echo $sFontSize?>">Live Map</font></a></li>
		      <li<?php if ($sPageName == "History")
		      {?> class="active"<?php } ?>><a href="map.php?sID=<?php echo $_REQUEST["sID"]?>&bHistory=1"><font size="<?php echo $sFontSize?>">History</font></a></li>
		      <li<?php if ($sPageName == "Units")
		      {?> class="active"<?php } ?>><a href="editUnits.php?sID=<?php echo $_REQUEST["sID"]?>"><font size="<?php echo $sFontSize?>">Units</font></a></li>
		      <li<?php if ($sPageName == "Alerts")
		      {?> class="active"<?php } ?>><a href="editSpeedAlerts.php?sID=<?php echo $_REQUEST["sID"]?>"><font size="<?php echo $sFontSize?>">Alerts</font></a></li>
		      <li<?php if ($sPageName == "Places")
		      {?> class="active"<?php } ?>><a href="editPlace.php?sID=<?php echo $_REQUEST["sID"]?>"><font size="<?php echo $sFontSize?>">Places</font></a></li>
		      <li<?php if ($sPageName == "Reports")
		      {?> class="active"<?php } ?>><a href="reports.php?sID=<?php echo $_REQUEST["sID"]?>"><font size="<?php echo $sFontSize?>">Reports</font></a></li>
                      <li<?php if ($sPageName == "Share Links")
                      {?> class="active"<?php } ?>><a href="editShareLinks.php?sID=<?php echo $_REQUEST["sID"]?>"><font size="<?php echo $sFontSize?>">Share<br>Links</font></a></li>
		      <?php if ($bAdminUser)
		      { ?><li<?php if ($sPageName == "Users")
		      {?> class="active"<?php } ?>><a href="adminUsersEdit.php?sID=<?php echo $_REQUEST["sID"]?>"><font size="<?php echo $sFontSize?>">Users</font></a></li><?php } ?>
		      <?php if ($bAdminUser)
		      { ?><li<?php if ($sPageName == "Companies")
		      {?> class="active"<?php } ?>><a href="adminCompaniesEdit.php?sID=<?php echo $_REQUEST["sID"]?>"><font size="<?php echo $sFontSize?>">Companies</font></a></li><?php } ?>
		      <li><a href="logout.php?sID=<?php echo $_REQUEST["sID"]?>"><font size="<?php echo $sFontSize?>">Logout</font></a></li>
		    </ul>
		  </div>
		</nav> </td>
	</tr>
    </table><br>
<?php }

function emailRegistration($sKey, $sEmail)
{
    $sURL = "https://".DOMAIN_NAME."/app/register.php?sKey=".$sKey;

    $sTo = $sEmail;
    $sFrom = FROM_EMAIL;
    $sTitle = PRODUCT_NAME." Registration";
    $sBody = "Welcome to ".PRODUCT_NAME."\n\n".
        "In order to get a Login to the website, please follow the following link:\n\n".
        $sURL."\n\n".
        "Thank you,\n".
        PRODUCT_NAME;
    SendEmail($sTo, $sFrom, $sTitle, $sBody);
}

function emailForgotPassword($sKey, $sEmail)
{
    $sURL = "http://".DOMAIN_NAME."/app/forgotPasswordB.php?sKey=".$sKey;

    $sTo = $sEmail;
    $sFrom = FROM_EMAIL;
    $sTitle = PRODUCT_NAME." Forgot Password";
    $sBody = "You have requested a Password Reset for ".PRODUCT_NAME."\n\n".
        "In order to Reset your Password, please follow the following link:\n\n".
        $sURL."\n\n".
        "Thank you,\n".
        PRODUCT_NAME;
    SendEmail($sTo, $sFrom, $sTitle, $sBody);
}


function displayAlertHeader($sField)
{
    $sID = @$_REQUEST["sID"];

    if (@$_REQUEST["lID"] == "")
    {
        ?>
    <tr>
	<td colspan="999">
	    <table width="100%" border="0">
		<tr>
		    <td align="center"><button type="button" class="btn btn-primary<?php if ($sField == "GeoFence")
		    {
		        echo " active";
		    }?>" onclick="window.location='editGeofenceAlerts.php?sID=<?php echo $sID?>'"><span class="glyphicon glyphicon-retweet">&nbsp;</span>GeoFence</button></td>
		    <td width='5'>&nbsp;</td>
		    <td align="center"><button type="button" class="btn btn-primary<?php if ($sField == "Ignition")
		    {
		        echo " active";
		    }?>" onclick="window.location='editIgnitionAlerts.php?sID=<?php echo $sID?>'"><span class="glyphicon glyphicon-cog">&nbsp;</span>Ignition</button></td>
		    <td width='5'>&nbsp;</td>
		    <td align="center"><button type="button" class="btn btn-primary<?php if ($sField == "Place")
		    {
		        echo " active";
		    }?>" onclick="window.location='editPlaceAlerts.php?sID=<?php echo $sID?>'"><span class="glyphicon glyphicon-home">&nbsp;</span>Place</button></td>
		    <td width='5'>&nbsp;</td>
		    <td align="center"><button type="button" class="btn btn-primary<?php if ($sField == "Speed")
		    {
		        echo " active";
		    }?>" onclick="window.location='editSpeedAlerts.php?sID=<?php echo $sID?>'"><span class="glyphicon glyphicon-road">&nbsp;</span>Speed</button></td>
		    <td width='5'>&nbsp;</td>
		    <td align="center"><button type="button" class="btn btn-primary<?php if ($sField == "Stopped")
		    {
		        echo " active";
		    }?>" onclick="window.location='editStoppedAlerts.php?sID=<?php echo $sID?>'"><span class="glyphicon glyphicon-alert">&nbsp;</span>Stopped</button></td>
		    <td width='5'>&nbsp;</td>
		    <td align="center"><button type="button" class="btn btn-primary<?php if ($sField == "Battery")
		    {
		        echo " active";
		    }?>" onclick="window.location='editBatteryAlerts.php?sID=<?php echo $sID?>'"><span class="glyphicon glyphicon-flash">&nbsp;</span>Battery</button></td>
		</tr>
	    </table></td>
    </tr>

    <tr>
	<td>&nbsp;</td>
    </tr>
<?php }
    }

function getState($pLatitude, $pLongitude)
{
    extract($GLOBALS);

    $sSQL = "SELECT SHPsState FROM shapefiles WHERE ST_Contains(SHPsShape, ST_GeomFromText('POINT(".$pLongitude." ".$pLatitude.")', 1))";
    $result = getData($sSQL, $conn);

    $sState = "";
    while ($row = getResultB($result))
    {
        $sState = $row["SHPsState"];
    }

    return $sState;
}

function isBatteryUnit($sESN)
{
    if (strlen($sESN) == 8)
    {
        return true;
    } else
    {
        return false;
    }
}

function getWarningErrors()
{
    ?>
    <?php if (@$_REQUEST["sError"] != "")
    { ?>
    <tr>
	<td align="center" colspan="9"><div class="alert alert-danger"><?php echo $_REQUEST["sError"]?></div></td>
    </tr>

    <tr>
	<td>&nbsp;</td>
    </tr>
    <?php } elseif (@$_REQUEST["sMessage"] != "")
    {   ?>
    <tr>
	<td align="center" colspan="9"><div class="alert alert-success"><?php echo $_REQUEST["sMessage"]?></div></td>
    </tr>

    <tr>
	<td>&nbsp;</td>
    </tr>
    <?php } ?>    
<?php }

function adminCompanyDropdown()
{
    extract($GLOBALS);

    $sID = $_REQUEST["sID"];
    $arrUser = getUserValues($sID);

    $bAdminUser = $arrUser["AdminUser"];
    $lCompanyID = @$_REQUEST["lCompanyID"];
    $sPageName = $_SERVER["SCRIPT_NAME"];

    if ($bAdminUser)
    {
        ?>
    <tr>
	<td align="right"><b>Company:</b></td>
	<td align="left">
	    <SELECT name="lCompanyID" class="form-control" onchange="window.location='<?php echo $sPageName?>?sID=<?php echo $sID?>&lCompanyID=' + document.frmMain.lCompanyID.value">
		<OPTION value="">- Select Company - </OPTION>
		<?php
            $sSQL = "SELECT * FROM companies WHERE COMbActive = 1 AND COMbCorporate = 0 ORDER BY COMsName";
        $result = getData($sSQL, $conn);

        while ($row = getResultB($result))
        {
            $lCurrentCompanyID = $row["COMnID"];
            $sCurrentName = $row["COMsName"];

            $sSelected = "";
            if ($lCurrentCompanyID == $lCompanyID)
            {
                $sSelected = " SELECTED";
            }
            ?>
		<OPTION value="<?php echo $lCurrentCompanyID?>"<?php echo $sSelected?>><?php echo $sCurrentName?></OPTION>
		<?php } ?>
	    </SELECT>
	</td>
    </tr>    
    <?php
    }
}

function getYear($sVIN)
{
    $s9th = substr($sVIN, 9, 1);

    $sReturn = "";
    if (substr($sVIN, 0, 5) == "1N4BZ")
    {
        //1N4BZ0CP5HC307851 = 2017  (Hardcoded for now)
        if ($s9th == "H")
        {
            $sReturn = "2017";
        } else
        {
            $sReturn = "2016";
        }
    } elseif (substr($sVIN, 0, 8) == "JN1AZ0CP")
    {
        if ($s9th == "B")
        {
            $sReturn = "2011";
        } elseif ($s9th == "C")
        {
            $sReturn = "2012";
        }
    } elseif (substr($sVIN, 0, 8) == "1N4AZ0CP")
    {
        if ($s9th == "D")
        {
            $sReturn = "2013";
        } elseif ($s9th == "E")
        {
            $sReturn = "2014";
        } elseif ($s9th == "F")
        {
            $sReturn = "2015";
        } elseif ($s9th == "G")
        {
            $sReturn = "2016";
        }
    }

    return $sReturn;
}

function getBaseline($lYear)
{
    $pBaseline = AHR_CAPACITY_V1;
    if ($lYear == 2013 || $lYear == 2014 || $lYear == 2015)
    {
        $pBaseline = AHR_CAPACITY_V2;
    }
    if ($lYear >= 2016)
    {
        $pBaseline = AHR_CAPACITY_V3;
    }

    return $pBaseline;
}

function getLoss($pAmpHr, $lYear)
{
    $pBaseline = getBaseline($lYear);

    $sValue = number_format((($pAmpHr - $pBaseline) / $pBaseline * 100), 2);

    if ($sValue < 0)
    {
        ?>
        <font color="red">(<?php echo $sValue?>%)</font>
        <?php
    } else
    {
        ?>
        <font color="green"><?php echo $sValue?>%</font>
        <?php
    }
}

function XMLit($sInput)
{
    $sInput = trim($sInput);

    //11/21/18 - Causing issues
    //$sInput = str_replace('&', '&#038', $sInput);
    $sInput = str_replace('&', '', $sInput);

    $sOutput = $sInput;
    if ($sInput == "")
    {
        $sOutput = "-";
    }

    return $sOutput;
}

function getAddressInfoWindow($bShowAddress, $pLatitude, $pLongitude, $sID, $sESN)
{
    if ($bShowAddress != "")
    { ?>
        <font size="2"><?php echo geocodeLatLong($pLatitude, $pLongitude)?></font>
    <?php
    } else
    {
        ?>
        <a href="infoWindow.php?sID=<?php echo $sID?>&sESN=<?php echo $sESN?>&dDate=<?php echo @$_REQUEST["dDate"]?>&bShowAddress=1">Show Address</a>
    <?php }
    }

function disableDemo()
{
    extract($GLOBALS);

    $sReturn = "";

    if (@$arrUser["Demo"] == 1)
    {
        $sReturn = " disabled";
    }

    return $sReturn;
}

function KPHtoMPH($kmph)
{
    return number_format((0.62137119224 * $kmph), 0);
}

function MPHtoKPH($mph)
{
    return number_format(($mph * 1.60934), 0);
}

function importLedgerX($sFileName, $sBaseFileName)
{
    $connTRADING = getConnectionTRADING("crypto");

    $handle = fopen($sFileName, "r");
    $sContents = fread($handle, filesize($sFileName));
    fclose($handle);

    $sDate = str_replace(".csv", "", $sBaseFileName);
    $dDate = str_replace("eod-report-", "", $sDate);

    $sSQL = "REPLACE INTO optionhistory (OPHdDate, OPHdExpiration, OPHnStrike, OPHsType, OPHnBid, OPHnAsk, OPHnVolume, OPHnOpenInterest, OPHnAvgPrice) VALUES ";

    $dCurrent = $dDate;

    $arrRow = explode(chr(10), $sContents);

    $lRowCount = 0;
    foreach ($arrRow as $sRecord)
    {
        //"contract","last_bid","last_ask","block_volume","volume","open_interest","vwap","contract_type"
        //"2020-08-27 Next-Day cBTC","$10,899.00","$11,359.00","---","562","315","$11,417.53","Day ahead swap"
        //"cBTC 2020-08-28 Call $12,500.00","$3.00","$12.00","---","1037","1126","$9.44","Options contract"

        $sRecord = strtolower($sRecord);
        $sRecord = str_replace("cbtc ", "", $sRecord);
        $sRecord = str_replace("btc ", "", $sRecord);
        $sRecord = str_replace("$", "", $sRecord);
        $sRecord = str_replace(".00", "", $sRecord);
        $arrRecord = str_getcsv($sRecord, ",");

        $sContract = $arrRecord[0];

        if (strpos($sContract, "call") > 0 || strpos($sContract, "put") > 0)
        {
            $lRowCount += 1;

            $arrValues = explode(" ", $sContract);

            $dExpiration = $arrValues[0];
            $sType = $arrValues[1];
            $lStrike = str_replace(",", "", $arrValues[2]);

            $pBid = str_replace(",", "", $arrRecord[1]);
            $pAsk = str_replace(",", "", $arrRecord[2]);
            //block_volume
            $lVolume = $arrRecord[4];
            $lOpenInterest = $arrRecord[5];
            $pAvg = $arrRecord[6];
            //Type

            //echo "Bid=".$pBid." : ".$pAsk."<br>";

            if (is_numeric($pAvg) == false)
            {
                $pAvg = "NULL";
            }

            if (is_numeric($pBid) && is_numeric($pAsk))
            {
                if ($lRowCount > 1)
                {
                    $sSQL .= ", ";
                }
                $sSQL .= "('".date("Y-m-d", strtotime($dCurrent))."' ".
                        ", '".date("Y-m-d", strtotime($dExpiration))."' ".
                        ", ".$lStrike.
                        ", '".$sType."' ".
                        ", ".str_replace(",", "", $pBid).
                        ", ".str_replace(",", "", $pAsk).
                        ", ".str_replace(",", "", $lVolume).
                        ", ".$lOpenInterest.
                        ", ".$pAvg.")\n";
            }
        } //END: Valid Row
    } //END: Each Row

    //echo $sSQL; die;

    if (!$result = getData($sSQL, $connTRADING))
    {
        echo "ERROR: ".str_replace("\n", "\n<br>", $sSQL)."<br><br><br>".getError($connTRADING);
        die;
    }

}

function createCMRANKINGStable($sTableName)
{
    $connTRADING = getConnectionTRADING("crypto");

    $sSQL = "CREATE TABLE $sTableName
        (
        CMRdDate 	DATETIME,
        CMRnSymbolID	MEDIUMINT	UNSIGNED,
        CMRnRanking	SMALLINT	UNSIGNED,
        CMRn1H		SMALLINT	SIGNED,
        CMRn24H		DECIMAL(12,2),
        CMRn7D		DECIMAL(12,2),
        CMRn14D		DECIMAL(12,2),
        CMRn30D		DECIMAL(12,2),
        CMRn7DH		MEDIUMINT	UNSIGNED,
        CMRn7DL		MEDIUMINT	UNSIGNED,
        CMRn30DH		MEDIUMINT	UNSIGNED,
        CMRn30DL		MEDIUMINT	UNSIGNED,
        CMRn60DH		MEDIUMINT	UNSIGNED,
        CMRn60DL		MEDIUMINT	UNSIGNED,
        CMRn90DH		MEDIUMINT	UNSIGNED,
        CMRn90DL		MEDIUMINT	UNSIGNED,
        CMRn120DH		MEDIUMINT	UNSIGNED,
        CMRn120DL		MEDIUMINT	UNSIGNED,
        CMRn180DH		MEDIUMINT	UNSIGNED,
        CMRn180DL		MEDIUMINT	UNSIGNED,
        PRIMARY KEY (CMRdDate, CMRnSymbolID),
        INDEX IDX_SymbolDate (CMRnSymbolID, CMRdDate),
        INDEX IDX_7DL (CMRn7DL),
        INDEX IDX_7DH (CMRn7DH),
        INDEX IDX_30DL (CMRn30DL),
        INDEX IDX_30DH (CMRn30DH),
        INDEX IDX_60DL (CMRn60DL),
        INDEX IDX_60DH (CMRn60DH),
        INDEX IDX_90DL (CMRn90DL),
        INDEX IDX_90DH (CMRn90DH),
        INDEX IDX_120DL (CMRn120DL),
        INDEX IDX_120DH (CMRn120DH),
        INDEX IDX_180DL (CMRn180DL),
        INDEX IDX_180DH (CMRn180DH)
        )";

    $bReturn = true;
    if (!$result = getData($sSQL, $connTRADING))
    {
        echo "ERROR: ".$sSQL."\n".mysqli_error($connTRADING);
        $bReturn = false;
    }

    mysqli_close($connTRADING);

    return $bReturn;
}

function tableExists($sTableName)
{
    $connTRADING = getConnectionTRADING("crypto");

    $sSQL = "SELECT COUNT(*) AS lCount FROM information_schema.TABLES WHERE TABLE_NAME = '".$sTableName."'";
    $result = getData($sSQL, $connTRADING);

    $lCount = "";
    while ($row = getResultB($result))
    {
        $lCount = $row["lCount"];
    }

    mysqli_close($connTRADING);

    return $lCount;
}

function calculateMACD($arrData, $points)
{
    $emaShort = [];
    $emaLong = [];
    $macdLine = [];
    $signalLine = [];

    // Extract dates and prices into separate arrays
    $dates = array_keys($arrData);
    $prices = array_values($arrData);

    // Calculate EMA for short-term and long-term periods
    $emaShort[0] = @$prices[0];
    $emaLong[0] = @$prices[0];
    for ($i = 1; $i < count($prices); $i++)
    {
        $emaShort[$i] = (2 / ($points + 1)) * $prices[$i] + (1 - (2 / ($points + 1))) * $emaShort[$i - 1];
        $emaLong[$i] = (2 / (2 * $points + 1)) * $prices[$i] + (1 - (2 / (2 * $points + 1))) * $emaLong[$i - 1];
    }

    // Calculate MACD Line
    for ($i = 0; $i < count($prices); $i++)
    {
        $macdLine[$dates[$i]] = $emaShort[$i] - $emaLong[$i];
    }

    // Calculate Signal Line (9-day EMA of MACD Line)
    $signalLine[8] = array_sum(array_slice($macdLine, 0, 9)) / 9;
    for ($i = 9; $i < count($prices); $i++)
    {
        $signalLine[$dates[$i]] = (2 / (10)) * @$macdLine[$dates[$i]] + (1 - (2 / (10))) * @$signalLine[$dates[$i - 1]];
    }

    return ['MACD' => $macdLine, 'SignalLine' => $signalLine];
}

function calculateBollingerBands($arrData, $period = 20, $multiplier = 2)
{
    $bollingerBands = array();

    $prices = array_values($arrData);
    $totalPrices = count($prices);

    for ($i = $period - 1; $i < $totalPrices; $i++)
    {
        $sma = array_sum(array_slice($prices, $i - $period + 1, $period)) / $period;
        $stdDev = 0;

        // Calculate standard deviation
        for ($j = $i - $period + 1; $j <= $i; $j++)
        {
            $stdDev += pow($prices[$j] - $sma, 2);
        }
        $stdDev = sqrt($stdDev / $period);

        // Upper and lower Bollinger Bands
        $upperBand = $sma + ($multiplier * $stdDev);
        $lowerBand = $sma - ($multiplier * $stdDev);

        // Date corresponding to the current price
        $date = array_keys($arrData)[$i];

        // Store the calculated bands for the current date
        $bollingerBands[$date] = array(
            'SMA' => $sma,
            'UpperBand' => $upperBand,
            'LowerBand' => $lowerBand
        );
    }

    return $bollingerBands;
}


function calculateExponentialMovingAverage($arrData, $period = 5, $bReturnAll = false, $lSymbolID = "")
{
    $total = count($arrData);

    if ($total < $period)
    {
        return false; // Not enough data points
    }

    $emaValues = [];

    $dates = array_keys($arrData);
    $values = array_values($arrData);

    // Calculate the smoothing factor
    $alpha = 2 / ($period + 1);

    // Calculate initial SMA as the sum of the first 'period' values divided by 'period'
    $sma = array_sum(array_slice($values, 0, $period)) / $period;
    $emaValues[$dates[$period - 1]] = $sma;

    // Calculate EMA for the remaining data points
    for ($i = $period; $i < $total; $i++)
    {
        $ema = ($values[$i] - $emaValues[$dates[$i - 1]]) * $alpha + $emaValues[$dates[$i - 1]];
        $emaValues[$dates[$i]] = $ema;
    }

    if ($bReturnAll)
    {
        return $emaValues;
    } else
    {
        return $pLatest;
    }

    return $emaValues;
}

function calculateMovingAverage($data, $period = 5, $bReturnAll = false, $lSymbolID = "")
{
    $total = count($data);
    if ($total < $period)
    {
        return false; // Not enough data points
    }

    $movingAverages = [];
    foreach ($data as $date => $value)
    {
        $movingAverages[$date] = null;
    }

    $dates = array_keys($data);

    for ($i = $period - 1; $i < $total; $i++)
    {
        $sum = 0;
        for ($j = 0; $j < $period; $j++)
        {
            $sum += $data[$dates[$i - $j]];
        }

        $pLatest = $sum / $period;
        $movingAverages[$dates[$i]] = $pLatest;
    }

    if ($bReturnAll)
    {
        return $movingAverages;
    } else
    {

        return $pLatest;
    }
}

function getMoralisWallet($sAddress)
{
    $wallet_address = $sAddress;
    $params = [
    'chains' => ['eth','polygon','base','bsc','arbitrum'],
    'exclude_spam' => 'true',
    'exclude_unverified_contracts' => 'true',
    ];
    $query = http_build_query($params, '', '&', PHP_QUERY_RFC3986);

    $url = "https://deep-index.moralis.io/api/v2.2/wallets/{$wallet_address}/net-worth?$query";

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => [
            "accept: application/json",
            "X-API-Key: " . MORALIS_KEY
        ],
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    if ($err)
    {
        echo "cURL Error #:" . $err;
        //continue;
    }

    $arrData = json_decode($response, true);

    // Add a small delay to avoid rate limiting
    usleep(100000); // 100ms delay
    //}

    // Add total across all chains
    return $arrData["total_networth_usd"];
}

function isBaseSymbol($sSymbol1, $sSymbol2, $lPosition)
{
    // Define base symbols in order of priority
    // Higher priority symbols are considered "more base" than lower priority ones
    $baseSymbols = [
        // USD-based stablecoins (highest priority)
        'USDC' => 10,
        'USDT' => 9,
        'DAI' => 8,
        'BUSD' => 7,
        'TUSD' => 6,
        'FRAX' => 5,
        'LUSD' => 4,

        // ETH variants (medium priority)
        'ETH' => 3,
        'WETH' => 3,

        // Other major base currencies (lower priority)
        'BTC' => 2,
        'WBTC' => 2,
    ];

    // Convert symbols to uppercase for case-insensitive comparison
    $symbol1 = strtoupper(trim($sSymbol1));
    $symbol2 = strtoupper(trim($sSymbol2));

    // Validate position parameter
    if ($lPosition !== 1 && $lPosition !== 2)
    {
        throw new InvalidArgumentException('Position must be either 1 or 2');
    }

    // Get the symbol at the specified position
    $targetSymbol = ($lPosition === 1) ? $symbol1 : $symbol2;
    $otherSymbol = ($lPosition === 1) ? $symbol2 : $symbol1;

    // Check if target symbol is a base symbol
    $isTargetBase = isset($baseSymbols[$targetSymbol]);
    $isOtherBase = isset($baseSymbols[$otherSymbol]);

    // If target is not a base symbol, return false
    if (!$isTargetBase)
    {
        return false;
    }

    // If other symbol is not a base symbol, target is definitely the base
    if (!$isOtherBase)
    {
        return true;
    }

    // Both are base symbols, return true if target has higher or equal priority
    return $baseSymbols[$targetSymbol] >= $baseSymbols[$otherSymbol];
}

function getBaseSymbol($sSymbol1, $sSymbol2)
{
    if (isBaseSymbol($sSymbol1, $sSymbol2, 1))
    {
        return strtoupper(trim($sSymbol1));
    } elseif (isBaseSymbol($sSymbol1, $sSymbol2, 2))
    {
        return strtoupper(trim($sSymbol2));
    }
    return null;
}

function cryptoMenu()
{
    ?>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="index.php">Home</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item active">
                    <a class="nav-link" href="chart.php">Chart</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="sim.php">Sim</a>
                </li>
            </ul>
        </div>
    </nav>                
<?php } ?>
