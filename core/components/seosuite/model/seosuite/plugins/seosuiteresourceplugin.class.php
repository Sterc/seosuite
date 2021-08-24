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
     * Check if the user has the right permission.
     *
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
     * Check if the SEO Suite is enabled for the current template.
     *
     * @access protected.
     * @param Object $resource.
     * @param String $mode.
     * @return Boolean.
     */
    protected function isEnabled($resource, $mode = 'create')
    {
        $template   = (string) $resource->get('template');
        $templates  = $this->modx->seosuite->config['disabled_templates'];

        if (isset($_GET['template'])) {
            $template = (string) $_GET['template'];
        }

        if ($template === '0') {
            $template = (string) $this->modx->getOption('default_template', null, '0');
        }

        if ($template === '0') {
            return false;
        }

        if (count($templates) >= 1) {
            return in_array($template, $templates, true);
        }

        return true;
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
            $this->seosuite->setRedirectProperties($resource);
        }
    }

    /**
     * @param $event
     */
    public function onDocFormRender($event)
    {
        $resource =& $event->params['resource'];
        $mode     =& $event->params['mode'];

        if ($this->isEnabled($resource, $mode)) {
            if ($this->hasPermission('meta')) {
                $this->loadMeta($resource);
            }

            if ($this->hasPermission('tab_seo')) {
                $this->loadTabSeo($resource);
            }

            if ($this->hasPermission('tab_social')) {
                $this->loadTabSocial($resource);
            }

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

                $this->modx->controller->addJavascript($this->seosuite->config['js_url'] . 'mgr/seosuite.js');
                $this->modx->controller->addJavascript($this->seosuite->config['js_url'] . 'mgr/extras/extras.js');

                /* Loading specific scripts for specific section. */
                if ($this->isLoaded('meta')) {
                    $this->modx->regClientStartupScript($this->seosuite->config['js_url'] . 'mgr/resource/metatag.js?v=' . $this->seosuite->config['version']);
                    $this->modx->regClientStartupScript($this->seosuite->config['js_url'] . 'mgr/resource/resource.tab_meta.js?v=' . $this->seosuite->config['version']);
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
    }

    /**
     * @TODO Fix it for duplicating child resources, I created a PR to fix this in MODX Core: https://github.com/modxcms/revolution/pull/14874
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
            $this->seosuite->setSocialProperties($newResource->get('id'), $this->seosuite->getSocialProperties($oldResource->get('id')));
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

    /**
     * Set placeholders for SeoSuite Meta data.
     */
    public function onLoadWebDocument()
    {
        if ($this->isEnabled($this->modx->resource) && $this->modx->seosuite->config['placeholder_plugin_enabled']) {
            $this->modx->runSnippet('seosuiteMeta', [
                'toPlaceholders' => true
            ]);
        }
    }

    public function onBeforeDocFormSave()
    {

    }

    public function onPageNotFound()
    {

    }

    public function onResourceBeforeSort()
    {
        list($sourceCtx, $resource) = explode('_', $this->modx->getOption('source', $_POST));
        list($targetCtx, $target) = explode('_', $this->modx->getOption('target', $_POST));

        switch ($this->modx->getOption('point', $_POST)) {
            case 'above':
            case 'below':
                $tmpRes = $this->modx->getObject('modResource', $target);
                if ($tmpRes) {
                    $target = $tmpRes->get('parent');
                    unset($tmpRes);
                }
                break;
        }

        $oldResource = $this->modx->getObject('modResource', $resource);
        $modResource = $this->modx->getObject('modResource', $resource);
        if ($oldResource && $modResource) {
            $modResource->set('parent', $target);
            $modResource->set('uri', '');

            $uriChanged = false;
            if ($oldResource->get('uri') != $modResource->get('uri') && $oldResource->get('uri') != '') {
                $uriChanged = true;
            }

            if ($oldResource->get('alias') != $modResource->get('alias') && $oldResource->get('alias') != '') {
                $newProperties['urls'][] = ['url' => $oldResource->get('uri')];
                $uriChanged              = true;
            }

            /* Recursive set redirects for drag/dropped resource, and its children (where uri_override is not set) . */
            if ($uriChanged && (int) $this->modx->getOption('use_alias_path') === 1) {
                $oldResource->set('isfolder', true);
                $resourceOldBasePath = $oldResource->getAliasPath(
                    $oldResource->get('alias'),
                    $oldResource->toArray()
                );

                $query = $this->modx->newQuery('modResource');
                $query->where([
                    [
                        'uri:LIKE'  => $resourceOldBasePath . '%',
                        'OR:id:='   => $oldResource->id
                    ],
                    'uri_override'  => false,
                    'published'     => true,
                    'deleted'       => false,
                    'context_key'   => $modResource->get('context_key')
                ]);

                $childResources = $this->modx->getIterator('modResource', $query);
                foreach ($childResources as $childResource) {
                    if (!$this->modx->getCount('SeoSuiteRedirect', ['old_url' => $childResource->get('uri'), 'context_key' => $childResource->get('context_key')])) {
                        $data = [
                            'old_url'       => $childResource->get('uri'),
                            'resource_id'   => $childResource->get('id'),
                            'context_key'   => $sourceCtx,
                            'new_url'       => $childResource->get('id'),
                            'redirect_type' => 'HTTP/1.1 301 Moved Permanently'
                        ];

                        $redirect = $this->modx->newObject('SeoSuiteRedirect');
                        $redirect->fromArray($data);
                        $redirect->save();
                    }
                }
            }
        }
    }

    public function onManagerPageBeforeRender()
    {

    }

    /**
     * Load tab Meta.
     *
     * @access protected.
     * @param Object $resource.
     */
    protected function loadMeta($resource)
    {
        //$properties = $this->seosuite->getResourceProperties($resource->get('id'));

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
}
