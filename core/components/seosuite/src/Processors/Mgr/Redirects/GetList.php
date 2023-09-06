<?php
namespace Sterc\SeoSuite\Processors\Mgr\Redirects;

use MODX\Revolution\Processors\Model\GetListProcessor;
use Sterc\SeoSuite\SeoSuite;
use Sterc\SeoSuite\Model\SeoSuiteRedirect;
use xPDO\Om\xPDOQuery;
use xPDO\Om\xPDOObject;
use MODX\Revolution\modResource;

class GetList extends GetListProcessor
{
    /**
     * @access public.
     * @var String.
     */
    public $classKey = SeoSuiteRedirect::class;

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
        $this->setDefaultProperties([
            'dateFormat' => $this->modx->getOption('manager_date_format') . ', ' .  $this->modx->getOption('manager_time_format')
        ]);

        $sortby = $this->getProperty('sort');
        if ($sortby) {
            switch ($sortby) {
                case 'new_url_formatted':
                    $this->setProperty('sort', 'new_url');
                    break;
                default:
                    $this->setProperty('sort', $sortby);
                    break;
            }
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
        $criteria->setClassAlias('Redirect');
        $this->setProperty('sortAlias', 'Redirect');

        $criteria->select($this->modx->getSelectColumns(SeoSuiteRedirect::class, 'Redirect'));
        $criteria->select($this->modx->getSelectColumns(modResource::class, 'Resource', 'resource_', ['id', 'context_key']));

        $criteria->leftJoin(modResource::class, 'Resource', '`Resource`.`id` = `Redirect`.`resource_id`');

        $resource = $this->getProperty('resource');
        if (!empty($resource)) {
            $criteria->orCondition([
                'Redirect.resource_id'  => (int) $resource,
                'Redirect.new_url'      => $resource
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
        /** @var SeoSuite $seosuite */
        $seosuite = $this->modx->services->get('seosuite');

        /** @var SeoSuiteRedirect $object */
        $array = array_merge($object->toArray(), [
            'old_site_url'      => $seosuite->getOldSiteUrl($object),
            'new_site_url'      => $seosuite->getNewSiteUrl($object),
            'new_url_formatted' => $seosuite->getRedirectUrl($object)
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
