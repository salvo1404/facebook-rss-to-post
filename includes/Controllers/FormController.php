<?php

namespace Controllers;

use Repositories\PostRepositoryInterface;
use Validators\Validator;

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
     * @var Validator
     */
    private $validator;

    /**
     * @param PostRepositoryInterface $postRepository
     * @param Validator               $validator
     */
    function __construct(PostRepositoryInterface $postRepository, Validator $validator)
    {
        $this->postRepository = $postRepository;
        $this->validator      = $validator;
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

        // import from JSON File Submission
        if (isset($_POST['json_submission'])) {
            if (isset($_FILES['import_json']) && is_uploaded_file($_FILES['import_json']['tmp_name'])) {
                $file = file_get_contents($_FILES['import_json']['tmp_name']);
                $json = json_decode($file);

                // apply some json file validation:

                $importedPostsNumber = $this->postRepository->createPostsFromJson($json);

                $this->printMessage($importedPostsNumber);
            }
        }

        // import from Facebook Page Submission
        if (isset($_POST['facebook_submission'])) {
            if ($this->validator->isValidFacebookSubmissionRequest($_POST)) {

                $response = $this->getFeedsFromFacebookPage($_POST);

                if ($this->validator->isValidResponse($response)) {
                    $json = json_decode($response['body']);

                    if ($this->validator->isValidJsonFormat($json)) {
                        $importedPostsNumber = $this->postRepository->createPostsFromJson($json);

                        $this->printMessage($importedPostsNumber);
                    }
                }
            }
        }
    }

    /**
     * @param array $postRequest
     *
     * @return mixed
     */
    private function getFeedsFromFacebookPage(array $postRequest)
    {
        $pageName = $postRequest['page_name'];
        $maxPosts = $postRequest['max_posts'] ?: FB_RSS_API_MAX_POSTS_DEFAULT;
        $apiCall  = FB_RSS_API_URL . '/' . FB_RSS_API_VERSION . '/' .
            $pageName . '/feed?fields=' . FB_RSS_API_FIELDS . '&limit=' . $maxPosts;

        $response = wp_remote_get(
            $apiCall,
            [
                'headers' => ['Authorization' => FB_RSS_API_TOKEN],
            ]
        );

        return $response;
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
