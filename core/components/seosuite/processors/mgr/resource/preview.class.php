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

        $rendered = [
            'title'       => $this->renderValue($this->getProperty('title')),
            'description' => $this->renderValue($this->getProperty('description'))
        ];

        return $this->outputArray(['output' => $rendered],0);
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
                        }
                    }
                }
            }
        }

        return implode('', $output);
    }
}

return 'SeoSuitePreviewProcessor';
