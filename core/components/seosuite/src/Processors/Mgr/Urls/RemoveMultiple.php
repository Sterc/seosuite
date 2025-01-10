<?php
namespace Sterc\SeoSuite\Processors\Mgr\Urls;

use MODX\Revolution\Processors\Processor;
use Sterc\SeoSuite\Model\SeoSuiteUrl;

class RemoveMultiple extends Processor
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

    /**
     * @access public.
     * @return Mixed.
     */
    public function process()
    {
        $redirects = $this->modx->getCollection($this->classKey, [
            'id:IN' => explode(',', $this->getProperty('id'))
        ]);

        foreach ($redirects as $redirect) {
            $redirect->remove();
        }

        return $this->success();
    }
}
