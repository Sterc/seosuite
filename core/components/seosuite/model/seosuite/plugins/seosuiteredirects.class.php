<?php

/**
 * SeoSuite
 *
 * Copyright 2019 by Sterc <modx@sterc.com>
 */

class SeoSuiteRedirects extends SeoSuitePlugin
{
    protected $request = '';

    /**
     * @access public.
     * @return Mixed.
     */
    public function onPageNotFound()
    {
        $this->request = $this->seosuite->formatUrl($_SERVER['REQUEST_URI']);

        if ($this->request !== '') {
            $this->countVisits();
            $this->redirect();
        }
    }

    protected function countVisits()
    {
        $notFound = $this->modx->getObject('SeoSuiteUrl', [
            'context_key' => $this->modx->context->get('key'),
            'url'         => $this->request
        ]);

        if (!$notFound) {
            $notFound = $this->modx->newObject('SeoSuiteUrl');
        }

        $notFound->fromArray([
            'context_key' => $this->modx->context->get('key'),
            'url'         => $this->request,
            'visits'      => (int) $notFound->get('visits') + 1,
            'last_visit'  => date('Y-m-d H:i:s')
        ]);

        $notFound->save();
    }

    //TODO If is int, then make url, else normal redirect
    protected function redirect()
    {
        $query = $this->modx->newQuery('SeoSuiteRedirect');
        $query->where([
            'active'  => 1,
            'old_url' => $this->request
        ]);

        $query->where([
            [
                'context_key' => $this->modx->context->key,
            ], [
                'context_key' => ''
            ]
        ], xPDOQuery::SQL_OR);

        /**
         * This is to ensure that if a redirect is available for this specific context it has priority over general redirects.
         * Because redirects tied to a specific context are now being returned first.
        */
        $query->sortby('context_key', 'desc');

        $redirect = $this->modx->getObject('SeoSuiteRedirect', $query);
        if ($redirect) {
            $redirectUrl = is_numeric($redirect->get('new_url')) ? $this->modx->makeUrl($redirect->get('new_url'), '', '', 'full') : $redirect->get('new_url');

            $this->modx->sendRedirect($redirectUrl, ['responseCode' => $redirect->get('redirect_type')]);
        }
    }
}