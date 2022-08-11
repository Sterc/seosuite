<?php
use Sterc\SeoSuite\SeoSuite;

$seosuite = $modx->services->get('seosuite');
if ($seosuite instanceof SeoSuite) {
    $seosuite->firePlugins($modx->event, $scriptProperties);
}
