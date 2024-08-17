<?php
require 'conexao.php';

class ParcelasReceberDAO {
    
    public static function criarParcelasReceber($idContaReceber, $parcelas) {
        global $conexao;
        
        foreach ($parcelas as $parcela) {
            $valorParcela = mysqli_real_escape_string($conexao, str_replace(['R$', '.', ','], ['', '', '.'], $parcela['valor_parcela']));
            $vencimentoParcela = mysqli_real_escape_string($conexao, $parcela['vencimento_parcela']);
            $dataRecebimento = isset($parcela['data_recebimento']) ? mysqli_real_escape_string($conexao, $parcela['data_recebimento']) : null;
            $tipoPagamento = isset($parcela['tipo_pagamento']) ? mysqli_real_escape_string($conexao, $parcela['tipo_pagamento']) : null;

            $sql = "INSERT INTO parcelas_receber (valor_parcela, vencimento_parcela, data_recebimento, fk_tipo_pagamento_id_pagamento, fk_contas_receber_id_conta_receber) 
                    VALUES ('$valorParcela', '$vencimentoParcela', " . ($dataRecebimento ? "'$dataRecebimento'" : "NULL") . ", " . ($tipoPagamento ? "'$tipoPagamento'" : "NULL") . ", '$idContaReceber')";
            mysqli_query($conexao, $sql);
        }

        return mysqli_affected_rows($conexao);
    }

    public static function editarParcelasReceber($idContaReceber, $parcelas) {
        global $conexao;

        // Primeiro, excluir as parcelas existentes
        $sql = "DELETE FROM parcelas_receber WHERE fk_contas_receber_id_conta_receber = '$idContaReceber'";
        mysqli_query($conexao, $sql);

        // Depois, recriar as parcelas com os novos valores
        return self::criarParcelasReceber($idContaReceber, $parcelas);
    }
}
?>
