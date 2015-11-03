<?php

namespace IngoWalther\ImageMinifyApi\Security;

use Doctrine\DBAL\Driver\Connection;
use Symfony\Component\Config\Definition\Exception\Exception;

/**
 * Class ApiKeyGenerator
 * @package IngoWalther\ImageMinifyApi\Security
 */
class ApiKeyGenerator
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * ApiKeyGenerator constructor.
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param $username
     */
    public function generate($username)
    {
        $this->checkUsername($username);

        do {
            $key = RandomString::generate();
        } while (!$this->checkKey($key));

        $statement = $this->connection->prepare('INSERT INTO `user` (`id`, `name`, `api_key`) VALUES (NULL, ?, ?)');
        $statement->execute(array($username, $key));

        return $key;
    }

    /**
     * @param $username
     */
    private function checkUsername($username)
    {
        $stmt = $this->connection->prepare('SELECT * FROM `user` WHERE `name` = ?');
        $stmt->execute(array($username));

        if ($stmt->rowCount() > 0) {
            throw new Exception('This username is taken');
        }
    }

    private function checkKey($key)
    {
        $stmt = $this->connection->prepare('SELECT * FROM `user` WHERE `api_key` = ?');
        $stmt->execute(array($key));

        if ($stmt->rowCount() > 0) {
            return false;
        }
        return true;
    }


}