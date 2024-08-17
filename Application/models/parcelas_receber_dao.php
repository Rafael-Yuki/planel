<?php
require 'conexao.php';

class ParcelasReceberDAO {
    public static function criarParcelaReceber($valor_parcela, $vencimento_parcela, $data_recebimento, $conta_receber_id, $orcamento_id, $cliente_id, $tipo_pagamento) {
        global $conexao;

        // Validação e formatação do valor da parcela
        if (empty($valor_parcela)) {
            return false; // Retorna falso se o valor da parcela estiver vazio
        }
        
        $valor_parcela = mysqli_real_escape_string($conexao, $valor_parcela);

        // Verifica se vencimento_parcela está vazio, se estiver, define como NULL
        $vencimento_parcela = !empty($vencimento_parcela) ? "'" . mysqli_real_escape_string($conexao, $vencimento_parcela) . "'" : 'NULL';

        // Verifica se data_recebimento está vazio, se estiver, define como NULL
        $data_recebimento = !empty($data_recebimento) ? "'" . mysqli_real_escape_string($conexao, $data_recebimento) . "'" : 'NULL';

        // Verifica se tipo_pagamento está vazio, se estiver, define como NULL
        $tipo_pagamento = !empty($tipo_pagamento) ? (int)$tipo_pagamento : 'NULL';

        // Montagem do SQL de inserção
        $sql = "INSERT INTO parcelas_receber (valor_parcela, vencimento_parcela, data_recebimento, fk_contas_receber_id_conta_receber, fk_orcamentos_id_orcamento, fk_clientes_id_cliente, fk_tipo_pagamento_id_pagamento) 
                VALUES ('$valor_parcela', $vencimento_parcela, $data_recebimento, $conta_receber_id, $orcamento_id, $cliente_id, $tipo_pagamento)";

        // Execução do SQL
        if (mysqli_query($conexao, $sql)) {
            return mysqli_insert_id($conexao);
        } else {
            error_log("Erro ao criar parcela: " . mysqli_error($conexao));
            return false;
        }
    }

    public static function editarParcelaReceber($id, $valor_parcela, $vencimento_parcela, $data_recebimento, $tipo_pagamento) {
        global $conexao;

        if (empty($valor_parcela)) {
            return false; // Retorna falso se o valor da parcela estiver vazio
        }

        $valor_parcela = mysqli_real_escape_string($conexao, $valor_parcela);
        $vencimento_parcela = !empty($vencimento_parcela) ? "'" . mysqli_real_escape_string($conexao, $vencimento_parcela) . "'" : 'NULL';
        $data_recebimento = !empty($data_recebimento) ? "'" . mysqli_real_escape_string($conexao, $data_recebimento) . "'" : 'NULL';
        $tipo_pagamento = !empty($tipo_pagamento) ? (int)$tipo_pagamento : 'NULL';

        $sql = "UPDATE parcelas_receber 
                SET valor_parcela = '$valor_parcela', vencimento_parcela = $vencimento_parcela, data_recebimento = $data_recebimento, fk_tipo_pagamento_id_pagamento = $tipo_pagamento 
                WHERE id_parcela_receber = $id";

        if (mysqli_query($conexao, $sql)) {
            return mysqli_affected_rows($conexao);
        } else {
            error_log("Erro ao atualizar parcela: " . mysqli_error($conexao));
            return false;
        }
    }

    public static function listarParcelasPorConta($conta_receber_id) {
        global $conexao;
        $conta_receber_id = mysqli_real_escape_string($conexao, $conta_receber_id);
    
        $sql = "SELECT pr.*, tp.tipo_pagamento FROM parcelas_receber pr 
                LEFT JOIN tipo_pagamento tp ON pr.fk_tipo_pagamento_id_pagamento = tp.id_pagamento
                WHERE pr.fk_contas_receber_id_conta_receber = $conta_receber_id";
    
        $result = mysqli_query($conexao, $sql);
        return $result;
    }

    // Método para excluir uma parcela específica
    public static function excluirParcela($parcela_id) {
        global $conexao;
        $parcela_id = mysqli_real_escape_string($conexao, $parcela_id);
        
        $sql = "DELETE FROM parcelas_receber WHERE id_parcela_receber = '$parcela_id'";
        
        mysqli_query($conexao, $sql);
        return mysqli_affected_rows($conexao);
    }

    // Método para excluir todas as parcelas acima de um determinado número
    public static function excluirParcelasAcimaDe($conta_receber_id, $quantidade_parcelas) {
        global $conexao;
        $conta_receber_id = mysqli_real_escape_string($conexao, $conta_receber_id);
        $quantidade_parcelas = (int) $quantidade_parcelas;
        
        $sql = "DELETE FROM parcelas_receber 
                WHERE fk_contas_receber_id_conta_receber = '$conta_receber_id' 
                AND numero_parcela > '$quantidade_parcelas'";
        
        mysqli_query($conexao, $sql);
        return mysqli_affected_rows($conexao);
    }
}
?>
