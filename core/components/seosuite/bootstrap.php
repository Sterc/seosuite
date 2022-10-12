<?php
/**
 * @see \MODX\Revolution\modX::_initNamespaces()
 */
require_once __DIR__ . '/vendor/autoload.php';

// Add your classes to modx's autoloader
$modx->addPackage('Sterc\SeoSuite\Model', __DIR__ . '/src/', null, 'Sterc\\SeoSuite\\');

// Register base class in the service container
$modx->services->add('seosuite', function() use ($modx) {
    return new \Sterc\SeoSuite\SeoSuite($modx);
});
