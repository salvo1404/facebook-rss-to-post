<?php

namespace Validators;

class Validator
{
    /**
     * This function validates Import from Facebook Page Submission Request
     *
     * @param array $postRequest
     *
     * @return bool
     */
    public function isValidFacebookSubmissionRequest(array $postRequest)
    {
        if (stripos($postRequest['page_name'], 'http') === 0) {
            $this->showError('page_name_http', 'Please Insert Facebook Page Name Only');

            return false;
        }

        return true;
    }

    /**
     * @param $response
     *
     * @return bool
     */
    public function isValidResponse($response)
    {
        if (is_wp_error($response)) {
            $this->showError('invalid_page_name', 'Response or WP_error on failure');

            return false;
        }

        $json = json_decode($response['body']);

        if (isset($json->error)) {
            $this->showError('invalid_page_name', $json->error->message);

            return false;
        }

        return true;
    }

    public function isValidJsonSubmissionRequest(array $postRequest)
    {
        var_dump($postRequest);
        $this->showError('invalid_page_name', 'Invalid Json File');
    }

    /**
     * This function validate the Json file
     *
     * @param $json
     *
     * @return bool
     */
    //TODO More validation
    public function isValidJsonFormat($json)
    {
        if (!isset($json->data)) {
            $this->showError('invalid_page_name', 'Malformed Json Received from Facebook API');

            return false;
        }

        return true;
    }

    /**
     * This function defines the settings error to display
     *
     * @param $error_slug
     * @param $message
     */
    private function showError($error_slug, $message)
    {
        add_settings_error(
            $error_slug,
            '',
            $message,
            'error'
        );
    }
}