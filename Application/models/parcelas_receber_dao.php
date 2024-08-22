<?php
require 'conexao.php';

class ParcelasReceberDAO {

    public static function gerenciarParcelas($conta_receber_id, $parcelas, $orcamento_id, $cliente_id, $postData) {
        global $conexao;

        $parcelas_existentes = self::listarParcelasPorConta($conta_receber_id);
        $parcelas_existentes_ids = [];
        while ($parcela = mysqli_fetch_assoc($parcelas_existentes)) {
            $parcelas_existentes_ids[] = $parcela['id_parcela_receber'];
        }

        $valor_parcela_automatico = round($postData['valor'] / $parcelas, 2);

        for ($i = 1; $i <= $parcelas; $i++) {
            $parcela_id = $parcelas_existentes_ids[$i - 1] ?? null;
            $valor_parcela = str_replace(['.', ','], ['', '.'], mysqli_real_escape_string($conexao, $postData["valor_parcela_$i"] ?? $valor_parcela_automatico));
            $vencimento_parcela = mysqli_real_escape_string($conexao, $postData["vencimento_parcela_$i"]);
            $data_recebimento = !empty($postData["data_recebimento_$i"]) ? mysqli_real_escape_string($conexao, $postData["data_recebimento_$i"]) : null;
            $tipo_pagamento = !empty($postData["tipo_pagamento_$i"]) ? (int)mysqli_real_escape_string($conexao, $postData["tipo_pagamento_$i"]) : null;

            if ($i <= count($parcelas_existentes_ids)) {
                self::reativarParcela($parcela_id); // Reativar a parcela se já existir
                self::criarOuAtualizarParcela($parcela_id, $valor_parcela, $vencimento_parcela, $data_recebimento, $conta_receber_id, $orcamento_id, $cliente_id, $tipo_pagamento);
            } else {
                self::criarOuAtualizarParcela(null, $valor_parcela, $vencimento_parcela, $data_recebimento, $conta_receber_id, $orcamento_id, $cliente_id, $tipo_pagamento);
            }
        }

        for ($j = $parcelas + 1; $j <= count($parcelas_existentes_ids); $j++) {
            self::desativarParcela($parcelas_existentes_ids[$j - 1]);
        }
    }

    public static function criarOuAtualizarParcela($id_parcela, $valor_parcela, $vencimento_parcela, $data_recebimento, $conta_receber_id, $orcamento_id, $cliente_id, $tipo_pagamento) {
        global $conexao;

        $valor_parcela = mysqli_real_escape_string($conexao, $valor_parcela);
        $vencimento_parcela = !empty($vencimento_parcela) ? "'" . mysqli_real_escape_string($conexao, $vencimento_parcela) . "'" : 'NULL';
        $data_recebimento = !empty($data_recebimento) ? "'" . mysqli_real_escape_string($conexao, $data_recebimento) . "'" : 'NULL';
        $tipo_pagamento = !empty($tipo_pagamento) ? (int)$tipo_pagamento : 'NULL';
        $conta_receber_id = (int)$conta_receber_id;
        $orcamento_id = (int)$orcamento_id;
        $cliente_id = (int)$cliente_id;

        if ($id_parcela) {
            $sql = "UPDATE parcelas_receber 
                    SET valor_parcela = '$valor_parcela', vencimento_parcela = $vencimento_parcela, data_recebimento = $data_recebimento, fk_tipo_pagamento_id_pagamento = $tipo_pagamento 
                    WHERE id_parcela_receber = $id_parcela";
            return mysqli_query($conexao, $sql);
        } else {
            $sql = "INSERT INTO parcelas_receber (valor_parcela, vencimento_parcela, data_recebimento, fk_contas_receber_id_conta_receber, fk_orcamentos_id_orcamento, fk_clientes_id_cliente, fk_tipo_pagamento_id_pagamento) 
                    VALUES ('$valor_parcela', $vencimento_parcela, $data_recebimento, $conta_receber_id, $orcamento_id, $cliente_id, $tipo_pagamento)";
            return mysqli_query($conexao, $sql) ? mysqli_insert_id($conexao) : false;
        }
    }

    public static function desativarParcela($id) {
        global $conexao;
        $id = (int)mysqli_real_escape_string($conexao, $id);
        $sql = "UPDATE parcelas_receber SET ativo = FALSE WHERE id_parcela_receber = $id";
        return mysqli_query($conexao, $sql);
    }

    public static function reativarParcela($id) {
        global $conexao;
        $id = (int)mysqli_real_escape_string($conexao, $id);
        $sql = "UPDATE parcelas_receber SET ativo = TRUE WHERE id_parcela_receber = $id";
        return mysqli_query($conexao, $sql);
    }

    public static function listarParcelasPorConta($conta_receber_id) {
        global $conexao;
        $conta_receber_id = (int)mysqli_real_escape_string($conexao, $conta_receber_id);

        $sql = "SELECT pr.*, tp.tipo_pagamento FROM parcelas_receber pr 
                LEFT JOIN tipo_pagamento tp ON pr.fk_tipo_pagamento_id_pagamento = tp.id_pagamento
                WHERE pr.fk_contas_receber_id_conta_receber = $conta_receber_id AND pr.ativo = TRUE";

        return mysqli_query($conexao, $sql);
    }
}
?>
