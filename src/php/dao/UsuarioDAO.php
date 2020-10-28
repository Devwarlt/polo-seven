<?php
/**
 * Created by PhpStorm.
 * User: devwarlt
 * Date: 28/10/2020
 * Time: 00:38
 */

namespace php\dao;

use php\dao\db\MySQLDatabase as mysqldb;
use php\dao\db\SQLQuery as sqlquery;
use php\model\UsuarioModel;

final class UsuarioDAO
{
    public function criarUsuario(UsuarioModel $usuario): bool
    {
        $mysql = mysqldb::getSingleton();
        return $mysql->insert(
            new sqlquery(
                "INSERT INTO `usuarios`(`nome`, `nivel`) VALUES (':nome', :nivel)",
                [
                    ":nome" => $usuario->getNome(),
                    ":nivel" => $usuario->getNivel()
                ]
            ));
    }

    public function consultarUsuario($id): UsuarioModel
    {
        $mysql = mysqldb::getSingleton();
        $result = $mysql->select(
            new sqlquery(
                "SELECT * FROM `usuarios` WHERE `id` = :id",
                [":id" => $id]
            )
        );
        if ($result === null)
            return null;

        $usuario = new UsuarioModel(
            $result->id,
            $result->nome,
            $result->nivel
        );
        return $usuario;
    }

    public function alterarUsuario(UsuarioModel $usuario): bool
    {
        $mysql = mysqldb::getSingleton();
        return $mysql->update(
            new sqlquery(
                "UPDATE `usuarios` SET `id` = :id, `nome` = ':nome', `nivel` = :nivel",
                [
                    ":id" => $usuario->getId(),
                    ":nome" => $usuario->getNome(),
                    ":nivel" => $usuario->getNivel()
                ]
            )
        );
    }

    public function removerUsuario($id): bool
    {
        $mysql = mysqldb::getSingleton();
        return $mysql->delete(
            new sqlquery(
                "DELETE FROM `usuarios` WHERE `id` = :id",
                [":id" => $id]
            )
        );
    }
}