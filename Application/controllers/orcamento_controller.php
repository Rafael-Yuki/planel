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
    $valor_total_orcamento = mysqli_real_escape_string($conexao, $_POST['valor_total_orcamento']);

    if (empty($nome_orcamento)) {
        $_SESSION['mensagem'] = 'O nome do orçamento é obrigatório!';
        $_SESSION['mensagem_tipo'] = 'error';
        header('Location: /planel/orcamentos');
        exit;
    }

    // Criar o orçamento
    $orcamentoId = OrcamentoDAO::criarOrcamento($nome_orcamento, $data_orcamento, $validade, $status, $observacao, $caminho_arquivo, $fk_cliente_id, $valor_total_orcamento);
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
