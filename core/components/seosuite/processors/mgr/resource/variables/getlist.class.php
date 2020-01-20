<?php

/**
 * SeoSuite
 *
 * Copyright 2019 by Sterc <modx@sterc.com>
 */

class SeoSuiteMetaVariablesProcessor extends modObjectProcessor
{
    /**
     * @access public.
     * @var Array.
     */
    public $languageTopics = ['core:resource', 'core:setting', 'seosuite:default'];

    /**
     * @access public.
     * @return Mixed.
     */
    public function initialize()
    {
        $this->modx->getService('seosuite', 'SeoSuite', $this->modx->getOption('seosuite.core_path', null, $this->modx->getOption('core_path') . 'components/seosuite/') . 'model/seosuite/');

        return parent::initialize();
    }

    /**
     * @access public.
     * @return Mixed.
     */
    public function process()
    {
        return $this->outputArray([
            [
                'key'   => 'pagetitle',
                'value' => $this->modx->lexicon('resource_pagetitle')
            ], [
                'key'   => 'longtitle',
                'value' => $this->modx->lexicon('seosuite.tab_meta.longtitle')
            ], [
                'key'   => 'description',
                'value' => $this->modx->lexicon('seosuite.tab_meta.description')
            ], [
                'key'   => 'introtext',
                'value' => $this->modx->lexicon('resource_summary')
            ], [
                'key'   => 'site_name',
                'value' => $this->modx->lexicon('setting_site_name')
            ], [
                'key'   => 'delimiter',
                'value' => $this->modx->lexicon('seosuite.tab_meta.delimiter')
            ]
        ]);
    }
}

return 'SeoSuiteMetaVariablesProcessor';
