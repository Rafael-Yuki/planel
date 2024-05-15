<?php
session_start();
require '../model/conexao.php';

if (isset($_POST['create_fornecedor'])) {
    $nome = mysqli_real_escape_string($conexao, trim($_POST['nome']));
    $cnpj = mysqli_real_escape_string($conexao, trim($_POST['cnpj']));
    $telefone = mysqli_real_escape_string($conexao, trim($_POST['telefone']));
    $email = mysqli_real_escape_string($conexao, trim($_POST['email']));
    $endereco = mysqli_real_escape_string($conexao, trim($_POST['endereco']));

    $sql = "INSERT INTO fornecedores (nome_fornecedor, telefone, endereco, email, cnpj) VALUES ('$nome', '$telefone', '$endereco', '$email', '$cnpj')";

    mysqli_query($conexao, $sql);
    if (mysqli_affected_rows($conexao) > 0) {
        $_SESSION['mensagem'] = 'Fornecedor criado com sucesso!';
        header('Location: ../view/dashboard.php');
        exit;
    } else {
        $_SESSION['mensagem'] = 'Fornecedor não foi criado';
        header('Location: ../view/dashboard.php');
        exit;
    }
}

if (isset($_POST['update_fornecedor'])) {
    $fornecedor_id = mysqli_real_escape_string($conexao, $_POST['fornecedor_id']);
    $nome = mysqli_real_escape_string($conexao, trim($_POST['nome']));
    $cnpj = mysqli_real_escape_string($conexao, trim($_POST['cnpj']));
    $telefone = mysqli_real_escape_string($conexao, trim($_POST['telefone']));
    $email = mysqli_real_escape_string($conexao, trim($_POST['email']));
    $endereco = mysqli_real_escape_string($conexao, trim($_POST['endereco']));

    $sql = "UPDATE fornecedores SET nome_fornecedor = '$nome', telefone = '$telefone', endereco = '$endereco', email = '$email', cnpj = '$cnpj' WHERE id_fornecedor = '$fornecedor_id'";

    mysqli_query($conexao, $sql);
    if (mysqli_affected_rows($conexao) > 0) {
        $_SESSION['mensagem'] = 'Fornecedor atualizado com sucesso!';
        header('Location: ../view/dashboard.php');
        exit;
    } else {
        $_SESSION['mensagem'] = 'Fornecedor não foi atualizado';
        header('Location: ../view/dashboard.php');
        exit;
    }
}

if (isset($_POST['delete_fornecedor'])) {
    $fornecedor_id = mysqli_real_escape_string($conexao, $_POST['delete_fornecedor']);
    
    $sql = "UPDATE fornecedores SET ativo = FALSE WHERE id_fornecedor = '$fornecedor_id'";
    
    mysqli_query($conexao, $sql);
    if (mysqli_affected_rows($conexao) > 0) {
        $_SESSION['mensagem'] = 'Fornecedor deletado com sucesso!';
        header('Location: ../view/dashboard.php');
        exit;
    } else {
        $_SESSION['mensagem'] = 'Fornecedor não foi marcado como inativo';
        header('Location: ../view/dashboard.php');
        exit;
    }
}
?>