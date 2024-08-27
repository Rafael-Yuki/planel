<?php
session_start();
require('Application/models/contas_pagar_dao.php');
require('Application/models/parcelas_pagar_dao.php');
require('Application/models/conexao.php');

if (isset($_POST['criar_conta_pagar'])) {
    $valor = mysqli_real_escape_string($conexao, $_POST['valor']);
    $data_vencimento = mysqli_real_escape_string($conexao, $_POST['data_vencimento']);
    $parcelas = (int)mysqli_real_escape_string($conexao, $_POST['parcelas']);
    $parcela_atual = (int)mysqli_real_escape_string($conexao, $_POST['parcela_atual']);
    $nota_fiscal_id = (int)mysqli_real_escape_string($conexao, $_POST['nota_fiscal']);
    $fornecedor_id = (int)mysqli_real_escape_string($conexao, $_POST['fornecedor']);

    $valor = str_replace(['.', ','], ['', '.'], $valor); // Remove o ponto e substitui a vírgula por ponto

    if (empty($valor) || empty($data_vencimento) || empty($parcelas) || $parcelas < 1 || $parcela_atual < 0 || $parcela_atual > $parcelas || empty($nota_fiscal_id) || empty($fornecedor_id)) {
        $_SESSION['mensagem'] = 'Todos os campos são obrigatórios, e a parcela atual deve ser válida!';
        $_SESSION['mensagem_tipo'] = 'error';
        header('Location: /planel/contas-a-pagar');
        exit;
    }

    $contaPagarId = ContasPagarDAO::criarContaPagar($valor, $data_vencimento, $parcelas, $parcela_atual, $nota_fiscal_id, $fornecedor_id);

    if ($contaPagarId > 0) {
        $valor_parcela_automatico = round($valor / $parcelas, 2);
        for ($i = 1; $i <= $parcelas; $i++) {
            $valor_parcela = str_replace(['.', ','], ['', '.'], mysqli_real_escape_string($conexao, $_POST["valor_parcela_$i"] ?? $valor_parcela_automatico));
            $vencimento_parcela = !empty($_POST["vencimento_parcela_$i"]) ? mysqli_real_escape_string($conexao, $_POST["vencimento_parcela_$i"]) : null;
            $data_pagamento = !empty($_POST["data_pagamento_$i"]) ? mysqli_real_escape_string($conexao, $_POST["data_pagamento_$i"]) : null;
            $tipo_pagamento = !empty($_POST["tipo_pagamento_$i"]) ? (int)mysqli_real_escape_string($conexao, $_POST["tipo_pagamento_$i"]) : null;

            $result = ParcelasPagarDAO::criarOuAtualizarParcela(null, $valor_parcela, $vencimento_parcela, $data_pagamento, $contaPagarId, $nota_fiscal_id, $fornecedor_id, $tipo_pagamento);

            if (!$result) {
                $_SESSION['mensagem'] = 'Erro ao criar uma das parcelas.';
                $_SESSION['mensagem_tipo'] = 'error';
                error_log("Erro ao criar parcela $i para a conta a pagar ID: $contaPagarId");
                header('Location: /planel/contas-a-pagar');
                exit;
            }
        }
        $_SESSION['mensagem'] = 'Conta a Pagar criada com sucesso!';
        $_SESSION['mensagem_tipo'] = 'success';
    } else {
        $_SESSION['mensagem'] = 'Conta a Pagar não foi criada';
        $_SESSION['mensagem_tipo'] = 'error';
    }
    header('Location: /planel/contas-a-pagar');
    exit;
}

if (isset($_POST['editar_conta_pagar'])) {
    $conta_pagar_id = (int)mysqli_real_escape_string($conexao, $_POST['conta_pagar_id']);
    $valor = mysqli_real_escape_string($conexao, $_POST['valor']);
    $data_vencimento = mysqli_real_escape_string($conexao, $_POST['data_vencimento']);
    $parcelas = (int)mysqli_real_escape_string($conexao, $_POST['parcelas']);
    $parcela_atual = (int)mysqli_real_escape_string($conexao, $_POST['parcela_atual']);
    $nota_fiscal_id = (int)mysqli_real_escape_string($conexao, $_POST['nota_fiscal']);
    $fornecedor_id = (int)mysqli_real_escape_string($conexao, $_POST['fornecedor']);

    $valor = str_replace(['.', ','], ['', '.'], $valor); // Remove o ponto e substitui a vírgula por ponto

    if (empty($valor) || empty($data_vencimento) || empty($parcelas) || $parcelas < 1 || $parcela_atual < 0 || $parcela_atual > $parcelas || empty($nota_fiscal_id) || empty($fornecedor_id)) {
        $_SESSION['mensagem'] = 'Todos os campos são obrigatórios, e a parcela atual deve ser válida!';
        $_SESSION['mensagem_tipo'] = 'error';
        header('Location: /planel/contas-a-pagar');
        exit;
    }

    // Editar a conta a pagar
    $result = ContasPagarDAO::editarContaPagar($conta_pagar_id, $valor, $data_vencimento, $parcelas, $parcela_atual, $nota_fiscal_id, $fornecedor_id);

    if ($result) {
        ParcelasPagarDAO::gerenciarParcelas($conta_pagar_id, $parcelas, $nota_fiscal_id, $fornecedor_id, $_POST);

        $_SESSION['mensagem'] = 'Conta a Pagar atualizada com sucesso!';
        $_SESSION['mensagem_tipo'] = 'success';
    } else {
        $_SESSION['mensagem'] = 'Conta a Pagar não foi atualizada';
        $_SESSION['mensagem_tipo'] = 'warning';
    }
    header('Location: /planel/contas-a-pagar');
    exit;
}

if (isset($_POST['excluir_conta_pagar'])) {
    $id = mysqli_real_escape_string($conexao, $_POST['excluir_conta_pagar']);
    
    $result = ContasPagarDAO::excluirContaPagar($id);
    if ($result > 0) {
        $_SESSION['mensagem'] = 'Conta a Pagar deletada com sucesso!';
        $_SESSION['mensagem_tipo'] = 'success';
    } else {
        $_SESSION['mensagem'] = 'Conta a Pagar não foi deletada';
        $_SESSION['mensagem_tipo'] = 'error';
    }
    header('Location: /planel/contas-a-pagar');
    exit;
}
?>
