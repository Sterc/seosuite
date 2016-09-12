<?php
/**
 * Create a source
 *
 * @package buster404
 * @subpackage processors
 */

class Buster404SourceCreateProcessor extends modObjectCreateProcessor
{
    public $classKey = 'Buster404Source';
    public $languageTopics = array('buster404:default');

    public function beforeSet()
    {
        $name = $this->getProperty('name');

        if (empty($name)) {
            $this->addFieldError('name', $this->modx->lexicon('buster404.err.item_name_ns'));
        } else if ($this->modx->getCount($this->classKey, array('name' => $name)) && ($this->object->name != $name)) {
            $this->addFieldError('name', $this->modx->lexicon('buster404.err.item_name_ae'));
        }
        return parent::beforeSet();
    }
}
return 'Buster404SourceCreateProcessor';
