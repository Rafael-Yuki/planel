$(document).ready(function() {
    $('#estado').change(function() {
        var estadoId = $(this).val();
        if (estadoId) {
            $.ajax({
                url: '/planel/fornecedor/cidades',
                type: 'POST',
                data: {estado_id: estadoId},
                success: function(data) {
                    $('#cidade').prop('disabled', false);
                    $('#cidade').html(data);
                    console.log(data);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    alert('Erro ao carregar cidades: ' + textStatus + ' - ' + errorThrown);
                    console.log(jqXHR.responseText);
                }
            });
        } else {
            $('#cidade').prop('disabled', true);
            $('#cidade').html('<option value="">Selecione um Estado</option>');
        }
    });

    // Trigger change event to load cities when the page loads
    var selectedEstado = $('#estado').val();
    if (selectedEstado) {
        $('#estado').trigger('change');
    }
});
