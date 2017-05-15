<?php
/**
 * Find redirect suggestions for a 404 url
 *
 * @package seosuite
 * @subpackage processors
 */

class SeoSuiteFindSuggestionProcessor extends modObjectUpdateProcessor
{
    public $classKey = 'SeoSuiteUrl';
    public $languageTopics = array('seosuite:default');

    public function beforeSet()
    {
        $url = $this->getProperty('url');
        $siteUrls = false;
        if ($this->getProperty('match_site_url')) {
            $siteUrls = $this->modx->seosuite->getSiteUrls();
        }
        $redirect_to      = 0;
        $solved           = 0;
        $redirect_handler = 0;
        $findSuggestions  = $this->modx->seosuite->findRedirectSuggestions($url, $siteUrls);
        if (count($findSuggestions)) {
            if (count($findSuggestions) === 1) {
                $redirect_to = $findSuggestions[0];
                $solved = 1;

                if (!$this->modx->seosuite->checkSeoTab()) {
                    $redirect_handler = 1;
                } else {
                    $this->modx->seosuite->addSeoTabRedirect($url, $findSuggestions[0]);
                }
            }
        }
        $this->setProperty('redirect_to', $redirect_to);
        $this->setProperty('solved', $solved);
        $this->setProperty('redirect_handler', $redirect_handler);
        $this->setProperty('suggestions', $findSuggestions);

        return parent::beforeSet();
    }
}
return 'SeoSuiteFindSuggestionProcessor';
