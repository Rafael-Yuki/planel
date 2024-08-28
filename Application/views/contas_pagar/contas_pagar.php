<?php
session_start();
require('Application/models/contas_pagar_dao.php');

function formatarMoeda($valor) {
    return 'R$ ' . number_format($valor, 2, ',', '.');
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contas a Pagar</title>
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
                        <h4> Contas a Pagar
                            <a href="conta-a-pagar/cadastro" class="btn btn-primary float-end">
                            <span class="bi bi-cash-stack me-2"></span>Adicionar Conta</a>
                        </h4>
                    </div>
                    <div class="card-body">
                        <?php
                        $conta_pagar_dao = new ContasPagarDAO;
                        $contas_pagar = $conta_pagar_dao->listarContasPagar();
                        if (mysqli_num_rows($contas_pagar) > 0) {
                            ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th>Nota Fiscal</th>
                                        <th>Fornecedor</th>
                                        <th>Valor</th>
                                        <th>Data de Vencimento</th>
                                        <th>Parcela Atual</th>
                                        <th>Parcelas</th>
                                        <th>Opções</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    while ($conta_pagar = mysqli_fetch_assoc($contas_pagar)) {
                                        ?>
                                        <tr>
                                            <td><?= $conta_pagar['numero_nota_fiscal']; ?></td>
                                            <td><?= $conta_pagar['nome_fornecedor']; ?></td>
                                            <td><?= formatarMoeda($conta_pagar['valor']); ?></td>
                                            <td><?= date('d/m/Y', strtotime($conta_pagar['data_vencimento'])); ?></td>
                                            <td><?= $conta_pagar['parcela_atual']; ?></td>
                                            <td><?= $conta_pagar['parcelas']; ?></td>
                                            <td class="text-center text-nowrap">
                                                <a href="conta-a-pagar/visualizar?id=<?= $conta_pagar['id_conta_pagar'] ?>" class="btn btn-secondary btn-sm">
                                                    <span class="bi-eye-fill"></span>
                                                </a>
                                                <a href="conta-a-pagar/editar?id=<?= $conta_pagar['id_conta_pagar'] ?>" class="btn btn-success btn-sm">
                                                    <span class="bi-pencil-fill"></span>
                                                </a>
                                                <form action="conta-a-pagar/atualizar" method="POST" class="d-inline">
                                                    <button onclick="return confirm('Tem certeza que deseja excluir?')" type="submit" name="excluir_conta_pagar" value="<?= $conta_pagar['id_conta_pagar'] ?>" class="btn btn-danger btn-sm">
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
                            echo '<h5>Nenhuma conta a pagar cadastrada</h5>';
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
