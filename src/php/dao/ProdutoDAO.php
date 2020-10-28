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

    public function consultarProduto(int $id): ProdutoModel
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

        $produto = new ProdutoModel(
            $result->id,
            $result->nome,
            $result->preco_unitario,
            $result->total_unidades
        );
        return $produto;
    }

    public function alterarProduto(ProdutoModel $produto): bool
    {
        $mysql = mysqldb::getSingleton();
        return $mysql->update(
            new sqlquery(
                "UPDATE `produtos` SET `id` = :id, `nome` = ':nome', `preco_unitario` = :preco_unitario, `total_unidades` = :total_unidades",
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