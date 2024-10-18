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
    <title>Editar Orçamento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" 
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body data-bs-theme="dark">
    <?php include(__DIR__ . '/../navbar.php'); ?>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Editar Orçamento
                            <a href="/planel/orcamentos" class="btn btn-danger float-end">
                                <span class="bi-arrow-left"></span>&nbsp;Voltar
                            </a>
                        </h4>
                    </div>
                    <div class="card-body">
                        <?php
                        if (isset($_GET['id'])) {
                            $orcamento_id = mysqli_real_escape_string($conexao, $_GET['id']);
                            $sql = "SELECT * FROM orcamentos WHERE id_orcamento='$orcamento_id'";
                            $query = mysqli_query($conexao, $sql);
                            if (mysqli_num_rows($query) > 0) {
                                $orcamento = mysqli_fetch_array($query);
                                ?>
                                <form action="/planel/orcamento/atualizar" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="orcamento_id" required value="<?= $orcamento['id_orcamento'] ?>">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="nome_orcamento">Nome do Orçamento</label>
                                                <input type="text" name="nome_orcamento" value="<?= $orcamento['nome_orcamento'] ?>"
                                                    class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="cliente">Cliente</label>
                                                <select id="cliente" name="cliente" class="form-control" required>
                                                    <option value="">Selecione um Cliente</option>
                                                    <?php
                                                    $query_clientes = "SELECT * FROM clientes WHERE ativo = TRUE";
                                                    $result_clientes = mysqli_query($conexao, $query_clientes);
                                                    while($row_cliente = mysqli_fetch_assoc($result_clientes)) {
                                                        $selected = ($row_cliente['id_cliente'] == $orcamento['fk_clientes_id_cliente']) ? 'selected' : '';
                                                        echo "<option value='".$row_cliente['id_cliente']."' $selected>".$row_cliente['nome_cliente']."</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="data_orcamento">Data do Orçamento</label>
                                                <input type="date" name="data_orcamento" value="<?= $orcamento['data_orcamento'] ?>"
                                                    class="form-control" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="validade">Validade</label>
                                                <input type="date" name="validade" value="<?= $orcamento['validade'] ?>"
                                                    class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="status">Status</label>
                                                <input type="text" name="status" value="<?= $orcamento['status'] ?>"
                                                    class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="anexo">Anexo</label>
                                                <input type="file" id="arquivo_pdf" name="arquivo_pdf" class="form-control">
                                                <?php if (!empty($orcamento['caminho_arquivo'])): ?>
                                                    <small class="form-text text-muted">
                                                        Arquivo atual: <a href="<?= '/planel/upload?file=' . urlencode(basename($orcamento['caminho_arquivo'])); ?>" target="_blank"><?= basename($orcamento['caminho_arquivo']); ?></a>
                                                    </small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label for="observacao">Observação</label>
                                                <textarea id="observacao" name="observacao" class="form-control"><?= $orcamento['observacao'] ?></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Materiais relacionados ao orçamento -->
                                    <div class="mt-4">
                                        <h4>Materiais no Orçamento</h4>
                                        <div id="materiais-container">
                                            <?php
                                            $sql_materiais = "SELECT om.*, m.nome_material 
                                                              FROM orcamento_material om
                                                              LEFT JOIN materiais m ON om.fk_materiais_id_material = m.id_material
                                                              WHERE om.fk_orcamentos_id_orcamento = {$orcamento_id}";
                                            $query_materiais = mysqli_query($conexao, $sql_materiais);

                                            if (mysqli_num_rows($query_materiais) > 0) {
                                                while ($material = mysqli_fetch_assoc($query_materiais)) {
                                                    ?>
                                                    <div class="row mb-3">
                                                        <div class="col-md-4">
                                                            <label for="nome_material">Nome do Material</label>
                                                            <input type="text" name="nome_material[]" class="form-control" 
                                                                   value="<?= $material['nome_material'] ?: $material['nome_orcamento_material']; ?>" required>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label for="quantidade">Quantidade</label>
                                                            <input type="number" name="quantidade[]" class="form-control" 
                                                                   value="<?= $material['quantidade_material']; ?>" required>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label for="preco">Preço Unitário</label>
                                                            <input type="number" name="preco[]" class="form-control" 
                                                                   value="<?= $material['valor_unitario']; ?>" step="0.01" required>
                                                        </div>
                                                        <div class="col-md-2 d-flex align-items-end">
                                                            <button type="button" class="btn btn-danger" onclick="removerMaterial(this)">
                                                                <span class="bi-trash3-fill"></span>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <?php
                                                }
                                            } else {
                                                echo '<p>Nenhum material adicionado ao orçamento.</p>';
                                            }
                                            ?>
                                        </div>
                                        <button type="button" class="btn btn-secondary" onclick="adicionarMaterial()">Adicionar Material</button>
                                    </div>

                                    <div class="mb-3 mt-4">
                                        <button type="submit" name="editar_orcamento" class="btn btn-primary">
                                            Salvar<span class="bi-save ms-2"></span>
                                        </button>
                                    </div>
                                </form>
                                <?php
                            } else {
                                echo "<h5>Orçamento não encontrado</h5>";
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function removerMaterial(btn) {
            btn.closest('.row').remove();
        }

        function adicionarMaterial() {
            const novoMaterial = `
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="nome_material">Nome do Material</label>
                        <input type="text" name="nome_material[]" class="form-control" placeholder="Nome do Material" required>
                    </div>
                    <div class="col-md-3">
                        <label for="quantidade">Quantidade</label>
                        <input type="number" name="quantidade[]" class="form-control" min="1" step="1" required>
                    </div>
                    <div class="col-md-3">
                        <label for="preco">Preço Unitário</label>
                        <input type="number" name="preco[]" class="form-control" step="0.01" required>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="button" class="btn btn-danger" onclick="removerMaterial(this)">
                            <span class="bi-trash3-fill"></span>
                        </button>
                    </div>
                </div>`;
            document.getElementById('materiais-container').insertAdjacentHTML('beforeend', novoMaterial);
        }
    </script>
</body>
</html>
