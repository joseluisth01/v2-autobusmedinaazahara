<?php
class ReservasAdmin {
    
    public function __construct() {
        // Hooks para AJAX
        add_action('wp_ajax_load_dashboard_section', array($this, 'load_dashboard_section'));
        add_action('wp_ajax_create_user', array($this, 'create_user'));
        add_action('wp_ajax_update_config', array($this, 'update_config'));
        add_action('wp_ajax_get_calendar_data', array($this, 'get_calendar_data'));
        add_action('wp_ajax_save_service', array($this, 'save_service'));
    }
    
    public function load_dashboard_section() {
        // Verificar nonce
        if (!wp_verify_nonce($_POST['nonce'], 'reservas_nonce')) {
            wp_die('Error de seguridad');
        }
        
        ReservasAuth::require_login();
        
        $section = sanitize_text_field($_POST['section']);
        $content = '';
        
        switch ($section) {
            case 'dashboard':
                $content = $this->get_dashboard_home();
                break;
            case 'usuarios':
                ReservasAuth::require_permission('super_admin');
                $content = $this->get_users_management();
                break;
            case 'calendario':
                ReservasAuth::require_permission('admin');
                $content = $this->get_calendar_management();
                break;
            case 'configuracion':
                ReservasAuth::require_permission('super_admin');
                $content = $this->get_configuration();
                break;
            default:
                $content = $this->get_dashboard_home();
        }
        
        wp_send_json_success($content);
    }
    
    private function get_dashboard_home() {
        ob_start();
        ?>
        <div class="dashboard-home">
            <h2>Panel Principal</h2>
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Reservas Hoy</h3>
                    <div class="stat-number"><?php echo $this->get_reservas_today(); ?></div>
                </div>
                <div class="stat-card">
                    <h3>Total Usuarios</h3>
                    <div class="stat-number"><?php echo $this->get_total_users(); ?></div>
                </div>
                <div class="stat-card">
                    <h3>Servicios Activos</h3>
                    <div class="stat-number"><?php echo $this->get_active_services(); ?></div>
                </div>
            </div>
            
            <div class="recent-activity">
                <h3>Actividad Reciente</h3>
                <div class="activity-list">
                    <?php echo $this->get_recent_activity(); ?>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    private function get_users_management() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'reservas_users';
        $users = $wpdb->get_results("SELECT * FROM $table_name ORDER BY created_at DESC");
        
        ob_start();
        ?>
        <div class="users-management">
            <h2>Gestión de Usuarios</h2>
            
            <div class="actions-bar">
                <button class="btn-primary" onclick="showCreateUserModal()">Crear Nuevo Usuario</button>
            </div>
            
            <div class="users-table">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Usuario</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th>Fecha Creación</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo $user->id; ?></td>
                            <td><?php echo esc_html($user->username); ?></td>
                            <td><?php echo esc_html($user->email); ?></td>
                            <td><span class="role-badge role-<?php echo $user->role; ?>"><?php echo $user->role; ?></span></td>
                            <td><span class="status-badge status-<?php echo $user->status; ?>"><?php echo $user->status; ?></span></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($user->created_at)); ?></td>
                            <td>
                                <button class="btn-edit" onclick="editUser(<?php echo $user->id; ?>)">Editar</button>
                                <?php if ($user->role != 'super_admin'): ?>
                                <button class="btn-delete" onclick="deleteUser(<?php echo $user->id; ?>)">Eliminar</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Modal Crear Usuario -->
        <div id="createUserModal" class="modal" style="display: none;">
            <div class="modal-content">
                <span class="close" onclick="closeCreateUserModal()">&times;</span>
                <h3>Crear Nuevo Usuario</h3>
                <form id="createUserForm">
                    <div class="form-group">
                        <label>Usuario:</label>
                        <input type="text" name="username" required>
                    </div>
                    <div class="form-group">
                        <label>Email:</label>
                        <input type="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label>Contraseña:</label>
                        <input type="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label>Rol:</label>
                        <select name="role" required>
                            <option value="admin">Administrador</option>
                            <option value="agencia">Agencia</option>
                            <option value="conductor">Conductor</option>
                        </select>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Crear Usuario</button>
                        <button type="button" class="btn-secondary" onclick="closeCreateUserModal()">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
        
        <script>
        function showCreateUserModal() {
            document.getElementById('createUserModal').style.display = 'block';
        }
        
        function closeCreateUserModal() {
            document.getElementById('createUserModal').style.display = 'none';
        }
        
