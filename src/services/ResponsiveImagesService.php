<?php
/**
 * Responsive Images plugin for Craft CMS 3.x
 *
 * @copyright Copyright (c) 2018 Pieter Beulque
 */

namespace gentsagency\responsiveimages\services;

use gentsagency\responsiveimages\ResponsiveImages;
use gentsagency\responsiveimages\jobs\Purge as PurgeJob;

use Craft;
use craft\base\Component;

/**
 * ResponsiveImagesService Service
 *
 * @author    Pieter Beulque
 * @package   ResponsiveImages
 * @since     0.0.1
 */
class ResponsiveImagesService extends Component
{
    /**
     * Queue a purge request with imgix
     *
     * @param \craft\elements\Asset $image
     *
     * @return bool
     */
    public function queuePurge(\craft\elements\Asset $image) : bool
    {
        $volume = ResponsiveImages::$plugin->getSettings()->volumes[$image->volume->id] ?? null;

        if (!empty($volume) && !empty($volume['imgix']) && !empty($volume['imgix']['domain'])) {
            Craft::$app->getQueue()->push(new PurgeJob([
                'image' => $image->id,
                'description' => 'Purge image: ' . $image->getPath(),
            ]));

            return true;
        }

        return false;
    }
}
