<?php
require 'conexao.php';

class NotaFiscalDAO {
    public static function criarNotaFiscal($numero, $data_emissao, $valor_total, $parcelas, $fk_fornecedores_id_fornecedor, $caminho_xml = null) {
        global $conexao;
        $numero = mysqli_real_escape_string($conexao, trim($numero));
        $data_emissao = mysqli_real_escape_string($conexao, trim($data_emissao));
        $valor_total = mysqli_real_escape_string($conexao, trim($valor_total));
        $parcelas = mysqli_real_escape_string($conexao, trim($parcelas));
        $fk_fornecedores_id_fornecedor = (int)$fk_fornecedores_id_fornecedor;
        $caminho_xml = mysqli_real_escape_string($conexao, $caminho_xml);

        $sql = "INSERT INTO notas_fiscais (numero, data_emissao, valor_total, parcelas, fk_fornecedores_id_fornecedor, caminho_xml) 
                VALUES ('$numero', '$data_emissao', '$valor_total', '$parcelas', $fk_fornecedores_id_fornecedor, '$caminho_xml')";
        
        mysqli_query($conexao, $sql);
        return mysqli_affected_rows($conexao);
    }

    public static function editarNotaFiscal($id_nota_fiscal, $numero, $data_emissao, $valor_total, $parcelas, $fk_fornecedores_id_fornecedor, $caminho_xml = null) {
        global $conexao;
        $id_nota_fiscal = mysqli_real_escape_string($conexao, $id_nota_fiscal);
        $numero = mysqli_real_escape_string($conexao, trim($numero));
        $data_emissao = mysqli_real_escape_string($conexao, trim($data_emissao));
        $valor_total = mysqli_real_escape_string($conexao, trim($valor_total));
        $parcelas = mysqli_real_escape_string($conexao, trim($parcelas));
        $fk_fornecedores_id_fornecedor = (int)$fk_fornecedores_id_fornecedor;
        $caminho_xml = mysqli_real_escape_string($conexao, $caminho_xml);

        $sql = "UPDATE notas_fiscais SET numero = '$numero', data_emissao = '$data_emissao', valor_total = '$valor_total', 
                parcelas = '$parcelas', fk_fornecedores_id_fornecedor = $fk_fornecedores_id_fornecedor";

        if (!empty($caminho_xml)) {
            $sql .= ", caminho_xml = '$caminho_xml'";
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

    public static function excluirCaminhoXML($id_nota_fiscal) {
        global $conexao;
        // Buscar o caminho do XML atual
        $nota = self::buscarNotaFiscalPorId($id_nota_fiscal);
        
        if ($nota && !empty($nota['caminho_xml'])) {
            $caminho_xml = $nota['caminho_xml'];
            
            // Deletar o caminho do XML no banco de dados
            $sql = "UPDATE notas_fiscais SET caminho_xml = NULL WHERE id_nota_fiscal = '$id_nota_fiscal'";
            mysqli_query($conexao, $sql);
            
            // Verificar se o arquivo existe e remover o arquivo físico
            $caminho_completo = __DIR__ . '/../../' . $caminho_xml; // Caminho relativo ao arquivo
            if (file_exists($caminho_completo)) {
                unlink($caminho_completo); // Excluir o arquivo físico
            }
        }
        return mysqli_affected_rows($conexao);
    }    

    public static function buscarNotaFiscalPorId($id_nota_fiscal) {
        global $conexao;
        $id_nota_fiscal = mysqli_real_escape_string($conexao, $id_nota_fiscal);
    
        $sql = "SELECT * FROM notas_fiscais WHERE id_nota_fiscal = '$id_nota_fiscal'";
        $result = mysqli_query($conexao, $sql);
        
        if (mysqli_num_rows($result) > 0) {
            return mysqli_fetch_assoc($result); // Retorna as informações da nota fiscal
        } else {
            return null; // Retorna null se não encontrar a nota fiscal
        }
    }    

    public static function listarNotasFiscaisComParcelas(){
        global $conexao;
        
        // Alterar a query para incluir a condição de que caminho_xml não deve ser nulo
        $sql = "
            SELECT 
                nf.*, 
                f.nome_fornecedor, 
                cp.parcela_atual, 
                cp.parcelas AS total_parcelas
            FROM notas_fiscais nf
            INNER JOIN fornecedores f ON nf.fk_fornecedores_id_fornecedor = f.id_fornecedor
            LEFT JOIN contas_pagar cp ON nf.id_nota_fiscal = cp.fk_notas_fiscais_id_nota_fiscal
            WHERE nf.ativo = TRUE
            AND nf.caminho_xml IS NOT NULL
        ";
    
        $notas_fiscais = mysqli_query($conexao, $sql);
        return $notas_fiscais;
    }
    
    public static function deletarNotaFiscal($id_nota_fiscal) {
        global $conexao;
        $sql = "DELETE FROM notas_fiscais WHERE id_nota_fiscal = '$id_nota_fiscal'";
        mysqli_query($conexao, $sql);
    }       
}
