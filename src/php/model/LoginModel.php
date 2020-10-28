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
    private $id_usuario, $nome, $senha;

    public function __construct($id_usuario, $nome, $senha)
    {
        $this->nome = $nome;
        $this->senha = $senha;
        $this->id_usuario = $id_usuario;
    }

    public function getIdUsuario(): int
    {
        return $this->id_usuario;
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