<?php
namespace Sterc\SeoSuite\Processors\Mgr\Urls\Suggestions;

use MODX\Revolution\Processors\Processor;
use MODX\Revolution\modResource;
use MODX\Revolution\modContext;
use MODX\Revolution\modContextSetting;
use Sterc\SeoSuite\Model\SeoSuiteUrl;
use Sterc\SeoSuite\Model\SeoSuiteSuggestion;
use Sterc\SeoSuite\Model\SeoSuiteRedirect;

/**
 * Processor to generate AI-based redirect suggestions for 404 URLs.
 */
class GenerateAI extends Processor
{
    /**
     * @access public.
     * @return Mixed.
     */
    public function process()
    {
        // Get parameters
        $urlIds = $this->getProperty('ids', '');
        $query = $this->getProperty('query', '');
        $createRedirects = (bool) $this->getProperty('create_redirects', false);
        $redirectType = $this->getProperty('redirect_type', '301');
        $aiModel = $this->modx->getOption('seosuite.ai_model', null, 'free');
        
        // If IDs are provided, use them
        if (!empty($urlIds)) {
            $ids = is_array($urlIds) ? $urlIds : explode(',', $urlIds);
            $urls = $this->modx->getCollection(SeoSuiteUrl::class, ['id:IN' => $ids]);
        } 
        // If a query is provided, filter URLs by the query
        else if (!empty($query)) {
            $c = $this->modx->newQuery(SeoSuiteUrl::class);
            $c->where(['url:LIKE' => '%' . $query . '%']);
            $urls = $this->modx->getCollection(SeoSuiteUrl::class, $c);
        } 
        // Otherwise, get all URLs
        else {
            $urls = $this->modx->getCollection(SeoSuiteUrl::class);
        }
        
        if (empty($urls)) {
            return $this->failure($this->modx->lexicon('seosuite.ai_error_no_urls'));
        }
        
        $results = [];
        $createdRedirects = 0;
        $totalUrls = count($urls);
        
        // Process each URL
        foreach ($urls as $url) {
            $urlString = $url->get('url');
            $suggestions = [];
            $contextKey = $url->get('context_key');
            
            // If context_key is empty, try to determine it from the URL domain
            if (empty($contextKey)) {
                $contextKey = $this->determineContextFromUrl($urlString);
                
                // If we found a context, update the URL object
                if (!empty($contextKey)) {
                    $url->set('context_key', $contextKey);
                    $url->save();
                }
            }
            
            // Check if this URL already has suggestions
            $existingSuggestions = $this->modx->getCollection(SeoSuiteSuggestion::class, ['url_id' => $url->get('id')]);
            
            if (!empty($existingSuggestions)) {
                $results[] = [
                    'id' => $url->get('id'),
                    'url' => $urlString,
                    'status' => 'existing'
                ];
                continue;
            }
            
            // Check if URL points to an error page
            if ($this->isErrorPage($urlString, $contextKey)) {
                $results[] = [
                    'id' => $url->get('id'),
                    'url' => $urlString,
                    'status' => 'error_page'
                ];
                continue;
            }
            
            // Get resources only for the current context to improve performance
            $contextResources = $this->getContextResources($contextKey);
            
            if (empty($contextResources)) {
                $results[] = [
                    'id' => $url->get('id'),
                    'url' => $urlString,
                    'status' => 'no_match'
                ];
                continue;
            }
            
            // Generate suggestions based on AI model
            if ($aiModel === 'openai') {
                $suggestions = $this->generateOpenAISuggestions($urlString, $contextResources);
            } else {
                $suggestions = $this->generateFreeSuggestions($urlString, $contextResources);
            }
            
            // If suggestions were found
            if (!empty($suggestions)) {
                $savedCount = 0;
                
                // Get error page IDs for all contexts to filter them out
                $errorPageIds = $this->getErrorPageIds();
                
                // Save suggestions to the database
                foreach ($suggestions as $resourceId => $score) {
                    // Skip error pages
                    if (in_array($resourceId, $errorPageIds)) {
                        continue;
                    }
                    
                    // Only save suggestions with a score of 60 or higher
                    if ($score >= 60) {
                        $suggestion = $this->modx->newObject(SeoSuiteSuggestion::class);
                        $suggestion->set('url_id', $url->get('id'));
                        $suggestion->set('resource_id', $resourceId);
                        $suggestion->set('score', $score);
                        $suggestion->save();
                        $savedCount++;
                    }
                }
                
                // If no suggestions were saved (all were below 60)
                if ($savedCount === 0) {
                    // Get the homepage ID for this context
                    $homepageId = $this->getContextHomepageId($contextKey);
                    
                    if ($homepageId) {
                        // Create a suggestion for the homepage with a lower score
                        $suggestion = $this->modx->newObject(SeoSuiteSuggestion::class);
                        $suggestion->set('url_id', $url->get('id'));
                        $suggestion->set('resource_id', $homepageId);
                        $suggestion->set('score', 50); // Lower score to indicate it's a fallback
                        $suggestion->save();
                        
                        // If create redirects is enabled, create a redirect to the homepage
                        if ($createRedirects) {
                            // Check if redirect already exists
                            $existingRedirect = $this->modx->getObject(SeoSuiteRedirect::class, [
                                'old_url' => $urlString
                            ]);
                            
                            if (!$existingRedirect) {
                                $redirect = $this->modx->newObject(SeoSuiteRedirect::class);
                                $redirect->set('old_url', $urlString);
                                $redirect->set('new_url', $homepageId);
                                $redirect->set('redirect_type', $redirectType);
                                $redirect->set('context_key', $contextKey);
                                $redirect->set('active', true);
                                
                                if ($redirect->save()) {
                                    $createdRedirects++;
                                }
                            }
                        }
                        
                        $results[] = [
                            'id' => $url->get('id'),
                            'url' => $urlString,
                            'status' => 'homepage_fallback'
                        ];
                    } else {
                        $results[] = [
                            'id' => $url->get('id'),
                            'url' => $urlString,
                            'status' => 'no_match'
                        ];
                    }
                    continue;
                }
                
                // Create redirects if requested
                if ($createRedirects && !empty($suggestions)) {
                    // Get the best match (highest score)
                    arsort($suggestions);
                    $bestMatch = array_key_first($suggestions);
                    
                    // Check if redirect already exists
                    $existingRedirect = $this->modx->getObject(SeoSuiteRedirect::class, [
                        'old_url' => $urlString
                    ]);
                    
                    if (!$existingRedirect) {
                        $redirect = $this->modx->newObject(SeoSuiteRedirect::class);
                        $redirect->set('old_url', $urlString);
                        $redirect->set('new_url', $bestMatch);
                        $redirect->set('redirect_type', $redirectType);
                        $redirect->set('context_key', $url->get('context_key'));
                        $redirect->set('active', true);
                        
                        if ($redirect->save()) {
                            $createdRedirects++;
                        }
                    }
                }
                
                $results[] = [
                    'id' => $url->get('id'),
                    'url' => $urlString,
                    'status' => 'success'
                ];
            } else {
                $results[] = [
                    'id' => $url->get('id'),
                    'url' => $urlString,
                    'status' => 'no_match'
                ];
            }
        }
        
        $message = $this->modx->lexicon('seosuite.ai_suggestions_generated', [
            'total' => $totalUrls,
            'success' => count(array_filter($results, function($item) { return $item['status'] === 'success'; })),
            'existing' => count(array_filter($results, function($item) { return $item['status'] === 'existing'; })),
            'no_match' => count(array_filter($results, function($item) { return $item['status'] === 'no_match'; })),
            'error_page' => count(array_filter($results, function($item) { return $item['status'] === 'error_page'; })),
            'homepage_fallback' => count(array_filter($results, function($item) { return $item['status'] === 'homepage_fallback'; })),
            'redirects' => $createdRedirects
        ]);
        
        return $this->success($message, [
            'results' => $results,
            'created_redirects' => $createdRedirects
        ]);
    }
    
