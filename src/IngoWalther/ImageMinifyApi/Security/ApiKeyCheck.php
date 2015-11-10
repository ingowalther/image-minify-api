<?php

namespace IngoWalther\ImageMinifyApi\Security;

use IngoWalther\ImageMinifyApi\Database\UserRepository;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Class ApiKeyCheck
 * @package IngoWalther\ImageMinifyApi\Security
 */
class ApiKeyCheck
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * ApiKeyCheck constructor.
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Checks for valid API-Key
     * @param string $apiKey
     */
    public function check($apiKey)
    {
        $user = $this->isKeyValid($apiKey);
        return $user;
    }

    /**
     * Checks if API-Key is valid
     * @param string $apiKey
     */
    private function isKeyValid($apiKey)
    {
        $user = $this->userRepository->findUserByKey($apiKey);

        if(!$user) {
            throw new AccessDeniedHttpException('Your API key is not valid');
        }
        return $user;
    }
}