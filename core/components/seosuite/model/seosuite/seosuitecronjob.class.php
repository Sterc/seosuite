<?php

/**
 * Class SeoSuiteCronjob
 */
class SeoSuiteCronjob
{
    /**
     * @var modX
     */
    private $modx;

    /**
     * @var array
     */
    private $logs = [];

    /**
     * SeoSuiteCronjob constructor.
     * @param $modx
     */
    public function __construct(modX $modx)
    {
        $this->modx = $modx;

        $this->modx->getService(
            'seosuite',
            'SeoSuite',
            $this->modx->getOption(
                'seosuite.core_path',
                null,
                $this->modx->getOption('core_path') . 'components/seosuite/'
            ) . 'model/seosuite/'
        );
    }

    /**
     * Add a log message to render on screen.
     *
     * Valid levels:
     * - info (blue)
     * - error (red)
     * - notice (yellow)
     * - success (green)
     *
     * @since    1.0.0
     * @param    string    $message    The message to log.
     * @param    string    $level      The log level.
     *
     * @return   bool      true
     */
    public function log($message, $level = 'info')
    {
        switch ($level) {
            case 'error':
                $prefix = 'ERROR::';
                $color  = 'red';
                break;
            case 'notice':
                $prefix = 'NOTICE::';
                $color  = 'yellow';
                break;
            case 'success':
                $prefix = 'SUCCESS::';
                $color  = 'green';
                break;
            case 'info':
            default:
                $prefix = 'INFO::';
                $color  = 'blue';
        }

        $logMessage  = $this->colorize($prefix, $color). ' ' . $message;
        $htmlMessage = '<span style="color: ' . $color . '">' . $prefix . '</span> ' . $message;

        /*
         * We use the info level in all times because
         * we dont want this function to terminate the script.
         *
         * Rather use exit(); after the log function call once
         * you have an error.
         */
        if (XPDO_CLI_MODE) {
            $this->modx->log(MODX_LOG_LEVEL_INFO, $logMessage);
        } else {
            $this->modx->log(MODX_LOG_LEVEL_INFO, $htmlMessage);
        }

        /*
         * logMessage has CLI markup
         * htmlMessage has HTML markup
         * cleanMessage has no markup
         */
        $this->logs['logMessage'][]   = $logMessage;
        $this->logs['htmlMessage'][]  = $htmlMessage;
        $this->logs['cleanMessage'][] = $prefix . ' ' . $message;

        return true;
    }

    /**
     * Give a string a color for CLI use.
     *
     * Valid colors:
     * - Red
     * - Green
     * - Yellow
     * - Blue
     *
     * @since    1.0.0
     * @param    string    $string    The string that needs the color.
     * @param    string    $color     The color for the string.
     *
     * @return   string    $string
     */
    protected function colorize($string, $color = 'white')
    {
        switch ($color) {
            case 'red':
                return "\033[31m" . $string . "\033[39m";
                break;
            case 'green':
                return "\033[32m" . $string . "\033[39m";
                break;
            case 'yellow':
                return "\033[33m" . $string . "\033[39m";
                break;
            case 'blue':
                return "\033[34m" . $string . "\033[39m";
                break;
            case 'white':
            default:
                return $string;
        }
    }

    /**
     * Cleanup unresolved redirects.
     *
     * @param $options
     */
    public function cleanupRedirects($options)
    {
        $this->log('Starting cleaning up redirects');

        $till      = isset($options['till']) && !empty($options['till']) ? $options['till'] : date('Y-m-d H:i:s', strtotime('-1 month'));
        $triggered = isset($options['triggered']) && !empty($options['triggered']) ? $options['triggered'] : 1;

        $removed = $this->modx->removeCollection('SeoSuiteUrl', [
            'createdon:<=' => $till,
            'visits:<='    => $triggered
        ]);

        $this->log('Removed redirects: ' . $removed);
        $this->log('Finished cleaning up redirects');
    }
}
