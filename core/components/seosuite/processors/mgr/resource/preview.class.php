<?php
/**
 * Get parsed search engine preview.
 *
 * @package seopro
 * @subpackage processors
 */
class SeoSuitePreviewProcessor extends modProcessor
{
    /**
     * @return mixed|string
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
            $title       = $this->modx->getOption('seosuite.meta.default_meta_title');
            $description = $this->modx->getOption('seosuite.meta.default_meta_description');
        }

        $rendered = [
            'title'       => $this->renderValue($title),
            'description' => $this->renderValue($description),
            'alias'       => $alias
        ];

        return $this->outputArray([
            'output' => $rendered,
            'counts' => [
                'title'       => strlen($rendered['title']),
                'description' => strlen($rendered['description'])
            ]
        ],
        0
        );
    }

    /**
     * @param $json
     * @return string
     */
    protected function renderValue($json)
    {
        $output = [];

        if (!empty($json)) {
            $array = json_decode($json, true);

            $fields = json_decode($this->getProperty('fields'), true);
            if (is_array($array) && count($array) > 0) {
                foreach ($array as $item) {
                    if ($item['type'] === 'text') {
                        $output[] = $item['value'];
                    } else {
                        switch ($item['value']) {
                            case 'site_name':
                                $output[] = $this->modx->getOption($item['value']);
                                break;
                            case 'pagetitle':
                            case 'longtitle':
                            case 'description':
                            case 'introtext':
                                if (isset($fields[$item['value']])) {
                                    $output[] = $fields[$item['value']];
                                }
                                break;
                            case 'title':
                                $output[] = $fields['longtitle'] ?: $fields['pagetitle'];
                                break;
                        }
                    }
                }
            }
        }

        return implode('', $output);
    }
}

return 'SeoSuitePreviewProcessor';
