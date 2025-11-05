<?php
ReservasAuth::require_login();
$current_user = ReservasAuth::get_current_user();

get_header();
?>

<div class="reservas-dashboard">
    <div class="dashboard-header">
        <h1>Panel de Control - Sistema de Reservas</h1>
        <div class="user-info">
            <span>Bienvenido, <?php echo esc_html($current_user['username']); ?></span>
            <span class="user-role">(<?php echo esc_html($current_user['role']); ?>)</span>
            <a href="#" id="logout-btn" class="btn-logout">Cerrar Sesión</a>
        </div>
    </div>
    
    <div class="dashboard-content">
        <div class="dashboard-sidebar">
            <nav class="dashboard-nav">
                <ul>
                    <li><a href="#dashboard" class="nav-link active">Dashboard</a></li>
                    
                    <?php if (ReservasAuth::has_permission('admin')): ?>
                        <li><a href="#calendario" class="nav-link">Gestión de Calendario</a></li>
                        <li><a href="#reservas" class="nav-link">Reservas</a></li>
                        <li><a href="#informes" class="nav-link">Informes</a></li>
                    <?php endif; ?>
                    
                    <?php if (ReservasAuth::has_permission('super_admin')): ?>
                        <li><a href="#usuarios" class="nav-link">Gestión de Usuarios</a></li>
                        <li><a href="#agencias" class="nav-link">Gestión de Agencias</a></li>
                        <li><a href="#configuracion" class="nav-link">Configuración</a></li>
                    <?php endif; ?>
                    
                    <?php if ($current_user['role'] == 'agencia'): ?>
                        <li><a href="#reservas-agencia" class="nav-link">Mis Reservas</a></li>
                        <li><a href="#nueva-reserva" class="nav-link">Nueva Reserva</a></li>
                    <?php endif; ?>
                    
                    <?php if ($current_user['role'] == 'conductor'): ?>
                        <li><a href="#mis-servicios" class="nav-link">Mis Servicios</a></li>
                        <li><a href="#verificar-billetes" class="nav-link">Verificar Billetes</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
        
        <div class="dashboard-main">
            <!-- Contenido dinámico se cargará aquí -->
            <div id="dashboard-content">
                <?php include 'dashboard-home.php'; ?>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Navegación del dashboard
    $('.nav-link').on('click', function(e) {
        e.preventDefault();
        
        $('.nav-link').removeClass('active');
        $(this).addClass('active');
        
        var section = $(this).attr('href').substring(1);
        loadDashboardSection(section);
    });
    
    // Logout
    $('#logout-btn').on('click', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: reservas_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'reservas_logout',
                nonce: reservas_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    window.location.href = response.data.redirect;
                }
            }
        });
    });
    
    function loadDashboardSection(section) {
        $('#dashboard-content').html('<div class="loading">Cargando...</div>');
        
        $.ajax({
            url: reservas_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'load_dashboard_section',
                section: section,
                nonce: reservas_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    $('#dashboard-content').html(response.data);
                } else {
                    $('#dashboard-content').html('<div class="error">Error cargando la sección</div>');
                }
            }
        });
    }
});
</script>

<?php get_footer(); ?>