<?php
/**
 * Get resource list
 *
 * @package buster404
 * @subpackage processors
 */
class Buster404UrlResourceGetListProcessor extends modObjectGetListProcessor
{
    public $classKey = 'modResource';
    public $languageTopics = array('buster404:default');
    public $defaultSortField = 'menuindex';
    public $defaultSortDirection = 'ASC';
}
return 'Buster404UrlResourceGetListProcessor';
