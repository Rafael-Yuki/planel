<?php
session_start();
require('Application/models/conexao.php');
?>
<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Adicionar Material</title>
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
            <h4>Adicionar Material
              <a href="/planel/materiais" class="btn btn-danger float-end">
              <span class="bi-arrow-left"></span>&nbsp;Voltar</a>
            </h4>
          </div>
          <div class="card-body">
            <form id="formMaterial" action="/planel/material/atualizar" method="POST">
              <div class="mb-3">
                <label for="nome_material">Nome do Material</label>
                <input type="text" id="nome_material" name="nome_material" class="form-control" required>
              </div>
              <div class="mb-3">
                <label for="ncm">NCM</label>
                <input type="text" id="ncm" name="ncm" class="form-control" required>
              </div>
              <div class="mb-3">
                <label for="quantidade">Quantidade</label>
                <input type="number" id="quantidade" name="quantidade" class="form-control" required>
              </div>
              <div class="mb-3">
                <label for="unidade_medida">Unidade de Medida</label>
                <input type="text" id="unidade_medida" name="unidade_medida" class="form-control" required>
              </div>
              <div class="mb-3">
                <label for="valor_compra">Valor de Compra</label>
                <input type="text" id="valor_compra" name="valor_compra" class="form-control valor-mask" required>
              </div>
              <div class="mb-3">
                <label for="valor_venda">Valor de Venda</label>
                <input type="text" id="valor_venda" name="valor_venda" class="form-control valor-mask" required>
              </div>
              <div class="mb-3">
                <label for="data_compra">Data da Compra</label>
                <input type="date" id="data_compra" name="data_compra" class="form-control" required>
              </div>
              <div class="mb-3">
                <label for="fornecedor">Fornecedor</label>
                <select id="fornecedor" name="fornecedor" class="form-control" required>
                  <option value="">Selecione um Fornecedor</option>
                  <?php
                  $query_fornecedores = "SELECT * FROM fornecedores WHERE ativo = TRUE";
                  $result_fornecedores = mysqli_query($conexao, $query_fornecedores);
                  while($row_fornecedor = mysqli_fetch_assoc($result_fornecedores)) {
                      echo "<option value='".$row_fornecedor['id_fornecedor']."'>".$row_fornecedor['nome_fornecedor']."</option>";
                  }
                  ?>
                </select>
              </div>
              <div class="mb-3">
                <button type="submit" name="criar_material" class="btn btn-primary">
                  <span class="bi-save"></span>&nbsp;Salvar
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

        $('#formMaterial').submit(function(event) {
            var valorCompra = $('#valor_compra').val().replace(/\./g, '').replace(',', '.');
            var valorVenda = $('#valor_venda').val().replace(/\./g, '').replace(',', '.');
            var quantidade = $('#quantidade').val();
            var fornecedor = $('#fornecedor').val();
            var valid = true;

            if (isNaN(parseFloat(valorCompra)) || parseFloat(valorCompra) <= 0) {
                $('#valor_compra').addClass('is-invalid');
                valid = false;
            } else {
                $('#valor_compra').removeClass('is-invalid');
            }

            if (isNaN(parseFloat(valorVenda)) || parseFloat(valorVenda) <= 0) {
                $('#valor_venda').addClass('is-invalid');
                valid = false;
            } else {
                $('#valor_venda').removeClass('is-invalid');
            }

            if (quantidade <= 0) {
                $('#quantidade').addClass('is-invalid');
                valid = false;
            } else {
                $('#quantidade').removeClass('is-invalid');
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
