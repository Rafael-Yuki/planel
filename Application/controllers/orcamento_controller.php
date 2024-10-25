<?php
session_start();
require('Application/models/conexao.php');
require('Application/models/orcamento_dao.php');
require('Application/models/itens_orcamento_dao.php');
require('Application/models/material_dao.php');
require('Application/models/servico_dao.php');

if (isset($_POST['criar_orcamento'])) {
    $nome_orcamento = mysqli_real_escape_string($conexao, $_POST['nome_orcamento']);
    $data_orcamento = mysqli_real_escape_string($conexao, $_POST['data_orcamento']);
    $validade = mysqli_real_escape_string($conexao, $_POST['validade']);
    $status = mysqli_real_escape_string($conexao, $_POST['status']);
    $observacao = mysqli_real_escape_string($conexao, $_POST['observacao']);
    $caminho_arquivo = !empty($_FILES['arquivo_pdf']['name']) ? $_FILES['arquivo_pdf']['name'] : NULL;
    $fk_cliente_id = mysqli_real_escape_string($conexao, $_POST['cliente']);

    if (empty($nome_orcamento)) {
        $_SESSION['mensagem'] = 'O nome do orçamento é obrigatório!';
        $_SESSION['mensagem_tipo'] = 'error';
        header('Location: /planel/orcamentos');
        exit;
    }

    // Criar o orçamento
    $orcamentoId = OrcamentoDAO::criarOrcamento($nome_orcamento, $data_orcamento, $validade, $status, $observacao, $caminho_arquivo, $fk_cliente_id);
    if ($orcamentoId > 0) {
        // Decodificar dados dos materiais e serviços, se houverem
        $materiais = isset($_POST['materiaisCapturados']) ? json_decode($_POST['materiaisCapturados'], true) : [];
        $servicos = isset($_POST['servicosCapturados']) ? json_decode($_POST['servicosCapturados'], true) : [];

        // Criar itens de orçamento e vincular materiais e serviços
        foreach ($_POST['nome_item'] as $index => $nomeItem) {
            // Criar o item de orçamento com nome e descrição fornecidos
            $descricaoItem = mysqli_real_escape_string($conexao, $_POST['descricao_item'][$index]);
            $nome_item = mysqli_real_escape_string($conexao, $nomeItem);
            $valor_total_item = mysqli_real_escape_string($conexao, $_POST['valor_total_item'][$index]);

            // Criar o item de orçamento na tabela itens_orcamento
            $itemId = ItensOrcamentoDAO::criarItemOrcamento($orcamentoId, $nome_item, $descricaoItem, $valor_total_item);

            if ($itemId > 0) {
                // Verificar se há materiais para este item e adicionar
                if (!empty($materiais)) {
                    foreach ($materiais as $materialGroup) {
                        if ($materialGroup['idItem'] == "item-" . ($index + 1)) {
                            foreach ($materialGroup['materiaisDoItem'] as $material) {
                                $materialId = $material['materialId'];
                                $quantidade = mysqli_real_escape_string($conexao, $material['quantidade']);
                                $valor_unitario = mysqli_real_escape_string($conexao, $material['preco']);
                                $nomeMaterial = MaterialDAO::buscarNomeMaterial($materialId);
                                if (!$nomeMaterial) $nomeMaterial = 'Material Desconhecido';

                                MaterialDAO::adicionarMaterialAoOrcamento($itemId, $materialId, $valor_unitario, $quantidade, $nomeMaterial);
                            }
                        }
                    }
                }

                // Verificar se há serviços para este item e adicionar
                if (!empty($servicos)) {
                    foreach ($servicos as $servicoGroup) {
                        if ($servicoGroup['idItem'] == "item-" . ($index + 1)) {
                            foreach ($servicoGroup['servicosDoItem'] as $servico) {
                                $servicoId = $servico['servicoId'];
                                $quantidade = mysqli_real_escape_string($conexao, $servico['quantidade']);
                                $valor_unitario = mysqli_real_escape_string($conexao, $servico['preco']);
                                $nomeServico = ServicoDAO::buscarNomeServico($servicoId);
                                if (!$nomeServico) $nomeServico = 'Serviço Desconhecido';

                                ServicoDAO::adicionarServicoAoOrcamento($itemId, $servicoId, $valor_unitario, $quantidade, $nomeServico);
                            }
                        }
                    }
                }
            }
        }

        $_SESSION['mensagem'] = 'Orçamento criado com sucesso!';
        $_SESSION['mensagem_tipo'] = 'success';
    } else {
        $_SESSION['mensagem'] = 'Orçamento não foi criado';
        $_SESSION['mensagem_tipo'] = 'error';
    }
    header('Location: /planel/orcamentos');
    exit;
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
                    ItensOrcamentoDAO::adicionarMaterialAoItem($id_orcamento, $id_material, $quantidade, $preco);
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
                    ItensOrcamentoDAO::adicionarServicoAoItem($id_orcamento, $id_servico, $quantidade_servico, $preco_servico);
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
?>
