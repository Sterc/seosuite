<?php
namespace Sterc\SeoSuite\Processors\Mgr\Redirects;

use MODX\Revolution\Processors\Model\CreateProcessor;
use Sterc\SeoSuite\Model\SeoSuiteRedirect;

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
        if ($this->getProperty('active') === null) {
            $this->setProperty('active', 0);
        }

        return parent::initialize();
    }

    /**
     * @access public.
     * @return Mixed.
     */
    public function beforeSave()
    {
        $this->object->set('old_url', trim($this->getProperty('old_url')));

        $criteria = [
            'id:!='       => $this->object->get('id'),
            'context_key' => $this->object->get('context_key'),
            'old_url'     => trim($this->object->get('old_url'))
        ];

        if ($this->doesAlreadyExist($criteria)) {
            $this->addFieldError('old_url', $this->modx->lexicon('seosuite.redirect_error_exists'));
        }

        $this->object->set('new_url', trim($this->getProperty('new_url')));
        $this->object->set('last_visit', null);

        return parent::beforeSave();
    }
}
