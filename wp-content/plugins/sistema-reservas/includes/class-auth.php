<?php
class ReservasAuth {
    
    public function __construct() {
        add_action('wp_ajax_reservas_login', array($this, 'handle_login'));
        add_action('wp_ajax_nopriv_reservas_login', array($this, 'handle_login'));
        add_action('wp_ajax_reservas_logout', array($this, 'handle_logout'));
        add_action('init', array($this, 'start_session'));
        add_action('init', array($this, 'configure_session_security'));
    }
    
    public function start_session() {
        if (!session_id() && !headers_sent()) {
            ini_set('session.cookie_httponly', 1);
            ini_set('session.cookie_secure', is_ssl() ? 1 : 0);
            ini_set('session.cookie_samesite', 'Lax');
            
            session_start();
            
            error_log('SESIÓN INICIADA: ' . session_id() . ' - IP: ' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
        }
    }
    
    public function configure_session_security() {
        if (strpos($_SERVER['REQUEST_URI'], 'reservas-') !== false) {
            header('Access-Control-Allow-Credentials: true');
            
            if (!headers_sent()) {
                header('X-Content-Type-Options: nosniff');
                header('X-Frame-Options: SAMEORIGIN');
            }
        }
    }
    
    public function handle_login() {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'reservas_nonce')) {
            error_log('RESERVAS AUTH: Nonce verification failed');
            wp_send_json_error('Error de seguridad - nonce inválido');
            return;
        }
        
        $username = sanitize_text_field($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if (empty($username) || empty($password)) {
            wp_send_json_error('Usuario y contraseña son obligatorios');
            return;
        }
        
        // ✅ PRIMERO INTENTAR AUTENTICACIÓN COMO USUARIO ADMIN
        $admin_result = $this->authenticate_admin($username, $password);
        
        if ($admin_result['success']) {
            $this->create_session($admin_result['user']);
            wp_send_json_success(array(
                'redirect' => home_url('/reservas-admin/'),
                'message' => 'Login exitoso como ' . $admin_result['user']['role']
            ));
            return;
        }
        
        // ✅ SI NO ES ADMIN, INTENTAR AUTENTICACIÓN COMO AGENCIA
        if (!class_exists('ReservasAgenciesAdmin')) {
            require_once RESERVAS_PLUGIN_PATH . 'includes/class-agencies-admin.php';
        }
        
        $agency_result = ReservasAgenciesAdmin::authenticate_agency($username, $password);
        
        if ($agency_result['success']) {
            $this->create_session($agency_result['agency']);
            wp_send_json_success(array(
                'redirect' => home_url('/reservas-admin/'),
                'message' => 'Login exitoso como agencia'
            ));
            return;
        }
        
        // ✅ SI NINGUNA AUTENTICACIÓN FUNCIONA
        error_log('RESERVAS AUTH: Login failed for user: ' . $username);
        wp_send_json_error('Usuario o contraseña incorrectos');
    }
    
    /**
     * ✅ NUEVA FUNCIÓN: Autenticar usuario administrador
     */
    private function authenticate_admin($username, $password) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'reservas_users';
        
        $user = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE username = %s AND status = 'active'",
            $username
        ));
        
        if ($user && password_verify($password, $user->password)) {
            return array(
                'success' => true,
                'user' => array(
                    'id' => $user->id,
                    'username' => $user->username,
                    'email' => $user->email,
                    'role' => $user->role,
                    'user_type' => 'admin'
                )
            );
        }
        
        return array('success' => false);
    }
    
    /**
     * ✅ NUEVA FUNCIÓN: Crear sesión unificada
     */
    private function create_session($user_data) {
        if (!session_id()) {
            $this->start_session();
        }
        
        session_regenerate_id(true);
        
        $_SESSION['reservas_user'] = array_merge($user_data, array(
            'login_time' => time(),
            'ip_address' => $this->get_client_ip()
        ));
        
        error_log('RESERVAS AUTH: Session created for user: ' . $user_data['username'] . ' (role: ' . $user_data['role'] . ')');
    }
    
    public function handle_logout() {
        if (!session_id()) {
            session_start();
        }
        
        if (isset($_SESSION['reservas_user'])) {
            unset($_SESSION['reservas_user']);
        }
        
        session_destroy();
        
        wp_send_json_success(array(
            'redirect' => home_url('/reservas-login/')
        ));
    }
    
    public static function is_logged_in() {
        if (!session_id()) {
            session_start();
        }
        
        if (!isset($_SESSION['reservas_user'])) {
            return false;
        }
        
        $login_time = $_SESSION['reservas_user']['login_time'] ?? 0;
        $session_duration = 86400; // 24 horas
        
        if ((time() - $login_time) > $session_duration) {
            unset($_SESSION['reservas_user']);
            return false;
        }
        
        return true;
    }
    
    public static function get_current_user() {
        if (!self::is_logged_in()) {
            return null;
        }
        
        return $_SESSION['reservas_user'];
    }
    
    public static function has_permission($required_role) {
        if (!self::is_logged_in()) {
            return false;
        }
        
        $user = self::get_current_user();
        $roles_hierarchy = array(
            'super_admin' => 4,
            'admin' => 3,
            'agencia' => 2,
            'conductor' => 1
        );
        
        $user_level = $roles_hierarchy[$user['role']] ?? 0;
        $required_level = $roles_hierarchy[$required_role] ?? 0;
        
        return $user_level >= $required_level;
    }
    
    public static function require_login() {
        if (!self::is_logged_in()) {
            if (wp_doing_ajax()) {
                return false;
            }
            
            wp_redirect(home_url('/reservas-login/'));
            exit;
        }
        return true;
    }
    
    public static function require_permission($required_role) {
        if (!self::require_login()) {
            if (wp_doing_ajax()) {
                return false;
            }
            wp_die('Sesión expirada');
        }
        
        if (!self::has_permission($required_role)) {
            if (wp_doing_ajax()) {
                return false;
            }
            wp_die('No tienes permisos para acceder a esta página');
        }
        return true;
    }
    
    /**
     * ✅ NUEVA FUNCIÓN: Verificar si el usuario actual es una agencia
     */
    public static function is_agency() {
        $user = self::get_current_user();
        return $user && $user['role'] === 'agencia';
    }
    
    /**
     * ✅ NUEVA FUNCIÓN: Verificar si el usuario actual es administrador
     */
    public static function is_admin() {
        $user = self::get_current_user();
        return $user && in_array($user['role'], ['super_admin', 'admin']);
    }
    
    /**
     * ✅ NUEVA FUNCIÓN: Obtener ID de agencia si el usuario es agencia
     */
    public static function get_agency_id() {
        $user = self::get_current_user();
        return ($user && $user['role'] === 'agencia') ? $user['id'] : null;
    }
    
    private function get_client_ip() {
        $ip_keys = array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR');
        
        foreach ($ip_keys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, 
                        FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    }
    
    public static function verify_ajax_nonce() {
        if (!isset($_POST['nonce'])) {
            error_log('RESERVAS AUTH: No nonce provided in AJAX request');
            wp_send_json_error('Error de seguridad: nonce faltante');
            return false;
        }
        
        if (!wp_verify_nonce($_POST['nonce'], 'reservas_nonce')) {
            error_log('RESERVAS AUTH: Invalid nonce in AJAX request');
            wp_send_json_error('Error de seguridad: nonce inválido');
            return false;
        }
        
        return true;
    }
}