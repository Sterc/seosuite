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

    protected $foundSuggestions = 0;

    protected $redirectCreated = false;

    protected $redirectExists = false;

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
     * @access public.
     * @return Mixed.
     */
    public function beforeSave()
    {
        $context      = $this->getProperty('match_context') !== null;
        $excludeWords = $this->modx->seosuite->getExcludeWords();

        $suggestions = $this->object->getRedirectSuggestions($context, $excludeWords);
        if (count($suggestions) >= 1) {
            if ($this->getProperty('create_redirect') !== null) {
                /* array_key_first retrieves the first array key of the suggestions array which contains the highest boosted suggested resource id. */
                $redirectToResourceId = array_key_first($suggestions);
                if ($redirectToResourceId) {
                    $redirect = $this->modx->getObject('SeoSuiteRedirect', [
                        'context_key' => $this->object->get('context_key'),
                        'resource_id' => $redirectToResourceId,
                        'old_url'     => $this->object->get('url'),
                        'new_url'     => $redirectToResourceId
                    ]);

                    if (!$redirect) {
                        $redirect = $this->modx->newObject('SeoSuiteRedirect');
                        $redirect->fromArray([
                            'context_key' => $this->object->get('context_key'),
                            'resource_id' => $redirectToResourceId,
                            'old_url'     => $this->object->get('url'),
                            'new_url'     => $redirectToResourceId
                        ]);

                        if ($redirect->save()) {
                            $this->redirectCreated = true;
                        }
                    } else {
                        $this->redirectExists = true;
                    }
                }
            }
        }

        $this->object->set('suggestions', json_encode($suggestions));

        $this->foundSuggestions = count($suggestions);

        return parent::beforeSave();
    }

    /**
     * @return array|string
     */
    public function cleanup()
    {
        if ($this->redirectCreated && $this->object->remove()) {
            return $this->success($this->modx->lexicon('seosuite.url.found_suggestions'));
        }

        if ($this->redirectExists && $this->object->remove()) {
            return $this->success($this->modx->lexicon('seosuite.url.found_suggestions.redirect_exists'));
        }

        return $this->success($this->modx->lexicon('seosuite.suggestions_found', ['suggestions' => $this->foundSuggestions]));
    }
}

return 'SeoSuiteSuggestionsFindProcessor';
