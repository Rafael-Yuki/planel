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
    <link rel="stylesheet" href="public/css/main.css">
</head>

<body data-bs-theme="dark">
    <?php include(__DIR__ . '/../navbar.php'); ?>
    <div class="container-fluid mt-4">
        <?php include(__DIR__ . '/../mensagem.php'); ?>
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <h4> Notas Fiscais
                            <a href="nota-fiscal/cadastro" class="btn btn-primary float-end">
                            <span class="bi-receipt me-2"></span>Adicionar Nota Fiscal</a>
                        </h4>
                    </div>
                    <div class="card-body">
                        <?php
                        $nota_fiscal_dao = new NotaFiscalDAO;
                        $notas_fiscais = $nota_fiscal_dao->listarNotasFiscais();
                        if (mysqli_num_rows($notas_fiscais) > 0) {
                            ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th>Número</th>
                                        <th>Fornecedor</th>
                                        <th>Data de Emissão</th>
                                        <th>Valor Total</th>
                                        <th>Parcelas</th>
                                        <th>XML</th>
                                        <th>Opções</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    while ($nota_fiscal = mysqli_fetch_assoc($notas_fiscais)) {
                                        ?>
                                        <tr>
                                            <td><?= $nota_fiscal['numero'] ?></td>
                                            <td><?= $nota_fiscal['nome_fornecedor'] ?></td>
                                            <td><?= date('d/m/Y', strtotime($nota_fiscal['data_emissao'])) ?></td>
                                            <td>R$ <?= number_format($nota_fiscal['valor_total'], 2, ',', '.') ?></td>
                                            <td><?= $nota_fiscal['parcelas'] ?></td>
                                            <td>
                                                <?php if (!empty($nota_fiscal['caminho_xml'])): ?>
                                                    <a href="<?= '/planel/upload?file=' . urlencode(basename($nota_fiscal['caminho_xml'])); ?>" 
                                                    class="btn btn-sm" target="_blank">
                                                    <span class="bi-file-earmark-text-fill"></span>&nbsp;Ver XML
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted">Nenhum XML</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center text-nowrap">
                                                <a href="nota-fiscal/visualizar?id=<?= $nota_fiscal['id_nota_fiscal'] ?>" class="btn btn-secondary btn-sm" title="Ver Nota Fiscal">
                                                    <span class="bi-eye-fill"></span>
                                                </a>
                                                <a href="nota-fiscal/editar?id=<?= $nota_fiscal['id_nota_fiscal'] ?>" class="btn btn-success btn-sm" title="Editar Nota Fiscal">
                                                    <span class="bi-pencil-fill"></span>
                                                </a>
                                                <form action="nota-fiscal/atualizar" method="POST" class="d-inline">
                                                    <button onclick="return confirm('Tem certeza que deseja excluir?')" type="submit" name="excluir_nota_fiscal" value="<?= $nota_fiscal['id_nota_fiscal'] ?>" class="btn btn-danger btn-sm" title="Excluir Nota Fiscal">
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" 
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
