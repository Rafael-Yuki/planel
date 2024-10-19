<?php
session_start();
require('Application/models/conexao.php');
require('Application/models/contas_receber_dao.php');
require('Application/models/parcelas_receber_dao.php');

function formatarMoeda($valor) {
    return 'R$ ' . number_format($valor, 2, ',', '.');
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Visualizar Conta a Receber</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body data-bs-theme="dark">
    <?php include(__DIR__ . '/../navbar.php');?>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Visualizar Conta a Receber
                            <a href="/planel/contas-a-receber" class="btn btn-danger float-end">
                            <span class="bi-arrow-left"></span>&nbsp;Voltar</a>
                        </h4>
                    </div>
                    <div class="card-body">
                        <?php
                        if (isset($_GET['id'])) {
                            $conta_receber_id = mysqli_real_escape_string($conexao, $_GET['id']);
                            $conta_receber = ContasReceberDAO::obterContaReceber($conta_receber_id);

                            if ($conta_receber) {
                                ?>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="orcamento">Orçamento</label>
                                            <p class="form-control" style="min-height: 38px;"><?= $conta_receber['nome_orcamento']; ?></p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="cliente">Cliente</label>
                                            <p class="form-control" style="min-height: 38px;"><?= $conta_receber['nome_cliente']; ?></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="valor">Valor</label>
                                            <p class="form-control" style="min-height: 38px;"><?= formatarMoeda($conta_receber['valor']); ?></p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="data_vencimento">Data de Vencimento</label>
                                            <p class="form-control" style="min-height: 38px;"><?= date('d/m/Y', strtotime($conta_receber['data_vencimento'])); ?></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="parcela_atual">Parcela Atual</label>
                                            <p class="form-control" style="min-height: 38px;"><?= $conta_receber['parcela_atual']; ?></p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="parcelas">Parcelas</label>
                                            <p class="form-control" style="min-height: 38px;"><?= $conta_receber['parcelas']; ?></p>
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <!-- Exibir Parcelas -->
                                <h5>Parcelas</h5>
                                <?php
                                $parcelas = ParcelasReceberDAO::listarParcelasPorConta($conta_receber_id);

                                if ($parcelas && mysqli_num_rows($parcelas) > 0) {
                                    while ($parcela = mysqli_fetch_assoc($parcelas)) {
                                        ?>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label>Valor da Parcela</label>
                                                    <p class="form-control" style="min-height: 38px;"><?= formatarMoeda($parcela['valor_parcela']); ?></p>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label>Vencimento da Parcela</label>
                                                    <p class="form-control" style="min-height: 38px;"><?= date('d/m/Y', strtotime($parcela['vencimento_parcela'])); ?></p>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label>Data de Recebimento</label>
                                                    <p class="form-control" style="min-height: 38px;"><?= $parcela['data_recebimento'] ? date('d/m/Y', strtotime($parcela['data_recebimento'])) : '-'; ?></p>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label>Tipo de Pagamento</label>
                                                    <p class="form-control" style="min-height: 38px;"><?= $parcela['tipo_pagamento'] ? $parcela['tipo_pagamento'] : '-'; ?></p>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <?php
                                    }
                                } else {
                                    echo "<p>Não há parcelas cadastradas para esta conta</p>";
                                }
                                ?>
                                <?php
                            } else {
                                echo "<h5>Conta a receber não encontrada</h5>";
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous">
    </script>
</body>
</html>
