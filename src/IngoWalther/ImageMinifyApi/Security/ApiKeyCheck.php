<?php

namespace IngoWalther\ImageMinifyApi\Security;

use Doctrine\DBAL\Driver\Connection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Class ApiKeyCheck
 * @package IngoWalther\ImageMinifyApi\Security
 */
class ApiKeyCheck
{

    /**
     * @var Connection
     */
    private $connection;

    /**
     * ApiKeyCheck constructor.
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Checks for valid API-Key
     * @param Request $request
     */
    public function check(Request $request)
    {
        $this->checkKeyExists($request);
        $this->isKeyValid($request);
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

        $stmt = $this->connection->prepare('SELECT * FROM `user` WHERE `api_key` = ?');
        $stmt->execute(array($key));

        if (!$stmt->rowCount()) {
            throw new AccessDeniedHttpException('Your API key is not valid');
        }
    }
}