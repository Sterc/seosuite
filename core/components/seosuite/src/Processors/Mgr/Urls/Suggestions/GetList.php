<?php
namespace Sterc\SeoSuite\Processors\Mgr\Urls\Suggestions;

use MODX\Revolution\Processors\Model\GetListProcessor;
use MODX\Revolution\modResource;
use Sterc\SeoSuite\Model\SeoSuiteSuggestion;

/**
 * Processor to get a list of suggestions for a URL.
 */
class GetList extends GetListProcessor
{
    /**
     * @access public.
     * @var string The class key.
     */
    public $classKey = SeoSuiteSuggestion::class;

    /**
     * @access public.
     * @var string The default sort field.
     */
    public $defaultSortField = 'score';

    /**
     * @access public.
     * @var string The default sort direction.
     */
    public $defaultSortDirection = 'DESC';

    /**
     * @access public.
     * @return Mixed.
     */
    public function initialize()
    {
        $this->setDefaultProperties([
            'start' => 0,
            'limit' => 20,
            'sort'  => $this->defaultSortField,
            'dir'   => $this->defaultSortDirection,
            'url_id' => 0
        ]);

        return parent::initialize();
    }

    /**
     * @access public.
     * @param xPDOQuery $c.
     * @return xPDOQuery.
     */
    public function prepareQueryBeforeCount($c)
    {
        $urlId = (int) $this->getProperty('url_id');
        
        if ($urlId > 0) {
            $c->where([
                'url_id' => $urlId
            ]);
        }

        return $c;
    }

    /**
     * @access public.
     * @param xPDOObject $object.
     * @return Array.
     */
    public function prepareRow($object)
    {
        $array = $object->toArray();
        
        // Get resource data
        $resource = $this->modx->getObject(modResource::class, $array['resource_id']);
        
        if ($resource) {
            $array['pagetitle'] = $resource->get('pagetitle');
            $array['uri'] = $resource->get('uri');
            $array['context_key'] = $resource->get('context_key');
        } else {
            $array['pagetitle'] = 'Resource not found';
            $array['uri'] = '';
            $array['context_key'] = '';
        }
        
        // Get URL data
        $url = $this->modx->getObject('Sterc\\SeoSuite\\Model\\SeoSuiteUrl', $array['url_id']);
        if ($url) {
            $array['url'] = $url->get('url') . ' (' . $array['url_id'] . ')';
        } else {
            $array['url'] = 'URL not found';
        }
        
        // Format pagetitle with resource ID
        if ($resource) {
            $array['pagetitle'] = $resource->get('pagetitle') . ' (' . $array['resource_id'] . ')';
        }
        
        return $array;
    }
}
