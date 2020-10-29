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
    private $id, $nome, $senha;

    public function __construct(int $id, string $nome, string $senha)
    {
        $this->id = $id;
        $this->nome = $nome;
        $this->senha = $senha;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getNome()
    {
        return $this->nome;
    }

    public function getSenha()
    {
        return $this->senha;
    }
}