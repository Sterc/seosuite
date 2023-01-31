<?php
namespace Sterc\SeoSuite\Snippets;

use Sterc\SeoSuite\SeoSuite;

class Base extends SeoSuite
{
    protected $babel;

    /**
     * Get Babel.
     * @return mixed
     */
    protected function getBabel()
    {
        if (!$this->babel) {
            $this->setBabel();
        }

        return $this->babel;
    }

    /**
     * Set babel.
     */
    protected function setBabel()
    {
        $this->babel = &$this->modx->getService(
            'babel',
            'Babel',
            $this->modx->getOption(
                'babel.core_path',
                null,
                $this->modx->getOption('core_path') . 'components/babel/'
            ) . 'model/babel/'
        );
    }

    /**
     * Adds alternative language links to sitemap XML.
     *
     * @param $resource
     * @param $options
     * @return array|string
     */
    protected function getAlternateLinks($resource, $options = [])
    {
        if (!$this->shouldAddBabelAlternativeLinks($resource)) {
            return '';
        }

        $alternates   = [];
        $html         = [];
        $translations = $this->getBabel()->getLinkedResources($resource->get('id'));
        foreach ($translations as $contextKey => $resourceId) {
            if ($ctx = $this->modx->getContext($contextKey)) {
                $alternate = [
                    'cultureKey' => $ctx->getOption('cultureKey', ['context_key' => $contextKey], 'en'),
                    'url'        => $this->modx->makeUrl($resourceId, '', '', 'full'),
                    'locale'     => $this->config['meta']['default_alternate_context'] === $ctx->get('key') ? 'x-default' : $ctx->getOption('locale')
                ];

                if (isset($options['alternateTpl']) && !empty($options['alternateTpl'])) {
                    $html[] = $this->getChunk($options['alternateTpl'], $alternate);
                }

                $alternates[] = $alternate;
            }
        }

        if (isset($options['alternateTpl']) && !empty($options['alternateTpl'])) {
            return implode(PHP_EOL, $html);
        }

        return $alternates;
    }

       /**
     * Determine if babel alternative links should be added.
     * @param modResource|null $resource
     * @return bool
     */
    protected function shouldAddBabelAlternativeLinks($resource)
    {
        if ($this->config['sitemap']['babel_add_alternate_links'] === false ||
            !file_exists($this->modx->getOption('babel.core_path', null, $this->modx->getOption('core_path') . 'components/babel/') . 'model/babel/') ||
            !$resource
        ) {
            return false;
        }

        return true;
    }
}