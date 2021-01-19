<?php

// Convenience
/** @var modX $modx */
if (!isset($modx) && isset($object) && isset($object->xpdo)) {
    $modx = $object->xpdo;
}

$resolver = new SeoSuiteSetupOptionsResolver($modx, $options);
return $resolver->process();

class SeoSuiteSetupOptionsResolver
{
    /**
     * Name for migration finished system setting.
     */
    const KEY_MIGRATION_FINISHED = 'seosuite.migration_finished';

    /**
     * @var $modx modX Holds modX class.
     */
    protected $modx;

    /**
     * @var array $options Holds all options.
     */
    protected $options = [];

    /**
     * @var array $domains Holds all domains.
     */
    protected $domains = [];

    /**
     * SeoSuiteSetupOptionsResolver constructor.
     * @param $modx
     * @param $options
     */
    public function __construct($modx, $options)
    {
        $this->modx    = $modx;
        $this->options = $options;
    }

    /**
     * Process setup options resolver.
     * @return bool
     */
    public function process()
    {
        /* If uninstall, then return true. */
        if ($this->options[xPDOTransport::PACKAGE_ACTION] === xPDOTransport::ACTION_UNINSTALL) {
            return true;
        }

        $this->savePriorityUpdateValues();

        /* Check if migration is already finished. */
        if ($this->modx->getOption(self::KEY_MIGRATION_FINISHED, null, false)) {
            return true;
        }

        $migrateSEOSuite = array_key_exists('migrate_seosuitev1', $this->options) ? true : false;
        $migrateSEOPro   = array_key_exists('migrate_seopro', $this->options) ? true : false;
        $migrateSEOTab   = array_key_exists('migrate_seotab', $this->options) ? true : false;

        if ($migrateSEOSuite) {
            if ($this->modx->query('SELECT * FROM ' . $this->modx->getOption(xPDO::OPT_TABLE_PREFIX) . 'seosuite_urls')) {
                $this->migrateSeoSuite();
            } else {
                $this->log('Cannot migrate data because the SEO Suite V1 is not installed.', 'error');
            }
        }

        if ($migrateSEOPro) {
            if ($this->isInstalledPackage('seopro')) {
                $this->migrateSeoPro();
            } else {
                $this->log('Cannot migrate data because the SEO Pro package is not installed.', 'error');
            }
        }

        if ($migrateSEOTab) {
            if ($this->isInstalledPackage('stercseo')) {
                $this->migrateSEOTab();
            } else {
                $this->log('Cannot migrate data because the SEO Tab package is not installed.', 'error');
            }
        }

        /*  Set migration finished setting. */
        $migrationSetting = $this->modx->getObject('modSystemSetting', ['key' => self::KEY_MIGRATION_FINISHED]);
        if ($migrationSetting instanceof modSystemSetting) {
            $migrationSetting->set('value', true);
            $migrationSetting->save();
        }
    }

    /**
     * Save priority update values.
     */
    protected function savePriorityUpdateValues()
    {
        foreach (['user_name', 'user_email'] as $key) {
            if (isset($this->options[$key])) {
                $settingObject = $this->modx->getObject(
                    'modSystemSetting',
                    ['key' => 'seosuite.' . $key]
                );

                if ($settingObject) {
                    $settingObject->set('value', $this->options[$key]);
                    $settingObject->save();
                }
            }
        }
    }


