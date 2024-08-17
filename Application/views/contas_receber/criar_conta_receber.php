<?php
session_start();
require('Application/models/conexao.php');
require('Application/controllers/parcelas_receber_controller.php'); // Incluindo o controlador de parcelas
?>
<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Adicionar Conta a Receber</title>
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
            <h4>Adicionar Conta a Receber
              <a href="/planel/contas-a-receber" class="btn btn-danger float-end">
              <span class="bi-arrow-left"></span>&nbsp;Voltar</a>
            </h4>
          </div>
          <div class="card-body">
            <form id="formContaReceber" action="/planel/conta-a-receber/atualizar" method="POST">
              <div class="row">
                <div class="col-md-6">
                  <div class="mb-3">
                      <label for="orcamento">Orçamento</label>
                      <select id="orcamento" name="orcamento" class="form-control" required>
                      <option value="">Selecione um Orçamento</option>
                      <?php
                      $query_orcamentos = "SELECT * FROM orcamentos WHERE ativo = TRUE";
                      $result_orcamentos = mysqli_query($conexao, $query_orcamentos);
                      while($row_orcamento = mysqli_fetch_assoc($result_orcamentos)) {
                          echo "<option value='".$row_orcamento['id_orcamento']."'>".$row_orcamento['nome_orcamento']."</option>";
                      }
                      ?>
                      </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                      <label for="cliente">Cliente</label>
                      <select id="cliente" name="cliente" class="form-control" required>
                      <option value="">Selecione um Cliente</option>
                      <?php
                      $query_clientes = "SELECT * FROM clientes WHERE ativo = TRUE";
                      $result_clientes = mysqli_query($conexao, $query_clientes);
                      while($row_cliente = mysqli_fetch_assoc($result_clientes)) {
                          echo "<option value='".$row_cliente['id_cliente']."'>".$row_cliente['nome_cliente']."</option>";
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
                <button type="submit" name="criar_conta_receber" class="btn btn-primary">
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
            const parcelaAtual = parseInt($('#parcela_atual').val());

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
                                <input type="text" id="valor_parcela_${i}" name="valor_parcela_${i}" class="form-control" value="${valorParcela}" readonly>
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
                                <label for="data_recebimento_${i}">Data de Recebimento da Parcela ${i}</label>
                                <input type="date" id="data_recebimento_${i}" name="data_recebimento_${i}" class="form-control" ${i <= parcelaAtual ? `value="${vencimentoStr}"` : ''} ${i <= parcelaAtual ? '' : 'disabled'}>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="tipo_pagamento_${i}">Tipo de Pagamento da Parcela ${i}</label>
                                <select id="tipo_pagamento_${i}" name="tipo_pagamento_${i}" class="form-control" ${i <= parcelaAtual ? '' : 'disabled'}>
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
        }

        $('#parcelas, #data_vencimento, #parcela_atual').on('input change', function() {
            updateParcelas();
        });

        $('#formContaReceber').on('submit', function(e) {
            const parcelas = parseInt($('#parcelas').val());
            const parcelaAtual = parseInt($('#parcela_atual').val());

            // Validação para impedir criação se data de recebimento estiver sem tipo de pagamento
            for (let i = 1; i <= parcelas; i++) {
                const dataRecebimento = $(`#data_recebimento_${i}`).val();
                const tipoPagamento = $(`#tipo_pagamento_${i}`).val();
                if (dataRecebimento && !tipoPagamento) {
                    e.preventDefault();
                    alert(`Selecione um método de pagamento para a Parcela ${i}!`);
                    return;
                }
            }

            if (parcelaAtual > parcelas) {
                e.preventDefault();
                alert('A parcela atual não pode ser maior que o número total de parcelas.');
            }
        });

        // Atualizar parcelas no carregamento inicial
        updateParcelas();
    });
  </script>
</body>
</html>
