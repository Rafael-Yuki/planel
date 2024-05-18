<?php
    if (isset($_SESSION['mensagem'])):
        $tipo_mensagem = isset($_SESSION['mensagem_tipo']) ? $_SESSION['mensagem_tipo'] : 'info';

        // Definindo classes CSS com base no tipo de mensagem
        $classes_css = [
            'success' => 'alert-success',  // Mensagem de sucesso (verde)
            'error' => 'alert-danger',     // Mensagem de erro (vermelho)
            'warning' => 'alert-warning',  // Mensagem de aviso (amarelo)
            'info' => 'alert-info'         // Mensagem informativa (azul)
        ];

        $classe_css = isset($classes_css[$tipo_mensagem]) ? $classes_css[$tipo_mensagem] : $classes_css['info'];
?>
 
<div class="alert <?= $classe_css ?> alert-dismissible fade show" role="alert">
    <?= $_SESSION['mensagem']; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
 
<?php
        unset($_SESSION['mensagem']);
        unset($_SESSION['mensagem_tipo']);
    endif;
?>
