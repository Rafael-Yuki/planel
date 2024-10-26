<?php
session_start();
require('Application/models/orcamento_dao.php');
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Orçamentos</title>
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
                        <h4 class="mb-0"> Orçamentos</h4>
                        <a href="orcamento/cadastro" class="btn btn-primary">
                            <span class="bi-file-earmark-text me-2"></span>Adicionar Orçamento
                        </a>
                    </div>
                    <div class="card-body">
                        <?php
                        $orcamento_dao = new OrcamentoDAO;
                        $orcamentos = $orcamento_dao->listarOrcamentos();
                        if (mysqli_num_rows($orcamentos) > 0) {
                            ?>
                            <div class="table-responsive">
                                <table id="orcamentosTable" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th>Nome do Orçamento</th>
                                        <th>Cliente</th>
                                        <th>Data do Orçamento</th>
                                        <th>Validade</th>
                                        <th>Valor Total</th>
                                        <th>Status</th>
                                        <th>Anexo</th>
                                        <th>Opções</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    while ($orcamento = mysqli_fetch_assoc($orcamentos)) {
                                        ?>
                                        <tr>
                                            <td><?= htmlspecialchars($orcamento['nome_orcamento'], ENT_QUOTES, 'UTF-8') ?></td>
                                            <td><?= htmlspecialchars($orcamento['nome_cliente'], ENT_QUOTES, 'UTF-8') ?></td>
                                            <td><?= date('d/m/Y', strtotime($orcamento['data_orcamento'])) ?></td>
                                            <td><?= date('d/m/Y', strtotime($orcamento['validade'])) ?></td>
                                            <td>R$ <?= number_format($orcamento['valor_total_orcamento'], 2, ',', '.') ?></td>
                                            <td><?= htmlspecialchars($orcamento['status'], ENT_QUOTES, 'UTF-8') ?></td>
                                            <td>
                                                <?php if (!empty($orcamento['caminho_arquivo'])): ?>
                                                    <a href="<?= '/planel/upload?file=' . urlencode(basename($orcamento['caminho_arquivo'])); ?>" 
                                                    class="btn btn-sm" target="_blank">
                                                    <span class="bi-file-earmark-pdf-fill"></span>&nbsp;Ver Arquivo
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted">Nenhum anexo</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center text-nowrap">
                                                <a href="orcamento/visualizar?id=<?= $orcamento['id_orcamento'] ?>" class="btn btn-secondary btn-sm" title="Ver Orçamento">
                                                    <span class="bi-eye-fill"></span>
                                                </a>
                                                <a href="orcamento/editar?id=<?= $orcamento['id_orcamento'] ?>" class="btn btn-success btn-sm" title="Editar Orçamento">
                                                    <span class="bi-pencil-fill"></span>
                                                </a>
                                                <form action="orcamento/atualizar" method="POST" class="d-inline">
                                                    <button onclick="return confirm('Tem certeza que deseja excluir?')" type="submit" name="excluir_orcamento" value="<?= $orcamento['id_orcamento'] ?>" class="btn btn-danger btn-sm" title="Excluir Orçamento">
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
                            echo '<h5>Nenhum orçamento cadastrado</h5>';
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
            const table = $('#orcamentosTable').DataTable({
                "paging": true,
                "searching": true,
                "ordering": true,
                "columnDefs": [
                    { "orderable": false, "targets": [7] }
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
