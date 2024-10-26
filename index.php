<?php
session_start(); // Iniciar a sessão no início do arquivo
require('Application/autoload.php');

// Definir rotas públicas (que não exigem login)
$publicRoutes = ['', 'login', 'logout'];

// Obter o caminho da URL
$path = isset($_GET['url']) ? $_GET['url'] : '';

// Verificar se o usuário está logado ou se a rota é pública
if (!isset($_SESSION['login']) && !in_array($path, $publicRoutes)) {
    // Se o usuário não estiver logado e a rota não for pública, redirecionar para a página de login
    header('Location: /planel/?url=login'); // Ajustar a URL para evitar loop
    exit();
}

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
    'orcamento/gerar-pdf' => 'Application/views/orcamento/gerar_pdf_orcamento.php',
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

    'servicos' => 'Application/views/servico/servicos.php',
    'servico/atualizar' => 'Application/controllers/servico_controller.php',
    'servico/cadastro' => 'Application/views/servico/criar_servico.php',
    'servico/editar' => 'Application/views/servico/editar_servico.php',
    'servico/visualizar' => 'Application/views/servico/ver_servico.php',

    'xml' => 'Application/views/xml/xml.php',
    'xml/atualizar' => 'Application/controllers/xml_controller.php',
    'xml/excluir' => 'Application/controllers/xml_controller.php',

    'notas-fiscais' => 'Application/views/notas_fiscais/notas_fiscais.php',
    'nota-fiscal/atualizar' => 'Application/controllers/nota_fiscal_controller.php',
    'nota-fiscal/cadastro' => 'Application/views/notas_fiscais/criar_nota_fiscal.php',
    'nota-fiscal/editar' => 'Application/views/notas_fiscais/editar_nota_fiscal.php',
    'nota-fiscal/visualizar' => 'Application/views/notas_fiscais/ver_nota_fiscal.php',

    'contas-a-pagar' => 'Application/views/contas_pagar/contas_pagar.php',
    'conta-a-pagar/atualizar' => 'Application/controllers/contas_pagar_controller.php',
    'conta-a-pagar/cadastro' => 'Application/views/contas_pagar/criar_conta_pagar.php',
    'conta-a-pagar/editar' => 'Application/views/contas_pagar/editar_conta_pagar.php',
    'conta-a-pagar/visualizar' => 'Application/views/contas_pagar/ver_conta_pagar.php',

    'parcelas-pagar/atualizar' => 'Application/controllers/parcelas_pagar_controller.php',

    'multiplicador-lucro' => 'Application/controllers/multiplicador_controller.php',
];

// Verificar se a rota existe
if (array_key_exists($path, $routes)) {
    require_once(__DIR__ . '/' . $routes[$path]);
} else {
    // Rota não encontrada, redirecionar para a página de login ou inicial
    header('Location: /planel/');
    exit();
}
?>
