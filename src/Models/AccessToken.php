<?php

namespace Suru\Docebo\DoceboApiWrapper\Models;

use Carbon\Carbon;

class AccessToken extends Token {
    
    /**
     * The token value.
     * 
     * @var string
     */
    protected $value;
    
    /**
     * Time at which the token will expire.
     * 
     * @var int
     */
    protected $expires_at;
    
    /**
     * Type of token, e.g. Bearer.
     * 
     * @var string
     */
    protected $token_type;
    
    /**
     * Scope of the token, what it can access, e.g. 'api'.
     * 
     * @var string
     */
    protected $scope;
    
    /**
     * @param array $attributes
     */
    public function __construct($attributes) 
    {        
        if ( ! isset($attributes['value']))
        {
            throw new \InvalidArgumentException('The access token value must be provided.');
        }
        
        if ( ! is_string($attributes['value']))
        {
            throw new \InvalidArgumentException('The access token value must be a string.');
        }

        // set attributes
        parent::__construct($attributes);
        
        // set expiracy date/time
        $expires_in = (isset($attributes['expires_in'])) ? $attributes['expires_in'] : 0;
        $this->expires_at = Carbon::now()->addSeconds($expires_in);
    }
    
    /**
     * Determines whether or not the token has expired.
     * 
     * @return boolean
     */
    public function hasExpired()
    {
        return (Carbon::now() >= $this->expires_at);
    }
}
