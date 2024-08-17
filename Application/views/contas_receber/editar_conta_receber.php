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
                    ?>
                    <form id="formContaReceber" action="/planel/conta-a-receber/atualizar" method="POST">
                        <input type="hidden" name="conta_receber_id" value="<?= $conta_receber['id_conta_receber'] ?>">
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
                                            $selected = $row_orcamento['id_orcamento'] == $conta_receber['fk_orcamentos_id_orcamento'] ? 'selected' : '';
                                            echo "<option value='".$row_orcamento['id_orcamento']."' $selected>".$row_orcamento['nome_orcamento']."</option>";
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
                                            $selected = $row_cliente['id_cliente'] == $conta_receber['fk_clientes_id_cliente'] ? 'selected' : '';
                                            echo "<option value='".$row_cliente['id_cliente']."' $selected>".$row_cliente['nome_cliente']."</option>";
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
                        <?php
                        $parcelas = ParcelasReceberDAO::listarParcelasPorConta($conta_receber_id);
                        if ($parcelas) {
                            while ($parcela = mysqli_fetch_assoc($parcelas)) {
                                $parcela_id = $parcela['id_parcela_receber'];
                                ?>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="parcelas_existentes[<?= $parcela_id ?>][valor]">Valor da Parcela</label>
                                            <input type="text" id="valor_parcela_<?= $parcela_id ?>" name="parcelas_existentes[<?= $parcela_id ?>][valor]" class="form-control" value="<?= number_format($parcela['valor_parcela'], 2, ',', '.') ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="parcelas_existentes[<?= $parcela_id ?>][vencimento]">Vencimento da Parcela</label>
                                            <input type="date" id="vencimento_parcela_<?= $parcela_id ?>" name="parcelas_existentes[<?= $parcela_id ?>][vencimento]" class="form-control" value="<?= $parcela['vencimento_parcela'] ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="parcelas_existentes[<?= $parcela_id ?>][data_recebimento]">Data de Recebimento</label>
                                            <input type="date" id="data_recebimento_<?= $parcela_id ?>" name="parcelas_existentes[<?= $parcela_id ?>][data_recebimento]" class="form-control" value="<?= $parcela['data_recebimento'] ?>" <?= $parcela_id <= $conta_receber['parcela_atual'] ? '' : 'disabled' ?>>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="parcelas_existentes[<?= $parcela_id ?>][tipo_pagamento]">Tipo de Pagamento</label>
                                            <select id="tipo_pagamento_<?= $parcela_id ?>" name="parcelas_existentes[<?= $parcela_id ?>][tipo_pagamento]" class="form-control" <?= $parcela_id <= $conta_receber['parcela_atual'] ? '' : 'disabled' ?>>
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
                            }
                        }
                        ?>

                        <div class="mb-3">
                            <button type="submit" name="editar_conta_receber" class="btn btn-primary">
                            <span class="bi-save"></span>&nbsp;Salvar
                            </button>
                        </div>
                    </form>
                    <script>
                      $(document).ready(function(){
                          $('#valor').mask('000.000.000.000.000,00', {reverse: true});

                          $('input[id^="valor_parcela_"]').mask('000.000.000.000.000,00', {reverse: true});

                          $('#formContaReceber').on('submit', function(e) {
                              const parcelas = parseInt($('#parcelas').val());
                              const parcelaAtual = parseInt($('#parcela_atual').val());

                              if (parcelaAtual > parcelas) {
                                  e.preventDefault();
                                  alert('A parcela atual não pode ser maior que o número total de parcelas.');
                                  return;
                              }

                              $('input[id^="valor_parcela_"]').each(function() {
                                  if ($(this).val() === '' || $(this).val() === '0,00') {
                                      e.preventDefault();
                                      alert('Por favor, preencha todos os valores das parcelas.');
                                      return false;
                                  }
                              });
                          });

                          // Função para bloquear ou desbloquear a dupla de campos "Data de Recebimento" e "Tipo de Pagamento"
                          function toggleFields(parcelaAtual) {
                              $('input[id^="data_recebimento_"], select[id^="tipo_pagamento_"]').each(function(index) {
                                  const parcelaIndex = index / 2 + 1; // Divide por 2 porque há dois campos por parcela (data e tipo)
                                  const campoRecebimento = $(`input[id^="data_recebimento_"]:eq(${index})`);
                                  const campoPagamento = $(`select[id^="tipo_pagamento_"]:eq(${index})`);

                                  if (parcelaIndex <= parcelaAtual) {
                                      campoRecebimento.removeAttr('disabled');
                                      campoPagamento.removeAttr('disabled');
                                  } else {
                                      campoRecebimento.attr('disabled', 'disabled').val('');
                                      campoPagamento.attr('disabled', 'disabled').val('');
                                  }
                              });
                          }

                          // Atualiza os campos ao mudar a parcela atual
                          $('#parcela_atual').on('input', function() {
                              const parcelaAtual = parseInt($(this).val());
                              toggleFields(parcelaAtual);
                          });

                          // Desbloqueia os campos ao carregar a página
                          const parcelaAtual = parseInt($('#parcela_atual').val());
                          toggleFields(parcelaAtual);
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
