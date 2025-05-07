<?php
namespace Sterc\SeoSuite\Processors\Mgr\Urls\Suggestions;

use MODX\Revolution\Processors\Model\RemoveProcessor;
use Sterc\SeoSuite\Model\SeoSuiteSuggestion;

/**
 * Processor to remove a suggestion.
 */
class Remove extends RemoveProcessor
{
    /**
     * @access public.
     * @var string The class key.
     */
    public $classKey = SeoSuiteSuggestion::class;

    /**
     * @access public.
     * @var string The language topic.
     */
    public $languageTopics = ['seosuite:default'];

    /**
     * @access public.
     * @var string The object ID field.
     */
    public $objectType = 'seosuite.suggestion';
}
