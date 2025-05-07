<?php
namespace Sterc\SeoSuite\Processors\Mgr\Urls\Suggestions;

use MODX\Revolution\Processors\Model\UpdateProcessor;
use Sterc\SeoSuite\Model\SeoSuiteSuggestion;

/**
 * Update a suggestion.
 */
class Update extends UpdateProcessor
{
    /**
     * @access public.
     * @var string $classKey
     */
    public $classKey = SeoSuiteSuggestion::class;

    /**
     * @access public.
     * @var string $languageTopics
     */
    public $languageTopics = ['seosuite:default'];

    /**
     * @access public.
     * @var string $objectType
     */
    public $objectType = 'seosuite.suggestion';

    /**
     * @access public.
     * @return mixed
     */
    public function beforeSet()
    {
        $resource_id = $this->getProperty('resource_id');
        $score = $this->getProperty('score');

        if (empty($resource_id)) {
            $this->addFieldError('resource_id', $this->modx->lexicon('seosuite.error_suggestion_resource_id_ns'));
            return false;
        }

        if (!is_numeric($score) || $score < 0 || $score > 100) {
            $this->addFieldError('score', $this->modx->lexicon('seosuite.error_suggestion_score_invalid'));
            return false;
        }

        return parent::beforeSet();
    }

    /**
     * @access public.
     * @return mixed
     */
    public function afterSave()
    {
        $this->modx->cacheManager->refresh([
            'seosuite' => [],
        ]);

        return parent::afterSave();
    }
}
