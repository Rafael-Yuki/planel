<?php
require 'conexao.php';

class FornecedorDAO {
    public static function criarFornecedor($nome, $cnpj, $telefone, $email, $endereco, $cidade_id, $estado_id) {
        global $conexao;
        $nome = mysqli_real_escape_string($conexao, trim($nome));
        $cnpj = mysqli_real_escape_string($conexao, trim($cnpj));
        $telefone = mysqli_real_escape_string($conexao, trim($telefone));
        $email = mysqli_real_escape_string($conexao, trim($email));
        $endereco = mysqli_real_escape_string($conexao, trim($endereco));
        $cidade_id = (int)$cidade_id;
        $estado_id = (int)$estado_id;

        $sql = "INSERT INTO fornecedores (nome_fornecedor, telefone, endereco, email, cnpj, fk_cidades_id_cidade, fk_estados_id_estado) 
        VALUES ('$nome', '$telefone', '$endereco', '$email', '$cnpj', $cidade_id, $estado_id)";
        
        mysqli_query($conexao, $sql);
        return mysqli_affected_rows($conexao);
    }

    public static function editarFornecedor($id, $nome, $cnpj, $telefone, $email, $endereco, $cidade_id) {
        global $conexao;
        $id = mysqli_real_escape_string($conexao, $id);
        $nome = mysqli_real_escape_string($conexao, trim($nome));
        $cnpj = mysqli_real_escape_string($conexao, trim($cnpj));
        $telefone = mysqli_real_escape_string($conexao, trim($telefone));
        $email = mysqli_real_escape_string($conexao, trim($email));
        $endereco = mysqli_real_escape_string($conexao, trim($endereco));
        $cidade_id = mysqli_real_escape_string($conexao, $cidade_id); 
    
        $sql = "UPDATE fornecedores SET nome_fornecedor = '$nome', telefone = '$telefone', endereco = '$endereco', 
        email = '$email', cnpj = '$cnpj', fk_cidades_id_cidade = '$cidade_id' WHERE id_fornecedor = '$id'";
        
        mysqli_query($conexao, $sql);
        return mysqli_affected_rows($conexao);
    }    

    public static function excluirFornecedor($id) {
        global $conexao;
        $id = mysqli_real_escape_string($conexao, $id);

        $sql = "UPDATE fornecedores SET ativo = FALSE WHERE id_fornecedor = '$id'";
        
        mysqli_query($conexao, $sql);
        return mysqli_affected_rows($conexao);
    }

    public static function listarFornecedores(){
        global $conexao;
        $sql = 'SELECT fornecedores.*, cidades.nome_cidade, estados.sigla_estado FROM fornecedores 
                INNER JOIN cidades ON fornecedores.fk_cidades_id_cidade = cidades.id_cidade
                INNER JOIN estados ON cidades.fk_estados_id_estado = estados.id_estado
                WHERE fornecedores.ativo = TRUE';
        $fornecedores = mysqli_query($conexao, $sql);
        return $fornecedores;
    }
}
?>