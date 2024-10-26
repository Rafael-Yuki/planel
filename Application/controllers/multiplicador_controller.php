<?php
session_start();
require_once 'Application/models/conexao.php'; 

// Função para atualizar o multiplicador de lucro
function atualizarMultiplicador($novo_multiplicador) {
    $caminhoArquivo = 'Application/config/multiplicador.txt'; 
    // Armazena o novo multiplicador no arquivo
    file_put_contents($caminhoArquivo, $novo_multiplicador);
    return true;
}

// Função para obter o multiplicador de lucro
function obterMultiplicador() {
    $caminhoArquivo = 'Application/config/multiplicador.txt'; 

    if (file_exists($caminhoArquivo)) {
        $conteudo = file_get_contents($caminhoArquivo);
        $valor = floatval(trim($conteudo));
        
        if ($valor > 0) {
            return $valor; // Retorna o valor se for válido
        }
    }
    return 1.00; // Retorna um valor padrão se o arquivo não existe ou o valor não é válido
}

// Verifica se a requisição é para atualizar o multiplicador
if (isset($_POST['novo_multiplicador'])) {
    $novo_multiplicador = $_POST['novo_multiplicador'];
    if (atualizarMultiplicador($novo_multiplicador)) {
        $_SESSION['mensagem'] = 'Multiplicador atualizado com sucesso!';
        $_SESSION['mensagem_tipo'] = 'success';
    } else {
        $_SESSION['mensagem'] = 'Erro ao atualizar o multiplicador.';
        $_SESSION['mensagem_tipo'] = 'error';
    }
    header('Location: /planel/dashboard');
    exit();
}
?>
