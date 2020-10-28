<?php
/**
 * Created by PhpStorm.
 * User: devwarlt
 * Date: 28/10/2020
 * Time: 00:31
 */

namespace php\model;

final class UserModel
{
    private $id, $nome, $nivel;

    public function __construct($id, $nome, $nivel)
    {
        $this->id = $id;
        $this->nome = $nome;
        $this->nivel = $nivel;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getNome()
    {
        return $this->nome;
    }

    public function setNome($nome)
    {
        $this->nome = $nome;
    }

    public function getNivel()
    {
        return $this->nivel;
    }

    public function setNivel($nivel)
    {
        $this->nivel = $nivel;
    }
}