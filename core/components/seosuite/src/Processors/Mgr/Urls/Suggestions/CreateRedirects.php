<?php
namespace Sterc\SeoSuite\Processors\Mgr\Urls\Suggestions;

use MODX\Revolution\Processors\Processor;
use Sterc\SeoSuite\Model\SeoSuiteUrl;
use Sterc\SeoSuite\Model\SeoSuiteSuggestion;
use Sterc\SeoSuite\Model\SeoSuiteRedirect;

/**
 * Processor to create redirects from suggestions.
 */
class CreateRedirects extends Processor
{
    /**
     * @access public.
     * @return Mixed.
     */
    public function process()
    {
        $ids = $this->getProperty('ids', '');
        $urlId = (int) $this->getProperty('url_id', 0);
        $urlString = $this->getProperty('url_string', '');
        $contextKey = $this->getProperty('context_key', '');
        
        if (empty($urlId) || empty($urlString)) {
            return $this->failure($this->modx->lexicon('seosuite.error_no_url'));
        }
        
        // Get the URL object
        $url = $this->modx->getObject(SeoSuiteUrl::class, $urlId);
        if (!$url) {
            return $this->failure($this->modx->lexicon('seosuite.error_url_not_found'));
        }
        
        // Get suggestions to create redirects for
        $suggestions = [];
        
        if ($ids === 'all') {
            // Get all suggestions for this URL
            $suggestionObjects = $this->modx->getCollection(SeoSuiteSuggestion::class, [
                'url_id' => $urlId
            ]);
            
            foreach ($suggestionObjects as $suggestion) {
                $suggestions[$suggestion->get('resource_id')] = $suggestion->get('score');
            }
        } else {
            // Get specific suggestions
            $suggestionIds = is_array($ids) ? $ids : explode(',', $ids);
            
            if (empty($suggestionIds)) {
                return $this->failure($this->modx->lexicon('seosuite.error_no_suggestions_selected'));
            }
            
            $suggestionObjects = $this->modx->getCollection(SeoSuiteSuggestion::class, [
                'id:IN' => $suggestionIds
            ]);
            
            foreach ($suggestionObjects as $suggestion) {
                $suggestions[$suggestion->get('resource_id')] = $suggestion->get('score');
            }
        }
        
        if (empty($suggestions)) {
            return $this->failure($this->modx->lexicon('seosuite.error_no_suggestions'));
        }
        
        // Create redirects
        $created = 0;
        $existing = 0;
        $total = count($suggestions);
        
        foreach ($suggestions as $resourceId => $score) {
            // Check if redirect already exists
            $existingRedirect = $this->modx->getObject(SeoSuiteRedirect::class, [
                'old_url' => $urlString
            ]);
            
            if ($existingRedirect) {
                $existing++;
                continue;
            }
            
            // Create new redirect
            $redirect = $this->modx->newObject(SeoSuiteRedirect::class);
            $redirect->set('old_url', $urlString);
            $redirect->set('new_url', $resourceId);
            $redirect->set('redirect_type', 'HTTP/1.1 301 Moved Permanently');
            $redirect->set('context_key', $contextKey);
            $redirect->set('active', true);
            
            if ($redirect->save()) {
                $created++;
            }
        }
        
        return $this->success($this->modx->lexicon('seosuite.redirects_created', [
            'created' => $created,
            'total' => $total,
            'existing' => $existing
        ]));
    }
}
