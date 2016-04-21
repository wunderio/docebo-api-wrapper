<?php

namespace Suru\Docebo\DoceboApiWrapper\Api;

use Suru\Docebo\DoceboApiWrapper\Api\BaseEndpoint;
use Suru\Docebo\DoceboApiWrapper\Exceptions\DoceboResponseException;
use Suru\Docebo\DoceboApiWrapper\Models\AccessToken;

class Authentication extends BaseEndpoint {

    /**
     * Authentication endpoints.
     * 
     * @var array
     */
    protected $endpoints = [
        'auth'  => '/oauth2/authorize',
		'token' => '/oauth2/token',
    ];
    
    /**
     * Retrieves a token by authenticating with client credentials.
     * 
     * @param string $client_id
     * @param string $client_secret
     * @return Token
     */
    public function getTokenWithClientCredentials($client_id, $client_secret)
    {
		$headers = [
			'Authorization' => 'Basic '.base64_encode($client_id.':'.$client_secret),
		];

        // NOTE: although the documentation says to set 'scope' to "API" it doesn't work when this is set
		$parameters = [
			'grant_type' => 'client_credentials',
		];

        $response = $this->master->call($this->endpoints['token'], $headers, $parameters);
        
        if (empty($response->body))
        {
            // TODO: throw some form of exception based on response headers
            throw new DoceboResponseException();
        }

		return new AccessToken([
            'value' => $response->body->access_token, 
            'expires_in' => $response->body->expires_in, 
            'token_type' => $response->body->token_type, 
            'scope' => $response->body->scope
        ]);
    }
}
