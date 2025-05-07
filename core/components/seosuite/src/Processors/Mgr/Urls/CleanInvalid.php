<?php
namespace Sterc\SeoSuite\Processors\Mgr\Urls;

use MODX\Revolution\Processors\Processor;
use Sterc\SeoSuite\Model\SeoSuiteUrl;

/**
 * Processor to clean invalid 404 URLs (hack/break attempts).
 */
class CleanInvalid extends Processor
{
    /**
     * @access public.
     * @return Mixed.
     */
    public function process()
    {
        // Common patterns for hack/break attempts
        $hackPatterns = [
            // Admin paths
            '/wp-admin/',
            '/wp-login',
            '/administrator/',
            '/admin/',
            '/phpmyadmin/',
            '/pma/',
            '/myadmin/',
            '/mysql/',
            '/cpanel/',
            '/webmail/',
            
            // Common exploit paths
            '/wp-content/',
            '/wp-includes/',
            '/xmlrpc.php',
            '/shell',
            '/install',
            '/setup',
            '/config',
            '/xxx',
            '/health-check',
            
            // File extensions that shouldn't be directly accessed
            '.php',
            '.asp',
            '.aspx',
            '.jsp',
            '.cgi',
            '.env',
            '.git',
            '.sql',
            '.bak',
            '.old',
            '.backup',
            '.zip',
            '.tar',
            '.gz',
            '.rar',
            '.txt',
            '.owa',
            
            // Common exploit file names
            'config.php',
            'configuration.php',
            'wp-config.php',
            'phpinfo.php',
            'info.php',
            'test.php',
            'shell.php',
            'backdoor',
            'cmd',
            'command',
            'eval',
            
            // Common exploit parameters
            '?cmd=',
            '?exec=',
            '?system=',
            '?shell=',
            '?download=',
            '?upload=',
            '?file=',
            '?page=',
            '?id=',
            '?view=',
            '?path=',
            '?dir=',
            '?action=',
            '?option=',
            
            // SQL injection attempts
            "'",
            '"',
            ';',
            '-- ',
            '/*',
            '*/;',
            'UNION SELECT',
            'UNION ALL SELECT',
            'SELECT FROM',
            'INSERT INTO',
            'UPDATE SET',
            'DELETE FROM',
            
            // XSS attempts
            '<script',
            'javascript:',
            'onerror=',
            'onload=',
            'onclick=',
            'alert(',
            'String.fromCharCode',
            'eval(',
            'document.cookie',
            
            // Path traversal
            '../',
            '..\\',
            '/..',
            '\\..', 
            
            // Bot/crawler patterns
            '/feed/',
            '/rss/',
            '/sitemap',
            '/robots.txt',
            '/favicon.ico',
            
            // Other suspicious patterns
            'passwd',
            'password',
            'admin',
            'root',
            'hack',
            'crack',
            'exploit',
            'config',
            'conf',
            'cfg',
            'db',
            'database',
            'install',
            'setup',
            'default',
            'temp',
            'tmp',
            'backup',
            'log',
            'logs',
        ];
        
        // Get all URLs
        $urls = $this->modx->getCollection(SeoSuiteUrl::class);
        
        $totalUrls = count($urls);
        $removedUrls = 0;
        $removedUrlStrings = [];
        $allUrlStrings = [];
        
        foreach ($urls as $url) {
            $urlString = $url->get('url');
            $allUrlStrings[] = $urlString;
            $isHackAttempt = false;
            
            // Check if URL matches any hack pattern
            foreach ($hackPatterns as $pattern) {
                if ($urlString && stripos($urlString, $pattern) !== false) {
                    $isHackAttempt = true;
                    break;
                }
            }
            
            // Additional checks for suspicious URL characteristics
            
            // Check for excessive length (often used in DoS attempts)
            if (strlen($urlString) > 255) {
                $isHackAttempt = true;
            }
            
            // Check for unusual character sequences
            if (preg_match('/(%[0-9a-f]{2}){4,}/i', $urlString)) {
                $isHackAttempt = true;
            }
            
            // Check for base64 encoded content (often used to hide malicious code)
            if (preg_match('/[a-zA-Z0-9+\/=]{20,}/', $urlString)) {
                $isHackAttempt = true;
            }
            
            // Check for URLs that are only numeric
            if (preg_match('/^[0-9]+$/', trim($urlString, '/'))) {
                $isHackAttempt = true;
            }
            
            // Check for file extensions - delete all URLs with extensions except for .html
            if (preg_match('/\.([^\/\?]+)(\?|$)/', $urlString, $matches)) {
                $extension = strtolower($matches[1]);
                if ($extension !== 'html') {
                    $isHackAttempt = true;
                }
            }
            
            // Check for blocked words from SeoSuite config
            $blockedWords = $this->modx->getOption('seosuite.blocked_words', null, '');
            if (!empty($blockedWords)) {
                $blockedWordsArray = is_array($blockedWords) ? $blockedWords : explode(',', $blockedWords);
                foreach ($blockedWordsArray as $word) {
                    $word = trim($word);
                    if (!empty($word) && stripos($urlString, $word) !== false) {
                        $isHackAttempt = true;
                        break;
                    }
                }
            }
            
            // If URL is identified as a hack attempt, remove it
            if ($isHackAttempt) {
                $url->remove();
                $removedUrls++;
                $removedUrlStrings[] = $urlString;
            }
        }
        
        // Generate suggested blocked words based on all URLs with AI-like analysis
        $suggestedWords = $this->generateSuggestedBlockedWords($removedUrlStrings, $allUrlStrings);
        
        $response = [
            'message' => sprintf($this->modx->lexicon('seosuite.urls_cleaned'), $removedUrls, $totalUrls),
            'suggested_blocked_words' => $suggestedWords
        ];
        
        return $this->success($response['message'], $response);
    }
    
