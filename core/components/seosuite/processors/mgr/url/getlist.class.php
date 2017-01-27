<?php
/**
 * Get list of 404 urls
 *
 * @package seosuite
 * @subpackage processors
 */
class SeoSuiteUrlGetListProcessor extends modObjectGetListProcessor
{
    public $classKey = 'SeoSuiteUrl';
    public $languageTopics = ['seosuite:default'];
    public $defaultSortField = 'url';
    public $defaultSortDirection = 'ASC';

    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        $query  = $this->getProperty('query');
        $solved = $this->getProperty('solved');

        if ($query !== null) {
            $c->where([
                'url:LIKE' => '%' . $query . '%'
            ]);
        }

        if ($solved !== null) {
            $c->where([
                'solved' => $solved
            ]);
        }

        $c->sortby('id', 'DESC');

        return $c;
    }

    public function prepareRow(xPDOObject $object)
    {
        $redirect_to  = $object->get('redirect_to');
        $redirectText = '-';

        if (intval($redirect_to) > 0) {
            // also check if resource exists
            $resourceObj = $this->modx->getObject('modResource', $redirect_to);
            if ($resourceObj) {
                $redirectText = $resourceObj->get('pagetitle') . ' (' . $redirect_to . ')<br><small>' . $this->modx->makeUrl($redirect_to, '', '', 'full') . '</small>';
            }
        }
        $object->set('redirect_to_text', $redirectText);

        $suggestions = $object->get('suggestions');

        if (!is_array($suggestions)) {
            $suggestions = [];
        }

        $suggestionsText = '-';
        $suggestionsArray = [];
        foreach ($suggestions as $id) {
            // also check if resource exists
            $resourceObj = $this->modx->getObject('modResource', $id);
            if ($resourceObj) {
                $suggestionsArray[] = $resourceObj->get('pagetitle') . ' (' . $resourceObj->get('id') . ')';
            }
        }

        if (count($suggestionsArray)) {
            $suggestionsText = implode('<br />', $suggestionsArray);
        }

        $object->set('suggestions_text', $suggestionsText);

        return parent::prepareRow($object);
    }
}
return 'SeoSuiteUrlGetListProcessor';
