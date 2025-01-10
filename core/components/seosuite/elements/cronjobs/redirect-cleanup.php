<?php
use Sterc\SeoSuite\Cronjobs\RedirectCleanup;

/* GPM path. */
if (file_exists(dirname(dirname(dirname(dirname(dirname(__DIR__))))) . '/config.core.php')) {
    require_once dirname(dirname(dirname(dirname(dirname(__DIR__))))) . '/config.core.php';
} else {
    /* Normal installation path. */
    require_once dirname(dirname(dirname(dirname(__DIR__))))  . '/config/config.inc.php';
}

require_once MODX_CORE_PATH . 'model/modx/modx.class.php';

$modx = new MODX\Revolution\modX();
$modx->initialize();

$modx->getService('error', 'error.modError');
$modx->setLogLevel(modX::LOG_LEVEL_INFO);
$modx->setLogTarget(XPDO_CLI_MODE ? 'ECHO' : 'HTML');

$options = $_GET;
if (XPDO_CLI_MODE) {
    $options = getopt('',
        [
            'till::',
            'triggered::'
        ]
    );
}

$cronjob = new RedirectCleanup($modx);
$cronjob->process($options);
