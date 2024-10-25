<?php
session_start();
require('Application/models/conexao.php');
require('Application/models/orcamento_dao.php');
require('Application/models/itens_orcamento_dao.php');
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Visualizar Orçamento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" 
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .item-number {
            background-color: #495057;
            color: #fff;
            border: none;
            border-radius: .25rem;
            padding: 0.375rem 0.75rem;
            text-align: center;
            font-weight: bold;
            min-width: 50px;
        }
        .input-group .form-control {
            border-left: 0;
        }
    </style>
</head>

<body data-bs-theme="dark">
    <?php include(__DIR__ . '/../navbar.php'); ?>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Visualizar Orçamento
                            <a href="/planel/orcamentos" class="btn btn-danger float-end">
                                <span class="bi-arrow-left"></span>&nbsp;Voltar
                            </a>
                        </h4>
                    </div>
                    <div class="card-body">
                        <?php
                        if (isset($_GET['id'])) {
                            $orcamento_id = mysqli_real_escape_string($conexao, $_GET['id']);
                            $orcamento = OrcamentoDAO::buscarOrcamentoPorId($orcamento_id);

                            if ($orcamento) {
                                ?>
                                <!-- Informações do orçamento em 3 colunas -->
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="nome_orcamento">Nome do Orçamento</label>
                                            <p class="form-control"><?= $orcamento['nome_orcamento']; ?></p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="cliente">Cliente</label>
                                            <p class="form-control"><?= $orcamento['nome_cliente']; ?></p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="data_orcamento">Data do Orçamento</label>
                                            <p class="form-control"><?= date('d/m/Y', strtotime($orcamento['data_orcamento'])); ?></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="validade">Validade</label>
                                            <p class="form-control"><?= date('d/m/Y', strtotime($orcamento['validade'])); ?></p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="status">Status</label>
                                            <p class="form-control"><?= $orcamento['status']; ?></p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="anexo">Anexo</label>
                                            <p class="form-control">
                                                <?php if (!empty($orcamento['caminho_arquivo'])): ?>
                                                    <a href="<?= '/planel/upload?file=' . urlencode(basename($orcamento['caminho_arquivo'])); ?>" 
                                                    class="text-decoration-none" target="_blank">
                                                        <span class="bi-file-earmark-pdf-fill"></span>&nbsp;<?= basename($orcamento['caminho_arquivo']); ?>
                                                    </a>
                                                <?php else: ?>
                                                    Nenhum arquivo anexado.
                                                <?php endif; ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="observacao">Observação</label>
                                            <p class="form-control"><?= $orcamento['observacao']; ?></p>
                                        </div>
                                    </div>
                                </div>

                                <h4 class="mt-4">Itens do Orçamento</h4>
                                <hr>

                                <?php
                                $itens = ItensOrcamentoDAO::listarItensPorOrcamento($orcamento_id);
                                if (!empty($itens)) {
                                    $contadorItens = 1; 
                                    foreach ($itens as $item) {
                                        ?>
                                        <div class="input-group mt-3">
                                            <span class="item-number"><?= $contadorItens++; ?>º</span>
                                            <div class="form-control">
                                                <strong><?= $item['nome_item']; ?></strong>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label class="mt-4" for="descricao_item">Descrição</label>
                                                <p class="form-control"><?= $item['descricao_item']; ?></p>
                                            </div>
                                        </div>
                                        <?php
                                        $materiais = ItensOrcamentoDAO::listarMateriaisPorItem($item['id_item_orcamento']);
                                        if (!empty($materiais)) {
                                            ?>
                                            <div class="mt-4">
                                                <h4>Materiais</h4>
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>Nome do Material</th>
                                                            <th>Quantidade</th>
                                                            <th>Preço Unitário</th>
                                                            <th>Valor Total</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        foreach ($materiais as $material) {
                                                            $valor_total = $material['quantidade_material'] * $material['valor_unitario'];
                                                            ?>
                                                            <tr>
                                                                <td><?= $material['nome_material']; ?></td>
                                                                <td><?= $material['quantidade_material']; ?></td>
                                                                <td>R$ <?= number_format($material['valor_unitario'], 2, ',', '.'); ?></td>
                                                                <td>R$ <?= number_format($valor_total, 2, ',', '.'); ?></td>
                                                            </tr>
                                                            <?php
                                                        }
                                                        ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <?php
                                        }
                                        ?>

                                        <?php
                                        $servicos = ItensOrcamentoDAO::listarServicosPorItem($item['id_item_orcamento']);
                                        if (!empty($servicos)) {
                                            ?>
                                            <div class="mt-4">
                                                <h4>Serviços</h4>
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>Nome do Serviço</th>
                                                            <th>Quantidade</th>
                                                            <th>Preço Unitário</th>
                                                            <th>Valor Total</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        foreach ($servicos as $servico) {
                                                            $valor_total_servico = $servico['quantidade_servico'] * $servico['valor_unitario'];
                                                            ?>
                                                            <tr>
                                                                <td><?= $servico['nome_servico']; ?></td>
                                                                <td><?= $servico['quantidade_servico']; ?></td>
                                                                <td>R$ <?= number_format($servico['valor_unitario'], 2, ',', '.'); ?></td>
                                                                <td>R$ <?= number_format($valor_total_servico, 2, ',', '.'); ?></td>
                                                            </tr>
                                                            <?php
                                                        }
                                                        ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <?php
                                        }
                                        ?>

                                        <div class="row mt-3">
                                            <div class="col-md-4 ms-auto">
                                                <label for="valor_total_item">Valor Total do Item</label>
                                                <p class="form-control">
                                                    R$ <?= number_format($item['valor_total_item'], 2, ',', '.'); ?>
                                                </p>
                                            </div>
                                        </div>
                                        <hr>
                                        <?php
                                    }
                                } else {
                                    echo "<h5>Nenhum item encontrado para este orçamento.</h5>";
                                }
                                ?>
                                <?php
                            } else {
                                echo "<h5>Orçamento não encontrado</h5>";
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
