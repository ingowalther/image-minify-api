<?php

namespace IngoWalther\ImageMinifyApi\Database;

/**
 * Class UserRepository
 * @package IngoWalther\ImageMinifyApi\Database
 */
class UserRepository extends AbstractRepository
{
    protected function setTableName()
    {
        $this->tableName = 'user';
    }

    /**
     * @param string $userName
     * @return bool|array
     */
    public function findUserByName($userName)
    {
        $query = 'SELECT *  FROM `user` WHERE `name` = ?';

        $result = $this->fetch($query, array($userName));
        if (count($result) == 0) {
            return false;
        }
        return array_pop($result);
    }

    /**
     * @param string $key
     * @return bool|array
     */
    public function findUserByKey($key)
    {
        $query = 'SELECT *  FROM `user` WHERE `api_key` = ?';

        $result = $this->fetch($query, array($key));
        if (count($result) == 0) {
            return false;
        }
        return array_pop($result);
    }

    /**
     * @param $username
     * @param $key
     */
    public function addUser($username, $key)
    {
        $statement = $this->connection->prepare('INSERT INTO `user` (`id`, `name`, `api_key`) VALUES (NULL, ?, ?)');
        $statement->execute(array($username, $key));
    }

}