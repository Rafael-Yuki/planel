<?php
session_start();
require_once(dirname(__DIR__) . '/models/fornecedor_dao.php');
require_once(dirname(__DIR__) . '/models/conexao.php');

if (isset($_POST['create_fornecedor'])) {
    $nome = mysqli_real_escape_string($conexao, $_POST['nome']);
    $cnpj = mysqli_real_escape_string($conexao, $_POST['cnpj']);
    $telefone = mysqli_real_escape_string($conexao, $_POST['telefone']);
    $email = mysqli_real_escape_string($conexao, $_POST['email']);
    $endereco = mysqli_real_escape_string($conexao, $_POST['endereco']);
    $cidade_id = mysqli_real_escape_string($conexao, $_POST['cidade']);
    $estado_id = mysqli_real_escape_string($conexao, $_POST['estado']);

    if (empty($nome)) {
        $_SESSION['mensagem'] = 'O nome do fornecedor é obrigatório!';
        $_SESSION['mensagem_tipo'] = 'error';
        header('Location: /planel/dashboard');
        exit;
    }

    $result = FornecedorDAO::criarFornecedor($nome, $cnpj, $telefone, $email, $endereco, $cidade_id, $estado_id);
    if ($result > 0) {
        $_SESSION['mensagem'] = 'Fornecedor criado com sucesso!';
        $_SESSION['mensagem_tipo'] = 'success';
    } else {
        $_SESSION['mensagem'] = 'Fornecedor não foi criado';
        $_SESSION['mensagem_tipo'] = 'error';
    }
    header('Location: /planel/dashboard');
    exit;
}

if (isset($_POST['update_fornecedor'])) {
    $id = mysqli_real_escape_string($conexao, $_POST['fornecedor_id']);
    $nome = mysqli_real_escape_string($conexao, $_POST['nome']);
    $cnpj = mysqli_real_escape_string($conexao, $_POST['cnpj']);
    $telefone = mysqli_real_escape_string($conexao, $_POST['telefone']);
    $email = mysqli_real_escape_string($conexao, $_POST['email']);
    $endereco = mysqli_real_escape_string($conexao, $_POST['endereco']);
    $cidade_id = mysqli_real_escape_string($conexao, $_POST['cidade']);
    $estado_id = mysqli_real_escape_string($conexao, $_POST['estado']);

    if (empty($nome)) {
        $_SESSION['mensagem'] = 'O nome do fornecedor é obrigatório!';
        $_SESSION['mensagem_tipo'] = 'error';
        header('Location: /planel/dashboard');
        exit;
    }

    $result = FornecedorDAO::editarFornecedor($id, $nome, $cnpj, $telefone, $email, $endereco, $cidade_id, $estado_id);
    if ($result >= 0) {
        $_SESSION['mensagem'] = 'Fornecedor atualizado com sucesso!';
        $_SESSION['mensagem_tipo'] = 'success';
    } else {
        $_SESSION['mensagem'] = 'Fornecedor não foi atualizado';
        $_SESSION['mensagem_tipo'] = 'warning';
    }   
    header('Location: /planel/dashboard');
    exit;
}

if (isset($_POST['delete_fornecedor'])) {
    $id = mysqli_real_escape_string($conexao, $_POST['delete_fornecedor']);
    
    $result = FornecedorDAO::deletarFornecedor($id);
    if ($result > 0) {
        $_SESSION['mensagem'] = 'Fornecedor deletado com sucesso!';
        $_SESSION['mensagem_tipo'] = 'success';
    } else {
        $_SESSION['mensagem'] = 'Fornecedor não foi deletado';
        $_SESSION['mensagem_tipo'] = 'error';
    }
    header('Location: /planel/dashboard');
    exit;
}
?>
