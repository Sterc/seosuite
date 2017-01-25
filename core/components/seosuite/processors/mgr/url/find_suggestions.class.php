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
        $this->setProperty('suggestions', $this->modx->seosuite->findRedirectSuggestions($url));

        return parent::beforeSet();
    }
}
return 'SeoSuiteFindSuggestionProcessor';
