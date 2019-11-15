<?php

class SeoSuiteMeta extends SeoSuitePlugin
{
    /**
     * @access public.
     * @return Boolean.
     */
    protected function hasPermission()
    {
        if (isset($this->seosuite->config['meta']['permission'])) {
            return $this->seosuite->config['meta']['permission'];
        }

        return false;
    }

    /**
     * Check if Meta is disabled for the current template.
     *
     * @param $resource
     * @return bool
     */
    protected function isDisabled($resource)
    {
        $template = (string) $resource->get('template');
        $override = false;
        if (isset($_REQUEST['template'])) {
            $template = (string) $_REQUEST['template'];
            $override = true;
        }

        if ((int) $template === 0) {
            $template = $this->modx->getOption('default_template');
        }

        $disabledTemplates = explode(',', $this->modx->seosuite->config['meta']['disabled_templates']);
        if (($override && empty($template)) || ($override && $template === '0') || (!empty($template) && in_array($template, $disabledTemplates))) {
            return true;
        }

        return false;
    }

    public function onMODXInit()
    {
        $version = $this->modx->getVersionData();
        $version = (int) ($version['version'] . $version['major_version']);
        if ($version < 27) {
            $this->modx->loadClass('modResource');
            $this->modx->map['modResource']['fieldMeta']['description'] = [
                'dbtype'   => 'text',
                'phptype'  => 'string',
                'index'    => 'fulltext',
                'indexgrp' => 'content_ft_idx'
            ];
        }
    }

