<?php
/**
 * Created by PhpStorm.
 * User: devwarlt
 * Date: 28/10/2020
 * Time: 00:38
 */

namespace php\dao\db;

final class SQLQuery
{
    private $sql, $params;

    public function __construct(string $sql, array $params = null)
    {
        $this->sql = $sql;
        $this->params = $params;
    }

    public function getQuery(): string
    {
        if ($this->params !== null)
            $this->sql = self::replaceArray($this->sql, $this->params);

        return $this->sql;
    }

    private static function replaceArray(string $value, array $array): string
    {
        foreach ($array as $innerKey => $innerValue)
            $value = self::replace($innerKey, $innerValue, $value);

        return $value;
    }

    private static function replace(string $key, string $value, string $subject): string
    {
        return str_replace($key, $value, $subject);
    }
}