    /**
     * Generates a list of suggested blocked words based on AI-like analysis of URLs.
     * 
     * @param array $removedUrls Array of removed URL strings.
     * @param array $allUrls Array of all URL strings.
     * @return array Array of suggested blocked words.
     */
    private function generateSuggestedBlockedWords($removedUrls, $allUrls)
    {
        if (empty($allUrls)) {
            return [];
        }
        
        // Get current blocked words
        $currentBlockedWords = $this->modx->getOption('seosuite.blocked_words', null, '');
        $currentBlockedWordsArray = !empty($currentBlockedWords) 
            ? (is_array($currentBlockedWords) ? $currentBlockedWords : explode(',', $currentBlockedWords))
            : [];
            
        // Convert to lowercase and trim
        $currentBlockedWordsArray = array_map('trim', array_map('strtolower', $currentBlockedWordsArray));
        
        // Word analysis data
        $wordData = [];
        
        // Common words to ignore (stop words)
        $stopWords = ['a', 'an', 'the', 'and', 'or', 'but', 'in', 'on', 'at', 'to', 'for', 'with', 'by', 'about', 'as', 'of'];
        
        // Common legitimate URL segments in websites
        $commonLegitSegments = [
            'about', 'contact', 'services', 'products', 'blog', 'news', 'faq', 'help',
            'support', 'login', 'register', 'account', 'profile', 'settings', 'privacy',
            'terms', 'search', 'category', 'tag', 'author', 'page', 'post', 'article',
            'gallery', 'portfolio', 'shop', 'store', 'cart', 'checkout', 'payment',
            'download', 'upload', 'media', 'video', 'audio', 'image', 'photo', 'file',
            'docs', 'documentation', 'api', 'feed', 'rss', 'sitemap', 'home', 'index',
            'main', 'welcome', 'intro', 'events', 'calendar', 'schedule', 'booking',
            'reservation', 'contact-us', 'about-us', 'who-we-are', 'what-we-do',
            'our-team', 'careers', 'jobs', 'press', 'media', 'partners', 'affiliates',
            'resources', 'tools', 'community', 'forum', 'discussion', 'comments',
            'reviews', 'testimonials', 'clients', 'customers', 'projects', 'portfolio',
            'showcase', 'examples', 'demo', 'preview', 'sample', 'test', 'beta'
        ];
        
        // Get site structure to understand legitimate paths
        $siteStructure = $this->getSiteStructure();
        
        // Process all URLs to build a comprehensive word analysis
        foreach ($allUrls as $url) {
            // Remove common URL parts and split into segments
            $cleanUrl = preg_replace('/https?:\/\/|www\.|\?.*$|\.html$|\.php$|\.asp$|\.aspx$/', '', $url);
            $segments = preg_split('/[\/\-_\.\s]+/', $cleanUrl);
            
            // Check if this URL was removed
            $wasRemoved = in_array($url, $removedUrls);
            
            foreach ($segments as $segment) {
                $segment = strtolower(trim($segment));
                
                // Skip empty segments, very short words, numbers only, and stop words
                if (empty($segment) || strlen($segment) < 3 || is_numeric($segment) || in_array($segment, $stopWords)) {
                    continue;
                }
                
                // Skip if already in blocked words
                if (in_array($segment, $currentBlockedWordsArray)) {
                    continue;
                }
                
                // Initialize segment data if not exists
                if (!isset($wordData[$segment])) {
                    $wordData[$segment] = [
                        'total_count' => 0,
                        'removed_count' => 0,
                        'legitimate_count' => 0,
                        'suspicious_score' => 0
                    ];
                }
                
                // Update counts
                $wordData[$segment]['total_count']++;
                
                if ($wasRemoved) {
                    $wordData[$segment]['removed_count']++;
                } else {
                    $wordData[$segment]['legitimate_count']++;
                }
                
                // Calculate suspicious score based on multiple factors
                $suspiciousScore = 0;
                
                // Factor 1: Ratio of removed to legitimate occurrences
                if ($wordData[$segment]['total_count'] > 0) {
                    $removalRatio = $wordData[$segment]['removed_count'] / $wordData[$segment]['total_count'];
                    $suspiciousScore += $removalRatio * 5; // Scale up to make this a significant factor
                }
                
                // Factor 2: Check if segment is a common legitimate segment
                if (in_array($segment, $commonLegitSegments)) {
                    $suspiciousScore -= 2; // Reduce score for common legitimate segments
                }
                
                // Factor 3: Check if segment appears in site structure
                if (in_array($segment, $siteStructure)) {
                    $suspiciousScore -= 3; // Reduce score for segments that match site structure
                }
                
                // Factor 4: Check for suspicious patterns
                if (preg_match('/hack|crack|exploit|admin|config|shell|root|passwd|tmp/', $segment)) {
                    $suspiciousScore += 3; // Increase score for suspicious patterns
                }
                
                // Factor 5: Length-based heuristic (very long segments are often suspicious)
                if (strlen($segment) > 15) {
                    $suspiciousScore += 1;
                }
                
                // Factor 6: Character distribution (legitimate words have more natural distribution)
                $charCounts = count_chars($segment, 1);
                $uniqueChars = count($charCounts);
                $segmentLength = strlen($segment);
                
                if ($uniqueChars < $segmentLength * 0.5 && $segmentLength > 5) {
                    // Low character diversity can indicate suspicious patterns
                    $suspiciousScore += 1;
                }
                
                // Update the suspicious score
                $wordData[$segment]['suspicious_score'] = $suspiciousScore;
            }
        }
        
        // Sort by suspicious score (descending)
        uasort($wordData, function($a, $b) {
            return $b['suspicious_score'] <=> $a['suspicious_score'];
        });
        
        // Take top 10 most suspicious words
        $suggestedWords = array_slice(array_keys($wordData), 0, 10);
        
        return $suggestedWords;
    }
    
