<?php

/**
 * SeoSuite
 *
 * Copyright 2019 by Sterc <modx@sterc.com>
 */

class SeoSuiteExcludeWordsSaveProcessor extends modObjectProcessor
{
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
        $setting = $this->modx->getObject('modSystemSetting', [
            'key' => 'seosuite.exclude_words'
        ]);

        if (!$setting) {
            $setting->fromArray([
                'key'       => 'seosuite.exclude_words',
                'xtype'     => 'textfield',
                'namespace' => 'seosuite',
                'area'      => 'area_seosuite'
            ]);
        }

        if ($setting) {
            $words = explode(',', strtolower($this->getProperty('exclude_words')));

            $setting->set('value', implode(',', array_filter(array_map('trim', $words))));

            if ($setting->save()) {
                return $this->success('GOED');
            }
        }

        return $this->failure('FOUT');
    }
}

return 'SeoSuiteExcludeWordsSaveProcessor';
