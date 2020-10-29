<?php
/**
 * Created by PhpStorm.
 * User: devwarlt
 * Date: 28/10/2020
 * Time: 01:37
 */

namespace php\view;

use php\model\UsuarioModel;

final class DashboardView
{
    private $usuario;

    public function __construct(UsuarioModel $usuario)
    {
        $this->usuario = $usuario;
    }

    public function criarPaineisAdministrativos(): void
    {
        $panels = array();
        $nivel = $this->usuario->getNivel();
        if ($nivel <= UsuarioModel::SYSADMIN)
            array_push($panels, $this->criarPainelSysAdmin());
        //if ($nivel === UsuarioModel::GERENTE)
        array_push($panels, $this->criarPainelGerente());
        //if ($nivel === UsuarioModel::GERENTE || $nivel === UsuarioModel::VENDEDOR)
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
        return "
        <h3 class='card-header'>Painel do SysAdmin</h3>
        <br/>
        <div class='container'>
            <div class='card card-body row border-secondary'>
                <div class='col-md-12 text-center'>
                    <h4>Ações para Gerente:</h4>
                    <div class='btn-group'>
                        <button type='button' class='btn btn-sm btn-outline-success' data-toggle='collapse' data-target='#criar-gerente' aria-expanded='false' aria-controls='criar-gerente'><strong>Criar</strong></button>
                        <button type='button' class='btn btn-sm btn-outline-info' data-toggle='collapse' data-target='#consultar-gerente' aria-expanded='false' aria-controls='consultar-gerente'><strong>Consultar</strong></button>
                        <button type='button' class='btn btn-sm btn-outline-warning' data-toggle='collapse' data-target='#alterar-gerente' aria-expanded='false' aria-controls='alterar-gerente'><strong>Alterar</strong></button>
                        <button type='button' class='btn btn-sm btn-outline-danger' data-toggle='collapse' data-target='#remover-gerente' aria-expanded='false' aria-controls='remover-gerente'><strong>Remover</strong></button>
                    </div>
                </div>
            </div>
        </div>
        <br/>
        <div class='container'>
            <div class='row'>
                <div class='col'>
                    <div class='collapse' id='criar-gerente'>
                        <div class='container alert alert-success  border-success'>
                            <button type='button' class='close' data-toggle='collapse' data-target='#criar-gerente' aria-expanded='false' aria-controls='criar-gerente'>&times;</button>
                            <h4>Criar Gerente</h4>
                            <hr/>
                            <strong>TO-DO:</strong> criar gerente form!
                        </div>
                    </div>
                </div>
            </div>
            <div class='row'>
                <div class='col'>
                    <div class='collapse' id='consultar-gerente'>
                        <div class='container alert alert-info  border-info'>
                            <button type='button' class='close' data-toggle='collapse' data-target='#consultar-gerente' aria-expanded='false' aria-controls='consultar-gerente'>&times;</button>
                            <h4>Consultar Gerente</h4>
                            <hr/>
                            <strong>TO-DO:</strong> consultar gerente form!
                        </div>
                    </div>
                </div>
            </div>
            <div class='row'>
                <div class='col'>
                    <div class='collapse' id='alterar-gerente'>
                        <div class='container alert alert-warning  border-warning'>
                            <button type='button' class='close' data-toggle='collapse' data-target='#alterar-gerente' aria-expanded='false' aria-controls='alterar-gerente'>&times;</button>
                            <h4>Alterar Gerente</h4>
                            <hr/>
                            <strong>TO-DO:</strong> alterar gerente form!
                        </div>
                    </div>
                </div>
            </div>
            <div class='row'>
                <div class='col'>
                    <div class='collapse' id='remover-gerente'>
                        <div class='container alert alert-danger  border-danger'>
                            <button type='button' class='close' data-toggle='collapse' data-target='#remover-gerente' aria-expanded='false' aria-controls='remover-gerente'>&times;</button>
                            <h4>Remover Gerente</h4>
                            <hr/>
                            <strong>TO-DO:</strong> remover gerente form!
                        </div>
                    </div>
                </div>
            </div>
        </div>
        ";
    }

