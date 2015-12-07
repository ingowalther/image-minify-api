<?php

namespace IngoWalther\ImageMinifyApi\Test\Error;

use IngoWalther\ImageMinifyApi\Error\ErrorHandler;

class ErrorHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ErrorHandler
     */
    private $object;

    public function setUp()
    {
        $logger = $this->getMockBuilder('Monolog\Logger')
            ->disableOriginalConstructor()
            ->getMock();

        $this->object = new ErrorHandler($logger);
    }

    public function testWith404()
    {
        $exception = new \Exception();
        $response = $this->object->handle($exception, '404');

        $this->assertEquals(404, $response->getStatusCode());

        $data = array(
            'success' => false,
            'code' => '404',
            'message' => 'The requested page could not be found.',
        );

        $expected = json_encode($data);
        $this->assertEquals($expected, $response->getContent());
    }

    public function testDefault()
    {
        $message = 'This is my test error';

        $exception = new \Exception($message);
        $response = $this->object->handle($exception, '500');

        $this->assertEquals(500, $response->getStatusCode());

        $data = array(
            'success' => false,
            'code' => '500',
            'message' => $message,
        );

        $expected = json_encode($data);
        $this->assertEquals($expected, $response->getContent());
    }
}
