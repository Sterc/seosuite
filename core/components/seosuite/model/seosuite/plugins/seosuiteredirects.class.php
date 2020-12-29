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

    /**
     * Determine if page not found should be tracked.
     * Checks for SQL injection via regex
     * Checks if url contains words which are set in system_setting
     */
    protected function shouldLog404()
    {
        $url = $this->modx->getOption('server_protocol').'://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        if (!preg_match('/^(?:http(s)?:\/\/)?[\w.-]+(?:\.[\w\.-]+)+[\w\-\._~:\/?#[\]@!\$&\'\(\)\*\+,;=.]+$/', $url)) {
            return false;
        }

        if (count($blockedWords = $this->modx->seosuite->config['blocked_words']) > 0) {
            foreach ($blockedWords as $word) {
                if (strpos($this->request, $word)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Add 404 url if page not found event is triggered.
     */
    protected function countVisits()
    {
        if (!$this->shouldLog404()) {
            return false;
        }

        $notFound = $this->modx->getObject('SeoSuiteUrl', [
            'context_key' => $this->modx->context->get('key'),
            'url'         => $this->request
        ]);

        if (!$notFound) {
            /* Check if there is not a redirect that already exists for this. */
            if ($this->modx->getObject('SeoSuiteRedirect', ['context_key' => $this->modx->context->get('key'), 'old_url' => $this->request, 'active' => true])) {
                return false;
            }

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

    /**
     * Check if there are any redirects set up.
     */
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

            $redirect->set('visits', (int) $redirect->get('visits') + 1);
            $redirect->set('last_visit', date('Y-m-d H:i:s'));
            $redirect->save();

            $this->modx->sendRedirect($redirectUrl, ['responseCode' => $redirect->get('redirect_type')]);
        }
    }
}