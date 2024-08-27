<?php
session_start();
require('Application/models/conexao.php');
require('Application/controllers/parcelas_pagar_controller.php'); // Incluindo o controlador de parcelas
?>
<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Adicionar Conta a Pagar</title>
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
            <h4>Adicionar Conta a Pagar
              <a href="/planel/contas-a-pagar" class="btn btn-danger float-end">
              <span class="bi-arrow-left"></span>&nbsp;Voltar</a>
            </h4>
          </div>
          <div class="card-body">
            <form id="formContaPagar" action="/planel/conta-a-pagar/atualizar" method="POST">
              <div class="row">
                <div class="col-md-6">
                  <div class="mb-3">
                      <label for="nota_fiscal">Nota Fiscal</label>
                      <select id="nota_fiscal" name="nota_fiscal" class="form-control" required>
                      <option value="">Selecione uma Nota Fiscal</option>
                      <?php
                      $query_notas_fiscais = "
                          SELECT nf.id_nota_fiscal, nf.numero, f.nome_fornecedor 
                          FROM notas_fiscais nf
                          INNER JOIN fornecedores f ON nf.fk_fornecedores_id_fornecedor = f.id_fornecedor
                          WHERE nf.ativo = TRUE
                      ";
                      $result_notas_fiscais = mysqli_query($conexao, $query_notas_fiscais);
                      while($row_nota = mysqli_fetch_assoc($result_notas_fiscais)) {
                          echo "<option value='".$row_nota['id_nota_fiscal']."'>".$row_nota['numero']." - ".$row_nota['nome_fornecedor']."</option>";
                      }
                      ?>
                      </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                      <label for="fornecedor">Fornecedor</label>
                      <select id="fornecedor" name="fornecedor" class="form-control" required>
                      <option value="">Selecione um Fornecedor</option>
                      <?php
                      $query_fornecedores = "SELECT id_fornecedor, nome_fornecedor FROM fornecedores WHERE ativo = TRUE";
                      $result_fornecedores = mysqli_query($conexao, $query_fornecedores);
                      while($row_fornecedor = mysqli_fetch_assoc($result_fornecedores)) {
                          echo "<option value='".$row_fornecedor['id_fornecedor']."'>".$row_fornecedor['nome_fornecedor']."</option>";
                      }
                      ?>
                      </select>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-6">
                  <div class="mb-3">
                      <label for="valor">Valor</label>
                      <input type="text" id="valor" name="valor" class="form-control" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                      <label for="data_vencimento">Data de Vencimento</label>
                      <input type="date" id="data_vencimento" name="data_vencimento" class="form-control" required>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-6">
                  <div class="mb-3">
                      <label for="parcela_atual">Parcela Atual</label>
                      <input type="number" id="parcela_atual" name="parcela_atual" class="form-control" required min="0">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                      <label for="parcelas">Parcelas</label>
                      <input type="number" id="parcelas" name="parcelas" class="form-control" required min="1">
                  </div>
                </div>
              </div>

              <hr>

              <!-- Seção de Parcelas -->
              <div id="parcelas_detalhes"></div>

              <div class="mb-3">
                <button type="submit" name="criar_conta_pagar" class="btn btn-primary">
                <span class="bi-save"></span>&nbsp;Salvar
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script>
    $(document).ready(function(){
        $('#valor').mask('000.000.000.000.000,00', {reverse: true});

        function updateParcelas() {
            const parcelas = parseInt($('#parcelas').val());
            let valor = $('#valor').val();
            valor = valor.replace(/\./g, '').replace(',', '.'); // Remove pontos e substitui vírgula por ponto
            const valorParcela = (parseFloat(valor) / parcelas).toFixed(2).replace('.', ','); // Calcula o valor da parcela e formata

            const vencimento = new Date($('#data_vencimento').val());

            let parcelasHtml = '';
            for (let i = 1; i <= parcelas; i++) {
                const vencimentoParcela = new Date(vencimento);
                vencimentoParcela.setMonth(vencimentoParcela.getMonth() + (i - 1));

                const vencimentoStr = vencimentoParcela.toISOString().split('T')[0]; // Formato AAAA-MM-DD

                parcelasHtml += `
                    <div class="row">
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
                                <label for="data_pagamento_${i}">Data de Pagamento da Parcela ${i}</label>
                                <input type="date" id="data_pagamento_${i}" name="data_pagamento_${i}" class="form-control" disabled>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="tipo_pagamento_${i}">Tipo de Pagamento da Parcela ${i}</label>
                                <select id="tipo_pagamento_${i}" name="tipo_pagamento_${i}" class="form-control" disabled>
                                    <option value="">Selecione</option>
                                    <?php
                                    $query_tipos_pagamento = "SELECT * FROM tipo_pagamento";
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

            // Aplica a máscara nos novos campos de valor de parcela
            $('input[id^="valor_parcela_"]').mask('000.000.000.000.000,00', {reverse: true});
        }

        function toggleParcelasEditable() {
            const parcelaAtual = parseInt($('#parcela_atual').val());

            $('.row').each(function(index) {
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

        $('#parcelas, #data_vencimento, #valor').on('input change', function() {
            updateParcelas();
            toggleParcelasEditable();
        });

        $('#parcela_atual').on('input change', function() {
            toggleParcelasEditable();
        });

        $('#formContaPagar').on('submit', function(e) {
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
                e.preventDefault();
                alert('A soma dos valores das parcelas deve ser igual ao valor total da Conta a Pagar.');
                return false;
            }

            // Validação para impedir criação se data de pagamento estiver sem tipo de pagamento
            for (let i = 1; i <= parcelas; i++) {
                const dataPagamento = $(`#data_pagamento_${i}`).val();
                const tipoPagamento = $(`#tipo_pagamento_${i}`).val();
                if (dataPagamento && !tipoPagamento) {
                    e.preventDefault();
                    alert(`Selecione um método de pagamento para a Parcela ${i}!`);
                    return;
                }
            }
        });

        // Atualizar parcelas e bloqueio de campos no carregamento inicial
        updateParcelas();
        toggleParcelasEditable();
    });
  </script>
</body>
</html>
