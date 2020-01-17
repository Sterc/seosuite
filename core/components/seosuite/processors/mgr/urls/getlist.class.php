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
    public $defaultSortField = 'last_visit';

    /**
     * @access public.
     * @var String.
     */
    public $defaultSortDirection = 'DESC';

    /**
     * @access public.
     * @var String.
     */
    public $objectType = 'seosuite.url';

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

        $this->setDefaultProperties([
            'dateFormat' => $this->modx->getOption('manager_date_format') . ', ' .  $this->modx->getOption('manager_time_format')
        ]);

        if ($this->getProperty('sortby')) {
            $this->defaultSortField = $this->getProperty('sortby');
        }

        if ($this->getProperty('sortdir')) {
            $this->defaultSortDirection = $this->getProperty('sortdir');
        }

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

        return $criteria;
    }

    /**
     * @access public.
     * @param xPDOObject $object.
     * @return Array.
     */
    public function prepareRow(xPDOObject $object)
    {
        $array = array_merge($object->toArray(), [
            'time_ago'  => $object->getTimeAgo(),
            'site_url'  => $this->getSiteUrl($object->get('context_key'))
        ]);

        if (in_array($object->get('last_visit'), ['-001-11-30 00:00:00', '-1-11-30 00:00:00', '0000-00-00 00:00:00', null], true)) {
            $array['last_visit'] = '';
        } else {
            $array['last_visit'] = date($this->getProperty('dateFormat'), strtotime($object->get('last_visit')));
        }

        if (in_array($object->get('createdon'), ['-001-11-30 00:00:00', '-1-11-30 00:00:00', '0000-00-00 00:00:00', null], true)) {
            $array['createdon'] = '';
        } else {
            $array['createdon'] = date($this->getProperty('dateFormat'), strtotime($object->get('createdon')));
        }

        return $array;
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

return 'SeoSuiteUrlGetListProcessor';
