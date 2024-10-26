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
                        <h4 class="mb-0"> Clientes</h4>
                        <a href="cliente/cadastro" class="btn btn-primary">
                            <span class="bi-person-plus-fill me-2"></span>Adicionar Cliente
                        </a>
                    </div>
                    <div class="card-body">
                        <?php
                        $cliente_dao = new clienteDAO;
                        $clientes = $cliente_dao->listarClientes();
                        if (mysqli_num_rows($clientes) > 0) {
                            ?>
                            <div class="table-responsive">
                                <table id="clientesTable" class="table table-bordered table-striped">
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
                                            <td class="text-center text-nowrap">
                                                <a href="cliente/visualizar?id=<?= $cliente['id_cliente'] ?>" class="btn btn-secondary btn-sm" title="Ver Cliente">
                                                    <span class="bi-eye-fill"></span>
                                                </a>
                                                <a href="cliente/editar?id=<?= $cliente['id_cliente'] ?>" class="btn btn-success btn-sm" title="Editar Cliente">
                                                    <span class="bi-pencil-fill"></span>
                                                </a>
                                                <form action="cliente/atualizar" method="POST" class="d-inline">
                                                    <button onclick="return confirm('Tem certeza que deseja excluir?')" type="submit" name="excluir_cliente" value="<?= $cliente['id_cliente'] ?>" class="btn btn-danger btn-sm" title="Excluir Cliente">
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
                            echo '<h5>Nenhum cliente cadastrado</h5>';
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
            const table = $('#clientesTable').DataTable({
                "paging": true,
                "searching": true,
                "ordering": true,
                "columnDefs": [
                    { "orderable": false, "targets": [5] }
                ],
                "order": [[0, "asc"]],
                "language": {
                    "url": "/planel/public/js/pt-BR.json"
                }
            });

            $('#searchInput').on('keyup', function() {
                table.search(this.value).draw();
            });
        });
    </script>
</body>
</html>
