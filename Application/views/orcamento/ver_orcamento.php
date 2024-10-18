<?php
session_start();
require('Application/models/conexao.php');
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
                            $sql = "SELECT orcamentos.*, clientes.nome_cliente 
                                    FROM orcamentos 
                                    INNER JOIN clientes ON orcamentos.fk_clientes_id_cliente = clientes.id_cliente
                                    WHERE orcamentos.ativo = TRUE AND orcamentos.id_orcamento = {$orcamento_id}";
                            $query = mysqli_query($conexao, $sql);
                            if (mysqli_num_rows($query) > 0) {
                                $orcamento = mysqli_fetch_array($query);
                                ?>
                                <!-- Informações do orçamento em 3 colunas -->
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="nome_orcamento">Nome do Orçamento</label>
                                            <p class="form-control">
                                                <?= $orcamento['nome_orcamento']; ?>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="cliente">Cliente</label>
                                            <p class="form-control">
                                                <?= $orcamento['nome_cliente']; ?>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="data_orcamento">Data do Orçamento</label>
                                            <p class="form-control">
                                                <?= date('d/m/Y', strtotime($orcamento['data_orcamento'])); ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="validade">Validade</label>
                                            <p class="form-control">
                                                <?= date('d/m/Y', strtotime($orcamento['validade'])); ?>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="status">Status</label>
                                            <p class="form-control">
                                                <?= $orcamento['status']; ?>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <!-- Campo de anexo ajustado para seguir o mesmo estilo dos outros campos -->
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

                                <!-- Observação ocupando a linha inteira -->
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="observacao">Observação</label>
                                            <p class="form-control">
                                                <?= $orcamento['observacao']; ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Materiais relacionados ao orçamento -->
                                <div class="mt-4">
                                    <h4>Materiais no Orçamento</h4>
                                    <?php
                                    $sql_materiais = "SELECT om.*, m.nome_material 
                                                      FROM orcamento_material om
                                                      LEFT JOIN materiais m ON om.fk_materiais_id_material = m.id_material
                                                      WHERE om.fk_orcamentos_id_orcamento = {$orcamento_id}";
                                    $query_materiais = mysqli_query($conexao, $sql_materiais);

                                    if (mysqli_num_rows($query_materiais) > 0) {
                                        ?>
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
                                                while ($material = mysqli_fetch_assoc($query_materiais)) {
                                                    $valor_total = $material['quantidade_material'] * $material['valor_unitario'];
                                                    ?>
                                                    <tr>
                                                        <td><?= $material['nome_material'] ?: $material['nome_orcamento_material']; ?></td>
                                                        <td><?= $material['quantidade_material']; ?></td>
                                                        <td>R$ <?= number_format($material['valor_unitario'], 2, ',', '.'); ?></td>
                                                        <td>R$ <?= number_format($valor_total, 2, ',', '.'); ?></td>
                                                    </tr>
                                                    <?php
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                        <?php
                                    } else {
                                        echo '<p>Nenhum material adicionado a este orçamento.</p>';
                                    }
                                    ?>
                                </div>

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
