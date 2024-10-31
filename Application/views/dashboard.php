<?php
session_start();
require('Application/controllers/dashboard_controller.php');
require('Application/models/contas_pagar_dao.php');
require('Application/models/nota_fiscal_dao.php');
require('Application/models/orcamento_dao.php');
require('Application/models/fornecedor_dao.php');
require('Application/models/cliente_dao.php');
require('Application/models/material_dao.php');
require('Application/models/servico_dao.php');
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .card {
            height: 100%;
            border: 1px solid transparent;
        }
        
        /* Estilo para modo escuro */
        body[data-bs-theme="dark"] .card {
            border-color: #ffffff;
        }

        /* Estilo para modo claro */
        body[data-bs-theme="light"] .card {
            border-color: #000000; 
        }
    </style>
</head>

<body data-bs-theme="dark">
    <?php include('navbar.php'); ?>

    <div class="container-fluid mt-4">
        <!-- Mensagem de feedback -->
        <?php if (isset($_SESSION['mensagem'])): ?>
            <div class="alert alert-<?= $_SESSION['mensagem_tipo'] ?> alert-dismissible fade show" role="alert">
                <?= $_SESSION['mensagem'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['mensagem']); ?>
        <?php endif; ?>

        <!-- Cards de resumo -->
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="card border-responsive">
                    <div class="card-body text-center">
                        <i class="bi bi-building" style="font-size: 2rem;"></i>
                        <h5 class="card-title">Fornecedores</h5>
                        <h2 class="mb-0"><?= DashboardController::contarFornecedores(); ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card border-responsive">
                    <div class="card-body text-center">
                        <i class="bi bi-person" style="font-size: 2rem;"></i>
                        <h5 class="card-title">Clientes</h5>
                        <h2 class="mb-0"><?= DashboardController::contarClientes(); ?></h2>
                    </div>
                </div>
            </div>

            <!-- Card de Multiplicador de Lucro -->
            <div class="col-md-4 mb-3">
                <div class="card border-responsive">
                    <div class="card-body text-center">
                        <h5 class="card-title">Multiplicador de Lucro</h5>
                        <h2 class="card-text">
                            <?= number_format(DashboardController::obterMultiplicadorLucro(), 2, ',', '.') ?>
                        </h2>
                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#editarMultiplicadorModal">
                            <i class="bi bi-pencil-fill me-2 responsive"></i>Editar Multiplicador
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <div class="card border-responsive">
                    <div class="card-body text-center">
                        <i class="bi bi-currency-dollar" style="font-size: 2rem;"></i>
                        <h5 class="card-title">Contas a Pagar</h5>
                        <h2 class="mb-0"><?= DashboardController::contarContasAPagar(); ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card border-responsive">
                    <div class="card-body text-center">
                        <i class="bi bi-wallet" style="font-size: 2rem;"></i>
                        <h5 class="card-title">Contas a Receber</h5>
                        <h2 class="mb-0"><?= DashboardController::contarContasAReceber(); ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card border-responsive">
                    <div class="card-body text-center">
                        <i class="bi bi-file-earmark-text" style="font-size: 2rem;"></i>
                        <h5 class="card-title">Orçamentos</h5>
                        <h2 class="mb-0"><?= DashboardController::contarOrcamentos(); ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card border-responsive">
                    <div class="card-body text-center">
                        <i class="bi-box-seam" style="font-size: 2rem;"></i>
                        <h5 class="card-title">Materiais</h5>
                        <h2 class="mb-0"><?= DashboardController::contarMateriais(); ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card border-responsive">
                    <div class="card-body text-center">
                        <i class="bi bi-tools" style="font-size: 2rem;"></i>
                        <h5 class="card-title">Serviços</h5>
                        <h2 class="mb-0"><?= DashboardController::contarServicos(); ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card border-responsive">
                    <div class="card-body text-center">
                        <i class="bi-receipt" style="font-size: 2rem;"></i>
                        <h5 class="card-title">Notas Fiscais</h5>
                        <h2 class="mb-0"><?= DashboardController::contarNotasFiscais(); ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal para Editar Multiplicador -->
        <div class="modal fade" id="editarMultiplicadorModal" tabindex="-1" aria-labelledby="editarMultiplicadorLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editarMultiplicadorLabel">Editar Multiplicador de Lucro</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formMultiplicador" action="multiplicador-lucro" method="POST">
                            <div class="mb-3">
                                <label for="multiplicador" class="form-label">Novo Multiplicador</label>
                                <input type="number" step="0.01" min="0" class="form-control" id="multiplicador" name="novo_multiplicador" required>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal"><span class="bi-arrow-left"></span>&nbsp;Voltar</button>
                        <button type="submit" form="formMultiplicador" class="btn btn-primary">Salvar<span class="bi-save ms-2"></span></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#contasPagarTable, #notasFiscaisTable, #orcamentosTable, #xmlTable').DataTable({
                "language": {
                    "url": "/planel/public/js/pt-BR.json"
                }
            });
        });
    </script>
</body>
</html>
