<?php
session_start();
require('Application/models/nota_fiscal_dao.php');
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Importar XML</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" 
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body data-bs-theme="dark">
    <?php include(__DIR__ . '/../navbar.php'); ?>
    <div class="container-fluid mt-4">
        <?php include(__DIR__ . '/../mensagem.php'); ?>
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h4 class="mb-0">XMLs Importados</h4>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#importarXmlModal">
                    <i class="bi bi-upload me-2"></i> Adicionar XML
                </button>
            </div>
            <div class="card-body">
                <?php
                $nota_fiscal_dao = new NotaFiscalDAO;
                $xml_importados = $nota_fiscal_dao->listarNotasFiscaisComParcelas(); // Função ajustada no DAO para incluir parcelas
                if (mysqli_num_rows($xml_importados) > 0) {
                    ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Número</th>
                                <th>Fornecedor</th>
                                <th>Data de Emissão</th>
                                <th>Valor Total</th>
                                <th>Parcela Atual</th> <!-- Coluna de Parcela Atual -->
                                <th>Total Parcelas</th> <!-- Coluna de Total de Parcelas -->
                                <th>Opções</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            while ($xml = mysqli_fetch_assoc($xml_importados)) {
                                ?>
                                <tr>
                                    <td><?= $xml['numero'] ?></td>
                                    <td><?= $xml['nome_fornecedor'] ?></td>
                                    <td><?= date('d/m/Y', strtotime($xml['data_emissao'])) ?></td>
                                    <td>R$ <?= number_format($xml['valor_total'], 2, ',', '.') ?></td>
                                    <td><?= $xml['parcela_atual'] ?></td> <!-- Exibe a parcela atual -->
                                    <td><?= $xml['total_parcelas'] ?></td> <!-- Exibe o total de parcelas -->
                                    <td class="text-center text-nowrap">
                                        <?php if (!empty($xml['caminho_xml'])): ?>
                                            <a href="<?= '/planel/upload?file=' . urlencode(basename($xml['caminho_xml'])); ?>" 
                                            class="btn btn-sm btn-secondary" target="_blank" title="Ver XML">
                                            <i class="bi-eye-fill"></i>
                                            </a>
                                        <?php endif; ?>
                                        <?php if (!empty($xml['caminho_xml'])): ?>
                                            <a href="<?= '/planel/upload?file=' . urlencode(basename($xml['caminho_xml'])); ?>" 
                                            class="btn btn-sm btn-success" download title="Download XML">
                                            <i class="bi-download"></i>
                                            </a>
                                        <?php endif; ?>
                                        <form action="xml/excluir" method="POST" class="d-inline">
                                            <button onclick="return confirm('Tem certeza que deseja excluir apenas o XML?')" type="submit" name="excluir_xml" value="<?= $xml['id_nota_fiscal'] ?>" class="btn btn-sm btn-danger" title="Excluir XML">
                                                <i class="bi-trash3-fill"></i>
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
                    echo '<h5>Nenhum arquivo XML importado</h5>';
                }
                ?>
            </div>
        </div>
    </div>

    <!-- Modal para importar XML -->
    <div class="modal fade" id="importarXmlModal" tabindex="-1" aria-labelledby="importarXmlModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importarXmlModalLabel">Importar XML de Nota Fiscal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formImportXML" action="xml/atualizar" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="xmlFile" class="form-label">Selecione o arquivo XML</label>
                            <input type="file" name="xmlFile" id="xmlFile" class="form-control" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x-circle me-2"></i>Cancelar</button>
                    <button form="formImportXML" type="submit" name="importar_xml" class="btn btn-primary"><i class="bi bi-upload me-2"></i>Importar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" 
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
