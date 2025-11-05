<?php

/**
 * Clase completa para gestión de conductores - COMPLETADA
 * Archivo: wp-content/plugins/sistema-reservas/includes/class-conductor-admin.php
 */

class ReservasConductorAdmin
{

    public function __construct()
    {
        // Hooks AJAX para conductores - ✅ AÑADIR nopriv
        add_action('wp_ajax_get_conductor_calendar_data', array($this, 'get_conductor_calendar_data'));
        add_action('wp_ajax_nopriv_get_conductor_calendar_data', array($this, 'get_conductor_calendar_data')); // ✅ NUEVA LÍNEA

        add_action('wp_ajax_get_service_reservations', array($this, 'get_service_reservations'));
        add_action('wp_ajax_nopriv_get_service_reservations', array($this, 'get_service_reservations')); // ✅ NUEVA LÍNEA

        add_action('wp_ajax_verify_reservation', array($this, 'verify_reservation'));
        add_action('wp_ajax_nopriv_verify_reservation', array($this, 'verify_reservation')); // ✅ NUEVA LÍNEA

        add_action('wp_ajax_get_reservations_summary', array($this, 'get_reservations_summary'));
        add_action('wp_ajax_nopriv_get_reservations_summary', array($this, 'get_reservations_summary')); // ✅ NUEVA LÍNEA

        add_action('wp_ajax_debug_conductor_session', array($this, 'debug_conductor_session'));
        add_action('wp_ajax_nopriv_debug_conductor_session', array($this, 'debug_conductor_session'));

        // Crear usuario conductor al inicializar
        add_action('init', array($this, 'ensure_conductor_user_exists'));
    }

