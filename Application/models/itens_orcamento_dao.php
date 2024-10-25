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

    public static function listarItensPorOrcamento($orcamento_id) {
        global $conexao;
        $orcamento_id = mysqli_real_escape_string($conexao, $orcamento_id);
    
        // Consultar itens do orçamento
        $sql = "SELECT * FROM itens_orcamento WHERE fk_orcamentos_id_orcamento = '$orcamento_id'";
        $result = mysqli_query($conexao, $sql);
        $itens = mysqli_fetch_all($result, MYSQLI_ASSOC);
    
        // Para cada item, buscar materiais e serviços associados
        foreach ($itens as &$item) {
            $item['materiais'] = self::listarMateriaisPorItem($item['id_item_orcamento']);
            $item['servicos'] = self::listarServicosPorItem($item['id_item_orcamento']);
        }
    
        return $itens;
    }      

    public static function listarMateriaisPorItem($item_orcamento_id) {
        global $conexao;
        $item_orcamento_id = mysqli_real_escape_string($conexao, $item_orcamento_id);
    
        $sql = "SELECT om.*, m.nome_material, om.quantidade_material as quantidade, om.valor_unitario as preco_unitario
                FROM orcamento_material om
                LEFT JOIN materiais m ON om.fk_materiais_id_material = m.id_material
                WHERE om.fk_itens_orcamento_id_item_orcamento = '$item_orcamento_id'";
        $result = mysqli_query($conexao, $sql);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }    
    
    public static function listarServicosPorItem($item_orcamento_id) {
        global $conexao;
        $item_orcamento_id = mysqli_real_escape_string($conexao, $item_orcamento_id);
    
        $sql = "SELECT os.*, s.nome_servico, os.quantidade_servico as quantidade, os.valor_unitario as preco_unitario
                FROM orcamento_servico os
                LEFT JOIN servicos s ON os.fk_servicos_id_servico = s.id_servico
                WHERE os.fk_itens_orcamento_id_item_orcamento = '$item_orcamento_id'";
        $result = mysqli_query($conexao, $sql);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }    
    
}
?>