    /**
     * Gets the site structure by analyzing existing resources.
     * 
     * @return array Array of common segments in the site structure.
     */
    private function getSiteStructure()
    {
        $structure = [];
        
        // Get resources to analyze site structure
        $resources = $this->modx->getCollection('modResource', ['published' => 1]);
        
        foreach ($resources as $resource) {
            $uri = $resource->get('uri');
            if (!empty($uri)) {
                $segments = preg_split('/[\/\-_\.\s]+/', $uri);
                foreach ($segments as $segment) {
                    $segment = strtolower(trim($segment));
                    if (!empty($segment) && strlen($segment) >= 3 && !is_numeric($segment)) {
                        $structure[] = $segment;
                    }
                }
            }
            
            // Also add pagetitle and alias as they represent legitimate content
            $pagetitle = $resource->get('pagetitle');
            if (!empty($pagetitle)) {
                $segments = preg_split('/[\-_\.\s]+/', $pagetitle);
                foreach ($segments as $segment) {
                    $segment = strtolower(trim($segment));
                    if (!empty($segment) && strlen($segment) >= 3 && !is_numeric($segment)) {
                        $structure[] = $segment;
                    }
                }
            }
            
            $alias = $resource->get('alias');
            if (!empty($alias)) {
                $segments = preg_split('/[\-_\.\s]+/', $alias);
                foreach ($segments as $segment) {
                    $segment = strtolower(trim($segment));
                    if (!empty($segment) && strlen($segment) >= 3 && !is_numeric($segment)) {
                        $structure[] = $segment;
                    }
                }
            }
        }
        
        // Return unique segments
        return array_unique($structure);
    }
}
