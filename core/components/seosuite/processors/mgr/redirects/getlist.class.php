<?php

/**
 * SeoSuite
 *
 * Copyright 2019 by Sterc <modx@sterc.com>
 */
    
class SeoSuiteRedirectGetListProcessor extends modObjectGetListProcessor
{
    /**
     * @access public.
     * @var String.
     */
    public $classKey = 'SeoSuiteRedirect';

    /**
     * @access public.
     * @var Array.
     */
    public $languageTopics = ['seosuite:default'];

    /**
     * @access public.
     * @var String.
     */
    public $defaultSortField = 'Redirect.id';

    /**
     * @access public.
     * @var String.
     */
    public $defaultSortDirection = 'DESC';

    /**
     * @access public.
     * @var String.
     */
    public $objectType = 'seosuite.redirect';

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
        $criteria->setClassAlias('Redirect');

        $criteria->select($this->modx->getSelectColumns('SeoSuiteRedirect', 'Redirect'));
        $criteria->select($this->modx->getSelectColumns('modResource', 'Resource', 'resource_', ['id', 'context_key']));

        $criteria->leftJoin('modResource', 'Resource', '`Resource`.`id` = `Redirect`.`resource_id`');

        $resource = $this->getProperty('resource');
        if (!empty($resource)) {
            $criteria->where([
                'Redirect.resource_id' => $resource
            ]);
        }

        $query = $this->getProperty('query');
        if (!empty($query)) {
            $criteria->where([
                'Redirect.old_url:LIKE'     => '%' . $query . '%',
                'OR:Redirect.new_url:LIKE'  => '%' . $query . '%'
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
            'old_site_url'      => $object->getOldSiteUrl(),
            'new_site_url'      => $object->getNewSiteUrl(),
            'new_url_formatted' => $object->getRedirectUrl()
        ]);

        if (in_array($object->get('editedon'), ['-001-11-30 00:00:00', '-1-11-30 00:00:00', '0000-00-00 00:00:00', null], true)) {
            $array['editedon'] = '';
        } else {
            $array['editedon'] = date($this->getProperty('dateFormat'), strtotime($object->get('editedon')));
        }

        if (in_array($object->get('last_visit'), ['-001-11-30 00:00:00', '-1-11-30 00:00:00', '0000-00-00 00:00:00', null], true)) {
            $array['last_visit'] = '';
        } else {
            $array['last_visit'] = date($this->getProperty('dateFormat'), strtotime($object->get('last_visit')));
        }

        return $array;
    }
}

return 'SeoSuiteRedirectGetListProcessor';
