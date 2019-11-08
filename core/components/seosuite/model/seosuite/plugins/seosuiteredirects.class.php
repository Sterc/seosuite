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
        $request = urldecode(trim($_SERVER['REQUEST_URI'], '/'));
        $baseUrl = ltrim(trim($this->modx->getOption('base_url', null, MODX_BASE_URL)), '/');

        if ($baseUrl !== '/' && $baseUrl !== '') {
            $request = trim(str_replace($baseUrl, '', $request), '/');
        }

        if ($request !== '') {
            $notFound = $this->modx->getObject('SeoSuiteUrl', [
                'context_key'   => $this->modx->context->get('key'),
                'url'           => $request
            ]);

            if (!$notFound) {
                $notFound = $this->modx->newObject('SeoSuiteUrl');
            }

            $notFound->fromArray([
                'context_key'  => $this->modx->context->get('key'),
                'url'          => $request,
                'visits'       => (int) $notFound->get('visits') + 1,
                'last_visit'   => date('Y-m-d H:i:s')
            ]);

            $notFound->save();
        }
    }

    /**
     * @TODO old plugin
     */
    public function onPageNotFound2()
    {
        $redirectUrl = false;
        $url = $this->modx->getOption('server_protocol').'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        $redirectObject = $this->modx->getObject('SeoSuiteUrl', array('url' => $url));
        if ($redirectObject) {
            $count = (int) $redirectObject->get('triggered');

            $redirectObject->set('last_triggered', time());
            $redirectObject->set('triggered', ++$count);
            $redirectObject->save();

            /* Only create redirectUrl when handler is 1 (SeoSuite) */
            if ($redirectObject->get('redirect_to') !== 0 && $redirectObject->get('redirect_handler') === 1) {
                $redirectUrl = $this->modx->makeUrl($redirectObject->get('redirect_to'), '', '', 'full');
            }
        } else {
            /* Create new SeoSuiteUrl object, and try to find matches */
            /* When one redirect match is found, redirect to that page */
            $suggestions      = '';
            $redirect_to      = 0;
            $solved           = 0;
            $redirect_handler = 0;
            $findSuggestions  = $this->seosuite->findRedirectSuggestions($url);
            if (count($findSuggestions)) {
                if (count($findSuggestions) === 1) {
                    $redirect_to = $findSuggestions[0];
                    $solved = 1;

                    if (!$this->seosuite->checkSeoTab()) {
                        $redirect_handler = 1;
                    } else {
                        $this->seosuite->addSeoTabRedirect($url, $findSuggestions[0]);
                    }
                }
                $suggestions = json_encode(array_values($findSuggestions));
            }

            $this->modx->exec(
                "INSERT INTO {$this->modx->getTableName('SeoSuiteUrl')}
                SET {$this->modx->escape('url')} = {$this->modx->quote($url)},
                    {$this->modx->escape('suggestions')} = {$this->modx->quote($suggestions)},
                    {$this->modx->escape('redirect_to')} = {$this->modx->quote($redirect_to)},
                    {$this->modx->escape('redirect_handler')} = {$this->modx->quote($redirect_handler)},
                    {$this->modx->escape('solved')} = {$this->modx->quote($solved)},
                    {$this->modx->escape('triggered')} = 1"
            );

            if ($redirect_to) {
                $redirectUrl = $url;
            }
        }

        if ($redirectUrl) {
            $this->modx->sendRedirect($redirectUrl, 0, 'REDIRECT_HEADER', 'HTTP/1.1 301 Moved Permanently');
        }
    }
}