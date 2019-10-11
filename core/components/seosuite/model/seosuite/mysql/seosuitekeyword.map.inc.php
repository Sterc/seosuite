<?php
/**
 * @package seosuite
 */
$xpdo_meta_map['SeoSuiteKeyword']= array (
  'package' => 'seosuite',
  'version' => '0.2',
  'table' => 'seosuite_keyword',
  'extends' => 'xPDOSimpleObject',
  'tableMeta' => 
  array (
    'engine' => 'InnoDB',
  ),
  'fields' => 
  array (
    'resource' => 0,
    'keywords' => '',
  ),
  'fieldMeta' => 
  array (
    'resource' => 
    array (
      'dbtype' => 'integer',
      'precision' => '10',
      'phptype' => 'int',
      'null' => false,
      'default' => 0,
      'index' => 'index',
    ),
    'keywords' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => true,
      'default' => '',
    ),
  ),
  'indexes' => 
  array (
    'resource' => 
    array (
      'alias' => 'resource',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'resource' => 
        array (
          'length' => '767',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
);
