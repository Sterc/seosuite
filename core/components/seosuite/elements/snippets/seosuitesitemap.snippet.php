<?php
$modelPath = $modx->getOption(
        'seosuite.core_path',
        null,
        $modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/seosuite/'
    ) . 'model/seosuite/snippets/';
$modx->loadClass('SeoSuiteSnippets', $modelPath, true, true);
$ssSnippets = new SeoSuiteSnippets($modx);

if (!$ssSnippets instanceof SeoSuiteSnippets) {
    $modx->log(xPDO::LOG_LEVEL_ERROR, '[SeoSuiteMeta] Failed to initialize class SeoSuiteSnippets.');
}

return $ssSnippets->seosuiteSitemap($scriptProperties);