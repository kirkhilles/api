<?php

/**
 * OpenRouter API Integration Library
 *
 * Provides reusable functions for interacting with the OpenRouter AI API.
 * This library can be included in any project that needs OpenRouter functionality.
 *
 * Required: Define OPEN_ROUTER_KEY constant in your main configuration file
 *
 * @version 1.0
 */

/**
 * Make a request to OpenRouter API with chat completion
 *
 * @param string $sPrompt - The user's message/prompt
 * @param string $sModel - The model identifier (default: "openai/gpt-3.5-turbo")
 * @param float $fTemp - Temperature for response generation (0-2, default: 0.7)
 * @param int $iMaxTokens - Maximum tokens in response (0 = no limit, default: 0)
 * @param float $fTopP - Top P sampling (0-1, default: 1)
 * @param array $arrSystemMessage - Optional system message array ['role' => 'system', 'content' => '...']
 * @param bool $bReturnFullResponse - Return full API response or just the text (default: false)
 *
 * @return array - Response array with 'text', 'tokens', 'cost' or ['error' => 'message'] on failure
 */
function getOpenRouter($sPrompt, $sModel = "openai/gpt-3.5-turbo", $fTemp = 0.7, $iMaxTokens = 0, $fTopP = 1, $arrSystemMessage = null, $bReturnFullResponse = false)
{
    // Validate API key
    if (!defined('OPEN_ROUTER_KEY') || empty(OPEN_ROUTER_KEY))
    {
        return ["error" => "OpenRouter API key not configured (OPEN_ROUTER_KEY)"];
    }

    // Build messages array
    $arrMessages = [];

    if ($arrSystemMessage !== null && is_array($arrSystemMessage))
    {
        $arrMessages[] = $arrSystemMessage;
    }

    $arrMessages[] = [
        "role" => "user",
        "content" => $sPrompt
    ];

    // Build request data
    $data = [
        "model" => $sModel,
        "messages" => $arrMessages,
        "temperature" => (float)$fTemp,
        "top_p" => (float)$fTopP
    ];

    // Add max_tokens if specified
    if ($iMaxTokens > 0)
    {
        $data["max_tokens"] = (int)$iMaxTokens;
    }

    // Make API request
    $response = openRouterCurlRequest($data);

    if (isset($response['error']))
    {
        return $response;
    }

    // Log the request if logging function exists
    if (function_exists('logOpenRouterRequest'))
    {
        logOpenRouterRequest($sPrompt, $response, $sModel);
    }

    // Return full response or parsed response
    if ($bReturnFullResponse)
    {
        return $response;
    }

    return openRouterParseResponse($response);
}

/**
 * Make a chat completion request with conversation history
 *
 * @param array $arrMessages - Array of message objects with 'role' and 'content'
 * @param string $sModel - The model identifier
 * @param float $fTemp - Temperature for response generation
 * @param int $iMaxTokens - Maximum tokens in response
 * @param float $fTopP - Top P sampling
 *
 * @return array - Response array with 'text', 'tokens', 'cost' or error
 */
function getOpenRouterConversation($arrMessages, $sModel = "openai/gpt-3.5-turbo", $fTemp = 0.7, $iMaxTokens = 0, $fTopP = 1)
{
    if (!defined('OPEN_ROUTER_KEY') || empty(OPEN_ROUTER_KEY))
    {
        return ["error" => "OpenRouter API key not configured"];
    }

    if (!is_array($arrMessages) || empty($arrMessages))
    {
        return ["error" => "Messages array is required and cannot be empty"];
    }

    $data = [
        "model" => $sModel,
        "messages" => $arrMessages,
        "temperature" => (float)$fTemp,
        "top_p" => (float)$fTopP
    ];

    if ($iMaxTokens > 0)
    {
        $data["max_tokens"] = (int)$iMaxTokens;
    }

    $response = openRouterCurlRequest($data);

    if (isset($response['error']))
    {
        return $response;
    }

    if (function_exists('logOpenRouterRequest') && !empty($arrMessages))
    {
        $sLastUserMessage = "";
        for ($i = count($arrMessages) - 1; $i >= 0; $i--)
        {
            if ($arrMessages[$i]['role'] === 'user')
            {
                $sLastUserMessage = $arrMessages[$i]['content'];
                break;
            }
        }
        logOpenRouterRequest($sLastUserMessage, $response, $sModel);
    }

    return openRouterParseResponse($response);
}

/**
 * Get vector embeddings for text input
 *
 * @param string|array $input - The text or array of texts to embed
 * @param string $sModel - The model identifier (default: "openai/text-embedding-3-small")
 *
 * @return array - The embedding coordinates (vector) or array of vectors if input is array
 */
function getOpenRouterEmbedding($input, $sModel = "openai/text-embedding-3-small")
{
    if (!defined('OPEN_ROUTER_KEY') || empty(OPEN_ROUTER_KEY))
    {
        return ["error" => "OpenRouter API key not configured"];
    }

    $data = [
        "model" => $sModel,
        "input" => $input
    ];

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, "https://openrouter.ai/api/v1/embeddings");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, openRouterHeaders());

    $response = curl_exec($ch);

    if (curl_errno($ch))
    {
        return ["error" => "cURL Error: " . curl_error($ch)];
    }

    $arrResponse = json_decode($response, true);

    if (isset($arrResponse['error']))
    {
        return ["error" => "API Error: " . ($arrResponse['error']['message'] ?? json_encode($arrResponse['error']))];
    }

    if (!isset($arrResponse['data']) || !is_array($arrResponse['data']))
    {
        return ["error" => "Invalid API response format"];
    }

    // Return just the embedding array for single input, or array of embeddings for multiple
    if (is_array($input))
    {
        $arrEmbeddings = [];
        foreach ($arrResponse['data'] as $item)
        {
            $arrEmbeddings[$item['index']] = $item['embedding'];
        }
        ksort($arrEmbeddings);
        return $arrEmbeddings;
    }

    return $arrResponse['data'][0]['embedding'] ?? [];
}

/**
 * Get streaming response from OpenRouter
 *
 * @param string $sPrompt - The user's prompt
 * @param string $sModel - The model identifier
 * @param float $fTemp - Temperature
 * @param int $iMaxTokens - Maximum tokens
 * @param callable $fnCallback - Callback function for each streamed chunk
 *
 * @return array - Final response or error
 */
