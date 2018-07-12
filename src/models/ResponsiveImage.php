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
class ResponsiveImage extends \craft\base\Model
{
    /**
     * The <img> src attribute
     *
     * @var string
     */
    public $src;

    /**
     * The <img> srcset attribute
     *
     * @var string
     */
    public $srcset;
}
