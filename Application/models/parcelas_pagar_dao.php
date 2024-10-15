<?php
require 'conexao.php';

class ParcelasPagarDAO {

    public static function gerenciarParcelas($conta_pagar_id, $parcelas, $nota_fiscal_id, $fornecedor_id, $postData) {
        global $conexao;

        $parcelas_existentes = self::listarParcelasPorConta($conta_pagar_id);
        $parcelas_existentes_ids = [];
        while ($parcela = mysqli_fetch_assoc($parcelas_existentes)) {
            $parcelas_existentes_ids[] = $parcela['id_parcela_pagar'];
        }

        $valor_parcela_automatico = round($postData['valor'] / $parcelas, 2);

        for ($i = 1; $i <= $parcelas; $i++) {
            $parcela_id = $parcelas_existentes_ids[$i - 1] ?? null;
            $valor_parcela = str_replace(['.', ','], ['', '.'], mysqli_real_escape_string($conexao, $postData["valor_parcela_$i"] ?? $valor_parcela_automatico));
            $vencimento_parcela = mysqli_real_escape_string($conexao, $postData["vencimento_parcela_$i"]);
            $data_pagamento = !empty($postData["data_pagamento_$i"]) ? mysqli_real_escape_string($conexao, $postData["data_pagamento_$i"]) : null;
            $tipo_pagamento = !empty($postData["tipo_pagamento_$i"]) ? (int)mysqli_real_escape_string($conexao, $postData["tipo_pagamento_$i"]) : null;

            if ($i <= count($parcelas_existentes_ids)) {
                self::reativarParcela($parcela_id); 
                self::criarOuAtualizarParcela($parcela_id, $valor_parcela, $vencimento_parcela, $data_pagamento, $conta_pagar_id, $nota_fiscal_id, $fornecedor_id, $tipo_pagamento);
            } else {
                self::criarOuAtualizarParcela(null, $valor_parcela, $vencimento_parcela, $data_pagamento, $conta_pagar_id, $nota_fiscal_id, $fornecedor_id, $tipo_pagamento);
            }
        }

        for ($j = $parcelas + 1; $j <= count($parcelas_existentes_ids); $j++) {
            self::desativarParcela($parcelas_existentes_ids[$j - 1]);
        }
    }

    public static function criarOuAtualizarParcela($id_parcela, $valor_parcela, $vencimento_parcela, $data_pagamento, $conta_pagar_id, $nota_fiscal_id, $fornecedor_id, $tipo_pagamento) {
        global $conexao;
    
        $valor_parcela = mysqli_real_escape_string($conexao, $valor_parcela);
        $vencimento_parcela = !empty($vencimento_parcela) ? "'" . mysqli_real_escape_string($conexao, $vencimento_parcela) . "'" : 'NULL';
        $data_pagamento = !empty($data_pagamento) ? "'" . mysqli_real_escape_string($conexao, $data_pagamento) . "'" : 'NULL';
        $tipo_pagamento = !empty($tipo_pagamento) ? (int)$tipo_pagamento : 'NULL';
        $conta_pagar_id = (int)$conta_pagar_id;
        $nota_fiscal_id = (int)$nota_fiscal_id;
        $fornecedor_id = (int)$fornecedor_id;
    
        if ($id_parcela) {
            $sql = "UPDATE parcelas_pagar 
                    SET valor_parcela = '$valor_parcela', vencimento_parcela = $vencimento_parcela, data_pagamento = $data_pagamento, fk_tipo_pagamento_id_pagamento = $tipo_pagamento 
                    WHERE id_parcela_pagar = $id_parcela";
            return mysqli_query($conexao, $sql);
        } else {
            $sql = "INSERT INTO parcelas_pagar (valor_parcela, vencimento_parcela, data_pagamento, fk_contas_pagar_id_conta_pagar, fk_notas_fiscais_id_nota_fiscal, fk_fornecedores_id_fornecedor, fk_tipo_pagamento_id_pagamento) 
                    VALUES ('$valor_parcela', $vencimento_parcela, $data_pagamento, $conta_pagar_id, $nota_fiscal_id, $fornecedor_id, $tipo_pagamento)";
            return mysqli_query($conexao, $sql) ? mysqli_insert_id($conexao) : false;
        }
    }      

    public static function desativarParcela($id) {
        global $conexao;
        $id = (int)mysqli_real_escape_string($conexao, $id);
        $sql = "UPDATE parcelas_pagar SET ativo = FALSE WHERE id_parcela_pagar = $id";
        return mysqli_query($conexao, $sql);
    }

    public static function reativarParcela($id) {
        global $conexao;
        $id = (int)mysqli_real_escape_string($conexao, $id);
        $sql = "UPDATE parcelas_pagar SET ativo = TRUE WHERE id_parcela_pagar = $id";
        return mysqli_query($conexao, $sql);
    }

    public static function listarParcelasPorConta($conta_pagar_id) {
        global $conexao;
        $conta_pagar_id = (int)mysqli_real_escape_string($conexao, $conta_pagar_id);

        $sql = "SELECT pp.*, tp.tipo_pagamento FROM parcelas_pagar pp 
                LEFT JOIN tipo_pagamento tp ON pp.fk_tipo_pagamento_id_pagamento = tp.id_pagamento
                WHERE pp.fk_contas_pagar_id_conta_pagar = $conta_pagar_id AND pp.ativo = TRUE";

        return mysqli_query($conexao, $sql);
    }
}
?>
