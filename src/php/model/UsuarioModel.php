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
    const SYSADMIN = 1;
    const GERENTE = 2;
    const VENDEDOR = 3;

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

    public function nivelDeAcesso(): ?string
    {
        if ($this->nivel === self::SYSADMIN)
            return "SYSADMIN";
        if ($this->nivel === self::GERENTE)
            return "GERENTE";
        if ($this->nivel === self::VENDEDOR)
            return "VENDEDOR";

        return null;
    }
}