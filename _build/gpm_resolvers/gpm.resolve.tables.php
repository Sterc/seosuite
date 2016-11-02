<?php
/**
 * Resolve creating db tables
 *
 * THIS RESOLVER IS AUTOMATICALLY GENERATED, NO CHANGES WILL APPLY
 *
 * @package buster404
 * @subpackage build
 */

if ($object->xpdo) {
    $modx =& $object->xpdo;
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:
            $modelPath = $modx->getOption('buster404.core_path', null, $modx->getOption('core_path') . 'components/buster404/') . 'model/';
            
            $modx->addPackage('buster404', $modelPath, null);


            $manager = $modx->getManager();

            $manager->createObjectContainer('Buster404Url');

            break;
    }
}

return true;