// JavaScript para el sistema de reservas
jQuery(document).ready(function($) {
    // Funciones globales para el dashboard
    
    // Confirmar eliminación de usuarios
    window.deleteUser = function(userId) {
        if (confirm('¿Estás seguro de que quieres eliminar este usuario?')) {
            $.ajax({
                url: reservas_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'delete_user',
                    user_id: userId,
                    nonce: reservas_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert('Error al eliminar el usuario');
                    }
                }
            });
        }
    };
    
    // Función para editar usuario
    window.editUser = function(userId) {
        // Implementar modal de edición
        alert('Función de edición en desarrollo');
    };
});