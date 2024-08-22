<?php
require 'conexao.php';

class ContasReceberDAO {
    public static function criarContaReceber($valor, $data_vencimento, $parcelas, $parcela_atual, $orcamento_id, $cliente_id) {
        global $conexao;
        $valor = mysqli_real_escape_string($conexao, $valor);
        $data_vencimento = mysqli_real_escape_string($conexao, $data_vencimento);
        $parcelas = (int)$parcelas;
        $parcela_atual = (int)$parcela_atual;
        $orcamento_id = (int)$orcamento_id;
        $cliente_id = (int)$cliente_id;

        $sql = "INSERT INTO contas_receber (valor, data_vencimento, parcelas, parcela_atual, fk_orcamentos_id_orcamento, fk_clientes_id_cliente) 
                VALUES ('$valor', '$data_vencimento', $parcelas, $parcela_atual, $orcamento_id, $cliente_id)";
        
        if (mysqli_query($conexao, $sql)) {
            return mysqli_insert_id($conexao);
        } else {
            error_log("Erro ao criar conta a receber: " . mysqli_error($conexao));
            return false;
        }
    }

    public static function editarContaReceber($id, $valor, $data_vencimento, $parcelas, $parcela_atual, $orcamento_id, $cliente_id) {
        global $conexao;
        $id = (int)mysqli_real_escape_string($conexao, $id);
        $valor = mysqli_real_escape_string($conexao, $valor);
        $data_vencimento = mysqli_real_escape_string($conexao, $data_vencimento);
        $parcelas = (int)$parcelas;
        $parcela_atual = (int)$parcela_atual;
        $orcamento_id = (int)$orcamento_id;
        $cliente_id = (int)$cliente_id;

        $sql = "UPDATE contas_receber 
                SET valor = '$valor', data_vencimento = '$data_vencimento', parcelas = $parcelas, parcela_atual = $parcela_atual, 
                    fk_orcamentos_id_orcamento = $orcamento_id, fk_clientes_id_cliente = $cliente_id
                WHERE id_conta_receber = $id";
        
        return mysqli_query($conexao, $sql);
    }

    public static function excluirContaReceber($id) {
        global $conexao;
        $id = mysqli_real_escape_string($conexao, $id);

        $sql = "UPDATE contas_receber SET ativo = FALSE WHERE id_conta_receber = '$id'";
        mysqli_query($conexao, $sql);

        $sql_parcelas = "UPDATE parcelas_receber SET ativo = FALSE WHERE fk_contas_receber_id_conta_receber = '$id'";
        mysqli_query($conexao, $sql_parcelas);
        
        return mysqli_affected_rows($conexao);
    }

    public static function listarContasReceber() {
        global $conexao;
        $sql = 'SELECT cr.*, c.nome_cliente, o.nome_orcamento 
                FROM contas_receber cr 
                INNER JOIN clientes c ON cr.fk_clientes_id_cliente = c.id_cliente
                INNER JOIN orcamentos o ON cr.fk_orcamentos_id_orcamento = o.id_orcamento
                WHERE cr.ativo = TRUE';
        $contas_receber = mysqli_query($conexao, $sql);
        if (!$contas_receber) {
            error_log("Erro na consulta listarContasReceber: " . mysqli_error($conexao));
            return false;
        }
        return $contas_receber;
    }

    public static function obterContaReceber($id) {
        global $conexao;
        $id = mysqli_real_escape_string($conexao, $id);
        $sql = "SELECT cr.*, c.nome_cliente, o.nome_orcamento FROM contas_receber cr 
                INNER JOIN clientes c ON cr.fk_clientes_id_cliente = c.id_cliente
                INNER JOIN orcamentos o ON cr.fk_orcamentos_id_orcamento = o.id_orcamento
                WHERE cr.id_conta_receber = '$id' AND cr.ativo = TRUE";
        
        $result = mysqli_query($conexao, $sql);
        return mysqli_fetch_assoc($result);
    }
}
?>
