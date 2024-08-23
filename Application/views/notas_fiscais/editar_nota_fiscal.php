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
    <title>Editar Nota Fiscal</title>
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
                        <h4>Editar Nota Fiscal
                            <a href="/planel/notas-fiscais" class="btn btn-danger float-end">
                            <span class="bi-arrow-left"></span>&nbsp;Voltar</a>
                        </h4>
                    </div>
                    <div class="card-body">
                        <?php
                        if (isset($_GET['id'])) {
                            $nota_fiscal_id = mysqli_real_escape_string($conexao, $_GET['id']);
                            $sql = "SELECT * FROM notas_fiscais WHERE id_nota_fiscal='$nota_fiscal_id'";
                            $query = mysqli_query($conexao, $sql);
                            if (mysqli_num_rows($query) > 0) {
                                $nota_fiscal = mysqli_fetch_array($query);
                                ?>
                                <form action="/planel/nota-fiscal/atualizar" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="nota_fiscal_id" required value="<?= $nota_fiscal['id_nota_fiscal'] ?>">
                                    <div class="mb-3">
                                        <label for="numero">Número da Nota Fiscal</label>
                                        <input type="text" name="numero" value="<?= $nota_fiscal['numero'] ?>"
                                            class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="fornecedor">Fornecedor</label>
                                        <select id="fornecedor" name="fornecedor" class="form-control" required>
                                            <option value="">Selecione um Fornecedor</option>
                                            <?php
                                            $query_fornecedores = "SELECT * FROM fornecedores WHERE ativo = TRUE";
                                            $result_fornecedores = mysqli_query($conexao, $query_fornecedores);
                                            while($row_fornecedor = mysqli_fetch_assoc($result_fornecedores)) {
                                                $selected = ($row_fornecedor['id_fornecedor'] == $nota_fiscal['fk_fornecedores_id_fornecedor']) ? 'selected' : '';
                                                echo "<option value='".$row_fornecedor['id_fornecedor']."' $selected>".$row_fornecedor['nome_fornecedor']."</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="data_emissao">Data de Emissão</label>
                                        <input type="date" name="data_emissao" value="<?= $nota_fiscal['data_emissao'] ?>"
                                            class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="valor_total">Valor Total</label>
                                        <input type="text" name="valor_total" value="<?= $nota_fiscal['valor_total'] ?>"
                                            class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="parcelas">Parcelas</label>
                                        <input type="text" name="parcelas" value="<?= $nota_fiscal['parcelas'] ?>"
                                            class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="caminho_xml">Arquivo XML</label>
                                        <input type="file" id="caminho_xml" name="caminho_xml" class="form-control">
                                        <?php if (!empty($nota_fiscal['caminho_xml'])): ?>
                                            <small class="form-text text-muted">
                                                Arquivo atual: <a href="<?= '/ver_xml?file=' . urlencode(basename($nota_fiscal['caminho_xml'])); ?>" target="_blank"><?= basename($nota_fiscal['caminho_xml']); ?></a>
                                            </small>
                                        <?php endif; ?>
                                    </div>
                                    <div class="mb-3">
                                        <button type="submit" name="editar_nota_fiscal" class="btn btn-primary">
                                            <span class="bi-save"></span>&nbsp;Salvar
                                        </button>
                                    </div>
                                </form>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
            crossorigin="anonymous">
    </script>
</body>
</html>
