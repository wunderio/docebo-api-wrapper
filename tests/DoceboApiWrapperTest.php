<?php

use Suru\Docebo\DoceboApiWrapper\DoceboApiWrapper;

class DoceboApiWrapperTest extends PHPUnit_Framework_TestCase {

    /**
     * @test
     */
    public function can_instantiate_the_wrapper()
    {
        $docebo = new DoceboApiWrapper('url', 'client_id', 'client_secret', 'access_token');
        $this->assertInstanceOf(DoceboApiWrapper::class, $docebo);
        $this->assertEquals('access_token', $docebo->getAccessToken()->value);
    }

}