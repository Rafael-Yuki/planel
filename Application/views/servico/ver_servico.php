<?php
session_start();
require('Application/models/conexao.php');
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Visualizar Serviço</title>
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
                        <h4>Visualizar Serviço
                            <a href="/planel/servicos" class="btn btn-danger float-end">
                                <span class="bi-arrow-left"></span>&nbsp;Voltar
                            </a>
                        </h4>
                    </div>
                    <div class="card-body">
                        <?php
                        if (isset($_GET['id'])) {
                            $servico_id = mysqli_real_escape_string($conexao, $_GET['id']);
                            $sql = "SELECT * FROM servicos WHERE id_servico = {$servico_id} AND ativo = TRUE";
                            $query = mysqli_query($conexao, $sql);
                            if (mysqli_num_rows($query) > 0) {
                                $servico = mysqli_fetch_array($query);
                                ?>
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <label for="nome_servico">Nome do Serviço</label>
                                            <p class="form-control" style="min-height: 38px;">
                                                <?= $servico['nome_servico']; ?>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="valor_servico">Valor do Serviço</label>
                                            <p class="form-control" style="min-height: 38px;">
                                                R$ <?= number_format($servico['valor_servico'], 2, ',', '.'); ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="descricao_servico">Descrição</label>
                                            <p class="form-control" style="min-height: 75px;">
                                                <?= $servico['descricao_servico']; ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            } else {
                                echo "<h5>Serviço não encontrado</h5>";
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
