<?php
/**
 * Get list of 404 urls
 *
 * @package buster404
 * @subpackage processors
 */
class Buster404UrlGetListProcessor extends modObjectGetListProcessor
{
    public $classKey = 'Buster404Url';
    public $languageTopics = array('buster404:default');
    public $defaultSortField = 'url';
    public $defaultSortDirection = 'ASC';

    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        $query = $this->getProperty('query');
        if (!empty($query)) {
            $c->where(array(
                    'url:LIKE' => '%'.$query.'%'
                ));
        }
        return $c;
    }
}
return 'Buster404UrlGetListProcessor';
