<?php

$settings = [
    [
        'key'   => 'user_name',
        'value' => '',
        'name'  => 'Name'
    ],
    [
        'key'   => 'user_email',
        'value' => '',
        'name'  => 'Email address'
    ]
];

switch ($options[xPDOTransport::PACKAGE_ACTION]) {
    case xPDOTransport::ACTION_INSTALL:
    case xPDOTransport::ACTION_UPGRADE:
        /* Add options if migration has not already been finished. */
        if (!$modx->getOption('seosuite.migration_finished', null, false)) {
            $options = [];

            $options[] = '<h1 style="margin-top:0;">SEO Suite V2 migrations</h1>
                        <p style="color: #53595F;">Upgrading from SEO Suite V1, SEO Pro or SEO Tab requires a data migration. Select the extras below which you like to migrate the data for.</p>';

            $options[] = '<label><input type="checkbox" name="migrate_seosuitev1"> Migrate SEO Suite V1</label>
                        <p style="color: #53595F;">Migrate data from SEO Suite V1.</p>';

            $options[] = '<label><input type="checkbox" name="migrate_seopro"> Migrate SEO Pro</label>
                        <p style="color: #53595F;">Migrate data from SEO Pro.</p>';

            $options[] = '<label><input type="checkbox" name="migrate_seotab"> Migrate SEO Tab</label>
                        <p style="color: #53595F;">Migrate data from SEO Tab.</p>';

            $output[] = implode('<br>', $options);
            $output[] = '<br/>';
        }

        foreach ($settings as $key => $setting) {
            $settingObject = $modx->getObject('modSystemSetting', ['key' => 'seosuite.' . $setting['key']]
            );
            if ($settingObject) {
                $settings[$key]['value'] = $settingObject->get('value');
            }
        }
        break;
    default:
    case xPDOTransport::ACTION_UNINSTALL:
        $output = '';
        break;
}

/* Hide default setup options text */
$output[] = '
<style type="text/css">
    #modx-setupoptions-panel { display: none; }
</style>
<script>
    var setupTitle = "SEO Suite installation - a MODX Extra by Sterc";
    document.getElementsByClassName("x-window-header-text")[0].innerHTML = setupTitle;
</script>
<h2>Get free priority updates</h2>
<p>Enter your name and email address below to receive priority updates about our extras.
Be the first to know about updates and new features.
<i><b>It is NOT required to enter your name and email to use this extra.</b></i></p>';

foreach ($settings as $setting) {
    $str = '<label for="' . $setting['key'] . '">'. $setting['name'] . ' (optional)</label>';
    $str .= '<input type="text" name="' . $setting['key'] . '"';
    $str .= ' id="' . $setting['key'] . '" width="300" value="' . $setting['value'] .'" />';

    $output[] = $str;
}

return implode('', $output);
