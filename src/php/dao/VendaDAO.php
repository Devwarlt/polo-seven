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
use php\model\VendaModel;

final class VendaDAO
{
    private static $singleton;

    private function __construct()
    {
    }

    public static function getSingleton(): VendaDAO
    {
        if (self::$singleton === null)
            self::$singleton = new VendaDAO();
        return self::$singleton;
    }

    public function criarVenda(VendaModel $venda): bool
    {
        $mysql = MySQLDatabase::getSingleton();
        return $mysql->insert(
            new SQLQuery(
                "INSERT INTO `vendas`(`id_usuario`, `id_pagamento`, `id_produtos`, `preco_produtos`, `valor`) VALUES (:id_usuario, :id_pagamento, ':id_produtos', ':preco_produtos', :valor)",
                [
                    ":id_usuario" => $venda->getIdUsuario(),
                    ":id_pagamento" => $venda->getIdPagamento(),
                    ":id_produtos" => implode(',', $venda->getIdProdutos()),
                    ":preco_produtos" => implode(',', $venda->getPrecoProdutos()),
                    ":valor" => $venda->getValor()
                ]
            ));
    }

    public function consultarVenda(int $id): ?VendaModel
    {
        $mysql = MySQLDatabase::getSingleton();
        $result = $mysql->select(
            new SQLQuery(
                "SELECT * FROM `vendas` WHERE `id` = :id",
                [":id" => $id]
            )
        );
        if ($result === null)
            return null;

        $data = $result->fetch(\PDO::FETCH_OBJ);
        $venda = new VendaModel(
            $data->id,
            $data->id_usuario,
            $data->id_pagamento,
            explode(',', $data->id_produtos),
            explode(',', $data->preco_produtos),
            $data->valor,
            $data->data_registro
        );
        return $venda;
    }

    public function consultarVendas(): ?array
    {
        $mysql = MySQLDatabase::getSingleton();
        $result = $mysql->select(new SQLQuery("SELECT * FROM `vendas`"));
        if ($result === null)
            return null;

        $collection = array();
        while ($data = $result->fetch(\PDO::FETCH_OBJ))
            array_push($collection,
                new VendaModel(
                    $data->id,
                    $data->id_usuario,
                    $data->id_pagamento,
                    explode(',', $data->id_produtos),
                    explode(',', $data->preco_produtos),
                    $data->valor,
                    $data->data_registro
                )
            );
        return $collection;
    }

    public function alterarVenda(VendaModel $venda): bool
    {
        $mysql = MySQLDatabase::getSingleton();
        return $mysql->update(
            new SQLQuery(
                "UPDATE `vendas` SET `id_usuario` = :id_usuario, `id_pagamento` = :id_pagamento, `id_produtos` = ':id_produtos', `preco_produtos` = ':preco_produtos', `valor` = :valor, `data_registro` = CURRENT_TIMESTAMP WHERE `id` = :id__",
                [
                    ":id__" => $venda->getId(),
                    ":id_usuario" => $venda->getIdUsuario(),
                    ":id_pagamento" => $venda->getIdPagamento(),
                    ":id_produtos" => implode(',', $venda->getIdProdutos()),
                    ":preco_produtos" => implode(',', $venda->getPrecoProdutos()),
                    ":valor" => $venda->getValor()
                ]
            )
        );
    }

    public function removerVenda(int $id): bool
    {
        $mysql = MySQLDatabase::getSingleton();
        return $mysql->delete(
            new SQLQuery(
                "DELETE FROM `vendas` WHERE `id` = :id",
                [":id" => $id]
            )
        );
    }
}