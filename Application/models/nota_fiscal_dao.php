<?php
require 'conexao.php';

class NotaFiscalDAO {
    public static function criarNotaFiscal($numero, $data_emissao, $valor_total, $parcelas, $fk_fornecedores_id_fornecedor, $caminho_arquivo = null) {
        global $conexao;
        $numero = mysqli_real_escape_string($conexao, trim($numero));
        $data_emissao = mysqli_real_escape_string($conexao, trim($data_emissao));
        $valor_total = mysqli_real_escape_string($conexao, trim($valor_total));
        $parcelas = mysqli_real_escape_string($conexao, trim($parcelas));
        $fk_fornecedores_id_fornecedor = (int)$fk_fornecedores_id_fornecedor;
        $caminho_arquivo = mysqli_real_escape_string($conexao, $caminho_arquivo);

        $sql = "INSERT INTO notas_fiscais (numero, data_emissao, valor_total, parcelas, fk_fornecedores_id_fornecedor, caminho_arquivo) 
                VALUES ('$numero', '$data_emissao', '$valor_total', '$parcelas', $fk_fornecedores_id_fornecedor, '$caminho_arquivo')";
        
        mysqli_query($conexao, $sql);
        return mysqli_affected_rows($conexao);
    }

    public static function editarNotaFiscal($id_nota_fiscal, $numero, $data_emissao, $valor_total, $parcelas, $fk_fornecedores_id_fornecedor, $caminho_arquivo = null) {
        global $conexao;
        $id_nota_fiscal = mysqli_real_escape_string($conexao, $id_nota_fiscal);
        $numero = mysqli_real_escape_string($conexao, trim($numero));
        $data_emissao = mysqli_real_escape_string($conexao, trim($data_emissao));
        $valor_total = mysqli_real_escape_string($conexao, trim($valor_total));
        $parcelas = mysqli_real_escape_string($conexao, trim($parcelas));
        $fk_fornecedores_id_fornecedor = (int)$fk_fornecedores_id_fornecedor;
        $caminho_arquivo = mysqli_real_escape_string($conexao, $caminho_arquivo);

        $sql = "UPDATE notas_fiscais SET numero = '$numero', data_emissao = '$data_emissao', valor_total = '$valor_total', 
                parcelas = '$parcelas', fk_fornecedores_id_fornecedor = $fk_fornecedores_id_fornecedor";

        if (!empty($caminho_arquivo)) {
            $sql .= ", caminho_arquivo = '$caminho_arquivo'";
        }

        $sql .= " WHERE id_nota_fiscal = '$id_nota_fiscal'";
        
        mysqli_query($conexao, $sql);
        return mysqli_affected_rows($conexao);
    }

    public static function excluirNotaFiscal($id_nota_fiscal) {
        global $conexao;
        $id_nota_fiscal = mysqli_real_escape_string($conexao, $id_nota_fiscal);

        $sql = "UPDATE notas_fiscais SET ativo = FALSE WHERE id_nota_fiscal = '$id_nota_fiscal'";
        
        mysqli_query($conexao, $sql);
        return mysqli_affected_rows($conexao);
    }

    public static function listarNotasFiscais(){
        global $conexao;
        $sql = 'SELECT notas_fiscais.*, fornecedores.nome_fornecedor FROM notas_fiscais 
                INNER JOIN fornecedores ON notas_fiscais.fk_fornecedores_id_fornecedor = fornecedores.id_fornecedor
                WHERE notas_fiscais.ativo = TRUE';
        $notas_fiscais = mysqli_query($conexao, $sql);
        return $notas_fiscais;
    }
}
