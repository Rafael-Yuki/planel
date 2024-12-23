<?php
session_start();
require('Application/models/conexao.php');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Visualizar Cliente</title>
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
                        <h4>Visualizar Cliente
                            <a href="/planel/clientes" class="btn btn-danger float-end">
                            <span class="bi-arrow-left"></span>&nbsp;Voltar</a>
                        </h4>
                    </div>
                    <div class="card-body">
                        <?php
                        if (isset($_GET['id'])) {
                            $cliente_id = mysqli_real_escape_string($conexao, $_GET['id']);
                            $sql = "SELECT clientes.*, cidades.nome_cidade, estados.sigla_estado FROM clientes 
                                    INNER JOIN cidades ON clientes.fk_cidades_id_cidade = cidades.id_cidade
                                    INNER JOIN estados ON cidades.fk_estados_id_estado = estados.id_estado
                                    WHERE clientes.ativo = TRUE
                                      and clientes.id_cliente = {$cliente_id}";
                            $query = mysqli_query($conexao, $sql);
                            if (mysqli_num_rows($query) > 0) {
                                $cliente = mysqli_fetch_array($query);
                                ?>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="nome">Nome</label>
                                            <p class="form-control" style="min-height: 38px;">
                                                <?= $cliente['nome_cliente']; ?>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="cnpj">CNPJ</label>
                                            <p class="form-control" style="min-height: 38px;">
                                                <?= $cliente['cnpj']; ?>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="telefone">Telefone</label>
                                            <p class="form-control" style="min-height: 38px;">
                                                <?= $cliente['telefone']; ?>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="email">E-mail</label>
                                            <p class="form-control" style="min-height: 38px;">
                                                <?= $cliente['email']; ?>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="estado">Estado</label>
                                            <p class="form-control" style="min-height: 38px;">
                                                <?= $cliente['sigla_estado']; ?>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="cidade">Cidade</label>
                                            <p class="form-control" style="min-height: 38px;">
                                                <?= $cliente['nome_cidade']; ?>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="endereco">Endereço</label>
                                            <p class="form-control" style="min-height: 38px;">
                                            <?=$cliente['endereco']?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            } else {
                                echo "<h5>Cliente não encontrado</h5>";
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
