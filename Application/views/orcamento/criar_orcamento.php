<?php
session_start();
require('Application/models/conexao.php');
require('Application/models/material_dao.php');
require('Application/models/servico_dao.php');
?>
<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Adicionar Orçamento</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" 
  integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <style>
    .hidden {
      display: none;
    }
  </style>
</head>

<body data-bs-theme="dark">
  <?php include(__DIR__ . '/../navbar.php'); ?>
  <div class="container mt-5">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h4>Adicionar Orçamento
              <a href="/planel/orcamentos" class="btn btn-danger float-end">
              <span class="bi-arrow-left"></span>&nbsp;Voltar</a>
            </h4>
          </div>
          <div class="card-body">
            <form id="formOrcamento" action="/planel/orcamento/atualizar" method="POST" enctype="multipart/form-data">
              <div class="row">
                <div class="col-md-4">
                  <div class="mb-3">
                    <label for="nome_orcamento">Nome do Orçamento</label>
                    <input type="text" id="nome_orcamento" name="nome_orcamento" class="form-control" required>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="mb-3">
                    <label for="cliente">Cliente</label>
                    <select id="cliente" name="cliente" class="form-control" required>
                      <option value="">Selecione um Cliente</option>
                      <?php
                      $query_clientes = "SELECT * FROM clientes WHERE ativo = TRUE";
                      $result_clientes = mysqli_query($conexao, $query_clientes);
                      while ($row_cliente = mysqli_fetch_assoc($result_clientes)) {
                        echo "<option value='".$row_cliente['id_cliente']."'>".$row_cliente['nome_cliente']."</option>";
                      }
                      ?>
                    </select>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="mb-3">
                    <label for="data_orcamento">Data do Orçamento</label>
                    <input type="date" id="data_orcamento" name="data_orcamento" class="form-control" required>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="mb-3">
                    <label for="validade">Validade</label>
                    <input type="date" id="validade" name="validade" class="form-control" required>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="mb-3">
                    <label for="status">Status</label>
                    <input type="text" id="status" name="status" class="form-control" required>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="mb-3">
                    <label for="arquivo_pdf">Arquivo PDF</label>
                    <input type="file" id="arquivo_pdf" name="arquivo_pdf" class="form-control">
                  </div>
                </div>
              </div>
              <div class="mb-3">
                <label for="observacao">Observação</label>
                <textarea id="observacao" name="observacao" class="form-control"></textarea>
              </div>

              <!-- Materiais -->
              <div class="container mt-4">
                <div class="row">
                  <div class="col-md-12">
                    <div class="d-flex justify-content-between align-items-center">
                      <h5>Materiais</h5>
                      <button type="button" class="btn btn-success" onclick="adicionarMaterial()">
                        <span class="bi-plus"></span>
                      </button>
                    </div>
                    <div id="materiais-container">
                      <div class="row mb-3" id="material-item-1">
                        <div class="col-md-4">
                          <label for="nome_material">Nome do Material</label>
                          <select name="nome_material[]" class="form-control" onchange="buscarPreco(this.value, 1)" required>
                            <option value="">Selecione um Material</option>
                            <?php
                            $query_materiais = "SELECT * FROM materiais WHERE ativo = TRUE";
                            $result_materiais = mysqli_query($conexao, $query_materiais);
                            while ($row_material = mysqli_fetch_assoc($result_materiais)) {
                              echo "<option value='".$row_material['id_material']."'>".$row_material['nome_material']."</option>";
                            }
                            ?>
                          </select>
                        </div>
                        <div class="col-md-2">
                          <label for="quantidade">Quantidade</label>
                          <input type="number" name="quantidade[]" class="form-control" min="0" step="1" onchange="atualizarTotal(1)" required>
                        </div>
                        <div class="col-md-2">
                          <label for="preco">Preço Unitário</label>
                          <input type="number" name="preco[]" id="preco-material-1" class="form-control" step="0.01" onchange="atualizarTotal(1)" min="0" required>
                        </div>
                        <div class="col-md-3">
                          <label for="total">Valor Total</label>
                          <input type="number" name="total[]" id="total-material-1" class="form-control" readonly>
                        </div>
                        <div class="col-md-1 d-flex justify-content-end align-items-center mt-4">
                          <button type="button" class="btn btn-danger" onclick="confirmarRemoverMaterial('material-item-1')">
                            <span class="bi-trash3-fill"></span>
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Serviços -->
              <div class="container mt-4">
                <div class="row">
                  <div class="col-md-12">
                    <div class="d-flex justify-content-between align-items-center">
                      <h5>Serviços</h5>
                      <button type="button" class="btn btn-success" onclick="adicionarServico()">
                        <span class="bi-plus"></span>
                      </button>
                    </div>
                    <div id="servicos-container">
                      <div class="row mb-3" id="servico-item-1">
                        <div class="col-md-4">
                          <label for="nome_servico">Nome do Serviço</label>
                          <select name="nome_servico[]" class="form-control" onchange="buscarPrecoServico(this.value, 1)" required>
                            <option value="">Selecione um Serviço</option>
                            <?php
                            $query_servicos = "SELECT * FROM servicos WHERE ativo = TRUE";
                            $result_servicos = mysqli_query($conexao, $query_servicos);
                            while ($row_servico = mysqli_fetch_assoc($result_servicos)) {
                              echo "<option value='".$row_servico['id_servico']."'>".$row_servico['nome_servico']."</option>";
                            }
                            ?>
                          </select>
                        </div>
                        <div class="col-md-2">
                          <label for="quantidade">Quantidade</label>
                          <input type="number" name="quantidade_servico[]" class="form-control" min="0" step="1" onchange="atualizarTotalServico(1)" required>
                        </div>
                        <div class="col-md-2">
                          <label for="preco">Preço Unitário</label>
                          <input type="number" name="preco_servico[]" id="preco-servico-1" class="form-control" step="0.01" onchange="atualizarTotalServico(1)" min="0" required>
                        </div>
                        <div class="col-md-3">
                          <label for="total">Valor Total</label>
                          <input type="number" name="total_servico[]" id="total-servico-1" class="form-control" readonly>
                        </div>
                        <div class="col-md-1 d-flex justify-content-end align-items-center mt-4">
                          <button type="button" class="btn btn-danger" onclick="confirmarRemoverServico('servico-item-1')">
                            <span class="bi-trash3-fill"></span>
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="mb-3 mt-4">
                <button type="submit" name="criar_orcamento" class="btn btn-primary">
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
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" 
  integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
  </script>

  <script>
    let contadorMateriais = 1;
    let contadorServicos = 1;

    function adicionarMaterial() {
      contadorMateriais++;
      const novoMaterial = `
        <div class="row mb-3" id="material-item-${contadorMateriais}">
          <div class="col-md-4">
            <label for="nome_material">Nome do Material</label>
            <select name="nome_material[]" class="form-control" onchange="buscarPreco(this.value, ${contadorMateriais})" required>
              <option value="">Selecione um Material</option>
              <?php
              $query_materiais = "SELECT * FROM materiais WHERE ativo = TRUE";
              $result_materiais = mysqli_query($conexao, $query_materiais);
              while ($row_material = mysqli_fetch_assoc($result_materiais)) {
                echo "<option value='".$row_material['id_material']."'>".$row_material['nome_material']."</option>";
              }
              ?>
            </select>
          </div>
          <div class="col-md-2">
            <label for="quantidade">Quantidade</label>
            <input type="number" name="quantidade[]" class="form-control" min="1" step="1" onchange="atualizarTotal(${contadorMateriais})" required>
          </div>
          <div class="col-md-2">
            <label for="preco">Preço Unitário</label>
            <input type="number" name="preco[]" id="preco-material-${contadorMateriais}" class="form-control" step="0.01" onchange="atualizarTotal(${contadorMateriais})" required>
          </div>
          <div class="col-md-3">
            <label for="total">Valor Total</label>
            <input type="number" name="total[]" id="total-material-${contadorMateriais}" class="form-control" readonly>
          </div>
          <div class="col-md-1 d-flex justify-content-end align-items-center mt-4">
            <button type="button" class="btn btn-danger" onclick="confirmarRemoverMaterial('material-item-${contadorMateriais}')">
              <span class="bi-trash3-fill"></span>
            </button>
          </div>
        </div>`;
      document.getElementById('materiais-container').insertAdjacentHTML('beforeend', novoMaterial);
    }

    function adicionarServico() {
      contadorServicos++;
      const novoServico = `
        <div class="row mb-3" id="servico-item-${contadorServicos}">
          <div class="col-md-4">
            <label for="nome_servico">Nome do Serviço</label>
            <select name="nome_servico[]" class="form-control" onchange="buscarPrecoServico(this.value, ${contadorServicos})" required>
              <option value="">Selecione um Serviço</option>
              <?php
              $query_servicos = "SELECT * FROM servicos WHERE ativo = TRUE";
              $result_servicos = mysqli_query($conexao, $query_servicos);
              while ($row_servico = mysqli_fetch_assoc($result_servicos)) {
                echo "<option value='".$row_servico['id_servico']."'>".$row_servico['nome_servico']."</option>";
              }
              ?>
            </select>
          </div>
          <div class="col-md-2">
            <label for="quantidade">Quantidade</label>
            <input type="number" name="quantidade_servico[]" class="form-control" min="1" step="1" onchange="atualizarTotalServico(${contadorServicos})" required>
          </div>
          <div class="col-md-2">
            <label for="preco">Preço Unitário</label>
            <input type="number" name="preco_servico[]" id="preco-servico-${contadorServicos}" class="form-control" step="0.01" onchange="atualizarTotalServico(${contadorServicos})" required>
          </div>
          <div class="col-md-3">
            <label for="total">Valor Total</label>
            <input type="number" name="total_servico[]" id="total-servico-${contadorServicos}" class="form-control" readonly>
          </div>
          <div class="col-md-1 d-flex justify-content-end align-items-center mt-4">
            <button type="button" class="btn btn-danger" onclick="confirmarRemoverServico('servico-item-${contadorServicos}')">
              <span class="bi-trash3-fill"></span>
            </button>
          </div>
        </div>`;
      document.getElementById('servicos-container').insertAdjacentHTML('beforeend', novoServico);
    }

    function atualizarTotal(id) {
      const quantidade = document.querySelector(`#material-item-${id} input[name="quantidade[]"]`).value;
      const preco = document.querySelector(`#material-item-${id} input[name="preco[]"]`).value;
      document.getElementById(`total-material-${id}`).value = (quantidade * preco).toFixed(2);
    }

    function atualizarTotalServico(id) {
      const quantidade = document.querySelector(`#servico-item-${id} input[name="quantidade_servico[]"]`).value;
      const preco = document.querySelector(`#servico-item-${id} input[name="preco_servico[]"]`).value;
      document.getElementById(`total-servico-${id}`).value = (quantidade * preco).toFixed(2);
    }

    function confirmarRemoverMaterial(id) {
      if (confirm('Tem certeza que deseja excluir o material?')) {
        removerMaterial(id);
      }
    }

    function confirmarRemoverServico(id) {
      if (confirm('Tem certeza que deseja excluir o serviço?')) {
        removerServico(id);
      }
    }

    function removerMaterial(id) {
      document.getElementById(id).remove();
    }

    function removerServico(id) {
      document.getElementById(id).remove();
    }

    function buscarPreco(materialId, contador) {
      if (materialId) {
        $.ajax({
          url: '/planel/orcamento/atualizar',
          method: 'POST',
          data: { id_material: materialId },
          success: function(response) {
            var result = JSON.parse(response);
            document.getElementById('preco-material-' + contador).value = result.preco;
            atualizarTotal(contador);
          },
          error: function() {
            alert('Erro ao buscar o preço do material.');
          }
        });
      }
    }

    function buscarPrecoServico(servicoId, contador) {
      if (servicoId) {
        $.ajax({
          url: '/planel/orcamento/atualizar',
          method: 'POST',
          data: { id_servico: servicoId },
          success: function(response) {
            var result = JSON.parse(response);
            document.getElementById('preco-servico-' + contador).value = result.preco;
            atualizarTotalServico(contador);
          },
          error: function() {
            alert('Erro ao buscar o preço do serviço.');
          }
        });
      }
    }
  </script>

</body>
</html>