function getOpenRouterStream($sPrompt, $sModel = "openai/gpt-3.5-turbo", $fTemp = 0.7, $iMaxTokens = 0, $fnCallback = null)
{
    if (!defined('OPEN_ROUTER_KEY') || empty(OPEN_ROUTER_KEY))
    {
        return ["error" => "OpenRouter API key not configured"];
    }

    $data = [
        "model" => $sModel,
        "messages" => [
            ["role" => "user", "content" => $sPrompt]
        ],
        "temperature" => (float)$fTemp,
        "stream" => true
    ];

    if ($iMaxTokens > 0)
    {
        $data["max_tokens"] = (int)$iMaxTokens;
    }

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, "https://openrouter.ai/api/v1/chat/completions");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, openRouterHeaders());
    curl_setopt($ch, CURLOPT_BUFFERSIZE, 128);
    curl_setopt($ch, CURLOPT_TIMEOUT, 300);

    // Handle streaming output
    $fp = fopen('php://temp', 'r+');
    curl_setopt($ch, CURLOPT_FILE, $fp);

    $sFullResponse = "";

    curl_exec($ch);

    if (curl_errno($ch))
    {
        fclose($fp);
        return ["error" => "cURL Error: " . curl_error($ch)];
    }

    rewind($fp);
    $sContent = stream_get_contents($fp);
    fclose($fp);

    // Parse streaming response
    $arrLines = array_filter(explode("\n", $sContent));
    foreach ($arrLines as $sLine)
    {
        if (strpos($sLine, 'data: ') === 0)
        {
            $sData = substr($sLine, 6);
            if ($sData === '[DONE]')
            {
                break;
            }

            $arrData = json_decode($sData, true);
            if (isset($arrData['choices'][0]['delta']['content']))
            {
                $sChunk = $arrData['choices'][0]['delta']['content'];
                $sFullResponse .= $sChunk;

                if (is_callable($fnCallback))
                {
                    call_user_func($fnCallback, $sChunk);
                }
            }
        }
    }

    return [
        "text" => $sFullResponse,
        "tokens" => 0,
        "cost" => 0
    ];
}

/**
 * Fetch all available OpenRouter models
 *
 * @param bool $bForceRefresh - Force refresh cache (default: false)
 * @param string $sCachePath - Path to cache file (default: current directory)
 *
 * @return array - Array of model objects or empty array on failure
 */
function getOpenRouterModels($bForceRefresh = false, $sCachePath = ".")
{
    if (!defined('OPEN_ROUTER_KEY') || empty(OPEN_ROUTER_KEY))
    {
        return [];
    }

    $sCacheFile = $sCachePath . DIRECTORY_SEPARATOR . "openrouter_models.json";
    $lCacheTime = 60 * 60 * 24; // 24 hours

    // Check cache first
    if (!$bForceRefresh && file_exists($sCacheFile) && (time() - filemtime($sCacheFile) < $lCacheTime))
    {
        $arrCached = json_decode(file_get_contents($sCacheFile), true);
        if (is_array($arrCached) && !empty($arrCached))
        {
            return $arrCached;
        }
    }

    // Fetch from API
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://openrouter.ai/api/v1/models");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_HTTPHEADER, openRouterHeaders());

    $response = curl_exec($ch);

    if (curl_errno($ch))
    {
        return [];
    }

    $arrResponse = json_decode($response, true);

    if (isset($arrResponse['data']) && is_array($arrResponse['data']))
    {
        // Cache the models
        @file_put_contents($sCacheFile, json_encode($arrResponse['data']));
        return $arrResponse['data'];
    }

    return [];
}

/**
 * Get information about a specific model
 *
 * @param string $sModelId - The model identifier
 * @param array $arrModels - Optional pre-fetched models array
 *
 * @return array|null - Model object or null if not found
 */
function getOpenRouterModelInfo($sModelId, $arrModels = null)
{
    if ($arrModels === null)
    {
        $arrModels = getOpenRouterModels();
    }

    foreach ($arrModels as $model)
    {
        if ($model['id'] === $sModelId)
        {
            return $model;
        }
    }

    return null;
}

/**
 * Filter models by criteria
 *
 * @param array $arrModels - Array of models to filter
 * @param array $arrCriteria - Filtering criteria:
 *                             - 'excludeFree' => bool
 *                             - 'minParams' => float (in billions)
 *                             - 'maxPrice' => float
 *                             - 'provider' => string (e.g., 'openai', 'anthropic')
 *
 * @return array - Filtered models
 */
function filterOpenRouterModels($arrModels, $arrCriteria = [])
{
    return array_filter($arrModels, function ($model) use ($arrCriteria)
    {
        // Filter free models
        if (!empty($arrCriteria['excludeFree']))
        {
            $fPromptPrice = floatval($model['pricing']['prompt'] ?? 0);
            $fCompletionPrice = floatval($model['pricing']['completion'] ?? 0);
            if ($fPromptPrice <= 0 && $fCompletionPrice <= 0)
            {
                return false;
            }
        }

        // Filter by minimum parameters
        if (!empty($arrCriteria['minParams']))
        {
            $fMinParams = floatval($arrCriteria['minParams']);
            if (preg_match('/(\d+(?:\.\d+)?)\s*[MB]/i', $model['name'], $matches))
            {
                $fParams = floatval($matches[1]);
                // Convert M to B if needed
                if (stripos($model['name'], 'M') !== false && stripos($matches[0], 'M') !== false)
                {
                    $fParams = $fParams / 1000;
                }
                if ($fParams < $fMinParams)
                {
                    return false;
                }
            }
        }

        // Filter by max price
        if (!empty($arrCriteria['maxPrice']))
        {
            $fMaxPrice = floatval($arrCriteria['maxPrice']);
            $fPromptPrice = floatval($model['pricing']['prompt'] ?? 0);
            $fCompletionPrice = floatval($model['pricing']['completion'] ?? 0);
            $fAvgPrice = ($fPromptPrice + $fCompletionPrice) / 2;
            if ($fAvgPrice > $fMaxPrice)
            {
                return false;
            }
        }

        // Filter by provider
        if (!empty($arrCriteria['provider']))
        {
            $sProvider = strtolower($arrCriteria['provider']);
            if (stripos($model['id'], $sProvider) === false)
            {
                return false;
            }
        }

        return true;
    });
}

/**
 * Sort models by specified criteria
 *
 * @param array $arrModels - Models to sort
 * @param string $sSort - Sort method: 'price', 'name', 'speed', 'context' (default: 'name')
 * @param bool $bAscending - Sort ascending (true) or descending (false)
 *
 * @return array - Sorted models
 */
