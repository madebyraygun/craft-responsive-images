<?php
/**
 * Responsive Images plugin for Craft CMS 3.x
 *
 * @copyright Copyright (c) 2018 Pieter Beulque
 */

namespace gentsagency\responsiveimages\models;

/**
 * ResponsiveImages Settings Model
 *
 * https://craftcms.com/docs/plugins/models
 *
 * @author    Pieter Beulque
 * @package   ResponsiveImages
 * @since     0.0.1
 */
class Settings extends \craft\base\Model
{
    /**
     * Settings are grouped per AssetVolume
     *
     * @var array
     */
    public $volumes = array();

    /**
     * All widths that will be generated
     *
     * @var int[]
     */
    public $widths = array(256, 384, 512, 768, 1024, 1280, 1536, 1760, 2560);
}
