<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-size: 150%;
        }

        input.form-control,
        button.btn {
            font-size: 1.5rem;
        }
    </style>
</head>

<body data-bs-theme="dark">
</div>
<section class="d-flex justify-content-center align-items-center vh-100">
    <div>
        <h1 class="text-center mb-4">PLANEL</h1>
        <div class="card">
            <div class="card-body">
                <?php
                session_start();
                if(isset($_SESSION['nao_autenticado'])):
                ?>
                <div class="alert alert-danger" role="alert">
                    Login / senha inválidos!
                </div>
                <?php
                endif;
                unset($_SESSION['nao_autenticado']);
                ?>
                <form action="login" method="POST">
                    <div class="mb-3">
                        <input name="login" class="form-control" placeholder="Login" autofocus="">
                    </div>

                    <div class="mb-3">
                        <input name="senha" class="form-control" type="password" placeholder="Senha">
                    </div>

                    <button type="submit" class="btn btn-primary btn-block w-100">Entrar</button>
                </form>
            </div>
        </div>
    </div>
</section>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