function sortOpenRouterModels($arrModels, $sSort = 'name', $bAscending = true)
{
    $arrModels = array_values($arrModels); // Reset array keys

    usort($arrModels, function ($a, $b) use ($sSort, $bAscending)
    {
        $iComparison = 0;

        switch ($sSort)
        {
            case 'price':
                $fPriceA = (floatval($a['pricing']['prompt'] ?? 0) + floatval($a['pricing']['completion'] ?? 0)) / 2;
                $fPriceB = (floatval($b['pricing']['prompt'] ?? 0) + floatval($b['pricing']['completion'] ?? 0)) / 2;
                $iComparison = ($fPriceA < $fPriceB) ? -1 : (($fPriceA > $fPriceB) ? 1 : 0);
                break;

            case 'context':
                $iContextA = intval($a['context_length'] ?? 0);
                $iContextB = intval($b['context_length'] ?? 0);
                $iComparison = ($iContextA < $iContextB) ? -1 : (($iContextA > $iContextB) ? 1 : 0);
                break;

            case 'speed':
                // Use context length as a proxy for speed (larger = slower typically)
                $iContextA = intval($a['context_length'] ?? 0);
                $iContextB = intval($b['context_length'] ?? 0);
                $iComparison = ($iContextA > $iContextB) ? -1 : (($iContextA < $iContextB) ? 1 : 0);
                break;

            case 'name':
            default:
                $iComparison = strcmp($a['name'], $b['name']);
                break;
        }

        return $bAscending ? $iComparison : -$iComparison;
    });

    return $arrModels;
}

/**
 * Calculate estimated cost for a request
 *
 * @param string $sModelId - The model identifier
 * @param int $nPromptTokens - Number of prompt tokens
 * @param int $nCompletionTokens - Number of completion tokens
 * @param array $arrModels - Optional pre-fetched models array
 *
 * @return float - Estimated cost in dollars
 */
function calculateOpenRouterCost($sModelId, $nPromptTokens, $nCompletionTokens, $arrModels = null)
{
    $model = getOpenRouterModelInfo($sModelId, $arrModels);

    if (!$model)
    {
        return 0.0;
    }

    $fPromptPrice = floatval($model['pricing']['prompt'] ?? 0);
    $fCompletionPrice = floatval($model['pricing']['completion'] ?? 0);

    // Prices are typically per 1M tokens, so divide by 1,000,000
    $fPromptCost = ($nPromptTokens / 1000000) * $fPromptPrice;
    $fCompletionCost = ($nCompletionTokens / 1000000) * $fCompletionPrice;

    return $fPromptCost + $fCompletionCost;
}

/**
 * Get models by provider
 *
 * @param string $sProvider - Provider name (e.g., 'openai', 'anthropic', 'meta-llama')
 * @param array $arrModels - Optional pre-fetched models
 *
 * @return array - Models from the specified provider
 */
function getOpenRouterModelsByProvider($sProvider, $arrModels = null)
{
    if ($arrModels === null)
    {
        $arrModels = getOpenRouterModels();
    }

    $sProvider = strtolower($sProvider);

    return array_filter($arrModels, function ($model) use ($sProvider)
    {
        return stripos($model['id'], $sProvider) === 0;
    });
}

/**
 * Count available models
 *
 * @param array $arrModels - Models array
 *
 * @return array - Count information with 'total', 'free', 'paid'
 */
function countOpenRouterModels($arrModels = null)
{
    if ($arrModels === null)
    {
        $arrModels = getOpenRouterModels();
    }

    $iTotal = count($arrModels);
    $iFree = 0;
    $iPaid = 0;

    foreach ($arrModels as $model)
    {
        $fPromptPrice = floatval($model['pricing']['prompt'] ?? 0);
        $fCompletionPrice = floatval($model['pricing']['completion'] ?? 0);

        if ($fPromptPrice > 0 || $fCompletionPrice > 0)
        {
            $iPaid++;
        } else
        {
            $iFree++;
        }
    }

    return [
        'total' => $iTotal,
        'free' => $iFree,
        'paid' => $iPaid
    ];
}

/**
 * Format pricing information for display
 *
 * @param array $model - Model object
 *
 * @return string - Formatted pricing string
 */
function formatOpenRouterPricing($model)
{
    $fPrompt = floatval($model['pricing']['prompt'] ?? 0);
    $fCompletion = floatval($model['pricing']['completion'] ?? 0);

    if ($fPrompt == 0 && $fCompletion == 0)
    {
        return "Free";
    }

    if ($fPrompt == $fCompletion)
    {
        return "$" . number_format($fPrompt, 7) . " / 1M tokens";
    }

    return "Prompt: $" . number_format($fPrompt, 7) . " / Completion: $" . number_format($fCompletion, 7) . " (per 1M tokens)";
}

/**
 * ============================================================================
 * HELPER FUNCTIONS (Internal Use)
 * ============================================================================
 */

/**
 * Check if an internet search is needed to answer a question
 *
 * Uses an LLM to analyze the prompt and determine if current/real-time
 * information from the internet would help provide a better answer.
 *
 * @param string $sPrompt - The user's question/prompt to analyze
 * @param string $sModel - The model to use for analysis (default: "openai/gpt-4o-mini")
 *
 * @return array - [
 *     'needs_search' => bool,      // true if search recommended
 *     'confidence' => float,       // 0-1 confidence score
 *     'reason' => string,          // explanation of decision
 *     'search_query' => string,    // suggested search query if needed
 *     'error' => string            // only present if error occurred
 * ]
 */
