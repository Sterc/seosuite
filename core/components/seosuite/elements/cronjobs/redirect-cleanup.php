<?php
if (file_exists(dirname(dirname(dirname(dirname(dirname(__DIR__))))) . '/config.core.php')) {
    require_once dirname(dirname(dirname(dirname(dirname(__DIR__))))) . '/config.core.php';
} else {
    require_once dirname(dirname(dirname(dirname(dirname(dirname(dirname(__DIR__))))))) . '/config.core.php';
}
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';

$modx = new modX();
$modx->initialize();

$modx->getService('error', 'error.modError');
$modx->setLogLevel(modX::LOG_LEVEL_INFO);
$modx->setLogTarget(XPDO_CLI_MODE ? 'ECHO' : 'HTML');

/*
 * 1x of minder getriggerd in en minimaal 1 maand oud is, dan verwijderen.
 */
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
        array(
            'till::',
            'triggered::'
        )
    );
} else {
    $options = $_GET;
}

$service->cleanupRedirects($options);
