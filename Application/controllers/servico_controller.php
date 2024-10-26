<?php
session_start();
require('Application/models/servico_dao.php');
require('Application/models/conexao.php');

if (isset($_POST['criar_servico'])) {
    $nome_servico = mysqli_real_escape_string($conexao, $_POST['nome_servico']);
    $valor_servico = str_replace(['.', ','], ['', '.'], $_POST['valor_servico']);
    $valor_servico = number_format((float)$valor_servico, 2, '.', '');

    $descricao_servico = mysqli_real_escape_string($conexao, $_POST['descricao_servico']);

    if (empty($nome_servico)) {
        $_SESSION['mensagem'] = 'O nome do serviço é obrigatório!';
        $_SESSION['mensagem_tipo'] = 'error';
        header('Location: /planel/servicos');
        exit;
    }

    $result = ServicoDAO::criarServico($nome_servico, $valor_servico, $descricao_servico);
    if ($result > 0) {
        $_SESSION['mensagem'] = 'Serviço criado com sucesso!';
        $_SESSION['mensagem_tipo'] = 'success';
    } else {
        $_SESSION['mensagem'] = 'Serviço não foi criado';
        $_SESSION['mensagem_tipo'] = 'error';
    }
    header('Location: /planel/servicos');
    exit;
}

if (isset($_POST['editar_servico'])) {
    $id = mysqli_real_escape_string($conexao, $_POST['servico_id']);
    $nome_servico = mysqli_real_escape_string($conexao, $_POST['nome_servico']);
    $valor_servico = str_replace(['.', ','], ['', '.'], $_POST['valor_servico']); 
    $valor_servico = number_format((float)$valor_servico, 2, '.', ''); 

    $descricao_servico = mysqli_real_escape_string($conexao, $_POST['descricao_servico']);

    if (empty($nome_servico)) {
        $_SESSION['mensagem'] = 'O nome do serviço é obrigatório!';
        $_SESSION['mensagem_tipo'] = 'error';
        header('Location: /planel/servicos');
        exit;
    }

    $result = ServicoDAO::editarServico($id, $nome_servico, $valor_servico, $descricao_servico);
    if ($result >= 0) {
        $_SESSION['mensagem'] = 'Serviço atualizado com sucesso!';
        $_SESSION['mensagem_tipo'] = 'success';
    } else {
        $_SESSION['mensagem'] = 'Serviço não foi atualizado';
        $_SESSION['mensagem_tipo'] = 'warning';
    }   
    header('Location: /planel/servicos');
    exit;
}

if (isset($_POST['excluir_servico'])) {
    $id = mysqli_real_escape_string($conexao, $_POST['excluir_servico']);
    
    $result = ServicoDAO::excluirServico($id);
    if ($result > 0) {
        $_SESSION['mensagem'] = 'Serviço deletado com sucesso!';
        $_SESSION['mensagem_tipo'] = 'success';
    } else {
        $_SESSION['mensagem'] = 'Serviço não foi deletado';
        $_SESSION['mensagem_tipo'] = 'error';
    }
    header('Location: /planel/servicos');
    exit;
}
?>
