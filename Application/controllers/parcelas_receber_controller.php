<?php
session_start();
require('Application/models/parcelas_receber_dao.php');
require('Application/models/conexao.php');

if (isset($_POST['criar_parcelas_receber'])) {
    $conta_receber_id = (int)mysqli_real_escape_string($conexao, $_POST['conta_receber_id']);
    $parcelas = (int)mysqli_real_escape_string($conexao, $_POST['parcelas']);
    $orcamento_id = (int)mysqli_real_escape_string($conexao, $_POST['orcamento']);
    $cliente_id = (int)mysqli_real_escape_string($conexao, $_POST['cliente']);

    // Loop para inserir cada parcela
    for ($i = 1; $i <= $parcelas; $i++) {
        $valor_parcela = str_replace(['R$', ' '], '', mysqli_real_escape_string($conexao, $_POST["valor_parcela_$i"]));
        $vencimento_parcela = mysqli_real_escape_string($conexao, $_POST["vencimento_parcela_$i"]);
        $data_recebimento = mysqli_real_escape_string($conexao, $_POST["data_recebimento_$i"]);
        $tipo_pagamento = (int)mysqli_real_escape_string($conexao, $_POST["tipo_pagamento_$i"]);

        // Se a parcela tiver data de recebimento, precisa ter tipo de pagamento
        if (!empty($data_recebimento) && empty($tipo_pagamento)) {
            $_SESSION['mensagem'] = "Selecione um tipo de pagamento para a Parcela $i.";
            $_SESSION['mensagem_tipo'] = 'error';
            header('Location: /planel/contas-a-receber');
            exit;
        }

        // Criar a parcela
        $result = ParcelasReceberDAO::criarParcelaReceber($valor_parcela, $vencimento_parcela, $data_recebimento, $conta_receber_id, $orcamento_id, $cliente_id, $tipo_pagamento);
        if (!$result) {
            $_SESSION['mensagem'] = "Erro ao criar a Parcela $i.";
            $_SESSION['mensagem_tipo'] = 'error';
            header('Location: /planel/contas-a-receber');
            exit;
        }
    }

    $_SESSION['mensagem'] = 'Parcelas criadas com sucesso!';
    $_SESSION['mensagem_tipo'] = 'success';
    header('Location: /planel/contas-a-receber');
    exit;
}

if (isset($_POST['editar_parcelas_receber'])) {
    $conta_receber_id = (int)mysqli_real_escape_string($conexao, $_POST['conta_receber_id']);
    $parcelas = (int)mysqli_real_escape_string($conexao, $_POST['parcelas']);
    $orcamento_id = (int)mysqli_real_escape_string($conexao, $_POST['orcamento']);
    $cliente_id = (int)mysqli_real_escape_string($conexao, $_POST['cliente']);

    // Loop para atualizar cada parcela
    for ($i = 1; $i <= $parcelas; $i++) {
        $id_parcela = (int)mysqli_real_escape_string($conexao, $_POST["parcela_id_$i"]);
        $valor_parcela = str_replace(['R$', ' '], '', mysqli_real_escape_string($conexao, $_POST["valor_parcela_$i"]));
        $vencimento_parcela = mysqli_real_escape_string($conexao, $_POST["vencimento_parcela_$i"]);
        $data_recebimento = mysqli_real_escape_string($conexao, $_POST["data_recebimento_$i"]);
        $tipo_pagamento = (int)mysqli_real_escape_string($conexao, $_POST["tipo_pagamento_$i"]);

        if (!empty($data_recebimento) && empty($tipo_pagamento)) {
            $_SESSION['mensagem'] = "Selecione um tipo de pagamento para a Parcela $i.";
            $_SESSION['mensagem_tipo'] = 'error';
            header('Location: /planel/contas-a-receber');
            exit;
        }

        // Editar a parcela
        $result = ParcelasReceberDAO::editarParcelaReceber($id_parcela, $valor_parcela, $vencimento_parcela, $data_recebimento, $tipo_pagamento);
        if (!$result) {
            $_SESSION['mensagem'] = "Erro ao atualizar a Parcela $i.";
            $_SESSION['mensagem_tipo'] = 'error';
            header('Location: /planel/contas-a-receber');
            exit;
        }
    }

    $_SESSION['mensagem'] = 'Parcelas atualizadas com sucesso!';
    $_SESSION['mensagem_tipo'] = 'success';
    header('Location: /planel/contas-a-receber');
    exit;
}
?>
