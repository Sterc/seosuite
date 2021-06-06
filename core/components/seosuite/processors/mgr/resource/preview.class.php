<?php

/**
 * SeoSuite
 *
 * Copyright 2019 by Sterc <modx@sterc.com>
 */

class SeoSuiteMetaPreviewProcessor extends modObjectProcessor
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
        $this->modx->switchContext($this->getProperty('context_key', $this->modx->getOption('default_context')));

        $alias      = '';
        $siteUrl    = trim($this->modx->getOption('site_url'), '/');
        $baseUrl    = trim($this->modx->getOption('base_url'), '/');

        if (preg_match('/^(http|https)/i', $siteUrl, $protocol)) {
            $protocol   = $protocol[0];
            $siteUrl    = str_replace(['http://', 'https://'], '', $siteUrl);
        } else {
            $protocol   = $this->modx->getOption('server_protocol', null, 'http');
        }

        if ((int) $this->modx->getOption('friendly_urls') === 1) {
            if ((int) $this->getProperty('id') !== (int) $this->modx->getOption('site_start')) {
                $resource = $this->modx->newObject('modResource');

                if ($resource) {
                    $alias = $resource->getAliasPath($this->getProperty('alias'), $this->getProperties());
                }
            }
        } else {
            $alias = $this->modx->getOption('request_controller') . '?' . $this->modx->getOption('request_param_id') . '=' . $this->getProperty('id');
        }

        $fields             = (array) json_decode($this->getProperty('fields'), true);

        $title              = $this->modx->seosuite->config['meta']['default_meta_title'];
        $description        = $this->modx->seosuite->config['meta']['default_meta_description'];

        $metaTitle          = $this->modx->seosuite->renderMetaValue($title, $fields, ['longtitle']);
        $metaDescription    = $this->modx->seosuite->renderMetaValue($description, $fields, ['description']);

        $output = [
            'output'        => [
                'protocol'      => $protocol,
                'site_url'      => $siteUrl,
                'base_url'      => $baseUrl,
                'alias'         => $alias,
                'title'         => $this->truncate($metaTitle['processed'], $this->modx->seosuite->config['meta']['preview'][$this->getProperty('preview_mode')]['title']),
                'description'   => $this->truncate($metaDescription['processed'], $this->modx->seosuite->config['meta']['preview'][$this->getProperty('preview_mode')]['description'])
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
            $output = mb_substr($output, 0, $maxLength) . '...';
        }

        return $output;
    }
}

return 'SeoSuiteMetaPreviewProcessor';