    /**
     * Generate suggestions using OpenAI
     * 
     * @param string $url The 404 URL
     * @param array $contentIndex The content index
     * @return array Suggestions with scores
     */
    protected function generateOpenAISuggestions($url, $contentIndex)
    {
        $apiKey = $this->modx->getOption('seosuite.openai_api_key');
        
        if (empty($apiKey)) {
            return $this->generateFreeSuggestions($url, $contentIndex);
        }
        
        // Prepare content for analysis - limit to top 10 resources to reduce API calls
        $contentSamples = [];
        $count = 0;
        foreach ($contentIndex as $id => $resource) {
            // Only include a sample of the content to avoid token limits
            $contentSamples[] = [
                'id' => $id,
                'title' => $resource['pagetitle'],
                'uri' => $resource['uri']
            ];
            
            $count++;
            if ($count >= 10) break; // Limit to 10 resources
        }
        
        // Prepare the prompt for OpenAI - simplified for faster response
        $prompt = "Find the best matching page for 404 URL: \"$url\" from these pages:\n\n";
        
        foreach ($contentSamples as $sample) {
            $prompt .= "ID: {$sample['id']}, Title: {$sample['title']}, URI: {$sample['uri']}\n";
        }
        
        $prompt .= "\nReturn JSON: {\"best_match\": page_id, \"confidence\": score_0_to_1}";
        
        try {
            // Set a timeout to prevent long-running requests
            $response = $this->callOpenAI($prompt, $apiKey, 5); // 5 second timeout
            $data = json_decode($response, true);
            
            if (isset($data['best_match']) && isset($data['confidence'])) {
                $bestMatchId = $data['best_match'];
                $confidence = $data['confidence'];
                
                // Convert confidence to a score between 1-100
                $score = round($confidence * 100);
                
                // Return the suggestion with the score
                return [$bestMatchId => $score];
            }
        } catch (\Exception $e) {
            $this->modx->log(1, 'OpenAI API error: ' . $e->getMessage());
        }
        
        // Fall back to free suggestions if OpenAI fails
        return $this->generateFreeSuggestions($url, $contentIndex);
    }
    
