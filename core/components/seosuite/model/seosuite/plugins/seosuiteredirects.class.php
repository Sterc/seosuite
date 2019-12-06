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
}