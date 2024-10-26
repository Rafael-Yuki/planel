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
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="planel/public/css/main.css">
</head>

<body data-bs-theme="dark">
    <?php include(__DIR__ . '/../navbar.php'); ?>
    <div class="container-fluid mt-4">
        <?php include(__DIR__ . '/../mensagem.php'); ?>
        <div class="row mb-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"> Materiais</h4>
                        <a href="material/cadastro" class="btn btn-primary">
                            <span class="bi-box-seam me-2"></span>Adicionar Material
                        </a>
                    </div>
                    <div class="card-body">
                        <?php
                        $material_dao = new MaterialDAO;
                        $materiais = $material_dao->listarMateriais();
                        if (mysqli_num_rows($materiais) > 0) {
                            ?>
                            <div class="table-responsive">
                                <table id="materiaisTable" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th>Material</th>
                                        <th>NCM</th>
                                        <th>Qnt.</th>
                                        <th>Unid.</th>
                                        <th>Valor Compra</th>
                                        <th>Valor Venda</th>
                                        <th>Compra</th>
                                        <th>Fornecedor</th>
                                        <th>Nota</th>
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
                                            <td><?= $material['numero_nota_fiscal'] ?></td>
                                            <td class="text-center text-nowrap">
                                                <a href="material/visualizar?id=<?= $material['id_material'] ?>" class="btn btn-secondary btn-sm" title="Ver Material">
                                                    <span class="bi-eye-fill"></span>
                                                </a>
                                                <a href="material/editar?id=<?= $material['id_material'] ?>" class="btn btn-success btn-sm" title="Editar Material">
                                                    <span class="bi-pencil-fill"></span>
                                                </a>
                                                <form action="material/atualizar" method="POST" class="d-inline">
                                                    <button onclick="return confirm('Tem certeza que deseja excluir?')" type="submit" name="excluir_material" value="<?= $material['id_material'] ?>" class="btn btn-danger btn-sm" title="Excluir Material">
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            const table = $('#materiaisTable').DataTable({
                "paging": true,
                "searching": true,
                "ordering": true,
                "columnDefs": [
                    { "orderable": false, "targets": [9] },
                    { "type": "currency", "targets": [4, 5] },
                    { "type": "date-uk", "targets": [6] }
                ],
                "order": [[6, "asc"]],
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json"
                }
            });
        });
    </script>
</body>
</html>
