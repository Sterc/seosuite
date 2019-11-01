<?php

/**
 * SeoSuite
 *
 * Copyright 2019 by Sterc <modx@sterc.com>
 */

class SeoSuiteUrlGetListProcessor extends modObjectGetListProcessor
{
    /**
     * @access public.
     * @var String.
     */
    public $classKey = 'SeoSuiteUrl';

    /**
     * @access public.
     * @var Array.
     */
    public $languageTopics = ['seosuite:default'];

    /**
     * @access public.
     * @var String.
     */
    public $defaultSortField = 'url';

    /**
     * @access public.
     * @var String.
     */
    public $defaultSortDirection = 'ASC';

    /**
     * @access public.
     * @var String.
     */
    public $objectType = 'seosuite.url';

    /**
     * @access public.
     * @return Mixed.
     */
    public function initialize()
    {
        $this->modx->getService('seosuite', 'SeoSuite', $this->modx->getOption('seosuite.core_path', null, $this->modx->getOption('core_path') . 'components/seosuite/') . 'model/seosuite/');

        $this->setDefaultProperties([
            'dateFormat' => $this->modx->getOption('manager_date_format') . ', ' .  $this->modx->getOption('manager_time_format')
        ]);

        return parent::initialize();
    }

    /**
     * @access public.
     * @param xPDOQuery $criteria.
     * @return xPDOQuery.
     */
    public function prepareQueryBeforeCount(xPDOQuery $criteria)
    {
        $query = $this->getProperty('query');

        if (!empty($query)) {
            $criteria->where([
                'old_url:LIKE' => '%' . $query . '%'
            ]);
        }

        $solved = $this->getProperty('solved', '');

        if ($solved !== '') {
            $criteria->where([
                'solved' => $solved
            ]);
        }

        return $criteria;
    }

    /**
     * @access public.
     * @param xPDOObject $object.
     * @return Array.
     */
    public function prepareRow(xPDOObject $object)
    {
        $redirect_to  = $object->get('redirect_to');
        $redirectText = '-';

        if ((int) $redirect_to > 0) {
            /* Also check if resource exists. */
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

        /* Render text for all the redirect suggestions */
        /* Only show 10 first suggestions to keep grid listing fast */
        $suggestionsText = '-';
        $suggestionsArray = [];

        $limit = 10;
        $count = count($suggestions);
        $i     = 0;
        foreach ($suggestions as $id) {
            // also check if resource exists
            $resourceObj = $this->modx->getObject('modResource', $id);
            if ($resourceObj) {
                $suggestionsArray[] = $resourceObj->get('pagetitle') . ' (' . $resourceObj->get('id') . ')';
            }

            $i++;

            if ($i >= $limit) {
                $suggestionsArray[] = '<i><b>- '.$count.' '.$this->modx->lexicon('seosuite.url.suggestions').' -</b></i>';
                break;
            }
        }

        if (count($suggestionsArray)) {
            $suggestionsText = implode('<br />', $suggestionsArray);
        }

        $object->set('suggestions_text', $suggestionsText);

        $array = $object->toArray();

        if (in_array($object->get('createdon'), ['-001-11-30 00:00:00', '-1-11-30 00:00:00', '0000-00-00 00:00:00', null], true)) {
            $array['createdon'] = '';
        } else {
            $array['createdon'] = date($this->getProperty('dateFormat'), strtotime($object->get('createdon')));
        }

        return $array;
    }
}

return 'SeoSuiteUrlGetListProcessor';
