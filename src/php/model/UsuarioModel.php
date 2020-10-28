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
    private $id, $nivel;

    public function __construct(int $id, int $nivel)
    {
        $this->id = $id;
        $this->nivel = $nivel;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getNivel(): int
    {
        return $this->nivel;
    }

    public function setNivel(int $nivel): void
    {
        $this->nivel = $nivel;
    }
}