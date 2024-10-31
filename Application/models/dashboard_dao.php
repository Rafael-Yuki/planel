<?php
require_once('Application/models/conexao.php');

class DashboardDAO
{
    public static function contarFornecedores()
    {
        global $conexao;
        $sql = "SELECT COUNT(*) AS total FROM fornecedores WHERE ativo = TRUE";
        $result = mysqli_query($conexao, $sql);
        $row = mysqli_fetch_assoc($result);
        return $row['total'];
    }

    public static function contarClientes()
    {
        global $conexao;
        $sql = "SELECT COUNT(*) AS total FROM clientes WHERE ativo = TRUE";
        $result = mysqli_query($conexao, $sql);
        $row = mysqli_fetch_assoc($result);
        return $row['total'];
    }

    public static function contarContasAPagar()
    {
        global $conexao;
        $sql = "SELECT COUNT(*) AS total FROM contas_pagar WHERE ativo = TRUE";
        $result = mysqli_query($conexao, $sql);
        $row = mysqli_fetch_assoc($result);
        return $row['total'];
    }

    public static function contarContasAReceber()
    {
        global $conexao;
        $sql = "SELECT COUNT(*) AS total FROM contas_receber WHERE ativo = TRUE";
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

    public static function contarMateriais()
    {
        global $conexao;
        $sql = "SELECT COUNT(*) AS total FROM materiais WHERE ativo = TRUE";
        $result = mysqli_query($conexao, $sql);
        $row = mysqli_fetch_assoc($result);
        return $row['total'];
    }

    public static function contarServicos()
    {
        global $conexao;
        $sql = "SELECT COUNT(*) AS total FROM servicos WHERE ativo = TRUE";
        $result = mysqli_query($conexao, $sql);
        $row = mysqli_fetch_assoc($result);
        return $row['total'];
    }
}
