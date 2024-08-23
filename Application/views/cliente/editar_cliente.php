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
    <title>Editar Cliente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" 
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body data-bs-theme="dark">
    <?php include(__DIR__ . '/../navbar.php');?>
    <div class="container mt-5">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <h4>Editar Cliente
                            <a href="/planel/clientes" class="btn btn-danger float-end">
                            <span class="bi-arrow-left"></span>&nbsp;Voltar</a>
                        </h4>
                    </div>
                    <div class="card-body">
                        <?php
                        if (isset($_GET['id'])) {
                            $cliente_id = mysqli_real_escape_string($conexao, $_GET['id']);
                            $sql = "SELECT * FROM clientes WHERE id_cliente='$cliente_id'";
                            $query = mysqli_query($conexao, $sql);
                            if (mysqli_num_rows($query) > 0) {
                                $cliente = mysqli_fetch_array($query);
                                $estado_id = $cliente['fk_estados_id_estado'];
                                $cidade_id = $cliente['fk_cidades_id_cidade'];
                                ?>
                                <form action="/planel/cliente/atualizar" method="POST">
                                    <input type="hidden" name="cliente_id" required value="<?= $cliente['id_cliente'] ?>">
                                    <div class="mb-3">
                                        <label for="nome">Nome</label>
                                        <input type="text" name="nome" value="<?= $cliente['nome_cliente'] ?>"
                                            class="form-control">
                                    </div>
                                    <div class="mb-3">
                                        <label for="cnpj">CNPJ</label>
                                        <input type="text" name="cnpj" value="<?= $cliente['cnpj'] ?>"
                                            class="form-control">
                                    </div>
                                    <div class="mb-3">
                                        <label for="telefone">Telefone</label>
                                        <input type="tel" name="telefone" value="<?= $cliente['telefone'] ?>"
                                            class="form-control">
                                    </div>
                                    <div class="mb-3">
                                        <label for="email">E-mail</label>
                                        <input type="email" name="email" value="<?= $cliente['email'] ?>"
                                            class="form-control">
                                    </div>
                                    <div class="mb-3">
                                        <label for="endereco">Endereço</label>
                                        <input type="text" name="endereco" value="<?= $cliente['endereco'] ?>"
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
                                            $selected = ($row_estado['id_estado'] == $estado_id) ? 'selected' : '';
                                            echo "<option value='".$row_estado['id_estado']."' $selected>". $row_estado['nome_estado']."</option>";
                                        }
                                        ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="cidade">Cidade</label>
                                        <select id="cidade" name="cidade" class="form-control">
                                        <option value="">Selecione uma cidade</option>
                                        <?php
                                        $query_cidades = "SELECT * FROM cidades WHERE fk_estados_id_estado = '$estado_id'";
                                        $result_cidades = mysqli_query($conexao, $query_cidades);
                                        while($row_cidade = mysqli_fetch_assoc($result_cidades)) {
                                            $selected = ($row_cidade['id_cidade'] == $cidade_id) ? 'selected' : '';
                                            echo "<option value='".$row_cidade['id_cidade']."' $selected>". $row_cidade['nome_cidade']."</option>";
                                        }
                                        ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <button type="submit" name="editar_cliente" class="btn btn-primary">
                                            <span class="bi-save"></span>&nbsp;Salvar
                                        </button>
                                    </div>
                                </form>
                                <script>
                                    $(document).ready(function() {
                                        $('#estado').change(function() {
                                            var estadoId = $(this).val();
                                            if (estadoId) {
                                                $.ajax({
                                                    url: '/planel/cidades',
                                                    type: 'POST',
                                                    data: {estado_id: estadoId},
                                                    success: function(data) {
                                                        $('#cidade').prop('disabled', false);
                                                        $('#cidade').html(data);
                                                    },
                                                    error: function(jqXHR, textStatus, errorThrown) {
                                                        alert('Erro ao carregar cidades: ' + textStatus + ' - ' + errorThrown);
                                                        console.log(jqXHR.responseText);
                                                    }
                                                });
                                            } else {
                                                $('#cidade').prop('disabled', true);
                                                $('#cidade').html('<option value="">Selecione um Estado</option>');
                                            }
                                        });
                                    });
                                </script>
                                <?php
                            } else {
                                echo "<h5>cliente não encontrado</h5>";
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
            crossorigin="anonymous">
    </script>
</body>
</html>
