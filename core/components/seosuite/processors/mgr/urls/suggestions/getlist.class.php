<?php

/**
 * SeoSuite
 *
 * Copyright 2019 by Sterc <modx@sterc.com>
 */

class SeoSuiteSuggestionsGetListProcessor extends modObjectGetListProcessor
{
    /**
     * @access public.
     * @var String.
     */
    public $classKey = 'modResource';

    /**
     * @access public.
     * @var Array.
     */
    public $languageTopics = ['seosuite:default'];

    /**
     * @access public.
     * @var String.
     */
    public $defaultSortField = null;

    /**
     * @access public.
     * @var String.
     */
    public $defaultSortDirection = 'ASC';

    /**
     * @access public.
     * @var String.
     */
    public $objectType = 'seosuite.suggestion';

    /**
     * @access public.
     * @var Array.
     */
    public $suggestions = [];

    /**
     * @access public.
     * @var Array.
     */
    public $contexts = [];

    /**
     * @access public.
     * @return Mixed.
     */
    public function initialize()
    {
        $this->modx->getService('seosuite', 'SeoSuite', $this->modx->getOption('seosuite.core_path', null, $this->modx->getOption('core_path') . 'components/seosuite/') . 'model/seosuite/');

        $suggestions = json_decode($this->getProperty('suggestions'), true);

        arsort($suggestions);

        $this->suggestions = $suggestions;

        return parent::initialize();
    }

    /**
     * @access public.
     * @param xPDOQuery $criteria.
     * @return xPDOQuery.
     */
    public function prepareQueryBeforeCount(xPDOQuery $criteria)
    {
        $criteria->where([
            'id:IN' => array_keys($this->suggestions)
        ]);

        return $criteria;
    }

    /**
     * @access public.
     * @param xPDOQuery $criteria.
     * @return xPDOQuery.
     */
    public function prepareQueryAfterCount(xPDOQuery $criteria) {
        $criteria->sortby('FIELD(id, ' . implode(', ', array_keys($this->suggestions)) . ')');

        return $criteria;
    }

    /**
     * @access public.
     * @param xPDOObject $object.
     * @return Array.
     */
    public function prepareRow(xPDOObject $object)
    {
        return [
            'id'                    => $object->get('id'),
            'pagetitle'             => $object->get('pagetitle'),
            'pagetitle_formatted'   => $object->get('pagetitle') . ($this->modx->hasPermission('tree_show_resource_ids') ? ' (' . $object->get('id') . ')' : ''),
            'uri'                   => $object->get('uri'),
            'site_url'              => $this->getSiteUrl($object->get('context_key')),
            'boost'                 => $this->suggestions[$object->get('id')]
        ];
    }

    /**
     * @access private.
     * @param String $key.
     * @return String.
     */
    private function getSiteUrl($key)
    {
        if (!isset($this->contexts[$key])) {
            $object = $this->modx->getObject('modContext', [
                'key' => $key
            ]);

            if ($object && $object->prepare()) {
                $this->contexts[$key] = $object->getOption('site_url');
            } else {
                $this->contexts[$key] = '';
            }
        }

        return $this->contexts[$key];
    }
}

return 'SeoSuiteSuggestionsGetListProcessor';
