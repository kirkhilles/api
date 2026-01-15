<?php

ini_set("display_errors", 1);

require_once("app_functions.php");

define("LLM_LLAMA_8B", "meta-llama/llama-3.1-8b-instruct");  //0.02/0.03
define("LLM_LLAMA_70B", "deepseek/deepseek-r1-distill-llama-70b"); //0.03/0.11
define("LLM_GPT_OSS_20B", "openai/gpt-oss-20b"); //0.03/0.14
define("LLM_GPT_OSS_120B", "openai/gpt-oss-120b:exacto"); //0.04/0.19

define("LLM_MODEL_LOW", LLM_LLAMA_8B);
define("LLM_MODEL_MEDIUM", LLM_LLAMA_70B);
define("LLM_MODEL_HIGH", LLM_GPT_OSS_120B);
define("LLM_MODEL_EMBEDDING", "openai/text-embedding-3-small");

$arrFields = array();
$sLogText = "";
for ($y = 0; $y <= 10; $y += 1)
{
    if (@$_REQUEST["x".$y] != "")
    {
        $arrFields[$y] = $_REQUEST["x".$y];

        $sLogText .= "x".$y."=".$_REQUEST["x".$y]."; ";
    }
}

writeAPIlog($sLogText);
//print_r($arrFields);

$arrOutput = array();
// ------------------------------------------------------------------------------------
// LLM
if ($arrFields[0] == "LLM")
{
    $arrOutput = getLLM($arrFields);
}
// ------------------------------------------------------------------------------------
// VECTOR
elseif ($arrFields[0] == "Vector")
{
    // Exists
    if ($arrFields[1] == "Exists")
    {
        $sCollection = $arrFields[2];
        $sID = $arrFields[3];

        $arrOutput = pointExistsInQdrant($sID, $sCollection);
    } elseif ($arrFields[1] == "Search")
    {
        $pMinScore = 0.3;
        $sCollection = $arrFields[2];
        $sValue = $arrFields[3];

        $arrOutput = searchVectorDatabase($sValue, $pMinScore, $sCollection);
    }
}
// ------------------------------------------------------------------------------------
else
{
    echo "Invalid Product";
    die;
}

header("Content-Type: application/json");
echo json_encode($arrOutput);
exit;


//getOpenRouter($sData, LLM_MODEL_LOW);

echo "Done";
