<?php
define('HOST', '127.0.0.1');
define('USER', 'root');
define('PASS', '1234');
define('DB', 'planel');

$conexao = mysqli_connect(HOST, USER, PASS, DB) or die ('Não foi possível conectar');