    private function criarPainelGerente(): string
    {
        return "
        <h3 class='card-header'>Painel do Gerente</h3>
        <br/>
        <div class='container'>
            <div class='card card-body row border-secondary'>
                <div class='col-md-12 text-center'>
                    <h4>Ações para Vendedor:</h4>
                    <div class='btn-group'>
                        <button type='button' class='btn btn-sm btn-outline-success' data-toggle='collapse' data-target='#criar-vendedor' aria-expanded='false' aria-controls='criar-vendedor'><strong>Criar</strong></button>
                        <button type='button' class='btn btn-sm btn-outline-info' data-toggle='collapse' data-target='#consultar-vendedor' aria-expanded='false' aria-controls='consultar-vendedor'><strong>Consultar</strong></button>
                        <button type='button' class='btn btn-sm btn-outline-warning' data-toggle='collapse' data-target='#alterar-vendedor' aria-expanded='false' aria-controls='alterar-vendedor'><strong>Alterar</strong></button>
                        <button type='button' class='btn btn-sm btn-outline-danger' data-toggle='collapse' data-target='#remover-vendedor' aria-expanded='false' aria-controls='remover-vendedor'><strong>Remover</strong></button>
                    </div>
                </div>
            </div>
        </div>
        <br/>
        <div class='container'>
            <div class='row'>
                <div class='col'>
                    <div class='collapse' id='criar-vendedor'>
                        <div class='container alert alert-success  border-success'>
                            <button type='button' class='close' data-toggle='collapse' data-target='#criar-vendedor' aria-expanded='false' aria-controls='criar-vendedor'>&times;</button>
                            <h4>Criar Vendedor</h4>
                            <hr/>
                            <strong>TO-DO:</strong> criar vendedor form!
                        </div>
                    </div>
                </div>
            </div>
            <div class='row'>
                <div class='col'>
                    <div class='collapse' id='consultar-vendedor'>
                        <div class='container alert alert-info  border-info'>
                            <button type='button' class='close' data-toggle='collapse' data-target='#consultar-vendedor' aria-expanded='false' aria-controls='consultar-vendedor'>&times;</button>
                            <h4>Consultar Vendedor</h4>
                            <hr/>
                            <strong>TO-DO:</strong> consultar vendedor form!
                        </div>
                    </div>
                </div>
            </div>
            <div class='row'>
                <div class='col'>
                    <div class='collapse' id='alterar-vendedor'>
                        <div class='container alert alert-warning  border-warning'>
                            <button type='button' class='close' data-toggle='collapse' data-target='#alterar-vendedor' aria-expanded='false' aria-controls='alterar-vendedor'>&times;</button>
                            <h4>Alterar Vendedor</h4>
                            <hr/>
                            <strong>TO-DO:</strong> alterar vendedor form!
                        </div>
                    </div>
                </div>
            </div>
            <div class='row'>
                <div class='col'>
                    <div class='collapse' id='remover-vendedor'>
                        <div class='container alert alert-danger  border-danger'>
                            <button type='button' class='close' data-toggle='collapse' data-target='#remover-vendedor' aria-expanded='false' aria-controls='remover-vendedor'>&times;</button>
                            <h4>Remover Vendedor</h4>
                            <hr/>
                            <strong>TO-DO:</strong> remover vendedor form!
                        </div>
                    </div>
                </div>
            </div>
        </div>
        ";
    }

