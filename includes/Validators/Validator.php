<?php

namespace includes\Validators;

class Validator
{
    /**
     * This function validates Import from Facebook Page Submission Request
     *
     * @param array $postRequest
     *
     * @return bool
     */
    public function validateFacebookSubmissionRequest(array $postRequest)
    {
        if (stripos($postRequest['page_name'], 'http') === 0) {
            return false;
        }

        return true;
    }

    /**
     * This function checks if the response is valid
     *
     * @param $response
     *
     * @return bool
     */
    public function validateResponse($response)
    {
        if (is_wp_error($response)) {
            return false;
        }

        return true;
    }

    /**
     * This function checks if a file has been uploaded
     *
     * @param array $requestFiles
     *
     * @return bool
     */
    public function validateJsonRequestFiles(array $requestFiles)
    {
        if(empty($requestFiles['import_json']['tmp_name'])){
            return false;
        }

        return true;
    }

    /**
     * This function check if the Json file contains error
     *
     * @param $json
     *
     * @return bool
     */
    public function jsonContainsError($json)
    {
        if (isset($json->error)) {
            return $json->error;
        }

        return null;
    }

    /**
     * This function validate the Json file
     *
     * @param $json
     *
     * @return bool
     */
    //TODO More validation
    public function validateJsonFormat($json)
    {
        if (!isset($json->data)) {
            return false;
        }

        return true;
    }
}