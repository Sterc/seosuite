<?php

/**
 * SeoSuite
 *
 * Copyright 2019 by Sterc <modx@sterc.com>
 */

require_once dirname(dirname(dirname(__DIR__))) . '/config.core.php';
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
require_once MODX_CONNECTORS_PATH . 'index.php';

$modx->getService('seosuite', 'SeoSuite', $modx->getOption('seosuite.core_path', null, $modx->getOption('core_path') . 'components/seosuite/') . 'model/seosuite/');

if ($modx->seosuite instanceof SeoSuite) {
    $modx->request->handleRequest([
        'processors_path'   => $modx->seosuite->config['processors_path'],
        'location'          => ''
    ]);
}
