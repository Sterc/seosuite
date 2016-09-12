<?php
/**
 * Get list of 404 urls
 *
 * @package buster404
 * @subpackage processors
 */
class Buster404SourceGetListProcessor extends modObjectGetListProcessor
{
    public $classKey = 'Buster404Source';
    public $languageTopics = array('buster404:default');
    public $defaultSortField = 'name';
    public $defaultSortDirection = 'ASC';

    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        $query = $this->getProperty('query');
        if (!empty($query)) {
            $c->where(array(
                    'name:LIKE' => '%'.$query.'%'
                ));
        }
        return $c;
    }
}
return 'Buster404SourceGetListProcessor';
