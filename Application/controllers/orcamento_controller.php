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

    mysqli_begin_transaction($conexao);

    try {
        $result = OrcamentoDAO::criarOrcamento($nome_orcamento, $data_orcamento, $validade, $status, $observacao, $fk_clientes_id_cliente, $caminho_arquivo);
        if ($result > 0) {
            $orcamento_id = mysqli_insert_id($conexao);

            if (isset($_POST['nome_material'])) {
                foreach ($_POST['nome_material'] as $index => $id_material) {
                    $quantidade = $_POST['quantidade'][$index];
                    $preco = $_POST['preco'][$index];

                    $quantidade = number_format($quantidade, 0, ',', '.');
                    $preco = number_format($preco, 2, ',', '.');

                    MaterialDAO::adicionarMaterialOrcamento($orcamento_id, $id_material, $quantidade, $preco);
                }
            }

            if (isset($_POST['nome_servico'])) {
                foreach ($_POST['nome_servico'] as $index => $id_servico) {
                    $quantidade = $_POST['quantidade_servico'][$index];
                    $preco = $_POST['preco_servico'][$index];

                    $quantidade = number_format($quantidade, 0, ',', '.');
                    $preco = number_format($preco, 2, ',', '.');

                    ServicoDAO::adicionarServicoOrcamento($orcamento_id, $id_servico, $quantidade, $preco);
                }
            }

            mysqli_commit($conexao);
            $_SESSION['mensagem'] = 'Orçamento criado com sucesso!';
            $_SESSION['mensagem_tipo'] = 'success';
        } else {
            throw new Exception('Erro ao criar o orçamento');
        }
    } catch (Exception $e) {
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

    mysqli_begin_transaction($conexao);

    try {
        // Editar o orçamento principal
        $result = OrcamentoDAO::editarOrcamento($id_orcamento, $nome_orcamento, $data_orcamento, $validade, $status, $observacao, $fk_clientes_id_cliente);
        
        // Verificar e editar/adicionar materiais
        if (isset($_POST['nome_material'])) {
            foreach ($_POST['nome_material'] as $index => $id_material) {
                $quantidade = (float)str_replace(',', '.', $_POST['quantidade'][$index]);
                $preco = (float)str_replace(',', '.', $_POST['preco'][$index]);
                $id_orcamento_material = $_POST['id_orcamento_material'][$index] ?? null;

                if ($id_orcamento_material) {
                    MaterialDAO::editarMaterialOrcamento($id_orcamento_material, $id_material, $quantidade, $preco);
                } else {
                    MaterialDAO::adicionarMaterialOrcamento($id_orcamento, $id_material, $quantidade, $preco);
                }
            }
        }

        // Verificar e editar/adicionar serviços
        if (isset($_POST['nome_servico'])) {
            foreach ($_POST['nome_servico'] as $index => $id_servico) {
                $quantidade_servico = (float)str_replace(',', '.', $_POST['quantidade_servico'][$index]);
                $preco_servico = (float)str_replace(',', '.', $_POST['preco_servico'][$index]);
                $id_orcamento_servico = $_POST['id_orcamento_servico'][$index] ?? null;

                if ($id_orcamento_servico) {
                    ServicoDAO::editarServicoOrcamento($id_orcamento_servico, $id_servico, $quantidade_servico, $preco_servico);
                } else {
                    ServicoDAO::adicionarServicoOrcamento($id_orcamento, $id_servico, $quantidade_servico, $preco_servico);
                }
            }
        }

        // Excluir materiais marcados para remoção
        if (isset($_POST['materiais_para_remover'])) {
            $materiaisParaRemover = json_decode($_POST['materiais_para_remover'], true);
            foreach ($materiaisParaRemover as $id_orcamento_material) {
                MaterialDAO::excluirMaterialOrcamento($id_orcamento_material);
            }
        }

        // Excluir serviços marcados para remoção
        if (isset($_POST['servicos_para_remover'])) {
            $servicosParaRemover = json_decode($_POST['servicos_para_remover'], true);
            foreach ($servicosParaRemover as $id_orcamento_servico) {
                ServicoDAO::excluirServicoOrcamento($id_orcamento_servico);
            }
        }

        mysqli_commit($conexao);
        $_SESSION['mensagem'] = 'Orçamento atualizado com sucesso!';
        $_SESSION['mensagem_tipo'] = 'success';
    } catch (Exception $e) {
        mysqli_rollback($conexao);
        $_SESSION['mensagem'] = 'Erro ao atualizar orçamento: ' . $e->getMessage();
        $_SESSION['mensagem_tipo'] = 'error';
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
