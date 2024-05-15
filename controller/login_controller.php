<?php
session_start();
include('../model/conexao.php');

if(empty($_POST['login']) || empty($_POST['senha'])) {
    header('Location: ../view/index.php');
    exit();
}

$login = mysqli_real_escape_string($conexao, $_POST['login']);
$senha = mysqli_real_escape_string($conexao, $_POST['senha']);

$query = "SELECT * FROM usuarios WHERE login = '{$login}' AND senha = md5('{$senha}')";

$result = mysqli_query($conexao, $query);

$row = mysqli_num_rows($result);

if($row == 1) {
    $_SESSION['login'] = $login;
    header('Location: ../view/dashboard.php');
    exit();
} else {
    $_SESSION['nao_autenticado'] = true;
    header('Location: ../view/index.php');
    exit();
}
?>