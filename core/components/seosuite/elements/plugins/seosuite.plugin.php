<?php
/**
 * Plugin for SEO Suite for handling the redirects.
 */
$corePath = $modx->getOption(
    'seosuite.core_path',
    null,
    $modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/seosuite/'
);
$seoSuite = $modx->getService(
    'seosuite',
    'SeoSuite',
    $corePath . 'model/seosuite/',
    array(
        'core_path' => $corePath
    )
);

if (!($seoSuite instanceof SeoSuite)) {
    $modx->log(MODX_LOG_LEVEL_ERROR, '[plugin.SEO Suite] Could not initialize SeoSuite.');

    return '';
}

$seoSuite->firePlugins($modx->event, $scriptProperties);