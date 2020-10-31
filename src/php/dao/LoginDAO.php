<?php
/**
 * Created by PhpStorm.
 * User: devwarlt
 * Date: 28/10/2020
 * Time: 03:01
 */

namespace php\dao;

use php\dao\db\MySQLDatabase;
use php\dao\db\SQLQuery;
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

    public function criarLogin(LoginModel $login, int $id_usuario): bool
    {
        $mysql = MySQLDatabase::getSingleton();
        return $mysql->insert(
            new SQLQuery(
                "INSERT INTO `logins`(`nome`, `senha`, `id_usuario`) VALUES (':nome', ':senha', :id_usuario)",
                [
                    ":nome" => $login->getNome(),
                    ":senha" => $login->getSenha(),
                    ":id_usuario" => $id_usuario
                ]
            ));
    }

    public function consultarLoginId(int $id): ?LoginModel
    {
        $mysql = MySQLDatabase::getSingleton();
        $result = $mysql->select(
            new SQLQuery(
                "SELECT * FROM `logins` WHERE `id` = :id",
                [":id" => $id]
            )
        );
        if ($result === null)
            return null;

        $data = $result->fetch(\PDO::FETCH_OBJ);
        $registered = new LoginModel(
            $id,
            $data->nome,
            $data->senha,
            $data->id_usuario
        );
        return $registered;
    }

    public function consultarLogin(LoginModel $login): ?LoginModel
    {
        $mysql = MySQLDatabase::getSingleton();
        $result = $mysql->select(
            new SQLQuery(
                "SELECT * FROM `logins` WHERE `nome` = ':nome' AND `senha` = ':senha'",
                [
                    ":nome" => $login->getNome(),
                    ":senha" => $login->getSenha()
                ]
            )
        );
        if ($result === null)
            return null;

        $data = $result->fetch(\PDO::FETCH_OBJ);
        $registered = new LoginModel(
            $data->id,
            $login->getNome(),
            $login->getSenha(),
            $data->id_usuario
        );
        return $registered;
    }

    public function alterarLogin(LoginModel $login): bool
    {
        $mysql = MySQLDatabase::getSingleton();
        return $mysql->update(
            new SQLQuery(
                "UPDATE `logins` SET `nome` = ':nome', `senha` = ':senha' WHERE `id` = :id",
                [
                    ":id" => $login->getId(),
                    ":nome" => $login->getNome(),
                    ":senha" => $login->getSenha()
                ]
            )
        );
    }

    public function removerLogin(int $id): bool
    {
        $mysql = MySQLDatabase::getSingleton();
        return $mysql->delete(
            new SQLQuery(
                "DELETE FROM `logins` WHERE `id` = :id",
                [":id" => $id]
            )
        );
    }
}