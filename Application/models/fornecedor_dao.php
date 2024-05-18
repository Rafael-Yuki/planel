<?php
require 'conexao.php';

class FornecedorDAO {
    public static function criarFornecedor($nome, $cnpj, $telefone, $email, $endereco) {
        global $conexao;
        $nome = mysqli_real_escape_string($conexao, trim($nome));
        $cnpj = mysqli_real_escape_string($conexao, trim($cnpj));
        $telefone = mysqli_real_escape_string($conexao, trim($telefone));
        $email = mysqli_real_escape_string($conexao, trim($email));
        $endereco = mysqli_real_escape_string($conexao, trim($endereco));

        $sql = "INSERT INTO fornecedores (nome_fornecedor, telefone, endereco, email, cnpj) VALUES ('$nome', '$telefone', '$endereco', '$email', '$cnpj')";
        
        mysqli_query($conexao, $sql);
        return mysqli_affected_rows($conexao);
    }

    public static function editarFornecedor($id, $nome, $cnpj, $telefone, $email, $endereco) {
        global $conexao;
        $id = mysqli_real_escape_string($conexao, $id);
        $nome = mysqli_real_escape_string($conexao, trim($nome));
        $cnpj = mysqli_real_escape_string($conexao, trim($cnpj));
        $telefone = mysqli_real_escape_string($conexao, trim($telefone));
        $email = mysqli_real_escape_string($conexao, trim($email));
        $endereco = mysqli_real_escape_string($conexao, trim($endereco));

        $sql = "UPDATE fornecedores SET nome_fornecedor = '$nome', telefone = '$telefone', endereco = '$endereco', email = '$email', cnpj = '$cnpj' WHERE id_fornecedor = '$id'";
        
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