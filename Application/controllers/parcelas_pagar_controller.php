<?php
session_start();
require('Application/models/parcelas_pagar_dao.php');
require('Application/models/conexao.php');

if (isset($_POST['criar_parcelas_pagar'])) {
    $conta_pagar_id = (int)mysqli_real_escape_string($conexao, $_POST['conta_pagar_id']);
    $parcelas = (int)mysqli_real_escape_string($conexao, $_POST['parcelas']);
    $nota_fiscal_id = (int)mysqli_real_escape_string($conexao, $_POST['nota_fiscal']);
    $fornecedor_id = (int)mysqli_real_escape_string($conexao, $_POST['fornecedor']);

    for ($i = 1; $i <= $parcelas; $i++) {
        $valor_parcela = str_replace(['R$', ' '], '', mysqli_real_escape_string($conexao, $_POST["valor_parcela_$i"]));
        $vencimento_parcela = mysqli_real_escape_string($conexao, $_POST["vencimento_parcela_$i"]);
        $data_pagamento = mysqli_real_escape_string($conexao, $_POST["data_pagamento_$i"]);
        $tipo_pagamento = (int)mysqli_real_escape_string($conexao, $_POST["tipo_pagamento_$i"]);

        if (!empty($data_pagamento) && empty($tipo_pagamento)) {
            $_SESSION['mensagem'] = "Selecione um tipo de pagamento para a Parcela $i.";
            $_SESSION['mensagem_tipo'] = 'error';
            header('Location: /planel/contas-a-pagar');
            exit;
        }

        $result = ParcelasPagarDAO::criarOuAtualizarParcela(null, $valor_parcela, $vencimento_parcela, $data_pagamento, $conta_pagar_id, $nota_fiscal_id, $fornecedor_id, $tipo_pagamento);
        if (!$result) {
            $_SESSION['mensagem'] = "Erro ao criar a Parcela $i.";
            $_SESSION['mensagem_tipo'] = 'error';
            header('Location: /planel/contas-a-pagar');
            exit;
        }
    }

    $_SESSION['mensagem'] = 'Parcelas criadas com sucesso!';
    $_SESSION['mensagem_tipo'] = 'success';
    header('Location: /planel/contas-a-pagar');
    exit;
}

if (isset($_POST['editar_parcelas_pagar'])) {
    $conta_pagar_id = (int)mysqli_real_escape_string($conexao, $_POST['conta_pagar_id']);
    $parcelas = (int)mysqli_real_escape_string($conexao, $_POST['parcelas']);
    $nota_fiscal_id = (int)mysqli_real_escape_string($conexao, $_POST['nota_fiscal']);
    $fornecedor_id = (int)mysqli_real_escape_string($conexao, $_POST['fornecedor']);

    $parcelas_existentes = ParcelasPagarDAO::listarParcelasPorConta($conta_pagar_id);
    $parcelas_existentes_ids = [];
    while ($parcela = mysqli_fetch_assoc($parcelas_existentes)) {
        $parcelas_existentes_ids[] = $parcela['id_parcela_pagar'];
    }

    for ($i = 1; $i <= $parcelas; $i++) {
        $parcela_id = $parcelas_existentes_ids[$i - 1] ?? null;
        $valor_parcela = str_replace(['R$', ' '], '', mysqli_real_escape_string($conexao, $_POST["valor_parcela_$i"]));
        $vencimento_parcela = mysqli_real_escape_string($conexao, $_POST["vencimento_parcela_$i"]);
        $data_pagamento = mysqli_real_escape_string($conexao, $_POST["data_pagamento_$i"]);
        $tipo_pagamento = (int)mysqli_real_escape_string($conexao, $_POST["tipo_pagamento_$i"]);

        if (!empty($data_pagamento) && empty($tipo_pagamento)) {
            $_SESSION['mensagem'] = "Selecione um tipo de pagamento para a Parcela $i.";
            $_SESSION['mensagem_tipo'] = 'error';
            header('Location: /planel/contas-a-pagar');
            exit;
        }

        if ($i <= count($parcelas_existentes_ids)) {
            ParcelasPagarDAO::reativarParcela($parcela_id);
            $result = ParcelasPagarDAO::criarOuAtualizarParcela($parcela_id, $valor_parcela, $vencimento_parcela, $data_pagamento, $conta_pagar_id, $nota_fiscal_id, $fornecedor_id, $tipo_pagamento);
        } else {
            $result = ParcelasPagarDAO::criarOuAtualizarParcela(null, $valor_parcela, $vencimento_parcela, $data_pagamento, $conta_pagar_id, $nota_fiscal_id, $fornecedor_id, $tipo_pagamento);
        }

        if (!$result) {
            $_SESSION['mensagem'] = "Erro ao atualizar a Parcela $i.";
            $_SESSION['mensagem_tipo'] = 'error';
            header('Location: /planel/contas-a-pagar');
            exit;
        }
    }

    // Desativar parcelas excedentes se o número de parcelas foi reduzido
    for ($j = $parcelas + 1; $j <= count($parcelas_existentes_ids); $j++) {
        ParcelasPagarDAO::desativarParcela($parcelas_existentes_ids[$j - 1]);
    }

    $_SESSION['mensagem'] = 'Parcelas atualizadas com sucesso!';
    $_SESSION['mensagem_tipo'] = 'success';
    header('Location: /planel/contas-a-pagar');
    exit;
}
?>
