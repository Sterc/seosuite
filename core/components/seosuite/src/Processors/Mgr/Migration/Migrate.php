<?php
namespace Sterc\SeoSuite\Processors\Mgr\Migration;

use MODX\Revolution\Processors\Processor;
use MODX\Revolution\modResource;
use MODX\Revolution\modContext;

use Sterc\SeoSuite\Model\SeoSuiteUrl;
use Sterc\SeoSuite\Model\SeoSuiteResource;
use Sterc\SeoSuite\Model\SeoSuiteRedirect;

class Migrate extends Processor
{
    private $logMessage = [];

    private $domains = [];

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
            'message' => implode('<br>', $this->logMessage)
        ];

        return $this->outputArray($output);
    }

    /**
     * Migrate SEO Suite data.
     */
    protected function migrateSeoSuite()
    {
        $new = 0;
        $updated = 0;
        $failed = 0;

        /* Migrate redirects. */
        $results = $this->modx->query('SELECT * FROM ' . $this->modx->getOption('table_prefix') . 'seosuite_urls WHERE solved = 1');
        while ($record = $results->fetch(\PDO::FETCH_ASSOC)) {
            if (!$context = $this->getContextByUrl($record['url'])) {
                $this->logToFile('Could not import redirect. Failed to find context for url: ' . $record['url'], 'error');
                $failed++;

                continue;
            }

            $formattedUrl = $this->formatUrl($record['url']);
            if (!$ssRedirect = $this->modx->getObject(SeoSuiteRedirect::class, ['context_key' => $context, 'old_url' => $formattedUrl, 'resource_id' => $record['redirect_to']])) {
                $ssRedirect = $this->modx->newObject(SeoSuiteRedirect::class);

                $new++;
            } else {
                $updated++;
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

        $this->log('Finished migrating SEO Suite V1 redirects.');
        $this->log('Created: <b> ' . $new . '</b>, Updated: <b>' . $updated . '</b>');

        if ($failed > 0) {
            $this->log('Warning: <b>' . $failed . '</b> redirects have failed to migrate. Please check the MODX error log for more information, and re-run the migration if needed.');
        }

        $this->log('---------------------------------------');

         /* Migrate 404 urls. */
        $new = 0;
        $updated = 0;
        $failed = 0;

        $results = $this->modx->query('SELECT * FROM ' . $this->modx->getOption('table_prefix') . 'seosuite_urls WHERE solved = 0');
        while ($record = $results->fetch(\PDO::FETCH_ASSOC)) {
            if (!$context = $this->getContextByUrl($record['url'])) {
                $this->logToFile('Could not import url. Failed to find context for url: ' . $record['url'], 'error');
                $failed++;

                continue;
            }

            $formattedUrl = $this->formatUrl($record['url']);
            if (!$ssUrl = $this->modx->getObject(SeoSuiteUrl::class, ['context_key' => $context, 'url' => $formattedUrl])) {
                $ssUrl = $this->modx->newObject(SeoSuiteUrl::class);

                $new++;
            } else {
                $updated++;
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

        $this->log('Finished migrating SEO Suite V1 404 urls.');
        $this->log('Created: <b> ' . $new . '</b>, Updated: <b>' . $updated . '</b>');

        if ($failed > 0) {
            $this->log('Warning: <b>' . $failed . '</b> urls have failed to migrate. Please check the MODX error log for more information, and re-run the migration if needed.');
        }
    }

    /**
     * Migrate SEO Pro data.
     */
    protected function migrateSeoPro()
    {
        $results = $this->modx->query('SELECT * FROM ' . $this->modx->getOption('table_prefix') . 'seopro_keywords WHERE keywords IS NOT NULL AND keywords != ""');

        $new = 0;
        $updated = 0;

        while ($record = $results->fetch(\PDO::FETCH_ASSOC)) {
            if (!$seoSuiteResource = $this->modx->getObject(SeoSuiteResource::class, ['resource_id' => $record['resource']])) {
                $seoSuiteResource = $this->modx->newObject(SeoSuiteResource::class);
                $seoSuiteResource->set('resource_id', $record['resource']);

                $new++;
            } else {
                $updated++;
            }

            $keywords = [];
            if (!empty($seoSuiteResource->get('keywords'))) {
                $keywords = explode(',', $seoSuiteResource->get('keywords'));
            }

            $keywords = array_unique(array_merge($keywords, explode(',', $record['keywords'])));

            $seoSuiteResource->set('keywords', implode(',', $keywords));
            $seoSuiteResource->save();
        }

        $this->log('Finished migrating SEO Pro data.');
        $this->log('Created: <b> ' . $new . '</b>, Updated: <b>' . $updated . '</b>');
    }

    /**
     * Migrate SEO Tab data.
     */
    protected function migrateSeoTab()
    {
        /* Migrate redirects. */
        $results = $this->modx->query('SELECT * FROM ' . $this->modx->getOption('table_prefix') . 'seo_urls');

        $new = 0;
        $skipped = 0;

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

                $new++;
            } else {
                $skipped++;
            }
        }

        $this->log('Finished migrating SEO Tab redirects.');
        $this->log('Created: <b> ' . $new . '</b>, Skipped (existing redirects): <b>' . $skipped . '</b>');
        $this->log('---------------------------------------');

        /* Migrate properties. */
        $query = $this->modx->newQuery(modResource::class);
        $query->where([
            'properties:LIKE' => '%stercseo%'
        ]);

        $new = 0;
        $updated = 0;

        foreach ($this->modx->getIterator(modResource::class, $query) as $modResource) {
            $ssResource = $this->modx->getObject(SeoSuiteResource::class, [
                'resource_id' => $modResource->get('id')
            ]);

            if (!$ssResource) {
                $ssResource = $this->modx->newObject(SeoSuiteResource::class);
                $ssResource->set('resource_id', $modResource->get('id'));

                $new++;
            } else {
                $updated++;
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

        $this->log('Finished migrating SEO Tab properties to SeoSuiteResource objects.');
        $this->log('Created: <b> ' . $new . '</b>, Updated: <b>' . $updated . '</b>');
    }

    /**
     * Log message to local log variable.
     *
     * @param string $message
     */
    private function log($message = '')
    {
        $this->logMessage[] = $message;
    }

    /**
     * Logs message to the MODX error log.
     *
     * @param string $message
     * @param string $level
     */
    private function logToFile($message = '', $level = 'info')
    {
        $logLevel = \modx::LOG_LEVEL_INFO;
        switch ($level) {
            case 'warning':
                $logLevel = \modX::LOG_LEVEL_WARN;
                break;
            case 'error':
                $logLevel = \modX::LOG_LEVEL_ERROR;
                break;
        }
        $this->modx->log($logLevel, '[SEO SUITE MIGRATION] ' . $message);
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
     * This strips the domain from the request.
     * For example: domain.tld/path/to/page will become path/to/page.
     *
     * @access public.
     * @param String $request.
     * @return String.
     */
    public function formatUrl($request)
    {
        if (!empty($request)) {
            $parts   = parse_url($request);

            if (isset($parts['path'])) {
                $request = $parts['path'];
            }
        }

        return urldecode(trim($request, '/'));
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
        foreach ($this->modx->getIterator(modContext::class, ['key:!=' => 'mgr']) as $modContext) {
            $modContext->prepare();

            $url                 = $this->cleanUrl($this->modx->makeUrl($modContext->getOption('site_start'), $modContext->get('key'), '', 'full'));
            $this->domains[$url] = $modContext->get('key');
        }

        uksort($this->domains, function ($first, $second) {
            return strlen($second) - strlen($first);
        });
    }
}
