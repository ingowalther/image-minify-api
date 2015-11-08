<?php

namespace IngoWalther\ImageMinifyApi\Test\Security;

use IngoWalther\ImageMinifyApi\Security\ApiKeyGenerator;

class ApiKeyGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ApiKeyGenerator
     */
    private $object;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $userRepository;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $randomStringGenerator;

    public function setUp()
    {
        $this->userRepository = $this->getMockBuilder('IngoWalther\ImageMinifyApi\Database\UserRepository')
            ->disableOriginalConstructor(true)
            ->getMock();

        $this->randomStringGenerator = $this->getMockBuilder('IngoWalther\ImageMinifyApi\Security\RandomStringGenerator')
            ->getMock();

        $this->object = new ApiKeyGenerator($this->userRepository, $this->randomStringGenerator);
    }

    public function testWithTakenUsername()
    {
        $username = 'taken';

        $this->userRepository->expects($this->once())
            ->method('findUserByName')
            ->with($username)
            ->will($this->returnValue(array('user' => 'peter')));

        $this->setExpectedException('Exception');
        $this->object->generate($username);
    }

    public function testWithFreeUsername()
    {
        $username = 'free';

        $this->userRepository->expects($this->at(0))
            ->method('findUserByName')
            ->with($username)
            ->will($this->returnValue(false));

        $this->randomStringGenerator->expects($this->at(0))
            ->method('generate')
            ->will($this->returnValue('taken1'));

        $this->randomStringGenerator->expects($this->at(1))
            ->method('generate')
            ->will($this->returnValue('taken2'));

        $this->randomStringGenerator->expects($this->at(2))
            ->method('generate')
            ->will($this->returnValue('freeKey'));

        $this->userRepository->expects($this->at(1))
            ->method('findUserByKey')
            ->with('taken1')
            ->will($this->returnValue(array('user' => 'peter')));

        $this->userRepository->expects($this->at(2))
            ->method('findUserByKey')
            ->with('taken2')
            ->will($this->returnValue(array('user' => 'peter2')));

        $this->userRepository->expects($this->at(3))
            ->method('findUserByKey')
            ->with('freeKey')
            ->will($this->returnValue(false));

        $this->userRepository->expects($this->at(4))
            ->method('addUser')
            ->with('free', 'freeKey');

        $key = $this->object->generate($username);

        $this->assertEquals('freeKey', $key);
    }

}