<?php
session_start();
require('Application/models/contas_receber_dao.php');
require('Application/models/parcelas_receber_dao.php');
require('Application/models/conexao.php');

if (isset($_POST['criar_conta_receber'])) {
    $valor = mysqli_real_escape_string($conexao, $_POST['valor']);
    $data_vencimento = mysqli_real_escape_string($conexao, $_POST['data_vencimento']);
    $parcelas = (int)mysqli_real_escape_string($conexao, $_POST['parcelas']);
    $parcela_atual = (int)mysqli_real_escape_string($conexao, $_POST['parcela_atual']);
    $orcamento_id = (int)mysqli_real_escape_string($conexao, $_POST['orcamento']);
    $cliente_id = (int)mysqli_real_escape_string($conexao, $_POST['cliente']);

    $valor = str_replace(['.', ','], ['', '.'], $valor); // Remove o ponto e substitui a vírgula por ponto

    if (empty($valor) || empty($data_vencimento) || empty($parcelas) || $parcelas < 1 || $parcela_atual < 0 || $parcela_atual > $parcelas || empty($orcamento_id) || empty($cliente_id)) {
        $_SESSION['mensagem'] = 'Todos os campos são obrigatórios, e a parcela atual deve ser válida!';
        $_SESSION['mensagem_tipo'] = 'error';
        header('Location: /planel/contas-a-receber');
        exit;
    }

    $contaReceberId = ContasReceberDAO::criarContaReceber($valor, $data_vencimento, $parcelas, $parcela_atual, $orcamento_id, $cliente_id);

    if ($contaReceberId > 0) {
        $valor_parcela_automatico = round($valor / $parcelas, 2);
        for ($i = 1; $i <= $parcelas; $i++) {
            $valor_parcela = str_replace(['.', ','], ['', '.'], mysqli_real_escape_string($conexao, $_POST["valor_parcela_$i"] ?? $valor_parcela_automatico));
            $vencimento_parcela = !empty($_POST["vencimento_parcela_$i"]) ? mysqli_real_escape_string($conexao, $_POST["vencimento_parcela_$i"]) : null;
            $data_recebimento = !empty($_POST["data_recebimento_$i"]) ? mysqli_real_escape_string($conexao, $_POST["data_recebimento_$i"]) : null;
            $tipo_pagamento = !empty($_POST["tipo_pagamento_$i"]) ? (int)mysqli_real_escape_string($conexao, $_POST["tipo_pagamento_$i"]) : null;

            $result = ParcelasReceberDAO::criarOuAtualizarParcela(null, $valor_parcela, $vencimento_parcela, $data_recebimento, $contaReceberId, $orcamento_id, $cliente_id, $tipo_pagamento);

            if (!$result) {
                $_SESSION['mensagem'] = 'Erro ao criar uma das parcelas.';
                $_SESSION['mensagem_tipo'] = 'error';
                error_log("Erro ao criar parcela $i para a conta a receber ID: $contaReceberId");
                header('Location: /planel/contas-a-receber');
                exit;
            }
        }
        $_SESSION['mensagem'] = 'Conta a Receber criada com sucesso!';
        $_SESSION['mensagem_tipo'] = 'success';
    } else {
        $_SESSION['mensagem'] = 'Conta a Receber não foi criada';
        $_SESSION['mensagem_tipo'] = 'error';
    }
    header('Location: /planel/contas-a-receber');
    exit;
}

if (isset($_POST['editar_conta_receber'])) {
    $conta_receber_id = (int)mysqli_real_escape_string($conexao, $_POST['conta_receber_id']);
    $valor = mysqli_real_escape_string($conexao, $_POST['valor']);
    $data_vencimento = mysqli_real_escape_string($conexao, $_POST['data_vencimento']);
    $parcelas = (int)mysqli_real_escape_string($conexao, $_POST['parcelas']);
    $parcela_atual = (int)mysqli_real_escape_string($conexao, $_POST['parcela_atual']);
    $orcamento_id = (int)mysqli_real_escape_string($conexao, $_POST['orcamento']);
    $cliente_id = (int)mysqli_real_escape_string($conexao, $_POST['cliente']);

    $valor = str_replace(['.', ','], ['', '.'], $valor); // Remove o ponto e substitui a vírgula por ponto

    if (empty($valor) || empty($data_vencimento) || empty($parcelas) || $parcelas < 1 || $parcela_atual < 0 || $parcela_atual > $parcelas || empty($orcamento_id) || empty($cliente_id)) {
        $_SESSION['mensagem'] = 'Todos os campos são obrigatórios, e a parcela atual deve ser válida!';
        $_SESSION['mensagem_tipo'] = 'error';
        header('Location: /planel/contas-a-receber');
        exit;
    }

    // Editar a conta a receber
    $result = ContasReceberDAO::editarContaReceber($conta_receber_id, $valor, $data_vencimento, $parcelas, $parcela_atual, $orcamento_id, $cliente_id);

    if ($result) {
        ParcelasReceberDAO::gerenciarParcelas($conta_receber_id, $parcelas, $orcamento_id, $cliente_id, $_POST);

        $_SESSION['mensagem'] = 'Conta a Receber atualizada com sucesso!';
        $_SESSION['mensagem_tipo'] = 'success';
    } else {
        $_SESSION['mensagem'] = 'Conta a Receber não foi atualizada';
        $_SESSION['mensagem_tipo'] = 'warning';
    }
    header('Location: /planel/contas-a-receber');
    exit;
}

if (isset($_POST['excluir_conta_receber'])) {
    $id = mysqli_real_escape_string($conexao, $_POST['excluir_conta_receber']);
    
    $result = ContasReceberDAO::excluirContaReceber($id);
    if ($result > 0) {
        $_SESSION['mensagem'] = 'Conta a Receber deletada com sucesso!';
        $_SESSION['mensagem_tipo'] = 'success';
    } else {
        $_SESSION['mensagem'] = 'Conta a Receber não foi deletada';
        $_SESSION['mensagem_tipo'] = 'error';
    }
    header('Location: /planel/contas-a-receber');
    exit;
}
?>
