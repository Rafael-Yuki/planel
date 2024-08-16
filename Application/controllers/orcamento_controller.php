<?php
session_start();
require('Application/models/orcamento_dao.php');
require('Application/models/conexao.php');

if (isset($_POST['criar_orcamento'])) {
    $nome_orcamento = mysqli_real_escape_string($conexao, $_POST['nome_orcamento']);
    $data_orcamento = mysqli_real_escape_string($conexao, $_POST['data_orcamento']);
    $validade = mysqli_real_escape_string($conexao, $_POST['validade']);
    $status = mysqli_real_escape_string($conexao, $_POST['status']);
    $observacao = mysqli_real_escape_string($conexao, $_POST['observacao']);
    $fk_clientes_id_cliente = mysqli_real_escape_string($conexao, $_POST['cliente']);

    $caminho_arquivo = null;
    if (isset($_FILES['arquivo_pdf']) && $_FILES['arquivo_pdf']['error'] == UPLOAD_ERR_OK) {
        $caminho_arquivo = 'uploads/' . basename($_FILES['arquivo_pdf']['name']);
        move_uploaded_file($_FILES['arquivo_pdf']['tmp_name'], $caminho_arquivo);
    }

    if (empty($nome_orcamento)) {
        $_SESSION['mensagem'] = 'O nome do orçamento é obrigatório!';
        $_SESSION['mensagem_tipo'] = 'error';
        header('Location: /planel/orcamento/cadastro');
        exit;
    }

    $result = OrcamentoDAO::criarOrcamento($nome_orcamento, $data_orcamento, $validade, $status, $observacao, $fk_clientes_id_cliente, $caminho_arquivo);
    if ($result > 0) {
        $_SESSION['mensagem'] = 'Orçamento criado com sucesso!';
        $_SESSION['mensagem_tipo'] = 'success';
    } else {
        $_SESSION['mensagem'] = 'Orçamento não foi criado';
        $_SESSION['mensagem_tipo'] = 'error';
    }
    header('Location: /planel/orcamentos');
    exit();
}

if (isset($_POST['editar_orcamento'])) {
    $id_orcamento = mysqli_real_escape_string($conexao, $_POST['orcamento_id']);
    $nome_orcamento = mysqli_real_escape_string($conexao, $_POST['nome_orcamento']);
    $data_orcamento = mysqli_real_escape_string($conexao, $_POST['data_orcamento']);
    $validade = mysqli_real_escape_string($conexao, $_POST['validade']);
    $status = mysqli_real_escape_string($conexao, $_POST['status']);
    $observacao = mysqli_real_escape_string($conexao, $_POST['observacao']);
    $fk_clientes_id_cliente = mysqli_real_escape_string($conexao, $_POST['cliente']);

    $caminho_arquivo = null;
    if (isset($_FILES['arquivo_pdf']) && $_FILES['arquivo_pdf']['error'] == UPLOAD_ERR_OK) {
        $caminho_arquivo = 'uploads/' . basename($_FILES['arquivo_pdf']['name']);
        move_uploaded_file($_FILES['arquivo_pdf']['tmp_name'], $caminho_arquivo);
    }

    if (empty($nome_orcamento)) {
        $_SESSION['mensagem'] = 'O nome do orçamento é obrigatório!';
        $_SESSION['mensagem_tipo'] = 'error';
        header('Location: /planel/orcamento/editar?id=' . $id_orcamento);
        exit();
    }

    $result = OrcamentoDAO::editarOrcamento($id_orcamento, $nome_orcamento, $data_orcamento, $validade, $status, $observacao, $fk_clientes_id_cliente, $caminho_arquivo);
    if ($result >= 0) {
        $_SESSION['mensagem'] = 'Orçamento atualizado com sucesso!';
        $_SESSION['mensagem_tipo'] = 'success';
    } else {
        $_SESSION['mensagem'] = 'Orçamento não foi atualizado';
        $_SESSION['mensagem_tipo'] = 'warning';
    }
    header('Location: /planel/orcamentos');
    exit();
}

if (isset($_POST['excluir_orcamento'])) {
    $id_orcamento = mysqli_real_escape_string($conexao, $_POST['excluir_orcamento']);
    
    $result = OrcamentoDAO::excluirOrcamento($id_orcamento);
    if ($result > 0) {
        $_SESSION['mensagem'] = 'Orçamento deletado com sucesso!';
        $_SESSION['mensagem_tipo'] = 'success';
    } else {
        $_SESSION['mensagem'] = 'Orçamento não foi deletado';
        $_SESSION['mensagem_tipo'] = 'error';
    }
    header('Location: /planel/orcamentos');
    exit();
}
?>
