<?php
spl_autoload_register(function ($class) {
    // Converta namespaces em caminhos de arquivo
    $file = str_replace('\\', '/', $class) . '.php';
    // Verifique se o arquivo existe
    if (file_exists($file)) {
        require_once $file;
    }
});