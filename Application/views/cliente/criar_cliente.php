<?php
session_start();
require('Application/models/conexao.php');
?>
<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Adicionar Cliente</title>
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
  <?php include(__DIR__ . '/../navbar.php');?>
  <div class="container mt-5">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h4>Adicionar Cliente
              <a href="/planel/clientes" class="btn btn-danger float-end">
              <span class="bi-arrow-left"></span>&nbsp;Voltar</a>
            </h4>
          </div>
          <div class="card-body">
            <form id="formcliente" action="/planel/cliente/atualizar" method="POST">
              <div class="mb-3">
                <label for="nome">Nome</label>
                <input type="text" id="nome" name="nome" class="form-control" required>
                <div id="nomeError" class="invalid-feedback">Nome inválido. Use apenas letras e espaços.</div>
              </div>
              <div class="mb-3">
                <label for="cnpj">CNPJ</label>
                <input type="text" id="cnpj" name="cnpj" class="form-control cnpj-mask" required>
                <div id="cnpjError" class="invalid-feedback">CNPJ inválido</div>
              </div>
              <div class="mb-3">
                <label for="telefone">Telefone</label>
                <input type="tel" id="telefone" name="telefone" class="form-control phone-mask">
              </div>
              <div class="mb-3">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" class="form-control" required>
                <div id="emailError" class="invalid-feedback">E-mail inválido</div>
              </div>
              <div class="mb-3">
                <label for="endereco">Endereço</label>
                <input type="text" id="endereco" name="endereco" class="form-control">
              </div>
              <div class="mb-3">
                <label for="estado">Estado</label>
                <select id="estado" name="estado" class="form-control" required>
                  <option value="">Selecione um Estado</option>
                  <?php
                  $query_estados = "SELECT * FROM estados";
                  $result_estados = mysqli_query($conexao, $query_estados);
                  while($row_estado = mysqli_fetch_assoc($result_estados)) {
                      echo "<option value='".$row_estado['id_estado']."'>". $row_estado['nome_estado']."</option>";
                  }
                  ?>
                </select>
                <div id="estadoError" class="invalid-feedback">Selecione um estado.</div>
              </div>
              <div id="cidade-container" class="mb-3 hidden">
                <label for="cidade">Cidade</label>
                <select id="cidade" name="cidade" class="form-control" required disabled>
                  <option value="">Selecione um Estado</option>
                </select>
                <div id="cidadeError" class="invalid-feedback">Selecione uma cidade.</div>
              </div>
              <div class="mb-3">
                <button type="submit" name="criar_cliente" class="btn btn-primary">
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
    function validarCNPJ(cnpj) {
      cnpj = cnpj.replace(/[^\d]+/g, '');

      if (cnpj.length !== 14)
        return false;

      // Elimina CNPJs inválidos conhecidos
      if (cnpj === "00000000000000" || 
          cnpj === "11111111111111" || 
          cnpj === "22222222222222" || 
          cnpj === "33333333333333" || 
          cnpj === "44444444444444" || 
          cnpj === "55555555555555" || 
          cnpj === "66666666666666" || 
          cnpj === "77777777777777" || 
          cnpj === "88888888888888" || 
          cnpj === "99999999999999")
        return false;

      // Valida DVs
      let tamanho = cnpj.length - 2;
      let numeros = cnpj.substring(0,tamanho);
      let digitos = cnpj.substring(tamanho);
      let soma = 0;
      let pos = tamanho - 7;
      for (let i = tamanho; i >= 1; i--) {
        soma += numeros.charAt(tamanho - i) * pos--;
        if (pos < 2)
              pos = 9;
      }
      let resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
      if (resultado != digitos.charAt(0))
        return false;

      tamanho = tamanho + 1;
      numeros = cnpj.substring(0,tamanho);
      soma = 0;
      pos = tamanho - 7;
      for (let i = tamanho; i >= 1; i--) {
        soma += numeros.charAt(tamanho - i) * pos--;
        if (pos < 2)
              pos = 9;
      }
      resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
      if (resultado != digitos.charAt(1))
            return false;
      
      return true;
    }

    function validarNome(nome) {
      var regex = /^[A-Za-zÀ-ÿ\s]+$/;
      return regex.test(nome);
    }

    function validarEmail(email) {
      var regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      return regex.test(email);
    }

    $(document).ready(function() {
        // Máscara de telefone/celular
        var behavior = function (val) {
            return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
        },
        options = {
            onKeyPress: function(val, e, field, options) {
                field.mask(behavior.apply({}, arguments), options);
            }
        };
        $('.phone-mask').mask(behavior, options);

        // Máscara de CNPJ
        $('.cnpj-mask').mask('00.000.000/0000-00');
        
        $('#estado').change(function() {
            var estadoId = $(this).val();
            if (estadoId) {
                $('#cidade-container').removeClass('hidden');
                $('#cidade').prop('disabled', false).prop('required', true);
                $.ajax({
                    url: '/planel/cidades',
                    type: 'POST',
                    data: {estado_id: estadoId},
                    success: function(data) {
                        $('#cidade').html(data);
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        alert('Erro ao carregar cidades: ' + textStatus + ' - ' + errorThrown);
                        console.log(jqXHR.responseText);
                    }
                });
            } else {
                $('#cidade-container').addClass('hidden');
                $('#cidade').prop('disabled', true).prop('required', false);
                $('#cidade').html('<option value="">Selecione um Estado</option>');
            }
        });

        $('#formcliente').submit(function(event) {
            var cnpj = $('#cnpj').val();
            var nome = $('#nome').val();
            var email = $('#email').val();
            var estado = $('#estado').val();
            var cidade = $('#cidade').val();
            var valid = true;

            if (!validarCNPJ(cnpj)) {
                $('#cnpj').addClass('is-invalid');
                valid = false;
            } else {
                $('#cnpj').removeClass('is-invalid');
            }

            if (!validarNome(nome)) {
                $('#nome').addClass('is-invalid');
                valid = false;
            } else {
                $('#nome').removeClass('is-invalid');
            }

            if (!validarEmail(email)) {
                $('#email').addClass('is-invalid');
                valid = false;
            } else {
                $('#email').removeClass('is-invalid');
            }

            if (estado === "") {
                $('#estado').addClass('is-invalid');
                valid = false;
            } else {
                $('#estado').removeClass('is-invalid');
            }

            if (cidade === "") {
                $('#cidade').addClass('is-invalid');
                valid = false;
            } else {
                $('#cidade').removeClass('is-invalid');
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