    /**
     * Migrate SEO Suite data.
     */
    protected function migrateSeoSuite()
    {
        $this->log('Start migration SEO Suite data...');

        /* Migrate redirects. */
        $results = $this->modx->query('SELECT * FROM ' . $this->modx->getOption(xPDO::OPT_TABLE_PREFIX) . 'seosuite_urls WHERE solved = 1');
        while ($record = $results->fetch(PDO::FETCH_ASSOC)) {
            if (!$context = $this->getContextByUrl($record['url'])) {
                $this->log('Could not import redirect. Failed to find context for url: ' . $record['url'], 'error');

                continue;
            }

            $formattedUrl = $this->modx->seosuite->formatUrl($record['url']);
            if (!$ssRedirect = $this->modx->getObject('SeoSuiteRedirect', ['context_key' => $context, 'old_url' => $formattedUrl, 'resource_id' => $record['redirect_to']])) {
                $ssRedirect = $this->modx->newObject('SeoSuiteRedirect');
            }

            $ssRedirect->fromArray([
                'context_key'   => $context,
                'resource_id'   => $record['redirect_to'],
                'old_url'       => $formattedUrl,
                'new_url'       => $record['redirect_to'],
                'suggestions'   => $record['suggestions'],
                'redirect_type' => 'HTTP/1.1 301 Moved Permanently',
                'active'        => 1,
                'visits'        => $record['triggered'],
                'last_visit'    => $record['last_triggered']
            ]);

            $ssRedirect->save();
        }

        /* Migrate 404 urls. */
        $results = $this->modx->query('SELECT * FROM ' . $this->modx->getOption(xPDO::OPT_TABLE_PREFIX) . 'seosuite_urls WHERE solved = 0');
        while ($record = $results->fetch(PDO::FETCH_ASSOC)) {
            if (!$context = $this->getContextByUrl($record['url'])) {
                $this->log('Could not import url. Failed to find context for url: ' . $record['url'], 'error');

                continue;
            }

            $formattedUrl = $this->modx->seosuite->formatUrl($record['url']);
            if (!$ssUrl = $this->modx->getObject('SeoSuiteUrl', ['context_key' => $context, 'url' => $formattedUrl])) {
                $ssUrl = $this->modx->newObject('SeoSuiteUrl');
            }

            $ssUrl->fromArray([
                'context_key' => $context,
                'url'         => $formattedUrl,
                'suggestions' => $record['suggestions'],
                'visits'      => $record['triggered'],
                'last_visit'  => $record['last_triggered'],
                'createdon'   => $record['createdon']
            ]);

            $ssUrl->save();
        }

        $this->log('Finished migrating SEO Suite V1 data');
    }

    protected function getContextByUrl($url)
    {
        foreach ($this->getDomains() as $domain => $contextKey) {
            if (preg_match('#^' . $domain . '(.*)$#i', $this->cleanUrl($url)) === 1) {
                return $contextKey;
            }
        }

        return null;
    }

    /**
     * Cleans URL, removing protocol and WWW.
     * @param $url
     * @return string|string[]
     */
    protected function cleanUrl($url)
    {
        $url = preg_replace('(^https?://)', '', $url );
        $url = str_replace('www.', '', $url);

        return $url;
    }

    /**
     * Get Domains.
     * @return array
     */
    protected function getDomains()
    {
        if (!$this->domains) {
            $this->setDomains();
        }

        return $this->domains;
    }

    /**
     * Set domains.
     */
    protected function setDomains()
    {
        foreach ($this->modx->getIterator('modContext', ['key:!=' => 'mgr']) as $modContext) {
            $modContext->prepare();

            $url                 = $this->cleanUrl($this->modx->makeUrl($modContext->getOption('site_start'), $modContext->get('key'), '', 'full'));
            $this->domains[$url] = $modContext->get('key');
        }

        uksort($this->domains, function ($first, $second) {
            return strlen($second) - strlen($first);
        });
    }

    /**
     * Migrate SEO Pro data.
     */
    protected function migrateSeoPro()
    {
        $this->log('Start migration SEO Pro data...');

        foreach ($this->modx->getIterator('seoKeywords') as $seoProKeyword) {
            if (!$seoSuiteResource = $this->modx->getObject('SeoSuiteResource', ['resource_id' => $seoProKeyword->get('resource')])) {
                $seoSuiteResource = $this->modx->newObject('SeoSuiteResource');
                $seoSuiteResource->set('resource_id', $seoProKeyword->get('resource'));
            }

            $keywords = [];
            if (!empty($seoSuiteResource->get('keywords'))) {
                $keywords = explode(',', $seoSuiteResource->get('keywords'));
            }

            $keywords = array_unique(array_merge($keywords, explode(',', $seoProKeyword->get('keywords'))));

            $seoSuiteResource->set('keywords', implode(',', $keywords));
            $seoSuiteResource->save();
        }

        $this->log('Finished migrating SEO Pro data');
    }

