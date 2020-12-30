<?php

switch ($options[xPDOTransport::PACKAGE_ACTION]) {
    case xPDOTransport::ACTION_INSTALL:
    case xPDOTransport::ACTION_UPGRADE:
        /* Add options if migration has not already been finished. */
        if (!$modx->getOption('seosuite.migration_finished', null, false)) {
            $output = [];

            $output[] = '<h1 style="margin-top:0;">SEO Suite V2 migrations</h1>
                        <p style="color: #53595F;">Upgrading from SEO Suite V1, SEO Pro or SEO Tab requires a data migration. Select the extras below which you like to migrate the data for.</p>';

            $output[] = '<label><input type="checkbox" name="migrate_seosuitev1"> Migrate SEO Suite V1</label>
                        <p style="color: #53595F;">Migrate data from SEO Suite V1.</p>';

            $output[] = '<label><input type="checkbox" name="migrate_seopro"> Migrate SEO Pro</label>
                        <p style="color: #53595F;">Migrate data from SEO Pro.</p>';

            $output[] = '<label><input type="checkbox" name="migrate_seotab"> Migrate SEO Tab</label>
                        <p style="color: #53595F;">Migrate data from SEO Tab.</p>';

            $output = implode('<br>', $output);
        }
        break;
    default:
    case xPDOTransport::ACTION_UNINSTALL:
        $output = '';
        break;
}

return $output;
