<?php
require('Application/models/conexao.php');
mysqli_set_charset($conexao, "utf8");

if (isset($_POST['estado_id'])) {
    $estado_id = intval($_POST['estado_id']);
    $query = "SELECT id_cidade, nome_cidade FROM cidades WHERE id_estado = ?";
    
    if ($stmt = $conexao->prepare($query)) {
        $stmt->bind_param("i", $estado_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $cidades = $result->fetch_all(MYSQLI_ASSOC);
            foreach ($cidades as $cidade) {
                echo "<option value='" . $cidade['id_cidade'] . "'>" . utf8_decode($cidade['nome_cidade']) . "</option>";
            }
        } else {
            echo "<option value=''>Nenhuma cidade encontrada</option>";
        }

        $stmt->close();
    } else {
        echo "<option value=''>Erro na preparação da consulta: " . $conexao->error . "</option>";
    }
} else {
    echo "<option value=''>Estado não informado</option>";
}
?>
