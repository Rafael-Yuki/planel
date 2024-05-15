<?php
session_start();
require '../model/fornecedor_dao.php';

if (isset($_POST['create_fornecedor'])) {
    $nome = $_POST['nome'];
    $cnpj = $_POST['cnpj'];
    $telefone = $_POST['telefone'];
    $email = $_POST['email'];
    $endereco = $_POST['endereco'];

    $result = FornecedorDAO::criarFornecedor($nome, $cnpj, $telefone, $email, $endereco);
    if ($result > 0) {
        $_SESSION['mensagem'] = 'Fornecedor criado com sucesso!';
    } else {
        $_SESSION['mensagem'] = 'Fornecedor não foi criado';
    }
    header('Location: ../view/dashboard.php');
    exit;
}

if (isset($_POST['update_fornecedor'])) {
    $id = $_POST['fornecedor_id'];
    $nome = $_POST['nome'];
    $cnpj = $_POST['cnpj'];
    $telefone = $_POST['telefone'];
    $email = $_POST['email'];
    $endereco = $_POST['endereco'];

    $result = FornecedorDAO::editarFornecedor($id, $nome, $cnpj, $telefone, $email, $endereco);
    if ($result > 0) {
        $_SESSION['mensagem'] = 'Fornecedor atualizado com sucesso!';
    } else {
        $_SESSION['mensagem'] = 'Fornecedor não foi atualizado';
    }
    header('Location: ../view/dashboard.php');
    exit;
}

if (isset($_POST['delete_fornecedor'])) {
    $id = $_POST['delete_fornecedor'];
    
    $result = FornecedorDAO::deletarFornecedor($id);
    if ($result > 0) {
        $_SESSION['mensagem'] = 'Fornecedor deletado com sucesso!';
    } else {
        $_SESSION['mensagem'] = 'Fornecedor não foi marcado como inativo';
    }
    header('Location: ../view/dashboard.php');
    exit;
}
?>