<?php
session_start();
require('Application/models/orcamento_dao.php');
require('Application/models/material_dao.php');
require('Application/models/servico_dao.php');
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

    // Iniciar uma transação para garantir que todas as operações sejam atômicas
    mysqli_begin_transaction($conexao);

    try {
        // Criar o orçamento
        $result = OrcamentoDAO::criarOrcamento($nome_orcamento, $data_orcamento, $validade, $status, $observacao, $fk_clientes_id_cliente, $caminho_arquivo);
        if ($result > 0) {
            $orcamento_id = mysqli_insert_id($conexao); // Obter o ID do orçamento criado

            // Adicionar materiais ao orçamento
            if (isset($_POST['nome_material'])) {
                foreach ($_POST['nome_material'] as $index => $id_material) {
                    $quantidade = $_POST['quantidade'][$index];
                    $preco = $_POST['preco'][$index];

                    // Formatar corretamente os valores de quantidade e preço
                    $quantidade = number_format($quantidade, 0, ',', '.'); // Quantidade sem decimais
                    $preco = number_format($preco, 2, ',', '.'); // Preço com 2 casas decimais

                    // Adicionar material ao orçamento com o ID correto
                    MaterialDAO::adicionarMaterialOrcamento($orcamento_id, $id_material, $quantidade, $preco);
                }
            }

            // Adicionar serviços ao orçamento
            if (isset($_POST['nome_servico'])) {
                foreach ($_POST['nome_servico'] as $index => $id_servico) {
                    $quantidade = $_POST['quantidade_servico'][$index];
                    $preco = $_POST['preco_servico'][$index];

                    // Formatar corretamente os valores de quantidade e preço
                    $quantidade = number_format($quantidade, 0, ',', '.'); // Quantidade sem decimais
                    $preco = number_format($preco, 2, ',', '.'); // Preço com 2 casas decimais

                    // Adicionar serviço ao orçamento com o ID correto
                    ServicoDAO::adicionarServicoOrcamento($orcamento_id, $id_servico, $quantidade, $preco);
                }
            }

            // Commitar a transação
            mysqli_commit($conexao);

            $_SESSION['mensagem'] = 'Orçamento criado com sucesso!';
            $_SESSION['mensagem_tipo'] = 'success';
        } else {
            throw new Exception('Erro ao criar o orçamento');
        }
    } catch (Exception $e) {
        // Em caso de erro, reverter a transação
        mysqli_rollback($conexao);
        $_SESSION['mensagem'] = 'Erro: ' . $e->getMessage();
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

    // Editar materiais
    if (isset($_POST['id_orcamento_material'])) {
        foreach ($_POST['id_orcamento_material'] as $index => $id_orcamento_material) {
            $id_material = $_POST['nome_material'][$index];
            $quantidade = $_POST['quantidade'][$index];
            $preco = $_POST['preco'][$index];

            // Atualizar material no orçamento
            MaterialDAO::editarMaterialOrcamento($id_orcamento_material, $id_material, $quantidade, $preco);
        }
    }

    // Editar serviços
    if (isset($_POST['id_orcamento_servico'])) {
        foreach ($_POST['id_orcamento_servico'] as $index => $id_orcamento_servico) {
            $id_servico = $_POST['nome_servico'][$index];
            $quantidade_servico = $_POST['quantidade_servico'][$index];
            $preco_servico = $_POST['preco_servico'][$index];

            // Atualizar serviço no orçamento
            ServicoDAO::editarServicoOrcamento($id_orcamento_servico, $id_servico, $quantidade_servico, $preco_servico);
        }
    }

    // Continue com a atualização do orçamento em si
    $result = OrcamentoDAO::editarOrcamento($id_orcamento, $nome_orcamento, $data_orcamento, $validade, $status, $observacao, $fk_clientes_id_cliente);
    
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

if (isset($_POST['id_material'])) {
    $id_material = mysqli_real_escape_string($conexao, $_POST['id_material']);
    $preco = MaterialDAO::buscarPrecoMaterial($id_material);

    if ($preco) {
        echo json_encode(['preco' => $preco]);
    } else {
        echo json_encode(['preco' => 0]);
    }
    exit;
}

if (isset($_POST['id_servico'])) {
    $id_servico = mysqli_real_escape_string($conexao, $_POST['id_servico']);
    $preco = ServicoDAO::buscarPrecoServico($id_servico);

    if ($preco) {
        echo json_encode(['preco' => $preco]);
    } else {
        echo json_encode(['preco' => 0]);
    }
    exit;
}
