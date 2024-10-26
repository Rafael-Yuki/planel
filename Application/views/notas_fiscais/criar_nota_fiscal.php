<?php
session_start();
require('Application/models/conexao.php');
require('Application/models/fornecedor_dao.php');
?>
<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Adicionar Nota Fiscal</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" 
  integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body data-bs-theme="dark">
  <?php include(__DIR__ . '/../navbar.php');?>
  <div class="container mt-5">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h4>Adicionar Nota Fiscal
              <a href="/planel/notas-fiscais" class="btn btn-danger float-end">
              <span class="bi-arrow-left"></span>&nbsp;Voltar</a>
            </h4>
          </div>
          <div class="card-body">
            <form id="formNotaFiscal" action="/planel/nota-fiscal/atualizar" method="POST" enctype="multipart/form-data">
              <div class="row">
                <div class="col-md-3">
                  <div class="mb-3">
                    <label for="numero" class="form-label">Número da Nota Fiscal</label>
                    <input type="text" id="numero" name="numero" class="form-control" required>
                  </div>
                </div>
                <div class="col-md-9">
                  <div class="mb-3">
                    <label for="fornecedor" class="form-label">Fornecedor</label>
                    <select id="fornecedor" name="fornecedor" class="form-control" required>
                      <option value="">Selecione um Fornecedor</option>
                      <?php
                      $fornecedores = FornecedorDAO::listarFornecedores();
                      while ($fornecedor = mysqli_fetch_assoc($fornecedores)) {
                          echo "<option value='" . $fornecedor['id_fornecedor'] . "'>" . $fornecedor['nome_fornecedor'] . "</option>";
                      }
                      ?>
                    </select>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-4">
                  <div class="mb-3">
                    <label for="data_emissao" class="form-label">Data de Emissão</label>
                    <input type="date" id="data_emissao" name="data_emissao" class="form-control" required>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="mb-3">
                    <label for="parcelas" class="form-label">Número de Parcelas</label>
                    <input type="number" id="parcelas" name="parcelas" class="form-control" required>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="mb-3">
                    <label for="valor_total" class="form-label">Valor Total</label>
                    <input type="text" id="valor_total" name="valor_total" class="form-control valor-mask" required>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-12">
                  <div class="mb-3">
                    <label for="arquivo_xml" class="form-label">Arquivo XML (opcional)</label>
                    <input type="file" id="arquivo_xml" name="arquivo_xml" class="form-control">
                  </div>
                </div>
              </div>

              <div class="mb-3">
                <button type="submit" name="criar_nota_fiscal" class="btn btn-primary">
                  Salvar<span class="bi-save ms-2"></span>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
  <script>
    $(document).ready(function() {
        // Máscara de valor monetário
        $('.valor-mask').mask('000.000.000.000.000,00', {reverse: true});

        $('#formNotaFiscal').submit(function(event) {
            var valorTotal = $('#valor_total').val().replace(/\./g, '').replace(',', '.');
            var parcelas = $('#parcelas').val();
            var fornecedor = $('#fornecedor').val();
            var valid = true;

            if (isNaN(parseFloat(valorTotal)) || parseFloat(valorTotal) <= 0) {
                $('#valor_total').addClass('is-invalid');
                valid = false;
            } else {
                $('#valor_total').removeClass('is-invalid');
            }

            if (parcelas <= 0) {
                $('#parcelas').addClass('is-invalid');
                valid = false;
            } else {
                $('#parcelas').removeClass('is-invalid');
            }

            if (fornecedor === "") {
                $('#fornecedor').addClass('is-invalid');
                valid = false;
            } else {
                $('#fornecedor').removeClass('is-invalid');
            }

            if (!valid) {
                event.preventDefault();
            }
        });
    });
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" 
  integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
  </script>
</body>
</html>
