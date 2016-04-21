<?php

namespace Suru\Docebo\DoceboApiWrapper\Api;

use Suru\Docebo\DoceboApiWrapper\DoceboApiWrapper;

class BaseEndpoint {

    protected $master;
    
    public function __construct(DoceboApiWrapper $master) 
    {
        $this->master = $master;
    }
}
