<?php
require 'conexao.php';

class MaterialDAO {
    public static function criarMaterial($nome_material, $valor_compra, $valor_venda, $data_compra, $quantidade, $unidade_medida, $fk_fornecedores_id_fornecedor, $ncm, $fk_notas_fiscais_id_nota_fiscal = null) {
        global $conexao;
    
        $nome_material = mysqli_real_escape_string($conexao, trim($nome_material));
        $valor_compra = str_replace(',', '.', mysqli_real_escape_string($conexao, trim($valor_compra)));
        $valor_venda = str_replace(',', '.', mysqli_real_escape_string($conexao, trim($valor_venda)));
        $data_compra = mysqli_real_escape_string($conexao, trim($data_compra));
        $quantidade = mysqli_real_escape_string($conexao, trim($quantidade));
        $unidade_medida = mysqli_real_escape_string($conexao, trim($unidade_medida));
        $ncm = mysqli_real_escape_string($conexao, trim($ncm));
        $fk_notas_fiscais_id_nota_fiscal = !empty($fk_notas_fiscais_id_nota_fiscal) ? $fk_notas_fiscais_id_nota_fiscal : 'NULL';
        
        $sql = "INSERT INTO materiais (nome_material, valor_compra, valor_venda, data_compra, quantidade, unidade_medida, fk_fornecedores_id_fornecedor, ativo, ncm, fk_notas_fiscais_id_nota_fiscal) 
                VALUES ('$nome_material', '$valor_compra', '$valor_venda', '$data_compra', '$quantidade', '$unidade_medida', $fk_fornecedores_id_fornecedor, TRUE, '$ncm', $fk_notas_fiscais_id_nota_fiscal)";
        
        mysqli_query($conexao, $sql);
        return mysqli_affected_rows($conexao);
    }    

    public static function editarMaterial($id, $nome_material, $valor_compra, $valor_venda, $data_compra, $quantidade, $unidade_medida, $fk_fornecedores_id_fornecedor = null, $fk_nota_fiscal_id = null) {
        global $conexao;
    
        $id = mysqli_real_escape_string($conexao, $id);
        $nome_material = mysqli_real_escape_string($conexao, trim($nome_material));
        $valor_compra = str_replace(',', '.', mysqli_real_escape_string($conexao, $valor_compra));
        $valor_venda = str_replace(',', '.', mysqli_real_escape_string($conexao, $valor_venda));
        $data_compra = mysqli_real_escape_string($conexao, $data_compra);
        $quantidade = mysqli_real_escape_string($conexao, $quantidade);
        $unidade_medida = mysqli_real_escape_string($conexao, trim($unidade_medida));   
        $fornecedor_val = !empty($fk_fornecedores_id_fornecedor) ? $fk_fornecedores_id_fornecedor : 'NULL';
        $nota_fiscal_val = !empty($fk_nota_fiscal_id) ? $fk_nota_fiscal_id : 'NULL';
    
        $sql = "UPDATE materiais 
                SET nome_material = '$nome_material', valor_compra = '$valor_compra', valor_venda = '$valor_venda', 
                    data_compra = '$data_compra', quantidade = '$quantidade', unidade_medida = '$unidade_medida', 
                    fk_fornecedores_id_fornecedor = $fornecedor_val, fk_notas_fiscais_id_nota_fiscal = $nota_fiscal_val
                WHERE id_material = '$id'";
        
        mysqli_query($conexao, $sql);
        return mysqli_affected_rows($conexao);
    }    

    public static function atualizarMaterial($id, $nova_quantidade, $valor_compra, $valor_venda, $data_compra) {
        global $conexao;

        $id = mysqli_real_escape_string($conexao, $id);
        $nova_quantidade = mysqli_real_escape_string($conexao, $nova_quantidade);
        $valor_compra = mysqli_real_escape_string($conexao, $valor_compra);
        $valor_venda = mysqli_real_escape_string($conexao, $valor_venda);
        $data_compra = mysqli_real_escape_string($conexao, $data_compra);

        $sql = "UPDATE materiais 
                SET quantidade = '$nova_quantidade', valor_compra = '$valor_compra', valor_venda = '$valor_venda', data_compra = '$data_compra' 
                WHERE id_material = '$id'";
        
        mysqli_query($conexao, $sql);
        return mysqli_affected_rows($conexao);
    }

    public static function excluirMaterial($id) {
        global $conexao;
        $id = mysqli_real_escape_string($conexao, $id);

        $sql = "UPDATE materiais SET ativo = FALSE WHERE id_material = '$id'";
        
        mysqli_query($conexao, $sql);
        return mysqli_affected_rows($conexao);
    }

    public static function listarMateriais(){
        global $conexao;
        $sql = 'SELECT materiais.*, 
                       fornecedores.nome_fornecedor, 
                       notas_fiscais.numero AS numero_nota_fiscal 
                FROM materiais 
                LEFT JOIN fornecedores ON materiais.fk_fornecedores_id_fornecedor = fornecedores.id_fornecedor
                LEFT JOIN notas_fiscais ON materiais.fk_notas_fiscais_id_nota_fiscal = notas_fiscais.id_nota_fiscal
                WHERE materiais.ativo = TRUE';
        $materiais = mysqli_query($conexao, $sql);
        return $materiais;
    }    

    public static function deletarMateriaisPorNotaFiscal($id_nota_fiscal) {
        global $conexao;
        $sql = "DELETE FROM materiais WHERE fk_notas_fiscais_id_nota_fiscal = '$id_nota_fiscal'";
        mysqli_query($conexao, $sql);
    }      

    public static function adicionarMaterialOrcamento($orcamento_id, $nome_material, $quantidade, $preco) {
        global $conexao;

        $nome_material = mysqli_real_escape_string($conexao, $nome_material);
        $quantidade = (float) $quantidade;
        $preco = (float) $preco;

        $sql = "INSERT INTO orcamento_material (nome_orcamento_material, quantidade_material, valor_unitario, fk_orcamentos_id_orcamento)
                VALUES ('$nome_material', '$quantidade', '$preco', '$orcamento_id')";
        
        mysqli_query($conexao, $sql);
    } 
}
