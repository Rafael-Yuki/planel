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

        $sql = "INSERT INTO fornecedores (nome_fornecedor, telefone, endereco, email, cnpj, id_cidade) 
        VALUES ('$nome', '$telefone', '$endereco', '$email', '$cnpj', $cidade_id)";
        
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
        email = '$email', cnpj = '$cnpj', id_cidade = '$cidade_id' WHERE id_fornecedor = '$id'";
        
        mysqli_query($conexao, $sql);
        return mysqli_affected_rows($conexao);
    }    

    public static function deletarFornecedor($id) {
        global $conexao;
        $id = mysqli_real_escape_string($conexao, $id);

        $sql = "UPDATE fornecedores SET ativo = FALSE WHERE id_fornecedor = '$id'";
        
        mysqli_query($conexao, $sql);
        return mysqli_affected_rows($conexao);
    }
}
?>