<?php
require_once(__DIR__ . '/application/autoload.php');

// Definir rotas
$routes = [
    '' => 'Application/views/login.php',
    'login' => 'Application/controllers/login_controller.php',
    'logout' => 'Application/controllers/logout_controller.php',
    'dashboard' => 'Application/views/dashboard.php',
    'fornecedor/cadastro' => 'Application/views/fornecedor/fornecedor_create.php',
    'fornecedor/editar' => 'Application/views/fornecedor/fornecedor_edit.php',
    'fornecedor/visualizar' => 'Application/views/fornecedor/fornecedor_view.php',
    'fornecedor/atualizar' => 'Application/controllers/fornecedor_controller.php',
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