function checkNeedsInternetSearch($sPrompt, $sModel = "openai/gpt-4o-mini")
{
    if (!defined('OPEN_ROUTER_KEY') || empty(OPEN_ROUTER_KEY))
    {
        return ["error" => "OpenRouter API key not configured", "needs_search" => false];
    }

    $sSystemPrompt = <<<EOT
You are an AI assistant that analyzes questions to determine if an internet search would be helpful to provide a complete and accurate answer.

IMPORTANT: You must determine if YOU (as an LLM) have reliable, specific knowledge to answer this question accurately, or if searching the internet would provide better/more accurate information.

Analyze the user's question and determine:
1. Does this question require current/real-time information (news, prices, weather, events, etc.)?
2. Does this question ask about specific facts that may have changed recently?
3. Does this question reference specific companies, products, people, or events that require up-to-date information?
4. Does this question ask about SPECIFIC product details, specifications, model numbers, or manufacturer recommendations that you may not have precise knowledge of?
5. Could your training data be outdated, incomplete, or potentially inaccurate for this specific query?
6. Would official/authoritative sources provide more reliable information than your training data?

LEAN TOWARDS SEARCHING when:
- The question involves specific product models, years, versions, or configurations
- The question asks about manufacturer recommendations, specifications, or official guidelines
- You are not 100% confident you have accurate, specific information
- The information could vary by region, version, or time period
- Getting this wrong could cause harm (medical, financial, safety, legal advice)

Respond ONLY with a valid JSON object (no markdown, no code blocks) in this exact format:
{
    "needs_search": true or false,
    "confidence": 0.0 to 1.0,
    "reason": "brief explanation of your decision",
    "search_query": "suggested search query if needs_search is true, otherwise empty string"
}

Examples of questions that NEED search:
- "What is the current price of Bitcoin?"
- "What happened in the news today?"
- "What is the weather in New York?"
- "Who won the latest Super Bowl?"
- "What are the current interest rates?"
- "What maintenance does a 2022 BMW X3 need at 35,000 miles?" (specific manufacturer schedules)
- "What are the side effects of Ozempic?" (medical - needs authoritative source)
- "What is the warranty on a Tesla Model 3?" (specific product details)
- "What are the 2024 tax brackets?" (changes annually)

Examples of questions that DON'T need search:
- "What is the capital of France?"
- "Explain how photosynthesis works"
- "Write a poem about nature"
- "What is 2 + 2?"
- "How do I write a for loop in Python?"
- "What is the Pythagorean theorem?"
EOT;

    $data = [
        "model" => $sModel,
        "messages" => [
            ["role" => "system", "content" => $sSystemPrompt],
            ["role" => "user", "content" => "Analyze this question and determine if an internet search is needed:\n\n" . $sPrompt]
        ],
        "temperature" => 0.1,
        "max_tokens" => 300
    ];

    $response = openRouterCurlRequest($data);

    if (isset($response['error']))
    {
        return [
            "error" => $response['error'],
            "needs_search" => false,
            "confidence" => 0,
            "reason" => "Error occurred during analysis",
            "search_query" => ""
        ];
    }

    // Log the request
    if (function_exists('logOpenRouterRequest'))
    {
        logOpenRouterRequest($sPrompt, $response, $sModel);
    }

    $sResponseText = $response['choices'][0]['message']['content'] ?? "";

    // Clean up response - remove markdown code blocks if present
    $sResponseText = trim($sResponseText);
    $sResponseText = preg_replace('/^```json\s*/i', '', $sResponseText);
    $sResponseText = preg_replace('/^```\s*/i', '', $sResponseText);
    $sResponseText = preg_replace('/\s*```$/i', '', $sResponseText);

    // Parse JSON response
    $arrResult = json_decode($sResponseText, true);

    if (json_last_error() !== JSON_ERROR_NONE || !is_array($arrResult))
    {
        // If JSON parsing fails, try to extract info from text
        $bNeedsSearch = (stripos($sResponseText, '"needs_search": true') !== false ||
                         stripos($sResponseText, '"needs_search":true') !== false);

        return [
            "needs_search" => $bNeedsSearch,
            "confidence" => 0.5,
            "reason" => "Could not parse LLM response properly",
            "search_query" => "",
            "raw_response" => $sResponseText
        ];
    }

    return [
        "needs_search" => (bool)($arrResult['needs_search'] ?? false),
        "confidence" => floatval($arrResult['confidence'] ?? 0.5),
        "reason" => $arrResult['reason'] ?? "",
        "search_query" => $arrResult['search_query'] ?? ""
    ];
}

/**
 * Generate multiple unique search queries for a given prompt
 *
 * Uses an LLM to create 3 diverse, focused search queries that together
 * will provide comprehensive coverage of the topic for Google search.
 *
 * @param string $sPrompt - The user's question/prompt to generate searches for
 * @param string $sModel - The model to use for generation (default: "openai/gpt-4o-mini")
 *
 * @return array - [
 *     'queries' => array,          // array of 3 search query strings
 *     'error' => string            // only present if error occurred
 * ]
 */
function generateSearchQueries($sPrompt, $sModel = "openai/gpt-4o-mini")
{
    if (!defined('OPEN_ROUTER_KEY') || empty(OPEN_ROUTER_KEY))
    {
        return ["error" => "OpenRouter API key not configured", "queries" => []];
    }

    $sSystemPrompt = <<<EOT
You are a search query optimization expert. Your task is to generate 3 unique, focused Google search queries that together will provide comprehensive information to answer the user's question.

Guidelines for generating queries:
1. Each query should target a DIFFERENT aspect or angle of the question
2. Use specific, searchable terms - avoid vague language
3. Include relevant keywords that would appear in authoritative sources
4. Consider different types of sources (news, official sites, expert analysis)
5. Keep queries concise but specific (typically 3-8 words)
6. For current events, include terms like "2024", "2025", "latest", "current" where appropriate
7. Avoid duplicate or overlapping queries

Respond ONLY with a valid JSON object (no markdown, no code blocks) in this exact format:
{
    "queries": [
        "first search query",
        "second search query", 
        "third search query"
    ],
    "reasoning": "brief explanation of why these queries provide good coverage"
}

Example for "What is the current price of Bitcoin and should I invest?":
{
    "queries": [
        "Bitcoin price today USD December 2025",
        "Bitcoin price prediction 2025 expert analysis",
        "Bitcoin investment risks benefits 2025"
    ],
    "reasoning": "Query 1 gets current price, Query 2 provides expert forecasts, Query 3 covers investment considerations"
}
EOT;

    $data = [
        "model" => $sModel,
        "messages" => [
            ["role" => "system", "content" => $sSystemPrompt],
            ["role" => "user", "content" => "Generate 3 unique Google search queries for this question:\n\n" . $sPrompt]
        ],
        "temperature" => 0.3,
        "max_tokens" => 800
    ];

    $response = openRouterCurlRequest($data);

    if (isset($response['error']))
    {
        return [
            "error" => $response['error'],
            "queries" => []
        ];
    }

    // Log the request
    if (function_exists('logOpenRouterRequest'))
    {
        logOpenRouterRequest($sPrompt, $response, $sModel);
    }

    $sResponseText = $response['choices'][0]['message']['content'] ?? "";

    // Clean up response - remove markdown code blocks if present
    $sResponseText = trim($sResponseText);
    $sResponseText = preg_replace('/^```json\s*/i', '', $sResponseText);
    $sResponseText = preg_replace('/^```\s*/i', '', $sResponseText);
    $sResponseText = preg_replace('/\s*```$/i', '', $sResponseText);

    // Parse JSON response
    $arrResult = json_decode($sResponseText, true);

    if (json_last_error() !== JSON_ERROR_NONE || !is_array($arrResult))
    {
        // Try to extract queries array even from truncated JSON
        if (preg_match('/"queries"\s*:\s*\[(.*?)\]/s', $sResponseText, $matches))
        {
            // Extract individual query strings
            preg_match_all('/"([^"]+)"/', $matches[1], $queryMatches);
            if (!empty($queryMatches[1]))
            {
                $arrQueries = array_slice($queryMatches[1], 0, 3);
                while (count($arrQueries) < 3)
                {
                    $arrQueries[] = $sPrompt;
                }
                return [
                    "queries" => $arrQueries,
                    "reasoning" => "Extracted from truncated response"
                ];
            }
        }

        return [
            "error" => "Could not parse LLM response properly",
            "queries" => [],
            "raw_response" => $sResponseText
        ];
    }

    // Validate we have 3 queries
    $arrQueries = $arrResult['queries'] ?? [];
    if (!is_array($arrQueries) || count($arrQueries) < 1)
    {
        return [
            "error" => "No valid queries returned",
            "queries" => []
        ];
    }

    // Ensure we have exactly 3 queries (pad or trim if needed)
    while (count($arrQueries) < 3)
    {
        $arrQueries[] = $sPrompt; // Fallback to original prompt
    }
    $arrQueries = array_slice($arrQueries, 0, 3);

    return [
        "queries" => $arrQueries,
        "reasoning" => $arrResult['reasoning'] ?? ""
    ];
}

