<?php
/*
 * This file is part of MODX Revolution.
 *
 * Copyright (c) MODX, LLC. All Rights Reserved.
 *
 * For complete copyright and license information, see the COPYRIGHT and LICENSE
 * files found in the top-level directory of this distribution.
 */

/**
 * Gets a list all snippet variable options.
 *
 * @package modx
 * @subpackage processors.security.user
 */
class SeoSuiteVariablesGetListProcessor extends modObjectGetListProcessor
{

    public function getData()
    {
        /**
         * Title is a non existing field but translates to longtitle with a default to pagetitle.
         */

        $this->modx->lexicon->load('core:resource', 'core:setting');

        return [
            'results' => [
                [
                    'key'   => 'title',
                    'value' => $this->modx->lexicon('seosuite.tab_meta.longtitle')
                ],
                [
                    'key'   => 'pagetitle',
                    'value' => $this->modx->lexicon('resource_pagetitle')
                ],
                [
                    'key'   => 'longtitle',
                    'value' => $this->modx->lexicon('resource_longtitle')
                ],
                [
                    'key'   => 'description',
                    'value' => $this->modx->lexicon('resource_description')
                ],
                [
                    'key'   => 'introtext',
                    'value' => $this->modx->lexicon('resource_summary')
                ],
                [
                    'key'   => 'site_name',
                    'value' => $this->modx->lexicon('setting_site_name')
                ]
            ]
        ];
    }

    public function prepareRow($array)
    {
        return $array;
    }

}

return 'SeoSuiteVariablesGetListProcessor';
