<?php
/**
 * @package buster404
 */
$xpdo_meta_map['Buster404Source']= array (
  'package' => 'buster404',
  'version' => '0.1',
  'table' => 'buster404_sources',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'name' => '',
    'url_column_position' => 1,
  ),
  'fieldMeta' => 
  array (
    'name' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'url_column_position' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => false,
      'default' => 1,
    ),
  ),
);
