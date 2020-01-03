<?php
/**
 * @package seosuite
 */
$xpdo_meta_map['SeoSuiteResource']= array (
  'package' => 'seosuite',
  'version' => '0.2',
  'table' => 'seosuite_resource',
  'extends' => 'xPDOSimpleObject',
  'tableMeta' => 
  array (
    'engine' => 'InnoDB',
  ),
  'fields' => 
  array (
    'keywords' => '',
    'resource_id' => 0,
    'use_default_meta' => 1,
    'meta_title' => NULL,
    'meta_description' => NULL,
    'index_type' => 1,
    'follow_type' => 1,
    'sitemap' => 1,
    'sitemap_prio' => '',
    'sitemap_changefreq' => '',
    'canonical' => 0,
    'canonical_uri' => '',
    'editedon' => NULL,
  ),
  'fieldMeta' => 
  array (
    'keywords' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => true,
      'default' => '',
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
    'use_default_meta' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'attributes' => 'unsigned',
      'phptype' => 'boolean',
      'null' => false,
      'default' => 1,
    ),
    'meta_title' => 
    array (
      'dbtype' => 'mediumtext',
      'phptype' => 'json',
      'null' => true,
    ),
    'meta_description' => 
    array (
      'dbtype' => 'mediumtext',
      'phptype' => 'json',
      'null' => true,
    ),
    'index_type' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'attributes' => 'unsigned',
      'phptype' => 'boolean',
      'null' => false,
      'default' => 1,
    ),
    'follow_type' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'attributes' => 'unsigned',
      'phptype' => 'boolean',
      'null' => false,
      'default' => 1,
    ),
    'sitemap' =>
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'attributes' => 'unsigned',
      'phptype' => 'boolean',
      'null' => false,
      'default' => 1,
    ),
    'sitemap_prio' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '10',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'sitemap_changefreq' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '10',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'canonical' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'attributes' => 'unsigned',
      'phptype' => 'boolean',
      'null' => false,
      'default' => 0,
    ),
    'canonical_uri' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
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
    'aggregates' =>
        array (
            'Resource' =>
                array (
                    'class'       => 'modResource',
                    'local'       => 'resource_id',
                    'foreign'     => 'id',
                    'cardinality' => 'one',
                    'owner'       => 'foreign',
                ),
        ),
  'indexes' => 
  array (
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
          'length' => '767',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
);
