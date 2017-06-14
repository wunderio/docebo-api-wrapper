<?php

namespace Suru\Docebo\DoceboApiWrapper;

use Suru\Docebo\DoceboApiWrapper\Api\Authentication;
use Suru\Docebo\DoceboApiWrapper\Api\Course;
use Suru\Docebo\DoceboApiWrapper\Api\Organization;
use Suru\Docebo\DoceboApiWrapper\Api\User;
use Suru\Docebo\DoceboApiWrapper\Api\Enrollment;
use Suru\Docebo\DoceboApiWrapper\Api\LearningPlan;
use Suru\Docebo\DoceboApiWrapper\Helpers\Helper;
use Suru\Docebo\DoceboApiWrapper\Models\AccessToken;
use Suru\Docebo\DoceboApiWrapper\Exceptions\DoceboWrapperException;
use Suru\Docebo\DoceboApiWrapper\Exceptions\DoceboRequestException;
use Suru\Docebo\DoceboApiWrapper\Exceptions\DoceboServerException;

class DoceboApiWrapper {

    /**
     * Base URL of Docebo service platform for client.
     *
     * @var string
     */
    protected $base_url;

    /**
     * @var string
     */
    protected $client_id;

    /**
     * @var string
     */
    protected $client_secret;

    /**
     * @var string
     */
    protected $access_token;

    /**
     *
     * @param string $base_url          URL where Docebo platform resides
     * @param string $client_id         Client ID of your API Credentials
     * @param string $client_secret     Client secret of your API Credentials
     * @param string $access_token      Optional if you have existing access token
     */
    public function __construct($base_url, $client_id, $client_secret, $access_token = null)
    {
        $this->base_url = $base_url;
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;

        if (isset($access_token))
        {
            $this->setAccessToken($access_token);
        }
    }

    /**
     * @var DoceboApiWrapper\Api\Authentication
     */
    public function authentication()
    {
        return new Authentication($this);
    }

    /**
     * @var DoceboApiWrapper\Api\Course
     */
    public function course()
    {
        return new Course($this);
    }

    /**
     * @var DoceboApiWrapper\Api\Organization
     */
    public function organization()
    {
        return new Organization($this);
    }

    /**
     * @var DoceboApiWrapper\Api\User
     */
    public function user()
    {
        return new User($this);
    }

    /**
     * @var DoceboApiWrapper\Api\Enrollment
     */
    public function enrollment()
    {
      return new Enrollment($this);
    }

    /**
     * @var DoceboApiWrapper\Api\LearningPlan
     */
    public function learningPlan()
    {
      return new LearningPlan($this);
    }

    /**
     * Returns the current access token or attempts to get a new one.
     *
     * @return AccessToken|null
     */
    public function getAccessToken()
    {
        if ( ! is_null($this->access_token))
        {
            return $this->access_token;
        }

        $this->access_token = $this->authentication()->getTokenWithClientCredentials($this->client_id, $this->client_secret);

        return $this->access_token;
    }

    /**
     * @param AccessToken|string $access_token
     * @throws \InvalidArgumentException
     */
    public function setAccessToken($access_token)
    {
        if (is_string($access_token))
        {
            $this->access_token = new AccessToken(['value' => $access_token]);
            return;
        }

        if ($access_token instanceof AccessToken)
        {
            $this->access_token = $access_token;
            return;
        }

        throw new \InvalidArgumentException('The default access token must be of type "string" or DoceboApiWrapper\Models\AccessToken');
    }

    /**
     * Currently all requests to the Docebo API are POST based.
     *
     * @param string $endpoint
     * @param array $headers
     * @param array $parameters
     * @return object
     */
    public function call($endpoint, $headers = [], $parameters = [])
    {
        // build URL
        $url = $this->base_url . $endpoint;

        // open connection
        $ch = curl_init();

        // set parameters for POST
        curl_setopt($ch, CURLOPT_POST, count($parameters));
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($parameters));

        // set the curl options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, Helper::convertKeyValueArrayToHttpHeaders($headers));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);

        // execute request and get response body and info
        $response = (object) [
            'body' => curl_exec($ch),
            'info' => curl_getinfo($ch),
        ];

        // json decode the body if necessary
        if (Helper::isJson($response->body))
        {
            $response->body = json_decode($response->body);
        }

        // catch errorneous requests
        $this->handleRequestErrors($response);

        // close connection
        curl_close($ch);

        // return body
        return $response;
    }

    /**
     * Checks the status code of the response header for erroneous HTTP codes.
     *
     * @param object $response
     * @throws DoceboRequestException
     * @throws DoceboServerException
     */
    protected function handleRequestErrors($response)
    {
        // get error message for erroneous requests
        if ($response->info['http_code'] >= 400)
        {
            $message = $this->buildErrorMessage($response);
        }

        // check for client errors in the 4xx range
        if ($response->info['http_code'] >= 400 && $response->info['http_code'] < 500)
        {
            throw new DoceboRequestException($message);
        }

        // check for server errors in the 5xx range
        if ($response->info['http_code'] >= 500)
        {
            throw new DoceboServerException($message);
        }

        // NOTE: have had to add this in as the Docebo API uses HTTP Status
        // Codes incorrectly, e.g:
        //
        // 201	Empty userid
        // 202	Error while assigning user level
        // 203	Cannot create godadmin users
        // 204	Cannot save user
        // 500	Internal server error
        //
        if ( ! empty($response->body->error))
        {
            $message = $this->buildErrorMessage($response);
            throw new DoceboRequestException($message);
        }
    }

    /**
     * Builds up the error message to include in the exception.
     *
     * @param object $response
     * @return string
     */
    protected function buildErrorMessage($response)
    {
        // create a default error message
        $message = $response->info['http_code'].' Unknown error occurred on making request to Docebo API';

        // override the default message if the response contains useful error information
        if ( ! empty($response->body->error) && ! empty($response->body->error_description))
        {
            $message = $response->info['http_code'].' '.$response->body->error.': '.$response->body->error_description;
        }

        // NOTE: have had to add this in as the Docebo API uses HTTP Status
        // Codes incorrectly, e.g:
        //
        // 201	Empty userid
        // 202	Error while assigning user level
        // 203	Cannot create godadmin users
        // 204	Cannot save user
        // 500	Internal server error
        //
        if ( ! empty($response->body->error) && ! empty($response->body->message))
        {
            $message = $response->info['http_code'].': '.$response->body->message;
        }

        // add URL to make it easy to determine which endpoint was being hit
        $message .= ' when requesting '.$response->info['url'];

        return $message;
    }

    public function getAccessTokenHeader()
    {
        if ( ! $this->access_token instanceof AccessToken)
        {
            throw new DoceboWrapperException('Access token has not yet been retrieved, please authenticate.');
        }

        if ($this->access_token->hasExpired())
        {
            throw new DoceboWrapperException('Access token has expired, please re-authenticate.');
        }

        return [
            'Authorization' => 'Bearer '.$this->access_token->value
        ];
    }
}
