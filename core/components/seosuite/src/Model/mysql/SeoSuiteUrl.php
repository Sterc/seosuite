<?php
namespace Sterc\SeoSuite\Model\mysql;

use xPDO\xPDO;
use MODX\Revolution\modContext;

class SeoSuiteUrl extends \Sterc\SeoSuite\Model\SeoSuiteUrl
{

    public static $metaMap = array (
        'package' => 'Sterc\\SeoSuite\\Model\\',
        'version' => '0.2',
        'table' => 'seosuite_url',
        'extends' => 'xPDOSimpleObject',
        'tableMeta' =>
        array (
            'engine' => 'InnoDB',
        ),
        'fields' =>
        array (
            'context_key' => '',
            'url' => '',
            'suggestions' => NULL,
            'visits' => 0,
            'last_visit' => NULL,
            'createdon' => 'CURRENT_TIMESTAMP',
        ),
        'fieldMeta' =>
        array (
            'context_key' =>
            array (
                'dbtype' => 'varchar',
                'precision' => '100',
                'phptype' => 'string',
                'null' => false,
                'default' => '',
                'index' => 'index',
            ),
            'url' =>
            array (
                'dbtype' => 'varchar',
                'precision' => '2000',
                'phptype' => 'string',
                'null' => false,
                'default' => '',
                'index' => 'index',
            ),
            'suggestions' =>
            array (
                'dbtype' => 'text',
                'phptype' => 'json',
                'null' => true,
            ),
            'visits' =>
            array (
                'dbtype' => 'integer',
                'precision' => '11',
                'phptype' => 'int',
                'null' => false,
                'default' => 0,
            ),
            'last_visit' =>
            array (
                'dbtype' => 'timestamp',
                'phptype' => 'timestamp',
                'null' => true,
                'default' => NULL,
            ),
            'createdon' =>
            array (
                'dbtype' => 'timestamp',
                'phptype' => 'timestamp',
                'null' => false,
                'default' => 'CURRENT_TIMESTAMP',
            ),
        ),
        'indexes' =>
        array (
            'context_key' =>
            array (
                'alias' => 'context_key',
                'primary' => false,
                'unique' => false,
                'type' => 'BTREE',
                'columns' =>
                array (
                    'context_key' =>
                    array (
                        'length' => '',
                        'collation' => 'A',
                        'null' => false,
                    ),
                ),
            ),
            'url' =>
            array (
                'alias' => 'url',
                'primary' => false,
                'unique' => false,
                'type' => 'BTREE',
                'columns' =>
                array (
                    'url' =>
                    array (
                        'length' => '',
                        'collation' => 'A',
                        'null' => false,
                    ),
                ),
            ),
        ),
        'aggregates' =>
        array (
            'Context' =>
            array (
                'class' => modContext::class,
                'local' => 'bundle',
                'foreign' => 'id',
                'cardinality' => 'one',
                'owner' => 'foreign',
            ),
        ),
    );

}
