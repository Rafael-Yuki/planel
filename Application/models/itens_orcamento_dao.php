<?php
require 'conexao.php';

class ItensOrcamentoDAO {
    public static function criarItemOrcamento($orcamentoId, $nome, $descricao, $valorTotal) {
        global $conexao;
        $sql = "INSERT INTO itens_orcamento (nome_item, descricao_item, valor_total_item, fk_orcamentos_id_orcamento) 
                VALUES ('$nome', '$descricao', $valorTotal, $orcamentoId)";
        
        if (mysqli_query($conexao, $sql)) {
            return mysqli_insert_id($conexao);
        }
        return -1;
    }

    public static function editarItemOrcamento($id_item_orcamento, $nome_item, $descricao_item, $valor_total_item) {
        global $conexao;

        $id_item_orcamento = mysqli_real_escape_string($conexao, $id_item_orcamento);
        $nome_item = mysqli_real_escape_string($conexao, $nome_item);
        $descricao_item = mysqli_real_escape_string($conexao, $descricao_item);
        $valor_total_item = (float)$valor_total_item;

        $sql = "UPDATE itens_orcamento 
                SET nome_item = '$nome_item', descricao_item = '$descricao_item', valor_total_item = '$valor_total_item'
                WHERE id_item_orcamento = '$id_item_orcamento'";

        mysqli_query($conexao, $sql);
        return mysqli_affected_rows($conexao);
    }

    public static function excluirItemOrcamento($id_item_orcamento) {
        global $conexao;
        $id_item_orcamento = mysqli_real_escape_string($conexao, $id_item_orcamento);

        $sql = "DELETE FROM itens_orcamento WHERE id_item_orcamento = '$id_item_orcamento'";

        mysqli_query($conexao, $sql);
        return mysqli_affected_rows($conexao);
    }

    public static function buscarItensOrcamento($orcamento_id) {
        global $conexao;
        $orcamento_id = mysqli_real_escape_string($conexao, $orcamento_id);

        $sql = "SELECT * FROM itens_orcamento WHERE fk_orcamentos_id_orcamento = '$orcamento_id'";
        $result = mysqli_query($conexao, $sql);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public static function adicionarMaterialAoItem($id_item_orcamento, $id_material, $quantidade, $preco) {
        global $conexao;
    
        // Buscar o nome do material
        $nome_material = MaterialDAO::buscarNomeMaterial($id_material);
        if (!$nome_material) {
            throw new Exception('Material não encontrado');
        }
    
        $quantidade = (float) $quantidade;
        $preco = (float) $preco;
    
        $sql = "INSERT INTO orcamento_material (fk_materiais_id_material, nome_orcamento_material, quantidade_material, valor_unitario, fk_itens_orcamento_id_item_orcamento)
                VALUES ('$id_material', '$nome_material', '$quantidade', '$preco', '$id_item_orcamento')";
    
        mysqli_query($conexao, $sql);
        return mysqli_affected_rows($conexao);
    }
    
    public static function adicionarServicoAoItem($id_item_orcamento, $id_servico, $quantidade, $preco) {
        global $conexao;
    
        // Buscar o nome do serviço
        $nome_servico = ServicoDAO::buscarNomeServico($id_servico);
        if (!$nome_servico) {
            throw new Exception('Serviço não encontrado');
        }
    
        $quantidade = (float) $quantidade;
        $preco = (float) $preco;
    
        $sql = "INSERT INTO orcamento_servico (fk_servicos_id_servico, nome_orcamento_servico, quantidade_servico, valor_unitario, fk_itens_orcamento_id_item_orcamento)
                VALUES ('$id_servico', '$nome_servico', '$quantidade', '$preco', '$id_item_orcamento')";
    
        mysqli_query($conexao, $sql);
        return mysqli_affected_rows($conexao);
    }
}
?>