/**
 * Identify the top 5 most relevant URLs from search results
 *
 * Uses an LLM to analyze search results and select the 5 most relevant
 * URLs that would provide the best information to answer the user's question.
 *
 * @param array $arrSearchResults - Array of search results with Search, Title, URL, Snippet keys
 * @param string $sOriginalPrompt - The original user question (for context)
 * @param string $sModel - The model to use for analysis (default: "openai/gpt-4o-mini")
 *
 * @return array - [
 *     'urls' => array,             // array of up to 5 URL strings
 *     'reasoning' => string,       // explanation of selections
 *     'error' => string            // only present if error occurred
 * ]
 */
function selectRelevantURLs($arrSearchResults, $sOriginalPrompt = "", $sModel = "openai/gpt-4o-mini")
{
    if (!defined('OPEN_ROUTER_KEY') || empty(OPEN_ROUTER_KEY))
    {
        return ["error" => "OpenRouter API key not configured", "urls" => []];
    }

    if (empty($arrSearchResults) || !is_array($arrSearchResults))
    {
        return ["error" => "No search results provided", "urls" => []];
    }

    // If 5 or fewer results, return all URLs
    if (count($arrSearchResults) <= 5)
    {
        $arrURLs = array_map(function ($item)
        {
            return $item['URL'] ?? $item['url'] ?? $item['link'] ?? '';
        }, $arrSearchResults);
        return [
            "urls" => array_filter($arrURLs),
            "reasoning" => "Returned all results as there were 5 or fewer"
        ];
    }

    // Build a formatted list of results for the LLM
    $sResultsList = "";
    foreach ($arrSearchResults as $i => $result)
    {
        $sURL = $result['URL'] ?? $result['url'] ?? $result['link'] ?? '';
        $sTitle = $result['Title'] ?? $result['title'] ?? '';
        $sSnippet = $result['Snippet'] ?? $result['snippet'] ?? '';

        $sResultsList .= "[$i] URL: $sURL\n";
        $sResultsList .= "    Title: $sTitle\n";
        $sResultsList .= "    Snippet: $sSnippet\n\n";
    }

    $sSystemPrompt = <<<EOT
You are an expert at evaluating search results to identify the most useful sources of information.

Your task is to select the TOP 5 most relevant URLs from the provided search results that would best help answer the user's question.

Prioritize:
1. Official/authoritative sources (manufacturer sites, official documentation, government sites)
2. Detailed, specific content that directly addresses the question
3. Recent/up-to-date information
4. Sources with comprehensive information (not just ads or brief mentions)
5. Diverse perspectives when relevant (not all from the same source)

Avoid:
- Generic sales/dealer pages with no useful content
- Outdated information when freshness matters
- Paywalled or inaccessible content
- Duplicate content from the same source
- Results that only tangentially relate to the question

Respond ONLY with a valid JSON object (no markdown, no code blocks) in this exact format:
{
    "selected_urls": [
        "https://first-url.com",
        "https://second-url.com",
        "https://third-url.com",
        "https://fourth-url.com",
        "https://fifth-url.com"
    ],
    "reasoning": "brief explanation of why these 5 were selected"
}

Return exactly 5 URLs in order of relevance (most relevant first). Use the exact URLs from the search results.
EOT;

    $sUserMessage = "";
    if (!empty($sOriginalPrompt))
    {
        $sUserMessage .= "User's Question: " . $sOriginalPrompt . "\n\n";
    }
    $sUserMessage .= "Search Results:\n\n" . $sResultsList;
    $sUserMessage .= "\nSelect the top 5 most relevant URLs to download content from.";

    $data = [
        "model" => $sModel,
        "messages" => [
            ["role" => "system", "content" => $sSystemPrompt],
            ["role" => "user", "content" => $sUserMessage]
        ],
        "temperature" => 0.1,
        "max_tokens" => 600
    ];

    $response = openRouterCurlRequest($data);

    if (isset($response['error']))
    {
        return [
            "error" => $response['error'],
            "urls" => []
        ];
    }

    // Log the request
    if (function_exists('logOpenRouterRequest'))
    {
        logOpenRouterRequest($sOriginalPrompt ?: "URL Selection", $response, $sModel);
    }

    $sResponseText = $response['choices'][0]['message']['content'] ?? "";

    // Handle reasoning models that put content in reasoning field
    if (empty($sResponseText))
    {
        // Try to get from reasoning field (for deepseek-r1 and similar models)
        $sResponseText = $response['choices'][0]['message']['reasoning'] ?? "";
    }

    // If still empty, return first 5 URLs from original results as fallback
    if (empty($sResponseText))
    {
        $arrFallbackURLs = [];
        foreach ($arrSearchResults as $result)
        {
            $sURL = $result['URL'] ?? $result['url'] ?? $result['link'] ?? '';
            if (!empty($sURL))
            {
                $arrFallbackURLs[] = $sURL;
            }
            if (count($arrFallbackURLs) >= 5)
            {
                break;
            }
        }
        return [
            "urls" => $arrFallbackURLs,
            "reasoning" => "Fallback: LLM returned empty response, using first 5 results"
        ];
    }

    // Clean up response - remove markdown code blocks if present
    $sResponseText = trim($sResponseText);
    $sResponseText = preg_replace('/^```json\s*/i', '', $sResponseText);
    $sResponseText = preg_replace('/^```\s*/i', '', $sResponseText);
    $sResponseText = preg_replace('/\s*```$/i', '', $sResponseText);

    // Parse JSON response
    $arrResult = json_decode($sResponseText, true);

    if (json_last_error() !== JSON_ERROR_NONE || !is_array($arrResult))
    {
        // Fallback: try to extract URLs mentioned in the response text
        // Look for array indices like [0], [1], [21] mentioned in the reasoning
        $arrMentionedIndices = [];
        if (preg_match_all('/\[(\d+)\]/', $sResponseText, $indexMatches))
        {
            $arrMentionedIndices = array_unique($indexMatches[1]);
        }

        $arrFallbackURLs = [];

        // First, try to get URLs from mentioned indices
        foreach ($arrMentionedIndices as $idx)
        {
            $idx = intval($idx);
            if (isset($arrSearchResults[$idx]))
            {
                $sURL = $arrSearchResults[$idx]['URL'] ?? $arrSearchResults[$idx]['url'] ?? $arrSearchResults[$idx]['link'] ?? '';
                if (!empty($sURL) && !in_array($sURL, $arrFallbackURLs))
                {
                    $arrFallbackURLs[] = $sURL;
                }
            }
            if (count($arrFallbackURLs) >= 5)
            {
                break;
            }
        }

        // If we found URLs from indices, return them
        if (!empty($arrFallbackURLs))
        {
            return [
                "urls" => array_slice($arrFallbackURLs, 0, 5),
                "reasoning" => "Extracted from LLM reasoning (indices mentioned: " . implode(", ", array_slice($arrMentionedIndices, 0, 10)) . ")"
            ];
        }

        // Otherwise, try exact URL matching
        foreach ($arrSearchResults as $result)
        {
            $sURL = $result['URL'] ?? $result['url'] ?? $result['link'] ?? '';
            if (!empty($sURL) && strpos($sResponseText, $sURL) !== false)
            {
                $arrFallbackURLs[] = $sURL;
            }
            if (count($arrFallbackURLs) >= 5)
            {
                break;
            }
        }

        if (!empty($arrFallbackURLs))
        {
            return [
                "urls" => $arrFallbackURLs,
                "reasoning" => "Extracted from LLM response (JSON parse failed)"
            ];
        }

        // Last resort: return first 5 URLs
        foreach ($arrSearchResults as $result)
        {
            $sURL = $result['URL'] ?? $result['url'] ?? $result['link'] ?? '';
            if (!empty($sURL))
            {
                $arrFallbackURLs[] = $sURL;
            }
            if (count($arrFallbackURLs) >= 5)
            {
                break;
            }
        }

        return [
            "urls" => $arrFallbackURLs,
            "reasoning" => "Fallback: Could not parse LLM response, using first 5 results"
        ];
    }

    // Extract URLs from response
    $arrSelectedURLs = $arrResult['selected_urls'] ?? $arrResult['urls'] ?? [];

    if (!is_array($arrSelectedURLs) || empty($arrSelectedURLs))
    {
        return [
            "error" => "No URLs returned from LLM",
            "urls" => []
        ];
    }

    // Validate URLs exist in original results
    $arrValidURLs = [];
    $arrOriginalURLs = array_map(function ($item)
    {
        return $item['URL'] ?? $item['url'] ?? $item['link'] ?? '';
    }, $arrSearchResults);

    foreach ($arrSelectedURLs as $sURL)
    {
        // Exact match
        if (in_array($sURL, $arrOriginalURLs))
        {
            $arrValidURLs[] = $sURL;
        } else
        {
            // Try matching with/without trailing slash
            $sURLAlt = (substr($sURL, -1) === '/') ? rtrim($sURL, '/') : $sURL . '/';
            if (in_array($sURLAlt, $arrOriginalURLs))
            {
                $arrValidURLs[] = $sURLAlt;
            }
        }
    }

    // If we have fewer than 5 valid URLs, pad with remaining URLs from original results
    if (count($arrValidURLs) < 5)
    {
        foreach ($arrOriginalURLs as $sURL)
        {
            if (!empty($sURL) && !in_array($sURL, $arrValidURLs))
            {
                $arrValidURLs[] = $sURL;
                if (count($arrValidURLs) >= 5)
                {
                    break;
                }
            }
        }
    }

    // Ensure we have up to 5 URLs
    $arrValidURLs = array_slice($arrValidURLs, 0, 5);

    return [
        "urls" => $arrValidURLs,
        "reasoning" => $arrResult['reasoning'] ?? ""
    ];
}

