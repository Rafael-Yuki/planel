<?php
session_start();
require('Application/models/conexao.php');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Visualizar Fornecedor</title>
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
                        <h4>Visualizar Fornecedor
                            <a href="/planel/fornecedores" class="btn btn-danger float-end">
                            <span class="bi-arrow-left"></span>&nbsp;Voltar</a>
                        </h4>
                    </div>
                    <div class="card-body">
                        <?php
                        if (isset($_GET['id'])) {
                            $fornecedor_id = mysqli_real_escape_string($conexao, $_GET['id']);
                            $sql = "SELECT fornecedores.*, cidades.nome_cidade, estados.sigla_estado FROM fornecedores 
                                    INNER JOIN cidades ON fornecedores.fk_cidades_id_cidade = cidades.id_cidade
                                    INNER JOIN estados ON cidades.fk_estados_id_estado = estados.id_estado
                                    WHERE fornecedores.ativo = TRUE
                                      and fornecedores.id_fornecedor = {$fornecedor_id}";
                            $query = mysqli_query($conexao, $sql);
                            if (mysqli_num_rows($query) > 0) {
                                $fornecedor = mysqli_fetch_array($query);
                                ?>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="nome">Nome</label>
                                            <p class="form-control" style="min-height: 38px;">
                                                <?= $fornecedor['nome_fornecedor']; ?>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="cnpj">CNPJ</label>
                                            <p class="form-control" style="min-height: 38px;">
                                                <?= $fornecedor['cnpj']; ?>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="telefone">Telefone</label>
                                            <p class="form-control" style="min-height: 38px;">
                                                <?= $fornecedor['telefone']; ?>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="email">E-mail</label>
                                            <p class="form-control" style="min-height: 38px;">
                                                <?= $fornecedor['email']; ?>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="estado">Estado</label>
                                            <p class="form-control" style="min-height: 38px;">
                                                <?= $fornecedor['sigla_estado']; ?>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="cidade">Cidade</label>
                                            <p class="form-control" style="min-height: 38px;">
                                                <?= $fornecedor['nome_cidade']; ?>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="endereco">Endereço</label>
                                            <p class="form-control" style="min-height: 38px;">
                                                <?= $fornecedor['endereco']; ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            } else {
                                echo "<h5>Fornecedor não encontrado</h5>";
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
