<?php
class ReservasDashboard
{

    public function __construct()
    {
        // Inicializar hooks
    }

    public function handle_logout()
    {
        if (!session_id()) {
            session_start();
        }
        session_destroy();
        wp_redirect(home_url('/reservas-login/?logout=success'));
        exit;
    }

    public function show_login()
    {
        if ($_POST && isset($_POST['username']) && isset($_POST['password'])) {
            $this->process_login();
        }

        $this->render_login_page();
    }

    public function show_dashboard()
    {
        if (!session_id()) {
            session_start();
        }

        if (!isset($_SESSION['reservas_user'])) {
            wp_redirect(home_url('/reservas-login/?error=access'));
            exit;
        }

        $this->render_dashboard_page();
    }

    private function render_login_page()
    {
?>
        <!DOCTYPE html>
        <html <?php language_attributes(); ?>>

        <head>
            <meta charset="<?php bloginfo('charset'); ?>">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title>Sistema de Reservas - Login</title>
            <link rel="stylesheet" href="<?php echo RESERVAS_PLUGIN_URL; ?>assets/css/admin-style.css">
        </head>

        <body>
            <div class="login-container">
                <h2>Sistema de Reservas</h2>

                <?php if (isset($_GET['error'])): ?>
                    <div class="error">
                        <?php echo $this->get_error_message($_GET['error']); ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['success'])): ?>
                    <div class="success">Login correcto. Redirigiendo...</div>
                <?php endif; ?>

                <?php if (isset($_GET['logout']) && $_GET['logout'] == 'success'): ?>
                    <div class="success">Sesión cerrada correctamente.</div>
                <?php endif; ?>

                <form method="post" action="">
                    <div class="form-group">
                        <label for="username">Usuario:</label>
                        <input type="text" id="username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Contraseña:</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn-login">Iniciar Sesión</button>
                </form>

                <div class="info-box" style="margin-top: 20px; background: #e8f4f8; border-left: 4px solid #0073aa;">
                    <h4>Acceso para Agencias/Conductor</h4>
                    <p>Si eres una agencia, utiliza las credenciales que te proporcionó el administrador.</p>
                    <p><em>Contacta con el administrador si tienes problemas de acceso.</em></p>
                </div>
            </div>
        </body>

        </html>
    <?php
    }

    public function handle_change_password()
    {
        if (!session_id()) {
            session_start();
        }

        if (!isset($_SESSION['reservas_user']) || $_SESSION['reservas_user']['role'] !== 'super_admin') {
            wp_redirect(home_url('/reservas-login/?error=access'));
            exit;
        }

        if ($_POST && isset($_POST['change_password'])) {
            $this->process_password_change();
        }

        $this->render_change_password_page();
    }

    private function process_password_change()
    {
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        // Validaciones
        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            wp_redirect(home_url('/reservas-change-password/?error=empty_fields'));
            exit;
        }

        if ($new_password !== $confirm_password) {
            wp_redirect(home_url('/reservas-change-password/?error=password_mismatch'));
            exit;
        }

        if (strlen($new_password) < 6) {
            wp_redirect(home_url('/reservas-change-password/?error=password_short'));
            exit;
        }

        // Verificar contraseña actual
        global $wpdb;
        $table_users = $wpdb->prefix . 'reservas_users';
        $user_id = $_SESSION['reservas_user']['id'];

        $user = $wpdb->get_row($wpdb->prepare(
            "SELECT password FROM $table_users WHERE id = %d AND role = 'super_admin'",
            $user_id
        ));

        if (!$user || !password_verify($current_password, $user->password)) {
            wp_redirect(home_url('/reservas-change-password/?error=wrong_current'));
            exit;
        }

        // Actualizar contraseña
        $result = $wpdb->update(
            $table_users,
            array('password' => password_hash($new_password, PASSWORD_DEFAULT)),
            array('id' => $user_id)
        );

        if ($result !== false) {
            wp_redirect(home_url('/reservas-change-password/?success=1'));
            exit;
        } else {
            wp_redirect(home_url('/reservas-change-password/?error=update_failed'));
            exit;
        }
    }

    private function render_change_password_page()
    {
    ?>
        <!DOCTYPE html>
        <html <?php language_attributes(); ?>>

        <head>
            <meta charset="<?php bloginfo('charset'); ?>">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title>Cambiar Contraseña - Sistema de Reservas</title>
            <link rel="stylesheet" href="<?php echo RESERVAS_PLUGIN_URL; ?>assets/css/admin-style.css">
        </head>

        <body>
            <div class="change-password-container">
                <div class="change-password-header">
                    <h1>🔐 Cambiar Contraseña</h1>
                    <a href="<?php echo home_url('/reservas-admin/'); ?>" class="btn-back">← Volver al Dashboard</a>
                </div>

                <?php if (isset($_GET['error'])): ?>
                    <div class="error">
                        <?php echo $this->get_password_error_message($_GET['error']); ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['success'])): ?>
                    <div class="success">
                        ✅ Contraseña cambiada correctamente. Tu nueva contraseña ya está activa.
                    </div>
                <?php endif; ?>

                <div class="change-password-form">
                    <form method="post" action="">
                        <input type="hidden" name="change_password" value="1">

                        <div class="form-group">
                            <label for="current_password">Contraseña Actual *</label>
                            <input type="password" id="current_password" name="current_password" required
                                placeholder="Introduce tu contraseña actual">
                        </div>

                        <div class="form-group">
                            <label for="new_password">Nueva Contraseña *</label>
                            <input type="password" id="new_password" name="new_password" required
                                placeholder="Mínimo 6 caracteres" minlength="6">
                            <small>La nueva contraseña debe tener al menos 6 caracteres</small>
                        </div>

                        <div class="form-group">
                            <label for="confirm_password">Confirmar Nueva Contraseña *</label>
                            <input type="password" id="confirm_password" name="confirm_password" required
                                placeholder="Repite la nueva contraseña">
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn-primary">🔐 Cambiar Contraseña</button>
                            <a href="<?php echo home_url('/reservas-admin/'); ?>" class="btn-secondary">Cancelar</a>
                        </div>
                    </form>
                </div>

                <div class="security-info">
                    <h3>⚠️ Información de Seguridad</h3>
                    <ul>
                        <li>Tu contraseña actual será reemplazada inmediatamente</li>
                        <li>Deberás usar la nueva contraseña en futuros inicios de sesión</li>
                        <li>La sesión actual permanecerá activa</li>
                        <li>Asegúrate de recordar tu nueva contraseña</li>
                    </ul>
                </div>
            </div>

            <style>
                .change-password-container {
                    max-width: 700px;
                    margin: 50px auto;
                    padding: 0;
                    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
                }

                .change-password-header {
                    background: #0073aa;
                    color: white;
                    padding: 30px;
                    border-radius: 8px 8px 0 0;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                }

                .change-password-header h1 {
                    margin: 0;
                    font-size: 24px;
                }

                .btn-back {
                    background: rgba(255, 255, 255, 0.2);
                    color: white;
                    padding: 8px 15px;
                    border-radius: 4px;
                    text-decoration: none;
                    font-size: 14px;
                    transition: background 0.3s;
                }

                .btn-back:hover {
                    background: rgba(255, 255, 255, 0.3);
                    color: white;
                }

                .change-password-form {
                    background: white;
                    padding: 40px;
                    border-radius: 0 0 8px 8px;
                    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
                }

                .form-group {
                    margin-bottom: 25px;
                }

                .form-group label {
                    display: block;
                    margin-bottom: 8px;
                    font-weight: 600;
                    color: #23282d;
                }

                .form-group input {
                    width: 100%;
                    padding: 12px 15px;
                    border: 2px solid #ddd;
                    border-radius: 6px;
                    font-size: 16px;
                    transition: border-color 0.3s;
                    box-sizing: border-box;
                }

                .form-group input:focus {
                    outline: none;
                    border-color: #0073aa;
                    box-shadow: 0 0 5px rgba(0, 115, 170, 0.3);
                }

                .form-group small {
                    display: block;
                    margin-top: 5px;
                    font-size: 13px;
                    color: #666;
                    font-style: italic;
                }

                .form-actions {
                    display: flex;
                    gap: 15px;
                    margin-top: 30px;
                }

                .btn-primary {
                    background: #0073aa;
                    color: white;
                    border: none;
                    padding: 12px 25px;
                    border-radius: 6px;
                    font-size: 16px;
                    font-weight: 600;
                    cursor: pointer;
                    transition: background 0.3s;
                    flex: 1;
                }

                .btn-primary:hover {
                    background: #005177;
                }

                .btn-secondary {
                    background: #f0f0f1;
                    color: #2c3338;
                    border: none;
                    padding: 12px 25px;
                    border-radius: 6px;
                    text-decoration: none;
                    text-align: center;
                    font-size: 16px;
                    transition: background 0.3s;
                    flex: 1;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }

                .btn-secondary:hover {
                    background: #dcdcde;
                    color: #2c3338;
                }

                .security-info {
                    background: #fff3cd;
                    border: 1px solid #ffeaa7;
                    border-radius: 6px;
                    padding: 20px;
                    margin-top: 20px;
                }

                .security-info h3 {
                    margin: 0 0 15px 0;
                    color: #856404;
                }

                .security-info ul {
                    margin: 0;
                    padding-left: 20px;
                    color: #856404;
                }

                .security-info li {
                    margin-bottom: 8px;
                }

                .error,
                .success {
                    padding: 15px;
                    border-radius: 6px;
                    margin-bottom: 20px;
                    font-weight: 600;
                }

                .error {
                    background: #fbeaea;
                    border-left: 4px solid #d63638;
                    color: #d63638;
                }

                .success {
                    background: #edfaed;
                    border-left: 4px solid #00a32a;
                    color: #00a32a;
                }
            </style>
        </body>

        </html>
    <?php
    }

    private function get_password_error_message($error)
    {
        switch ($error) {
            case 'empty_fields':
                return '❌ Todos los campos son obligatorios.';
            case 'password_mismatch':
                return '❌ Las contraseñas no coinciden.';
            case 'password_short':
                return '❌ La nueva contraseña debe tener al menos 6 caracteres.';
            case 'wrong_current':
                return '❌ La contraseña actual es incorrecta.';
            case 'update_failed':
                return '❌ Error al actualizar la contraseña. Inténtalo de nuevo.';
            default:
                return '❌ Error desconocido.';
        }
    }

    private function render_dashboard_page()
    {
        $user = $_SESSION['reservas_user'];
        $is_agency = ($user['role'] === 'agencia');
        $is_super_admin = ($user['role'] === 'super_admin');
        $is_admin = in_array($user['role'], ['super_admin', 'admin']);
        $is_conductor = ($user['role'] === 'conductor');
    ?>
        <!DOCTYPE html>
        <html <?php language_attributes(); ?>>

        <head>
            <meta charset="<?php bloginfo('charset'); ?>">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title>Sistema de Reservas - Dashboard</title>
            <link rel="stylesheet" href="<?php echo RESERVAS_PLUGIN_URL; ?>assets/css/admin-style.css">
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script src="<?php echo RESERVAS_PLUGIN_URL; ?>assets/js/dashboard-script.js"></script>
            <script src="<?php echo RESERVAS_PLUGIN_URL; ?>assets/js/dashboardparte2-script.js"></script>
            <?php if ($is_conductor): ?>
                <script src="<?php echo RESERVAS_PLUGIN_URL; ?>assets/js/conductor-dashboard-script.js"></script>
            <?php endif; ?>
            <script>
                const reservasAjax = {
                    ajax_url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    nonce: '<?php echo wp_create_nonce('reservas_nonce'); ?>'
                };

                window.reservasUser = {
                    role: '<?php echo esc_js($user['role']); ?>',
                    username: '<?php echo esc_js($user['username']); ?>',
                    user_type: '<?php echo esc_js($user['user_type'] ?? 'admin'); ?>'
                };
            </script>

            <style>
                <?php
                $reports_css = RESERVAS_PLUGIN_PATH . 'assets/css/reports-styles.css';
                if (file_exists($reports_css)) {
                    include_once $reports_css;
                } else {
                    echo '.loading { text-align: center; padding: 40px; color: #666; }';
                }
                ?><?php
                    $reports_css = RESERVAS_PLUGIN_PATH . 'assets/css/admin-style.css';
                    if (file_exists($reports_css)) {
                        include_once $reports_css;
                    }
                    ?>.loading {
                    text-align: center;
                    padding: 40px;
                    color: #666;
                }

                .error {
                    background: #fbeaea;
                    border-left: 4px solid #d63638;
                    padding: 12px;
                    margin: 15px 0;
                    border-radius: 4px;
                    color: #d63638;
                }

                .agency-welcome {
                    background: linear-gradient(135deg, #0073aa 0%, #005177 100%);
                    color: white;
                    padding: 30px;
                    border-radius: 8px;
                    margin-bottom: 30px;
                    text-align: center;
                }

                .agency-welcome h2 {
                    margin: 0 0 10px 0;
                    font-size: 28px;
                }

                .agency-welcome p {
                    margin: 0;
                    font-size: 16px;
                    opacity: 0.9;
                }

                .agency-stats {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                    gap: 20px;
                    margin-bottom: 30px;
                }

                .agency-stat-card {
                    background: white;
                    padding: 20px;
                    border-radius: 8px;
                    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                    border-left: 4px solid #0073aa;
                }

                .agency-stat-card h3 {
                    margin: 0 0 10px 0;
                    color: #23282d;
                    font-size: 14px;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                }

                .agency-stat-card .stat-number {
                    font-size: 32px;
                    font-weight: bold;
                    color: #0073aa;
                    margin: 10px 0;
                }

                .agency-stat-card p {
                    margin: 0;
                    color: #666;
                    font-size: 14px;
                }

                .conductor-welcome {
                    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
                    color: white;
                    padding: 30px;
                    border-radius: 8px;
                    margin-bottom: 30px;
                    text-align: center;
                }

                .conductor-welcome h2 {
                    margin: 0 0 10px 0;
                    font-size: 28px;
                }

                .conductor-welcome p {
                    margin: 0;
                    font-size: 16px;
                    opacity: 0.9;
                }

                .conductor-main-action {
                    text-align: center;
                    margin: 40px 0;
                }

                .conductor-main-btn {
                    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
                    color: white;
                    border: none;
                    padding: 20px 40px;
                    font-size: 20px;
                    font-weight: bold;
                    border-radius: 10px;
                    cursor: pointer;
                    transition: all 0.3s;
                    box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
                    min-width: 300px;
                }

                .conductor-main-btn:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 6px 20px rgba(0, 123, 255, 0.4);
                }

                .conductor-info-cards {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
                    gap: 20px;
                    margin-top: 30px;
                }

                .conductor-info-card {
                    background: white;
                    padding: 25px;
                    border-radius: 8px;
                    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
                    border-left: 4px solid #28a745;
                }

                .conductor-info-card h3 {
                    margin: 0 0 15px 0;
                    color: #28a745;
                    font-size: 18px;
                }

                .conductor-info-card p {
                    margin: 5px 0;
                    color: #555;
                    line-height: 1.5;
                }

                /* ✅ CONTENEDOR DINÁMICO PARA CONDUCTOR */
                #conductor-dynamic-content {
                    display: none;
                }
            </style>
        </head>

        <body>
            <div class="dashboard-header">
                <h1>Sistema de Reservas</h1>
                <div class="user-info">
                    <span>Bienvenido, <?php echo esc_html($user['username']); ?></span>
                    <span class="user-role"><?php echo esc_html($user['role']); ?></span>
                    <?php if ($is_agency): ?>
                        <span class="agency-name">(<?php echo esc_html($user['agency_name'] ?? 'Agencia'); ?>)</span>
                    <?php endif; ?>
                    <?php if ($user['role'] === 'super_admin'): ?>
                        <a href="<?php echo home_url('/reservas-change-password/'); ?>" class="btn-change-password">🔐 Cambiar Contraseña</a>
                    <?php endif; ?>
                    <a href="<?php echo home_url('/reservas-login/?logout=1'); ?>" class="btn-logout">Cerrar Sesión</a>
                </div>
            </div>

            <div class="dashboard-content">
                <?php if ($is_conductor): ?>
                    <!-- ✅ DASHBOARD COMPLETO PARA CONDUCTORES -->
                    <div class="conductor-welcome">
                        <h2>👨‍✈️ Bienvenido Conductor!</h2>
                        <p>Panel de control para conductores - Consulta los servicios y las reservas de los pasajeros</p>
                    </div>

                    <div class="conductor-main-action">
                        <button class="conductor-main-btn" onclick="loadConductorCalendarSection()">
                            📅 Ver Servicios y Reservas
                        </button>
                    </div>

                    <div class="conductor-info-cards">
                        <div class="conductor-info-card">
                            <h3>📋 Tu función</h3>
                            <p>• Consultar servicios programados</p>
                            <p>• Ver lista completa de pasajeros por servicio</p>
                            <p>• Verificar datos de las reservas</p>
                            <p>• Confirmar embarque de pasajeros</p>
                        </div>
                        <div class="conductor-info-card">
                            <h3>⏰ Información importante</h3>
                            <p>• Solo verás servicios activos y futuros</p>
                            <p>• Las reservas se muestran en tiempo real</p>
                            <p>• Puedes marcar pasajeros como verificados</p>
                            <p>• Contacta al administrador si hay problemas</p>
                        </div>
                        <div class="conductor-info-card">
                            <h3>📱 Cómo usar el sistema</h3>
                            <p>1. Haz clic en "Ver Servicios y Reservas"</p>
                            <p>2. Selecciona el día en el calendario</p>
                            <p>3. Haz clic en el servicio que te interese</p>
                            <p>4. Revisa la lista completa de pasajeros</p>
                        </div>
                    </div>

                <?php elseif ($is_agency): ?>
                    <!-- Dashboard para Agencias -->
                    <div class="agency-welcome">
                        <h2>¡Bienvenido <?php echo esc_html($user['agency_name'] ?? $user['username']); ?>!</h2>
                        <p>Panel de control para agencias - Gestiona tus reservas y consulta tu información</p>
                    </div>

                    <div class="agency-stats">
                        <div class="agency-stat-card">
                            <h3>Estado</h3>
                            <div class="stat-number">✓</div>
                            <p>Agencia activa</p>
                        </div>
                        <div class="agency-stat-card">
                            <h3>CIF</h3>
                            <div class="stat-number"><?php echo !empty($user['cif']) ? esc_html($user['cif']) : '-'; ?></div>
                            <p>Identificación fiscal</p>
                        </div>
                        <div class="agency-stat-card">
                            <h3>Datos Fiscales</h3>
                            <div class="stat-number"><?php echo (!empty($user['cif']) && !empty($user['razon_social'])) ? '✓' : '⚠️'; ?></div>
                            <p><?php echo (!empty($user['cif']) && !empty($user['razon_social'])) ? 'Completos' : 'Incompletos'; ?></p>
                        </div>
                    </div>

                    <div class="menu-actions">
                        <h3>Funciones Disponibles</h3>
                        <div class="action-buttons">
                            <button class="action-btn" onclick="loadAgencyReservations()">🎫 Mis Reservas</button>

                            <button class="action-btn" onclick="loadAgencyProfile()">👤 Mi Perfil</button>

                            <!-- ✅ NUEVO BOTÓN -->
                            <button class="action-btn" onclick="loadAgencyVisitasReports()" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-left: 4px solid #5b21b6;">📊 Informes Visitas Guiadas</button>

                            <button class="action-btn" onclick="initAgencyReservaRapida()" style="background: linear-gradient(135deg, #0073aa 0%, #005177 100%); border-left: 4px solid #003f5c;">⚡ Reserva Rápida</button>
                        </div>
                    </div>

                <?php else: ?>
                    <!-- Dashboard para Administradores -->
                    <div class="welcome-card">
                        <h2>Dashboard Principal</h2>
                        <p class="status-active">✅ El sistema está funcionando correctamente</p>
                        <p>Has iniciado sesión correctamente en el sistema de reservas.</p>
                    </div>

                    <div class="stats-grid">
                        <div class="stat-card">
                            <h3>Estado del Sistema</h3>
                            <div class="stat-number">✓</div>
                            <p>Operativo</p>
                        </div>
                        <div class="stat-card">
                            <h3>Tu Rol</h3>
                            <div class="stat-number"><?php echo strtoupper($user['role']); ?></div>
                            <p>Nivel de acceso</p>
                        </div>
                        <div class="stat-card">
                            <h3>Versión</h3>
                            <div class="stat-number">2.0</div>
                            <p>Sistema base</p>
                        </div>

                        <?php if ($is_admin): ?>
                            <div class="stat-card">
                                <h3>Reservas Hoy</h3>
                                <div class="stat-number"><?php echo $this->get_reservas_today(); ?></div>
                                <p>Confirmadas</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if ($is_super_admin): ?>
                        <div class="menu-actions">
                            <h3>Acciones Disponibles</h3>
                            <div class="action-buttons">
                                <button class="action-btn" onclick="loadCalendarSection()">📅 Gestionar Calendario</button>
                                <button class="action-btn" onclick="loadDiscountsConfigSection()">💰 Configurar Descuentos</button>
                                <button class="action-btn" onclick="loadConfigurationSection()">⚙️ Configuración</button>
                                <button class="action-btn" onclick="loadReportsSection()">📊 Informes y Reservas</button>
                                <button class="action-btn" onclick="loadVisitasReportsSection()">📊 Informes Visitas Guiadas</button>
                                <button class="action-btn" onclick="loadAdminReservaRapida()" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); border-left: 4px solid #155724;">⚡ Reserva Rápida</button>
                                <button class="action-btn" onclick="loadAdminReservaRetroactiva()" style="background: linear-gradient(135deg, #fd7e14 0%, #e55a00 100%); border-left: 4px solid #c25500;">📅 Reserva Retroactiva</button>
                                <button class="action-btn" onclick="loadAgenciesSection()">🏢 Gestionar Agencias</button>
                            </div>
                        </div>
                    <?php elseif ($is_admin): ?>
                        <div class="menu-actions">
                            <h3>Acciones Disponibles</h3>
                            <div class="action-buttons">
                                <button class="action-btn" onclick="loadCalendarSection()">📅 Gestionar Calendario</button>
                                <button class="action-btn" onclick="loadReportsSection()">📊 Informes y Reservas</button>
                                <button class="action-btn" onclick="loadAdminReservaRapida()" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); border-left: 4px solid #155724;">⚡ Reserva Rápida</button>
                                <button class="action-btn" onclick="alert('Función en desarrollo')">📈 Ver Estadísticas</button>
                            </div>
                        </div>
                    <?php endif; ?>

                <?php endif; ?>

            </div>
            <?php if ($is_conductor): ?>
                <div id="conductor-dynamic-content">
                    <!-- El contenido del calendario se cargará aquí -->
                </div>
            <?php endif; ?>
        </body>

        </html>