/**
 * Fetch webpage content using Jina AI Reader
 *
 * Uses Jina AI's reader service to extract clean, readable content from a URL.
 * Returns the page content in markdown format, ideal for feeding to an LLM.
 *
 * @param string $sURL - The URL to fetch content from
 * @param array $arrOptions - Optional settings:
 *                            - 'timeout' => int (default: 30 seconds)
 *                            - 'returnFormat' => 'markdown'|'text'|'html' (default: 'markdown')
 *                            - 'includeLinks' => bool (default: true)
 *                            - 'includeImages' => bool (default: false)
 *
 * @return array - [
 *     'success' => bool,
 *     'content' => string,         // The extracted content
 *     'title' => string,           // Page title if available
 *     'url' => string,             // The original URL
 *     'error' => string            // Only present if error occurred
 * ]
 */
function getJinaContent($sURL, $arrOptions = [])
{
    if (empty($sURL))
    {
        return [
            "success" => false,
            "error" => "URL is required",
            "content" => "",
            "url" => ""
        ];
    }

    // Validate URL format
    if (!filter_var($sURL, FILTER_VALIDATE_URL))
    {
        return [
            "success" => false,
            "error" => "Invalid URL format",
            "content" => "",
            "url" => $sURL
        ];
    }

    // Options with defaults
    $iTimeout = $arrOptions['timeout'] ?? 30;
    $sFormat = $arrOptions['returnFormat'] ?? 'markdown';
    $bIncludeLinks = $arrOptions['includeLinks'] ?? true;
    $bIncludeImages = $arrOptions['includeImages'] ?? false;

    // Build Jina AI Reader URL
    $sJinaURL = "https://r.jina.ai/" . $sURL;

    // Build headers
    $arrHeaders = [
        "Accept: text/plain"
    ];

    // Add Jina API key if defined
    if (defined('JINA_API_KEY') && !empty(JINA_API_KEY))
    {
        $arrHeaders[] = "Authorization: Bearer " . JINA_API_KEY;
    }

    // Add optional headers based on options
    if (!$bIncludeLinks)
    {
        $arrHeaders[] = "X-No-Links: true";
    }
    if ($bIncludeImages)
    {
        $arrHeaders[] = "X-With-Images-Summary: true";
    }
    if ($sFormat === 'html')
    {
        $arrHeaders[] = "X-Return-Format: html";
    } elseif ($sFormat === 'text')
    {
        $arrHeaders[] = "X-Return-Format: text";
    }

    // Execute cURL request
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $sJinaURL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, $iTimeout);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $arrHeaders);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (compatible; JinaReader/1.0)");

    $sResponse = curl_exec($ch);
    $iHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch))
    {
        $sError = curl_error($ch);
        return [
            "success" => false,
            "error" => "cURL Error: " . $sError,
            "content" => "",
            "url" => $sURL
        ];
    }

    // Check HTTP status
    if ($iHttpCode !== 200)
    {
        return [
            "success" => false,
            "error" => "HTTP Error: " . $iHttpCode,
            "content" => $sResponse,
            "url" => $sURL
        ];
    }

    // Try to extract title from markdown content
    $sTitle = "";
    if (preg_match('/^#\s+(.+)$/m', $sResponse, $matches))
    {
        $sTitle = trim($matches[1]);
    } elseif (preg_match('/^Title:\s*(.+)$/mi', $sResponse, $matches))
    {
        $sTitle = trim($matches[1]);
    }

    return [
        "success" => true,
        "content" => $sResponse,
        "title" => $sTitle,
        "url" => $sURL,
        "length" => strlen($sResponse)
    ];
}

