<?php
session_start();
require('Application/models/material_dao.php');
require('Application/models/conexao.php');

if (isset($_POST['criar_material'])) {
    $nome_material = mysqli_real_escape_string($conexao, $_POST['nome_material']);
    $valor_compra = mysqli_real_escape_string($conexao, str_replace(',', '.', str_replace('.', '', $_POST['valor_compra'])));
    $valor_venda = mysqli_real_escape_string($conexao, str_replace(',', '.', str_replace('.', '', $_POST['valor_venda'])));
    $data_compra = mysqli_real_escape_string($conexao, $_POST['data_compra']);
    $quantidade = mysqli_real_escape_string($conexao, $_POST['quantidade']);
    $unidade_medida = mysqli_real_escape_string($conexao, $_POST['unidade_medida']);
    $fk_fornecedor_id = !empty($_POST['fornecedor']) ? mysqli_real_escape_string($conexao, $_POST['fornecedor']) : NULL;
    $ncm = mysqli_real_escape_string($conexao, $_POST['ncm']);
    $fk_nota_fiscal_id = !empty($_POST['nota_fiscal']) ? mysqli_real_escape_string($conexao, $_POST['nota_fiscal']) : NULL;

    if (empty($nome_material)) {
        $_SESSION['mensagem'] = 'O nome do material é obrigatório!';
        $_SESSION['mensagem_tipo'] = 'error';
        header('Location: /planel/materiais');
        exit;
    }

    $result = MaterialDAO::criarMaterial($descricao_produto, $valor_compra, $valor_venda, $data_compra, $quantidade, $unidade_medida, $fornecedor_id, $ncm_produto, $id_nota_fiscal);
    if ($result > 0) {
        $_SESSION['mensagem'] = 'Material criado com sucesso!';
        $_SESSION['mensagem_tipo'] = 'success';
    } else {
        $_SESSION['mensagem'] = 'Material não foi criado';
        $_SESSION['mensagem_tipo'] = 'error';
    }
    header('Location: /planel/materiais');
    exit;
}

if (isset($_POST['editar_material'])) {
    $id = mysqli_real_escape_string($conexao, $_POST['material_id']);
    $nome_material = mysqli_real_escape_string($conexao, $_POST['nome_material']);
    $valor_compra = mysqli_real_escape_string($conexao, str_replace(',', '.', str_replace('.', '', $_POST['valor_compra'])));
    $valor_venda = mysqli_real_escape_string($conexao, str_replace(',', '.', str_replace('.', '', $_POST['valor_venda'])));
    $data_compra = mysqli_real_escape_string($conexao, $_POST['data_compra']);
    $quantidade = mysqli_real_escape_string($conexao, $_POST['quantidade']);
    $unidade_medida = mysqli_real_escape_string($conexao, $_POST['unidade_medida']);
    $fk_fornecedor_id = !empty($_POST['fornecedor']) ? mysqli_real_escape_string($conexao, $_POST['fornecedor']) : NULL;
    $ncm = mysqli_real_escape_string($conexao, $_POST['ncm']);
    $fk_nota_fiscal_id = !empty($_POST['nota_fiscal']) ? mysqli_real_escape_string($conexao, $_POST['nota_fiscal']) : NULL;

    if (empty($nome_material)) {
        $_SESSION['mensagem'] = 'O nome do material é obrigatório!';
        $_SESSION['mensagem_tipo'] = 'error';
        header('Location: /planel/materiais');
        exit;
    }

    $result = MaterialDAO::editarMaterial($id, $nome_material, $valor_compra, $valor_venda, $data_compra, $quantidade, $unidade_medida, $fk_fornecedor_id, $ncm, $fk_nota_fiscal_id);
    if ($result >= 0) {
        $_SESSION['mensagem'] = 'Material atualizado com sucesso!';
        $_SESSION['mensagem_tipo'] = 'success';
    } else {
        $_SESSION['mensagem'] = 'Material não foi atualizado';
        $_SESSION['mensagem_tipo'] = 'warning';
    }   
    header('Location: /planel/materiais');
    exit;
}

if (isset($_POST['excluir_material'])) {
    $id = mysqli_real_escape_string($conexao, $_POST['excluir_material']);
    
    $result = MaterialDAO::excluirMaterial($id);
    if ($result > 0) {
        $_SESSION['mensagem'] = 'Material deletado com sucesso!';
        $_SESSION['mensagem_tipo'] = 'success';
    } else {
        $_SESSION['mensagem'] = 'Material não foi deletado';
        $_SESSION['mensagem_tipo'] = 'error';
    }
    header('Location: /planel/materiais');
    exit;
}
?>