    private function criarPainelVendedor(): string
    {
        return "
        <h3 class='card-header'>Painel do Vendedor</h3>
        <br/>
        <div class='container'>
            <div class='card card-body row border-secondary'>
                <div class='col-md-12 text-center'>
                    <h4>Ações para Produto:</h4>
                    <div class='btn-group'>
                        <button type='button' class='btn btn-sm btn-outline-success' data-toggle='collapse' data-target='#criar-produto' aria-expanded='false' aria-controls='criar-produto'><strong>Criar</strong></button>
                        <button type='button' class='btn btn-sm btn-outline-info' data-toggle='collapse' data-target='#consultar-produto' aria-expanded='false' aria-controls='consultar-produto'><strong>Consultar</strong></button>
                        <button type='button' class='btn btn-sm btn-outline-warning' data-toggle='collapse' data-target='#alterar-produto' aria-expanded='false' aria-controls='alterar-produto'><strong>Alterar</strong></button>
                        <button type='button' class='btn btn-sm btn-outline-danger' data-toggle='collapse' data-target='#remover-produto' aria-expanded='false' aria-controls='remover-produto'><strong>Remover</strong></button>
                    </div>
                </div>
            </div>
            <br/>
            <div class='card card-body row border-secondary'>
                <div class='col-md-12 text-center'>
                    <h4>Ações para Venda:</h4>
                    <div class='btn-group'>
                        <button type='button' class='btn btn-sm btn-outline-success' data-toggle='collapse' data-target='#criar-venda' aria-expanded='false' aria-controls='criar-venda'><strong>Criar</strong></button>
                        <button type='button' class='btn btn-sm btn-outline-info' data-toggle='collapse' data-target='#consultar-venda' aria-expanded='false' aria-controls='consultar-venda'><strong>Consultar</strong></button>
                        <button type='button' class='btn btn-sm btn-outline-warning' data-toggle='collapse' data-target='#alterar-venda' aria-expanded='false' aria-controls='alterar-venda'><strong>Alterar</strong></button>
                        <button type='button' class='btn btn-sm btn-outline-danger' data-toggle='collapse' data-target='#remover-venda' aria-expanded='false' aria-controls='remover-venda'><strong>Remover</strong></button>
                    </div>
                </div>
            </div>
        </div>
        <br/>
        <div class='container'>
            <div class='row'>
                <div class='col'>
                    <div class='collapse' id='criar-produto'>
                        <div class='container alert alert-success  border-success'>
                            <button type='button' class='close' data-toggle='collapse' data-target='#criar-produto' aria-expanded='false' aria-controls='criar-produto'>&times;</button>
                            <h4>Criar Produto</h4>
                            <hr/>
                            <strong>TO-DO:</strong> criar produto form!
                        </div>
                    </div>
                </div>
            </div>
            <div class='row'>
                <div class='col'>
                    <div class='collapse' id='consultar-produto'>
                        <div class='container alert alert-info  border-info'>
                            <button type='button' class='close' data-toggle='collapse' data-target='#consultar-produto' aria-expanded='false' aria-controls='consultar-produto'>&times;</button>
                            <h4>Consultar Produto</h4>
                            <hr/>
                            <strong>TO-DO:</strong> consultar produto form!
                        </div>
                    </div>
                </div>
            </div>
            <div class='row'>
                <div class='col'>
                    <div class='collapse' id='alterar-produto'>
                        <div class='container alert alert-warning  border-warning'>
                            <button type='button' class='close' data-toggle='collapse' data-target='#alterar-produto' aria-expanded='false' aria-controls='alterar-produto'>&times;</button>
                            <h4>Alterar Produto</h4>
                            <hr/>
                            <strong>TO-DO:</strong> alterar produto form!
                        </div>
                    </div>
                </div>
            </div>
            <div class='row'>
                <div class='col'>
                    <div class='collapse' id='remover-produto'>
                        <div class='container alert alert-danger  border-danger'>
                            <button type='button' class='close' data-toggle='collapse' data-target='#remover-produto' aria-expanded='false' aria-controls='remover-produto'>&times;</button>
                            <h4>Remover Produto</h4>
                            <hr/>
                            <strong>TO-DO:</strong> remover produto form!
                        </div>
                    </div>
                </div>
            </div>
            <div class='row'>
                <div class='col'>
                    <div class='collapse' id='criar-venda'>
                        <div class='container alert alert-success  border-success'>
                            <button type='button' class='close' data-toggle='collapse' data-target='#criar-venda' aria-expanded='false' aria-controls='criar-venda'>&times;</button>
                            <h4>Criar Venda</h4>
                            <hr/>
                            <strong>TO-DO:</strong> criar venda form!
                        </div>
                    </div>
                </div>
            </div>
            <div class='row'>
                <div class='col'>
                    <div class='collapse' id='consultar-venda'>
                        <div class='container alert alert-info  border-info'>
                            <button type='button' class='close' data-toggle='collapse' data-target='#consultar-venda' aria-expanded='false' aria-controls='consultar-venda'>&times;</button>
                            <h4>Consultar Venda</h4>
                            <hr/>
                            <strong>TO-DO:</strong> consultar venda form!
                        </div>
                    </div>
                </div>
            </div>
            <div class='row'>
                <div class='col'>
                    <div class='collapse' id='alterar-venda'>
                        <div class='container alert alert-warning  border-warning'>
                            <button type='button' class='close' data-toggle='collapse' data-target='#alterar-venda' aria-expanded='false' aria-controls='alterar-venda'>&times;</button>
                            <h4>Alterar Venda</h4>
                            <hr/>
                            <strong>TO-DO:</strong> alterar venda form!
                        </div>
                    </div>
                </div>
            </div>
            <div class='row'>
                <div class='col'>
                    <div class='collapse' id='remover-venda'>
                        <div class='container alert alert-danger  border-danger'>
                            <button type='button' class='close' data-toggle='collapse' data-target='#remover-venda' aria-expanded='false' aria-controls='remover-venda'>&times;</button>
                            <h4>Remover Venda</h4>
                            <hr/>
                            <strong>TO-DO:</strong> remover venda form!
                        </div>
                    </div>
                </div>
            </div>
        </div>
        ";
    }
}