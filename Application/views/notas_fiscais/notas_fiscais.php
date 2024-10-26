<?php
session_start();
require('Application/models/nota_fiscal_dao.php');
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Notas Fiscais</title>
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
                        <h4 class="mb-0">Notas Fiscais</h4>
                        <a href="nota-fiscal/cadastro" class="btn btn-primary">
                            <span class="bi-receipt me-2"></span>Adicionar Nota Fiscal
                        </a>
                    </div>
                    <div class="card-body">
                        <?php
                        $notas_dao = new NotaFiscalDAO;
                        $notas = $notas_dao->listarNotasFiscais();
                        if (mysqli_num_rows($notas) > 0) {
                            ?>
                            <div class="table-responsive">
                                <table id="notasTable" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th>Número</th>
                                        <th>Fornecedor</th>
                                        <th>Data de Emissão</th>
                                        <th>Valor Total</th>
                                        <th>Parcelas</th>
                                        <th>Anexo</th>
                                        <th>Opções</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    while ($nota = mysqli_fetch_assoc($notas)) {
                                        ?>
                                        <tr>
                                            <td><?= htmlspecialchars($nota['numero'], ENT_QUOTES, 'UTF-8') ?></td>
                                            <td><?= htmlspecialchars($nota['nome_fornecedor'], ENT_QUOTES, 'UTF-8') ?></td>
                                            <td><?= date('d/m/Y', strtotime($nota['data_emissao'])) ?></td>
                                            <td>R$ <?= number_format($nota['valor_total'], 2, ',', '.') ?></td>
                                            <td><?= htmlspecialchars($nota['parcelas'], ENT_QUOTES, 'UTF-8') ?></td>
                                            <td>
                                                <?php if (!empty($nota['caminho_xml'])): ?>
                                                    <a href="<?= '/planel/upload?file=' . urlencode(basename($nota['caminho_xml'])); ?>" 
                                                    class="btn btn-sm" target="_blank">
                                                    <span class="bi-file-earmark-pdf-fill"></span>&nbsp;Ver Anexo
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted">Nenhum anexo</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center text-nowrap">
                                                <a href="nota-fiscal/visualizar?id=<?= $nota['id_nota_fiscal'] ?>" class="btn btn-secondary btn-sm" title="Ver Nota Fiscal">
                                                    <span class="bi-eye-fill"></span>
                                                </a>
                                                <a href="nota-fiscal/editar?id=<?= $nota['id_nota_fiscal'] ?>" class="btn btn-success btn-sm" title="Editar Nota Fiscal">
                                                    <span class="bi-pencil-fill"></span>
                                                </a>
                                                <form action="nota-fiscal/atualizar" method="POST" class="d-inline">
                                                    <button onclick="return confirm('Tem certeza que deseja excluir?')" type="submit" name="excluir_nota_fiscal" value="<?= $nota['id_nota_fiscal'] ?>" class="btn btn-danger btn-sm" title="Excluir Nota Fiscal">
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
                            echo '<h5>Nenhuma nota fiscal cadastrada</h5>';
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
            $('#notasTable').DataTable({
                "paging": true,
                "searching": true,
                "ordering": true,
                "columnDefs": [
                    { "orderable": false, "targets": [6] }
                ],
                "order": [[0, "asc"]],
                "language": {
                   "url": "/planel/public/js/pt-BR.json" 
                }
            });
        });
    </script>
</body>
</html>
