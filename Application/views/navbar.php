<?php
function isActive($routes) {
    $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    foreach ($routes as $route) {
        if (strpos($currentPath, $route) !== false) {
            return 'active';
        }
    }
    
    return '';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PLANEL</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/5.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.5.0/font/bootstrap-icons.min.css">
</head>
<body data-bs-theme="dark">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid px-5">
            <a class="navbar-brand" href="/planel/dashboard" style="font-weight: bold;">PLANEL</a>
            <div class="d-flex ms-auto order-lg-1">
                <button id="themeToggleBtn" class="btn btn-outline-light me-2">
                    <i id="themeIcon" class="bi bi-moon"></i>
                </button>
                <a class="btn btn-outline-light me-2" href="/planel/logout">Sair<span class="bi bi-box-arrow-right ms-2"></span></a>
                <button class="navbar-toggler ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>
            <div class="collapse navbar-collapse order-lg-0" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item me-4">
                        <a class="nav-link text-light <?php echo isActive(['/planel/fornecedores', '/planel/fornecedor/cadastro', '/planel/fornecedor/editar', '/planel/fornecedor/visualizar']); ?>" href="/planel/fornecedores">Fornecedores</a>
                    </li>
                    <li class="nav-item me-4">
                        <a class="nav-link text-light <?php echo isActive(['/planel/clientes', '/planel/cliente/cadastro', '/planel/cliente/editar', '/planel/cliente/visualizar']); ?>" href="/planel/clientes">Clientes</a>
                    </li>
                    <li class="nav-item me-4">
                        <a class="nav-link text-light <?php echo isActive(['/planel/contas-a-pagar', '/planel/conta-a-pagar/cadastro', '/planel/conta-a-pagar/editar', '/planel/conta-a-pagar/visualizar']); ?>" href="/planel/contas-a-pagar">Contas a Pagar</a>
                    </li>
                    <li class="nav-item me-4">
                        <a class="nav-link text-light <?php echo isActive(['/planel/contas-a-receber', '/planel/conta-a-receber/cadastro', '/planel/conta-a-receber/editar', '/planel/conta-a-receber/visualizar']); ?>" href="/planel/contas-a-receber">Contas a Receber</a>
                    </li>
                    <li class="nav-item me-4">
                        <a class="nav-link text-light <?php echo isActive(['/planel/materiais', '/planel/material/cadastro', '/planel/material/editar', '/planel/material/visualizar']); ?>" href="/planel/materiais">Materiais</a>
                    </li>
                    <li class="nav-item me-4">
                        <a class="nav-link text-light <?php echo isActive(['/planel/servicos', '/planel/servico/cadastro', '/planel/servico/editar', '/planel/servico/visualizar']); ?>" href="/planel/servicos">Serviços</a>
                    </li>
                    <li class="nav-item me-4">
                        <a class="nav-link text-light <?php echo isActive(['/planel/orcamentos', '/planel/orcamento/cadastro', '/planel/orcamento/editar', '/planel/orcamento/visualizar']); ?>" href="/planel/orcamentos">Orçamentos</a>
                    </li>
                    <li class="nav-item me-4">
                        <a class="nav-link text-light <?php echo isActive(['/planel/notas-fiscais', '/planel/nota-fiscal/cadastro', '/planel/nota-fiscal/editar', '/planel/nota-fiscal/visualizar']); ?>" href="/planel/notas-fiscais">Notas Fiscais</a>
                    </li>
                    <li class="nav-item me-4">
                        <a class="nav-link text-light <?php echo isActive(['/planel/xml']); ?>" href="/planel/xml">XML</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <style>
        .nav-link:hover, .nav-link.active {
            text-decoration: underline;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const body = document.body;
            const themeToggleBtn = document.getElementById('themeToggleBtn');
            const themeIcon = document.getElementById('themeIcon');

            // Verificar o tema salvo no localStorage ao carregar a página
            const savedTheme = localStorage.getItem('theme') || 'dark';
            body.setAttribute('data-bs-theme', savedTheme);

            if (savedTheme === 'light') {
                themeIcon.classList.remove('bi-moon');
                themeIcon.classList.add('bi-sun');
            } else {
                themeIcon.classList.remove('bi-sun');
                themeIcon.classList.add('bi-moon');
            }

            // Alternar o tema ao clicar no botão e salvar no localStorage
            themeToggleBtn.addEventListener('click', function() {
                const currentTheme = body.getAttribute('data-bs-theme');
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

                body.setAttribute('data-bs-theme', newTheme);
                localStorage.setItem('theme', newTheme);

                themeIcon.classList.toggle('bi-sun');
                themeIcon.classList.toggle('bi-moon');
            });
        });
    </script>

    <?php
    // Início da verificação de login
    if (isset($_SESSION['login'])) {
        // Conteúdo protegido aqui
    } else {
        // Se não estiver logado, redirecionar para a página de login
        header('Location: /planel/');
        exit();
    }
    // Fim da verificação de login
    ?>
</body>
</html>
