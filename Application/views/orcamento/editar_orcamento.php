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
    <title>Editar Orçamento</title>
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
                        <h4>Editar Orçamento
                            <a href="/planel/orcamentos" class="btn btn-danger float-end">
                            <span class="bi-arrow-left"></span>&nbsp;Voltar</a>
                        </h4>
                    </div>
                    <div class="card-body">
                        <?php
                        if (isset($_GET['id'])) {
                            $orcamento_id = mysqli_real_escape_string($conexao, $_GET['id']);
                            $sql = "SELECT * FROM orcamentos WHERE id_orcamento='$orcamento_id'";
                            $query = mysqli_query($conexao, $sql);
                            if (mysqli_num_rows($query) > 0) {
                                $orcamento = mysqli_fetch_array($query);
                                ?>
                                <form action="/planel/orcamento/atualizar" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="orcamento_id" required value="<?= $orcamento['id_orcamento'] ?>">
                                    <div class="mb-3">
                                        <label for="nome_orcamento">Nome do Orçamento</label>
                                        <input type="text" name="nome_orcamento" value="<?= $orcamento['nome_orcamento'] ?>"
                                            class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="cliente">Cliente</label>
                                        <select id="cliente" name="cliente" class="form-control" required>
                                            <option value="">Selecione um Cliente</option>
                                            <?php
                                            $query_clientes = "SELECT * FROM clientes WHERE ativo = TRUE";
                                            $result_clientes = mysqli_query($conexao, $query_clientes);
                                            while($row_cliente = mysqli_fetch_assoc($result_clientes)) {
                                                $selected = ($row_cliente['id_cliente'] == $orcamento['fk_clientes_id_cliente']) ? 'selected' : '';
                                                echo "<option value='".$row_cliente['id_cliente']."' $selected>".$row_cliente['nome_cliente']."</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="data_orcamento">Data do Orçamento</label>
                                        <input type="date" name="data_orcamento" value="<?= $orcamento['data_orcamento'] ?>"
                                            class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="validade">Validade</label>
                                        <input type="date" name="validade" value="<?= $orcamento['validade'] ?>"
                                            class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="status">Status</label>
                                        <input type="text" name="status" value="<?= $orcamento['status'] ?>"
                                            class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="observacao">Observação</label>
                                        <textarea id="observacao" name="observacao" class="form-control"><?= $orcamento['observacao'] ?></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="arquivo_pdf">Arquivo PDF</label>
                                        <input type="file" id="arquivo_pdf" name="arquivo_pdf" class="form-control">
                                        <?php if (!empty($orcamento['caminho_arquivo'])): ?>
                                            <small class="form-text text-muted">
                                                Arquivo atual: <a href="<?= '/planel/upload?file=' . urlencode(basename($orcamento['caminho_arquivo'])); ?>" target="_blank"><?= basename($orcamento['caminho_arquivo']); ?></a>
                                            </small>
                                        <?php endif; ?>
                                    </div>
                                    <div class="mb-3">
                                        <button type="submit" name="editar_orcamento" class="btn btn-primary">
                                            <span class="bi-save"></span>&nbsp;Salvar
                                        </button>
                                    </div>
                                </form>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
            crossorigin="anonymous">
    </script>
</body>
</html>
