<?php
require('Application/autoload.php');

// Definir rotas
$routes = [
    '' => 'Application/views/login.php',
    'login' => 'Application/controllers/login_controller.php',
    'logout' => 'Application/controllers/logout_controller.php',
    'dashboard' => 'Application/views/dashboard.php',
    'cidades' => 'Application/controllers/cidades_controller.php',

    'fornecedores' => 'Application/views/fornecedor/fornecedores.php',
    'fornecedor/atualizar' => 'Application/controllers/fornecedor_controller.php',
    'fornecedor/cadastro' => 'Application/views/fornecedor/criar_fornecedor.php',
    'fornecedor/editar' => 'Application/views/fornecedor/editar_fornecedor.php',
    'fornecedor/visualizar' => 'Application/views/fornecedor/ver_fornecedor.php',

    'clientes' => 'Application/views/cliente/clientes.php',
    'cliente/atualizar' => 'Application/controllers/cliente_controller.php',
    'cliente/cadastro' => 'Application/views/cliente/criar_cliente.php',
    'cliente/editar' => 'Application/views/cliente/editar_cliente.php',
    'cliente/visualizar' => 'Application/views/cliente/ver_cliente.php',

    'orcamentos' => 'Application/views/orcamento/orcamentos.php',
    'orcamento/atualizar' => 'Application/controllers/orcamento_controller.php',
    'orcamento/cadastro' => 'Application/views/orcamento/criar_orcamento.php',
    'orcamento/editar' => 'Application/views/orcamento/editar_orcamento.php',
    'orcamento/visualizar' => 'Application/views/orcamento/ver_orcamento.php',
    'upload' => 'Application/controllers/upload_controller.php',

    'contas-a-receber' => 'Application/views/contas_receber/contas_receber.php',
    'conta-a-receber/atualizar' => 'Application/controllers/contas_receber_controller.php',
    'conta-a-receber/cadastro' => 'Application/views/contas_receber/criar_conta_receber.php',
    'conta-a-receber/editar' => 'Application/views/contas_receber/editar_conta_receber.php',
    'conta-a-receber/visualizar' => 'Application/views/contas_receber/ver_conta_receber.php',

    'parcelas-receber/atualizar' => 'Application/controllers/parcelas_receber_controller.php',

    'materiais' => 'Application/views/material/materiais.php',
    'material/atualizar' => 'Application/controllers/material_controller.php',
    'material/cadastro' => 'Application/views/material/criar_material.php',
    'material/editar' => 'Application/views/material/editar_material.php',
    'material/visualizar' => 'Application/views/material/ver_material.php',

    'contas-a-pagar' => 'Application/views/contas_a_pagar.php',
    'notas-fiscais' => 'Application/views/notas_fiscais.php',
];

// Obter o caminho da URL
$path = isset($_GET['url']) ? $_GET['url'] : '';

// Verificar se a rota existe
if (array_key_exists($path, $routes)) {
    require_once(__DIR__ . '/' . $routes[$path]);
} else {
    // Rota não encontrada, redirecionar para página de erro ou página inicial
    header('Location: /');
    exit;
}
?>
