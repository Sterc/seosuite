<?php
namespace Sterc\SeoSuite\Processors\Mgr\Redirects;

use MODX\Revolution\Processors\Processor;
use Sterc\SeoSuite\Model\SeoSuiteRedirect;

class RemoveMultiple extends Processor
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
    /**
     * @access public.
     * @return Mixed.
     */
    public function process()
    {
        $redirects = $this->modx->getCollection($this->classKey, ['id:IN' => explode(',', $this->getProperty('id'))]);
        foreach ($redirects as $redirect) {
            $redirect->remove();
        }

        return $this->success();
    }
}
