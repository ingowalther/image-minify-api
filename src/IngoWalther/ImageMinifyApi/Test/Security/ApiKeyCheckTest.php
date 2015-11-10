<?php

namespace IngoWalther\ImageMinifyApi\Test\Security;

use IngoWalther\ImageMinifyApi\Security\ApiKeyCheck;

class ApiKeyCheckTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var ApiKeyCheck
     */
    private $object;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $userRepository;

    public function setUp()
    {
        $this->userRepository = $this->getMockBuilder('IngoWalther\ImageMinifyApi\Database\UserRepository')
                               ->disableOriginalConstructor(true)
                               ->getMock();

        $this->object = new ApiKeyCheck($this->userRepository);
    }

    public function testCheckWithInvalidAPIKey()
    {
        $this->userRepository->expects($this->once())
             ->method('findUserByKey')
             ->with('invalid')
             ->will($this->returnValue(false));

        $this->setExpectedException('Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException');
        $this->object->check('invalid');
    }

    public function testWithValidAPIKey()
    {
        $fakeUser = array('fakeuser' => true);

        $this->userRepository->expects($this->once())
            ->method('findUserByKey')
            ->with('valid')
            ->will($this->returnValue($fakeUser));

        $result = $this->object->check('valid');
        $this->assertEquals($fakeUser, $result);
    }
}