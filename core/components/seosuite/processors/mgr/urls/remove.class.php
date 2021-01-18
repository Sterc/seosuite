<?php

/**
 * SeoSuite
 *
 * Copyright 2019 by Sterc <modx@sterc.com>
 */

class SeoSuiteUrlRemoveProcessor extends modObjectRemoveProcessor
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
    public $objectType = 'seosuite.url';

    /**
     * @access public.
     * @return Mixed.
     */
    public function initialize()
    {
        $this->modx->getService('seosuite', 'SeoSuite', $this->modx->getOption('seosuite.core_path', null, $this->modx->getOption('core_path') . 'components/seosuite/') . 'model/seosuite/');

        return parent::initialize();
    }
}

return 'SeoSuiteUrlRemoveProcessor';
