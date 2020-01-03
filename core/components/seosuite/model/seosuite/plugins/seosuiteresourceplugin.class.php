<?php

class SeoSuiteResourcePlugin extends SeoSuitePlugin
{

    /**
     * Holds an array of the record to be used in JS.
     * @var array
     */
    protected $record = [];

    /**
     * Holds an array of all loaded sections.
     * @var array
     */
    protected $loaded = [];

    /**
     * @access protected.
     * @param String $section.
     * @return Boolean.
     */
    protected function hasPermission($section)
    {
        if (isset($this->seosuite->config[$section]['permission'])) {
            return $this->seosuite->config[$section]['permission'];
        }

        return false;
    }

    /**
     * @access protected.
     * @return Array.
     */
    protected function getSeoSuiteFields()
    {
        $fields = [];

        foreach ($_POST as $key => $value) {
            if (preg_match('/^seosuite_(.*)/', $key, $matches)) {
                $fields[$matches[1]] = $value;
            }
        }

        foreach (['sitemap', 'searchable', 'searchable', 'canonical'] as $key) {
            if (!isset($fields[$key])) {
                $fields[$key] = 0;
            }
        }

        return $fields;
    }

    /**
     *
     */
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
     * @param $event
     */
    public function onDocFormRender($event)
    {
        $resource =& $event->params['resource'];
        $mode     =& $event->params['mode'];

        if ($this->hasPermission('meta') && !$this->isMetaDisabled($resource)) {
            $this->loadMeta($resource, $mode);
        }

        if ($this->hasPermission('tab_seo')) {
            $this->loadTabSeo($resource);
        }

        if ($this->hasPermission('tab_social')) {
            $this->loadTabSocial($resource);
        }

        /* Loading base scripts. */
        if ($this->isLoaded()) {
            if (is_array($this->seosuite->config['lexicons'])) {
                foreach ($this->seosuite->config['lexicons'] as $lexicon) {
                    $this->modx->controller->addLexiconTopic($lexicon);
                }
            } else {
                $this->modx->controller->addLexiconTopic($this->seosuite->config['lexicons']);
            }

            $this->modx->controller->addHtml('<script type="text/javascript">
                Ext.onReady(function() {
                    SeoSuite.config = ' . $this->modx->toJSON($this->seosuite->config) . ';
                    SeoSuite.record = ' . $this->modx->toJSON($this->record) . ';
                });
            </script>');

            $this->modx->controller->addCss($this->seosuite->config['css_url'] . 'mgr/seosuite.css');
            $this->modx->controller->addCss($this->seosuite->config['css_url'] . 'mgr.css');

            $this->modx->controller->addJavascript($this->seosuite->config['js_url'] . 'mgr/seosuite.js');

            $this->modx->controller->addJavascript($this->seosuite->config['js_url'] . 'mgr/extras/extras.js');

            /* Loading specific scripts for specific section. */
            if ($this->isLoaded('meta')) {
                $this->modx->controller->addLastJavascript($this->seosuite->config['js_url'] . 'node_modules/web-animations-js/web-animations.min.js');
                $this->modx->controller->addLastJavascript($this->seosuite->config['js_url'] . 'mgr/resource/metatag.js?v=' . $this->seosuite->config['version']);
                $this->modx->controller->addLastJavascript($this->seosuite->config['js_url'] . 'mgr/resource/preview.js?v=' . $this->seosuite->config['version']);
            }

            /* Loading specific scripts for specific section. */
            if ($this->isLoaded('seo')) {
                $this->modx->controller->addLastJavascript($this->seosuite->config['js_url'] . 'mgr/widgets/redirects.grid.js?v='. $this->seosuite->config['version']);
                $this->modx->controller->addLastJavascript($this->seosuite->config['js_url'] . 'mgr/resource/resource.tab_seo.js?v='. $this->seosuite->config['version']);
            }

            /* Loading specific scripts for specific section. */
            if ($this->isLoaded('social')) {
                $this->modx->controller->addLastJavascript($this->seosuite->config['js_url'] . 'mgr/resource/resource.tab_social.js?v='. $this->seosuite->config['version']);
            }
        }
    }

    /**
     * @access public.
     * @param Object $event.
     * @return void.
     */
    public function onDocFormSave($event)
    {
        $resource =& $event->params['resource'];

        if ($resource) {
            $this->seosuite->setResourceProperties($resource->get('id'), $this->getSeoSuiteFields());
            $this->seosuite->setSocialProperties($resource->get('id'), $this->getSeoSuiteFields());
        }
    }

    /**
     * @TODO Fix it for duplicating child resources.
     *
     * @access public.
     * @param Object $event.
     * @return void.
     */
    public function onResourceDuplicate($event)
    {
        $oldResource =& $event->params['oldResource'];
        $newResource =& $event->params['newResource'];

        if ($oldResource && $newResource) {
            $this->seosuite->setResourceProperties($newResource->get('id'), $this->seosuite->getResourceProperties($oldResource->get('id')));
            $this->seosuite->setSocialProperties($newResource->get('id'), $this->seosuite->setSocialProperties($oldResource->get('id')));
        }
    }

    /**
     * @access public.
     * @param Object $event.
     * @return void.
     */
    public function onEmptyTrash($event)
    {
        foreach ((array) $event->params['ids'] as $id) {
            $this->seosuite->removeResourceProperties($id);
            $this->seosuite->removeSocialProperties($id);
        }
    }

    public function onBeforeDocFormSave()
    {

    }

    public function onLoadWebDocument()
    {

    }

    public function onPageNotFound()
    {

    }

    public function onResourceBeforeSort()
    {

    }

    public function onManagerPageBeforeRender()
    {

    }

    /**
     * Check if a section is loaded.
     *
     * @access protected.
     * @param Null|String $section.
     * @return Boolean.
     */
    protected function isLoaded($section = null)
    {
        if ($section !== null) {
            return in_array($section, $this->loaded, true);
        }

        return count($this->loaded) >= 1;
    }

    /**
     * Load section meta.
     *
     * @param $resource
     * @param $mode
     * @return |null
     */
    protected function loadMeta($resource, $mode)
    {
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

        if ((int) $_REQUEST['id'] === (int) $this->modx->getOption('site_start')) {
            unset($arrFields['alias'], $arrFields['menutitle']);
        }

        $seoSuiteResource = $this->modx->getObject('SeoSuiteResource', ['resource_id' => $resource->get('id')]);
        if ($seoSuiteResource) {
            $this->record['keywords']         = $seoSuiteResource->get('keywords');
            $this->record['use_default_meta'] = $seoSuiteResource->get('use_default_meta');
            $this->record['meta_title']       = $seoSuiteResource->get('meta_title');
            $this->record['meta_description'] = $seoSuiteResource->get('meta_description');
        } else {
            $this->record['keywords']         = '';
            $this->record['use_default_meta'] = 1;
            $this->record['meta_title']       = json_decode($this->modx->seosuite->config['meta']['default_meta_title'], true);
            $this->record['meta_description'] = json_decode($this->modx->seosuite->config['meta']['default_meta_description'], true);
        }

        $this->record['fields']  = implode(',', array_keys($arrFields));
        $this->record['values']  = [];
        $this->record['chars']   = $arrFields;
        $this->record['url']     = $this->prepareUrl($resource, $mode);;
        $this->record['favicon'] = $this->getFavicon($resource);

        $this->loaded[] = 'meta';
    }

    /**
     * Load tab SEO.
     *
     * @access protected.
     * @param Object $resource.
     */
    protected function loadTabSeo($resource)
    {
        $properties = $this->seosuite->getResourceProperties($resource->get('id'));

        foreach ((array) $properties as $key => $value) {
            $this->record['seosuite_' . $key] = $value;
        }

        $this->loaded[] = 'seo';
    }

    /**
     * Load tab social.
     *
     * @access public.
     * @param Object $resource.
     */
    protected function loadTabSocial($resource)
    {
        $properties = $this->seosuite->getSocialProperties($resource->get('id'));

        foreach ((array) $properties as $key => $value) {
            $this->record['seosuite_' . $key] = $value;
        }

        $this->loaded[] = 'social';
    }

    /**
     * Check if Meta is disabled for the current template.
     *
     * @param $resource
     * @return bool
     */
    protected function isMetaDisabled($resource)
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
        return ($override && empty($template)) || ($override && (int) $template === 0) || (!empty($template) && in_array($template, $disabledTemplates, false));
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
}
