<?php

namespace IngoWalther\ImageMinifyApi\Security;

use IngoWalther\ImageMinifyApi\Database\UserRepository;
use Symfony\Component\HttpFoundation\Request;
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
     * @param Request $request
     */
    public function check(Request $request)
    {
        $this->checkKeyExists($request);
        $user = $this->isKeyValid($request);

        return $user;
    }

    /**
     * Checks if API-Key is supplied
     * @param Request $request
     */
    private function checkKeyExists(Request $request)
    {
        if (!$request->get('api_key', false)) {
            throw new AccessDeniedHttpException('You must supply an API Key');
        }
    }

    /**
     * Checks if API-Key is valid
     * @param Request $request
     */
    private function isKeyValid(Request $request)
    {
        $key = $request->get('api_key');
        $user = $this->userRepository->findUserByKey($key);

        if(!$user) {
            throw new AccessDeniedHttpException('Your API key is not valid');
        }
        return $user;
    }
}