<?php
/**
 * SeoSuite
 *
 * Copyright 2019 by Sterc <modx@sterc.com>
 */
$instance = $modx->getService('seosuite', 'SeoSuite', $modx->getOption('seosuite.core_path', null, $modx->getOption('core_path') . 'components/seosuite/') . 'model/seosuite/');
if ($instance instanceof SeoSuite) {
    $instance->firePlugins($modx->event, $scriptProperties);
}