<?php
session_start();
require('Application/models/conexao.php');
require('Application/models/contas_pagar_dao.php');
require('Application/models/parcelas_pagar_dao.php');
?>
<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Editar Conta a Pagar</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
  integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
</head>

<body data-bs-theme="dark">
  <?php include(__DIR__ . '/../navbar.php'); ?>
  <div class="container mt-5">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h4>Editar Conta a Pagar
              <a href="/planel/contas-a-pagar" class="btn btn-danger float-end">
              <span class="bi-arrow-left"></span>&nbsp;Voltar</a>
            </h4>
          </div>
          <div class="card-body">
            <?php
            if (isset($_GET['id'])) {
                $conta_pagar_id = mysqli_real_escape_string($conexao, $_GET['id']);
                $conta_pagar = ContasPagarDAO::obterContaPagar($conta_pagar_id);

                if ($conta_pagar) {
                    $parcelas_existentes = ParcelasPagarDAO::listarParcelasPorConta($conta_pagar_id);
                    ?>
                    <form id="formContaPagar" action="/planel/conta-a-pagar/atualizar" method="POST">
                        <input type="hidden" name="conta_pagar_id" value="<?= $conta_pagar['id_conta_pagar'] ?>">
                        <input type="hidden" name="nota_fiscal" value="<?= $conta_pagar['fk_notas_fiscais_id_nota_fiscal'] ?>">
                        <input type="hidden" name="fornecedor" value="<?= $conta_pagar['fk_fornecedores_id_fornecedor'] ?>">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nota_fiscal">Nota Fiscal</label>
                                    <input type="text" id="nota_fiscal" class="form-control" value="<?= $conta_pagar['numero'] . ' - ' . $conta_pagar['nome_fornecedor'] ?>" disabled>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="fornecedor">Fornecedor</label>
                                    <input type="text" id="fornecedor" class="form-control" value="<?= $conta_pagar['nome_fornecedor'] ?>" disabled>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="valor">Valor</label>
                                    <input type="text" id="valor" name="valor" class="form-control" value="<?= number_format($conta_pagar['valor'], 2, ',', '.') ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="data_vencimento">Data de Vencimento</label>
                                    <input type="date" id="data_vencimento" name="data_vencimento" class="form-control" value="<?= $conta_pagar['data_vencimento'] ?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="parcela_atual">Parcela Atual</label>
                                    <input type="number" id="parcela_atual" name="parcela_atual" class="form-control" value="<?= $conta_pagar['parcela_atual'] ?>" required min="0">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="parcelas">Parcelas</label>
                                    <input type="number" id="parcelas" name="parcelas" class="form-control" value="<?= $conta_pagar['parcelas'] ?>" required min="1">
                                </div>
                            </div>
                        </div>

                        <hr>
                        
                        <!-- Seção de Parcelas -->
                        <h5>Editar Parcelas</h5>
                        <div id="parcelas_detalhes">
                        <?php
                        $i = 1;
                        if ($parcelas_existentes) {
                            while ($parcela = mysqli_fetch_assoc($parcelas_existentes)) {
                                ?>
                                <div class="row parcela" data-id="<?= $i ?>">
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="valor_parcela_<?= $i ?>">Valor da Parcela <?= $i ?></label>
                                            <input type="text" id="valor_parcela_<?= $i ?>" name="valor_parcela_<?= $i ?>" class="form-control" value="<?= number_format($parcela['valor_parcela'], 2, ',', '.') ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="vencimento_parcela_<?= $i ?>">Vencimento da Parcela <?= $i ?></label>
                                            <input type="date" id="vencimento_parcela_<?= $i ?>" name="vencimento_parcela_<?= $i ?>" class="form-control" value="<?= $parcela['vencimento_parcela'] ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="data_pagamento_<?= $i ?>">Data de Pagamento</label>
                                            <input type="date" id="data_pagamento_<?= $i ?>" name="data_pagamento_<?= $i ?>" class="form-control" value="<?= $parcela['data_pagamento'] ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="tipo_pagamento_<?= $i ?>">Tipo de Pagamento</label>
                                            <select id="tipo_pagamento_<?= $i ?>" name="tipo_pagamento_<?= $i ?>" class="form-control">
                                                <option value="">Selecione</option>
                                                <?php
                                                $query_tipos_pagamento = "SELECT * FROM tipo_pagamento";
                                                $result_tipos_pagamento = mysqli_query($conexao, $query_tipos_pagamento);
                                                while($row_tipo = mysqli_fetch_assoc($result_tipos_pagamento)) {
                                                    $selected = $row_tipo['id_pagamento'] == $parcela['fk_tipo_pagamento_id_pagamento'] ? 'selected' : '';
                                                    echo "<option value='".$row_tipo['id_pagamento']."' $selected>".$row_tipo['tipo_pagamento']."</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <?php
                                $i++;
                            }
                        }
                        ?>
                        </div>

                        <div class="mb-3">
                            <button type="submit" name="editar_conta_pagar" class="btn btn-primary">
                                Salvar<span class="bi-save ms-2"></span>
                            </button>
                        </div>
                    </form>
                    <script>
                        $(document).ready(function() {
                            function applyMasks() {
                                $('input[id^="valor_parcela_"]').mask('000.000.000.000.000,00', {reverse: true});
                            }

                            function updateParcelas() {
                                const parcelas = parseInt($('#parcelas').val());
                                let valor = $('#valor').val();
                                valor = valor.replace(/\./g, '').replace(',', '.');
                                const valorParcela = (parseFloat(valor) / parcelas).toFixed(2).replace('.', ',');
                                const vencimento = new Date($('#data_vencimento').val());

                                let parcelasHtml = '';
                                for (let i = 1; i <= parcelas; i++) {
                                    const vencimentoParcela = new Date(vencimento);
                                    vencimentoParcela.setMonth(vencimentoParcela.getMonth() + (i - 1));
                                    const vencimentoStr = vencimentoParcela.toISOString().split('T')[0];

                                    parcelasHtml += `
                                        <div class="row parcela" data-id="${i}">
                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label for="valor_parcela_${i}">Valor da Parcela ${i}</label>
                                                    <input type="text" id="valor_parcela_${i}" name="valor_parcela_${i}" class="form-control" value="${valorParcela}" required>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label for="vencimento_parcela_${i}">Vencimento da Parcela ${i}</label>
                                                    <input type="date" id="vencimento_parcela_${i}" name="vencimento_parcela_${i}" class="form-control" value="${vencimentoStr}" required>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label for="data_pagamento_${i}">Data de Pagamento</label>
                                                    <input type="date" id="data_pagamento_${i}" name="data_pagamento_${i}" class="form-control" disabled>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label for="tipo_pagamento_${i}">Tipo de Pagamento</label>
                                                    <select id="tipo_pagamento_${i}" name="tipo_pagamento_${i}" class="form-control" disabled>
                                                        <option value="">Selecione</option>
                                                        <?php
                                                        $result_tipos_pagamento = mysqli_query($conexao, $query_tipos_pagamento);
                                                        while($row_tipo = mysqli_fetch_assoc($result_tipos_pagamento)) {
                                                            echo "<option value='".$row_tipo['id_pagamento']."'>".$row_tipo['tipo_pagamento']."</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                    `;
                                }
                                $('#parcelas_detalhes').html(parcelasHtml);
                                applyMasks();
                                toggleParcelasEditable();
                            }

                            function toggleParcelasEditable() {
                                const parcelaAtual = parseInt($('#parcela_atual').val());

                                $('.row.parcela').each(function(index) {
                                    const parcelaIndex = index + 1;
                                    const dataPagamentoField = $(`#data_pagamento_${parcelaIndex}`);
                                    const tipoPagamentoField = $(`#tipo_pagamento_${parcelaIndex}`);

                                    if (parcelaIndex <= parcelaAtual) {
                                        const vencimento = $(`#vencimento_parcela_${parcelaIndex}`).val();
                                        dataPagamentoField.prop('disabled', false);
                                        tipoPagamentoField.prop('disabled', false);

                                        // Se a data de pagamento estiver vazia, definir como a data de vencimento
                                        if (!dataPagamentoField.val()) {
                                            dataPagamentoField.val(vencimento);
                                        }
                                    } else {
                                        dataPagamentoField.prop('disabled', true).val('');
                                        tipoPagamentoField.prop('disabled', true).val('');
                                    }
                                });
                            }

                            function validateForm() {
                                const parcelas = parseInt($('#parcelas').val());
                                let totalParcelas = 0;

                                // Calcula a soma dos valores das parcelas
                                for (let i = 1; i <= parcelas; i++) {
                                    let valorParcela = $(`#valor_parcela_${i}`).val();
                                    valorParcela = valorParcela.replace(/\./g, '').replace(',', '.');
                                    totalParcelas += parseFloat(valorParcela);
                                }

                                let valorTotal = $('#valor').val();
                                valorTotal = valorTotal.replace(/\./g, '').replace(',', '.');

                                if (Math.abs(totalParcelas - parseFloat(valorTotal)) > 0.01) {
                                    alert('A soma dos valores das parcelas deve ser igual ao valor total da Conta a Pagar.');
                                    return false;
                                }

                                let isValid = true;

                                for (let i = 1; i <= parcelas; i++) {
                                    const dataPagamento = $(`#data_pagamento_${i}`).val();
                                    const tipoPagamento = $(`#tipo_pagamento_${i}`).val();
                                    const valor = $(`#valor_parcela_${i}`).val();

                                    if (!dataPagamento && !tipoPagamento && valor) {
                                        continue;
                                    }

                                    if (dataPagamento && !tipoPagamento) {
                                        isValid = false;
                                        alert(`Selecione um tipo de pagamento para a Parcela ${i}!`);
                                        break;
                                    }

                                    if (tipoPagamento && !dataPagamento) {
                                        isValid = false;
                                        alert(`Selecione uma data de pagamento para a Parcela ${i}!`);
                                        break;
                                    }
                                }

                                return isValid;
                            }

                            $('#formContaPagar').on('submit', function(e) {
                                if (!validateForm()) {
                                    e.preventDefault();
                                    return;
                                }
                            });

                            $('#parcelas').on('input change', function() {
                                updateParcelas();
                            });

                            $('#parcela_atual').on('input change', function() {
                                toggleParcelasEditable();
                            });

                            toggleParcelasEditable();
                            applyMasks();
                        });
                    </script>
                    <?php
                } else {
                    echo "<h5>Conta a pagar não encontrada</h5>";
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
