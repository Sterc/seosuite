<?php
namespace Sterc\SeoSuite\Processors\Mgr\Resource\AI;

use MODX\Revolution\Processors\Processor;
use MODX\Revolution\modResource;

class GenerateMetaDescription extends Processor
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
        if ($aiModel === 'OpenAI') {
            $apiKey = $this->modx->getOption('seosuite.openai_api_key');
            
            // Check if API key is configured
            if (empty($apiKey)) {
                return $this->failure($this->modx->lexicon('seosuite.ai_error_no_api_key'));
            }
            
            // Generate meta description using OpenAI
            try {
                $metaDescription = $this->generateMetaDescriptionWithOpenAI($content, $pagetitle, $longtitle, $apiKey);
                
                // Return the generated meta description
                return $this->success('', ['meta_description' => $metaDescription]);
            } catch (\Exception $e) {
                // If OpenAI fails, fall back to the free model
                $this->modx->log(3, 'OpenAI API error: ' . $e->getMessage() . '. Falling back to free model.');
                $metaDescription = $this->generateMetaDescriptionFree($content, $pagetitle, $longtitle);
                return $this->success('', ['meta_description' => $metaDescription]);
            }
        } else {
            // Use the free model
            $metaDescription = $this->generateMetaDescriptionFree($content, $pagetitle, $longtitle);
            return $this->success('', ['meta_description' => $metaDescription]);
        }
    }
    
    /**
     * Generate a meta description using OpenAI based on the content
     * 
     * @access protected
     * @param string $content The content to generate a meta description from
     * @param string $pagetitle The page title
     * @param string $longtitle The long title
     * @param string $apiKey The OpenAI API key
     * @return string The generated meta description
     * @throws \Exception If there's an error with the API request
     */
    protected function generateMetaDescriptionWithOpenAI($content, $pagetitle, $longtitle, $apiKey)
    {
        // Strip HTML tags and trim the content
        $cleanContent = strip_tags($content);
        $cleanContent = preg_replace('/\s+/', ' ', $cleanContent);
        $cleanContent = trim($cleanContent);
        
        // Limit content length to avoid processing too much text
        $maxContentLength = 1000;
        if (strlen($cleanContent) > $maxContentLength) {
            $cleanContent = substr($cleanContent, 0, $maxContentLength);
        }
        
        // Use the title as context
        $title = !empty($longtitle) ? $longtitle : $pagetitle;
        
        // Prepare the prompt for OpenAI
        $prompt = "Generate a concise and engaging meta description for a webpage with the following title and content. The meta description should be under 160 characters and accurately summarize the page content.\n\nTitle: $title\n\nContent: $cleanContent\n\nMeta Description:";
        
        // Make the API request to OpenAI
        $response = $this->callOpenAI($prompt, $apiKey);
        
        // Extract the generated meta description from the response
        $metaDescription = trim($response);
        
        // Ensure the description is not too long (max 160 characters)
        $maxLength = 160;
        if (strlen($metaDescription) > $maxLength) {
            $metaDescription = substr($metaDescription, 0, strrpos(substr($metaDescription, 0, $maxLength), ' ')) . '...';
        }
        
        return $metaDescription;
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
            'max_tokens' => 200,
            'temperature' => 0.7,
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
     * Generate a meta description using a free algorithm based on the content
     * 
     * @access protected
     * @param string $content The content to generate a meta description from
     * @param string $pagetitle The page title
     * @param string $longtitle The long title
     * @return string The generated meta description
     */
    protected function generateMetaDescriptionFree($content, $pagetitle, $longtitle)
    {
        // Strip HTML tags and trim the content
        $cleanContent = strip_tags($content);
        $cleanContent = preg_replace('/\s+/', ' ', $cleanContent);
        $cleanContent = trim($cleanContent);
        
        // Limit content length to avoid processing too much text
        $maxContentLength = 1000;
        if (strlen($cleanContent) > $maxContentLength) {
            $cleanContent = substr($cleanContent, 0, $maxContentLength);
        }
        
        // Use the title as context
        $title = !empty($longtitle) ? $longtitle : $pagetitle;
        
        // Simple approach: Extract the first sentence that contains important keywords from the title
        $sentences = preg_split('/(?<=[.!?])\s+/', $cleanContent, -1, PREG_SPLIT_NO_EMPTY);
        
        // If no sentences found, return a default message
        if (empty($sentences)) {
            return "Learn more about " . $title . " on our website.";
        }
        
        // Find the most relevant sentence (containing keywords from the title)
        $titleWords = preg_split('/\s+/', strtolower($title), -1, PREG_SPLIT_NO_EMPTY);
        $titleWords = array_filter($titleWords, function($word) {
            return strlen($word) > 3; // Only consider words longer than 3 characters
        });
        
        $bestSentence = '';
        $highestScore = 0;
        
        foreach ($sentences as $sentence) {
            $score = 0;
            $sentenceLower = strtolower($sentence);
            
            foreach ($titleWords as $word) {
                if (strpos($sentenceLower, $word) !== false) {
                    $score++;
                }
            }
            
            // Prefer sentences of appropriate length for meta descriptions (50-160 chars)
            $length = strlen($sentence);
            if ($length >= 50 && $length <= 160) {
                $score += 2;
            }
            
            if ($score > $highestScore) {
                $highestScore = $score;
                $bestSentence = $sentence;
            }
            
            // If we found a good match, no need to check all sentences
            if ($score >= count($titleWords) / 2 + 2) {
                break;
            }
        }
        
        // If no good sentence found, use the first sentence
        if (empty($bestSentence)) {
            $bestSentence = $sentences[0];
        }
        
        // Ensure the description is not too long (max 160 characters)
        $maxLength = 160;
        if (strlen($bestSentence) > $maxLength) {
            $bestSentence = substr($bestSentence, 0, strrpos(substr($bestSentence, 0, $maxLength), ' ')) . '...';
        }
        
        return $bestSentence;
    }
}
