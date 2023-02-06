<?php
namespace Sterc\SeoSuite\Processors\Mgr\Migration;

use MODX\Revolution\Processors\Processor;
use MODX\Revolution\modResource;

use Sterc\SeoSuite\Model\SeoSuiteUrl;
use Sterc\SeoSuite\Model\SeoSuiteResource;
use Sterc\SeoSuite\Model\SeoSuiteRedirect;

class Migrate extends Processor
{
    public $logMessage = [];

    /**
     * @access public.
     * @return Mixed.
     */
    public function initialize()
    {
        $this->modx->seosuite = $this->modx->services->get('seosuite');

        return parent::initialize();
    }

    /**
     * @access public.
     * @return Mixed.
     */
    public function process()
    {
        $source = $this->getProperty('source');

        switch ($source) {
            case 'seosuitev1':
                $this->migrateSeoSuite();
                break;
            case 'seopro':
                $this->migrateSeoPro();
                break;
            case 'seotab':
                $this->migrateSeoTab();
                break;
        }

        $output = [
            'message' => implode(PHP_EOL, $this->logMessage)
        ];

        return $this->outputArray($output);
    }

    /**
     * Migrate SEO Suite data.
     */
    protected function migrateSeoSuite()
    {
        // $this->log('Start migration SEO Suite data...');

        /* Migrate redirects. */
        $results = $this->modx->query('SELECT * FROM ' . $this->modx->getOption('table_prefix') . 'seosuite_urls WHERE solved = 1');
        while ($record = $results->fetch(PDO::FETCH_ASSOC)) {
            if (!$context = $this->getContextByUrl($record['url'])) {
                $this->log('Could not import redirect. Failed to find context for url: ' . $record['url'], 'error');

                continue;
            }

            $formattedUrl = $this->formatUrl($record['url']);
            if (!$ssRedirect = $this->modx->getObject(SeoSuiteRedirect::class, ['context_key' => $context, 'old_url' => $formattedUrl, 'resource_id' => $record['redirect_to']])) {
                $ssRedirect = $this->modx->newObject(SeoSuiteRedirect::class);
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
        $results = $this->modx->query('SELECT * FROM ' . $this->modx->getOption(\xPDO::OPT_TABLE_PREFIX) . 'seosuite_urls WHERE solved = 0');
        while ($record = $results->fetch(PDO::FETCH_ASSOC)) {
            if (!$context = $this->getContextByUrl($record['url'])) {
                $this->log('Could not import url. Failed to find context for url: ' . $record['url'], 'error');

                continue;
            }

            $formattedUrl = $this->formatUrl($record['url']);
            if (!$ssUrl = $this->modx->getObject(SeoSuiteUrl::class, ['context_key' => $context, 'url' => $formattedUrl])) {
                $ssUrl = $this->modx->newObject(SeoSuiteUrl::class);
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

    /**
     * Migrate SEO Pro data.
     */
    protected function migrateSeoPro()
    {
        $this->log('Start migration for SEO Pro...');

        $results = $this->modx->query('SELECT * FROM ' . $this->modx->getOption('table_prefix') . 'seopro_keywords WHERE keywords IS NOT NULL AND keywords != ""');

        while ($record = $results->fetch(\PDO::FETCH_ASSOC)) {
            if (!$seoSuiteResource = $this->modx->getObject(SeoSuiteResource::class, ['resource_id' => $record['resource']])) {
                $seoSuiteResource = $this->modx->newObject(SeoSuiteResource::class);
                $seoSuiteResource->set('resource_id', $record['resource']);
            }

            $keywords = [];
            if (!empty($seoSuiteResource->get('keywords'))) {
                $keywords = explode(',', $seoSuiteResource->get('keywords'));
            }

            $keywords = array_unique(array_merge($keywords, explode(',', $record['keywords'])));

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
        $this->log('Start migration for SEO Tab...');

        /* Migrate redirects. */
        $results = $this->modx->query('SELECT * FROM ' . $this->modx->getOption('table_prefix') . 'seo_urls');

        while ($record = $results->fetch(\PDO::FETCH_ASSOC)) {
            /* Create redirect if not exists. */
            $oldUrlArray = parse_url(urldecode($record['url']));
            if (!isset($oldUrlArray['path'])) {
                continue;
            }

            $oldUrl = trim($oldUrlArray['path'], '/');
            if (!$this->modx->getObject(SeoSuiteRedirect::class, [
                'resource_id' => $record['resource'],
                'context_key' => $record['context_key'],
                'old_url'     => $oldUrl
            ])) {
                $redirect = $this->modx->newObject(SeoSuiteRedirect::class);
                $redirect->fromArray([
                    'context_key'   => $record['context_key'],
                    'resource_id'   => $record['resource'],
                    'old_url'       => $oldUrl,
                    'new_url'       => $record['resource'],
                    'redirect_type' => 'HTTP/1.1 301 Moved Permanently',
                    'active'        => true
                ]);

                $redirect->save();
            }
        }

        /* Migrate properties. */
        $query = $this->modx->newQuery(modResource::class);
        $query->where([
            'properties:LIKE' => '%stercseo%'
        ]);

        foreach ($this->modx->getIterator(modResource::class, $query) as $modResource) {
            $ssResource = $this->modx->getObject(SeoSuiteResource::class, [
                'resource_id' => $modResource->get('id')
            ]);

            if (!$ssResource) {
                $ssResource = $this->modx->newObject(SeoSuiteResource::class);
                $ssResource->set('resource_id', $modResource->get('id'));
            }

            if ($oldProperties = $modResource->getProperties('stercseo')) {
                $ssResource->fromArray([
                    'index_type'         => isset($oldProperties['index']) ? $oldProperties['index'] : 1,
                    'follow_type'        => isset($oldProperties['follow']) ? $oldProperties['follow'] : 1,
                    'sitemap'            => isset($oldProperties['sitemap']) ? $oldProperties['sitemap'] : 1,
                    'sitemap_prio'       => isset($oldProperties['priority']) ? str_replace(['0.25', '0.5', '1.0'], ['low', 'normal', 'high'], $oldProperties['priority']) : 'normal',
                    'sitemap_changefreq' => isset($oldProperties['changefreq']) ? str_replace(['high', 'normal'], ['always', 'hourly'], $oldProperties['changefreq']) : 'hourly'
                ]);

                $ssResource->save();
            }
        }

        if ($plugin = $this->modx->getObject('modPlugin', ['name' => 'StercSEO'])) {
            if (!$plugin->disabled) {
                $plugin->set('disabled', true);
                $plugin->save();

                $this->log('Plugin StercSEO disabled');
            }
        }

        $this->log('Finished migrating SEO Tab data');
    }

    private function log($message)
    {
        // Decrease log level to enable INFO level logging
        // First get the current log level
        // $logLevel = $this->modx->getOption('log_level');
        // $this->modx->setLogLevel(MODx::LOG_LEVEL_INFO);

        // $logTarget = [
        //     'target'  => 'FILE',
        //     'options' => [
        //         'filepath' => $this->modx->formit->config['assets_path'],
        //         'filename' => 'migration.log'
        //     ]
        // ];
        // $this->modx->log(MODx::LOG_LEVEL_INFO, $message, $logTarget);
        // // Set log level back to original
        // $this->modx->setLogLevel($logLevel);
        $this->logMessage[] = $message;
    }
}
