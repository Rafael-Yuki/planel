<?php
require_once('Application/models/conexao.php');

class DashboardDAO
{
    public static function obterContasAPagar()
    {
        global $conexao;
        $sql = "SELECT id_conta_pagar, valor, data_vencimento, parcelas FROM contas_pagar WHERE ativo = TRUE";
        return mysqli_query($conexao, $sql);
    }

    public static function obterNotasFiscais()
    {
        global $conexao;
        $sql = "SELECT id_nota_fiscal, numero, data_emissao, valor_total FROM notas_fiscais WHERE ativo = TRUE";
        return mysqli_query($conexao, $sql);
    }

    public static function obterOrcamentos()
    {
        global $conexao;
        $sql = "SELECT id_orcamento, data_orcamento, valor_total_orcamento, status FROM orcamentos WHERE ativo = TRUE";
        return mysqli_query($conexao, $sql);
    }

    public static function obterXmlImportados()
    {
        global $conexao;
        $sql = "SELECT nf.id_nota_fiscal, nf.numero, nf.data_emissao, nf.valor_total, nf.parcelas AS total_parcelas, nf.caminho_xml,
                       f.nome_fornecedor, cp.parcela_atual
                FROM notas_fiscais nf
                LEFT JOIN fornecedores f ON nf.fk_fornecedores_id_fornecedor = f.id_fornecedor
                LEFT JOIN contas_pagar cp ON nf.id_nota_fiscal = cp.fk_notas_fiscais_id_nota_fiscal
                WHERE nf.ativo = TRUE AND nf.caminho_xml IS NOT NULL";
        return mysqli_query($conexao, $sql);
    }

    public static function contarContasAPagar()
    {
        global $conexao;
        $sql = "SELECT COUNT(*) AS total FROM contas_pagar WHERE ativo = TRUE";
        $result = mysqli_query($conexao, $sql);
        $row = mysqli_fetch_assoc($result);
        return $row['total'];
    }

    public static function contarNotasFiscais()
    {
        global $conexao;
        $sql = "SELECT COUNT(*) AS total FROM notas_fiscais WHERE ativo = TRUE";
        $result = mysqli_query($conexao, $sql);
        $row = mysqli_fetch_assoc($result);
        return $row['total'];
    }

    public static function contarOrcamentos()
    {
        global $conexao;
        $sql = "SELECT COUNT(*) AS total FROM orcamentos WHERE ativo = TRUE";
        $result = mysqli_query($conexao, $sql);
        $row = mysqli_fetch_assoc($result);
        return $row['total'];
    }

    public static function contarXmlImportados()
    {
        global $conexao;
        $sql = "SELECT COUNT(*) AS total FROM notas_fiscais WHERE ativo = TRUE AND caminho_xml IS NOT NULL";
        $result = mysqli_query($conexao, $sql);
        $row = mysqli_fetch_assoc($result);
        return $row['total'];
    }
}
