<?php
session_start();
require('Application/models/servico_dao.php');
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Serviços</title>
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
                        <h4 class="mb-0"> Serviços</h4>
                        <a href="servico/cadastro" class="btn btn-primary">
                            <span class="bi-tools me-2"></span>Adicionar Serviço
                        </a>
                    </div>
                    <div class="card-body">
                        <?php
                        $servico_dao = new ServicoDAO;
                        $servicos = $servico_dao->listarServicos();
                        if (mysqli_num_rows($servicos) > 0) {
                            ?>
                            <div class="table-responsive">
                                <table id="servicosTable" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th>Serviço</th>
                                        <th>Valor de Serviço</th>
                                        <th>Descrição</th>
                                        <th>Opções</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    foreach ($servicos as $servico) {
                                        ?>
                                        <tr>
                                            <td><?= $servico['nome_servico'] ?></td>
                                            <td>R$ <?= number_format($servico['valor_servico'], 2, ',', '.') ?></td>
                                            <td><?= $servico['descricao_servico'] ?></td>
                                            <td class="text-center text-nowrap">
                                                <a href="servico/visualizar?id=<?= $servico['id_servico'] ?>" class="btn btn-secondary btn-sm" title="Ver Serviço">
                                                    <span class="bi-eye-fill"></span>
                                                </a>
                                                <a href="servico/editar?id=<?= $servico['id_servico'] ?>" class="btn btn-success btn-sm" title="Editar Serviço">
                                                    <span class="bi-pencil-fill"></span>
                                                </a>
                                                <form action="servico/atualizar" method="POST" class="d-inline">
                                                    <button onclick="return confirm('Tem certeza que deseja excluir?')" type="submit" name="excluir_servico" value="<?= $servico['id_servico'] ?>" class="btn btn-danger btn-sm" title="Excluir Serviço">
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
                            echo '<h5>Nenhum serviço cadastrado</h5>';
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
            const table = $('#servicosTable').DataTable({
                "paging": true,
                "searching": true,
                "ordering": true,
                "columnDefs": [
                    { "orderable": false, "targets": [3] },
                    { "type": "currency", "targets": [1] }
                ],
                "order": [[0, "asc"]],
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json"
                }
            });
        });
    </script>
</body>
</html>
