<?php
/**
 * Get resource list
 *
 * @package seosuite
 * @subpackage processors
 */
class SeoSuiteUrlResourceGetListProcessor extends modObjectGetListProcessor
{
    public $classKey = 'modResource';
    public $languageTopics = array('seosuite:default');
    public $defaultSortField = 'menuindex';
    public $defaultSortDirection = 'ASC';

    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        $query = $this->getProperty('query');
        if (!empty($query)) {
            $c->where(array(
                'id:LIKE' => '%'.$query.'%',
                'OR:pagetitle:LIKE' => '%'.$query.'%',
                'OR:longtitle:LIKE' => '%'.$query.'%'
            ));
        }
        $ids = $this->getProperty('ids');
        if (!empty($ids)) {
            $ids = json_decode($ids, true);
            $c->where(array(
                'id:IN' => $ids
            ));
        }
        return $c;
    }

    public function prepareRow(xPDOObject $object)
    {
        $id      = $object->get('id');
        $url     = '';
        $ctx_key = $object->get('context_key');
        $ctx     = $this->modx->getContext($ctx_key);
        if ($ctx) {
            $url = $ctx->getOption('site_url');
        }

        $object->set('pagetitle_id', $object->get('pagetitle'). ' ('.$id.')');
        $object->set('resource_url', $url.$object->get('uri'));

        return parent::prepareRow($object);
    }
}

return 'SeoSuiteUrlResourceGetListProcessor';
