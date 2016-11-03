<?php
/**
 * Get resource list
 *
 * @package buster404
 * @subpackage processors
 */
class Buster404UrlResourceGetListProcessor extends modObjectGetListProcessor
{
    public $classKey = 'modResource';
    public $languageTopics = array('buster404:default');
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
return 'Buster404UrlResourceGetListProcessor';
