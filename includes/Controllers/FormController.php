<?php

namespace Controllers;

use Repositories\PostRepositoryInterface;

/**
 * Processes the admin screen form submissions
 *
 * @author mobilova UG (haftungsbeschränkt) <rsspostimporter@feedsapi.com>
 */
class FormController
{
    /**
     * @var PostRepositoryInterface
     */
    private $postRepository;

    /**
     * @param PostRepositoryInterface $postRepository
     */
    function __construct(PostRepositoryInterface $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    /**
     * This function handles form submissions
     *
     */
    function handle()
    {
        // Invalid nonce
        if (!wp_verify_nonce($_POST['fb_rss_nonce'], 'settings_page')) {
            return;
        }

        // Nothing to process
        if (!isset($_POST['json_submission']) && !isset($_POST['facebook_submission'])) {
            return;
        }

        // import JSON file
        if (isset($_POST['json_submission'])) {
            if (isset($_FILES['import_json']) && is_uploaded_file($_FILES['import_json']['tmp_name'])) {
                $file = file_get_contents($_FILES['import_json']['tmp_name']);
                $json = json_decode($file);

                // apply some json file validation:

                $importedPostsNumber = $this->postRepository->createPostsFromJson($json);

                $this->printMessage($importedPostsNumber);
            }
        }

        // import from Facebook
        if (isset($_POST['facebook_submission']) && $_POST['feed_url'] !== "") {
            $feedUrl  = $_POST['feed_url'];
            $maxPosts = $_POST['max_posts'] ?: FB_RSS_API_MAX_POSTS_DEFAULT;
            $apiCall  = FB_RSS_API_URL . '/' . FB_RSS_API_VERSION . '/' .
                $feedUrl . '/feed?fields=' . FB_RSS_API_FIELDS . '&limit=' . $maxPosts;

            $response = wp_remote_get(
                $apiCall,
                [
                    'headers' => ['Authorization' => FB_RSS_API_TOKEN],
                ]
            );

            if ($response) {
                $json                = json_decode($response['body']);
                $importedPostsNumber = $this->postRepository->createPostsFromJson($json);

                $this->printMessage($importedPostsNumber);
            }
        }
    }

    /**
     * This function prints a success message after importing
     *
     * @param $importedPostsNumber
     */
    private function printMessage($importedPostsNumber)
    {
        ?>
        <div id="message" class="updated">
            <p><strong><?php _e($importedPostsNumber . ' Posts Imported.') ?></strong></p>
        </div>
        <?php
    }
}
