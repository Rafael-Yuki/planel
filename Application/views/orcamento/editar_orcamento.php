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
  <title>Editar Orçamento</title>
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
            <h4>Editar Orçamento
              <a href="/planel/orcamentos" class="btn btn-danger float-end">
              <span class="bi-arrow-left"></span>&nbsp;Voltar</a>
            </h4>
          </div>
          <div class="card-body">
            <?php
            if (isset($_GET['id'])) {
              $orcamento_id = mysqli_real_escape_string($conexao, $_GET['id']);
              $sql = "SELECT * FROM orcamentos WHERE id_orcamento='$orcamento_id'";
              $query = mysqli_query($conexao, $sql);
              if (mysqli_num_rows($query) > 0) {
                $orcamento = mysqli_fetch_array($query);
            ?>
                <form id="formOrcamento" action="/planel/orcamento/atualizar" method="POST" enctype="multipart/form-data">
                  <input type="hidden" name="orcamento_id" required value="<?= $orcamento['id_orcamento'] ?>">
                  <input type="hidden" id="materiais_para_remover" name="materiais_para_remover" value="">
                  <input type="hidden" id="servicos_para_remover" name="servicos_para_remover" value="">
                  <div class="row">
                    <div class="col-md-4">
                      <div class="mb-3">
                        <label for="nome_orcamento">Nome do Orçamento</label>
                        <input type="text" id="nome_orcamento" name="nome_orcamento" class="form-control" required value="<?= $orcamento['nome_orcamento'] ?>">
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
                            $selected = ($row_cliente['id_cliente'] == $orcamento['fk_clientes_id_cliente']) ? 'selected' : '';
                            echo "<option value='".$row_cliente['id_cliente']."' $selected>".$row_cliente['nome_cliente']."</option>";
                          }
                          ?>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="mb-3">
                        <label for="data_orcamento">Data do Orçamento</label>
                        <input type="date" id="data_orcamento" name="data_orcamento" class="form-control" required value="<?= $orcamento['data_orcamento'] ?>">
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="mb-3">
                        <label for="validade">Validade</label>
                        <input type="date" id="validade" name="validade" class="form-control" required value="<?= $orcamento['validade'] ?>">
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="mb-3">
                        <label for="status">Status</label>
                        <input type="text" id="status" name="status" class="form-control" required value="<?= $orcamento['status'] ?>">
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="mb-3">
                        <label for="arquivo_pdf">Arquivo PDF</label>
                        <input type="file" id="arquivo_pdf" name="arquivo_pdf" class="form-control">
                        <?php if (!empty($orcamento['caminho_arquivo'])): ?>
                          <small class="form-text text-muted">
                            Arquivo atual: <a href="<?= '/planel/upload?file=' . urlencode(basename($orcamento['caminho_arquivo'])); ?>" target="_blank"><?= basename($orcamento['caminho_arquivo']); ?></a>
                          </small>
                        <?php endif; ?>
                      </div>
                    </div>
                  </div>
                  <div class="mb-3">
                    <label for="observacao">Observação</label>
                    <textarea id="observacao" name="observacao" class="form-control"><?= $orcamento['observacao'] ?></textarea>
                  </div>

                  <!-- Materiais -->
                  <div class="container mt-4">
                    <div class="row">
                        <div class="col-md-12">
                        <h5>Materiais</h5>
                        <div id="materiais-container">
                            <?php
                            $sql_materiais = "SELECT om.*, m.nome_material 
                                            FROM orcamento_material om
                                            LEFT JOIN materiais m ON om.fk_materiais_id_material = m.id_material
                                            WHERE om.fk_orcamentos_id_orcamento = {$orcamento_id}";
                            $query_materiais = mysqli_query($conexao, $sql_materiais);

                            if (mysqli_num_rows($query_materiais) > 0) {
                            while ($material = mysqli_fetch_assoc($query_materiais)) {
                            ?>
                                <div class="row mb-3" id="material-item-<?= $material['id_orcamento_material'] ?>">
                                  <input type="hidden" name="id_orcamento_material[]" value="<?= $material['id_orcamento_material'] ?>" />
                                  <div class="col-md-4">
                                      <label for="nome_material">Nome do Material</label>
                                      <select name="nome_material[]" class="form-control" onchange="buscarPreco(this.value, <?= $material['id_orcamento_material'] ?>)" required>
                                      <option value="">Selecione um Material</option>
                                      <?php
                                      $query_materiais_lista = "SELECT * FROM materiais WHERE ativo = TRUE";
                                      $result_materiais_lista = mysqli_query($conexao, $query_materiais_lista);
                                      while ($row_material = mysqli_fetch_assoc($result_materiais_lista)) {
                                          $selected = ($row_material['id_material'] == $material['fk_materiais_id_material']) ? 'selected' : '';
                                          echo "<option value='".$row_material['id_material']."' $selected>".$row_material['nome_material']."</option>";
                                      }
                                      ?>
                                      </select>
                                  </div>
                                  <div class="col-md-2">
                                      <label for="quantidade">Quantidade</label>
                                      <input type="number" name="quantidade[]" class="form-control" value="<?= $material['quantidade_material'] ?>" onchange="atualizarTotal(<?= $material['id_orcamento_material'] ?>)" required>
                                  </div>
                                  <div class="col-md-2">
                                      <label for="preco">Preço Unitário</label>
                                      <input type="number" name="preco[]" id="preco-material-<?= $material['id_orcamento_material'] ?>" class="form-control" value="<?= $material['valor_unitario'] ?>" step="0.01" onchange="atualizarTotal(<?= $material['id_orcamento_material'] ?>)" required>
                                  </div>
                                  <div class="col-md-3">
                                      <label for="total">Valor Total</label>
                                      <input type="text" name="total[]" id="total-material-<?= $material['id_orcamento_material'] ?>" class="form-control" value="<?= number_format($material['quantidade_material'] * $material['valor_unitario'], 2, ',', '.') ?>" readonly>
                                  </div>
                                  <div class="col-md-1 d-flex align-items-end">
                                    <button type="button" class="btn btn-success me-2" onclick="adicionarMaterial()">
                                      <span class="bi-arrow-down"></span>
                                    </button>
                                    <button type="button" class="btn btn-danger" onclick="confirmarRemoverMaterial('material-item-<?= $material['id_orcamento_material'] ?>')">
                                      <span class="bi-trash3-fill"></span>
                                    </button>
                                  </div>
                                </div>
                            <?php
                            }
                            }
                            ?>
                        </div>
                        </div>
                    </div>
                  </div>

                  <!-- Serviços -->
                  <div class="container mt-4">
                    <div class="row">
                        <div class="col-md-12">
                        <h5>Serviços</h5>
                        <div id="servicos-container">
                            <?php
                            $sql_servicos = "SELECT os.*, s.nome_servico 
                                            FROM orcamento_servico os
                                            LEFT JOIN servicos s ON os.fk_servicos_id_servico = s.id_servico
                                            WHERE os.fk_orcamentos_id_orcamento = {$orcamento_id}";
                            $query_servicos = mysqli_query($conexao, $sql_servicos);

                            if (mysqli_num_rows($query_servicos) > 0) {
                            while ($servico = mysqli_fetch_assoc($query_servicos)) {
                            ?>
                                <div class="row mb-3" id="servico-item-<?= $servico['id_orcamento_servico'] ?>">
                                  <input type="hidden" name="id_orcamento_servico[]" value="<?= $servico['id_orcamento_servico'] ?>" />
                                  <div class="col-md-4">
                                      <label for="nome_servico">Nome do Serviço</label>
                                      <select name="nome_servico[]" class="form-control" onchange="buscarPrecoServico(this.value, <?= $servico['id_orcamento_servico'] ?>)" required>
                                      <option value="">Selecione um Serviço</option>
                                      <?php
                                      $query_servicos_lista = "SELECT * FROM servicos WHERE ativo = TRUE";
                                      $result_servicos_lista = mysqli_query($conexao, $query_servicos_lista);
                                      while ($row_servico = mysqli_fetch_assoc($result_servicos_lista)) {
                                          $selected = ($row_servico['id_servico'] == $servico['fk_servicos_id_servico']) ? 'selected' : '';
                                          echo "<option value='".$row_servico['id_servico']."' $selected>".$row_servico['nome_servico']."</option>";
                                      }
                                      ?>
                                      </select>
                                  </div>
                                  <div class="col-md-2">
                                      <label for="quantidade">Quantidade</label>
                                      <input type="number" name="quantidade_servico[]" class="form-control" value="<?= $servico['quantidade_servico'] ?>" onchange="atualizarTotalServico(<?= $servico['id_orcamento_servico'] ?>)" required>
                                  </div>
                                  <div class="col-md-2">
                                      <label for="preco">Preço Unitário</label>
                                      <input type="number" name="preco_servico[]" class="form-control" value="<?= $servico['valor_unitario'] ?>" step="0.01" onchange="atualizarTotalServico(<?= $servico['id_orcamento_servico'] ?>)" required>
                                  </div>
                                  <div class="col-md-3">
                                      <label for="total">Valor Total</label>
                                      <input type="text" name="total_servico[]" id="total-servico-<?= $servico['id_orcamento_servico'] ?>" class="form-control" value="<?= number_format($servico['quantidade_servico'] * $servico['valor_unitario'], 2, ',', '.') ?>" readonly>
                                  </div>
                                  <div class="col-md-1 d-flex align-items-end">
                                      <button type="button" class="btn btn-success me-2" onclick="adicionarServico()">
                                        <span class="bi-arrow-down"></span>
                                      </button>
                                      <button type="button" class="btn btn-danger" onclick="confirmarRemoverServico('servico-item-<?= $servico['id_orcamento_servico'] ?>')">
                                        <span class="bi-trash3-fill"></span>
                                      </button>
                                  </div>
                                </div>
                            <?php
                            }
                            }
                            ?>
                        </div>
                        </div>
                    </div>
                  </div>

                  <div class="mb-3 mt-4">
                    <button type="submit" name="editar_orcamento" class="btn btn-primary">
                      Salvar<span class="bi-save ms-2"></span>
                    </button>
                  </div>
                </form>
            <?php
              } else {
                echo "<h5>Orçamento não encontrado</h5>";
              }
            }
            ?>
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
    let contadorMateriais = <?= mysqli_num_rows($query_materiais) + 1 ?>;
    let contadorServicos = <?= mysqli_num_rows($query_servicos) + 1 ?>;
    let materiaisParaRemover = [];
    let servicosParaRemover = [];

    // Função para atualizar o campo oculto antes do envio do formulário
    document.getElementById('formOrcamento').addEventListener('submit', function () {
        document.getElementById('materiais_para_remover').value = JSON.stringify(materiaisParaRemover);
        document.getElementById('servicos_para_remover').value = JSON.stringify(servicosParaRemover);
    });

    function atualizarTotal(id) {
        const quantidadeElement = document.querySelector(`#material-item-${id} input[name="quantidade[]"]`);
        const precoElement = document.querySelector(`#material-item-${id} input[name="preco[]"]`);
        const totalElement = document.getElementById(`total-material-${id}`);

        if (quantidadeElement && precoElement && totalElement) {
            let quantidade = parseFloat(quantidadeElement.value) || 0;
            let preco = parseFloat(precoElement.value) || 0;

            if (!isNaN(quantidade) && !isNaN(preco)) {
                quantidadeElement.value = quantidade.toFixed(0);
                totalElement.value = (quantidade * preco).toFixed(2);
            } else {
                totalElement.value = '0.00';
            }
        }
    }

    function atualizarTotalServico(id) {
        const quantidadeElement = document.querySelector(`#servico-item-${id} input[name="quantidade_servico[]"]`);
        const precoElement = document.querySelector(`#servico-item-${id} input[name="preco_servico[]"]`);
        const totalElement = document.querySelector(`#servico-item-${id} input[name="total_servico[]"]`);

        if (quantidadeElement && precoElement && totalElement) {
            let quantidade = parseFloat(quantidadeElement.value) || 0;
            let preco = parseFloat(precoElement.value) || 0;

            if (!isNaN(quantidade) && !isNaN(preco)) {
                quantidadeElement.value = quantidade.toFixed(0);
                totalElement.value = (quantidade * preco).toFixed(2);
            } else {
                totalElement.value = '0.00';
            }
        }
    }

    function adicionarMaterial() {
        const idNovoMaterial = 'novo-' + contadorMateriais;
        contadorMateriais++;
        const novoMaterial = `
            <div class="row mb-3" id="material-item-${idNovoMaterial}">
                <div class="col-md-4">
                    <label for="nome_material">Nome do Material</label>
                    <select name="nome_material[]" class="form-control" onchange="buscarPreco(this.value, '${idNovoMaterial}')" required>
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
                    <input type="number" name="quantidade[]" class="form-control" min="1" step="1" onchange="atualizarTotal('${idNovoMaterial}')" required>
                </div>
                <div class="col-md-2">
                    <label for="preco">Preço Unitário</label>
                    <input type="number" name="preco[]" id="preco-material-${idNovoMaterial}" class="form-control" step="0.01" onchange="atualizarTotal('${idNovoMaterial}')" required>
                </div>
                <div class="col-md-3">
                    <label for="total">Valor Total</label>
                    <input type="number" name="total[]" id="total-material-${idNovoMaterial}" class="form-control" readonly>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="button" class="btn btn-success me-2" onclick="adicionarMaterial()">
                        <span class="bi-arrow-down"></span>
                    </button>
                    <button type="button" class="btn btn-danger" onclick="removerItem('material-item-${idNovoMaterial}')">
                        <span class="bi-trash3-fill"></span>
                    </button>
                </div>
            </div>`;
        document.getElementById('materiais-container').insertAdjacentHTML('beforeend', novoMaterial);
    }

    function adicionarServico() {
        const idNovoServico = 'novo-' + contadorServicos;
        contadorServicos++;
        const novoServico = `
            <div class="row mb-3" id="servico-item-${idNovoServico}">
                <div class="col-md-4">
                    <label for="nome_servico">Nome do Serviço</label>
                    <select name="nome_servico[]" class="form-control" onchange="buscarPrecoServico(this.value, '${idNovoServico}')" required>
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
                    <input type="number" name="quantidade_servico[]" class="form-control" min="1" step="1" onchange="atualizarTotalServico('${idNovoServico}')" required>
                </div>
                <div class="col-md-2">
                    <label for="preco">Preço Unitário</label>
                    <input type="number" name="preco_servico[]" id="preco-servico-${idNovoServico}" class="form-control" step="0.01" onchange="atualizarTotalServico('${idNovoServico}')" required>
                </div>
                <div class="col-md-3">
                    <label for="total">Valor Total</label>
                    <input type="number" name="total_servico[]" id="total-servico-${idNovoServico}" class="form-control" readonly>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="button" class="btn btn-success me-2" onclick="adicionarServico()">
                        <span class="bi-arrow-down"></span>
                    </button>
                    <button type="button" class="btn btn-danger" onclick="removerItem('servico-item-${idNovoServico}')">
                        <span class="bi-trash3-fill"></span>
                    </button>
                </div>
            </div>`;
        document.getElementById('servicos-container').insertAdjacentHTML('beforeend', novoServico);
    }

    function removerItem(id) {
        const elemento = document.getElementById(id);
        if (elemento) {
            elemento.remove();
        }
    }

    function confirmarRemoverMaterial(id) {
        const materialId = id.replace("material-item-", "");
        const inputHiddenId = document.querySelector(`#material-item-${materialId} input[name="id_orcamento_material[]"]`);

        if (inputHiddenId && inputHiddenId.value) {
            // Material já existente com ID atribuído
            if (confirm('Tem certeza que deseja excluir o material?')) {
                materiaisParaRemover.push(inputHiddenId.value);
                document.getElementById("materiais_para_remover").value = JSON.stringify(materiaisParaRemover);
                document.getElementById(id).style.display = 'none';
            }
        } else {
            removerItem(id);
        }
    }

    function confirmarRemoverServico(id) {
        const servicoId = id.replace("servico-item-", "");
        const inputHiddenId = document.querySelector(`#servico-item-${servicoId} input[name="id_orcamento_servico[]"]`);

        if (inputHiddenId && inputHiddenId.value) {
            // Serviço já existente com ID atribuído
            if (confirm('Tem certeza que deseja excluir o serviço?')) {
                servicosParaRemover.push(inputHiddenId.value);
                document.getElementById("servicos_para_remover").value = JSON.stringify(servicosParaRemover);
                document.getElementById(id).style.display = 'none';
            }
        } else {
            removerItem(id);
        }
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
