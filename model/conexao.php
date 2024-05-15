<?php
define('HOST', '127.0.0.1');
define('USER', 'root');
define('PASS', '');
define('DB', 'estagio');

$conexao = mysqli_connect(HOST, USER, PASS, DB) or die ('Não foi possível conectar');
