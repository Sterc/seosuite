<?php
/**
 * Update an url
 *
 * @package seosuite
 * @subpackage processors
 */

class SeoSuiteUrlUpdateProcessor extends modObjectUpdateProcessor
{
    public $classKey = 'SeoSuiteUrl';
    public $languageTopics = array('seosuite:default');

    public function beforeSet()
    {
        $url = $this->getProperty('url');
        if (empty($url)) {
            $this->addFieldError('url', $this->modx->lexicon('seosuite.err.item_name_ns'));
        } elseif ($this->modx->getCount($this->classKey, array('url' => $url)) && ($this->object->url != $url)) {
            $this->addFieldError('url', $this->modx->lexicon('seosuite.err.item_name_ae'));
        }

        $redirectTo = $this->getProperty('redirect_to');

        /* Getting the old object for deleting old seotab redirects if needed */
        $oldObject = $this->modx->getObject($this->classKey, $this->getProperty('id'));
        if ($oldObject) {
            $oldRedirectTo = $oldObject->get('redirect_to');
            if ((int)$oldRedirectTo != 0
                && ((int)$oldRedirectTo != (int)$redirectTo) && $this->modx->seosuite->checkSeoTab()
            ) {
                $redirect = $this->modx->getObject('seoUrl', array('resource' => $oldRedirectTo));
                if ($redirect) {
                    $redirect->remove();
                }
            }
        }

        $redirect_handler = 0;
        $solved = 0;
        if ((int)$redirectTo > 0) {
            $solved = 1;
            // If SEO Tab is not installed, use SeoSuite as redirect handler
            if (!$this->modx->seosuite->checkSeoTab()) {
                $redirect_handler = 1;
            } else {
                $this->modx->seosuite->addSeoTabRedirect($url, $redirectTo);
            }
        }
        $this->setProperty('redirect_handler', $redirect_handler);
        $this->setProperty('solved', $solved);

        return parent::beforeSet();
    }
}
return 'SeoSuiteUrlUpdateProcessor';
