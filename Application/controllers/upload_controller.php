<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['login'])) {
    // Se não estiver logado, redireciona para a página de login
    header("Location: /planel/");
    exit();
}

// Verifica se o arquivo foi especificado na URL
if (isset($_GET['file'])) {
    $file = basename($_GET['file']);  // Protege contra ataques de path traversal
    $file_path = __DIR__ . '/../../uploads/' . $file;

    // Verifica se o arquivo existe
    if (file_exists($file_path)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/pdf'); // Você pode ajustar o tipo conforme necessário
        header('Content-Disposition: inline; filename="'.basename($file_path).'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file_path));

        readfile($file_path);
        exit();
    } else {
        echo "Arquivo não encontrado.";
        exit();
    }
} else {
    echo "Nenhum arquivo especificado.";
    exit();
}
?>
