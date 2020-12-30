<?php

/**
 * SeoSuite
 *
 * Copyright 2019 by Sterc <modx@sterc.com>
 */

class SeoSuiteSocial extends xPDOSimpleObject
{
    const INHERIT_FIELD = [
        'twitter_title'         => 'og_title',
        'twitter_description'   => 'og_description',
        'twitter_image'         => 'og_image',
        'twitter_image_alt'     => 'og_image_alt'
    ];

    /**
     * Get all values, inherit the Facebook values if needed.
     *
     * @access public.
     * @return Array.
     */
    public function getValues()
    {
        $data = parent::toArray();

        if ((int) $this->get('inherit_facebook') === 1) {
            foreach ($data as $key => $value) {
                if (isset(self::INHERIT_FIELD[$key])) {
                    $data[$key] = $this->get(self::INHERIT_FIELD[$key]);
                }
            }
        }

        return $data;
    }
}