    /**
     * Migrate SEO Tab data.
     */
    protected function migrateSeoTab()
    {
        $this->log('Start migration SEO Tab data...');

        /* Migrate redirects. */
        $corePath = $this->modx->getOption('stercseo.core_path', null, $this->modx->getOption('core_path') . 'components/stercseo/');
        $this->modx->loadClass('seoUrl', $corePath . 'model/stercseo/');
        foreach ($this->modx->getIterator('seoUrl') as $seoUrl) {
            /* Create redirect if not exists. */
            $oldUrlArray = parse_url(urldecode($seoUrl->get('url')));
            if (!isset($oldUrlArray['path'])) {
                continue;
            }

            $oldUrl = trim($oldUrlArray['path'], '/');
            if (!$this->modx->getObject('SeoSuiteRedirect', [
                'resource_id' => $seoUrl->get('resource'),
                'context_key' => $seoUrl->get('context_key'),
                'old_url'     => $oldUrl
            ])) {
                $redirect = $this->modx->newObject('SeoSuiteRedirect');
                $redirect->fromArray([
                    'context_key' => $seoUrl->get('context_key'),
                    'resource_id' => $seoUrl->get('resource'),
                    'old_url'     => $oldUrl,
                    'new_url'     => $seoUrl->get('resource'),
                    'active'      => true
                ]);

                $redirect->save();
            }
        }

        /* Migrate properties. */
        $query = $this->modx->newQuery('modResource');
        $query->where([
            'properties:LIKE' => '%stercseo%'
        ]);
        foreach ($this->modx->getIterator('modResource', $query) as $modResource) {
            $ssResource = $this->modx->getObject('SeoSuiteResource', [
                'resource_id' => $modResource->get('id')
            ]);

            if (!$ssResource) {
                $ssResource = $this->modx->newObject('SeoSuiteResource');
                $ssResource->set('resource_id', $modResource->get('id'));
            }

            if ($oldProperties = $modResource->getProperties('stercseo')) {
                $ssResource->fromArray([
                    'index_type'         => isset($oldProperties['index']) ? $oldProperties['index'] : 1,
                    'follow_type'        => isset($oldProperties['follow']) ? $oldProperties['follow'] : 1,
                    'sitemap'            => isset($oldProperties['sitemap']) ? $oldProperties['sitemap'] : 1,
                    'sitemap_prio'       => isset($oldProperties['priority']) ? str_replace(['0.25', '0.5', '1.0'], ['low', 'normal', 'high'], $oldProperties['priority']) : 'normal',
                    'sitemap_changefreq' => isset($oldProperties['changefreq']) ? str_replace(['always', 'hourly'], ['high', 'normal'], $oldProperties['changefreq']) : 'normal'
                ]);

                $ssResource->save();
            }
        }

        $this->log('Finished migrating SEO Tab data');
    }

    /**
     * @param string $message
     */
    protected function log($message = '', $level = 'info')
    {
        $logLevel = xPDO::LOG_LEVEL_INFO;
        switch ($level) {
            case 'warning':
                $logLevel = xPDO::LOG_LEVEL_WARN;
                break;
            case 'error':
                $logLevel = xPDO::LOG_LEVEL_ERROR;
                break;
        }
        $this->modx->log($logLevel, '[SEO SUITE] ' . $message);
    }

    /**
     * Check if a package is installed.
     * @param $package
     * @return bool
     */
    protected function isInstalledPackage($package)
    {
        return file_exists(MODX_CORE_PATH . 'components/' . $package);
    }
}
