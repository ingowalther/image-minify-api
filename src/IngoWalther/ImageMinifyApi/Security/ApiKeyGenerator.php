<?php

namespace IngoWalther\ImageMinifyApi\Security;

use IngoWalther\ImageMinifyApi\Database\UserRepository;
use Symfony\Component\Config\Definition\Exception\Exception;

/**
 * Class ApiKeyGenerator
 * @package IngoWalther\ImageMinifyApi\Security
 */
class ApiKeyGenerator
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var RandomStringGenerator
     */
    private $randomStringGenerator;

    /**
     * ApiKeyGenerator constructor.
     * @param Connection $connection
     */
    public function __construct(UserRepository $userRepository, RandomStringGenerator $randomStringGenerator)
    {
        $this->userRepository = $userRepository;
        $this->randomStringGenerator = $randomStringGenerator;
    }

    /**
     * @param $username
     * @return string
     */
    public function generate($username)
    {
        $this->checkUsername($username);

        do {
            $key = $this->randomStringGenerator->generate();
        } while (!$this->checkKey($key));

        $this->userRepository->addUser($username, $key);
        return $key;
    }

    /**
     * @param $username
     */
    private function checkUsername($username)
    {
        $user = $this->userRepository->findUserByName($username);
        if ($user) {
            throw new Exception('This username is taken');
        }
    }

    private function checkKey($key)
    {
        $user = $this->userRepository->findUserByKey($key);
        if($user) {
            return false;
        }
        return true;
    }
}