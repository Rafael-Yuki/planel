<?php
session_start();
require('Application/models/conexao.php');
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Visualizar Nota Fiscal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" 
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body data-bs-theme="dark">
    <?php include(__DIR__ . '/../navbar.php');?>
    <div class="container mt-5">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <h4>Visualizar Nota Fiscal
                            <a href="/planel/notas-fiscais" class="btn btn-danger float-end">
                            <span class="bi-arrow-left"></span>&nbsp;Voltar</a>
                        </h4>
                    </div>
                    <div class="card-body">
                        <?php
                        if (isset($_GET['id'])) {
                            $nota_fiscal_id = mysqli_real_escape_string($conexao, $_GET['id']);
                            $sql = "SELECT notas_fiscais.*, fornecedores.nome_fornecedor FROM notas_fiscais 
                                    INNER JOIN fornecedores ON notas_fiscais.fk_fornecedores_id_fornecedor = fornecedores.id_fornecedor
                                    WHERE notas_fiscais.ativo = TRUE AND notas_fiscais.id_nota_fiscal = {$nota_fiscal_id}";
                            $query = mysqli_query($conexao, $sql);
                            if (mysqli_num_rows($query) > 0) {
                                $nota_fiscal = mysqli_fetch_array($query);
                                ?>
                                <div class="mb-3">
                                    <label for="numero">Número da Nota Fiscal</label>
                                    <p class="form-control" style="min-height: 38px;">
                                        <?= $nota_fiscal['numero']; ?>
                                    </p>
                                </div>
                                <div class="mb-3">
                                    <label for="fornecedor">Fornecedor</label>
                                    <p class="form-control" style="min-height: 38px;">
                                        <?= $nota_fiscal['nome_fornecedor']; ?>
                                    </p>
                                </div>
                                <div class="mb-3">
                                    <label for="data_emissao">Data de Emissão</label>
                                    <p class="form-control" style="min-height: 38px;">
                                        <?= date('d/m/Y', strtotime($nota_fiscal['data_emissao'])); ?>
                                    </p>
                                </div>
                                <div class="mb-3">
                                    <label for="valor_total">Valor Total</label>
                                    <p class="form-control" style="min-height: 38px;">
                                        <?= number_format($nota_fiscal['valor_total'], 2, ',', '.'); ?>
                                    </p>
                                </div>
                                <div class="mb-3">
                                    <label for="parcelas">Parcelas</label>
                                    <p class="form-control" style="min-height: 38px;">
                                        <?= $nota_fiscal['parcelas']; ?>
                                    </p>
                                </div>
                                <div class="mb-3">
                                    <h4>Visualizar XML</h4>

                                    <?php if (!empty($nota_fiscal['caminho_xml'])): ?>
                                        <a href="<?= '/planel/upload?file=' . urlencode(basename($nota_fiscal['caminho_xml'])); ?>" 
                                        class="btn btn-primary mt-2" target="_blank">
                                        <span class="bi-file-earmark-text-fill"></span>&nbsp;<?= basename($nota_fiscal['caminho_xml']); ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">Nenhum XML</span>
                                    <?php endif; ?>
                                </div>
                                <?php
                            } else {
                                echo "<h5>Nota Fiscal não encontrada</h5>";
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
