<?php
/**
 * Responsive Images plugin for Craft CMS 3.x
 *
 * @copyright Copyright (c) 2018 Pieter Beulque
 */

namespace gentsagency\responsiveimages;

use gentsagency\responsiveimages\services\ResponsiveImagesService as ResponsiveImagesServiceService;
use gentsagency\responsiveimages\twigextensions\ResponsiveImagesTwigExtension;
use gentsagency\responsiveimages\models\Settings;

use Craft;
use craft\base\Plugin;
use craft\events\ElementEvent;
use craft\events\ReplaceAssetEvent;
use craft\events\PluginEvent;
use craft\services\Assets;
use craft\services\Elements;
use craft\services\Plugins;

use yii\base\Event;

/**
 * Craft plugins are very much like little applications in and of themselves. We’ve made
 * it as simple as we can, but the training wheels are off. A little prior knowledge is
 * going to be required to write a plugin.
 *
 * For the purposes of the plugin docs, we’re going to assume that you know PHP and SQL,
 * as well as some semi-advanced concepts like object-oriented programming and PHP namespaces.
 *
 * https://craftcms.com/docs/plugins/introduction
 *
 * @author    Pieter Beulque
 * @package   ResponsiveImages
 * @since     0.0.1
 *
 * @property  ResponsiveImagesServiceService $responsiveImagesService
 * @property  Settings $settings
 * @method    Settings getSettings()
 */
class ResponsiveImages extends Plugin
{
    /**
     * Keep an instance of this plugin so that it can be accessed via ResponsiveImages::$plugin
     *
     * @var ResponsiveImages
     */
    public static $plugin;

    /**
     * The plugin's schema version.
     *
     * @var string
     */
    public $schemaVersion = '1.0.0';

    /**
     * Initialize the plugin
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        // Add in our Twig extensions
        Craft::$app->view->registerTwigExtension(new ResponsiveImagesTwigExtension());

        // Configure hooks
        Event::on(
            Elements::class,
            Elements::EVENT_BEFORE_SAVE_ELEMENT,
            function (ElementEvent $event) {
                if ($event->element instanceof \craft\elements\Asset && !$event->isNew) {
                    ResponsiveImages::$plugin->responsiveImagesService->queuePurge($event->element);
                }
            }
        );

        Event::on(
            Assets::class,
            Assets::EVENT_BEFORE_REPLACE_ASSET,
            function (ReplaceAssetEvent $event) {
                ResponsiveImages::$plugin->responsiveImagesService->queuePurge($event->asset);
            }
        );

        Event::on(
            Assets::class,
            Assets::EVENT_AFTER_SAVE_VOLUME,
            function (ReplaceAssetEvent $event) {
                $volume = $event->volume;
                $settings = $this->getSettings();

                if (!isset($settings->volumes[$volume->id])) {
                    $settings->volumes[$volume->id] = array(
                        'imgix' => array(
                            'domain' => null,
                            'apiKey' => null,
                            'signKey' => null,
                        )
                    );
                }
            }
        );

        Event::on(
            Assets::class,
            Assets::EVENT_AFTER_DELETE_VOLUME,
            function (ReplaceAssetEvent $event) {
                $volume = $event->volume;
                $settings = $this->getSettings();

                if (isset($settings->volumes[$volume->id])) {
                    unset($settings->volumes[$volume->id]);
                }
            }
        );
    }

    /**
     * Create the plugin’s settings.
     *
     * @return \gentsagency\responsiveimages\models\Settings|null
     */
    protected function createSettingsModel()
    {
        return new Settings();
    }

    /**
     * Returns the rendered settings HTML, which will be inserted into the content
     * block on the settings page.
     *
     * @return string The rendered settings HTML
     */
    protected function settingsHtml(): string
    {
        $volumes = Craft::$app->getVolumes()->getAllVolumes();
        $mapped = [];

        $settings = $this->getSettings();

        foreach ($volumes as &$volume) {
            $type = 'local';

            if ($volume instanceof craft\awss3\Volume) {
                $type = 's3';
            }

            $mapped[] = array(
                'type' => $type,
                'volume' => $volume,
            );

            if (!isset($settings->volumes[$volume->id])) {
                $settings->volumes[$volume->id] = [];
            }

            if (!isset($settings->volumes[$volume->id]['imgix'])) {
                $settings->volumes[$volume->id]['imgix'] = array(
                    'domain' => null,
                    'apiKey' => null,
                    'signKey' => null,
                );
            }
        }

        return Craft::$app->view->renderTemplate(
            'responsive-images/settings',
            [
                'settings' => $settings,
                'volumes' => $mapped,
            ]
        );
    }

    public function onAfterInstall()
    {
        $volumes = Craft::$app->getVolumes()->getAllVolumes();

        foreach ($volumes as &$volume) {
            $type = 'local';

            if ($volume instanceof craft\awss3\Volume) {
                $type = 's3';
            }

            $mapped[] = array(
                'type' => $type,
                'volume' => $volume,
            );

            if (!isset($settings->volumes[$volume->id])) {
                $settings->volumes[$volume->id] = [];
            }

            if (!isset($settings->volumes[$volume->id]['imgix'])) {
                $settings->volumes[$volume->id]['imgix'] = array(
                    'domain' => null,
                    'apiKey' => null,
                    'signKey' => null,
                );
            }
        }
    }

}
