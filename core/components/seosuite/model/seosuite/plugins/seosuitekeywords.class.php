<?php

class SeoSuiteKeywords extends SeoSuitePlugin
{
    public function onMODXInit()
    {
        $version = $this->modx->getVersionData();
        $version = (int)($version['version'] . $version['major_version']);
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
     * @TODO Refactor disabled templates.
     *
     * @return string
     */
    public function onDocFormRender($event, array $properties = [])
    {
        $resource = $properties['resource'];
        $mode     = $properties['mode'];

        $template = (string) $resource->get('template');
        $override = false;
        if (isset($_REQUEST['template'])) {
            $template = (string)$_REQUEST['template'];
            $override = true;
        }

        $disabledTemplates = explode(',', $this->modx->getOption('seosuite.keywords..disabledtemplates', null, '0'));
        if (($override && $template === '0') || (!empty($template) && in_array($template, $disabledTemplates))) {
            return '';
        }

        $strFields = $this->modx->getOption('seosuite.keywords.fields', null, 'pagetitle:70,longtitle:70,description:160,alias:2023,menutitle:2023');
        $arrFields = [];
        if (is_array(explode(',', $strFields))) {
            foreach (explode(',', $strFields) as $field) {
                list($fieldName, $fieldCount) = explode(':', $field);
                $arrFields[$fieldName]        = $fieldCount;
            }
        } else {
            return '';
        }

        $this->modx->controller->addLexiconTopic('seosuite:default');

        $keywords = '';
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

            $url = str_replace(
                $resource->get('alias'),
                '<span id=\"seopro-replace-alias\">' . $resource->get('alias') . '</span>',
                $url
            );

            $seoKeywords = $this->modx->getObject('SeoSuiteKeyword', ['resource' => $resource->get('id')]);
            if ($seoKeywords) {
                $keywords = $seoKeywords->get('keywords');
            }
        }

        if ($_REQUEST['id'] == $this->modx->getOption('site_start')) {
            unset($arrFields['alias']);
            unset($arrFields['menutitle']);
        }

        $config = $this->seosuite->options;
        unset($config['resource']);

        $this->modx->regClientStartupHTMLBlock('<script type="text/javascript">
        Ext.onReady(function() {
            SeoSuite.config        = ' . $this->modx->toJSON($config) . ';
            SeoSuite.config.record = "' . $keywords . '";
            SeoSuite.config.values = {};
            SeoSuite.config.fields = "' . implode(',', array_keys($arrFields)) . '";
            SeoSuite.config.chars  = ' . $this->modx->toJSON($arrFields) . '
            SeoSuite.config.url    = "' . $url . '";
        });</script>');

        $this->modx->regClientCSS($this->seosuite->options['assetsUrl'] . 'css/mgr.css');
        $this->modx->regClientStartupScript($this->seosuite->options['assetsUrl'] . 'js/mgr/seosuite.js??v=' . $this->modx->getOption('seosuite.version', null, 'v1.0.0'));
        $this->modx->regClientStartupScript($this->seosuite->options['assetsUrl'] . 'js/mgr/resource/keywords.js?v=' . $this->modx->getOption('seosuite.version', null, 'v1.0.0'));

    }

    /**
     * @TODO Refactor disabled templates.
     */
    public function onDocFormSave($event, $properties)
    {
        $resource = $properties['resource'];

        $disabledTemplates = explode(',', $this->modx->getOption('seosuite.keywords.disabledtemplates', null, '0'));

        $template = (string) $resource->get('template');
        $override = false;
        if (isset($_REQUEST['template'])) {
            $template = (string)$_REQUEST['template'];
            $override = true;
        }
        if (($override && $template === '0') || (!empty($template) && in_array($template, $disabledTemplates))) {
            return '';
        }

        $seoKeywords = $this->modx->getObject('SeoSuiteKeyword', ['resource' => $resource->get('id')]);
        if (!$seoKeywords && isset($resource)) {
            $seoKeywords = $this->modx->newObject('SeoSuiteKeyword', ['resource' => $resource->get('id')]);
        }

        if ($seoKeywords){
            if (isset($_POST['keywords'])) {
                $seoKeywords->set('keywords', trim($_POST['keywords'], ','));
            } else {
                $seoKeywords->set('keywords', '');
            }

            $seoKeywords->save();
        }
    }

    /**
     * @TODO Refactor this method and test it.
     *
     * @return string
     */
    public function onResourceDuplicate($event, $properties)
    {
        $resource    = $properties['oldResource'];
        $newResource = $properties['newResource'];

        $template = (string) $resource->get('template');
        $override = false;
        if (isset($_REQUEST['template'])) {
            $template = (string) $_REQUEST['template'];
            $override = true;
        }

        /**
         * @TODO Refactor disabled templates.
         */
        $disabledTemplates = explode(',', $this->modx->getOption('seosuite.keywords.disabledtemplates', null, '0'));

        if (($override && $template === '0') || (!empty($template) && in_array($template, $disabledTemplates))) {
            return '';
        }

        $seoKeywords = $this->modx->getObject('SeoSuiteKeyword', ['resource' => $resource->get('id')]);
        if (!$seoKeywords) {
            $seoKeywords = $this->modx->newObject('SeoSuiteKeyword', ['resource' => $resource->get('id')]);
        }

        $newSeoKeywords = $this->modx->newObject('SeoSuiteKeyword');
        $newSeoKeywords->fromArray($seoKeywords->toArray());
        $newSeoKeywords->set('resource', $newResource->get('id'));
        $newSeoKeywords->save();
    }

    /**
     * @TODO Refactor this method.
     * @return string
     */
    public function onLoadWebDocument()
    {
        if ($this->modx->context->get('key') === 'mgr') {
            return '';
        }

        /**
         * @TODO Refactor disabled templates.
         */
        $disabledTemplates = explode(',', $this->modx->getOption('seosuite.keywords.disabledtemplates', null, '0'));

        $template = ($this->modx->resource->get('template')) ? (string) $this->modx->resource->get('template') : '';
        if (in_array($template, $disabledTemplates)) {
            return '';
        }

        $seoKeywords = $this->modx->getObject('seoKeywords', ['resource' => $this->modx->resource->get('id')]);
        if ($seoKeywords) {
            $keyWords = $seoKeywords->get('keywords');
            $this->modx->setPlaceholder('seosuite.keywords', $keyWords);
        }

        /* Render the meta title, based on system settings. */
        $titleFormat = $this->modx->getOption('seosuite.preview.title_format');
        if (empty($titleFormat)) {
            $siteDelimiter   = $this->modx->getOption('seosuite.preview.delimiter', null, '/');
            $siteUseSitename = (boolean) $this->modx->getOption('seosuite.preview.usesitename', null, true);
            $siteID          = $this->modx->resource->get('id');
            $siteName        = $this->modx->getOption('site_name');
            $longtitle       = $this->modx->resource->get('longtitle');
            $pagetitle       = $this->modx->resource->get('pagetitle');
            $seoProTitle     = [];

            if ($siteID === (int) $this->modx->getOption('site_start')) {
                $seoProTitle['pagetitle'] = !empty($longtitle) ? $longtitle : $siteName;
            } else {
                $seoProTitle['pagetitle'] = !empty($longtitle) ? $longtitle : $pagetitle;
                if ($siteUseSitename) {
                    $seoProTitle['delimiter'] = $siteDelimiter;
                    $seoProTitle['sitename'] = $siteName;
                }
            }

            $title = implode(' ', $seoProTitle);
        } else {
            $title = $this->modx->getOption('seosuite.preview.title_format');
        }

        $this->modx->setPlaceholder('seosuite.title', $title);
    }
}
