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
        $this->modx->resource = $this->modx->getObject('modResource', $this->getProperty('resource'));

        /* If no resource is set, when creating a resource, then temporarily create a new one so the getAliasPath method can be used. */
        if (!$this->modx->resource) {
            $this->modx->resource = $this->modx->newObject('modResource');
        }

        $alias = $this->modx->resource->getAliasPath($this->getProperty('alias'), [
            'content_type' => $this->getProperty('content_type'),
            'uri'          => $this->getProperty('uri'),
            'uri_override' => $this->getProperty('uri_override') === 'true' ? true : false,
            'context_key'  => $this->getProperty('context', 'web')
        ]);

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
