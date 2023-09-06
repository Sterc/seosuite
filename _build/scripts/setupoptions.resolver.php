<?php
use xPDO\Transport\xPDOTransport;
use MODX\Revolution\modSystemSetting;

/** @var modX $modx */
$modx = $transport->xpdo;

$resolver = new SeoSuiteSetupOptionsResolver($modx, $options);
return $resolver->process();

class SeoSuiteSetupOptionsResolver
{
    /**
     * Name for migration finished system setting.
     */
    const KEY_MIGRATION_FINISHED = 'seosuite.migration_finished';

    /**
     * @var $modx modX Holds modX class.
     */
    protected $modx;

    /**
     * @var array $options Holds all options.
     */
    protected $options = [];

    /**
     * @var array $domains Holds all domains.
     */
    protected $domains = [];

    /**
     * SeoSuiteSetupOptionsResolver constructor.
     * @param $modx
     * @param $options
     */
    public function __construct($modx, $options)
    {
        $this->modx    = $modx;
        $this->options = $options;
    }

    /**
     * Process setup options resolver.
     * @return bool
     */
    public function process()
    {
        /* If uninstall, then return true. */
        if ($this->options[xPDOTransport::PACKAGE_ACTION] === xPDOTransport::ACTION_UNINSTALL) {
            return true;
        }

        $this->savePriorityUpdateValues();

        /* Check if migration is already finished. */
        if ($this->modx->getOption(self::KEY_MIGRATION_FINISHED, null, false)) {
            return true;
        }

        /*  Set migration finished setting. */
        $migrationSetting = $this->modx->getObject(modSystemSetting::class, ['key' => self::KEY_MIGRATION_FINISHED]);
        if ($migrationSetting instanceof modSystemSetting) {
            $migrationSetting->set('value', true);
            $migrationSetting->save();
        }

        return true;
    }

    /**
     * Save priority update values.
     */
    protected function savePriorityUpdateValues()
    {
        foreach (['user_name', 'user_email'] as $key) {
            if (isset($this->options[$key])) {
                $settingObject = $this->modx->getObject(modSystemSetting::class, ['key' => 'seosuite.' . $key]);

                if ($settingObject) {
                    $settingObject->set('value', $this->options[$key]);
                    $settingObject->save();
                }
            }
        }
    }

    /**
     * @param string $message
     */
    protected function log($message = '', $level = 'info')
    {
        $logLevel = \xPDO::LOG_LEVEL_INFO;
        switch ($level) {
            case 'warning':
                $logLevel = \xPDO::LOG_LEVEL_WARN;
                break;
            case 'error':
                $logLevel = \xPDO::LOG_LEVEL_ERROR;
                break;
        }
        $this->modx->log($logLevel, '[SEO SUITE] ' . $message);
    }
}
