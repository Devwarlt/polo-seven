<?php
/**
 * Created by PhpStorm.
 * ProdutoModel: devwarlt
 * Date: 28/10/2020
 * Time: 00:39
 */

namespace php\dao;

use php\dao\db\MySQLDatabase as mysqldb;
use php\dao\db\SQLQuery as sqlquery;
use php\model\ProdutoModel;

final class ProdutoDAO
{
    private static $singleton;

    private function __construct()
    {
    }

    public static function getSingleton(): ProdutoDAO
    {
        if (self::$singleton === null)
            self::$singleton = new ProdutoDAO();
        return self::$singleton;
    }

    public function criarProduto(ProdutoModel $produto): bool
    {
        $mysql = mysqldb::getSingleton();
        return $mysql->insert(
            new sqlquery(
                "INSERT INTO `produtos`(`nome`, `preco_unitario`, `total_unidades`) VALUES (':nome', :preco_unitario, :total_unidades)",
                [
                    ":nome" => $produto->getNome(),
                    ":preco_unitario" => $produto->getPrecoUnitario(),
                    ":total_unidades" => $produto->getTotalUnidades()
                ]
            ));
    }

    public function consultarProduto(int $id): ?ProdutoModel
    {
        $mysql = mysqldb::getSingleton();
        $result = $mysql->select(
            new sqlquery(
                "SELECT * FROM `produtos` WHERE `id` = :id",
                [":id" => $id]
            )
        );
        if ($result === null)
            return null;

        $data = $result->fetch(\PDO::FETCH_OBJ);
        $produto = new ProdutoModel(
            $data->id,
            $data->nome,
            $data->preco_unitario,
            $data->total_unidades
        );
        return $produto;
    }

    public function alterarProduto(ProdutoModel $produto): bool
    {
        $mysql = mysqldb::getSingleton();
        return $mysql->update(
            new sqlquery(
                "UPDATE `produtos` SET `nome` = ':nome', `preco_unitario` = :preco_unitario, `total_unidades` = :total_unidades WHERE `id` = :id",
                [
                    ":id" => $produto->getId(),
                    ":nome" => $produto->getNome(),
                    ":preco_unitario" => $produto->getPrecoUnitario(),
                    ":total_unidades" => $produto->getTotalUnidades()
                ]
            )
        );
    }

    public function removerProduto(int $id): bool
    {
        $mysql = mysqldb::getSingleton();
        return $mysql->delete(
            new sqlquery(
                "DELETE FROM `produtos` WHERE `id` = :id",
                [":id" => $id]
            )
        );
    }
}