<?php
namespace Sterc\SeoSuite\Processors\Mgr\Urls;

use MODX\Revolution\Processors\Model\GetProcessor;
use Sterc\SeoSuite\Model\SeoSuiteUrl;

/**
 * Processor to get a URL.
 */
class Get extends GetProcessor
{
    /**
     * @access public.
     * @var string The class key.
     */
    public $classKey = SeoSuiteUrl::class;

    /**
     * @access public.
     * @var string The language topics to load.
     */
    public $languageTopics = ['seosuite:default'];

    /**
     * @access public.
     * @var string The object ID field name.
     */
    public $primaryKeyField = 'id';

    /**
     * @access public.
     * @return Mixed.
     */
    public function cleanup()
    {
        $array = $this->object->toArray();

        // Get the site URL for the context
        $contextKey = $array['context_key'];
        $siteUrl = $this->modx->getOption('site_url');

        if (!empty($contextKey)) {
            $context = $this->modx->getContext($contextKey);
            
            if ($context) {
                $siteUrl = $context->getOption('site_url', $siteUrl);
            }
        }

        $array['site_url'] = $siteUrl;

        return $this->success('', $array);
    }
}
