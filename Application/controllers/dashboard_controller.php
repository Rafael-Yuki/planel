<?php
require_once('Application/models/dashboard_dao.php');

class DashboardController
{
    // Método para obter o multiplicador de lucro
    public static function obterMultiplicadorLucro() {
        require_once 'Application/controllers/multiplicador_controller.php';
        return obterMultiplicador();
    }

    // Método para atualizar o multiplicador no arquivo de configuração
    public static function atualizarMultiplicadorLucro($novoMultiplicador)
    {
        // Caminho do arquivo de configuração
        $configFile = 'config_multiplicador.php';
        $config = include $configFile;

        // Atualizar o valor do multiplicador
        $config['multiplicador_lucro'] = $novoMultiplicador;

        // Escrever de volta no arquivo de configuração
        $novoConteudo = '<?php return ' . var_export($config, true) . ';';
        file_put_contents($configFile, $novoConteudo);
    }

    public static function contarFornecedores()
    {
        return DashboardDAO::contarFornecedores();
    }

    public static function contarClientes()
    {
        return DashboardDAO::contarClientes();
    }

    public static function contarContasAPagar()
    {
        return DashboardDAO::contarContasAPagar();
    }

    public static function contarContasAReceber()
    {
        return DashboardDAO::contarContasAReceber();
    }

    public static function contarNotasFiscais()
    {
        return DashboardDAO::contarNotasFiscais();
    }

    public static function contarOrcamentos()
    {
        return DashboardDAO::contarOrcamentos();
    }

    public static function contarMateriais()
    {
        return DashboardDAO::contarMateriais();
    }

    public static function contarServicos()
    {
        return DashboardDAO::contarServicos();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['novo_multiplicador'])) {
    $novoMultiplicador = floatval($_POST['novo_multiplicador']);
    DashboardController::atualizarMultiplicadorLucro($novoMultiplicador);

    // Redirecionar para o dashboard com uma mensagem de sucesso
    $_SESSION['mensagem'] = 'Multiplicador de lucro atualizado com sucesso!';
    $_SESSION['mensagem_tipo'] = 'success';
    header('Location: /planel/dashboard');
    exit;
}
