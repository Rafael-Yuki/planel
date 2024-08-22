<?php
session_start();
require('Application/models/conexao.php');
require('Application/models/contas_receber_dao.php');
require('Application/models/parcelas_receber_dao.php');
?>
<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Editar Conta a Receber</title>
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
            <h4>Editar Conta a Receber
              <a href="/planel/contas-a-receber" class="btn btn-danger float-end">
              <span class="bi-arrow-left"></span>&nbsp;Voltar</a>
            </h4>
          </div>
          <div class="card-body">
            <?php
            if (isset($_GET['id'])) {
                $conta_receber_id = mysqli_real_escape_string($conexao, $_GET['id']);
                $conta_receber = ContasReceberDAO::obterContaReceber($conta_receber_id);

                if ($conta_receber) {
                    $parcelas_existentes = ParcelasReceberDAO::listarParcelasPorConta($conta_receber_id);
                    ?>
                    <form id="formContaReceber" action="/planel/conta-a-receber/atualizar" method="POST">
                        <input type="hidden" name="conta_receber_id" value="<?= $conta_receber['id_conta_receber'] ?>">
                        <input type="hidden" name="orcamento" value="<?= $conta_receber['fk_orcamentos_id_orcamento'] ?>">
                        <input type="hidden" name="cliente" value="<?= $conta_receber['fk_clientes_id_cliente'] ?>">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="orcamento">Orçamento</label>
                                    <input type="text" id="orcamento" class="form-control" value="<?= $conta_receber['nome_orcamento'] ?>" disabled>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="cliente">Cliente</label>
                                    <input type="text" id="cliente" class="form-control" value="<?= $conta_receber['nome_cliente'] ?>" disabled>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="valor">Valor</label>
                                    <input type="text" id="valor" name="valor" class="form-control" value="<?= number_format($conta_receber['valor'], 2, ',', '.') ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="data_vencimento">Data de Vencimento</label>
                                    <input type="date" id="data_vencimento" name="data_vencimento" class="form-control" value="<?= $conta_receber['data_vencimento'] ?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="parcela_atual">Parcela Atual</label>
                                    <input type="number" id="parcela_atual" name="parcela_atual" class="form-control" value="<?= $conta_receber['parcela_atual'] ?>" required min="0">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="parcelas">Parcelas</label>
                                    <input type="number" id="parcelas" name="parcelas" class="form-control" value="<?= $conta_receber['parcelas'] ?>" required min="1">
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
                                            <label for="data_recebimento_<?= $i ?>">Data de Recebimento</label>
                                            <input type="date" id="data_recebimento_<?= $i ?>" name="data_recebimento_<?= $i ?>" class="form-control" value="<?= $parcela['data_recebimento'] ?>">
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
                            <button type="submit" name="editar_conta_receber" class="btn btn-primary">
                            <span class="bi-save"></span>&nbsp;Salvar
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

                                // Preserva os valores atuais dos campos antes da atualização
                                let tiposPagamento = [];
                                let valoresParcelas = [];
                                let vencimentosParcelas = [];
                                let datasRecebimento = [];

                                $('.row.parcela').each(function(index) {
                                    const i = index + 1;
                                    tiposPagamento[i] = $(`#tipo_pagamento_${i}`).val();
                                    valoresParcelas[i] = $(`#valor_parcela_${i}`).val();
                                    vencimentosParcelas[i] = $(`#vencimento_parcela_${i}`).val();
                                    datasRecebimento[i] = $(`#data_recebimento_${i}`).val();
                                });

                                let parcelasHtml = '';
                                for (let i = 1; i <= parcelas; i++) {
                                    const vencimentoParcela = new Date(vencimento);
                                    vencimentoParcela.setMonth(vencimentoParcela.getMonth() + (i - 1));
                                    const vencimentoStr = vencimentoParcela.toISOString().split('T')[0];

                                    // Reaplica os valores preservados ou usa o padrão
                                    const valorParcelaExistente = valoresParcelas[i] || valorParcela;
                                    const vencimentoParcelaExistente = vencimentosParcelas[i] || vencimentoStr;
                                    const dataRecebimentoExistente = datasRecebimento[i] || '';
                                    const tipoPagamentoExistente = tiposPagamento[i] || '';

                                    parcelasHtml += `
                                        <div class="row parcela" data-id="${i}">
                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label for="valor_parcela_${i}">Valor da Parcela ${i}</label>
                                                    <input type="text" id="valor_parcela_${i}" name="valor_parcela_${i}" class="form-control" value="${valorParcelaExistente}" required>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label for="vencimento_parcela_${i}">Vencimento da Parcela ${i}</label>
                                                    <input type="date" id="vencimento_parcela_${i}" name="vencimento_parcela_${i}" class="form-control" value="${vencimentoParcelaExistente}" required>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label for="data_recebimento_${i}">Data de Recebimento</label>
                                                    <input type="date" id="data_recebimento_${i}" name="data_recebimento_${i}" class="form-control" value="${dataRecebimentoExistente}">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label for="tipo_pagamento_${i}">Tipo de Pagamento</label>
                                                    <select id="tipo_pagamento_${i}" name="tipo_pagamento_${i}" class="form-control">
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

                                // Reaplica os tipos de pagamento preservados
                                for (let i = 1; i <= parcelas; i++) {
                                    $(`#tipo_pagamento_${i}`).val(tiposPagamento[i]);
                                }

                                applyMasks();
                                toggleParcelasEditable();
                            }

                            function toggleParcelasEditable() {
                                const parcelaAtual = parseInt($('#parcela_atual').val());

                                $('.row.parcela').each(function(index) {
                                    const parcelaIndex = index + 1;
                                    const dataRecebimentoField = $(`#data_recebimento_${parcelaIndex}`);
                                    const tipoPagamentoField = $(`#tipo_pagamento_${parcelaIndex}`);

                                    if (parcelaIndex <= parcelaAtual) {
                                        dataRecebimentoField.prop('disabled', false);
                                        tipoPagamentoField.prop('disabled', false);
                                    } else {
                                        dataRecebimentoField.prop('disabled', true).val('');
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
                                    alert('A soma dos valores das parcelas deve ser igual ao valor total da Conta a Receber.');
                                    return false;
                                }

                                let isValid = true;

                                for (let i = 1; i <= parcelas; i++) {
                                    const dataRecebimento = $(`#data_recebimento_${i}`).val();
                                    const tipoPagamento = $(`#tipo_pagamento_${i}`).val();
                                    const valor = $(`#valor_parcela_${i}`).val();

                                    if (!dataRecebimento && !tipoPagamento && valor) {
                                        continue;
                                    }

                                    if (dataRecebimento && !tipoPagamento) {
                                        isValid = false;
                                        alert(`Selecione um tipo de pagamento para a Parcela ${i}!`);
                                        break;
                                    }

                                    if (tipoPagamento && !dataRecebimento) {
                                        isValid = false;
                                        alert(`Selecione uma data de recebimento para a Parcela ${i}!`);
                                        break;
                                    }
                                }

                                return isValid;
                            }

                            $('#formContaReceber').on('submit', function(e) {
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
                    echo "<h5>Conta a receber não encontrada</h5>";
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
