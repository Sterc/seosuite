<?php
use xPDO\Transport\xPDOTransport;
use MODX\Revolution\modAccessPolicyTemplate;
use MODX\Revolution\modAccessPermission;
use MODX\Revolution\modAccessPolicy;

$permissions = [[
    'name'          => 'seosuite',
    'description'   => 'To access SeoSuite CMP.',
    'templates'     => ['AdministratorTemplate']
],[
    'name'          => 'seosuite_tab_meta',
    'description'   => 'To view the SeoSuite keywords/character counters and search engine meta data.',
    'templates'     => ['AdministratorTemplate']
], [
    'name'          => 'seosuite_tab_seo',
    'description'   => 'To view the SeoSuite Search Engine tab and manage search engine settings.',
    'templates'     => ['AdministratorTemplate']
], [
    'name'          => 'seosuite_tab_social',
    'description'   => 'To view the SeoSuite Social tab and manage social settings.',
    'templates'     => ['AdministratorTemplate']
]];

$success = false;
if ($transport->xpdo) {
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:
            $modx =& $transport->xpdo;

            foreach ($modx->getCollection(modAccessPolicyTemplate::class) as $accessTemplate) {
                foreach ($permissions as $permission) {
                    if (!isset($permission['templates']) || in_array($accessTemplate->get('name'), $permission['templates'])) {
                        $accessPermission = $modx->getObject(modAccessPermission::class, [
                            'name'      => $permission['name'],
                            'template'  => $accessTemplate->get('id')
                        ]);

                        if (!$accessPermission) {
                            $accessPermission = $modx->newObject(modAccessPermission::class);

                            if ($accessPermission) {
                                $accessPermission->fromArray(array_merge($permission, [
                                    'template'  => $accessTemplate->get('id'),
                                    'value'     => 1
                                ]));

                                $accessPermission->save();
                            }
                        }
                    }
                }
            }

            foreach ($modx->getCollection(modAccessPolicy::class) as $accessPolicy) {
                $data = $accessPolicy->get('data');

                foreach ($permissions as $permission) {
                    if (isset($permission['policies'])) {
                        if (in_array($accessPolicy->get('name'), $permission['policies'], true)) {
                            $data[$permission['name']] = true;
                        } else {
                            $data[$permission['name']] = false;
                        }
                    } else {
                        $data[$permission['name']] = true;
                    }
                }

                $accessPolicy->set('data', $data);

                $accessPolicy->save();
            }

            $success = true;

            break;
        case xPDOTransport::ACTION_UNINSTALL:
            $success = true;

            break;
    }
}

return $success;
