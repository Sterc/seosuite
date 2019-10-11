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
class SeoSuiteSnippetVariablesGetListProcessor extends modObjectGetListProcessor
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
                    'key'   => 'site_title',
                    'value' => 'site_title'
                ]
            ]
        ];
    }

    public function prepareRow($array)
    {
        return $array;
    }

}

return 'SeoSuiteSnippetVariablesGetListProcessor';
