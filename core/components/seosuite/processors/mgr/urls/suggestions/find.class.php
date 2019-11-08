<?php

/**
 * SeoSuite
 *
 * Copyright 2019 by Sterc <modx@sterc.com>
 */

class SeoSuiteSuggestionsFindProcessor extends modObjectUpdateProcessor
{
    /**
     * @access public.
     * @var String.
     */
    public $classKey = 'SeoSuiteUrl';

    /**
     * @access public.
     * @var Array.
     */
    public $languageTopics = ['seosuite:default'];

    /**
     * @access public.
     * @var String.
     */
    public $objectType = 'seosuite.url';

    /**
     * @access public.
     * @return Mixed.
     */
    public function initialize()
    {
        $this->modx->getService('seosuite', 'SeoSuite', $this->modx->getOption('seosuite.core_path', null, $this->modx->getOption('core_path') . 'components/seosuite/') . 'model/seosuite/');

        return parent::initialize();
    }

    /**
     * @TODO create redirect from first suggestion.
     *
     * @access public.
     * @return Mixed.
     */
    public function beforeSave()
    {
        $context = $this->getProperty('match_context') !== null;
        $excludeWords = $this->modx->seosuite->getExcludeWords();

        $suggestions = $this->object->getRedirectSuggestions($context, $excludeWords);

        if (count($suggestions) >= 1) {
            if ($this->getProperty('create_redirect') !== null) {
                // Create redirect
            }
        }

        $this->object->set('suggestions', json_encode($suggestions));

        return parent::beforeSave();
    }
}

return 'SeoSuiteSuggestionsFindProcessor';
