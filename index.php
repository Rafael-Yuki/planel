<?php
require('Application/autoload.php');

// Definir rotas
$routes = [
    '' => 'Application/views/login.php',
    'login' => 'Application/controllers/login_controller.php',
    'logout' => 'Application/controllers/logout_controller.php',
    'dashboard' => 'Application/views/dashboard.php',

    'fornecedores' => 'Application/views/fornecedor/fornecedores.php',
    'fornecedor/atualizar' => 'Application/controllers/fornecedor_controller.php',
    'fornecedor/cadastro' => 'Application/views/fornecedor/criar_fornecedor.php',
    'fornecedor/editar' => 'Application/views/fornecedor/editar_fornecedor.php',
    'fornecedor/visualizar' => 'Application/views/fornecedor/ver_fornecedor.php',
    'cidades' => 'Application/controllers/cidades_controller.php',

    'clientes' => 'Application/views/cliente/clientes.php',
    'cliente/atualizar' => 'Application/controllers/cliente_controller.php',
    'cliente/cadastro' => 'Application/views/cliente/criar_cliente.php',
    'cliente/visualizar' => 'Application/views/cliente/ver_cliente.php',

    'materiais' => 'Application/views/materiais.php',
    'contas-a-pagar' => 'Application/views/contas_a_pagar.php',
    'contas-a-receber' => 'Application/views/contas_a_receber.php',
    'notas-fiscais' => 'Application/views/notas_fiscais.php',
    'orcamentos' => 'Application/views/orcamentos.php',
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
