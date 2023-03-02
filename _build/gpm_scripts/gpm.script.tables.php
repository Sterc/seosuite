<?php
use xPDO\Transport\xPDOTransport;

/**
 * Create tables
 *
 * THIS SCRIPT IS AUTOMATICALLY GENERATED, NO CHANGES WILL APPLY
 *
 * @package seosuite
 * @subpackage build.scripts
 *
 * @var \xPDO\Transport\xPDOTransport $transport
 * @var array $object
 * @var array $options
 */

$modx =& $transport->xpdo;

if ($options[xPDOTransport::PACKAGE_ACTION] === xPDOTransport::ACTION_UNINSTALL) return true;

$manager = $modx->getManager();

$manager->createObjectContainer(\Sterc\SeoSuite\Model\SeoSuiteRedirect::class);
$manager->createObjectContainer(\Sterc\SeoSuite\Model\SeoSuiteResource::class);
$manager->createObjectContainer(\Sterc\SeoSuite\Model\SeoSuiteSocial::class);
$manager->createObjectContainer(\Sterc\SeoSuite\Model\SeoSuiteUrl::class);

return true;
