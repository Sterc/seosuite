<?php
namespace Sterc\SeoSuite\Processors\Mgr\Migration;

use MODX\Revolution\Processors\Processor;
use MODX\Revolution\modResource;

class Status extends Processor
{
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
        set_time_limit(300);

        $v1Redirects = $this->seoSuiteV1RedirectCount();
        $v1Urls = $this->seoSuiteV1UrlCount();

        $seoPro = $this->seoProCount();

        $seoTabUrls = $this->seoTabUrlCount();
        $seoTabResources = $this->seoTabResourceCount();

        $output = [
            'seosuitev1'    => $v1Redirects || $v1Urls ? $this->modx->lexicon('seosuite.migration.seosuitev1.results', ['redirects' => $v1Redirects, 'urls' => $v1Urls]) : null,
            'seopro'        => $seoPro ? $this->modx->lexicon('seosuite.migration.seopro.results', ['count' => $seoPro]) : null,
            'seotab'        => $seoTabUrls || $seoTabResources ? $this->modx->lexicon('seosuite.migration.seotab.results', ['urls' => $seoTabUrls, 'resources' => $seoTabResources]) : null
        ];

        return $this->outputArray($output);
    }

    private function seoSuiteV1RedirectCount ()
    {
        if ($query = $this->modx->query('SELECT COUNT(*) FROM ' . $this->modx->getOption('table_prefix') . 'seosuite_urls WHERE solved = 1')) {
            $results = $query->fetch();

            return $results[0] ? (int) $results[0] : 0;
        }

        return 0;
    }

    private function seoSuiteV1UrlCount ()
    {
        if ($query = $this->modx->query('SELECT COUNT(*) FROM ' . $this->modx->getOption('table_prefix') . 'seosuite_urls WHERE solved = 0')) {
            $results = $query->fetch();

            return $results[0] ? (int) $results[0] : 0;
        }

        return 0;
    }

    private function seoProCount ()
    {
        if ($query = $this->modx->query('SELECT COUNT(*) FROM ' . $this->modx->getOption('table_prefix') . 'seopro_keywords WHERE keywords IS NOT NULL AND keywords != ""')) {
            $results = $query->fetch();

            return $results[0] ? (int) $results[0] : 0;
        }

        return 0;
    }

    private function seoTabUrlCount ()
    {
        if ($query = $this->modx->query('SELECT COUNT(*) FROM ' . $this->modx->getOption('table_prefix') . 'seo_urls')) {
            $results = $query->fetch();

            return $results[0] ? (int) $results[0] : 0;
        }

        return 0;
    }

    private function seoTabResourceCount ()
    {
        $query = $this->modx->newQuery(modResource::class);
        $query->where([
            'properties:LIKE' => '%stercseo%',
            'deleted' => 0
        ]);

        return $this->modx->getCount(modResource::class, $query);
    }
}
