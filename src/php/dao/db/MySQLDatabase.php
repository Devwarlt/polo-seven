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
        if ($query->execute())
            return true;
        else {
            $this->displayPDOError($query);
            return false;
        }
    }

    private function displayPDOError(\PDOStatement $query): void
    {
        echo "
        <div class='card text-white bg-danger' style='padding: 4px; width: 100%'>
            <div class='text-warning card-header'><strong><span class='glyphicon glyphicon-alert' aria-hidden='true'></span>&nbsp;&nbsp;<u>Query error detected!</u></strong></div>
            <div class='card-body'>
                <h5>Query:</h5>&nbsp;
                <code class='text-warning bg-dark border-warning rounded'>&nbsp;" . $query->queryString . "&nbsp;</code>            
            </div>
            <div class='card-footer'>
                <h5>PDO::errorInfo():</h5>
                <p><small><strong>&blacktriangleright;&nbsp;SQLSTATE Error Code:</strong> " . $query->errorInfo()[0] . "</small></p>
                <p><small><strong>&blacktriangleright;&nbsp;Driver-specific Error Code:</strong> " . $query->errorInfo()[1] . "</small></p>
                <p><small><strong>&blacktriangleright;&nbsp;Driver-specific Error Message:</strong> " . $query->errorInfo()[2] . "</small></p>
            </div>
        </div>
        <hr/>
        ";
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
        if (($success = $query->execute()) && $query->rowCount() > 0)
            return $query;

        if (!$success)
            $this->displayPDOError($query);
        return null;
    }
}