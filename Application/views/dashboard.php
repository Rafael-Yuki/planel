<?php
session_start();
require('Application/controllers/dashboard_controller.php');
require('Application/models/contas_pagar_dao.php');
require('Application/models/nota_fiscal_dao.php');
require('Application/models/orcamento_dao.php');
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
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
            <div class="col-md-2 mb-3">
                <div class="card bg-primary text-white h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-currency-dollar" style="font-size: 2rem;"></i>
                        <h5 class="card-title">Contas a Pagar</h5>
                        <h2 class="mb-0"><?= DashboardController::contarContasAPagar(); ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-2 mb-3">
                <div class="card bg-success text-white h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-receipt" style="font-size: 2rem;"></i>
                        <h5 class="card-title">Notas Fiscais</h5>
                        <h2 class="mb-0"><?= DashboardController::contarNotasFiscais(); ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-2 mb-3">
                <div class="card bg-warning text-white h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-file-earmark-text" style="font-size: 2rem;"></i>
                        <h5 class="card-title">Orçamentos</h5>
                        <h2 class="mb-0"><?= DashboardController::contarOrcamentos(); ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-2 mb-3">
                <div class="card bg-danger text-white h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-file-earmark-arrow-down" style="font-size: 2rem;"></i>
                        <h5 class="card-title">XMLs Importados</h5>
                        <h2 class="mb-0"><?= DashboardController::contarXmlImportados(); ?></h2>
                    </div>
                </div>
            </div>
            <!-- Card de Multiplicador de Lucro -->
            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    <div class="card-header text-center">
                        <h5 class="card-title">Multiplicador de Lucro</h5>
                    </div>
                    <div class="card-body text-center">
                        <h2 class="card-text">
                            <?= number_format(DashboardController::obterMultiplicadorLucro(), 2, ',', '.') ?>
                        </h2>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editarMultiplicadorModal">
                            <i class="bi bi-pencil-fill me-2"></i>Editar Multiplicador
                        </button>
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
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" form="formMultiplicador" class="btn btn-primary">Salvar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sistema de Abas -->
        <ul class="nav nav-tabs" id="dashboardTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="contas-tab" data-bs-toggle="tab" href="#contas" role="tab" aria-controls="contas" aria-selected="true">Contas a Pagar</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="notas-tab" data-bs-toggle="tab" href="#notas" role="tab" aria-controls="notas" aria-selected="false">Notas Fiscais</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="orcamentos-tab" data-bs-toggle="tab" href="#orcamentos" role="tab" aria-controls="orcamentos" aria-selected="false">Orçamentos</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="xml-tab" data-bs-toggle="tab" href="#xml" role="tab" aria-controls="xml" aria-selected="false">XMLs Importados</a>
            </li>
        </ul>

        <div class="tab-content mt-3" id="dashboardTabsContent">
            <div class="tab-pane fade show active" id="contas" role="tabpanel" aria-labelledby="contas-tab">
                <div class="table-responsive">
                    <table id="contasPagarTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Nota Fiscal</th>
                                <th>Fornecedor</th>
                                <th>Valor</th>
                                <th>Data de Vencimento</th>
                                <th>Parcela Atual</th>
                                <th>Parcelas</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $contas = ContasPagarDAO::listarContasPagar();
                            while ($conta = mysqli_fetch_assoc($contas)) {
                                echo "<tr>
                                        <td>{$conta['numero_nota_fiscal']}</td>
                                        <td>{$conta['nome_fornecedor']}</td>
                                        <td>R$ " . number_format($conta['valor'], 2, ',', '.') . "</td>
                                        <td>" . date('d/m/Y', strtotime($conta['data_vencimento'])) . "</td>
                                        <td>{$conta['parcela_atual']}</td>
                                        <td>{$conta['parcelas']}</td>
                                      </tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="tab-pane fade" id="notas" role="tabpanel" aria-labelledby="notas-tab">
                <div class="table-responsive">
                    <table id="notasFiscaisTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Número</th>
                                <th>Fornecedor</th>
                                <th>Data de Emissão</th>
                                <th>Valor Total</th>
                                <th>Parcelas</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $notas = NotaFiscalDAO::listarNotasFiscais();
                            while ($nota = mysqli_fetch_assoc($notas)) {
                                echo "<tr>
                                        <td>" . htmlspecialchars($nota['numero'], ENT_QUOTES, 'UTF-8') . "</td>
                                        <td>" . htmlspecialchars($nota['nome_fornecedor'], ENT_QUOTES, 'UTF-8') . "</td>
                                        <td>" . date('d/m/Y', strtotime($nota['data_emissao'])) . "</td>
                                        <td>R$ " . number_format($nota['valor_total'], 2, ',', '.') . "</td>
                                        <td>" . htmlspecialchars($nota['parcelas'], ENT_QUOTES, 'UTF-8') . "</td>
                                      </tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="tab-pane fade" id="orcamentos" role="tabpanel" aria-labelledby="orcamentos-tab">
                <div class="table-responsive">
                    <table id="orcamentosTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Nome do Orçamento</th>
                                <th>Cliente</th>
                                <th>Data do Orçamento</th>
                                <th>Validade</th>
                                <th>Valor Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $orcamentos = OrcamentoDAO::listarOrcamentos();
                            while ($orcamento = mysqli_fetch_assoc($orcamentos)) {
                                echo "<tr>
                                        <td>" . htmlspecialchars($orcamento['nome_orcamento'], ENT_QUOTES, 'UTF-8') . "</td>
                                        <td>" . htmlspecialchars($orcamento['nome_cliente'], ENT_QUOTES, 'UTF-8') . "</td>
                                        <td>" . date('d/m/Y', strtotime($orcamento['data_orcamento'])) . "</td>
                                        <td>" . date('d/m/Y', strtotime($orcamento['validade'])) . "</td>
                                        <td>R$ " . number_format($orcamento['valor_total_orcamento'], 2, ',', '.') . "</td>
                                        <td>" . htmlspecialchars($orcamento['status'], ENT_QUOTES, 'UTF-8') . "</td>
                                      </tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="tab-pane fade" id="xml" role="tabpanel" aria-labelledby="xml-tab">
                <div class="table-responsive">
                    <table id="xmlTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Número</th>
                                <th>Fornecedor</th>
                                <th>Data de Emissão</th>
                                <th>Valor Total</th>
                                <th>Parcela Atual</th>
                                <th>Total Parcelas</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $xml_importados = NotaFiscalDAO::listarNotasFiscaisComParcelas();
                            while ($xml = mysqli_fetch_assoc($xml_importados)) {
                                echo "<tr>
                                        <td>" . htmlspecialchars($xml['numero'], ENT_QUOTES, 'UTF-8') . "</td>
                                        <td>" . htmlspecialchars($xml['nome_fornecedor'], ENT_QUOTES, 'UTF-8') . "</td>
                                        <td>" . date('d/m/Y', strtotime($xml['data_emissao'])) . "</td>
                                        <td>R$ " . number_format($xml['valor_total'], 2, ',', '.') . "</td>
                                        <td>" . htmlspecialchars($xml['parcela_atual'], ENT_QUOTES, 'UTF-8') . "</td>
                                        <td>" . htmlspecialchars($xml['total_parcelas'], ENT_QUOTES, 'UTF-8') . "</td>
                                      </tr>";
                            }
                            ?>
                        </tbody>
                    </table>
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
