<?php
/**
 * Created by PhpStorm.
 * User: devwarlt
 * Date: 28/10/2020
 * Time: 03:01
 */

namespace php\dao;

use php\dao\db\MySQLDatabase as mysqldb;
use php\dao\db\SQLQuery as sqlquery;
use php\model\LoginModel;

final class LoginDAO
{
    private static $singleton;

    private function __construct()
    {
    }

    public static function getSingleton(): LoginDAO
    {
        if (self::$singleton === null)
            self::$singleton = new LoginDAO();
        return self::$singleton;
    }

    public function consultarLogin(string $nome, string $senha): ?LoginModel
    {
        $mysql = mysqldb::getSingleton();
        $result = $mysql->select(
            new sqlquery(
                "SELECT `id` FROM `logins` WHERE `nome` = ':nome' AND `senha` = ':senha'",
                [
                    ":nome" => $nome,
                    ":senha" => $senha
                ]
            )
        );

        if ($result === null)
            return null;

        $data = $result->fetch(\PDO::FETCH_OBJ);
        $login = new LoginModel(
            $data->id,
            $nome,
            $senha
        );
        return $login;
    }
}