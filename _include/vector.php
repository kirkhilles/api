<?php

function searchFactoidsVector($sSearchText, $fMinScore = 0.3)
{
    $arrEmbedding = getOpenAIEmbedding($sSearchText);

    if ($arrEmbedding === false)
    {
        return false;
    }

    $arrSearchRequest = array(
        "vector" => $arrEmbedding,
        "limit" => 1000,
        "with_payload" => true,
        "with_vectors" => false,
        "score_threshold" => $fMinScore
    );

    try
    {
        $arrSearchResult = qdrantRequest("POST", "/collections/factoids/points/search", $arrSearchRequest);
    }
    catch (Exception $e)
    {
        return false;
    }

    if (!isset($arrSearchResult["result"]) || !is_array($arrSearchResult["result"]))
    {
        return false;
    }

    $arrResults = $arrSearchResult["result"];
    $arrReturnResults = array();

    foreach ($arrResults as $arrItem)
    {
        $arrReturnResults[] = array(
            "score" => isset($arrItem["score"]) ? (float)$arrItem["score"] : 0,
            "id" => isset($arrItem["id"]) ? $arrItem["id"] : "",
            "type" => isset($arrItem["payload"]["type"]) ? $arrItem["payload"]["type"] : "",
            "factoid" => isset($arrItem["payload"]["factoid"]) ? $arrItem["payload"]["factoid"] : "",
            "mysql_id" => isset($arrItem["payload"]["mysql_id"]) ? $arrItem["payload"]["mysql_id"] : ""
        );
    }

    return $arrReturnResults;
}

function searchVectorDatabase($sSearchText, $fMinScore = 0.3, $sCollection = "chat_data")
{
    $arrEmbedding = getOpenAIEmbedding($sSearchText);

    if ($arrEmbedding === false)
    {
        return false;
    }

    $arrSearchRequest = array(
        "vector" => $arrEmbedding,
        "limit" => 1000,
        "with_payload" => true,
        "with_vectors" => false,
        "score_threshold" => $fMinScore
    );

    try
    {
        $arrSearchResult = qdrantRequest("POST", "/collections/".$sCollection."/points/search", $arrSearchRequest);
    }
    catch (Exception $e)
    {
        return false;
    }

    if (!isset($arrSearchResult["result"]) || !is_array($arrSearchResult["result"]))
    {
        return false;
    }

    $arrResults = $arrSearchResult["result"];
    $arrReturnResults = array();

    $lCount = 0;

    //if ($sCollection == "chatgpt_history") print_r($arrResults); die;
    
    foreach ($arrResults as $arrItem)
    {
        if ($sCollection == "factoids")
        {
            $arrReturnResults[$lCount]["score"] = $arrItem["score"];
            $arrReturnResults[$lCount]["value"] = @$arrItem["payload"]["factoid"];
        }
        elseif ($sCollection == "chat_data")
        {
            $arrReturnResults[$lCount]["score"] = $arrItem["score"];
            $arrReturnResults[$lCount]["prompt"] = $arrItem["payload"]["message"];
            $arrReturnResults[$lCount]["response"] = $arrItem["payload"]["response"];
        }
        elseif ($sCollection == "chatgpt_history")
        {
            $arrReturnResults[$lCount]["score"] = $arrItem["score"];
            $arrReturnResults[$lCount]["prompt"] = $arrItem["payload"]["prompt"];
            $arrReturnResults[$lCount]["response"] = $arrItem["payload"]["response"];
        }
        else
        {
            $arrReturnResults[$lCount]["score"] = $arrItem["score"];
            $arrReturnResults[$lCount]["value"] = $arrItem["value"];
        }

        $lCount += 1;
    }

    return $arrReturnResults;
}


