<?php

/**
 * SeoSuite
 *
 * Copyright 2019 by Sterc <modx@sterc.com>
 */

class SeoSuiteRedirectCreateProcessor extends modObjectCreateProcessor
{
    /**
     * @access public.
     * @var String.
     */
    public $classKey = 'SeoSuiteRedirect';

    /**
     * @access public.
     * @var Array.
     */
    public $languageTopics = ['seosuite:default'];

    /**
     * @access public.
     * @var String.
     */
    public $objectType = 'seosuite.redirect';

    /**
     * @access public.
     * @return Mixed.
     */
    public function initialize()
    {
        $this->modx->getService('seosuite', 'SeoSuite', $this->modx->getOption('seosuite.core_path', null, $this->modx->getOption('core_path') . 'components/seosuite/') . 'model/seosuite/');

        $this->setDefaultProperties([
            'active' => 1
        ]);

        return parent::initialize();
    }

    /**
     * @access public.
     * @return Mixed.
     */
    public function beforeSave()
    {
        $object = $this->modx->getObject('SeoSuiteUrl', [
            'id' => $this->getProperty('id')
        ]);

        if ($object) {
            $criteria = [
                'old_url' => $object->get('url')
            ];

            if ($this->doesAlreadyExist($criteria)) {
                $this->addFieldError('url', $this->modx->lexicon('seosuite.redirect_error_exists'));
            } else {
                $this->object->set('context_key', $object->get('context_key'));
                $this->object->set('old_url', $object->get('url'));

                if (!empty($this->getProperty('suggestion'))) {
                    $this->object->set('resource_id', $this->getProperty('suggestion'));
                    $this->object->set('new_url', $this->getProperty('suggestion'));
                }

                if (!empty($this->getProperty('new_url'))) {
                    $this->object->set('new_url', $this->getProperty('new_url'));
                }

                $object->remove();
            }
        }

        return parent::beforeSave();
    }
}

return 'SeoSuiteRedirectCreateProcessor';
