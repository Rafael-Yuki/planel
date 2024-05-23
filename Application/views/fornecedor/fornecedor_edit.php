<?php
session_start();
require(dirname(__DIR__) . '../../models/conexao.php');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar Fornecedor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" 
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>
    <?php include(__DIR__ . '/../navbar.php');?>
    <div class="container mt-5">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <h4>Editar Fornecedor
                            <a href="../dashboard" class="btn btn-danger float-end">
                            <span class="bi-arrow-left"></span>&nbsp;Voltar</a>
                        </h4>
                    </div>
                    <div class="card-body">
                        <?php
                        if (isset($_GET['id'])) {
                            $fornecedor_id = mysqli_real_escape_string($conexao, $_GET['id']);
                            $sql = "SELECT * FROM fornecedores WHERE id_fornecedor='$fornecedor_id'";
                            $query = mysqli_query($conexao, $sql);
                            if (mysqli_num_rows($query) > 0) {
                                $fornecedor = mysqli_fetch_array($query);
                                ?>
                                <form action="/planel/fornecedor/atualizar" method="POST">
                                    <input type="hidden" name="fornecedor_id" required value="<?= $fornecedor['id_fornecedor'] ?>">
                                    <div class="mb-3">
                                        <label for="nome">Nome</label>
                                        <input type="text" name="nome" value="<?= $fornecedor['nome_fornecedor'] ?>"
                                            class="form-control">
                                    </div>
                                    <div class="mb-3">
                                        <label for="cnpj">CNPJ</label>
                                        <input type="text" name="cnpj" value="<?= $fornecedor['cnpj'] ?>"
                                            class="form-control">
                                    </div>
                                    <div class="mb-3">
                                        <label for="telefone">Telefone</label>
                                        <input type="tel" name="telefone" value="<?= $fornecedor['telefone'] ?>"
                                            class="form-control">
                                    </div>
                                    <div class="mb-3">
                                        <label for="email">E-mail</label>
                                        <input type="email" name="email" value="<?= $fornecedor['email'] ?>"
                                            class="form-control">
                                    </div>
                                    <div class="mb-3">
                                        <label for="endereco">Endereço</label>
                                        <input type="text" name="endereco" value="<?= $fornecedor['endereco'] ?>"
                                            class="form-control">
                                    </div>
                                    <div class="mb-3">
                                        <label for="estado">Estado</label>
                                        <select id="estado" name="estado" class="form-control">
                                        <option value="">Selecione um estado</option>
                                        <?php
                                        $query_estados = "SELECT * FROM estados";
                                        $result_estados = mysqli_query($conexao, $query_estados);
                                        while($row_estado = mysqli_fetch_assoc($result_estados)) {
                                            echo "<option value='".$row_estado['id_estado']."'>". utf8_decode($row_estado['nome_estado'])."</option>";
                                        }
                                        ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="cidade">Cidade</label>
                                        <select id="cidade" name="cidade" class="form-control">
                                        <option value="">Selecione uma cidade</option>
                                        <?php
                                        $query_cidades = "SELECT * FROM cidades";
                                        $result_cidades = mysqli_query($conexao, $query_cidades);
                                        while($row_cidade = mysqli_fetch_assoc($result_cidades)) {
                                            echo "<option value='".$row_cidade['id_cidade']."'>". utf8_decode($row_cidade['nome_cidade'])."</option>";
                                        }
                                        ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <button type="submit" name="update_fornecedor" class="btn btn-primary">
                                            <span class="bi-save"></span>&nbsp;Salvar
                                        </button>
                                    </div>
                                </form>
                                <?php
                            } else {
                                echo "<h5>Fornecedor não encontrado</h5>";
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
            crossorigin="anonymous">
    </script>
</body>
</html>
