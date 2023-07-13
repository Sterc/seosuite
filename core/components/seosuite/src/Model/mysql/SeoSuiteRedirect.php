<?php
namespace Sterc\SeoSuite\Model\mysql;

use xPDO\xPDO;
use MODX\Revolution\modContext;
use MODX\Revolution\modResource;

class SeoSuiteRedirect extends \Sterc\SeoSuite\Model\SeoSuiteRedirect
{

    public static $metaMap = array (
        'package' => 'Sterc\\SeoSuite\\Model\\',
        'version' => '0.2',
        'table' => 'seosuite_redirect',
        'extends' => 'xPDOSimpleObject',
        'tableMeta' =>
        array (
            'engine' => 'InnoDB',
        ),
        'fields' =>
        array (
            'context_key' => '',
            'resource_id' => 0,
            'old_url' => '',
            'new_url' => '',
            'redirect_type' => '',
            'active' => 1,
            'visits' => 0,
            'last_visit' => NULL,
            'editedon' => NULL,
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
            'resource_id' =>
            array (
                'dbtype' => 'integer',
                'precision' => '10',
                'phptype' => 'int',
                'null' => false,
                'default' => 0,
                'index' => 'index',
            ),
            'old_url' =>
            array (
                'dbtype' => 'varchar',
                'precision' => '255',
                'phptype' => 'string',
                'null' => false,
                'default' => '',
                'index' => 'index',
            ),
            'new_url' =>
            array (
                'dbtype' => 'varchar',
                'precision' => '255',
                'phptype' => 'string',
                'null' => false,
                'default' => '',
            ),
            'redirect_type' =>
            array (
                'dbtype' => 'varchar',
                'precision' => '75',
                'phptype' => 'string',
                'null' => false,
                'default' => '',
            ),
            'active' =>
            array (
                'dbtype' => 'tinyint',
                'precision' => '1',
                'attributes' => 'unsigned',
                'phptype' => 'boolean',
                'null' => false,
                'default' => 1,
                'index' => 'index',
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
            'editedon' =>
            array (
                'dbtype' => 'timestamp',
                'phptype' => 'timestamp',
                'null' => true,
                'default' => NULL,
                'attributes' => 'ON UPDATE CURRENT_TIMESTAMP',
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
            'old_url' =>
            array (
                'alias' => 'old_url',
                'primary' => false,
                'unique' => false,
                'type' => 'BTREE',
                'columns' =>
                array (
                    'old_url' =>
                    array (
                        'length' => '',
                        'collation' => 'A',
                        'null' => false,
                    ),
                ),
            ),
            'active' =>
            array (
                'alias' => 'active',
                'primary' => false,
                'unique' => false,
                'type' => 'BTREE',
                'columns' =>
                array (
                    'active' =>
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
            'Resource' =>
            array (
                'class' => modResource::class,
                'local' => 'bundle',
                'foreign' => 'id',
                'cardinality' => 'one',
                'owner' => 'foreign',
            ),
        ),
    );

}
