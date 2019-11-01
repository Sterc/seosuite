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
        return [
            'results' => [
                [
                    'key'   => 'pagetitle',
                    'value' => 'pagetitle'
                ],
                [
                    'key'   => 'longtitle',
                    'value' => 'longtitle'
                ],
                [
                    'key'   => 'description',
                    'value' => 'description'
                ],
                [
                    'key'   => 'introtext',
                    'value' => 'introtext'
                ],
                [
                    'key'   => 'site_name',
                    'value' => 'site_name'
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
