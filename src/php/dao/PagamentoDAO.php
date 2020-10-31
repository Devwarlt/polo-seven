<?php
/**
 * Created by PhpStorm.
 * User: devwarlt
 * Date: 28/10/2020
 * Time: 01:44
 */

namespace php\dao;

use php\dao\db\MySQLDatabase;
use php\dao\db\SQLQuery;
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
        $mysql = MySQLDatabase::getSingleton();
        return $mysql->insert(
            new SQLQuery(
                "INSERT INTO `pagamentos`(`nome`) VALUES (':nome')",
                [":nome" => $pagamento->getNome()]
            ));
    }

    public function consultarPagamento(int $id): ?PagamentoModel
    {
        $mysql = MySQLDatabase::getSingleton();
        $result = $mysql->select(
            new SQLQuery(
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
        $mysql = MySQLDatabase::getSingleton();
        return $mysql->update(
            new SQLQuery(
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
        $mysql = MySQLDatabase::getSingleton();
        return $mysql->delete(
            new SQLQuery(
                "DELETE FROM `pagamentos` WHERE `id` = :id",
                [":id" => $id]
            )
        );
    }
}