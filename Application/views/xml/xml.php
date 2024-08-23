<?php
session_start();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Importar XML</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" 
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body data-bs-theme="dark">
    <?php include(__DIR__ . '/../navbar.php'); ?>
    <div class="container mt-4">
        <?php include(__DIR__ . '/../mensagem.php'); ?>
        <div class="card">
            <div class="card-header">
                <h4>Importar XML de Nota Fiscal</h4>
            </div>
            <div class="card-body">
                <!-- Atualização da ação do formulário -->
                <form action="xml/atualizar" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="xmlFile" class="form-label">Selecione o arquivo XML</label>
                        <input type="file" name="xmlFile" id="xmlFile" class="form-control" required>
                    </div>
                    <button type="submit" name="importar_xml" class="btn btn-primary">Importar</button>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" 
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
