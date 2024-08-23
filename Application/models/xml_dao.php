<?php
require 'conexao.php';

class XMLDAO {
    public static function cadastrarFornecedor($fornecedor) {
        global $conexao;
        
        $nome = mysqli_real_escape_string($conexao, $fornecedor['nome']);
        $cnpj = mysqli_real_escape_string($conexao, $fornecedor['cnpj']);
        $telefone = mysqli_real_escape_string($conexao, $fornecedor['telefone']);
        $endereco = mysqli_real_escape_string($conexao, $fornecedor['endereco']);
        $cidade = mysqli_real_escape_string($conexao, $fornecedor['cidade']);
        $estado = mysqli_real_escape_string($conexao, $fornecedor['estado']);
        $email = mysqli_real_escape_string($conexao, $fornecedor['email']);
        
        $sql = "SELECT id_fornecedor FROM fornecedores WHERE cnpj = '$cnpj' LIMIT 1";
        $result = mysqli_query($conexao, $sql);
        
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            return $row['id_fornecedor'];
        }

        $sql = "INSERT INTO fornecedores (nome_fornecedor, cnpj, telefone, endereco, email, fk_cidades_id_cidade, fk_estados_id_estado, ativo) 
                VALUES ('$nome', '$cnpj', '$telefone', '$endereco', '$email', $cidade, $estado, TRUE)";
        
        mysqli_query($conexao, $sql);
        return mysqli_insert_id($conexao);
    }

    public static function cadastrarMaterial($material) {
        global $conexao;

        $nome_material = mysqli_real_escape_string($conexao, trim($material['nome_material']));
        $valor_compra = mysqli_real_escape_string($conexao, trim($material['valor_compra']));
        $valor_venda = mysqli_real_escape_string($conexao, trim($material['valor_venda']));
        $quantidade = mysqli_real_escape_string($conexao, trim($material['quantidade']));
        $unidade_medida = mysqli_real_escape_string($conexao, trim($material['unidade_medida']));
        $fornecedor_id = (int)$material['fornecedor_id'];

        $sql = "INSERT INTO materiais (nome_material, valor_compra, valor_venda, quantidade, unidade_medida, fk_fornecedores_id_fornecedor, ativo) 
                VALUES ('$nome_material', '$valor_compra', '$valor_venda', '$quantidade', '$unidade_medida', $fornecedor_id, TRUE)";
        
        mysqli_query($conexao, $sql);
        return mysqli_affected_rows($conexao);
    }

    public static function cadastrarNotaFiscal($nota_fiscal) {
        global $conexao;

        $numero = mysqli_real_escape_string($conexao, $nota_fiscal['numero']);
        $data_emissao = mysqli_real_escape_string($conexao, $nota_fiscal['data_emissao']);
        $valor_total = mysqli_real_escape_string($conexao, $nota_fiscal['valor_total']);
        $parcelas = (int)$nota_fiscal['parcelas'];
        $caminho_xml = mysqli_real_escape_string($conexao, $nota_fiscal['caminho_xml']);
        $fornecedor_id = (int)$nota_fiscal['fornecedor_id'];

        $sql = "INSERT INTO notas_fiscais (numero, data_emissao, valor_total, parcelas, caminho_xml, fk_fornecedores_id_fornecedor, ativo) 
                VALUES ('$numero', '$data_emissao', '$valor_total', $parcelas, '$caminho_xml', $fornecedor_id, TRUE)";
        
        mysqli_query($conexao, $sql);
        return mysqli_insert_id($conexao);
    }
}
