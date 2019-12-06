<?php

/**
 * SeoSuite
 *
 * Copyright 2019 by Sterc <modx@sterc.com>
 */

$xpdo_meta_map['SeoSuiteSocial'] = [
    'package'       => 'seosuite',
    'version'       => '1.0',
    'table'         => 'seosuite_social',
    'extends'       => 'xPDOSimpleObject',
    'tableMeta'     => [
        'engine'        => 'InnoDB'
    ],
    'fields'        => [
        'id'            => null,
        'resource_id'   => null,
        'og_title'      => null,
        'og_description' => null,
        'og_image'      => null,
        'og_image_alt'  => null,
        'og_type'       => null,
        'twitter_title' => null,
        'twitter_description' => null,
        'twitter_image' => null,
        'twitter_image_alt' => null,
        'twitter_card'  => 1,
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
        'og_title'      => [
            'dbtype'        => 'varchar',
            'precision'     => '255',
            'phptype'       => 'string',
            'null'          => true,
            'default'       => ''
        ],
        'og_description' => [
            'dbtype'        => 'varchar',
            'precision'     => '255',
            'phptype'       => 'string',
            'null'          => true,
            'default'       => ''
        ],
        'og_image'      => [
            'dbtype'        => 'varchar',
            'precision'     => '255',
            'phptype'       => 'string',
            'null'          => true,
            'default'       => ''
        ],
        'og_image_alt'  => [
            'dbtype'        => 'varchar',
            'precision'     => '255',
            'phptype'       => 'string',
            'null'          => true,
            'default'       => ''
        ],
        'og_type'       => [
            'dbtype'        => 'varchar',
            'precision'     => '40',
            'phptype'       => 'string',
            'null'          => true,
            'default'       => ''
        ],
        'twitter_title' => [
            'dbtype'        => 'varchar',
            'precision'     => '255',
            'phptype'       => 'string',
            'null'          => true,
            'default'       => ''
        ],
        'twitter_description' => [
            'dbtype'        => 'varchar',
            'precision'     => '255',
            'phptype'       => 'string',
            'null'          => true,
            'default'       => ''
        ],
        'twitter_image' => [
            'dbtype'        => 'varchar',
            'precision'     => '255',
            'phptype'       => 'string',
            'null'          => true,
            'default'       => ''
        ],
        'twitter_image_alt' => [
            'dbtype'        => 'varchar',
            'precision'     => '255',
            'phptype'       => 'string',
            'null'          => true,
            'default'       => ''
        ],
        'twitter_card'  => [
            'dbtype'        => 'varchar',
            'precision'     => '40',
            'phptype'       => 'string',
            'null'          => true,
            'default'       => ''
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
