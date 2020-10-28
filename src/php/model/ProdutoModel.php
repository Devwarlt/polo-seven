<?php
/**
 * Created by PhpStorm.
 * User: devwarlt
 * Date: 28/10/2020
 * Time: 00:34
 */

namespace php\model;

final class ProdutoModel
{
    private $id, $nome, $preco_unitario, $total_unidades;

    public function __construct($id, $nome, $preco_unitario, $total_unidades)
    {
        $this->id = $id;
        $this->nome = $nome;
        $this->preco_unitario = $preco_unitario;
        $this->total_unidades = $total_unidades;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function setNome($nome): void
    {
        $this->nome = $nome;
    }

    public function getPrecoUnitario(): float
    {
        return $this->preco_unitario;
    }

    public function setPrecoUnitario($preco_unitario): void
    {
        $this->preco_unitario = $preco_unitario;
    }

    public function getTotalUnidades(): int
    {
        return $this->total_unidades;
    }

    public function setTotalUnidades($total_unidades): void
    {
        $this->total_unidades = $total_unidades;
    }


}