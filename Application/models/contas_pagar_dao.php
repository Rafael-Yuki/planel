<?php
require 'conexao.php';

class ContasPagarDAO {
    public static function criarContaPagar($valor, $data_vencimento, $parcelas, $parcela_atual, $nota_fiscal_id, $fornecedor_id) {
        global $conexao;
        $valor = mysqli_real_escape_string($conexao, $valor);
        $data_vencimento = mysqli_real_escape_string($conexao, $data_vencimento);
        $parcelas = (int)$parcelas;
        $parcela_atual = (int)$parcela_atual;
        $nota_fiscal_id = (int)$nota_fiscal_id;
        $fornecedor_id = (int)$fornecedor_id;

        $sql = "INSERT INTO contas_pagar (valor, data_vencimento, parcelas, parcela_atual, fk_notas_fiscais_id_nota_fiscal, fk_fornecedores_id_fornecedor) 
                VALUES ('$valor', '$data_vencimento', $parcelas, $parcela_atual, $nota_fiscal_id, $fornecedor_id)";
        
        if (mysqli_query($conexao, $sql)) {
            return mysqli_insert_id($conexao);
        } else {
            error_log("Erro ao criar conta a pagar: " . mysqli_error($conexao));
            return false;
        }
    }

    public static function editarContaPagar($id, $valor, $data_vencimento, $parcelas, $parcela_atual, $nota_fiscal_id, $fornecedor_id) {
        global $conexao;
        $id = (int)mysqli_real_escape_string($conexao, $id);
        $valor = mysqli_real_escape_string($conexao, $valor);
        $data_vencimento = mysqli_real_escape_string($conexao, $data_vencimento);
        $parcelas = (int)$parcelas;
        $parcela_atual = (int)$parcela_atual;
        $nota_fiscal_id = (int)$nota_fiscal_id;
        $fornecedor_id = (int)$fornecedor_id;

        $sql = "UPDATE contas_pagar 
                SET valor = '$valor', data_vencimento = '$data_vencimento', parcelas = $parcelas, parcela_atual = $parcela_atual, 
                    fk_notas_fiscais_id_nota_fiscal = $nota_fiscal_id, fk_fornecedores_id_fornecedor = $fornecedor_id
                WHERE id_conta_pagar = $id";
        
        return mysqli_query($conexao, $sql);
    }

    public static function excluirContaPagar($id) {
        global $conexao;
        $id = mysqli_real_escape_string($conexao, $id);

        $sql = "UPDATE contas_pagar SET ativo = FALSE WHERE id_conta_pagar = '$id'";
        mysqli_query($conexao, $sql);

        $sql_parcelas = "UPDATE parcelas_pagar SET ativo = FALSE WHERE fk_contas_pagar_id_conta_pagar = '$id'";
        mysqli_query($conexao, $sql_parcelas);
        
        return mysqli_affected_rows($conexao);
    }

    public static function listarContasPagar() {
        global $conexao;
        $sql = "SELECT cp.*, nf.numero AS numero_nota_fiscal, f.nome_fornecedor 
                FROM contas_pagar cp
                INNER JOIN notas_fiscais nf ON cp.fk_notas_fiscais_id_nota_fiscal = nf.id_nota_fiscal
                INNER JOIN fornecedores f ON cp.fk_fornecedores_id_fornecedor = f.id_fornecedor
                WHERE cp.ativo = TRUE";
        return mysqli_query($conexao, $sql);
    }    

    public static function obterContaPagar($id) {
        global $conexao;
        $id = (int)mysqli_real_escape_string($conexao, $id);
        $sql = "SELECT cp.*, f.nome_fornecedor, nf.numero 
                FROM contas_pagar cp 
                INNER JOIN fornecedores f ON cp.fk_fornecedores_id_fornecedor = f.id_fornecedor
                INNER JOIN notas_fiscais nf ON cp.fk_notas_fiscais_id_nota_fiscal = nf.id_nota_fiscal
                WHERE cp.id_conta_pagar = '$id' AND cp.ativo = TRUE";
        
        $result = mysqli_query($conexao, $sql);
        return mysqli_fetch_assoc($result);
    }    

    public static function deletarContasPorNotaFiscal($id_nota_fiscal) {
        global $conexao;
        $sql = "DELETE FROM contas_pagar WHERE fk_notas_fiscais_id_nota_fiscal = '$id_nota_fiscal'";
        mysqli_query($conexao, $sql);
    }    
}
?>
