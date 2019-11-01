<?php

/**
 * SeoSuite
 *
 * Copyright 2019 by Sterc <modx@sterc.com>
 */

$xpdo_meta_map['SeoSuiteUrl'] = [
    'package'       => 'seosuite',
    'version'       => '1.0',
    'table'         => 'seosuite_url',
    'extends'       => 'xPDOSimpleObject',
    'tableMeta'     => [
        'engine'        => 'InnoDB',
    ],
    'fields'        => [
        'id'            => null,
        'context_key'   => null,
        'url'           => null,
        'suggestions'   => null,
        'visits'        => 0,
        'last_visit'    => '0000-00-00 00:00:00',
        'createdon'     => 'CURRENT_TIMESTAMP'
    ],
    'fieldMeta'     => [
        'id'            => [
            'dbtype'        => 'int',
            'precision'     => '11',
            'phptype'       => 'integer',
            'null'          => false,
            'index'         => 'pk',
            'generated'     => 'native'
        ],
        'context_key'   => [
            'dbtype'        => 'varchar',
            'precision'     => '75',
            'phptype'       => 'string',
            'null'          => false
        ],
        'url'           => [
            'dbtype'        => 'varchar',
            'precision'     => '2000',
            'phptype'       => 'string',
            'null'          => false,
            'default'       => '',
            'index'         => 'index'
        ],
        'suggestions'   => [
            'dbtype'        => 'text',
            'phptype'       => 'json',
            'null'          => true
        ],
        'visits'        => [
            'dbtype'        => 'int',
            'precision'     => '10',
            'phptype'       => 'integer',
            'null'          => false,
            'default'       => 0
        ],
        'last_visit'    => [
            'dbtype'        => 'timestamp',
            'phptype'       => 'timestamp',
            'null'          => true,
            'default'       => '0000-00-00 00:00:00'
        ],
        'createdon'     => [
            'dbtype'          => 'timestamp',
            'phptype'         => 'timestamp',
            'null'            => false,
            'default'         => 'CURRENT_TIMESTAMP'
        ],
    ],
    'indexes'       => [
        'PRIMARY'       => [
            'alias'         => 'PRIMARY',
            'primary'       => true,
            'unique'        => true,
            'columns'       => [
                'id'            => [
                    'collation'     => 'A',
                    'null'          => false
                ]
            ]
        ],
        'url'           => [
            'alias'         => 'url',
            'primary'       => false,
            'unique'        => false,
            'type'          => 'BTREE',
            'columns'       => [
                'url'           => [
                    'length'        => '767',
                    'collation'     => 'A',
                    'null'          => false
                ]
            ]
        ]
    ],
    'aggregates'    =>  [
        'Context'       => [
            'local'         => 'context_key',
            'class'         => 'modContext',
            'foreign'       => 'key',
            'owner'         => 'local',
            'cardinality'   => 'one'
        ]
    ]
];