    /**
     * Generate suggestions using a free algorithm
     * 
     * @param string $url The 404 URL
     * @param array $contentIndex The content index
     * @return array Suggestions with scores
     */
    protected function generateFreeSuggestions($url, $contentIndex)
    {
        $suggestions = [];
        $urlSegments = $this->getUrlSegments($url);
        
        // Get exclude words from SeoSuite config
        $excludeWords = $this->modx->getOption('seosuite.exclude_words', null, '');
        $excludeWordsArray = is_array($excludeWords) ? $excludeWords : explode(',', $excludeWords);
        
        // Clean up exclude words
        foreach ($excludeWordsArray as $key => $word) {
            $excludeWordsArray[$key] = trim(strtolower($word));
        }
        
        // Filter out common words and exclude words - limit to 5 most important words for performance
        $urlWords = [];
        foreach ($urlSegments as $segment) {
            $words = preg_split('/[^a-zA-Z0-9]/', strtolower($segment), -1, PREG_SPLIT_NO_EMPTY);
            foreach ($words as $word) {
                if (strlen($word) > 2 && !in_array($word, $excludeWordsArray)) {
                    $urlWords[] = $word;
                }
            }
        }
        
        // Limit to 5 most important words (longer words are usually more important)
        usort($urlWords, function($a, $b) {
            return strlen($b) - strlen($a);
        });
        $urlWords = array_slice($urlWords, 0, 5);
        
        // If no meaningful words found, return empty suggestions
        if (empty($urlWords)) {
            return [];
        }
        
        // Score each resource based on word matches - with early exit for performance
        foreach ($contentIndex as $id => $resource) {
            $score = 0;
            
            // Check URI/alias matches first (highest weight) - if we get a direct match, return immediately
            foreach ($urlWords as $word) {
                if (stripos($resource['uri'], $word) !== false || stripos($resource['alias'], $word) !== false) {
                    $score += 15;
                }
            }
            
            // Check title matches (high weight)
            foreach ($urlWords as $word) {
                if (stripos($resource['pagetitle'], $word) !== false) {
                    $score += 10;
                }
                if (!empty($resource['longtitle']) && stripos($resource['longtitle'], $word) !== false) {
                    $score += 8;
                }
            }
            
            // Only check content if we don't have a good match yet
            if ($score < 20) {
                // Check content matches (lower weight) - only if content is not too large
                if (isset($resource['content']) && strlen($resource['content']) < 10000) {
                    $contentText = strip_tags($resource['content']);
                    foreach ($urlWords as $word) {
                        if (stripos($contentText, $word) !== false) {
                            $score += 3; // Simplified scoring
                        }
                    }
                }
                
                // Check description matches
                if (!empty($resource['description'])) {
                    foreach ($urlWords as $word) {
                        if (stripos($resource['description'], $word) !== false) {
                            $score += 5;
                        }
                    }
                }
            }
            
            // Only include resources with a minimum score
            if ($score >= 10) {
                $suggestions[$id] = $score;
            }
        }
        
        // Sort by score (highest first)
        arsort($suggestions);
        
        // Return top 30 suggestions
        return array_slice($suggestions, 0, 30, true);
    }
    
    /**
     * Split a URL into segments
     * 
     * @param string $url The URL to split
     * @return array URL segments
     */
    protected function getUrlSegments($url)
    {
        // Check if URL is only numeric
        if (preg_match('/^[0-9]+$/', trim($url, '/'))) {
            return []; // Return empty array to skip processing
        }
        
        // Remove query string
        $url = strtok($url, '?');
        
        // Remove file extension
        $url = preg_replace('/\.(html|php|aspx?|jsp)$/i', '', $url);
        
        // Split by slashes
        $segments = explode('/', trim($url, '/'));
        
        // Filter out empty segments
        return array_filter($segments);
    }
    