/**
 * Fetch content from multiple URLs using Jina AI Reader
 *
 * Checks the database cache first before downloading. Stores new content
 * to the pagecontent table in the ai database.
 *
 * @param array $arrURLs - Array of URLs to fetch
 * @param array $arrOptions - Options to pass to getJinaContent
 *
 * @return array - Array of text content, keyed by URL
 */
function getJinaContentMultiple($arrURLs, $arrOptions = [])
{
    $connAI = getConnectionTRADING("ai");
    $arrResults = [];

    foreach ($arrURLs as $sURL)
    {
        // Check database cache first
        $sCachedContent = getPageContentFromDB($sURL, $connAI);

        if ($sCachedContent !== null)
        {
            // Use cached content
            $arrResults[$sURL] = $sCachedContent;
        } else
        {
            // Fetch from Jina AI
            $arrResponse = getJinaContent($sURL, $arrOptions);

            if ($arrResponse['success'] && !empty($arrResponse['content']))
            {
                $sContent = $arrResponse['content'];
                $arrResults[$sURL] = $sContent;

                // Store in database cache
                savePageContentToDB($sURL, $sContent, $connAI);
            } else
            {
                // Store error or empty result
                $arrResults[$sURL] = "";
            }
        }
    }

    return $arrResults;
}

/**
 * Clean raw scraped content using an LLM
 *
 * Extracts the core useful content from noisy web scrape data.
 * Removes navigation, ads, signatures, UI elements while preserving
 * the original wording of the actual content.
 *
 * @param string $sRawContent - The raw scraped content to clean
 * @param string $sModel - The LLM model to use (default: uses LLM_MODEL_LOW if defined)
 * @param string $sContentType - Type hint: 'auto', 'forum', 'article', 'documentation' (default: 'auto')
 *
 * @return array - [
 *     'success' => bool,
 *     'content' => string,         // Cleaned content
 *     'error' => string            // Only present if error occurred
 * ]
 */
function cleanScrapedContent($sRawContent, $sModel = null, $sContentType = 'auto')
{
    // Default model
    if ($sModel === null)
    {
        $sModel = defined('LLM_MODEL_LOW') ? LLM_MODEL_LOW : "meta-llama/llama-3.1-8b-instruct";
    }

    if (!defined('OPEN_ROUTER_KEY') || empty(OPEN_ROUTER_KEY))
    {
        return [
            "success" => false,
            "error" => "OpenRouter API key not configured",
            "content" => ""
        ];
    }

    if (empty($sRawContent))
    {
        return [
            "success" => false,
            "error" => "No content provided",
            "content" => ""
        ];
    }

    // Truncate very long content to avoid token limits
    $iMaxChars = 50000;
    if (strlen($sRawContent) > $iMaxChars)
    {
        $sRawContent = substr($sRawContent, 0, $iMaxChars) . "\n\n[Content truncated due to length...]";
    }

    $sSystemPrompt = <<<EOT
You are a Web Scraper Cleaning Agent. Your job is to extract the core forum conversation or article content from the provided noisy text.

IMPORTANT RULES:
1. Remove all navigation menus, headers, footers, sidebars, and UI elements
2. Remove user signatures, 'join dates', 'reputation scores', 'post counts', and similar metadata
3. Remove advertisements, promotional content, and cookie notices
4. Remove "Reply", "Quote", "Like", "Share" buttons and similar UI text
5. Remove duplicate content (like quoted text that appears multiple times)

FORMAT RULES:
- For forum/discussion content: Format as a clean Markdown conversation (e.g., **Username:** their post content)
- For articles: Keep the article structure with headings and paragraphs
- For documentation: Preserve code blocks and technical formatting
- If a post contains a quote of a previous post, format it as a Markdown blockquote (> quoted text)

CRITICAL:
- Do NOT summarize. Keep the original wording of posts/content exactly as written
- Do NOT add your own commentary or analysis
- Do NOT change the meaning or intent of any content
- Simply extract and clean, preserving the original text

Output ONLY the cleaned content, nothing else.
EOT;

    $data = [
        "model" => $sModel,
        "messages" => [
            ["role" => "system", "content" => $sSystemPrompt],
            ["role" => "user", "content" => "Clean the following scraped web content and extract the useful information:\n\n" . $sRawContent]
        ],
        "temperature" => 0.1,
        "max_tokens" => 4000
    ];

    $response = openRouterCurlRequest($data);

    if (isset($response['error']))
    {
        return [
            "success" => false,
            "error" => $response['error'],
            "content" => ""
        ];
    }

    // Log the request
    if (function_exists('logOpenRouterRequest'))
    {
        logOpenRouterRequest("Content cleaning request", $response, $sModel);
    }

    $sCleanedContent = $response['choices'][0]['message']['content'] ?? "";

    // Handle reasoning models that put content in reasoning field
    if (empty($sCleanedContent))
    {
        $sCleanedContent = $response['choices'][0]['message']['reasoning'] ?? "";
    }

    if (empty($sCleanedContent))
    {
        return [
            "success" => false,
            "error" => "LLM returned empty response",
            "content" => ""
        ];
    }

    return [
        "success" => true,
        "content" => trim($sCleanedContent)
    ];
}

/**
 * Clean multiple scraped content items
 *
 * @param array $arrContent - Array of content, keyed by URL or identifier
 * @param string $sModel - The LLM model to use
 *
 * @return array - Array of cleaned content, keyed by URL
 */
function cleanScrapedContentMultiple($arrContent, $sModel = null)
{
    $arrResults = [];

    foreach ($arrContent as $sKey => $sRawContent)
    {
        if (!empty($sRawContent))
        {
            $arrResult = cleanScrapedContent($sRawContent, $sModel);
            $arrResults[$sKey] = $arrResult['success'] ? $arrResult['content'] : "";
        } else
        {
            $arrResults[$sKey] = "";
        }
    }

    return $arrResults;
}

/**
 * Get cached page content from database
 *
 * @param string $sURL - The URL to look up
 * @param mysqli $conn - Database connection
 *
 * @return string|null - Cached content or null if not found
 */
