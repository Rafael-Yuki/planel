<?php
require 'conexao.php';

class ClienteDAO {
    public static function criarCliente($nome, $cnpj, $telefone, $email, $endereco, $cidade_id, $estado_id) {
        global $conexao;
        $nome = mysqli_real_escape_string($conexao, trim($nome));
        $cnpj = mysqli_real_escape_string($conexao, trim($cnpj));
        $telefone = mysqli_real_escape_string($conexao, trim($telefone));
        $email = mysqli_real_escape_string($conexao, trim($email));
        $endereco = mysqli_real_escape_string($conexao, trim($endereco));
        $cidade_id = (int)$cidade_id;
        $estado_id = (int)$estado_id;

        $sql = "INSERT INTO clientes (nome_cliente, telefone, endereco, email, cnpj, fk_cidades_id_cidade, fk_estados_id_estado) 
        VALUES ('$nome', '$telefone', '$endereco', '$email', '$cnpj', $cidade_id, $estado_id)";
        
        mysqli_query($conexao, $sql);
        return mysqli_affected_rows($conexao);
    }

    public static function editarCliente($id, $nome, $cnpj, $telefone, $email, $endereco, $cidade_id) {
        global $conexao;
        $id = mysqli_real_escape_string($conexao, $id);
        $nome = mysqli_real_escape_string($conexao, trim($nome));
        $cnpj = mysqli_real_escape_string($conexao, trim($cnpj));
        $telefone = mysqli_real_escape_string($conexao, trim($telefone));
        $email = mysqli_real_escape_string($conexao, trim($email));
        $endereco = mysqli_real_escape_string($conexao, trim($endereco));
        $cidade_id = mysqli_real_escape_string($conexao, $cidade_id); 
    
        $sql = "UPDATE clientes SET nome_cliente = '$nome', telefone = '$telefone', endereco = '$endereco', 
        email = '$email', cnpj = '$cnpj', fk_cidades_id_cidade = '$cidade_id' WHERE id_cliente = '$id'";
        
        mysqli_query($conexao, $sql);
        return mysqli_affected_rows($conexao);
    }    

    public static function excluirCliente($id) {
        global $conexao;
        $id = mysqli_real_escape_string($conexao, $id);

        $sql = "UPDATE clientes SET ativo = FALSE WHERE id_cliente = '$id'";
        
        mysqli_query($conexao, $sql);
        return mysqli_affected_rows($conexao);
    }

    public static function listarClientes(){
        global $conexao;
        $sql = 'SELECT clientes.*, cidades.nome_cidade, estados.sigla_estado FROM clientes 
                INNER JOIN cidades ON clientes.fk_cidades_id_cidade = cidades.id_cidade
                INNER JOIN estados ON cidades.fk_estados_id_estado = estados.id_estado
                WHERE clientes.ativo = TRUE';
        $clientes = mysqli_query($conexao, $sql);
        return $clientes;
    }
}
?>