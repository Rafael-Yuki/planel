<?php
session_start();
require('Application/models/material_dao.php');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Materiais</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" 
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body data-bs-theme="dark">
    <?php include(__DIR__ . '/../navbar.php'); ?>
    <div class="container-fluid mt-4">
        <?php include(__DIR__ . '/../mensagem.php'); ?>
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <h4> Materiais
                            <a href="material/cadastro" class="btn btn-primary float-end">
                            <span class="bi-box-seam me-2"></span>Adicionar Material</a>
                        </h4>
                    </div>
                    <div class="card-body">
                        <?php
                        $material_dao = new MaterialDAO;
                        $materiais = $material_dao->listarMateriais();
                        if (mysqli_num_rows($materiais) > 0) {
                            ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th>Nome do Material</th>
                                        <th>NCM</th>
                                        <th>Quantidade</th>
                                        <th>Unidade de Medida</th>
                                        <th>Valor de Compra</th>
                                        <th>Valor de Venda</th>
                                        <th>Data de Compra</th>
                                        <th>Fornecedor</th>
                                        <th>Opções</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    foreach ($materiais as $material) {
                                        ?>
                                        <tr>
                                            <td><?= $material['nome_material'] ?></td>
                                            <td><?= $material['ncm'] ?></td>
                                            <td><?= $material['quantidade'] ?></td>
                                            <td><?= $material['unidade_medida'] ?></td>
                                            <td>R$ <?= number_format($material['valor_compra'], 2, ',', '.') ?></td>
                                            <td>R$ <?= number_format($material['valor_venda'], 2, ',', '.') ?></td>
                                            <td><?= date('d/m/Y', strtotime($material['data_compra'])) ?></td>
                                            <td><?= $material['nome_fornecedor'] ?></td>
                                            <td class="text-center text-nowrap">
                                                <a href="material/visualizar?id=<?= $material['id_material'] ?>" class="btn btn-secondary btn-sm">
                                                    <span class="bi-eye-fill"></span>
                                                </a>
                                                <a href="material/editar?id=<?= $material['id_material'] ?>" class="btn btn-success btn-sm">
                                                    <span class="bi-pencil-fill"></span>
                                                </a>
                                                <form action="material/atualizar" method="POST" class="d-inline">
                                                    <button onclick="return confirm('Tem certeza que deseja excluir?')" type="submit" name="excluir_material" value="<?= $material['id_material'] ?>" class="btn btn-danger btn-sm">
                                                        <span class="bi-trash3-fill"></span>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php
                        } else {
                            echo '<h5>Nenhum material cadastrado</h5>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" 
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
