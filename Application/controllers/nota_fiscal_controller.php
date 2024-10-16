<?php
session_start();
require('Application/models/nota_fiscal_dao.php');
require('Application/models/conexao.php');
require('Application/models/fornecedor_dao.php');

if (isset($_POST['criar_nota_fiscal'])) {
    $numero = mysqli_real_escape_string($conexao, $_POST['numero']);
    $data_emissao = mysqli_real_escape_string($conexao, $_POST['data_emissao']);
    $valor_total = mysqli_real_escape_string($conexao, str_replace(',', '.', str_replace('.', '', $_POST['valor_total'])));
    $parcelas = mysqli_real_escape_string($conexao, $_POST['parcelas']);
    $fk_fornecedores_id_fornecedor = mysqli_real_escape_string($conexao, $_POST['fornecedor']);

    $caminho_arquivo = null;
    if (isset($_FILES['arquivo_xml']) && $_FILES['arquivo_xml']['error'] == UPLOAD_ERR_OK) {
        $caminho_arquivo = 'uploads/xml/' . basename($_FILES['arquivo_xml']['name']);
        move_uploaded_file($_FILES['arquivo_xml']['tmp_name'], $caminho_arquivo);
    }

    if (empty($numero)) {
        $_SESSION['mensagem'] = 'O número da nota fiscal é obrigatório!';
        $_SESSION['mensagem_tipo'] = 'error';
        header('Location: /planel/nota-fiscal/cadastro');
        exit;
    }

    $result = NotaFiscalDAO::criarNotaFiscal($numero, $data_emissao, $valor_total, $parcelas, $fk_fornecedores_id_fornecedor, $caminho_arquivo);
    if ($result > 0) {
        $_SESSION['mensagem'] = 'Nota Fiscal criada com sucesso!';
        $_SESSION['mensagem_tipo'] = 'success';
    } else {
        $_SESSION['mensagem'] = 'Nota Fiscal não foi criada';
        $_SESSION['mensagem_tipo'] = 'error';
    }
    header('Location: /planel/notas-fiscais');
    exit();
}

if (isset($_POST['editar_nota_fiscal'])) {
    $id_nota_fiscal = mysqli_real_escape_string($conexao, $_POST['nota_fiscal_id']);
    $numero = mysqli_real_escape_string($conexao, $_POST['numero']);
    $data_emissao = mysqli_real_escape_string($conexao, $_POST['data_emissao']);
    $valor_total = mysqli_real_escape_string($conexao, $_POST['valor_total']);
    $parcelas = mysqli_real_escape_string($conexao, $_POST['parcelas']);
    $fk_fornecedores_id_fornecedor = mysqli_real_escape_string($conexao, $_POST['fornecedor']);

    $caminho_arquivo = null;
    if (isset($_FILES['arquivo_xml']) && $_FILES['arquivo_xml']['error'] == UPLOAD_ERR_OK) {
        $caminho_arquivo = 'uploads/xml/' . basename($_FILES['arquivo_xml']['name']);
        move_uploaded_file($_FILES['arquivo_xml']['tmp_name'], $caminho_arquivo);
    }

    if (empty($numero)) {
        $_SESSION['mensagem'] = 'O número da nota fiscal é obrigatório!';
        $_SESSION['mensagem_tipo'] = 'error';
        header('Location: /planel/nota-fiscal/editar?id=' . $id_nota_fiscal);
        exit();
    }

    $result = NotaFiscalDAO::editarNotaFiscal($id_nota_fiscal, $numero, $data_emissao, $valor_total, $parcelas, $fk_fornecedores_id_fornecedor, $caminho_arquivo);
    if ($result >= 0) {
        $_SESSION['mensagem'] = 'Nota Fiscal atualizada com sucesso!';
        $_SESSION['mensagem_tipo'] = 'success';
    } else {
        $_SESSION['mensagem'] = 'Nota Fiscal não foi atualizada';
        $_SESSION['mensagem_tipo'] = 'warning';
    }
    header('Location: /planel/notas-fiscais');
    exit();
}

if (isset($_POST['excluir_nota_fiscal'])) {
    $id_nota_fiscal = mysqli_real_escape_string($conexao, $_POST['excluir_nota_fiscal']);
    
    $result = NotaFiscalDAO::excluirNotaFiscal($id_nota_fiscal);
    if ($result > 0) {
        $_SESSION['mensagem'] = 'Nota Fiscal deletada com sucesso!';
        $_SESSION['mensagem_tipo'] = 'success';
    } else {
        $_SESSION['mensagem'] = 'Nota Fiscal não foi deletada';
        $_SESSION['mensagem_tipo'] = 'error';
    }
    header('Location: /planel/notas-fiscais');
    exit();
}
