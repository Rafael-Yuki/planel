<?php
require 'conexao.php';

class ServicoDAO {
    public static function criarServico($nome_servico, $valor_servico, $descricao_servico) {
        global $conexao;
    
        $nome_servico = mysqli_real_escape_string($conexao, trim($nome_servico));
        $valor_servico = str_replace(',', '.', mysqli_real_escape_string($conexao, trim($valor_servico)));
        $descricao_servico = mysqli_real_escape_string($conexao, trim($descricao_servico));

        $sql = "INSERT INTO servicos (nome_servico, valor_servico, descricao_servico, ativo) 
                VALUES ('$nome_servico', '$valor_servico', '$descricao_servico', TRUE)";
        
        mysqli_query($conexao, $sql);
        return mysqli_affected_rows($conexao);
    }    

    public static function editarServico($id, $nome_servico, $valor_servico, $descricao_servico) {
        global $conexao;
    
        $id = mysqli_real_escape_string($conexao, $id);
        $nome_servico = mysqli_real_escape_string($conexao, trim($nome_servico));
        $valor_servico = str_replace(',', '.', mysqli_real_escape_string($conexao, $valor_servico));
        $descricao_servico = mysqli_real_escape_string($conexao, $descricao_servico);
    
        $sql = "UPDATE servicos 
                SET nome_servico = '$nome_servico', valor_servico = '$valor_servico', descricao_servico = '$descricao_servico'
                WHERE id_servico = '$id'";
        
        mysqli_query($conexao, $sql);
        return mysqli_affected_rows($conexao);
    }    

    public static function excluirServico($id) {
        global $conexao;
        $id = mysqli_real_escape_string($conexao, $id);

        $sql = "UPDATE servicos SET ativo = FALSE WHERE id_servico = '$id'";
        
        mysqli_query($conexao, $sql);
        return mysqli_affected_rows($conexao);
    }

    public static function listarServicos() {
        global $conexao;
        $sql = "SELECT * FROM servicos WHERE ativo = TRUE";
        $servicos = mysqli_query($conexao, $sql);
        return $servicos;
    }    

    public static function adicionarServicoOrcamento($orcamento_id, $nome_servico, $quantidade, $preco) {
        global $conexao;

        $nome_servico = mysqli_real_escape_string($conexao, $nome_servico);
        $quantidade = (float) $quantidade;
        $preco = (float) $preco;

        $sql = "INSERT INTO orcamento_servico (nome_orcamento_servico, quantidade_servico, valor_unitario, fk_orcamentos_id_orcamento)
                VALUES ('$nome_servico', '$quantidade', '$preco', '$orcamento_id')";
        
        mysqli_query($conexao, $sql);
    }
}
