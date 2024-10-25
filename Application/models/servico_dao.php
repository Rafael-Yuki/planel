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

    public static function adicionarServicoAoOrcamento($itemId, $servicoId, $valorUnitario, $quantidade, $nomeServico) {
        global $conexao;
        $sql = "INSERT INTO orcamento_servico (fk_itens_orcamento_id_item_orcamento, fk_servicos_id_servico, valor_unitario, quantidade_servico, nome_orcamento_servico) 
                VALUES ('$itemId', $servicoId, $valorUnitario, $quantidade, '$nomeServico')";
        
        return mysqli_query($conexao, $sql);
    }    

    public static function buscarNomeServico($id_servico) {
        global $conexao;
        $sql = "SELECT nome_servico FROM servicos WHERE id_servico = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param('i', $id_servico);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $servico = $result->fetch_assoc();
            return $servico['nome_servico'];
        }   
        return null;
    }

    public static function buscarPrecoServico($id_servico) {
        global $conexao;
        $id_servico = mysqli_real_escape_string($conexao, $id_servico);

        $query = "SELECT valor_servico AS preco FROM servicos WHERE id_servico = '$id_servico' AND ativo = TRUE";
        $result = mysqli_query($conexao, $query);

        if (mysqli_num_rows($result) > 0) {
            return mysqli_fetch_assoc($result)['preco'];
        } else {
            return null;
        }
    }

    public static function editarServicoOrcamento($id_orcamento_servico, $id_servico, $quantidade_servico, $preco_servico) {
        global $conexao;
    
        $sql = "UPDATE orcamento_servico 
                SET fk_servicos_id_servico = '$id_servico', quantidade_servico = '$quantidade_servico', valor_unitario = '$preco_servico' 
                WHERE id_orcamento_servico = '$id_orcamento_servico'";
        
        mysqli_query($conexao, $sql);
        return mysqli_affected_rows($conexao);
    }    

    public static function excluirServicoOrcamento($id_orcamento_servico) {
        global $conexao;
        
        $id_orcamento_servico = mysqli_real_escape_string($conexao, $id_orcamento_servico);
        $sql = "DELETE FROM orcamento_servico WHERE id_orcamento_servico = '$id_orcamento_servico'";
        
        mysqli_query($conexao, $sql);
        return mysqli_affected_rows($conexao);
    }
}
?>
