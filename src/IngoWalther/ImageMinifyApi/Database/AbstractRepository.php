<?php

namespace IngoWalther\ImageMinifyApi\Database;

use \Doctrine\DBAL\Driver\Connection;

/**
 * Class AbstractRepository
 * @package IngoWalther\ImageMinifyApi\Database
 */
abstract class AbstractRepository
{
    protected $tableName = '';

    protected $connection;

    /**
     * AbstractRepository constructor.
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->setTableName();
    }

    /**
     * @param int $fetchType
     */
    public function findAll($fetchMode = \PDO::FETCH_ASSOC)
    {
        $query = sprintf('SELECT * FROM `%s`', $this->tableName);
        return $this->fetch($query, array(), $fetchMode);
    }

    /**
     * @param $query
     * @param array $params
     * @param int $fetchMode
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function fetch($query, $params = array(), $fetchMode= \PDO::FETCH_ASSOC)
    {
        $statement = $this->connection->prepare($query);
        $statement->execute($params);

        return $statement->fetchAll($fetchMode);
    }

    protected abstract function setTableName();
}