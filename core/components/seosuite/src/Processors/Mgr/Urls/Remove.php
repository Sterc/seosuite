<?php
namespace Sterc\SeoSuite\Processors\Mgr\Urls;

use MODX\Revolution\Processors\Model\RemoveProcessor;
use Sterc\SeoSuite\Model\SeoSuiteUrl;

class Remove extends RemoveProcessor
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
}