    /**
     * Get resources for a specific context
     * 
     * @param string $contextKey The context key
     * @return array Resources indexed by ID
     */
    protected function getContextResources($contextKey)
    {
        // Check if we have a cached version of the resources for this context
        $cacheKey = 'seosuite_resources_' . $contextKey;
        $cache = $this->modx->cacheManager->get($cacheKey);
        
        if ($cache) {
            return $cache;
        }
        
        // Get resources only for the specified context
        $resources = $this->modx->getCollection(modResource::class, [
            'published' => 1,
            'deleted' => 0,
            'context_key' => $contextKey
        ]);
        
        if (empty($resources)) {
            return [];
        }
        
        // Build a content index for faster searching - with optimized content handling
        $contentIndex = [];
        foreach ($resources as $resource) {
            // Only store a truncated version of the content to save memory and processing time
            $content = $resource->get('content');
            $contentSample = '';
            
            // Only process content if it's not too large (skip very large content)
            if (strlen($content) < 50000) {
                // Extract only the first 1000 characters of content
                $contentSample = substr(strip_tags($content), 0, 1000);
            }
            
            $contentIndex[$resource->get('id')] = [
                'id' => $resource->get('id'),
                'pagetitle' => $resource->get('pagetitle'),
                'longtitle' => $resource->get('longtitle'),
                'description' => substr($resource->get('description'), 0, 500),
                'content' => $contentSample,
                'alias' => $resource->get('alias'),
                'uri' => $resource->get('uri'),
                'context_key' => $resource->get('context_key')
            ];
        }
        
        // Cache the result for 1 hour to improve performance for future requests
        $this->modx->cacheManager->set($cacheKey, $contentIndex, 3600);
        
        return $contentIndex;
    }
    
    /**
     * Get the homepage ID for a specific context
     * 
     * @param string $contextKey The context key
     * @return int|null The homepage ID or null if not found
     */
    protected function getContextHomepageId($contextKey)
    {
        // Try to get the site_start setting for the context
        $siteStartId = 0;
        $contextSetting = $this->modx->getObject(modContextSetting::class, [
            'key' => 'site_start',
            'context_key' => $contextKey
        ]);
        
        if ($contextSetting) {
            $siteStartId = (int) $contextSetting->get('value');
            
            if ($siteStartId > 0) {
                // Check if the resource exists and is published
                $homepage = $this->modx->getObject(modResource::class, [
                    'id' => $siteStartId,
                    'published' => 1,
                    'deleted' => 0
                ]);
                
                if ($homepage) {
                    return $siteStartId;
                }
            }
        }
        
        // If no site_start setting or the resource doesn't exist, try to find the homepage by URI
        $homepage = $this->modx->getObject(modResource::class, [
            'uri' => '',
            'context_key' => $contextKey,
            'published' => 1,
            'deleted' => 0
        ]);
        
        if ($homepage) {
            return $homepage->get('id');
        }
        
        // If still not found, get the first resource in the context
        $c = $this->modx->newQuery(modResource::class);
        $c->where([
            'context_key' => $contextKey,
            'published' => 1,
            'deleted' => 0
        ]);
        $c->sortby('menuindex', 'ASC');
        $c->limit(1);
        
        $firstResource = $this->modx->getObject(modResource::class, $c);
        
        if ($firstResource) {
            return $firstResource->get('id');
        }
        
        return null;
    }
    
    /**
     * Get all error page IDs from all contexts
     * 
     * @return array Array of error page IDs
     */
    protected function getErrorPageIds()
    {
        $errorPageIds = [];
        
        // Get global error page ID
        $globalErrorPageId = 0;
        $globalSetting = $this->modx->getOption('error_page');
        
        if ($globalSetting) {
            $errorPageIds[] = $globalSetting;
        }
        
        // Get all contexts
        $contexts = $this->modx->getCollection(modContext::class);
        
        foreach ($contexts as $context) {
            $contextKey = $context->get('key');
            
            // Skip 'mgr' context
            if ($contextKey === 'mgr') {
                continue;
            }
            
            // Get error page ID for this context
            $contextSetting = $this->modx->getObject(modContextSetting::class, [
                'key' => 'error_page',
                'context_key' => $contextKey
            ]);
            
            if ($contextSetting) {
                $contextErrorPageId = (int) $contextSetting->get('value');
                
                if ($contextErrorPageId > 0 && !in_array($contextErrorPageId, $errorPageIds)) {
                    $errorPageIds[] = $contextErrorPageId;
                }
            }
        }
        
        return $errorPageIds;
    }
    
