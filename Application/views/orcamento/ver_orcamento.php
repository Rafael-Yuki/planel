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
    <?php include(__DIR__ . '/../navbar.php');?>
    <div class="container mt-5">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <h4>Visualizar Orçamento
                            <a href="/planel/orcamentos" class="btn btn-danger float-end">
                            <span class="bi-arrow-left"></span>&nbsp;Voltar</a>
                        </h4>
                    </div>
                    <div class="card-body">
                        <?php
                        if (isset($_GET['id'])) {
                            $orcamento_id = mysqli_real_escape_string($conexao, $_GET['id']);
                            $sql = "SELECT orcamentos.*, clientes.nome_cliente FROM orcamentos 
                                    INNER JOIN clientes ON orcamentos.fk_clientes_id_cliente = clientes.id_cliente
                                    WHERE orcamentos.ativo = TRUE AND orcamentos.id_orcamento = {$orcamento_id}";
                            $query = mysqli_query($conexao, $sql);
                            if (mysqli_num_rows($query) > 0) {
                                $orcamento = mysqli_fetch_array($query);
                                ?>
                                <div class="mb-3">
                                    <label for="nome_orcamento">Nome do Orçamento</label>
                                    <p class="form-control">
                                        <?= $orcamento['nome_orcamento']; ?>
                                    </p>
                                </div>
                                <div class="mb-3">
                                    <label for="cliente">Cliente</label>
                                    <p class="form-control">
                                        <?= $orcamento['nome_cliente']; ?>
                                    </p>
                                </div>
                                <div class="mb-3">
                                    <label for="data_orcamento">Data do Orçamento</label>
                                    <p class="form-control">
                                        <?= $orcamento['data_orcamento']; ?>
                                    </p>
                                </div>
                                <div class="mb-3">
                                    <label for="validade">Validade</label>
                                    <p class="form-control">
                                        <?= $orcamento['validade']; ?>
                                    </p>
                                </div>
                                <div class="mb-3">
                                    <label for="status">Status</label>
                                    <p class="form-control">
                                        <?= $orcamento['status']; ?>
                                    </p>
                                </div>
                                <div class="mb-3">
                                    <label for="observacao">Observação</label>
                                    <p class="form-control">
                                        <?= $orcamento['observacao']; ?>
                                    </p>
                                </div>
                                <div class="mb-3">
                                    <h4>Visualizar Anexo</h4>
                                    <?php if (!empty($orcamento['caminho_arquivo'])): ?>
                                        <a href="<?= '/planel/upload?file=' . urlencode(basename($orcamento['caminho_arquivo'])); ?>" 
                                        class="btn btn-primary mt-2"
                                        target="_blank">
                                            <span class="bi-file-earmark-pdf-fill"></span>&nbsp;<?= basename($orcamento['caminho_arquivo']); ?>
                                        </a>
                                    <?php else: ?>
                                        <p class="form-control">Nenhum arquivo anexado.</p>
                                    <?php endif; ?>
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
