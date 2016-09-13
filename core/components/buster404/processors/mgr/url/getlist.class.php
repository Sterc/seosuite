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
        $solved = $this->getProperty('solved');
        if (!empty($solved)) {
            $c->where(array(
                    'solved' => $solved
                ));
        }
        return $c;
    }

    public function prepareRow(xPDOObject $object)
    {
        $redirect_to = $object->get('redirect_to');
        $redirectText = '-';
        if (intval($redirect_to) > 0) {
            // also check if resource exists
            $resourceObj = $this->modx->getObject('modResource', $redirect_to);
            if ($resourceObj) {
                $redirectText = $resourceObj->get('pagetitle').'<br><small>'.$this->modx->makeUrl($redirect_to, '', '', 'full').'</small>';
            }
        }
        $object->set('redirect_to_text', $redirectText);

        $suggestions = $object->get('suggestions');
        $suggestionsText = '-';
        $suggestionsArray = [];
        foreach ($suggestions as $id) {
            // also check if resource exists
            $resourceObj = $this->modx->getObject('modResource', $id);
            if ($resourceObj) {
                $suggestionsArray[] = $resourceObj->get('pagetitle').' ('.$resourceObj->get('id').')';
            }
        }
        if (count($suggestionsArray)) {
            $suggestionsText = implode('<br>', $suggestionsArray);
        }
        $object->set('suggestions_text', $suggestionsText);

        return parent::prepareRow($object);
    }
}
return 'Buster404UrlGetListProcessor';
