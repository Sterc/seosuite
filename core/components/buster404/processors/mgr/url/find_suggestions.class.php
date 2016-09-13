<?php
/**
 * Find redirect suggestions for a 404 url
 *
 * @package buster404
 * @subpackage processors
 */

class Buster404FindSuggestionProcessor extends modObjectUpdateProcessor
{
    public $classKey = 'Buster404Url';
    public $languageTopics = array('buster404:default');

    public function beforeSet()
    {
        $url = $this->getProperty('url');
        $this->setProperty('suggestions', $this->modx->buster404->findRedirectSuggestions($url));

        return parent::beforeSet();
    }
}
return 'Buster404FindSuggestionProcessor';
