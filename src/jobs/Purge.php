<?php
/**
 * Responsive Images plugin for Craft CMS 3.x
 *
 * @copyright Copyright (c) 2018 Pieter Beulque
 */

namespace gentsagency\responsiveimages\jobs;

use gentsagency\responsiveimages\ResponsiveImages;

use Craft;
use craft\queue\BaseJob;

/**
 * Purge job
 *
 * Jobs are run in separate process via a Queue of pending jobs. This allows
 * you to spin lengthy processing off into a separate PHP process that does not
 * block the main process.
 *
 * You can use it like this:
 *
 * use gentsagency\responsiveimages\jobs\Purge as PurgeJob;
 *
 * $queue = Craft::$app->getQueue();
 * $jobId = $queue->push(new PurgeJob([
 *     'description' => Craft::t('responsive-images', 'This overrides the default description'),
 *     'someAttribute' => 'someValue',
 * ]));
 *
 * The key/value pairs that you pass in to the job will set the public properties
 * for that object. Thus whatever you set 'someAttribute' to will cause the
 * public property $someAttribute to be set in the job.
 *
 * Passing in 'description' is optional, and only if you want to override the default
 * description.
 *
 * More info: https://github.com/yiisoft/yii2-queue
 *
 * @author    Pieter Beulque
 * @package   ResponsiveImages
 * @since     0.0.1
 */
class Purge extends BaseJob
{
    // Public Properties
    // =========================================================================

    /**
     * Some attribute
     *
     * @var string
     */
    public $image;

    // Public Methods
    // =========================================================================

    /**
     * Purge a master image on imgix.
     *
     * @param \craft\queue\Queue $queue
     */
    public function execute(\craft\queue\Queue $queue)
    {
        $settings = ResponsiveImages::$plugin->getSettings()->volumes[$this->image->volume->id];

        if (empty($settings['imgix']) || empty($settings['imgix']['apiKey'])) {
            return false;
        }
        $apiKey = $settings['imgix']['apiKey'];
        $url = 'https://' . $settings['imgix']['domain'] . '/' . $this->image->getPath();

        Craft::trace(
            $url,
            'Purge::purge'
        );

        try {
            $client = Craft::createGuzzleClient([ 'timeout' => 30, 'connect_timeout' => 30 ]);

            $response = $client->post('https://api.imgix.com/v2/image/purger', [
                'auth'        => [
                    $apiKey, ''
                ],
                'form_params' => [
                    'url' => $url,
                ]
            ]);
            Craft::trace(
                'Purge::purge',
                'Purged asset: {url} - Status code {statusCode}',
                [
                    'url'        => $url,
                    'statusCode' => $response->getStatusCode()
                ]
            );

            return $response->getStatusCode() >= 200 && $response->getStatusCode() < 400;
        } catch (RequestException $e) {
            Craft::error(
                'Purge::purge',
                'Failed to purge {url}: {statusCode} {error}',
                [
                    'url'        => $url,
                    'error'      => $e->getMessage(),
                    'statusCode' => $e->getResponse()->getStatusCode()
                ]
            );
            return false;
        }
    }

    // Protected Methods
    // =========================================================================

    /**
     * Returns a default description for [[getDescription()]], if [[description]] isn’t set.
     *
     * @return string The default task description
     */
    protected function defaultDescription(): string
    {
        return 'Purging master image on imgix';
    }
}
