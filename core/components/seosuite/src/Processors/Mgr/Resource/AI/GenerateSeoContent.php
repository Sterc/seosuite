<?php
namespace Sterc\SeoSuite\Processors\Mgr\Resource\AI;

use MODX\Revolution\Processors\Processor;
use MODX\Revolution\modResource;
use MODX\Revolution\modContextSetting;

class GenerateSeoContent extends Processor
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
        $language = 'en'; // Default language
        
        // If we have a resource ID, try to get the content from the resource
        if ($resourceId > 0) {
            $resource = $this->modx->getObject(modResource::class, $resourceId);
            if ($resource) {
                $content = $resource->get('content');
                $pagetitle = $resource->get('pagetitle');
                $longtitle = $resource->get('longtitle');
                
                // Get the context of the resource to determine its language
                $contextKey = $resource->get('context_key');
                if ($contextKey) {
                    // Get the cultureKey setting for this context
                    $cultureKey = $this->modx->getOption('cultureKey', null, 'en', ['context' => $contextKey]);

                    $ctx = $this->modx->getObject(modContextSetting::class, [
                        'key' => 'cultureKey',
                        'context_key' => $contextKey
                    ]);

                    if ($ctx) {
                        $cultureKey = $ctx->get('value');
                    }
            
                    if (!empty($cultureKey)) {
                        $language = $cultureKey;
                    }
                }
            }
        }
        
        // If no content is provided, return an error
        if (empty($content)) {
            return $this->failure($this->modx->lexicon('seosuite.ai_error_no_content'));
        }
        
        // Get AI model type from system settings (free or openai)
        $aiModel = $this->modx->getOption('seosuite.ai_model', null, 'free');
        
        // Initialize results array
        $results = [
            'meta_description' => '',
            'keywords' => '',
            'language' => $language // Store the detected language for reference
        ];
        
        // If using OpenAI, check for API key
        if ($aiModel === 'openai') {
            $apiKey = $this->modx->getOption('seosuite.openai_api_key');
            
            // Check if API key is configured
            if (empty($apiKey)) {
                return $this->failure($this->modx->lexicon('seosuite.ai_error_no_api_key'));
            }
            
            // Generate content using OpenAI
            try {
                // Generate meta description
                $results['meta_description'] = $this->generateMetaDescriptionWithOpenAI($content, $pagetitle, $longtitle, $apiKey, $language);
                
                // Generate keywords
                $results['keywords'] = $this->generateKeywordsWithOpenAI($content, $pagetitle, $longtitle, $apiKey, $language);
                
                // Return the generated content
                return $this->success('', $results);
            } catch (\Exception $e) {
                // If OpenAI fails, fall back to the free model
                $this->modx->log(3, 'OpenAI API error: ' . $e->getMessage() . '. Falling back to free model.');
                $results['meta_description'] = $this->generateMetaDescriptionFree($content, $pagetitle, $longtitle, $language);
                $results['keywords'] = $this->generateKeywordsFree($content, $pagetitle, $longtitle, $language);
                return $this->success('', $results);
            }
        } else {
            // Use the free model
            $results['meta_description'] = $this->generateMetaDescriptionFree($content, $pagetitle, $longtitle, $language);
            $results['keywords'] = $this->generateKeywordsFree($content, $pagetitle, $longtitle, $language);
            return $this->success('', $results);
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
    protected function generateMetaDescriptionWithOpenAI($content, $pagetitle, $longtitle, $apiKey, $language = 'en')
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
        
        // Prepare the prompt for OpenAI with language specification
        $prompt = $this->modx->getOption('seosuite.ai_prompt_meta_description', null, 'Generate a concise and engaging meta description in the language for a webpage with the following title and content. The meta description should be under 160 characters and accurately summarize the page content.');
        $prompt.= "The response MUST be in the $language language.\n\nTitle: $title\n\nContent: $cleanContent\n\nMeta Description in $language:";

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
    protected function generateKeywordsWithOpenAI($content, $pagetitle, $longtitle, $apiKey, $language = 'en')
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
        
        // Prepare the prompt for OpenAI with language specification
        $prompt = $this->modx->getOption('seosuite.ai_prompt_meta_keywords', null, 'Extract 3-5 relevant SEO focus keywords or short phrases in the language from the following webpage content. Return only the keywords as a comma-separated list without numbering or additional text.');
        $prompt.= "The keywords MUST be in the $language language.\n\nTitle: $title\n\nContent: $cleanContent\n\nKeywords in $language:";         
        // Make the API request to OpenAI
        $response = $this->callOpenAI($prompt, $apiKey);
        
        // Extract the generated keywords from the response
        $keywords = trim($response);
        
        // Remove any numbering or bullet points that might be in the response
        $keywords = preg_replace('/^\d+\.\s*/', '', $keywords);
        $keywords = preg_replace('/^-\s*/', '', $keywords);
        
        return html_entity_decode($keywords);
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
    protected function generateMetaDescriptionFree($content, $pagetitle, $longtitle, $language = 'en')
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
    
    /**
     * Generate keywords using a free algorithm based on the content
     * 
     * @access protected
     * @param string $content The content to generate keywords from
     * @param string $pagetitle The page title
     * @param string $longtitle The long title
     * @return string The generated keywords as a comma-separated string
     */
    protected function generateKeywordsFree($content, $pagetitle, $longtitle, $language = 'en')
    {
        // Strip HTML tags and trim the content
        $cleanContent = strip_tags($content);
        $cleanContent = preg_replace('/\s+/', ' ', $cleanContent);
        $cleanContent = trim($cleanContent);
        
        // Use the title as context
        $title = !empty($longtitle) ? $longtitle : $pagetitle;
        
        // Get stop words from SeoSuite
        $stopWords = $this->modx->seosuite->getExcludeWords();
        
        // Add language-specific stop words if the exclude words are empty
        if (empty($stopWords)) {
            // Default to English stop words
            $stopWords = ['a', 'an', 'the', 'and', 'or', 'but', 'if', 'then', 'else', 'when', 'at', 'from', 'by', 'for', 'with', 'about', 'to', 'in', 'on', 'of', 'is', 'are', 'was', 'were', 'be', 'been', 'being', 'have', 'has', 'had', 'do', 'does', 'did', 'will', 'would', 'shall', 'should', 'can', 'could', 'may', 'might', 'must', 'this', 'that', 'these', 'those', 'i', 'you', 'he', 'she', 'it', 'we', 'they', 'me', 'him', 'her', 'us', 'them', 'my', 'your', 'his', 'its', 'our', 'their', 'mine', 'yours', 'hers', 'ours', 'theirs'];
            
            // Add language-specific stop words based on the detected language
            switch ($language) {
                case 'nl':
                    // Dutch stop words
                    $stopWords = array_merge($stopWords, ['de', 'het', 'een', 'en', 'van', 'ik', 'te', 'dat', 'die', 'in', 'is', 'het', 'op', 'zijn', 'met', 'voor', 'als', 'er', 'door', 'ze', 'naar', 'maar', 'dan', 'ons', 'nog', 'over', 'tot', 'bij', 'ook', 'mijn', 'uit', 'wel', 'nu', 'om', 'zo', 'deze', 'aan']);
                    break;
                case 'de':
                    // German stop words
                    $stopWords = array_merge($stopWords, ['der', 'die', 'das', 'und', 'in', 'zu', 'den', 'mit', 'auf', 'für', 'von', 'im', 'nicht', 'ein', 'eine', 'dem', 'sich', 'ist', 'des', 'sie', 'ich', 'dass', 'es', 'wie', 'auch', 'als', 'bei', 'wird', 'oder', 'aus', 'an', 'nach', 'so', 'zum', 'kann', 'nur', 'einen', 'über']);
                    break;
                case 'fr':
                    // French stop words
                    $stopWords = array_merge($stopWords, ['le', 'la', 'les', 'un', 'une', 'des', 'et', 'en', 'de', 'à', 'que', 'qui', 'dans', 'par', 'sur', 'pour', 'avec', 'ce', 'il', 'elle', 'je', 'tu', 'nous', 'vous', 'ils', 'elles', 'mon', 'ton', 'son', 'ma', 'ta', 'sa', 'mes', 'tes', 'ses', 'notre', 'votre', 'leur']);
                    break;
                case 'es':
                    // Spanish stop words
                    $stopWords = array_merge($stopWords, ['el', 'la', 'los', 'las', 'un', 'una', 'unos', 'unas', 'y', 'o', 'pero', 'si', 'de', 'del', 'a', 'al', 'en', 'para', 'por', 'con', 'mi', 'tu', 'su', 'nuestro', 'vuestro', 'este', 'ese', 'aquel', 'yo', 'tu', 'él', 'ella', 'nosotros', 'vosotros', 'ellos', 'ellas']);
                    break;
                case 'ru':
                    // Russian stop words
                    $stopWords = array_merge($stopWords, ['и', 'в', 'во', 'не', 'что', 'он', 'на', 'я', 'с', 'со', 'как', 'а', 'то', 'все', 'она', 'так', 'его', 'но', 'да', 'ты', 'к', 'у', 'же', 'вы', 'за', 'бы', 'по', 'только', 'ее', 'мне', 'было', 'вот', 'от', 'меня', 'еще', 'нет', 'о', 'из', 'ему']);
                    break;
                // Add more languages as needed
            }
            
            // Remove duplicates
            $stopWords = array_unique($stopWords);
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
