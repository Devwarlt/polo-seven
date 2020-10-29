<?php
/**
 * Created by PhpStorm.
 * User: devwarlt
 * Date: 28/10/2020
 * Time: 01:44
 */

namespace php\dao;

use php\dao\db\MySQLDatabase as mysqldb;
use php\dao\db\SQLQuery as sqlquery;
use php\model\PagamentoModel;

final class PagamentoDAO
{
    private static $singleton;

    private function __construct()
    {
    }

    public static function getSingleton(): PagamentoDAO
    {
        if (self::$singleton === null)
            self::$singleton = new PagamentoDAO();
        return self::$singleton;
    }

    public function criarPagamento(PagamentoModel $pagamento): bool
    {
        $mysql = mysqldb::getSingleton();
        return $mysql->insert(
            new sqlquery(
                "INSERT INTO `pagamentos`(`nome`) VALUES (':nome')",
                [":nome" => $pagamento->getNome()]
            ));
    }

    public function consultarPagamento(int $id): ?PagamentoModel
    {
        $mysql = mysqldb::getSingleton();
        $result = $mysql->select(
            new sqlquery(
                "SELECT * FROM `pagamentos` WHERE `id` = :id",
                [":id" => $id]
            )
        );
        if ($result === null)
            return null;

        $data = $result->fetch(\PDO::FETCH_OBJ);
        $pagamento = new PagamentoModel(
            $data->id,
            $data->nome
        );
        return $pagamento;
    }

    public function alterarPagamento(PagamentoModel $pagamento): bool
    {
        $mysql = mysqldb::getSingleton();
        return $mysql->update(
            new sqlquery(
                "UPDATE `pagamentos` SET `nome` = ':nome' WHERE `id` = :id",
                [
                    ":id" => $pagamento->getId(),
                    ":nome" => $pagamento->getNome()
                ]
            )
        );
    }

    public function removerPagamento(int $id): bool
    {
        $mysql = mysqldb::getSingleton();
        return $mysql->delete(
            new sqlquery(
                "DELETE FROM `pagamentos` WHERE `id` = :id",
                [":id" => $id]
            )
        );
    }
}