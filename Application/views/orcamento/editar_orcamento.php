<?php
session_start();
require('Application/models/conexao.php');
require('Application/models/orcamento_dao.php');
require('Application/models/itens_orcamento_dao.php');
require('Application/models/material_dao.php');
require('Application/models/servico_dao.php');

if (isset($_GET['id'])) {
    $orcamento_id = mysqli_real_escape_string($conexao, $_GET['id']);
    $orcamento = OrcamentoDAO::buscarOrcamentoPorId($orcamento_id);
    $itens = ItensOrcamentoDAO::listarItensPorOrcamento($orcamento_id);
}
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
                        <h4>Editar Orçamento
                            <a href="/planel/orcamentos" class="btn btn-danger float-end">
                            <span class="bi-arrow-left"></span>&nbsp;Voltar</a>
                        </h4>
                    </div>
                    <div class="card-body">
                        <form id="formOrcamento" action="/planel/orcamento/atualizar" method="POST" enctype="multipart/form-data" onsubmit="return verificarEnvio(event)">
                            <input type="hidden" name="id_orcamento" value="<?= $orcamento_id ?>">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="nome_orcamento">Nome do Orçamento</label>
                                        <input type="text" id="nome_orcamento" name="nome_orcamento" class="form-control" value="<?= $orcamento['nome_orcamento'] ?>" required>
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
                                                $selected = $row_cliente['id_cliente'] == $orcamento['fk_clientes_id_cliente'] ? 'selected' : '';
                                                echo "<option value='".$row_cliente['id_cliente']."' $selected>".$row_cliente['nome_cliente']."</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="data_orcamento">Data do Orçamento</label>
                                        <input type="date" id="data_orcamento" name="data_orcamento" class="form-control" value="<?= $orcamento['data_orcamento'] ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="validade">Validade</label>
                                        <input type="date" id="validade" name="validade" class="form-control" value="<?= $orcamento['validade'] ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="status">Status</label>
                                        <input type="text" id="status" name="status" class="form-control" value="<?= $orcamento['status'] ?>" required>
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
                                <textarea id="observacao" name="observacao" class="form-control"><?= $orcamento['observacao'] ?></textarea>
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

                            <!-- Valor Total do Orçamento -->
                            <div class="row mt-4">
                                <div class="col-md-4 ms-auto">
                                    <label for="valor_total_orcamento">Valor Total do Orçamento</label>
                                    <input type="number" id="valor_total_orcamento" name="valor_total_orcamento" class="form-control" step="0.01" min="0" value="<?= $orcamento['valor_total_orcamento'] ?>" oninput="atualizarValorTotalOrcamento()">
                                </div>
                            </div>

                            <div class="mb-3 mt-4">
                                <button type="submit" name="editar_orcamento" class="btn btn-primary">
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

        function adicionarItem(idItem = '', nomeItem = '', descricaoItem = '', valorTotal = '') {
            // Contar o número atual de itens na interface
            const numeroDeItens = document.querySelectorAll('#itens-container > .container').length;
            const novoIdItem = idItem || `item-${numeroDeItens + 1}`;

            // Reiniciar contadores de materiais e serviços para o novo item
            contadorMateriais = 1;
            contadorServicos = 1;

            const novoItem = `
                <div class="container mt-4" id="${novoIdItem}">
                    <div class="input-group">
                        <span class="input-group-text item-number">${numeroDeItens + 1}º</span>
                        <input type="text" name="nome_item[]" class="form-control input-group-item-name" placeholder="Nome do Item" value="${nomeItem}" required>
                        <div class="btn-group-actions ms-2">
                            <button type="button" class="btn btn-success" onclick="adicionarItem()">
                                <span class="bi-plus"></span>
                            </button>
                            <button type="button" class="btn btn-danger" onclick="removerElemento('${novoIdItem}')">
                                <span class="bi-trash3-fill"></span>
                            </button>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <label for="descricao_item">Descrição do Item</label>
                            <textarea name="descricao_item[]" class="form-control" placeholder="Descrição do Item" required>${descricaoItem}</textarea>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12 d-flex justify-content-between align-items-center">
                            <h4>Materiais</h4>
                            <button type="button" class="btn btn-success" onclick="adicionarMaterialAoItem('${novoIdItem}')">
                                <span class="bi-plus"></span>
                            </button>
                        </div>
                        <div id="materiais-${novoIdItem}" class="mt-2"></div>
                    </div>
                    <hr>
                    <div class="row mt-3">
                        <div class="col-md-12 d-flex justify-content-between align-items-center">
                            <h4>Serviços</h4>
                            <button type="button" class="btn btn-success" onclick="adicionarServicoAoItem('${novoIdItem}')">
                                <span class="bi-plus"></span>
                            </button>
                        </div>
                        <div id="servicos-${novoIdItem}" class="mt-2"></div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-4 ms-auto">
                            <label for="valor_total_item">Valor Total do Item</label>
                            <input type="number" name="valor_total_item[]" id="valor-total-item-${novoIdItem}" class="form-control" step="0.01" min="0" value="${valorTotal}" oninput="atualizarValorManual('${novoIdItem}')">
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

        function adicionarMaterialAoItem(idItem, idMaterial = '', quantidade = '', preco = '') {
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
                        <input type="number" name="quantidade-${idItem}[]" class="form-control" min="1" step="1" onchange="atualizarTotal('${idNovoMaterial}', '${idItem}')" value="${quantidade}" required>
                    </div>
                    <div class="col-md-2">
                        <label for="preco">Preço Unitário</label>
                        <input type="number" name="preco-${idItem}[]" id="preco-${idNovoMaterial}" class="form-control" step="0.01" onchange="atualizarTotal('${idNovoMaterial}', '${idItem}')" value="${preco}" required>
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

        function adicionarServicoAoItem(idItem, idServico = '', quantidade = '', preco = '') {
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
                        <input type="number" name="quantidade_servico-${idItem}[]" class="form-control" min="1" step="1" onchange="atualizarTotalServico('${idNovoServico}', '${idItem}')" value="${quantidade}" required>
                    </div>
                    <div class="col-md-2">
                        <label for="preco">Preço Unitário</label>
                        <input type="number" name="preco_servico-${idItem}[]" id="preco-${idNovoServico}" class="form-control" step="0.01" onchange="atualizarTotalServico('${idNovoServico}', '${idItem}')" value="${preco}" required>
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

            document.querySelectorAll('#itens-container > .container').forEach((item, index) => {
                const idItem = item.getAttribute('id');
                const nomeItem = item.querySelector('.input-group-item-name').value;
                const descricaoItem = item.querySelector('textarea[name="descricao_item[]"]').value;
                const valorTotalItem = item.querySelector(`#valor-total-item-${idItem}`).value;

                const materiaisDoItem = [];
                document.querySelectorAll(`#materiais-${idItem} select[name^="materiais-"]`).forEach((materialSelect, i) => {
                    const materialId = materialSelect.value;
                    const quantidade = materialSelect.closest('.row').querySelector(`input[name^="quantidade-"]`).value;
                    const preco = materialSelect.closest('.row').querySelector(`input[name^="preco-"]`).value;

                    if (materialId && quantidade && preco) {
                        materiaisDoItem.push({ materialId, quantidade, preco });
                    }
                });

                const servicosDoItem = [];
                document.querySelectorAll(`#servicos-${idItem} select[name^="servicos-"]`).forEach((servicoSelect, j) => {
                    const servicoId = servicoSelect.value;
                    const quantidade = servicoSelect.closest('.row').querySelector(`input[name^="quantidade_servico-"]`).value;
                    const preco = servicoSelect.closest('.row').querySelector(`input[name^="preco_servico-"]`).value;

                    if (servicoId && quantidade && preco) {
                        servicosDoItem.push({ servicoId, quantidade, preco });
                    }
                });

                materiais.push({ idItem, materiaisDoItem });
                servicos.push({ idItem, servicosDoItem });
            });

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

            console.log('Materiais Capturados:', JSON.stringify(materiais));
            console.log('Serviços Capturados:', JSON.stringify(servicos));

            return true;
        }

        function removerElemento(id) {
            document.getElementById(id).remove();
            recontarItens();
            atualizarValorTotalOrcamento();
        }

        function removerTodosItens() {
            document.getElementById('itens-container').innerHTML = '';
            contadorItens = 1;
            atualizarValorTotalOrcamento();
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
            atualizarValorTotalOrcamento();
        }

        function atualizarValorManual(idItem) {
            const valorTotalInput = document.getElementById(`valor-total-item-${idItem}`);
            valorTotalInput.setAttribute('data-editado-manualmente', 'true');
        }

        function atualizarValorTotalOrcamento() {
            let valorTotalOrcamento = 0;
            const totaisItens = document.querySelectorAll('[id^="valor-total-item-"]');
            totaisItens.forEach(item => {
                valorTotalOrcamento += parseFloat(item.value) || 0;
            });

            const valorTotalOrcamentoInput = document.getElementById('valor_total_orcamento');
            if (valorTotalOrcamentoInput && valorTotalOrcamentoInput.getAttribute('data-editado-manualmente') !== 'true') {
                valorTotalOrcamentoInput.value = valorTotalOrcamento.toFixed(2);
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
          carregarDadosDoOrcamento(<?= json_encode($orcamento); ?>, <?= json_encode($itens); ?>);
        });

        function carregarDadosDoOrcamento(orcamento, itens) {
          // Preencher os campos do orçamento com os dados
          document.getElementById("nome_orcamento").value = orcamento.nome_orcamento;
          document.getElementById("cliente").value = orcamento.fk_clientes_id_cliente;
          document.getElementById("data_orcamento").value = orcamento.data_orcamento;
          document.getElementById("validade").value = orcamento.validade;
          document.getElementById("status").value = orcamento.status;
          document.getElementById("observacao").value = orcamento.observacao;
          document.getElementById("valor_total_orcamento").value = orcamento.valor_total_orcamento;

          // Ajustar o contador de itens para o número de itens existentes
          contadorItens = itens.length;

          // Iterar sobre os itens e carregá-los
          itens.forEach((item, index) => {
              const idItem = `item-${index + 1}`;
              adicionarItem(idItem, item.nome_item, item.descricao_item, item.valor_total_item);

              // Reinicializar os contadores de materiais e serviços para cada item
              let contadorMateriais = 1;
              let contadorServicos = 1;

              // Iterar sobre os materiais deste item e carregá-los
              if (Array.isArray(item.materiais)) {
                  item.materiais.forEach((material, materialIndex) => {
                      const idMaterial = `material-${idItem}-${contadorMateriais}`;
                      contadorMateriais++;
                      adicionarMaterialAoItem(idItem, material.material_id, material.quantidade, material.preco_unitario);

                      // Definir o valor dos campos de material
                      document.querySelector(`#${idMaterial} select[name^='materiais-']`).value = material.fk_materiais_id_material;
                      document.querySelector(`#${idMaterial} input[name^='quantidade-']`).value = material.quantidade;
                      document.getElementById(`preco-${idMaterial}`).value = material.preco_unitario;
                      document.getElementById(`total-${idMaterial}`).value = (material.quantidade * material.preco_unitario).toFixed(2);
                  });
              }

              // Iterar sobre os serviços deste item e carregá-los
              if (Array.isArray(item.servicos)) {
                  item.servicos.forEach((servico, servicoIndex) => {
                      const idServico = `servico-${idItem}-${contadorServicos}`;
                      contadorServicos++;
                      adicionarServicoAoItem(idItem, servico.servico_id, servico.quantidade, servico.preco_unitario);

                      // Definir o valor dos campos de serviço
                      document.querySelector(`#${idServico} select[name^='servicos-']`).value = servico.fk_servicos_id_servico;
                      document.querySelector(`#${idServico} input[name^='quantidade_servico-']`).value = servico.quantidade;
                      document.getElementById(`preco-${idServico}`).value = servico.preco_unitario;
                      document.getElementById(`total-${idServico}`).value = (servico.quantidade * servico.preco_unitario).toFixed(2);
                  });
              }
          });
        }
    </script>
</body>
</html>
