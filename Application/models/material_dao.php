<?php
require 'conexao.php';

class MaterialDAO {
    public static function criarMaterial($nome_material, $valor_compra, $valor_venda, $data_compra, $quantidade, $unidade_medida, $fk_fornecedores_id_fornecedor, $ncm) {
        global $conexao;

        $nome_material = mysqli_real_escape_string($conexao, trim($nome_material));
        $valor_compra = str_replace(',', '.', mysqli_real_escape_string($conexao, trim($valor_compra)));
        $valor_venda = str_replace(',', '.', mysqli_real_escape_string($conexao, trim($valor_venda)));
        $data_compra = mysqli_real_escape_string($conexao, trim($data_compra));
        $quantidade = mysqli_real_escape_string($conexao, trim($quantidade));
        $unidade_medida = mysqli_real_escape_string($conexao, trim($unidade_medida));
        $fk_fornecedores_id_fornecedor = (int)$fk_fornecedores_id_fornecedor;
        $ncm = mysqli_real_escape_string($conexao, trim($ncm));

        $sql = "INSERT INTO materiais (nome_material, valor_compra, valor_venda, data_compra, quantidade, unidade_medida, fk_fornecedores_id_fornecedor, ativo, ncm) 
                VALUES ('$nome_material', '$valor_compra', '$valor_venda', '$data_compra', '$quantidade', '$unidade_medida', $fk_fornecedores_id_fornecedor, TRUE, '$ncm')";
        
        mysqli_query($conexao, $sql);
        return mysqli_affected_rows($conexao);
    }

    public static function editarMaterial($id, $nome_material, $valor_compra, $valor_venda, $data_compra, $quantidade, $unidade_medida, $fk_fornecedores_id_fornecedor) {
        global $conexao;
        $id = mysqli_real_escape_string($conexao, $id);
        $nome_material = mysqli_real_escape_string($conexao, trim($nome_material));
        $valor_compra = mysqli_real_escape_string($conexao, $valor_compra);
        $valor_venda = mysqli_real_escape_string($conexao, $valor_venda);
        $data_compra = mysqli_real_escape_string($conexao, $data_compra);
        $quantidade = mysqli_real_escape_string($conexao, $quantidade);
        $unidade_medida = mysqli_real_escape_string($conexao, trim($unidade_medida));
        $fk_fornecedores_id_fornecedor = mysqli_real_escape_string($conexao, $fk_fornecedores_id_fornecedor);

        $sql = "UPDATE materiais SET nome_material = '$nome_material', valor_compra = '$valor_compra', valor_venda = '$valor_venda', 
        data_compra = '$data_compra', quantidade = '$quantidade', unidade_medida = '$unidade_medida', fk_fornecedores_id_fornecedor = '$fk_fornecedores_id_fornecedor' 
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
        $sql = 'SELECT materiais.*, fornecedores.nome_fornecedor FROM materiais 
                INNER JOIN fornecedores ON materiais.fk_fornecedores_id_fornecedor = fornecedores.id_fornecedor
                WHERE materiais.ativo = TRUE';
        $materiais = mysqli_query($conexao, $sql);
        return $materiais;
    }
}
