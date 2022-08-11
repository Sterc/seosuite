<?php
namespace Sterc\SeoSuite\Processors\Mgr\ExcludeWords;

use MODX\Revolution\Processors\Processor;
use MODX\Revolution\modSystemSetting;

class Save extends Processor
{
    /**
     * @access public.
     * @return Mixed.
     */
    public function process()
    {
        $setting = $this->modx->getObject(modSystemSetting::class, ['key' => 'seosuite.exclude_words']);

        if (!$setting) {
            $setting->fromArray([
                'key'       => 'seosuite.exclude_words',
                'xtype'     => 'textfield',
                'namespace' => 'seosuite',
                'area'      => 'area_seosuite'
            ]);
        }

        if ($setting) {
            $words = explode(',', strtolower($this->getProperty('exclude_words')));
            $value = implode(',', array_filter(array_map('trim', $words)));

            $setting->set('value', $value);

            if ($setting->save()) {
                $this->modx->reloadConfig();

                return $this->success('', ['exclude_words' => $value]);
            }
        }

        return $this->failure('');
    }
}
