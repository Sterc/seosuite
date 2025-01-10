<?php
use Sterc\SeoSuite\SeoSuite;

require_once dirname(__DIR__, 3) . '/config.core.php';
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
require_once MODX_CONNECTORS_PATH . 'index.php';

$seosuite = $modx->services->get('seosuite');
if ($seosuite instanceof SeoSuite) {
    $modx->request->handleRequest([
        'processors_path'   => $seosuite->config['processors_path'],
        'location'          => ''
    ]);
}
