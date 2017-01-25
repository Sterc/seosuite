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

        if ((int)$redirectTo > 0) {
            if (!$this->modx->seosuite->checkSeoTab()) {
                $this->addFieldError('redirect_to', $this->modx->lexicon('seosuite.seotab.versioninvalid'));
            }
            $seotabRedirect = $this->modx->seosuite->addSeoTabRedirect($url, $redirectTo);
            if (!$seotabRedirect) {
                $this->setProperty('redirect_to', 0);
            } else {
                $this->setProperty('solved', 1);
            }
        } elseif ((int)$redirectTo == 0) {
            $this->setProperty('solved', 0);
        }

        return parent::beforeSet();
    }
}
return 'SeoSuiteUrlUpdateProcessor';