    /**
     * @return string
     */
    public function onDocFormRender($event, array $properties = [])
    {
        if (!$this->hasPermission() || $this->isDisabled($properties['resource'])) {
            return null;
        }

        $resource = $properties['resource'];
        $mode     = $properties['mode'];

        $strFields = $this->modx->seosuite->config['meta']['counter_fields'];
        $arrFields = [];
        if (is_array(explode(',', $strFields))) {
            foreach (explode(',', $strFields) as $field) {
                list($fieldName, $fieldCount) = explode(':', $field);

                $min = 0;
                $max = $fieldCount;
                if (strpos($fieldCount, '|')) {
                    list($min, $max) = explode('|', $fieldCount);
                }

                $arrFields[$fieldName]['min'] = $min;
                $arrFields[$fieldName]['max'] = $max;
            }
        } else {
            return null;
        }

        $this->modx->controller->addLexiconTopic('seosuite:default');
        if ((int) $_REQUEST['id'] === (int) $this->modx->getOption('site_start')) {
            unset($arrFields['alias'], $arrFields['menutitle']);
        }

        $seoSuiteResource = $this->modx->getObject('SeoSuiteResource', ['resource_id' => $resource->get('id')]);
        if ($seoSuiteResource) {
            $record['keywords']         = $seoSuiteResource->get('keywords');
            $record['use_default_meta'] = $seoSuiteResource->get('use_default_meta');
            $record['meta_title']       = $seoSuiteResource->get('meta_title');
            $record['meta_description'] = $seoSuiteResource->get('meta_description');
        } else {
            $record['keywords']         = '';
            $record['use_default_meta'] = 1;
            $record['meta_title']       = json_decode($this->modx->seosuite->config['meta']['default_meta_title'], true);
            $record['meta_description'] = json_decode($this->modx->seosuite->config['meta']['default_meta_description'], true);
        }

        $record['fields']  = implode(',', array_keys($arrFields));
        $record['values']  = [];
        $record['chars']   = $arrFields;
        $record['url']     = $this->prepareUrl($resource, $mode);;
        $record['favicon'] = $this->getFavicon($resource);

        $this->modx->regClientStartupHTMLBlock('<script type="text/javascript">
        Ext.onReady(function() {
            SeoSuite.config = ' . $this->modx->toJSON($this->seosuite->config) . ';
            SeoSuite.record = ' . $this->modx->toJSON($record) . ';
        });</script>');

        $this->modx->regClientCSS($this->seosuite->options['assetsUrl'] . 'css/mgr.css');
        $this->modx->regClientStartupScript($this->seosuite->options['assetsUrl'] . 'js/mgr/seosuite.js?v=' . $this->modx->getOption('seosuite.version', null, 'v1.0.0'));
        $this->modx->regClientStartupScript($this->seosuite->options['assetsUrl'] . 'js/mgr/resource/metatag.js?v=' . $this->modx->getOption('seosuite.version', null, 'v1.0.0'));
        $this->modx->regClientStartupScript($this->seosuite->options['assetsUrl'] . 'js/mgr/resource/preview.js?v=' . $this->modx->getOption('seosuite.version', null, 'v1.0.0'));
    }

    /**
     * Prepare url HTML.
     *
     * @param $resource
     * @param $mode
     * @return mixed|string|string[]|null
     */
    protected function prepareUrl($resource, $mode)
    {
        $ctxKey   = !empty($resource) ? $resource->get('context_key') : $this->modx->getOption('default_context');
        $ctx      = $this->modx->getContext($ctxKey);
        $url      = $ctx ? $ctx->getOption('site_url', '', $this->modx->getOption('site_url')) : $this->modx->getOption('site_url');

        if ($mode === 'upd') {
            if ($ctx) {
                if ($resource->get('id') != $ctx->getOption('site_start', '', $this->modx->getOption('site_start'))) {
                    $url .= $resource->get('uri');
                }
            } else {
                $url = $this->modx->makeUrl($resource->get('id'), '', '', 'full');
            }

            $url = preg_replace(
                '/' . $resource->get('alias') . '(.*)/',
                '<span id="seosuite-replace-alias">' . $resource->get('alias') . '$1</span>',
                $url
            );

            if (!strpos($url, 'seosuite-replace-alias')) {
                $url .= '<span id="seosuite-replace-alias"></span>';
            }
        } else {
            $url .= '<span id="seosuite-replace-alias"></span>';
        }

        return $url;
    }

    /**
     * Retrieve http_host based upon current context.
     *
     * @param $resource
     * @return string
     */
    protected function getFavicon($resource)
    {
        $result   = $this->modx->query(sprintf('SELECT * FROM %scontext_setting WHERE `context_key`="%s" AND `key` = "http_host" LIMIT 1', $this->modx->getOption(xpdo::OPT_TABLE_PREFIX), $resource->get('context_key')));
        $row      = $result ? $result->fetch(PDO::FETCH_ASSOC) : null;
        $httpHost = $row ? $row['value'] : $this->modx->getOption('http_host');

        return 'https://www.google.com/s2/favicons?domain='  . $httpHost;
    }

    /**
     * @param $event
     * @param $properties
     * @return |null
     */
    public function onDocFormSave($event, $properties)
    {
        if (!$this->hasPermission() || $this->isDisabled($properties['resource'])) {
            return null;
        }

        $resource = $properties['resource'];
        if ($resource) {
            $properties = [
                'keywords' => ''
            ];

            foreach (array_keys($properties) as $key) {
                if (isset($_POST['seosuite_' . $key])) {
                    $properties[$key] = trim($_POST['seosuite_' . $key], ',');
                }
            }

            $this->seosuite->setSeoSuiteResourceProperties($resource->get('id'), $properties);
        }
    }

    /**
     * @TODO Refactor this method and test it.
     *
     * @return string
     */
    public function onResourceDuplicate($event, $properties)
    {
        if (!$this->hasPermission() || $this->isDisabled($properties['oldResource'])) {
            return null;
        }

        $oldResource =& $event->params['oldResource'];
        $newResource =& $event->params['newResource'];

        if ($oldResource && $newResource) {
            $properties = $this->seosuite->getSeoSuiteResourceProperties($oldResource->get('id'));

            if ($properties) {
                $newProperties = [
                    'resource_id' => $newResource->get('id'),
                    'keywords'    => $properties->get('keywords')
                ];

                $this->seosuite->setSeoSuiteResourceProperties($newResource->get('id'), $newProperties);
            }
        }
    }
}
