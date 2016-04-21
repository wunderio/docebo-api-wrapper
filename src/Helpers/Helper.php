<?php

namespace Suru\Docebo\DoceboApiWrapper\Helpers;

use Assert\Assertion;
use InvalidArgumentException;

class Helper {
    
    /**
	 * Converts a key value array to HTTP Headers.
	 *
	 * @param array $headers
	 * @return array
	 */
	public static function convertKeyValueArrayToHttpHeaders($headers)
	{
		$http_headers = [];

		foreach ($headers as $key => $value)
		{
			$http_headers[] = $key.':'.$value;
		}

		return $http_headers;
	}

	/**
	 * Determines whether given string is JSON.
	 *
	 */
	public static function isJson($string) 
	{
		json_decode($string);
		return (json_last_error() == JSON_ERROR_NONE);
	}
    
    /**
     * Checks to see if given argument can be converted to an integer.
     * 
     * @param mixed $candidate
     * @return boolean
     */
    public function isIntegerish($candidate)
    {
        try
        {
            Assertion::integerish($candidate);
        }
        catch (InvalidArgumentException $e)
        {
            return false;
        }

        return true;
    }
}
