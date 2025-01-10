<?php
namespace Sterc\SeoSuite\Processors\Mgr\Urls;

use MODX\Revolution\modContext;
use MODX\Revolution\Processors\Model\GetListProcessor;
use Sterc\SeoSuite\Model\SeoSuiteUrl;
use xPDO\Om\xPDOQuery;
use xPDO\Om\xPDOObject;

class GetList extends GetListProcessor
{
    /**
     * @access public.
     * @var String.
     */
    public $classKey = SeoSuiteUrl::class;

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
        $this->setDefaultProperties([
            'dateFormat' => $this->modx->getOption('manager_date_format') . ', ' .  $this->modx->getOption('manager_time_format')
        ]);

        $sortby = $this->getProperty('sort');
        if ($sortby) {
            switch ($sortby) {
                case 'time_ago':
                    $this->setProperty('sort', 'last_visit');
                    break;
                default:
                    $this->setProperty('sort', $sortby);
                    break;
            }
        }

        $sortdir = $this->getProperty('dir');
        if ($sortdir) {
            switch ($sortby) {
                case 'time_ago':
                    $this->setProperty('dir', ($sortdir == 'ASC' ? 'DESC' : 'ASC'));
                    break;
                default:
                    $this->setProperty('dir', $sortdir);
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
            $object = $this->modx->getObject(modContext::class, [
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
