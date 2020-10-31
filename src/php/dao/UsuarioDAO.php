<?php
/**
 * Created by PhpStorm.
 * User: devwarlt
 * Date: 28/10/2020
 * Time: 00:38
 */

namespace php\dao;

use php\dao\db\MySQLDatabase;
use php\dao\db\SQLQuery;
use php\model\LoginModel;
use php\model\UsuarioModel;

final class UsuarioDAO
{
    private static $singleton;

    private function __construct()
    {
    }

    public static function getSingleton(): UsuarioDAO
    {
        if (self::$singleton === null)
            self::$singleton = new UsuarioDAO();
        return self::$singleton;
    }

    public function criarUsuario(UsuarioModel $usuario): ?int
    {
        $mysql = MySQLDatabase::getSingleton();
        if (!$mysql->insert(
            new SQLQuery(
                "INSERT INTO `usuarios`(`nivel`) VALUES (:nivel)",
                [":nivel" => $usuario->getNivel()]
            )))
            return null;

        $result = $mysql->select(new SQLQuery("SELECT LAST_INSERT_ID() AS `id`"));
        if ($result === null)
            return null;

        $data = $result->fetch(\PDO::FETCH_OBJ);
        return $data->id;
    }

    public function consultarUsuario(int $id): ?UsuarioModel
    {
        $mysql = MySQLDatabase::getSingleton();
        $result = $mysql->select(
            new SQLQuery(
                "SELECT * FROM `usuarios` WHERE `id` = :id",
                [":id" => $id]
            )
        );
        if ($result === null)
            return null;

        $data = $result->fetch(\PDO::FETCH_OBJ);
        $usuario = new UsuarioModel(
            $data->id,
            $data->nivel
        );
        return $usuario;
    }

    public function consultarUsuarioPorNivel(int $id, int $nivel): ?UsuarioModel
    {
        $mysql = MySQLDatabase::getSingleton();
        $result = $mysql->select(
            new SQLQuery(
                "SELECT * FROM `usuarios` WHERE `id` = :id AND `nivel` = :nivel",
                [
                    ":id" => $id,
                    ":nivel" => $nivel
                ]
            )
        );
        if ($result === null)
            return null;

        $data = $result->fetch(\PDO::FETCH_OBJ);
        $usuario = new UsuarioModel(
            $data->id,
            $data->nivel
        );
        return $usuario;
    }

    public function consultarUsuariosPorNivel(int $nivel): ?array
    {
        $mysql = MySQLDatabase::getSingleton();
        $result = $mysql->select(
            new SQLQuery(
                "SELECT `logins`.*, `usuarios`.`nivel` FROM `logins` INNER JOIN `usuarios` ON `logins`.`id_usuario` = `usuarios`.`id` AND `usuarios`.`nivel` = :nivel",
                [":nivel" => $nivel]
            )
        );
        if ($result === null)
            return null;

        $collection = array();
        while ($data = $result->fetch(\PDO::FETCH_OBJ))
            array_push($collection,
                array(
                    "usr" => new UsuarioModel(
                        $data->id_usuario,
                        $data->nivel
                    ),
                    "login" => new LoginModel(
                        $data->id,
                        $data->nome,
                        $data->senha,
                        $data->id_usuario
                    )
                )
            );
        return $collection;
    }

    public function alterarUsuario(UsuarioModel $usuario): bool
    {
        $mysql = MySQLDatabase::getSingleton();
        return $mysql->update(
            new SQLQuery(
                "UPDATE `usuarios` SET `nivel` = :nivel WHERE `id` = :id",
                [
                    ":id" => $usuario->getId(),
                    ":nivel" => $usuario->getNivel()
                ]
            )
        );
    }

    public function removerUsuario(int $id): bool
    {
        $mysql = MySQLDatabase::getSingleton();
        return $mysql->delete(
            new SQLQuery(
                "DELETE FROM `usuarios` WHERE `id` = :id",
                [":id" => $id]
            )
        );
    }
}