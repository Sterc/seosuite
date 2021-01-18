<?php

/**
 * SeoSuite
 *
 * Copyright 2019 by Sterc <modx@sterc.com>
 */

class SeoSuiteRedirects extends SeoSuitePlugin
{
    /**
     * @access public.
     * @return Mixed.
     */
    public function onPageNotFound()
    {
        $request = urldecode(trim($_GET[$this->modx->getOption('request_param_alias', null, 'q')], '/'));

        if (!empty($request)) {
            $this->redirect($request);
        }
    }

    /**
     * Redirect or log the 404 request.
     *
     * @access protected.
     * @param String $request.
     */
    protected function redirect($request)
    {
        $criteria = $this->modx->newQuery('SeoSuiteRedirect', [
            'old_url'       => $request,
            'active'        => 1,
            [
                'context_key'       => '',
                'OR:context_key:='  => $this->modx->context->get('key')
            ]
        ]);

        $criteria->sortby('context_key', 'DESC');

        foreach ($this->modx->getIterator('SeoSuiteRedirect', $criteria) as $redirect) {
            $redirect->set('visits', (int) $redirect->get('visits') + 1);
            $redirect->set('last_visit', date('Y-m-d H:i:s'));

            if ($redirect->save()) {
                if (is_numeric($redirect->get('new_url'))) {
                    $url = $this->modx->makeUrl($redirect->get('new_url'), '', '', 'full');
                } else {
                    $url = $redirect->get('new_url');
                }

                if (strpos($url, 'www') === 0) {
                    $url = 'http://' . $url;
                }

                if (!empty($url)) {
                    $this->modx->sendRedirect($url, [
                        'responseCode' => $redirect->get('redirect_type')
                    ]);
                }
            }
        }

        if ($this->shouldLog404($request)) {
            $url = $this->modx->getObject('SeoSuiteUrl', [
                'url' => $request
            ]);

            if (!$url) {
                $url = $this->modx->newObject('SeoSuiteUrl');
            }

            if ($url) {
                $url->fromArray([
                    'context_key' => $this->modx->context->get('key'),
                    'url'         => $request,
                    'visits'      => (int) $url->get('visits') + 1,
                    'last_visit'  => date('Y-m-d H:i:s')
                ]);

                $url->save();
            }
        }
    }

    /**
     * Check if the 404 request should be logged.
     *
     * Checks for SQL injection via regex.
     * Checks if url contains words which are set in system_setting.
     *
     * @access protected.
     * @param String $request.
     * @return Bool.
     */
    protected function shouldLog404($request)
    {
        $url = $this->modx->getOption('server_protocol').'://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

        if (!preg_match('/^(?:http(s)?:\/\/)?[\w.-]+(?:\.[\w\.-]+)+[\w\-\._~:\/?#[\]@!\$&\'\(\)\*\+,;=.]+$/', $url)) {
            return false;
        }

        $blockedWords = $this->modx->seosuite->config['blocked_words'];

        if (count($blockedWords) > 0) {
            foreach ($blockedWords as $word) {
                if (strpos($request, $word)) {
                    return false;
                }
            }
        }

        return true;
    }
}
