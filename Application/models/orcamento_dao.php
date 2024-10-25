<?php
require 'conexao.php';

class OrcamentoDAO {
    public static function criarOrcamento($nome, $data, $validade, $status, $observacao, $caminhoArquivo, $clienteId) {
        global $conexao;
        $sql = "INSERT INTO orcamentos (nome_orcamento, data_orcamento, validade, status, observacao, caminho_arquivo, fk_clientes_id_cliente) 
                VALUES ('$nome', '$data', '$validade', '$status', '$observacao', '$caminhoArquivo', $clienteId)";
        
        if (mysqli_query($conexao, $sql)) {
            return mysqli_insert_id($conexao);
        }
        return -1;
    }

    public static function editarOrcamento($id_orcamento, $nome_orcamento, $data_orcamento, $validade, $status, $observacao, $fk_clientes_id_cliente, $caminho_arquivo = null) {
        global $conexao;
        $id_orcamento = mysqli_real_escape_string($conexao, $id_orcamento);
        $nome_orcamento = mysqli_real_escape_string($conexao, trim($nome_orcamento));
        $data_orcamento = mysqli_real_escape_string($conexao, trim($data_orcamento));
        $validade = mysqli_real_escape_string($conexao, trim($validade));
        $status = mysqli_real_escape_string($conexao, trim($status));
        $observacao = mysqli_real_escape_string($conexao, trim($observacao));
        $fk_clientes_id_cliente = (int)$fk_clientes_id_cliente;
        $caminho_arquivo = mysqli_real_escape_string($conexao, $caminho_arquivo);

        $sql = "UPDATE orcamentos SET nome_orcamento = '$nome_orcamento', data_orcamento = '$data_orcamento', validade = '$validade', 
                status = '$status', observacao = '$observacao', fk_clientes_id_cliente = $fk_clientes_id_cliente";

        if (!empty($caminho_arquivo)) {
            $sql .= ", caminho_arquivo = '$caminho_arquivo'";
        }

        $sql .= " WHERE id_orcamento = '$id_orcamento'";
        
        mysqli_query($conexao, $sql);
        return mysqli_affected_rows($conexao);
    }

    public static function excluirOrcamento($id_orcamento) {
        global $conexao;
        $id_orcamento = mysqli_real_escape_string($conexao, $id_orcamento);

        $sql = "UPDATE orcamentos SET ativo = FALSE WHERE id_orcamento = '$id_orcamento'";
        
        mysqli_query($conexao, $sql);
        return mysqli_affected_rows($conexao);
    }

    public static function listarOrcamentos(){
        global $conexao;
        $sql = 'SELECT orcamentos.*, clientes.nome_cliente FROM orcamentos 
                INNER JOIN clientes ON orcamentos.fk_clientes_id_cliente = clientes.id_cliente
                WHERE orcamentos.ativo = TRUE';
        $orcamentos = mysqli_query($conexao, $sql);
        return $orcamentos;
    }

    public static function buscarOrcamentoPorId($id_orcamento) {
        global $conexao;
        $id_orcamento = mysqli_real_escape_string($conexao, $id_orcamento);

        $sql = "SELECT orcamentos.*, clientes.nome_cliente 
                FROM orcamentos 
                INNER JOIN clientes ON orcamentos.fk_clientes_id_cliente = clientes.id_cliente
                WHERE orcamentos.ativo = TRUE AND orcamentos.id_orcamento = '$id_orcamento'";
        
        $result = mysqli_query($conexao, $sql);
        if (mysqli_num_rows($result) > 0) {
            return mysqli_fetch_assoc($result);
        }
        return null;
    }
}
?>
