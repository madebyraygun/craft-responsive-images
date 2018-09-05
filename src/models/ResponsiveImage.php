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
    public $orig;

    private $sources = array();

    public function addSource(int $width, string $source)
    {
        $this->sources[$width] = $source;
        return $source;
    }

    public function __get($name)
    {
        if ($name === 'src') {
            return $this->src();
        }

        if ($name === 'srcset') {
            return $this->srcset();
        }
    }

    public function src(int $width = 0) : string
    {
        ksort($this->sources);

        $bigEnough = array_filter($this->sources, function ($w) use ($width) {
            return $w >= $width;
        }, ARRAY_FILTER_USE_KEY);

        if (count($bigEnough) > 0) {
            return array_shift($bigEnough);
        }

        $sizes = array_keys($this->sources);
        $biggest = array_pop($sizes);

        return $this->sources[$biggest];
    }

    public function srcset() : string
    {
        ksort($this->sources);

        $srcset = [];

        foreach ($this->sources as $width => $src) {
            $srcset[] = $src . ' ' . $width . 'w';
        }

        return join(',', $srcset);
    }
}
