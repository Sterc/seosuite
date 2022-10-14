<?php

namespace Sterc\SeoSuite\Processors\Mgr\Urls\Redirects;

use MODX\Revolution\Processors\Model\CreateProcessor;
use Sterc\SeoSuite\Model\SeoSuiteRedirect;
use Sterc\SeoSuite\Model\mysql\SeoSuiteUrl;

class Create extends CreateProcessor
{
    /**
     * @access public.
     * @var String.
     */
    public $classKey = SeoSuiteRedirect::class;

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
        $object = $this->modx->getObject(SeoSuiteUrl::class, ['id' => $this->getProperty('id')]);

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