        // Enviar formulario de crear usuario
        jQuery('#createUserForm').on('submit', function(e) {
            e.preventDefault();
            
            jQuery.ajax({
                url: reservas_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'create_user',
                    username: jQuery('[name="username"]').val(),
                    email: jQuery('[name="email"]').val(),
                    password: jQuery('[name="password"]').val(),
                    role: jQuery('[name="role"]').val(),
                    nonce: reservas_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        alert('Usuario creado correctamente');
                        location.reload();
                    } else {
                        alert('Error: ' + response.data);
                    }
                }
            });
        });
        </script>
        <?php
        return ob_get_clean();
    }
    
    private function get_calendar_management() {
        ob_start();
        ?>
        <div class="calendar-management">
            <h2>Gestión de Calendario</h2>
            
            <div class="calendar-controls">
                <button class="btn-primary" onclick="showAddServiceModal()">Añadir Servicio</button>
                <button class="btn-secondary" onclick="showBulkAddModal()">Añadir Múltiples Días</button>
            </div>
            
            <div class="calendar-container">
                <div id="calendar-grid">
                    <!-- El calendario se cargará aquí via AJAX -->
                </div>
            </div>
        </div>
        
        <!-- Modal Añadir Servicio -->
        <div id="addServiceModal" class="modal" style="display: none;">
            <div class="modal-content">
                <span class="close" onclick="closeAddServiceModal()">&times;</span>
                <h3>Añadir Nuevo Servicio</h3>
                <form id="addServiceForm">
                    <div class="form-group">
                        <label>Fecha:</label>
                        <input type="date" name="fecha" required>
                    </div>
                    <div class="form-group">
                        <label>Hora:</label>
                        <input type="time" name="hora" required>
                    </div>
                    <div class="form-group">
                        <label>Plazas Totales:</label>
                        <input type="number" name="plazas_totales" min="1" max="100" required>
                    </div>
                    <div class="form-group">
                        <label>Precio Adulto (€):</label>
                        <input type="number" name="precio_adulto" step="0.01" min="0" required>
                    </div>
                    <div class="form-group">
                        <label>Precio Niño (€):</label>
                        <input type="number" name="precio_nino" step="0.01" min="0" required>
                    </div>
                    <div class="form-group">
                        <label>Precio Residente (€):</label>
                        <input type="number" name="precio_residente" step="0.01" min="0" required>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Guardar Servicio</button>
                        <button type="button" class="btn-secondary" onclick="closeAddServiceModal()">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
        
        <script>
        function showAddServiceModal() {
            document.getElementById('addServiceModal').style.display = 'block';
        }
        
        function closeAddServiceModal() {
            document.getElementById('addServiceModal').style.display = 'none';
        }
        
        // Cargar calendario al mostrar la sección
        loadCalendarData();
        
        function loadCalendarData() {
            jQuery.ajax({
                url: reservas_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'get_calendar_data',
                    nonce: reservas_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        jQuery('#calendar-grid').html(response.data);
                    }
                }
            });
        }
        </script>
        <?php
        return ob_get_clean();
    }
    
    private function get_configuration() {
        ob_start();
        ?>
        <div class="configuration">
            <h2>Configuración del Sistema</h2>
            
            <form id="configForm">
                <div class="config-section">
                    <h3>Precios por Defecto</h3>
                    <div class="form-group">
                        <label>Precio Adulto (€):</label>
                        <input type="number" name="precio_adulto_defecto" step="0.01" 
                               value="<?php echo ReservasDatabase::get_config('precio_adulto_defecto', '15.00'); ?>">
                    </div>
                    <div class="form-group">
                        <label>Precio Niño (€):</label>
                        <input type="number" name="precio_nino_defecto" step="0.01" 
                               value="<?php echo ReservasDatabase::get_config('precio_nino_defecto', '7.50'); ?>">
                    </div>
                    <div class="form-group">
                        <label>Precio Residente (€):</label>
                        <input type="number" name="precio_residente_defecto" step="0.01" 
                               value="<?php echo ReservasDatabase::get_config('precio_residente_defecto', '5.00'); ?>">
                    </div>
                </div>
                
                <div class="config-section">
                    <h3>Descuentos por Grupo</h3>
                    <div class="form-group">
                        <label>Mínimo personas para descuento:</label>
                        <input type="number" name="descuento_grupo_minimo" min="1" 
                               value="<?php echo ReservasDatabase::get_config('descuento_grupo_minimo', '10'); ?>">
                    </div>
                    <div class="form-group">
                        <label>Porcentaje de descuento (%):</label>
                        <input type="number" name="descuento_grupo_porcentaje" min="0" max="100" 
                               value="<?php echo ReservasDatabase::get_config('descuento_grupo_porcentaje', '15'); ?>">
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn-primary">Guardar Configuración</button>
                </div>
            </form>
        </div>
        
        <script>
        jQuery('#configForm').on('submit', function(e) {
            e.preventDefault();
            
            jQuery.ajax({
                url: reservas_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'update_config',
                    config_data: jQuery(this).serialize(),
                    nonce: reservas_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        alert('Configuración guardada correctamente');
                    } else {
                        alert('Error al guardar la configuración');
                    }
                }
            });
        });
        </script>
        <?php
        return ob_get_clean();
    }
    
    // Métodos para obtener estadísticas
    private function get_reservas_today() {
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
    
    private function get_total_users() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'reservas_users';
        return $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status = 'active'");
    }
    
    private function get_active_services() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'reservas_servicios';
        return $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE fecha >= CURDATE() AND status = 'active'");
    }
    
    private function get_recent_activity() {
        return '<p>No hay actividad reciente</p>';
    }
    
    // Método para crear usuarios via AJAX
    public function create_user() {
        if (!wp_verify_nonce($_POST['nonce'], 'reservas_nonce')) {
            wp_die('Error de seguridad');
        }
        
        ReservasAuth::require_permission('super_admin');
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'reservas_users';
        
        $username = sanitize_text_field($_POST['username']);
        $email = sanitize_email($_POST['email']);
        $password = $_POST['password'];
        $role = sanitize_text_field($_POST['role']);
        
        // Verificar si el usuario ya existe
        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name WHERE username = %s OR email = %s",
            $username, $email
        ));
        
        if ($existing > 0) {
            wp_send_json_error('El usuario o email ya existe');
            return;
        }
        
        $result = $wpdb->insert(
            $table_name,
            array(
                'username' => $username,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'role' => $role,
                'status' => 'active',
                'created_at' => current_time('mysql')
            )
        );
        
        if ($result) {
            wp_send_json_success('Usuario creado correctamente');
        } else {
            wp_send_json_error('Error al crear el usuario');
        }
    }
}