<?php
// Verificar si ya está logueado
if (ReservasAuth::is_logged_in()) {
    wp_redirect(home_url('/reservas-admin/'));
    exit;
}

get_header();
?>

<div class="reservas-login-container">
    <div class="reservas-login-form">
        <h2>Sistema de Reservas - Acceso</h2>
        
        <form id="reservas-login-form">
            <div class="form-group">
                <label for="username">Usuario:</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="btn-login">Iniciar Sesión</button>
            
            <div id="login-message" class="message"></div>
        </form>
        
        <div class="login-info">
            <p><strong>Usuario inicial:</strong> administrador</p>
            <p><strong>Contraseña inicial:</strong> busmedina</p>
            <p><em>Cambia estas credenciales después del primer acceso</em></p>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    $('#reservas-login-form').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: reservas_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'reservas_login',
                username: $('#username').val(),
                password: $('#password').val(),
                nonce: reservas_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    window.location.href = response.data.redirect;
                } else {
                    $('#login-message').html('<div class="error">' + response.data + '</div>');
                }
            },
            error: function() {
                $('#login-message').html('<div class="error">Error de conexión</div>');
            }
        });
    });
});
</script>

<?php get_footer(); ?>