<?php
    }

    private function get_reservas_today()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'reservas_reservas';

        $count = $wpdb->get_var("
        SELECT COUNT(*) 
        FROM $table_name 
        WHERE fecha = CURDATE() 
        AND estado = 'confirmada'
    ");

        return $count ? $count : 0;
    }

    private function process_login()
    {
        $username = sanitize_text_field($_POST['username']);
        $password = $_POST['password'];

        // Intentar login como administrador
        global $wpdb;
        $table_users = $wpdb->prefix . 'reservas_users';

        $user = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_users WHERE username = %s AND status = 'active'",
            $username
        ));

        if ($user && password_verify($password, $user->password)) {
            $this->create_admin_session($user);
            wp_redirect(home_url('/reservas-admin/?success=1'));
            exit;
        }

        // Si no es admin, intentar login como agencia
        if (!class_exists('ReservasAgenciesAdmin')) {
            require_once RESERVAS_PLUGIN_PATH . 'includes/class-agencies-admin.php';
        }

        $agency_result = ReservasAgenciesAdmin::authenticate_agency($username, $password);

        if ($agency_result['success']) {
            $this->create_agency_session($agency_result['agency']);
            wp_redirect(home_url('/reservas-admin/?success=1'));
            exit;
        }

        // Si ninguno funciona, error
        wp_redirect(home_url('/reservas-login/?error=invalid'));
        exit;
    }

    private function create_admin_session($user)
    {
        if (!session_id()) {
            session_start();
        }

        $_SESSION['reservas_user'] = array(
            'id' => $user->id,
            'username' => $user->username,
            'email' => $user->email,
            'role' => $user->role,
            'user_type' => 'admin',
            'login_time' => time()
        );
    }

    private function create_agency_session($agency)
    {
        if (!session_id()) {
            session_start();
        }

        $_SESSION['reservas_user'] = $agency;
        $_SESSION['reservas_user']['user_type'] = 'agency';
        $_SESSION['reservas_user']['login_time'] = time();
    }

    public function get_error_message($error)
    {
        switch ($error) {
            case 'invalid':
                return 'Usuario o contraseña incorrectos.';
            case 'access':
                return 'Debes iniciar sesión para acceder.';
            case 'suspended':
                return 'Tu cuenta de agencia está suspendida. Contacta con el administrador.';
            case 'inactive':
                return 'Tu cuenta de agencia está inactiva. Contacta con el administrador.';
            default:
                return 'Error desconocido.';
        }
    }
}
