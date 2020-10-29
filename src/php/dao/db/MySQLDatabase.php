<?php
/**
 * Created by PhpStorm.
 * User: devwarlt
 * Date: 28/10/2020
 * Time: 00:37
 */

namespace php\dao\db;

define("DB_HOST", "localhost");
define("DB_SCHEMA", "polo-seven");
define("DB_USER", "root");
define("DB_PASSWORD", "");

final class MySQLDatabase
{
    private static $singleton, $connection;

    private function __construct(\PDO $connection)
    {
        self::$connection = $connection;
    }

    public static function getSingleton(): MySQLDatabase
    {
        if (self::$singleton === null)
            self::$singleton = new MySQLDatabase(new \PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_SCHEMA, DB_USER, DB_PASSWORD));

        return self::$singleton;
    }

    public function update(SQLQuery $query): bool
    {
        return $this->dml($query->getQuery());
    }

    private function dml(string $sql): bool
    {
        $query = self::$connection->prepare($sql);
        return $query->execute();
    }

    public function delete(SQLQuery $query): bool
    {
        return $this->dml($query->getQuery());
    }

    public function insert(SQLQuery $query): bool
    {
        return $this->dml($query->getQuery());
    }

    public function select(SQLQuery $query): ?\PDOStatement
    {
        return $this->dql($query->getQuery());
    }

    private function dql(string $sql): ?\PDOStatement
    {
        $query = self::$connection->query($sql);
        if ($query->execute() && $query->rowCount() > 0)
            return $query;
        return null;
    }
}