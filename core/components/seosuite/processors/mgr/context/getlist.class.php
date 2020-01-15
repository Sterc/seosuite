<?php

/**
 * SeoSuite
 *
 * Copyright 2019 by Sterc <modx@sterc.com>
 */

class SeoSuiteContextGetListProcessor extends modObjectGetListProcessor
{
    public $classKey = 'modContext';
    public $defaultSortField = 'key';

    /**
     * {@inheritDoc}
     * @return boolean
     */
    public function initialize()
    {
        $initialized = parent::initialize();

        $this->setDefaultProperties([
            'search'  => '',
            'exclude' => '',
        ]);

        return $initialized;
    }

    /**
     * {@inheritDoc}
     * @param xPDOQuery $query
     * @return xPDOQuery
     */
    public function prepareQueryBeforeCount(xPDOQuery $query)
    {
        $search = $this->getProperty('search');
        if (!empty($search)) {
            $query->where([
                'key:LIKE'            => '%' . $search . '%',
                'OR:description:LIKE' => '%' . $search . '%',
            ]);
        }

        $exclude = $this->getProperty('exclude');
        if (!empty($exclude)) {
            $query->where([
                'key:NOT IN' => is_string($exclude) ? explode(',', $exclude) : $exclude,
            ]);
        }

        return $query;
    }

    /**
     * Filter the query by the valueField of MODx.combo.Context to get the initially value displayed right
     * @param xPDOQuery $query
     * @return xPDOQuery
     */
    public function prepareQueryAfterCount(xPDOQuery $query)
    {
        $key = $this->getProperty('key','');
        if (!empty($key)) {
            $query->where([
                $this->classKey . '.key:IN' => is_string($key) ? explode(',', $key) : $key,
            ]);
        }

        return $query;
    }

    /**
     * {@inheritDoc}
     * @param xPDOObject $object
     * @return array
     */
    public function prepareRow(xPDOObject $object)
    {
        return $object->toArray();
    }

    /**
     * Can be used to insert a row before iteration
     * @param array $list
     * @return array
     */
    public function afterIteration(array $list)
    {
        array_unshift($list, ['name' => $this->modx->lexicon('seosuite.use_redirect_across_domains'), 'key' => 'seosuite_all']);

        return $list;
    }
}

return 'SeoSuiteContextGetListProcessor';
