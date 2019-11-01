<?php

/**
 * SeoSuite
 *
 * Copyright 2019 by Sterc <modx@sterc.com>
 */

$xpdo_meta_map['SeoSuiteResource'] = [
    'package'       => 'seosuite',
    'version'       => '1.0',
    'table'         => 'seosuite_resource',
    'extends'       => 'xPDOSimpleObject',
    'tableMeta'     => [
        'engine'        => 'InnoDB'
    ],
    'fields'        => [
        'id'            => null,
        'resource_id'   => null,
        'index_type'    => null,
        'follow_type'   => null,
        'sitemap'       => null,
        'sitemap_prio'  => null,
        'sitemap_changefreq' => null,
        'canonical'     => 1,
        'canonical_uri' => null,
        'editedon'      => null
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
        'resource_id'   => [
            'dbtype'        => 'int',
            'precision'     => '1',
            'phptype'       => 'integer',
            'null'          => false
        ],
        'index_type'    => [
            'dbtype'        => 'tinyint',
            'precision'     => '1',
            'phptype'       => 'integer',
            'null'          => false,
            'default'       => 1
        ],
        'follow_type'   => [
            'dbtype'        => 'tinyint',
            'precision'     => '75',
            'phptype'       => 'integer',
            'null'          => false,
            'default'       => 1
        ],
        'sitemap'       => [
            'dbtype'        => 'tinyint',
            'precision'     => '1',
            'phptype'       => 'integer',
            'null'          => false,
            'default'       => 1
        ],
        'sitemap_prio'  => [
            'dbtype'        => 'varchar',
            'precision'     => '10',
            'phptype'       => 'string',
            'null'          => true
        ],
        'sitemap_changefreq'    => [
            'dbtype'        => 'varchar',
            'precision'     => '10',
            'phptype'       => 'string',
            'null'          => true
        ],
        'canonical'     => [
            'dbtype'        => 'tinyint',
            'precision'     => '1',
            'phptype'       => 'integer',
            'null'          => false,
            'default'       => 0
        ],
        'canonical_uri' => [
            'dbtype'        => 'varchar',
            'precision'     => '255',
            'phptype'       => 'string',
            'null'          => true
        ],
        'editedon'      => [
            'dbtype'        => 'timestamp',
            'phptype'       => 'timestamp',
            'attributes'    => 'ON UPDATE CURRENT_TIMESTAMP',
            'null'          => false
        ]
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
        ]
    ],
    'aggregates'    =>  [
        'Resource'      => [
            'local'         => 'resource_id',
            'class'         => 'modResource',
            'foreign'       => 'id',
            'owner'         => 'local',
            'cardinality'   => 'one'
        ]
    ]
];
