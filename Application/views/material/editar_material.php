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
    <title>Editar Material</title>
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
                        <h4>Editar Material
                            <a href="/planel/materiais" class="btn btn-danger float-end">
                            <span class="bi-arrow-left"></span>&nbsp;Voltar</a>
                        </h4>
                    </div>
                    <div class="card-body">
                        <?php
                        if (isset($_GET['id'])) {
                            $material_id = mysqli_real_escape_string($conexao, $_GET['id']);
                            $sql = "SELECT * FROM materiais WHERE id_material='$material_id'";
                            $query = mysqli_query($conexao, $sql);
                            if (mysqli_num_rows($query) > 0) {
                                $material = mysqli_fetch_array($query);
                                $fornecedor_id = $material['fk_fornecedores_id_fornecedor'];
                                ?>
                                <form action="/planel/material/atualizar" method="POST">
                                    <input type="hidden" name="material_id" required value="<?= $material['id_material'] ?>">
                                    <div class="mb-3">
                                        <label for="nome_material">Nome do Material</label>
                                        <input type="text" name="nome_material" value="<?= $material['nome_material'] ?>"
                                            class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="valor_compra">Valor de Compra</label>
                                        <input type="number" step="0.01" name="valor_compra" value="<?= $material['valor_compra'] ?>"
                                            class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="valor_venda">Valor de Venda</label>
                                        <input type="number" step="0.01" name="valor_venda" value="<?= $material['valor_venda'] ?>"
                                            class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="data_compra">Data da Compra</label>
                                        <input type="date" name="data_compra" value="<?= $material['data_compra'] ?>"
                                            class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="quantidade">Quantidade</label>
                                        <input type="number" name="quantidade" value="<?= $material['quantidade'] ?>"
                                            class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="unidade_medida">Unidade de Medida</label>
                                        <input type="text" name="unidade_medida" value="<?= $material['unidade_medida'] ?>"
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
                                            $selected = ($row_fornecedor['id_fornecedor'] == $fornecedor_id) ? 'selected' : '';
                                            echo "<option value='".$row_fornecedor['id_fornecedor']."' $selected>". utf8_decode($row_fornecedor['nome_fornecedor'])."</option>";
                                        }
                                        ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <button type="submit" name="editar_material" class="btn btn-primary">
                                            <span class="bi-save"></span>&nbsp;Salvar
                                        </button>
                                    </div>
                                </form>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
            crossorigin="anonymous">
    </script>
</body>
</html>
