<?php
namespace Sterc\SeoSuite\Processors\Mgr\Resource\AI;

use MODX\Revolution\Processors\Processor;
use MODX\Revolution\modResource;

class GenerateKeywords extends Processor
{
    /**
     * @access public.
     * @return Mixed.
     */
    public function initialize()
    {
        $this->modx->seosuite = $this->modx->services->get('seosuite');

        return parent::initialize();
    }

    /**
     * @access public.
     * @return Mixed.
     */
    public function process()
    {
        $resourceId = (int) $this->getProperty('id', 0);
        $content = $this->getProperty('content', '');
        $pagetitle = $this->getProperty('pagetitle', '');
        $longtitle = $this->getProperty('longtitle', '');
        
        // If we have a resource ID, try to get the content from the resource
        if ($resourceId > 0) {
            $resource = $this->modx->getObject(modResource::class, $resourceId);
            if ($resource) {
                $content = $resource->get('content');
                $pagetitle = $resource->get('pagetitle');
                $longtitle = $resource->get('longtitle');
            }
        }
        
        // If no content is provided, return an error
        if (empty($content)) {
            return $this->failure($this->modx->lexicon('seosuite.ai_error_no_content'));
        }
        
        // Get AI model type from system settings (free or openai)
        $aiModel = $this->modx->getOption('seosuite.ai_model', null, 'free');
        
        // If using OpenAI, check for API key
        if ($aiModel === 'openai') {
            $apiKey = $this->modx->getOption('seosuite.openai_api_key');
            
            // Check if API key is configured
            if (empty($apiKey)) {
                return $this->failure($this->modx->lexicon('seosuite.ai_error_no_api_key'));
            }
            
            // Generate keywords using OpenAI
            try {
                $keywords = $this->generateKeywordsWithOpenAI($content, $pagetitle, $longtitle, $apiKey);
                
                // Return the generated keywords
                return $this->success('', ['keywords' => $keywords]);
            } catch (\Exception $e) {
                // If OpenAI fails, fall back to the free model
                $this->modx->log(3, 'OpenAI API error: ' . $e->getMessage() . '. Falling back to free model.');
                $keywords = $this->generateKeywordsFree($content, $pagetitle, $longtitle);
                return $this->success('', ['keywords' => $keywords]);
            }
        } else {
            // Use the free model
            $keywords = $this->generateKeywordsFree($content, $pagetitle, $longtitle);
            return $this->success('', ['keywords' => $keywords]);
        }
    }
    
    /**
     * Generate keywords using OpenAI based on the content
     * 
     * @access protected
     * @param string $content The content to generate keywords from
     * @param string $pagetitle The page title
     * @param string $longtitle The long title
     * @param string $apiKey The OpenAI API key
     * @return string The generated keywords as a comma-separated string
     * @throws \Exception If there's an error with the API request
     */
    protected function generateKeywordsWithOpenAI($content, $pagetitle, $longtitle, $apiKey)
    {
        // Strip HTML tags and trim the content
        $cleanContent = strip_tags($content);
        $cleanContent = preg_replace('/\s+/', ' ', $cleanContent);
        $cleanContent = trim($cleanContent);
        
        // Limit content length to avoid processing too much text
        $maxContentLength = 1500;
        if (strlen($cleanContent) > $maxContentLength) {
            $cleanContent = substr($cleanContent, 0, $maxContentLength);
        }
        
        // Use the title as context
        $title = !empty($longtitle) ? $longtitle : $pagetitle;
        
        // Prepare the prompt for OpenAI
        $prompt = "Extract 3-5 relevant SEO focus keywords or short phrases from the following webpage content. Return only the keywords as a comma-separated list without numbering or additional text.\n\nTitle: $title\n\nContent: $cleanContent\n\nKeywords:";
        
        // Make the API request to OpenAI
        $response = $this->callOpenAI($prompt, $apiKey);
        
        // Extract the generated keywords from the response
        $keywords = trim($response);
        
        // Remove any numbering or bullet points that might be in the response
        $keywords = preg_replace('/^\d+\.\s*/', '', $keywords);
        $keywords = preg_replace('/^-\s*/', '', $keywords);
        
        return $keywords;
    }
    
    /**
     * Call the OpenAI API to generate text
     * 
     * @access protected
     * @param string $prompt The prompt to send to OpenAI
     * @param string $apiKey The OpenAI API key
     * @return string The generated text
     * @throws \Exception If there's an error with the API request
     */
    protected function callOpenAI($prompt, $apiKey)
    {
        // API endpoint
        $url = 'https://api.openai.com/v1/completions';
        
        // Request data
        $data = [
            'model' => 'gpt-3.5-turbo-instruct',
            'prompt' => $prompt,
            'max_tokens' => 100,
            'temperature' => 0.5,
            'top_p' => 1,
            'frequency_penalty' => 0.2,
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
    
    /**
     * Generate keywords using a free algorithm based on the content
     * 
     * @access protected
     * @param string $content The content to generate keywords from
     * @param string $pagetitle The page title
     * @param string $longtitle The long title
     * @return string The generated keywords as a comma-separated string
     */
    protected function generateKeywordsFree($content, $pagetitle, $longtitle)
    {
        // Strip HTML tags and trim the content
        $cleanContent = strip_tags($content);
        $cleanContent = preg_replace('/\s+/', ' ', $cleanContent);
        $cleanContent = trim($cleanContent);
        
        // Use the title as context
        $title = !empty($longtitle) ? $longtitle : $pagetitle;
        
        // Get stop words from SeoSuite
        $stopWords = $this->modx->seosuite->getExcludeWords();
        
        // Add common English stop words if the exclude words are empty
        if (empty($stopWords)) {
            $stopWords = ['a', 'an', 'the', 'and', 'or', 'but', 'if', 'then', 'else', 'when', 'at', 'from', 'by', 'for', 'with', 'about', 'to', 'in', 'on', 'of', 'is', 'are', 'was', 'were', 'be', 'been', 'being', 'have', 'has', 'had', 'do', 'does', 'did', 'will', 'would', 'shall', 'should', 'can', 'could', 'may', 'might', 'must', 'this', 'that', 'these', 'those', 'i', 'you', 'he', 'she', 'it', 'we', 'they', 'me', 'him', 'her', 'us', 'them', 'my', 'your', 'his', 'its', 'our', 'their', 'mine', 'yours', 'hers', 'ours', 'theirs'];
        }
        
        // Extract all words from content
        preg_match_all('/\b\w{3,}\b/i', strtolower($cleanContent), $matches);
        $words = $matches[0];
        
        // Count word frequency
        $wordCounts = array_count_values($words);
        
        // Remove stop words
        foreach ($stopWords as $stopWord) {
            if (isset($wordCounts[strtolower($stopWord)])) {
                unset($wordCounts[strtolower($stopWord)]);
            }
        }
        
        // Sort by frequency
        arsort($wordCounts);
        
        // Get top words
        $topWords = array_slice($wordCounts, 0, 10, true);
        
        // Extract words from title to prioritize
        preg_match_all('/\b\w{3,}\b/i', strtolower($title), $titleMatches);
        $titleWords = $titleMatches[0];
        
        // Filter out stop words from title words
        $titleWords = array_diff($titleWords, $stopWords);
        
        // Combine title words with top content words, prioritizing title words
        $keywords = array_unique(array_merge($titleWords, array_keys($topWords)));
        
        // Limit to 5 keywords
        $keywords = array_slice($keywords, 0, 5);
        
        // Return as comma-separated string
        return implode(', ', $keywords);
    }
}
