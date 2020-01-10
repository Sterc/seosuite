<?php
/**
 * Resolve creating db tables
 *
 * THIS RESOLVER IS AUTOMATICALLY GENERATED, NO CHANGES WILL APPLY
 *
 * @package seosuite
 * @subpackage build
 *
 * @var mixed $object
 * @var modX $modx
 * @var array $options
 */

if ($object->xpdo) {
    $modx =& $object->xpdo;
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:
            $modelPath = $modx->getOption('seosuite.core_path', null, $modx->getOption('core_path') . 'components/seosuite/') . 'model/';
            
            $modx->addPackage('seosuite', $modelPath, null);


            $manager = $modx->getManager();

            $manager->createObjectContainer('SeoSuiteRedirect');
            $manager->createObjectContainer('SeoSuiteResource');
            $manager->createObjectContainer('SeoSuiteSocial');
            $manager->createObjectContainer('SeoSuiteUrl');

            break;
    }
}

return true;