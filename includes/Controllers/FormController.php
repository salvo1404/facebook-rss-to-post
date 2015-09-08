<?php

namespace includes\Controllers;

use includes\Repositories\PostRepositoryInterface;
use includes\Validators\Validator;

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

            if (!$this->validator->validateJsonRequestFile($_FILES)) {
                $this->respondWithError('file_not_found', 'Please Upload a Json File');

                return;
            }

            $file = file_get_contents($_FILES['import_json']['tmp_name']);

            if (!$json = json_decode($file)) {
                $this->respondWithError('invalid_json', 'Please Upload a valid Json File');

                return;
            }

            if ($error = $this->validator->jsonContainsError($json)) {
                $this->respondWithError('response_error', $error->message);

                return;
            }
            if (!$this->validator->validateJsonFormat($json)) {
                $this->respondWithError('invalid_json', 'Malformed Json Received from Facebook API');

                return;
            }

            $importedPostsNumber = $this->postRepository->createPostsFromJson($json);

            $this->respondWithSuccess($importedPostsNumber);

        }

        // import from Facebook Page Submission
        if (isset($_POST['facebook_submission'])) {

            if (!$this->validator->validateFacebookSubmissionRequest($_POST)) {
                $this->respondWithError('page_name_http', 'Please Insert Facebook Page Name Only');

                return;
            }

            $response = $this->getFeedsFromFacebookPage($_POST);

            if (!$this->validator->validateResponse($response)) {
                $this->respondWithError('invalid_response', 'Response or WP_error on failure');

                return;
            }

            $json = json_decode($response['body']);

            if ($error = $this->validator->jsonContainsError($json)) {
                $this->respondWithError('response_error', $error->message);

                return;
            }
            if (!$this->validator->validateJsonFormat($json)) {
                $this->respondWithError('invalid_json', 'Malformed Json Received from Facebook API');

                return;
            }

            $importedPostsNumber = $this->postRepository->createPostsFromJson($json);

            $this->respondWithSuccess($importedPostsNumber);

        }
    }

    /**
     * This function makes the request to Facebook API
     *
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
    private function respondWithSuccess($importedPostsNumber)
    {
        ?>
        <div id="message" class="updated">
            <p><strong><?php _e($importedPostsNumber . ' Posts Imported.') ?></strong></p>
        </div>
        <?php
    }

    /**
     * This function defines the settings error to display
     *
     * @param $error_slug
     * @param $message
     */
    private function respondWithError($error_slug, $message)
    {
        add_settings_error(
            $error_slug,
            '',
            $message,
            'error'
        );
    }

}
