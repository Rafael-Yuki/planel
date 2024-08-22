<?php
session_start();
require('Application/models/conexao.php');
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Visualizar Material</title>
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
                        <h4>Visualizar Material
                            <a href="/planel/materiais" class="btn btn-danger float-end">
                            <span class="bi-arrow-left"></span>&nbsp;Voltar</a>
                        </h4>
                    </div>
                    <div class="card-body">
                        <?php
                        if (isset($_GET['id'])) {
                            $material_id = mysqli_real_escape_string($conexao, $_GET['id']);
                            $sql = "SELECT materiais.*, fornecedores.nome_fornecedor FROM materiais 
                                    INNER JOIN fornecedores ON materiais.fk_fornecedores_id_fornecedor = fornecedores.id_fornecedor
                                    WHERE materiais.ativo = TRUE
                                      and materiais.id_material = {$material_id}";
                            $query = mysqli_query($conexao, $sql);
                            if (mysqli_num_rows($query) > 0) {
                                $material = mysqli_fetch_array($query);
                                ?>
                                <div class="mb-3">
                                    <label for="nome_material">Nome do Material</label>
                                    <p class="form-control">
                                        <?= $material['nome_material']; ?>
                                    </p>
                                </div>
                                <div class="mb-3">
                                    <label for="quantidade">Quantidade</label>
                                    <p class="form-control">
                                        <?= $material['quantidade']; ?>
                                    </p>
                                </div>
                                <div class="mb-3">
                                    <label for="unidade_medida">Unidade de Medida</label>
                                    <p class="form-control">
                                        <?= $material['unidade_medida']; ?>
                                    </p>
                                </div>
                                <div class="mb-3">
                                    <label for="valor_compra">Valor de Compra</label>
                                    <p class="form-control">
                                        R$ <?= number_format($material['valor_compra'], 2, ',', '.'); ?>
                                    </p>
                                </div>
                                <div class="mb-3">
                                    <label for="valor_venda">Valor de Venda</label>
                                    <p class="form-control">
                                        R$ <?= number_format($material['valor_venda'], 2, ',', '.'); ?>
                                    </p>
                                </div>
                                <div class="mb-3">
                                    <label for="data_compra">Data da Compra</label>
                                    <p class="form-control">
                                        <?= date('d/m/Y', strtotime($material['data_compra'])); ?>
                                    </p>
                                </div>
                                <div class="mb-3">
                                    <label for="fornecedor">Fornecedor</label>
                                    <p class="form-control">
                                        <?= $material['nome_fornecedor']; ?>
                                    </p>
                                </div>
                                <?php
                            } else {
                                echo "<h5>Material não encontrado</h5>";
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
