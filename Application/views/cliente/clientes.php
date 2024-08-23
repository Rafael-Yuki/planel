<?php
session_start();
require('Application/models/cliente_dao.php');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Clientes</title>
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
                        <h4> Clientes
                            <a href="cliente/cadastro" class="btn btn-primary float-end">
                            <span class="bi-person-plus-fill"></span>&nbsp;Adicionar Cliente</a>
                        </h4>
                    </div>
                    <div class="card-body">
                        <?php
                        $cliente_dao = new clienteDAO;
                        $clientes = $cliente_dao->listarClientes();
                        if (mysqli_num_rows($clientes) > 0) {
                            ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th>CNPJ</th>
                                        <th>Telefone</th>
                                        <th>E-mail</th>
                                        <th>Endereço</th>
                                        <th>Opções</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    foreach ($clientes as $cliente) {
                                        ?>
                                        <tr>
                                            <td><?= $cliente['nome_cliente'] ?></td>
                                            <td><?= $cliente['cnpj'] ?></td>
                                            <td><?= $cliente['telefone'] ?></td>
                                            <td><?= $cliente['email'] ?></td>
                                            <td><?= $cliente['endereco'] . ', ' . $cliente['nome_cidade'] . ' - ' . $cliente['sigla_estado'] ?></td>
                                            <td>
                                                <a href="cliente/visualizar?id=<?= $cliente['id_cliente'] ?>" class="btn btn-secondary btn-sm">
                                                    <span class="bi-eye-fill"></span>&nbsp;Visualizar
                                                </a>
                                                <a href="cliente/editar?id=<?= $cliente['id_cliente'] ?>" class="btn btn-success btn-sm">
                                                    <span class="bi-pencil-fill"></span>&nbsp;Editar
                                                </a>
                                                <form action="cliente/atualizar" method="POST" class="d-inline">
                                                    <button onclick="return confirm('Tem certeza que deseja excluir?')" type="submit" name="excluir_cliente" value="<?= $cliente['id_cliente'] ?>" class="btn btn-danger btn-sm">
                                                        <span class="bi-trash3-fill"></span>&nbsp;Excluir
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
                            echo '<h5>Nenhum cliente cadastrado</h5>';
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
