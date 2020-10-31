<?php
/**
 * Created by PhpStorm.
 * User: devwarlt
 * Date: 28/10/2020
 * Time: 01:37
 */

namespace php\view;

use php\model\UsuarioModel;
use php\PhpUtils;

final class DashboardView
{
    private $usuario;

    public function __construct(UsuarioModel $usuario)
    {
        $this->usuario = $usuario;
    }

    public function adicionarElementosHTML(): void
    {
        $panels = array();
        $nivel = $this->usuario->getNivel();
        if ($nivel <= UsuarioModel::SYSADMIN)
            array_push($panels, $this->criarPainelSysAdmin());
        //if ($nivel === usrmodel::GERENTE)
        array_push($panels, $this->criarPainelGerente());
        //if ($nivel === usrmodel::GERENTE || $nivel === UsuarioModel::VENDEDOR)
        array_push($panels, $this->criarPainelVendedor());
        if (sizeof($panels) == 0)
            echo "
            <strong>Não foi possível criar nenhum painel administrativo!</strong>
            <br/>
            <strong>Nível de acesso:</strong> $nivel
            <br/>
            Verifique o nível atribuido na conta deste usuário.
            ";
        else {
            $result = "";
            foreach ($panels as $panel)
                $result .= "
                <div class='container'>
                    <div class='row'>
                        <div class='col border-secondary'>
                            $panel
                        </div>
                    </div>
                </div>";

            echo $result;
        }
    }

    private function criarPainelSysAdmin(): string
    {
        $utils = PhpUtils::getSingleton();
        return $utils->getContents("/assets/panel_sysadmin.html");
    }

    private function criarPainelGerente(): string
    {
        $utils = PhpUtils::getSingleton();
        return $utils->getContents("/assets/panel_gerente.html");
    }

    private function criarPainelVendedor(): string
    {
        $utils = PhpUtils::getSingleton();
        return $utils->getContents("/assets/panel_vendedor.html");
    }

    public function adicionarScriptsJS(): void
    {
        $fscripts = array();
        $scripts = array();
        array_push($scripts, "/php/assets/utils.js");

        foreach ($scripts as $script)
            array_push($fscripts, "<script type='text/javascript' src='$script?t=" . time() . "'></script>");
        echo implode("\n", $fscripts);
    }
}