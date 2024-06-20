<nav class="navbar navbar-dark bg-dark">
    <div class="container-md">
        <a class="navbar-brand" href="/planel/dashboard" style="font-weight: bold;">PLANEL</a>
        <a class="btn btn-outline-light" href="/planel/logout">Sair&nbsp;<span class="bi bi-box-arrow-right"></span></a>
    </div>
</nav>

<?php
    // Início da verificação de login
    if(isset($_SESSION['login'])) {
    ?>
    <?php
    } else {
        // Se não estiver logado, redirecionar para a página de login
        header('Location: /planel/');
        exit();
    }
    // Fim da verificação de login
    ?>
