<?php
session_start();
require('Application/models/cliente_dao.php');
require('Application/models/conexao.php');

if (isset($_POST['criar_cliente'])) {
    $nome = mysqli_real_escape_string($conexao, $_POST['nome']);
    $cnpj = mysqli_real_escape_string($conexao, $_POST['cnpj']);
    $telefone = mysqli_real_escape_string($conexao, $_POST['telefone']);
    $email = mysqli_real_escape_string($conexao, $_POST['email']);
    $endereco = mysqli_real_escape_string($conexao, $_POST['endereco']);
    $cidade_id = mysqli_real_escape_string($conexao, $_POST['cidade']);
    $estado_id = mysqli_real_escape_string($conexao, $_POST['estado']);

    if (empty($nome)) {
        $_SESSION['mensagem'] = 'O nome do cliente é obrigatório!';
        $_SESSION['mensagem_tipo'] = 'error';
        header('Location: /planel/clientes');
        exit;
    }

    $result = clienteDAO::criarCliente($nome, $cnpj, $telefone, $email, $endereco, $cidade_id, $estado_id);
    if ($result > 0) {
        $_SESSION['mensagem'] = 'Cliente criado com sucesso!';
        $_SESSION['mensagem_tipo'] = 'success';
    } else {
        $_SESSION['mensagem'] = 'Cliente não foi criado';
        $_SESSION['mensagem_tipo'] = 'error';
    }
    header('Location: /planel/clientes');
    exit;
}

if (isset($_POST['editar_cliente'])) {
    $id = mysqli_real_escape_string($conexao, $_POST['cliente_id']);
    $nome = mysqli_real_escape_string($conexao, $_POST['nome']);
    $cnpj = mysqli_real_escape_string($conexao, $_POST['cnpj']);
    $telefone = mysqli_real_escape_string($conexao, $_POST['telefone']);
    $email = mysqli_real_escape_string($conexao, $_POST['email']);
    $endereco = mysqli_real_escape_string($conexao, $_POST['endereco']);
    $cidade_id = mysqli_real_escape_string($conexao, $_POST['cidade']);
    $estado_id = mysqli_real_escape_string($conexao, $_POST['estado']);

    if (empty($nome)) {
        $_SESSION['mensagem'] = 'O nome do cliente é obrigatório!';
        $_SESSION['mensagem_tipo'] = 'error';
        header('Location: /planel/clientes');
        exit;
    }

    $result = clienteDAO::editarCliente($id, $nome, $cnpj, $telefone, $email, $endereco, $cidade_id, $estado_id);
    if ($result >= 0) {
        $_SESSION['mensagem'] = 'Cliente atualizado com sucesso!';
        $_SESSION['mensagem_tipo'] = 'success';
    } else {
        $_SESSION['mensagem'] = 'Cliente não foi atualizado';
        $_SESSION['mensagem_tipo'] = 'warning';
    }   
    header('Location: /planel/clientes');
    exit;
}

if (isset($_POST['excluir_cliente'])) {
    $id = mysqli_real_escape_string($conexao, $_POST['excluir_cliente']);
    
    $result = clienteDAO::excluirCliente($id);
    if ($result > 0) {
        $_SESSION['mensagem'] = 'Cliente deletado com sucesso!';
        $_SESSION['mensagem_tipo'] = 'success';
    } else {
        $_SESSION['mensagem'] = 'Cliente não foi deletado';
        $_SESSION['mensagem_tipo'] = 'error';
    }
    header('Location: /planel/clientes');
    exit;
}
?>