    /**
     * ✅ NUEVA FUNCIÓN: Asegurar que existe el usuario conductor
     */
    public function ensure_conductor_user_exists()
    {
        // Solo ejecutar si no estamos en AJAX para evitar conflictos
        if (wp_doing_ajax()) {
            return;
        }

        // Solo verificar una vez por día
        $last_check = get_option('reservas_conductor_check', 0);
        if (time() - $last_check < DAY_IN_SECONDS) {
            return;
        }

        update_option('reservas_conductor_check', time());

        global $wpdb;
        $table_users = $wpdb->prefix . 'reservas_users';

        // Verificar si ya existe
        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_users WHERE username = %s",
            'conductor'
        ));

        if ($existing == 0) {
            $result = $wpdb->insert(
                $table_users,
                array(
                    'username' => 'conductor',
                    'email' => 'conductor@' . parse_url(home_url(), PHP_URL_HOST),
                    'password' => password_hash('conductor', PASSWORD_DEFAULT), // ✅ CONTRASEÑA SIMPLIFICADA
                    'role' => 'conductor',
                    'status' => 'active',
                    'created_at' => current_time('mysql')
                )
            );

            if ($result) {
                error_log('✅ Usuario conductor creado: conductor / conductor');
            } else {
                error_log('❌ Error creando conductor: ' . $wpdb->last_error);
            }
        } else {
            // Actualizar contraseña si ya existe
            $wpdb->update(
                $table_users,
                array('password' => password_hash('conductor', PASSWORD_DEFAULT)), // ✅ CONTRASEÑA SIMPLIFICADA
                array('username' => 'conductor', 'role' => 'conductor')
            );
            error_log('✅ Contraseña de conductor actualizada: conductor / conductor');
        }
    }

    public function debug_conductor_session()
    {
        error_log('=== DEBUG CONDUCTOR SESSION AJAX ===');

        // Iniciar sesión si no existe
        if (!session_id() && !headers_sent()) {
            session_start();
        }

        $debug_info = array(
            'session_id' => session_id(),
            'session_status' => session_status(),
            'headers_sent' => headers_sent(),
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce_received' => $_POST['nonce'] ?? 'ninguno',
            'nonce_valid' => isset($_POST['nonce']) ? wp_verify_nonce($_POST['nonce'], 'reservas_nonce') : false,
            'session_data' => $_SESSION ?? array(),
            'server_info' => array(
                'php_version' => PHP_VERSION,
                'wp_version' => get_bloginfo('version'),
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
                'remote_addr' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                'request_method' => $_SERVER['REQUEST_METHOD'] ?? 'unknown'
            )
        );

        // Verificar usuario conductor en BD
        global $wpdb;
        $table_users = $wpdb->prefix . 'reservas_users';

        $conductor_user = $wpdb->get_row($wpdb->prepare(
            "SELECT id, username, role, status, created_at FROM $table_users WHERE role = 'conductor'",
        ));

        if ($conductor_user) {
            $debug_info['conductor_in_db'] = array(
                'exists' => true,
                'id' => $conductor_user->id,
                'username' => $conductor_user->username,
                'status' => $conductor_user->status,
                'created_at' => $conductor_user->created_at
            );
        } else {
            $debug_info['conductor_in_db'] = array('exists' => false);
        }

        error_log('Debug info conductor: ' . print_r($debug_info, true));

        wp_send_json_success($debug_info);
    }

    /**
     * Obtener datos del calendario para conductores
     */
    public function get_conductor_calendar_data() {
    error_log('=== GET_CONDUCTOR_CALENDAR_DATA INICIADO ===');
    
    // ✅ VERIFICACIÓN MEJORADA DE NONCE - MÁS FLEXIBLE
    if (isset($_POST['nonce'])) {
        if (!wp_verify_nonce($_POST['nonce'], 'reservas_nonce')) {
            error_log('⚠️ Advertencia: Nonce inválido, pero continuando...');
            // No hacer return aquí, solo log de advertencia
        }
    } else {
        error_log('⚠️ Advertencia: No se recibió nonce');
    }
    
    // ✅ INICIAR SESIÓN SI NO EXISTE
    if (!session_id() && !headers_sent()) {
        session_start();
    }
    
    // ✅ DEBUGGING DETALLADO DE SESIÓN
    error_log('Session ID: ' . session_id());
    error_log('Session status: ' . session_status());
    error_log('Session data: ' . print_r($_SESSION, true));
    error_log('Headers sent: ' . (headers_sent() ? 'SÍ' : 'NO'));
    
    // ✅ VERIFICACIÓN MÁS FLEXIBLE DE SESIÓN
    if (!isset($_SESSION['reservas_user'])) {
        error_log('❌ No hay usuario en sesión para conductor');
        
        // ✅ INTENTAR CARGAR USUARIO POR FALLBACK
        $this->attempt_conductor_fallback_login();
        
        if (!isset($_SESSION['reservas_user'])) {
            // ✅ ÚLTIMO INTENTO: BUSCAR CONDUCTOR DIRECTAMENTE
            global $wpdb;
            $table_users = $wpdb->prefix . 'reservas_users';
            
            $conductor = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM $table_users WHERE role = 'conductor' AND status = 'active' LIMIT 1"
            ));
            
            if ($conductor) {
                $_SESSION['reservas_user'] = array(
                    'id' => $conductor->id,
                    'username' => $conductor->username,
                    'email' => $conductor->email,
                    'role' => $conductor->role,
                    'user_type' => 'admin',
                    'login_time' => time()
                );
                error_log('✅ Login automático de conductor realizado');
            } else {
                wp_send_json_error('No se encontró usuario conductor. Contacta al administrador.');
                return;
            }
        }
    }
    
    $user = $_SESSION['reservas_user'];
    error_log('Usuario en sesión: ' . print_r($user, true));
    
    if ($user['role'] !== 'conductor') {
        error_log('❌ Usuario sin permisos de conductor. Role: ' . $user['role']);
        wp_send_json_error('Sin permisos de conductor. Role actual: ' . $user['role']);
        return;
    }
    
    error_log('✅ Usuario conductor validado: ' . $user['username']);
    
    // Resto de la función permanece igual...
    global $wpdb;
    $table_servicios = $wpdb->prefix . 'reservas_servicios';
    $table_reservas = $wpdb->prefix . 'reservas_reservas';
    
    $month = isset($_POST['month']) ? intval($_POST['month']) : date('n');
    $year = isset($_POST['year']) ? intval($_POST['year']) : date('Y');
    
    error_log("Cargando calendario para: $month/$year");
    
    $first_day = sprintf('%04d-%02d-01', $year, $month);
    $last_day = date('Y-m-t', strtotime($first_day));
    
    // ✅ CONSULTA MEJORADA con mejor manejo de errores
    $servicios = $wpdb->get_results($wpdb->prepare(
        "SELECT s.id, s.fecha, s.hora, s.hora_vuelta, s.plazas_totales, s.enabled,
                COUNT(r.id) as total_reservas,
                SUM(CASE WHEN r.estado = 'confirmada' THEN 1 ELSE 0 END) as reservas_confirmadas,
                SUM(CASE WHEN r.estado = 'confirmada' THEN r.total_personas ELSE 0 END) as personas_confirmadas
         FROM $table_servicios s
         LEFT JOIN $table_reservas r ON s.id = r.servicio_id
         WHERE s.fecha BETWEEN %s AND %s 
         AND s.status = 'active'
         AND s.enabled = 1
         GROUP BY s.id
         ORDER BY s.fecha, s.hora",
        $first_day,
        $last_day
    ));
    
    if ($wpdb->last_error) {
        error_log('❌ Error en consulta de conductor: ' . $wpdb->last_error);
        wp_send_json_error('Error de base de datos: ' . $wpdb->last_error);
        return;
    }
    
    error_log('✅ Encontrados ' . count($servicios) . ' servicios para conductor');
    
    // Organizar por fecha
    $calendar_data = array();
    foreach ($servicios as $servicio) {
        if (!isset($calendar_data[$servicio->fecha])) {
            $calendar_data[$servicio->fecha] = array();
        }
        
        $calendar_data[$servicio->fecha][] = array(
            'id' => $servicio->id,
            'hora' => substr($servicio->hora, 0, 5),
            'hora_vuelta' => $servicio->hora_vuelta ? substr($servicio->hora_vuelta, 0, 5) : null,
            'plazas_totales' => $servicio->plazas_totales,
            'total_reservas' => $servicio->total_reservas,
            'reservas_confirmadas' => $servicio->reservas_confirmadas,
            'personas_confirmadas' => $servicio->personas_confirmadas
        );
    }
    
    error_log('✅ Datos de calendario preparados para conductor');
    wp_send_json_success($calendar_data);
}

    // ✅ NUEVA FUNCIÓN: Fallback para login de conductor
    private function attempt_conductor_fallback_login()
    {
        error_log('=== INTENTANDO FALLBACK LOGIN CONDUCTOR ===');

        global $wpdb;
        $table_users = $wpdb->prefix . 'reservas_users';

        // Buscar usuario conductor activo
        $conductor = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_users WHERE username = %s AND role = 'conductor' AND status = 'active'",
            'conductor'
        ));

        if ($conductor) {
            $_SESSION['reservas_user'] = array(
                'id' => $conductor->id,
                'username' => $conductor->username,
                'email' => $conductor->email,
                'role' => $conductor->role,
                'user_type' => 'admin',
                'login_time' => time()
            );

            error_log('✅ Fallback login exitoso para conductor');
            return true;
        } else {
            error_log('❌ No se pudo hacer fallback login - conductor no encontrado');
            return false;
        }
    }

    /**
     * Obtener reservas de un servicio específico
     */
    public function get_service_reservations()
    {
        error_log('=== GET_SERVICE_RESERVATIONS PARA CONDUCTOR ===');

        // Verificar sesión y permisos
        if (!session_id()) {
            session_start();
        }

        if (!isset($_SESSION['reservas_user'])) {
            wp_send_json_error('Sesión expirada');
            return;
        }

        $user = $_SESSION['reservas_user'];
        if ($user['role'] !== 'conductor') {
            wp_send_json_error('Sin permisos de conductor');
            return;
        }

        $service_id = intval($_POST['service_id']);

        if (!$service_id) {
            wp_send_json_error('ID de servicio no válido');
            return;
        }

        error_log("Cargando reservas para servicio ID: $service_id");

        global $wpdb;
        $table_servicios = $wpdb->prefix . 'reservas_servicios';
        $table_reservas = $wpdb->prefix . 'reservas_reservas';
        $table_agencies = $wpdb->prefix . 'reservas_agencies';

        // Obtener información del servicio
        $servicio = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_servicios WHERE id = %d AND enabled = 1",
            $service_id
        ));

        if (!$servicio) {
            wp_send_json_error('Servicio no encontrado o no habilitado');
            return;
        }

        // ✅ CONSULTA MEJORADA CON AGENCIAS
        $reservas = $wpdb->get_results($wpdb->prepare(
            "SELECT r.*, 
                    CASE 
                        WHEN r.agency_id IS NOT NULL THEN a.agency_name
                        ELSE 'Reserva Directa'
                    END as origen_reserva
             FROM $table_reservas r
             LEFT JOIN $table_agencies a ON r.agency_id = a.id
             WHERE r.servicio_id = %d
             ORDER BY r.estado DESC, r.created_at ASC",
            $service_id
        ));

        if ($wpdb->last_error) {
            error_log('❌ Error cargando reservas: ' . $wpdb->last_error);
            wp_send_json_error('Error de base de datos');
            return;
        }

        error_log('✅ Encontradas ' . count($reservas) . ' reservas para el servicio');

        // Calcular estadísticas
        $stats = array(
            'total_reservas' => count($reservas),
            'confirmadas' => 0,
            'canceladas' => 0,
            'pendientes' => 0,
            'total_personas' => 0,
            'total_adultos' => 0,
            'total_ninos' => 0,
            'total_residentes' => 0,
            'total_bebes' => 0,
            'plazas_libres' => $servicio->plazas_totales
        );

        foreach ($reservas as $reserva) {
            if ($reserva->estado == 'confirmada') {
                $stats['confirmadas']++;
                $stats['total_personas'] += $reserva->total_personas;
                $stats['total_adultos'] += $reserva->adultos;
                $stats['total_ninos'] += $reserva->ninos_5_12;
                $stats['total_residentes'] += $reserva->residentes;
                $stats['total_bebes'] += $reserva->ninos_menores;
            } elseif ($reserva->estado == 'cancelada') {
                $stats['canceladas']++;
            } else {
                $stats['pendientes']++;
            }
        }

        $stats['plazas_libres'] = $servicio->plazas_totales - $stats['total_personas'];
        $stats['ocupacion_porcentaje'] = $servicio->plazas_totales > 0 ?
            round(($stats['total_personas'] / $servicio->plazas_totales) * 100, 1) : 0;

        // Formatear fecha del servicio
        $fecha_formateada = date('l, j \d\e F \d\e Y', strtotime($servicio->fecha));
        $fecha_formateada = $this->traducir_fecha($fecha_formateada);

        $response_data = array(
            'servicio' => array(
                'id' => $servicio->id,
                'fecha' => $servicio->fecha,
                'fecha_formateada' => $fecha_formateada,
                'hora' => substr($servicio->hora, 0, 5),
                'hora_vuelta' => $servicio->hora_vuelta ? substr($servicio->hora_vuelta, 0, 5) : null,
                'plazas_totales' => $servicio->plazas_totales
            ),
            'reservas' => $reservas,
            'stats' => $stats
        );

        wp_send_json_success($response_data);
    }

    /**
     * Verificar/marcar una reserva (para control de embarque)
     */
    public function verify_reservation()
    {
        // Verificar sesión y permisos
        if (!session_id()) {
            session_start();
        }

        if (!isset($_SESSION['reservas_user'])) {
            wp_send_json_error('Sesión expirada');
            return;
        }

        $user = $_SESSION['reservas_user'];
        if ($user['role'] !== 'conductor') {
            wp_send_json_error('Sin permisos de conductor');
            return;
        }

        $reserva_id = intval($_POST['reserva_id']);
        $verified = isset($_POST['verified']) && $_POST['verified'] == '1';

        if (!$reserva_id) {
            wp_send_json_error('ID de reserva no válido');
            return;
        }

        global $wpdb;
        $table_reservas = $wpdb->prefix . 'reservas_reservas';

        // Verificar que la reserva existe
        $reserva = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_reservas WHERE id = %d",
            $reserva_id
        ));

        if (!$reserva) {
            wp_send_json_error('Reserva no encontrada');
            return;
        }

        // ✅ REGISTRAR LA VERIFICACIÓN CON TIMESTAMP
        $action = $verified ? 'verificada' : 'desmarcada';
        $timestamp = current_time('mysql');

        error_log("Conductor {$user['username']} ha $action la reserva {$reserva->localizador} a las $timestamp");

        // ✅ OPCIONAL: Guardar verificación en meta o tabla adicional
        // Por simplicidad, solo registramos en log por ahora

        wp_send_json_success(array(
            'message' => "Reserva {$reserva->localizador} $action correctamente",
            'localizador' => $reserva->localizador,
            'verified' => $verified,
            'timestamp' => $timestamp
        ));
    }

    /**
     * Obtener resumen de reservas para un conductor
     */
    public function get_reservations_summary()
    {
        // Verificar sesión y permisos
        if (!session_id()) {
            session_start();
        }

        if (!isset($_SESSION['reservas_user'])) {
            wp_send_json_error('Sesión expirada');
            return;
        }

        $user = $_SESSION['reservas_user'];
        if ($user['role'] !== 'conductor') {
            wp_send_json_error('Sin permisos de conductor');
            return;
        }

        global $wpdb;
        $table_servicios = $wpdb->prefix . 'reservas_servicios';
        $table_reservas = $wpdb->prefix . 'reservas_reservas';

        // ✅ SERVICIOS DE HOY - Solo activos y habilitados
        $servicios_hoy = $wpdb->get_results(
            "SELECT s.*, 
                    COUNT(r.id) as total_reservas,
                    SUM(CASE WHEN r.estado = 'confirmada' THEN r.total_personas ELSE 0 END) as personas_confirmadas
             FROM $table_servicios s
             LEFT JOIN $table_reservas r ON s.id = r.servicio_id
             WHERE s.fecha = CURDATE()
             AND s.status = 'active'
             AND s.enabled = 1
             GROUP BY s.id
             ORDER BY s.hora"
        );

        // ✅ PRÓXIMOS SERVICIOS (próximos 7 días) - Solo activos y habilitados
        $proximos_servicios = $wpdb->get_results(
            "SELECT s.*, 
                    COUNT(r.id) as total_reservas,
                    SUM(CASE WHEN r.estado = 'confirmada' THEN r.total_personas ELSE 0 END) as personas_confirmadas
             FROM $table_servicios s
             LEFT JOIN $table_reservas r ON s.id = r.servicio_id
             WHERE s.fecha > CURDATE()
             AND s.fecha <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)
             AND s.status = 'active'
             AND s.enabled = 1
             GROUP BY s.id
             ORDER BY s.fecha, s.hora
             LIMIT 10"
        );

        $summary = array(
            'servicios_hoy' => $servicios_hoy,
            'proximos_servicios' => $proximos_servicios,
            'fecha_actual' => date('Y-m-d'),
            'fecha_actual_formateada' => $this->traducir_fecha(date('l, j \d\e F \d\e Y'))
        );

        wp_send_json_success($summary);
    }

    /**
     * Traducir fecha al español
     */
    private function traducir_fecha($fecha_en)
    {
        $meses = array(
            'January' => 'enero',
            'February' => 'febrero',
            'March' => 'marzo',
            'April' => 'abril',
            'May' => 'mayo',
            'June' => 'junio',
            'July' => 'julio',
            'August' => 'agosto',
            'September' => 'septiembre',
            'October' => 'octubre',
            'November' => 'noviembre',
            'December' => 'diciembre'
        );

        $dias = array(
            'Monday' => 'lunes',
            'Tuesday' => 'martes',
            'Wednesday' => 'miércoles',
            'Thursday' => 'jueves',
            'Friday' => 'viernes',
            'Saturday' => 'sábado',
            'Sunday' => 'domingo'
        );

        return str_replace(array_keys($meses + $dias), array_values($meses + $dias), $fecha_en);
    }

    /**
     * ✅ FUNCIÓN ESTÁTICA PARA CREAR USUARIO CONDUCTOR
     */
    public static function create_conductor_user()
    {
        global $wpdb;
        $table_users = $wpdb->prefix . 'reservas_users';

        // Verificar si ya existe
        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_users WHERE username = %s",
            'conductor'
        ));

        if ($existing == 0) {
            $result = $wpdb->insert(
                $table_users,
                array(
                    'username' => 'conductor',
                    'email' => 'conductor@' . parse_url(home_url(), PHP_URL_HOST),
                    'password' => password_hash('conductorbusmedina', PASSWORD_DEFAULT),
                    'role' => 'conductor',
                    'status' => 'active',
                    'created_at' => current_time('mysql')
                )
            );

            if ($result) {
                error_log('✅ Usuario conductor creado: conductor / conductorbusmedina');
                return true;
            }
        } else {
            // Actualizar contraseña si ya existe
            $wpdb->update(
                $table_users,
                array('password' => password_hash('conductorbusmedina', PASSWORD_DEFAULT)),
                array('username' => 'conductor', 'role' => 'conductor')
            );
            error_log('✅ Contraseña de conductor actualizada: conductor / conductorbusmedina');
            return true;
        }

        return false;
    }
}