function getOpenAIEmbedding($sText)
{
    $sAPIKey = OPENAI_KEY_V2;
    $sModel = "text-embedding-3-small";
    $sURL = "https://api.openai.com/v1/embeddings";

    // Prepare the request data
    $arrData = array(
        "input" => $sText,
        "model" => $sModel
    );

    $sJSONData = json_encode($arrData);

    // Initialize cURL
    $ch = curl_init($sURL);

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $sJSONData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Content-Type: application/json",
        "Authorization: Bearer " . $sAPIKey
    ));

    // Execute the request
    $sResponse = curl_exec($ch);
    $iHTTPCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $sError = curl_error($ch);

    // Check for cURL errors
    if ($sError)
    {
        error_log("cURL Error: " . $sError);
        return false;
    }

    // Check HTTP response code
    if ($iHTTPCode != 200)
    {
        error_log("OpenAI API Error: HTTP " . $iHTTPCode . " - " . $sResponse);
        return false;
    }

    // Parse the response
    $arrResponse = json_decode($sResponse, true);

    // Verify response structure
    if (!isset($arrResponse["data"][0]["embedding"]))
    {
        error_log("Invalid OpenAI API Response: " . $sResponse);
        return false;
    }

    // Return the vector coordinates
    return $arrResponse["data"][0]["embedding"];
}

function qdrantRequest($method, $path, $data = null)
{
    $ch = curl_init();

    $url = "http://192.168.0.7:6333".$path;

    $headers = ["Content-Type: application/json"];
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_HTTPHEADER => $headers
    ]);

    if (!is_null($data))
    {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }

    $resp = curl_exec($ch);

    if ($resp === false)
    {
        throw new Exception("Qdrant HTTP error: ".curl_error($ch));
    }

    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $json = json_decode($resp, true);
    
    //print_r($json);

    if ($code >= 300)
    {
        throw new Exception("Qdrant API ".$code.": ".$resp);
    }

    return $json;
}

function createCollectionChatData($dim = 1536)
{
    $data = [
        "vectors" => [
            "message" => ["size" => $dim, "distance" => "Cosine"],
            "summary" => ["size" => $dim, "distance" => "Cosine"]
        ]
    ];

    return qdrantRequest("PUT", "/collections/chat_data", $data);
}

function upsertPoints($points)
{
    // $points: array of items with keys: id, vector (assoc: name=>array), payload (assoc)
    $data = ["points" => $points];

    return qdrantRequest("PUT", "/collections/chat_data/points", $data);
}

function searchSimilar($vectorName, $vector, $limit = 8, $filter = null)
{
    $data = [
        "vector" => ["name" => $vectorName, "vector" => $vector],
        "limit" => $limit,
        "with_payload" => true,
        "params" => ["hnsw_ef" => 128]
    ];

    if (!is_null($filter))
    {
        $data["filter"] = $filter;
    }

    return qdrantRequest("POST", "/collections/chat_data/points/search", $data);
}

function pointExistsInQdrant($lID, $sCollection = "chat_data")
{
    try
    {
        $arrResult = qdrantRequest("GET", "/collections/".$sCollection."/points/" . $lID);
        
        return isset($arrResult["result"]);
    }
    catch (Exception $e)
    {
        return false;
    }
}

function createCollection($collectionName, $dim = 1536)
{
    $data = [
        "vectors" => [
            "message" => ["size" => $dim, "distance" => "Cosine"],
            "summary" => ["size" => $dim, "distance" => "Cosine"]
        ]
    ];

    return qdrantRequest("PUT", "/collections/" . urlencode($collectionName), $data);
}

function upsertPointsToCollection($collectionName, $points)
{
    $data = ["points" => $points];

    return qdrantRequest("PUT", "/collections/" . urlencode($collectionName) . "/points", $data);
}

function pointExistsInQdrantCollection($collectionName, $lID)
{
    return pointExistsInQdrant($lID, $collectionName);
}

function deletePointFromQdrant($lID, $sCollection = "chat_data")
{
    $data = ["points" => [$lID]];
    return qdrantRequest("POST", "/collections/" . $sCollection . "/points/delete", $data);
}

function createQdrantUuid(): string
{
    // 16 bytes = 128 bits
    $data = random_bytes(16);

    // Set version to 0100 (UUID v4)
    $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);

    // Set variant to 10xx (RFC 4122)
    $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);

    // Format: 8-4-4-4-12 (36 chars)
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}