<?php
namespace Sterc\SeoSuite\Model\mysql;

use xPDO\xPDO;

class SeoSuiteSuggestion extends \Sterc\SeoSuite\Model\SeoSuiteSuggestion
{

    public static $metaMap = array (
        'package' => 'Sterc\\SeoSuite\\Model\\',
        'version' => '3.0',
        'table' => 'seosuite_suggestion',
        'extends' => 'xPDO\\Om\\xPDOSimpleObject',
        'tableMeta' => 
        array (
            'engine' => 'InnoDB',
        ),
        'fields' => 
        array (
            'url_id' => 0,
            'resource_id' => 0,
            'score' => 0,
            'createdon' => 'CURRENT_TIMESTAMP',
        ),
        'fieldMeta' => 
        array (
            'url_id' => 
            array (
                'dbtype' => 'integer',
                'precision' => '10',
                'phptype' => 'int',
                'null' => false,
                'default' => 0,
                'index' => 'index',
            ),
            'resource_id' => 
            array (
                'dbtype' => 'integer',
                'precision' => '10',
                'phptype' => 'int',
                'null' => false,
                'default' => 0,
                'index' => 'index',
            ),
            'score' => 
            array (
                'dbtype' => 'integer',
                'precision' => '10',
                'phptype' => 'int',
                'null' => false,
                'default' => 0,
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
            'url_id' => 
            array (
                'alias' => 'url_id',
                'primary' => false,
                'unique' => false,
                'type' => 'BTREE',
                'columns' => 
                array (
                    'url_id' => 
                    array (
                        'length' => '',
                        'collation' => 'A',
                        'null' => false,
                    ),
                ),
            ),
            'resource_id' => 
            array (
                'alias' => 'resource_id',
                'primary' => false,
                'unique' => false,
                'type' => 'BTREE',
                'columns' => 
                array (
                    'resource_id' => 
                    array (
                        'length' => '',
                        'collation' => 'A',
                        'null' => false,
                    ),
                ),
            ),
            'url_resource' => 
            array (
                'alias' => 'url_resource',
                'primary' => false,
                'unique' => true,
                'type' => 'BTREE',
                'columns' => 
                array (
                    'url_id' => 
                    array (
                        'length' => '',
                        'collation' => 'A',
                        'null' => false,
                    ),
                    'resource_id' => 
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
            'Url' => 
            array (
                'class' => 'Sterc\\SeoSuite\\Model\\SeoSuiteUrl',
                'local' => 'url_id',
                'foreign' => 'id',
                'cardinality' => 'one',
                'owner' => 'foreign',
            ),
            'Resource' => 
            array (
                'class' => 'MODX\\Revolution\\modResource',
                'local' => 'resource_id',
                'foreign' => 'id',
                'cardinality' => 'one',
                'owner' => 'foreign',
            ),
        ),
    );

}
