<?php
use Sterc\SeoSuite\SeoSuite;

$seosuite = $modx->services->get('seosuite');
$seosuite->firePlugins($modx->event, $scriptProperties);
