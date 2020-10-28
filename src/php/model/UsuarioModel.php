<?php
/**
 * Created by PhpStorm.
 * User: devwarlt
 * Date: 28/10/2020
 * Time: 00:31
 */

namespace php\model;

final class UsuarioModel
{
    private $id, $nome, $nivel;

    public function __construct($id, $nome, $nivel)
    {
        $this->id = $id;
        $this->nome = $nome;
        $this->nivel = $nivel;
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

    public function getNivel(): int
    {
        return $this->nivel;
    }

    public function setNivel($nivel): void
    {
        $this->nivel = $nivel;
    }
}