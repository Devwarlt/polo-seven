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

    public function __construct(int $id, int $id_usuario, int $id_pagamento, array $id_produtos, array $preco_produtos, float $valor, ?string $data_registro)
    {
        $this->id = $id;
        $this->id_usuario = $id_usuario;
        $this->id_pagamento = $id_pagamento;
        $this->id_produtos = $id_produtos;
        $this->preco_produtos = $preco_produtos;
        $this->valor = $valor;
        $this->data_registro = $data_registro;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getIdUsuario(): int
    {
        return $this->id_usuario;
    }

    public function setIdUsuario(int $id_usuario): void
    {
        $this->id_usuario = $id_usuario;
    }

    public function getIdPagamento(): int
    {
        return $this->id_pagamento;
    }

    public function setIdPagamento(int $id_pagamento): void
    {
        $this->id_pagamento = $id_pagamento;
    }

    public function getIdProdutos(): array
    {
        return $this->id_produtos;
    }

    public function setIdProdutos(array $id_produtos): void
    {
        $this->id_produtos = $id_produtos;
    }

    public function getPrecoProdutos(): array
    {
        return $this->preco_produtos;
    }

    public function setPrecoProdutos(array $preco_produtos): void
    {
        $this->preco_produtos = $preco_produtos;
    }

    public function getValor(): float
    {
        return $this->valor;
    }

    public function setValor(float $valor): void
    {
        $this->valor = $valor;
    }

    public function getDataRegistro(): string
    {
        return $this->data_registro;
    }
}