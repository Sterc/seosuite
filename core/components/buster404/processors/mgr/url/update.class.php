<?php
/**
 * Update an url
 *
 * @package buster404
 * @subpackage processors
 */

class Buster404UrlUpdateProcessor extends modObjectUpdateProcessor
{
    public $classKey = 'Buster404Url';
    public $languageTopics = array('buster404:default');

    public function beforeSet()
    {
        $url = $this->getProperty('url');

        if (empty($name)) {
            $this->addFieldError('url', $this->modx->lexicon('buster404.err.item_name_ns'));
        } else if ($this->modx->getCount($this->classKey, array('url' => $url)) && ($this->object->url != $url)) {
            $this->addFieldError('url', $this->modx->lexicon('buster404.err.item_name_ae'));
        }
        return parent::beforeSet();
    }
}
return 'Buster404UrlUpdateProcessor';
