<?php
/**
 * Created by PhpStorm.
 * User: devwarlt
 * Date: 28/10/2020
 * Time: 00:34
 */

namespace php\model;

final class VendaModel
{
    private $id, $id_usuario, $id_pagamento, $id_produtos, $preco_produtos, $valor, $data_registro;

    public function __construct($id, $id_usuario, $id_pagamento, $id_produtos, $preco_produtos, $valor, $data_registro)
    {
        $this->id = $id;
        $this->id_usuario = $id_usuario;
        $this->id_pagamento = $id_pagamento;
        $this->id_produtos = $id_produtos;
        $this->preco_produtos = $preco_produtos;
        $this->valor = $valor;
        $this->data_registro = $data_registro;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getIdUsuario()
    {
        return $this->id_usuario;
    }

    public function setIdUsuario($id_usuario): void
    {
        $this->id_usuario = $id_usuario;
    }

    public function getIdPagamento()
    {
        return $this->id_pagamento;
    }

    public function setIdPagamento($id_pagamento): void
    {
        $this->id_pagamento = $id_pagamento;
    }

    public function getIdProdutos()
    {
        return $this->id_produtos;
    }

    public function setIdProdutos($id_produtos): void
    {
        $this->id_produtos = $id_produtos;
    }

    public function getPrecoProdutos()
    {
        return $this->preco_produtos;
    }

    public function setPrecoProdutos($preco_produtos): void
    {
        $this->preco_produtos = $preco_produtos;
    }

    public function getValor()
    {
        return $this->valor;
    }

    public function setValor($valor): void
    {
        $this->valor = $valor;
    }

    public function getDataRegistro()
    {
        return $this->data_registro;
    }

    public function setDataRegistro($data_registro): void
    {
        $this->data_registro = $data_registro;
    }
}