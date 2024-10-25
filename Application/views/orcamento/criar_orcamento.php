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

    .input-group {
      display: flex;
      align-items: center;
      width: 100%;
    }

    .input-group-prepend .item-number {
      background-color: #e9ecef;
      color: #495057;
      border: 1px solid #ced4da;
      border-top-left-radius: .25rem;
      border-bottom-left-radius: .25rem;
      padding: 0.375rem 0.75rem;
    }

    .input-group .form-control {
      border-left: 0;
    }

    .input-group-append .btn-group-actions {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      margin-left: auto;
    }

    .input-group-append button {
      border-top-left-radius: 0;
      border-bottom-left-radius: 0;
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
            <form id="formOrcamento" action="/planel/orcamento/atualizar" method="POST" enctype="multipart/form-data" onsubmit="return verificarEnvio(event)">
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

              <!-- Itens do Orçamento -->
              <div class="container mt-4">
                <div class="row">
                  <div class="col-md-12">
                    <div class="d-flex justify-content-between align-items-center">
                      <h4>Itens do Orçamento</h4>
                      <div class="btn-group-actions">
                        <button type="button" class="btn btn-success" onclick="adicionarItem()">
                          <span class="bi-plus"></span>
                        </button>
                        <button type="button" class="btn btn-danger" onclick="removerTodosItens()">
                          <span class="bi-trash3-fill"></span>
                        </button>
                      </div>
                    </div>
                    <hr>
                    <div id="itens-container">
                      <!-- Os itens serão gerados dinamicamente aqui -->
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
    let contadorItens = 1;
    let contadorMateriais = 1;
    let contadorServicos = 1;

    function adicionarItem() {
      const idNovoItem = 'item-' + contadorItens;
      contadorItens++;
      
      const novoItem = `
        <div class="container mt-4" id="${idNovoItem}">
          <div class="input-group">
            <span class="input-group-text item-number">${contadorItens - 1}º</span>
            <input type="text" name="nome_item[]" class="form-control input-group-item-name" placeholder="Nome do Item" required>
            <div class="btn-group-actions ms-2">
              <button type="button" class="btn btn-success" onclick="adicionarItem()">
                <span class="bi-plus"></span>
              </button>
              <button type="button" class="btn btn-danger" onclick="removerElemento('${idNovoItem}')">
                <span class="bi-trash3-fill"></span>
              </button>
            </div>
          </div>
          <div class="row mt-4">
            <div class="col-md-12">
              <textarea name="descricao_item[]" class="form-control" placeholder="Descrição do Item" required></textarea>
            </div>
          </div>

          <!-- Seção de Materiais e Serviços -->
          <div class="row mt-3">
            <div class="col-md-12 d-flex justify-content-between align-items-center">
              <h4>Materiais</h4>
              <button type="button" class="btn btn-success" onclick="adicionarMaterialAoItem('${idNovoItem}')">
                <span class="bi-plus"></span>
              </button>
            </div>
            <div id="materiais-${idNovoItem}" class="mt-2"></div>
          </div>
          <hr>
          <div class="row mt-3">
            <div class="col-md-12 d-flex justify-content-between align-items-center">
              <h4>Serviços</h4>
              <button type="button" class="btn btn-success" onclick="adicionarServicoAoItem('${idNovoItem}')">
                <span class="bi-plus"></span>
              </button>
            </div>
            <div id="servicos-${idNovoItem}" class="mt-2"></div>
          </div>

          <div class="row mt-3">
            <div class="col-md-4 ms-auto">
              <label for="valor_total_item">Valor Total do Item</label>
              <input type="number" name="valor_total_item[]" id="valor-total-item-${idNovoItem}" class="form-control" step="0.01" oninput="atualizarValorManual('${idNovoItem}')">
            </div>
          </div>
          <hr>
        </div>`;
      document.getElementById('itens-container').insertAdjacentHTML('beforeend', novoItem);
      recontarItens();
    }

    function recontarItens() {
      const itens = document.querySelectorAll('#itens-container > .container');
      itens.forEach((item, index) => {
        const numeroElemento = item.querySelector('.item-number');
        if (numeroElemento) {
          numeroElemento.textContent = `${index + 1}º`;
        }
      });
    }

    function adicionarMaterialAoItem(idItem) {
      const idNovoMaterial = `material-${idItem}-${contadorMateriais}`;
      contadorMateriais++;
      
      const novoMaterial = `
        <div class="row mb-3" id="${idNovoMaterial}">
          <div class="col-md-5">
            <label for="nome_material">Nome do Material</label>
            <select name="materiais-${idItem}[]" class="form-control" onchange="buscarPreco(this.value, '${idNovoMaterial}')" required>
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
          <div class="col-md-1">
            <label for="quantidade">Quantidade</label>
            <input type="number" name="quantidade-${idItem}[]" class="form-control" min="1" step="1" onchange="atualizarTotal('${idNovoMaterial}', '${idItem}')" required>
          </div>
          <div class="col-md-2">
            <label for="preco">Preço Unitário</label>
            <input type="number" name="preco-${idItem}[]" id="preco-${idNovoMaterial}" class="form-control" step="0.01" onchange="atualizarTotal('${idNovoMaterial}', '${idItem}')" required>
          </div>
          <div class="col-md-3">
            <label for="total">Valor Total</label>
            <input type="number" name="total-${idItem}[]" id="total-${idNovoMaterial}" class="form-control" readonly>
          </div>
          <div class="col-md-1 d-flex justify-content-end align-items-center mt-4">
            <button type="button" class="btn btn-danger" onclick="removerElemento('${idNovoMaterial}')">
              <span class="bi-trash3-fill"></span>
            </button>
          </div>
        </div>`;
      
      document.getElementById(`materiais-${idItem}`).insertAdjacentHTML('beforeend', novoMaterial);
    }

    function adicionarServicoAoItem(idItem) {
      const idNovoServico = `servico-${idItem}-${contadorServicos}`;
      contadorServicos++;
      
      const novoServico = `
        <div class="row mb-3" id="${idNovoServico}">
          <div class="col-md-5">
            <label for="nome_servico">Nome do Serviço</label>
            <select name="servicos-${idItem}[]" class="form-control" onchange="buscarPrecoServico(this.value, '${idNovoServico}')" required>
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
          <div class="col-md-1">
            <label for="quantidade">Quantidade</label>
            <input type="number" name="quantidade_servico-${idItem}[]" class="form-control" min="1" step="1" onchange="atualizarTotalServico('${idNovoServico}', '${idItem}')" required>
          </div>
          <div class="col-md-2">
            <label for="preco">Preço Unitário</label>
            <input type="number" name="preco_servico-${idItem}[]" id="preco-${idNovoServico}" class="form-control" step="0.01" onchange="atualizarTotalServico('${idNovoServico}', '${idItem}')" required>
          </div>
          <div class="col-md-3">
            <label for="total">Valor Total</label>
            <input type="number" name="total_servico-${idItem}[]" id="total-${idNovoServico}" class="form-control" readonly>
          </div>
          <div class="col-md-1 d-flex justify-content-end align-items-center mt-4">
            <button type="button" class="btn btn-danger" onclick="removerElemento('${idNovoServico}')">
              <span class="bi-trash3-fill"></span>
            </button>
          </div>
        </div>`;
      
      document.getElementById(`servicos-${idItem}`).insertAdjacentHTML('beforeend', novoServico);
    }

    function verificarEnvio(event) {
      const materiais = [];
      const servicos = [];

      // Loop para capturar todos os itens adicionados
      document.querySelectorAll('#itens-container > .container').forEach((item, index) => {
        const idItem = item.getAttribute('id');
        const nomeItem = item.querySelector('.input-group-item-name').value;
        const descricaoItem = item.querySelector('textarea[name="descricao_item[]"]').value;
        const valorTotalItem = item.querySelector(`#valor-total-item-${idItem}`).value;

        // Capturar os materiais do item
        const materiaisDoItem = [];
        document.querySelectorAll(`#materiais-${idItem} select[name^="materiais-"]`).forEach((materialSelect, i) => {
          const materialId = materialSelect.value;
          const quantidade = materialSelect.closest('.row').querySelector(`input[name^="quantidade-"]`).value;
          const preco = materialSelect.closest('.row').querySelector(`input[name^="preco-"]`).value;

          if (materialId && quantidade && preco) {
            materiaisDoItem.push({ materialId, quantidade, preco });
          }
        });

        // Capturar os serviços do item
        const servicosDoItem = [];
        document.querySelectorAll(`#servicos-${idItem} select[name^="servicos-"]`).forEach((servicoSelect, j) => {
          const servicoId = servicoSelect.value;
          const quantidade = servicoSelect.closest('.row').querySelector(`input[name^="quantidade_servico-"]`).value;
          const preco = servicoSelect.closest('.row').querySelector(`input[name^="preco_servico-"]`).value;

          if (servicoId && quantidade && preco) {
            servicosDoItem.push({ servicoId, quantidade, preco });
          }
        });

        // Adicionar itens ao array principal
        materiais.push({ idItem, materiaisDoItem });
        servicos.push({ idItem, servicosDoItem });
      });

      // Armazenar os dados capturados em campos ocultos para envio
      const form = document.getElementById('formOrcamento');
      const materiaisInput = document.createElement('input');
      materiaisInput.type = 'hidden';
      materiaisInput.name = 'materiaisCapturados';
      materiaisInput.value = JSON.stringify(materiais);

      const servicosInput = document.createElement('input');
      servicosInput.type = 'hidden';
      servicosInput.name = 'servicosCapturados';
      servicosInput.value = JSON.stringify(servicos);

      form.appendChild(materiaisInput);
      form.appendChild(servicosInput);

      // Retornar true para permitir o envio do formulário
      return true;
    }

    // Restante das funções
    function removerElemento(id) {
      document.getElementById(id).remove();
      recontarItens();
      atualizarValorTotalDoItem(id.split('-')[1]);
    }

    function removerTodosItens() {
      document.getElementById('itens-container').innerHTML = '';
      contadorItens = 1;
    }

    function buscarPreco(materialId, contador) {
      if (materialId) {
        $.ajax({
          url: '/planel/orcamento/atualizar',
          method: 'POST',
          data: { id_material: materialId },
          success: function(response) {
            var result = JSON.parse(response);
            document.getElementById('preco-' + contador).value = result.preco;
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
            document.getElementById('preco-' + contador).value = result.preco;
            atualizarTotalServico(contador);
          },
          error: function() {
            alert('Erro ao buscar o preço do serviço.');
          }
        });
      }
    }

    function atualizarTotal(id, idItem) {
      const quantidade = parseFloat(document.querySelector(`#${id} input[name^="quantidade"]`).value) || 0;
      const preco = parseFloat(document.querySelector(`#${id} input[name^="preco"]`).value) || 0;
      const total = (quantidade * preco).toFixed(2);
      document.getElementById(`total-${id}`).value = total;
      atualizarValorTotalDoItem(idItem);
    }

    function atualizarTotalServico(id, idItem) {
      const quantidade = parseFloat(document.querySelector(`#${id} input[name^="quantidade_servico"]`).value) || 0;
      const preco = parseFloat(document.querySelector(`#${id} input[name^="preco_servico"]`).value) || 0;
      const total = (quantidade * preco).toFixed(2);
      document.getElementById(`total-${id}`).value = total;
      atualizarValorTotalDoItem(idItem);
    }

    function atualizarValorTotalDoItem(idItem) {
      const totaisMateriais = document.querySelectorAll(`#materiais-${idItem} input[id^="total-"]`);
      const totaisServicos = document.querySelectorAll(`#servicos-${idItem} input[id^="total-"]`);

      let totalItem = 0;
      totaisMateriais.forEach(input => totalItem += parseFloat(input.value) || 0);
      totaisServicos.forEach(input => totalItem += parseFloat(input.value) || 0);

      const valorTotalInput = document.getElementById(`valor-total-item-${idItem}`);
      if (valorTotalInput) {
        valorTotalInput.value = totalItem.toFixed(2);
      }
    }

    function atualizarValorManual(idItem) {
      const valorTotalInput = document.getElementById(`valor-total-item-${idItem}`);
      valorTotalInput.setAttribute('data-editado-manualmente', 'true');
    }
  </script>

</body>
</html>
