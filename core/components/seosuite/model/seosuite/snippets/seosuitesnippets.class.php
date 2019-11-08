<?php
require_once dirname(__DIR__) . '/seosuite.class.php';

/**
 * Class SeoSuiteSnippets.
 */
class SeoSuiteSnippets extends SeoSuite
{
    const PHS_PREFIX = 'ss_meta';

    public function seosuiteMeta($properties)
    {
        $id             = $this->modx->getOption('id', $properties, $this->modx->resource->get('id'));
        $tplTitle       = $this->modx->getOption('tplTitle', $properties, 'tplMetaTitle');
        $tpl            = $this->modx->getOption('tpl', $properties, 'tplMeta');
        $toPlaceholders = $this->modx->getOption('toPlaceholders', $properties, false);

        $meta = [
            'meta_title' => [
                'name'  => 'title',
                'value' => $this->config['meta']['default_meta_title'],
                'tpl'   => $tplTitle
            ],
            'meta_description' => [
                'name'  => 'description',
                'value' => $this->config['meta']['default_meta_description'],
                'tpl'   => $tpl
            ]
        ];

        $ssResource = $this->modx->getObject('SeoSuiteResource', $id);
        if ($ssResource) {
            foreach ($meta as $key => $values) {
                $meta[$key]['value'] = $ssResource->get($key);
            }
        }

        $resourceArray = [];
        if ($modResource = $this->modx->getObject('modResource', $id)) {
            $resourceArray = $modResource->toArray();
        }

        $html = [];
        foreach ($meta as $item) {
            $tpl = $item['tpl'];

            /* Unset tpl from placeholders. */
            unset($item['tpl']);

            /* Parse JSON. */
            $item['value'] = $this->renderMetaValue($item['value'], $resourceArray);

            $rowHtml = $this->getChunk($tpl, $item);
            if ($toPlaceholders) {
                $this->modx->toPlaceholder($item['name'], $rowHtml,self::PHS_PREFIX);
            } else {
                $html[] = $rowHtml;
            }
        }

        if ($toPlaceholders) {
            return '';
        }

        return implode(PHP_EOL, $html);
    }
}
