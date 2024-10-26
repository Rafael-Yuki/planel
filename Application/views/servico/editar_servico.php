<?php
session_start();
require('Application/models/conexao.php');
mysqli_set_charset($conexao, "utf8");
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar Serviço</title>
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
                        <h4>Editar Serviço
                            <a href="/planel/servicos" class="btn btn-danger float-end">
                                <span class="bi-arrow-left"></span>&nbsp;Voltar
                            </a>
                        </h4>
                    </div>
                    <div class="card-body">
                        <?php
                        if (isset($_GET['id'])) {
                            $servico_id = mysqli_real_escape_string($conexao, $_GET['id']);
                            $sql = "SELECT * FROM servicos WHERE id_servico='$servico_id'";
                            $query = mysqli_query($conexao, $sql);
                            if (mysqli_num_rows($query) > 0) {
                                $servico = mysqli_fetch_array($query);
                                ?>
                                <form action="/planel/servico/atualizar" method="POST">
                                    <input type="hidden" name="servico_id" required value="<?= $servico['id_servico'] ?>">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="nome_servico">Nome do Serviço</label>
                                                <input type="text" name="nome_servico" value="<?= $servico['nome_servico'] ?>"
                                                    class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="valor_servico">Valor do Serviço</label>
                                                <input type="text" name="valor_servico" id="valor_servico" 
                                                    value="<?= number_format($servico['valor_servico'], 2, ',', '.') ?>"
                                                    class="form-control" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="descricao_servico">Descrição do Serviço</label>
                                        <textarea id="descricao_servico" name="descricao_servico" class="form-control"><?= $servico['descricao_servico'] ?></textarea>
                                    </div>

                                    <div class="mb-3 mt-4">
                                        <button type="submit" name="editar_servico" class="btn btn-primary">
                                            Salvar<span class="bi-save ms-2"></span>
                                        </button>
                                    </div>
                                </form>
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
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        document.getElementById('valor_servico').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = (value / 100).toFixed(2).replace('.', ',');
            e.target.value = value;
        });
    </script>
</body>
</html>
