<?php
namespace Sterc\SeoSuite\Processors\Mgr\Redirects;

use MODX\Revolution\Processors\Model\RemoveProcessor;
use Sterc\SeoSuite\Model\SeoSuiteRedirect;

class Remove extends RemoveProcessor
{
    /**
     * @access public.
     * @var String.
     */
    public $classKey = SeoSuiteRedirect::class;

    /**
     * @access public.
     * @var Array.
     */
    public $languageTopics = ['seosuite:default'];

    /**
     * @access public.
     * @var String.
     */
    public $objectType = 'seosuite.redirect';
}
