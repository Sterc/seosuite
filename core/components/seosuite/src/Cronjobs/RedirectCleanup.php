<?php
namespace Sterc\SeoSuite\Cronjobs;

use Sterc\SeoSuite\Model\SeoSuiteUrl;

class RedirectCleanup extends Base
{
     /**
     * Cleanup unresolved redirects.
     *
     * @param $options
     */
    public function process($options)
    {
        $this->log('Starting cleaning up redirects');

        $till      = isset($options['till']) && !empty($options['till']) ? $options['till'] : date('Y-m-d H:i:s', strtotime('-1 month'));
        $triggered = isset($options['triggered']) && !empty($options['triggered']) ? $options['triggered'] : 1;

        $removed = $this->modx->removeCollection(SeoSuiteUrl::class, [
            'createdon:<=' => $till,
            'visits:<='    => $triggered
        ]);

        $this->log('Removed redirects: ' . $removed);
        $this->log('Finished cleaning up redirects');
    }
}
