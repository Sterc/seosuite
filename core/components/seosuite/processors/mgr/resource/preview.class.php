<?php

/**
 * SeoSuite
 *
 * Copyright 2019 by Sterc <modx@sterc.com>
 */

class SeoSuiteMetaPreviewProcessor extends modProcessor
{
    /**
     * @access public.
     * @return Mixed.
     */
    public function initialize()
    {
        $this->modx->getService('seosuite', 'SeoSuite', $this->modx->getOption('seosuite.core_path', null, $this->modx->getOption('core_path') . 'components/seosuite/') . 'model/seosuite/');

        return parent::initialize();
    }

    /**
     * @access public.
     * @return Mixed.
     */
    public function process()
    {
        $this->modx->switchContext($this->getProperty('context', 'web'));

        $resource = $this->modx->newObject('modResource');

        $protocol   = $this->modx->getOption('server_protocol', null, 'http');
        $siteUrl    = trim($this->modx->getOption('site_url'), '/');
        $baseUrl    = trim($this->modx->getOption('base_url'), '/');

        if (preg_match('/^(http|https)/i', $siteUrl, $matches)) {
            $protocol   = $matches[0];
            $siteUrl    = str_replace(['http://', 'https://'], '', $siteUrl);
        }

        if (!empty($baseUrl)) {
            $siteUrl    = rtrim($siteUrl, '/' . $baseUrl . '/');
        }

        if ((int) $this->getProperty('id') === (int) $this->modx->getOption('site_start')) {
            $alias = '';
        } else {
            $alias = $resource->getAliasPath($this->getProperty('alias'), [
                'context_key'   => $this->getProperty('context', 'web'),
                'parent'        => $this->getProperty('parent', 0),
                'content_type'  => $this->getProperty('content_type'),
                'uri'           => $this->getProperty('uri'),
                'uri_override'  => $this->getProperty('uri_override') === 'true'
            ]);
        }

        $title       = $this->getProperty('title');
        $description = $this->getProperty('description');

        if ($this->getProperty('use_default_meta') === 'true') {
            $title       = $this->modx->seosuite->config['meta']['default_meta_title'];
            $description = $this->modx->seosuite->config['meta']['default_meta_description'];
        }

        $fields = json_decode($this->getProperty('fields'), true);

        $renderedTitle       = $this->modx->seosuite->renderMetaValue($title, $fields);
        $renderedDescription = $this->modx->seosuite->renderMetaValue($description, $fields);

        $output = [
            'output'        => [
                'protocol'      => $protocol,
                'site_url'      => $siteUrl,
                'base_url'      => $baseUrl,
                'title'         => $this->truncate($renderedTitle, $this->modx->seosuite->config['meta']['preview'][$this->getProperty('preview_mode')]['title']),
                'description'   => $this->truncate($renderedDescription, $this->modx->seosuite->config['meta']['preview'][$this->getProperty('preview_mode')]['description']),
                'alias'         => $alias
            ],
            'counts'        => [
                'title'         => strlen($renderedTitle),
                'description'   => strlen($renderedDescription)
            ]
        ];

        return $this->outputArray($output, 0);
    }

    /**
     *
     * @access public.
     * @param String $output.
     * @param Integer $maxLength.
     * @return String.
     */
    protected function truncate($output, $maxLength = 0)
    {
        if ($maxLength !== 0 && strlen($output) > $maxLength) {
            $output = substr($output, 0, $maxLength) . '...';
        }

        return $output;
    }
}

return 'SeoSuiteMetaPreviewProcessor';
