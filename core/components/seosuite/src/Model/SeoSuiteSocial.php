<?php
namespace Sterc\SeoSuite\Model;

use xPDO\xPDO;

/**
 * Class SeoSuiteSocial
 *
 * @property int $resource_id
 * @property string $og_title
 * @property string $og_description
 * @property string $og_image
 * @property string $og_image_alt
 * @property string $og_type
 * @property string $twitter_title
 * @property string $twitter_description
 * @property string $twitter_image
 * @property string $twitter_image_alt
 * @property string $twitter_creator_id
 * @property string $twitter_card
 * @property integer $inherit_facebook
 * @property string $editedon
 *
 * @package Sterc\SeoSuite\Model
 */
class SeoSuiteSocial extends \xPDOSimpleObject
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
