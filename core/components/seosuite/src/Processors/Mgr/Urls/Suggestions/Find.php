<?php
namespace Sterc\SeoSuite\Processors\Mgr\Urls\Suggestions;

use MODX\Revolution\Processors\Model\UpdateProcessor;
use Sterc\SeoSuite\Model\SeoSuiteUrl;
use Sterc\SeoSuite\Model\SeoSuiteRedirect;

class Find extends UpdateProcessor
{
    /**
     * @access public.
     * @var String.
     */
    public $classKey = SeoSuiteUrl::class;

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
    public function beforeSave()
    {
        $seosuite     = $this->modx->services->get('seosuite');
        $context      = $this->getProperty('match_context') !== null;
        $excludeWords = $seosuite->getExcludeWords();

        $suggestions = $this->object->getRedirectSuggestions($context, $excludeWords);
        if (count($suggestions) >= 1) {
            if ($this->getProperty('create_redirect') !== null) {
                /* array_key_first retrieves the first array key of the suggestions array which contains the highest boosted suggested resource id. */
                $redirectToResourceId = array_key_first($suggestions);
                if ($redirectToResourceId) {
                    $redirect = $this->modx->getObject(SeoSuiteRedirect::class, [
                        'context_key' => $this->object->get('context_key'),
                        'resource_id' => $redirectToResourceId,
                        'old_url'     => $this->object->get('url'),
                        'new_url'     => $redirectToResourceId
                    ]);

                    if (!$redirect) {
                        $redirect = $this->modx->newObject(SeoSuiteRedirect::class);
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
