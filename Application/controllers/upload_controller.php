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
    $file_extension = pathinfo($file, PATHINFO_EXTENSION);

    // Determina o caminho com base na extensão do arquivo
    if ($file_extension === 'xml') {
        $file_path = __DIR__ . '/../../uploads/xml/' . $file;
        $content_type = 'application/xml';
    } else {
        $file_path = __DIR__ . '/../../uploads/' . $file;
        $content_type = 'application/pdf';  // Tipo de conteúdo para PDF
    }

    // Verifica se o arquivo existe
    if (file_exists($file_path)) {
        header('Content-Description: File Transfer');
        header('Content-Type: ' . $content_type);
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
