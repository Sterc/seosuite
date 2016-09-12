<?php
/**
 * Buster404 Connector
 *
 * @package buster404
 */
require_once dirname(dirname(dirname(dirname(__FILE__)))).'/config.core.php';
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
require_once MODX_CONNECTORS_PATH . 'index.php';

$corePath = $modx->getOption('buster404.core_path', null, $modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/buster404/');
$buster404 = $modx->getService(
    'buster404',
    'Buster404',
    $corePath . 'model/buster404/',
    array(
        'core_path' => $corePath
    )
);

/* handle request */
$modx->request->handleRequest(
    array(
        'processors_path' => $buster404->getOption('processorsPath', null, $corePath . 'processors/'),
        'location' => '',
    )
);