function getPageContentFromDB($sURL, $conn)
{
    $stmt = $conn->prepare("SELECT CONsText FROM pagecontent WHERE CONsURL = ?");
    $stmt->bind_param("s", $sURL);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0)
    {
        $row = $result->fetch_assoc();
        return $row['CONsText'];
    }

    return null;
}

/**
 * Save page content to database cache
 *
 * @param string $sURL - The URL
 * @param string $sContent - The page content
 * @param mysqli $conn - Database connection
 */
function savePageContentToDB($sURL, $sContent, $conn)
{
    // Use REPLACE to handle both insert and update
    $stmt = $conn->prepare("REPLACE INTO pagecontent (CONsURL, CONsText) VALUES (?, ?)");
    $stmt->bind_param("ss", $sURL, $sContent);
    $stmt->execute();
}

/**
 * Build HTTP headers for OpenRouter API requests
 *
 * @return array - HTTP headers
 */
function openRouterHeaders()
{
    return [
        "Authorization: Bearer " . OPEN_ROUTER_KEY,
        "Content-Type: application/json",
        "HTTP-Referer: " . (isset($_SERVER['HTTP_HOST']) ? "http://" . $_SERVER['HTTP_HOST'] : "http://localhost"),
        "X-Title: PHP-OpenRouter-Integration"
    ];
}

/**
 * Execute a cURL request to OpenRouter API
 *
 * @param array $data - Request data
 *
 * @return array - Decoded response
 */
function openRouterCurlRequest($data)
{
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, "https://openrouter.ai/api/v1/chat/completions");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 300);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, openRouterHeaders());

    $response = curl_exec($ch);

    if (curl_errno($ch))
    {
        return ["error" => "cURL Error: " . curl_error($ch)];
    }

    $arrResponse = json_decode($response, true);

    if (isset($arrResponse['error']))
    {
        return ["error" => "API Error: " . ($arrResponse['error']['message'] ?? json_encode($arrResponse['error']))];
    }

    if (!isset($arrResponse['choices']) || !is_array($arrResponse['choices']) || empty($arrResponse['choices']))
    {
        return ["error" => "Invalid API response format"];
    }

    return $arrResponse;
}

/**
 * Parse OpenRouter API response into simplified format
 *
 * @param array $arrResponse - Raw API response
 *
 * @return array - Parsed response with 'text', 'tokens', 'cost'
 */
function openRouterParseResponse($arrResponse)
{
    if (isset($arrResponse['error']))
    {
        return $arrResponse;
    }

    $sText = $arrResponse['choices'][0]['message']['content'] ?? "";
    $nPromptTokens = intval($arrResponse['usage']['prompt_tokens'] ?? 0);
    $nCompletionTokens = intval($arrResponse['usage']['completion_tokens'] ?? 0);
    $nTotalTokens = intval($arrResponse['usage']['total_tokens'] ?? 0);
    $fCost = floatval($arrResponse['usage']['cost'] ?? 0);

    // Include additional metadata if available
    $nCachedTokens = intval($arrResponse['usage']['prompt_tokens_details']['cached_tokens'] ?? 0);
    $nReasoningTokens = intval($arrResponse['usage']['completion_tokens_details']['reasoning_tokens'] ?? 0);

    return [
        "text" => $sText,
        "tokens" => [
            "prompt" => $nPromptTokens,
            "completion" => $nCompletionTokens,
            "total" => $nTotalTokens,
            "cached" => $nCachedTokens,
            "reasoning" => $nReasoningTokens
        ],
        "cost" => $fCost,
        "model" => $arrResponse['model'] ?? "",
        "finish_reason" => $arrResponse['choices'][0]['finish_reason'] ?? ""
    ];
}

/**
 * Log OpenRouter request and response
 * This function is called automatically if it exists in your codebase
 * Override this function in your application to store logs in your database
 *
 * @param string $sPrompt - User prompt
 * @param array $arrResponse - API response
 * @param string $sModel - Model used
 */
function logOpenRouterRequest($sPrompt, $arrResponse, $sModel)
{
    global $dNow;
    $connAI = getConnectionTRADING("ai");

    $sResponseText = $arrResponse['choices'][0]['message']['content'] ?? "";

    // Handle null/empty response text - check for reasoning models
    if (empty($sResponseText))
    {
        // Try reasoning field for models like deepseek-r1
        $sResponseText = $arrResponse['choices'][0]['message']['reasoning'] ?? "";
    }

    // Ensure not null for database
    if ($sResponseText === null)
    {
        $sResponseText = "";
    }

    // Handle null prompt
    if ($sPrompt === null || $sPrompt === "")
    {
        $sPrompt = "(empty)";
    }

    $nPromptTokens = intval($arrResponse['usage']['prompt_tokens'] ?? 0);
    $nCompletionTokens = intval($arrResponse['usage']['completion_tokens'] ?? 0);
    $nTotalTokens = intval($arrResponse['usage']['total_tokens'] ?? 0);
    $nCachedTokens = intval($arrResponse['usage']['prompt_tokens_details']['cached_tokens'] ?? 0);
    $nReasoningTokens = intval($arrResponse['usage']['completion_tokens_details']['reasoning_tokens'] ?? 0);
    $nCost = floatval($arrResponse['usage']['cost'] ?? 0);
    $sMetadata = json_encode($arrResponse);

    // Ensure response text is not empty for NOT NULL column
    if (empty($sResponseText))
    {
        $sResponseText = "(no content)";
    }

    $sSQL = "INSERT INTO ai_chat_history (ACHdDate, ACHsModel, ACHsUserMessage, ACHsAssistantMessage, ACHnPromptTokens, ACHnCachedTokens, ACHnCompletionTokens, ACHnReasoningTokens, ACHnTotalTokens, ACHnCost, ACHsMetadata) VALUES (" .
        " " . SQLit($dNow) .
        ", " . SQLit($sModel) .
        ", " . SQLit($sPrompt) .
        ", " . SQLit($sResponseText) .
        ", " . $nPromptTokens .
        ", " . $nCachedTokens .
        ", " . $nCompletionTokens .
        ", " . $nReasoningTokens .
        ", " . $nTotalTokens .
        ", " . $nCost .
        ", " . SQLit($sMetadata) .
        ") ";

    getData($sSQL, $connAI);
}

/**
 * ROLE: "user", "system", or "assistant"
 * - 'system': Sets global behavior/rules.
 * - 'user': The human input/prompt.
 * - 'assistant': AI history/context.
 */
function getContextJSON(string $markdownInput, string $role = 'user'): string
{
    // 1. Build the array structure specific to the OpenAI/OpenRouter schema
    $messageStructure = [
        'role' => $role,    // 'user', 'system', or 'assistant'
        'content' => "<context>$markdownInput</context>"
    ];

    return json_encode($messageStructure, JSON_UNESCAPED_SLASHES);
}
