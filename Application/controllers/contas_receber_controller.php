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

    // Formatação do valor para garantir que esteja no formato correto
    $valor = str_replace(['.', ','], ['', '.'], $valor); // Remove o ponto e substitui a vírgula por ponto

    if (empty($valor) || empty($data_vencimento) || empty($parcelas) || $parcelas < 1 || $parcela_atual < 0 || $parcela_atual > $parcelas || empty($orcamento_id) || empty($cliente_id)) {
        $_SESSION['mensagem'] = 'Todos os campos são obrigatórios, e a parcela atual deve ser válida!';
        $_SESSION['mensagem_tipo'] = 'error';
        header('Location: /planel/contas-a-receber');
        exit;
    }

    // Primeiro, criamos a conta a receber
    $contaReceberId = ContasReceberDAO::criarContaReceber($valor, $data_vencimento, $parcelas, $parcela_atual, $orcamento_id, $cliente_id);

    if ($contaReceberId > 0) {
        // Se a conta a receber foi criada com sucesso, criamos as parcelas
        for ($i = 1; $i <= $parcelas; $i++) {
            $valor_parcela = str_replace(['.', ','], ['', '.'], mysqli_real_escape_string($conexao, $_POST["valor_parcela_$i"]));
            $vencimento_parcela = !empty($_POST["vencimento_parcela_$i"]) ? mysqli_real_escape_string($conexao, $_POST["vencimento_parcela_$i"]) : null;
            $data_recebimento = !empty($_POST["data_recebimento_$i"]) ? mysqli_real_escape_string($conexao, $_POST["data_recebimento_$i"]) : null;
            $tipo_pagamento = !empty($_POST["tipo_pagamento_$i"]) ? (int)mysqli_real_escape_string($conexao, $_POST["tipo_pagamento_$i"]) : null;

            $result = ParcelasReceberDAO::criarParcelaReceber($valor_parcela, $vencimento_parcela, $data_recebimento, $contaReceberId, $orcamento_id, $cliente_id, $tipo_pagamento);

            if (!$result) {
                // Se ocorrer um erro ao criar uma das parcelas, exiba uma mensagem de erro
                $_SESSION['mensagem'] = 'Erro ao criar uma das parcelas. Verifique os logs de erro.';
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
    $id = mysqli_real_escape_string($conexao, $_POST['conta_receber_id']);
    $valor = str_replace(['.', ','], ['', '.'], mysqli_real_escape_string($conexao, $_POST['valor']));
    $data_vencimento = mysqli_real_escape_string($conexao, $_POST['data_vencimento']);
    $parcelas = (int)mysqli_real_escape_string($conexao, $_POST['parcelas']);
    $parcela_atual = (int)mysqli_real_escape_string($conexao, $_POST['parcela_atual']);
    $orcamento_id = (int)mysqli_real_escape_string($conexao, $_POST['orcamento']);
    $cliente_id = (int)mysqli_real_escape_string($conexao, $_POST['cliente']);

    if (empty($valor) || empty($data_vencimento) || empty($parcelas) || $parcelas < 1 || $parcela_atual < 0 || $parcela_atual > $parcelas || empty($orcamento_id) || empty($cliente_id)) {
        $_SESSION['mensagem'] = 'Todos os campos são obrigatórios, e a parcela atual deve ser válida!';
        $_SESSION['mensagem_tipo'] = 'error';
        header('Location: /planel/contas-a-receber');
        exit;
    }

    $result = ContasReceberDAO::editarContaReceber($id, $valor, $data_vencimento, $parcelas, $parcela_atual, $orcamento_id, $cliente_id);
    if ($result >= 0) {
        // Atualizar ou criar parcelas
        foreach ($_POST['parcelas_existentes'] as $parcela_id => $parcela_data) {
            $valor_parcela = str_replace(['.', ','], ['', '.'], mysqli_real_escape_string($conexao, $parcela_data['valor']));
            $vencimento_parcela = !empty($parcela_data['vencimento']) ? mysqli_real_escape_string($conexao, $parcela_data['vencimento']) : null;
            $data_recebimento = !empty($parcela_data['data_recebimento']) ? mysqli_real_escape_string($conexao, $parcela_data['data_recebimento']) : null;
            $tipo_pagamento = !empty($parcela_data['tipo_pagamento']) ? (int)mysqli_real_escape_string($conexao, $parcela_data['tipo_pagamento']) : null;

            $result = ParcelasReceberDAO::editarParcelaReceber($parcela_id, $valor_parcela, $vencimento_parcela, $data_recebimento, $tipo_pagamento);

            if ($result < 0) {
                $_SESSION['mensagem'] = "Erro ao atualizar a Parcela $parcela_id.";
                $_SESSION['mensagem_tipo'] = 'error';
                header('Location: /planel/contas-a-receber/editar?id='.$id);
                exit;
            }
        }
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
