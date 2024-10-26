<?php
session_start();
require('Application/models/contas_receber_dao.php');

function formatarMoeda($valor) {
    return 'R$ ' . number_format($valor, 2, ',', '.');
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contas a Receber</title>
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
                        <h4 class="mb-0"> Contas a Receber</h4>
                        <a href="conta-a-receber/cadastro" class="btn btn-primary">
                            <span class="bi bi-wallet2 me-2"></span>Adicionar Conta
                        </a>
                    </div>
                    <div class="card-body">
                        <?php
                        $conta_receber_dao = new ContasReceberDAO;
                        $contas_receber = $conta_receber_dao->listarContasReceber();
                        if (mysqli_num_rows($contas_receber) > 0) {
                            ?>
                            <div class="table-responsive">
                                <table id="contasReceberTable" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th>Orçamento</th>
                                        <th>Cliente</th>
                                        <th>Valor</th>
                                        <th>Data de Vencimento</th>
                                        <th>Parcela Atual</th>
                                        <th>Parcelas</th>
                                        <th>Opções</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    while ($conta_receber = mysqli_fetch_assoc($contas_receber)) {
                                        ?>
                                        <tr>
                                            <td><?= $conta_receber['nome_orcamento']; ?></td>
                                            <td><?= $conta_receber['nome_cliente']; ?></td>
                                            <td><?= formatarMoeda($conta_receber['valor']); ?></td>
                                            <td><?= date('d/m/Y', strtotime($conta_receber['data_vencimento'])); ?></td>
                                            <td><?= $conta_receber['parcela_atual']; ?></td>
                                            <td><?= $conta_receber['parcelas']; ?></td>
                                            <td class="text-center text-nowrap">
                                                <a href="conta-a-receber/visualizar?id=<?= $conta_receber['id_conta_receber'] ?>" class="btn btn-secondary btn-sm" title="Ver Conta a Receber">
                                                    <span class="bi-eye-fill"></span>
                                                </a>
                                                <a href="conta-a-receber/editar?id=<?= $conta_receber['id_conta_receber'] ?>" class="btn btn-success btn-sm" title="Editar Conta a Receber">
                                                    <span class="bi-pencil-fill"></span>
                                                </a>
                                                <form action="conta-a-receber/atualizar" method="POST" class="d-inline">
                                                    <button onclick="return confirm('Tem certeza que deseja excluir?')" type="submit" name="excluir_conta_receber" value="<?= $conta_receber['id_conta_receber'] ?>" class="btn btn-danger btn-sm" title="Excluir Conta a Receber">
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
                            echo '<h5>Nenhuma conta a receber cadastrada</h5>';
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
            const table = $('#contasReceberTable').DataTable({
                "paging": true,
                "searching": true,
                "ordering": true,
                "columnDefs": [
                    { "orderable": false, "targets": [6] },
                    { "type": "currency", "targets": [2] },
                    { "type": "date-uk", "targets": [3] }
                ],
                "order": [[3, "asc"]],
                "language": {
                    "url": "/planel/public/js/pt-BR.json"
                }
            });
        });
    </script>
</body>
</html>
