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
                'pagetitle:LIKE' => '%'.$query.'%',
                'OR:longtitle:LIKE' => '%'.$query.'%'
            ));
        }
        return $c;
    }
}
return 'SeoSuiteUrlResourceGetListProcessor';
