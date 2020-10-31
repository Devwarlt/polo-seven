<?php
/**
 * Created by PhpStorm.
 * User: devwarlt
 * Date: 28/10/2020
 * Time: 02:53
 */

namespace php\model;

final class LoginModel
{
    private $id, $nome, $senha, $id_usuario;

    public function __construct(int $id, string $nome, string $senha, int $id_usuario)
    {
        $this->id = $id;
        $this->nome = $nome;
        $this->senha = $senha;
        $this->id_usuario = $id_usuario;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function getSenha(): string
    {
        return $this->senha;
    }

    public function getIdUsuario(): int
    {
        return $this->id_usuario;
    }
}