<?php
/* GPM path. */
if (file_exists(dirname(dirname(dirname(dirname(dirname(__DIR__))))) . '/config.core.php')) {
    require_once dirname(dirname(dirname(dirname(dirname(__DIR__))))) . '/config.core.php';
} else {
    /* Normal installation path. */
    require_once dirname(dirname(dirname(dirname(__DIR__))))  . '/config/config.inc.php';
}
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';

$modx = new modX();
$modx->initialize();

$modx->getService('error', 'error.modError');
$modx->setLogLevel(modX::LOG_LEVEL_INFO);
$modx->setLogTarget(XPDO_CLI_MODE ? 'ECHO' : 'HTML');

/* Redirect redirect if it is triggered only once or less and is at least one month old. */
$service = $modx->getService(
    'seosuitecronjob',
    'SeoSuiteCronjob',
    $modx->getOption(
        'seosuite.core_path',
        null,
        $modx->getOption('core_path') . 'components/seosuite/'
    ) . 'model/seosuite/',
    $modx
);

if (!$service instanceof SeoSuiteCronjob) {
    die('Could not load SeoSuiteCronjob class.');
}

$options = $_GET;
if (XPDO_CLI_MODE) {
    $options = getopt('',
        [
            'till::',
            'triggered::'
        ]
    );
}

$service->cleanupRedirects($options);
