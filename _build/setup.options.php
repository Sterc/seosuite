<?php

use xPDO\Transport\xPDOTransport;
use MODX\Revolution\modSystemSetting;

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

            $options[] = '<h1 style="margin-top:0;">SEO Suite migrations</h1>
                        <p style="color: #53595F;">Upgrading from SEO Suite V1, SEO Pro or SEO Tab requires a data migration. <br> NOTE: The migration tools for this are now available inside the SEO Suite manager page.</p>';

            $output[] = implode('<br>', $options);
            $output[] = '<br/>';
        }

        foreach ($settings as $key => $setting) {
            $settingObject = $modx->getObject(modSystemSetting::class, ['key' => 'seosuite.' . $setting['key']]
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

/* Check 404 Logging */
$log404 = $modx->getOption('seosuite.log_404', null, true);

$output[] = '
<style>
    #modx-setupoptions-panel { display: none; }
</style>
<script>
    var setupTitle = "SEO Suite installation - a MODX Extra by Sterc";
    document.getElementsByClassName("x-window-header-text")[0].innerHTML = setupTitle;
</script>
<fieldset class="x-fieldset">
<div class="x-fieldset-header">
    <div class="x-fieldset-header-text">
        <h2 style="margin-top:8px;">Log 404 Errors</h2>
        <p>Logging 404 errors can be a valuable tool. However, it is not recommended to enable this 
        feature if you have a high-traffic website or do not plan to analyze the data.</p>
    </div>
</div>
<div class="x-fieldset-bwrap">
    <div class="x-fieldset-body">
        <input type="hidden" name="log_404" value="0" />
        <div class="x-form-item">
            <div class="display-switch">
                <div class="x-form-check-wrap">
                    <input type="checkbox" name="log_404" id="log_404" '. ($log404 ? 'checked' : '') .' value="1"/>
                    <label for="log_404" class="x-form-cb-label">Enable 404 Logging</label>
                </div>
            </div>
        </div>
    </div>
</div>
</fieldset>
';

/* Hide default setup options text */
$output[] = '
<style>
    #modx-setupoptions-panel { display: none; }
</style>
<script>
    var setupTitle = "SEO Suite installation - a MODX Extra by Sterc";
    document.getElementsByClassName("x-window-header-text")[0].innerHTML = setupTitle;
</script>
<fieldset class="x-fieldset">
<div class="x-fieldset-header">
    <div class="x-fieldset-header-text">
        <h2 style="margin-top:8px;">Get free priority updates</h2>
        <p>Enter your name and email address below to receive priority updates about our extras.
        Be the first to know about updates and new features.
        <i><b>It is NOT required to enter your name and email to use this extra.</b></i></p>
    </div>
</div>
<div class="x-fieldset-bwrap">
    <div class="x-fieldset-body">';

foreach ($settings as $setting) {
    $str = '<div class="x-form-item">
                <label for="' . $setting['key'] . '" class="x-form-item-label" style="width:100%;">
                    '. $setting['name'] . ' (optional)
                </label>
                <div class="x-form-element" style="padding-left:0;">
                    <input type="text" class="x-form-text x-form-field" name="' . $setting['key'] . '" id="' . $setting['key'] . '" width="300" style="width:100%;box-sizing: border-box;height: auto;" value="' . $setting['value'] .'" />
                </div>
                <div class="x-form-clear-left"></div>
            </div>';

    $output[] = $str;
}
$output[] = '</div></div></fieldset>';

return implode('', $output);
