<?php
function isActive($route) {
    $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    return $currentPath === $route ? 'active' : '';
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
                <a class="btn btn-outline-light me-2" href="/planel/logout">Sair&nbsp;<span class="bi bi-box-arrow-right"></span></a>
                <button class="navbar-toggler ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>
            <div class="collapse navbar-collapse order-lg-0" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item me-4">
                        <a class="nav-link text-light <?php echo isActive('/planel/fornecedores'); ?>" href="/planel/fornecedores">Fornecedores</a>
                    </li>
                    <li class="nav-item me-4">
                        <a class="nav-link text-light <?php echo isActive('/planel/clientes'); ?>" href="/planel/clientes">Clientes</a>
                    </li>
                    <li class="nav-item me-4">
                        <a class="nav-link text-light <?php echo isActive('/planel/materiais'); ?>" href="/planel/materiais">Materiais</a>
                    </li>
                    <li class="nav-item me-4">
                        <a class="nav-link text-light <?php echo isActive('/planel/contas-a-pagar'); ?>" href="/planel/contas-a-pagar">Contas a Pagar</a>
                    </li>
                    <li class="nav-item me-4">
                        <a class="nav-link text-light <?php echo isActive('/planel/contas-a-receber'); ?>" href="/planel/contas-a-receber">Contas a Receber</a>
                    </li>
                    <li class="nav-item me-4">
                        <a class="nav-link text-light <?php echo isActive('/planel/notas-fiscais'); ?>" href="/planel/notas-fiscais">Notas fiscais</a>
                    </li>
                    <li class="nav-item me-4">
                        <a class="nav-link text-light <?php echo isActive('/planel/orcamentos'); ?>" href="/planel/orcamentos">Orçamentos</a>
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
        document.getElementById('themeToggleBtn').addEventListener('click', function() {
            const body = document.body;
            const themeIcon = document.getElementById('themeIcon');
            const currentTheme = body.getAttribute('data-bs-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            body.setAttribute('data-bs-theme', newTheme);
            themeIcon.classList.toggle('bi-sun');
            themeIcon.classList.toggle('bi-moon');
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
