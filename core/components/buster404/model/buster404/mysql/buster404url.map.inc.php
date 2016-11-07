<?php
/**
 * @package buster404
 */
$xpdo_meta_map['Buster404Url']= array (
  'package' => 'buster404',
  'version' => '0.1',
  'table' => 'buster404_urls',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'url' => '',
    'solved' => 0,
    'redirect_to' => NULL,
    'redirect_handler' => NULL,
    'suggestions' => NULL,
  ),
  'fieldMeta' => 
  array (
    'url' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'solved' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'attributes' => 'unsigned',
      'phptype' => 'boolean',
      'null' => false,
      'default' => 0,
    ),
    'redirect_to' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => false,
    ),
    'redirect_handler' => 
    array (
      'dbtype' => 'int',
      'precision' => '2',
      'phptype' => 'integer',
      'null' => false,
    ),
    'suggestions' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'json',
      'null' => true,
    ),
  ),
);
