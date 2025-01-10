<?php
namespace Sterc\SeoSuite\Processors\Mgr\BlockedWords;

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
        $setting = $this->modx->getObject(modSystemSetting::class, ['key' => 'seosuite.blocked_words']);

        if (!$setting) {
            $setting->fromArray([
                'key'       => 'seosuite.blocked_words',
                'xtype'     => 'textfield',
                'namespace' => 'seosuite',
                'area'      => 'area_seosuite'
            ]);
        }

        if ($setting) {
            $words = explode(',', strtolower($this->getProperty('blocked_words')));
            $value = implode(',', array_filter(array_map('trim', $words)));

            $setting->set('value', $value);

            if ($setting->save()) {
                $this->modx->reloadConfig();

                return $this->success('', ['blocked_words' => $value]);
            }
        }

        return $this->failure('');
    }
}