    /**
     * Check if a URL points to an error page
     * 
     * @param string $url The URL to check
     * @param string $contextKey The context key
     * @return bool True if the URL points to an error page, false otherwise
     */
    protected function isErrorPage($url, $contextKey)
    {
        // Get the error page ID for the context
        $errorPageId = 0;
        
        // First check if there's a context-specific error page
        $contextSetting = $this->modx->getObject(modContextSetting::class, [
            'key' => 'error_page',
            'context_key' => $contextKey
        ]);
        
        if ($contextSetting) {
            $errorPageId = (int) $contextSetting->get('value');
        } else {
            // If no context-specific setting, get the global error page
            $errorPageId = $this->modx->getOption('error_page');
        }
        
        if ($errorPageId === 0) {
            return false;
        }
        
        // Get the error page resource
        $errorPage = $this->modx->getObject('modResource', $errorPageId);
        
        if (!$errorPage) {
            return false;
        }
        
        // Get the URI of the error page
        $errorPageUri = $errorPage->get('uri');
        
        // Clean the URL for comparison
        $cleanUrl = trim($url, '/');
        
        // If the URL matches the error page URI, it's an error page
        if ($cleanUrl === $errorPageUri) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Determine the context key from a URL by matching the domain with site_url settings
     * 
     * @param string $url The URL to check
     * @return string|null The context key or null if not found
     */
    protected function determineContextFromUrl($url)
    {
        // Extract domain from URL
        $domain = '';
        if (preg_match('/^(?:https?:\/\/)?([^\/]+)/', $url, $matches)) {
            $domain = $matches[1];
        } else {
            // If URL doesn't have a domain, we can't determine the context
            return null;
        }
        
        // Get all contexts
        $contexts = $this->modx->getCollection(modContext::class);
        
        foreach ($contexts as $context) {
            $contextKey = $context->get('key');
            
            // Skip 'mgr' context
            if ($contextKey === 'mgr') {
                continue;
            }
            
            // Get site_url setting for this context
            $contextSetting = $this->modx->getObject(modContextSetting::class, [
                'key' => 'site_url',
                'context_key' => $contextKey
            ]);
            
            if ($contextSetting) {
                // Get the value of the site_url setting
                $siteUrl = $contextSetting->get('value');
            }

            // Extract domain from site_url
            $siteUrlDomain = '';
            if (preg_match('/^(?:https?:\/\/)?([^\/]+)/', $siteUrl, $matches)) {
                $siteUrlDomain = $matches[1];
            }
            
            // If domains match, return this context key
            if (!empty($siteUrlDomain) && $siteUrlDomain === $domain) {
                return $contextKey;
            }
        }
        
        return null;
    }
    
    /**
     * Call the OpenAI API
     * 
     * @param string $prompt The prompt to send
     * @param string $apiKey The OpenAI API key
     * @param int $timeout Timeout in seconds
     * @return string The response
     * @throws \Exception If there's an error with the API request
     */
    protected function callOpenAI($prompt, $apiKey, $timeout = 10)
    {
        // API endpoint
        $url = 'https://api.openai.com/v1/completions';
        
        // Request data - simplified for faster response
        $data = [
            'model' => 'gpt-3.5-turbo-instruct',
            'prompt' => $prompt,
            'max_tokens' => 150, // Reduced from 300
            'temperature' => 0.3, // Reduced from 0.5 for more deterministic responses
            'top_p' => 1,
            'frequency_penalty' => 0,
            'presence_penalty' => 0
        ];
        
        // Initialize cURL
        $ch = curl_init($url);
        
        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout); // Add timeout
        
        // Execute the request
        $response = curl_exec($ch);
        
        // Check for errors
        if (curl_errno($ch)) {
            throw new \Exception('cURL error: ' . curl_error($ch));
        }
        
        // Get HTTP status code
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        // Close cURL
        curl_close($ch);
        
        // Check if the request was successful
        if ($httpCode !== 200) {
            $responseData = json_decode($response, true);
            $errorMessage = isset($responseData['error']['message']) 
                ? $responseData['error']['message'] 
                : 'HTTP error: ' . $httpCode;
            
            throw new \Exception($errorMessage);
        }
        
        // Decode the response
        $responseData = json_decode($response, true);
        
        // Check if the response contains the expected data
        if (!isset($responseData['choices'][0]['text'])) {
            throw new \Exception('Unexpected response format from OpenAI API');
        }
        
        // Return the generated text
        return $responseData['choices'][0]['text'];
    }
}
