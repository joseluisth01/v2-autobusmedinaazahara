<?php

/**
 * Clase para gestionar Reserva Rápida para administradores Y AGENCIAS
 * Archivo: wp-content/plugins/sistema-reservas/includes/class-reserva-rapida-admin.php
 */
class ReservasReservaRapidaAdmin
{

    public function __construct()
    {
        // Hooks AJAX para reserva rápida ADMIN
        add_action('wp_ajax_get_reserva_rapida_form', array($this, 'get_reserva_rapida_form'));
        add_action('wp_ajax_nopriv_get_reserva_rapida_form', array($this, 'get_reserva_rapida_form'));


        add_action('wp_ajax_process_reserva_rapida', array($this, 'process_reserva_rapida'));
        add_action('wp_ajax_nopriv_process_reserva_rapida', array($this, 'process_reserva_rapida'));

        // ✅ NUEVO: Hooks AJAX para reserva rápida AGENCIAS
        add_action('wp_ajax_get_agency_reserva_rapida_form', array($this, 'get_agency_reserva_rapida_form'));
        add_action('wp_ajax_nopriv_get_agency_reserva_rapida_form', array($this, 'get_agency_reserva_rapida_form'));

        add_action('wp_ajax_process_agency_reserva_rapida', array($this, 'process_agency_reserva_rapida'));
        add_action('wp_ajax_nopriv_process_agency_reserva_rapida', array($this, 'process_agency_reserva_rapida'));

        // Hooks comunes
        add_action('wp_ajax_get_available_services_rapida', array($this, 'get_available_services_rapida'));
        add_action('wp_ajax_nopriv_get_available_services_rapida', array($this, 'get_available_services_rapida'));

        add_action('wp_ajax_calculate_price', array($this, 'calculate_price'));
        add_action('wp_ajax_nopriv_calculate_price', array($this, 'calculate_price'));


        add_action('wp_ajax_get_available_services', array($this, 'get_available_services'));
        add_action('wp_ajax_nopriv_get_available_services', array($this, 'get_available_services'));


        add_action('wp_ajax_calculate_price_rapida', array($this, 'calculate_price_rapida'));
        add_action('wp_ajax_nopriv_calculate_price_rapida', array($this, 'calculate_price_rapida'));

        add_action('wp_ajax_get_reserva_retroactiva_form', array($this, 'get_reserva_retroactiva_form'));
        add_action('wp_ajax_nopriv_get_reserva_retroactiva_form', array($this, 'get_reserva_retroactiva_form'));

        add_action('wp_ajax_get_available_services_retroactiva', array($this, 'get_available_services_retroactiva'));
        add_action('wp_ajax_nopriv_get_available_services_retroactiva', array($this, 'get_available_services_retroactiva'));

        add_action('wp_ajax_process_reservation_retroactiva', array($this, 'process_reservation_retroactiva'));
        add_action('wp_ajax_nopriv_process_reservation_retroactiva', array($this, 'process_reservation_retroactiva'));

        add_action('wp_ajax_get_agencies_for_reservation', array($this, 'get_agencies_for_reservation'));
        add_action('wp_ajax_nopriv_get_agencies_for_reservation', array($this, 'get_agencies_for_reservation'));
    }


    public function get_available_services()
    {
        if (!wp_verify_nonce($_POST['nonce'], 'reservas_nonce')) {
            wp_send_json_error('Error de seguridad');
            return;
        }

        if (!session_id()) {
            session_start();
        }

        if (!isset($_SESSION['reservas_user'])) {
            wp_send_json_error('Sesión expirada');
            return;
        }

        $user = $_SESSION['reservas_user'];

        if (!in_array($user['role'], ['super_admin', 'admin', 'agencia'])) {
            wp_send_json_error('Sin permisos');
            return;
        }

        $month = isset($_POST['month']) ? intval($_POST['month']) : date('n');
        $year = isset($_POST['year']) ? intval($_POST['year']) : date('Y');

        global $wpdb;
        $table_name = $wpdb->prefix . 'reservas_servicios';

        $first_day = sprintf('%04d-%02d-01', $year, $month);
        $last_day = date('Y-m-t', strtotime($first_day));

        $servicios = $wpdb->get_results($wpdb->prepare(
            "SELECT id, fecha, hora, plazas_totales, plazas_disponibles, 
                precio_adulto, precio_nino, precio_residente,
                tiene_descuento, porcentaje_descuento
        FROM $table_name 
        WHERE fecha BETWEEN %s AND %s 
        AND status = 'active'
        AND plazas_disponibles > 0
        ORDER BY fecha, hora",
            $first_day,
            $last_day
        ));

        // Organizar por fecha (mismo formato que frontend)
        $calendar_data = array();
        foreach ($servicios as $servicio) {
            if (!isset($calendar_data[$servicio->fecha])) {
                $calendar_data[$servicio->fecha] = array();
            }

            $calendar_data[$servicio->fecha][] = array(
                'id' => $servicio->id,
                'hora' => substr($servicio->hora, 0, 5),
                'plazas_totales' => $servicio->plazas_totales,
                'plazas_disponibles' => $servicio->plazas_disponibles,
                'precio_adulto' => $servicio->precio_adulto,
                'precio_nino' => $servicio->precio_nino,
                'precio_residente' => $servicio->precio_residente,
                'tiene_descuento' => $servicio->tiene_descuento,
                'porcentaje_descuento' => $servicio->porcentaje_descuento
            );
        }

        wp_send_json_success($calendar_data);
    }


    public function calculate_price()
    {
        if (!wp_verify_nonce($_POST['nonce'], 'reservas_nonce')) {
            wp_send_json_error('Error de seguridad');
            return;
        }

        $service_id = intval($_POST['service_id']);
        $adultos = intval($_POST['adultos']);
        $residentes = intval($_POST['residentes']);
        $ninos_5_12 = intval($_POST['ninos_5_12']);
        $ninos_menores = intval($_POST['ninos_menores']);

        // ✅ VERIFICAR SI ES UNA AGENCIA
        if (!session_id()) {
            session_start();
        }

        $user = $_SESSION['reservas_user'] ?? null;
        $is_agency = ($user && $user['role'] === 'agencia');

        global $wpdb;
        $table_name = $wpdb->prefix . 'reservas_servicios';

        $servicio = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE id = %d",
            $service_id
        ));

        if (!$servicio) {
            wp_send_json_error('Servicio no encontrado');
            return;
        }

        // Usar la misma lógica que el frontend
        $total_personas_con_plaza = $adultos + $residentes + $ninos_5_12;

        // ✅ CALCULAR PRECIO BASE CORRECTO
        $precio_base = 0;

        // ❌ AQUÍ ESTÁ EL ERROR - CAMBIAR ESTO:
        if ($is_agency) {
            // ✅ PARA AGENCIAS: CADA TIPO PAGA SU TARIFA CORRESPONDIENTE
            $precio_base += $adultos * $servicio->precio_adulto;
            $precio_base += $residentes * $servicio->precio_residente;  // ✅ CORREGIDO
            $precio_base += $ninos_5_12 * $servicio->precio_nino;       // ✅ CORREGIDO
        } else {
            // ✅ PARA ADMIN: CADA TIPO PAGA SU TARIFA
            $precio_base += $adultos * $servicio->precio_adulto;
            $precio_base += $residentes * $servicio->precio_residente;
            $precio_base += $ninos_5_12 * $servicio->precio_nino;
        }

        $descuento_total = 0;
        $descuento_grupo = 0;
        $regla_aplicada = null;

        // ✅ PARA AGENCIAS: NO APLICAR DESCUENTOS POR GRUPO
        if (!$is_agency && $total_personas_con_plaza > 0) {
            if (!class_exists('ReservasDiscountsAdmin')) {
                require_once RESERVAS_PLUGIN_PATH . 'includes/class-discounts-admin.php';
            }

            $subtotal = $precio_base;
            $discount_info = ReservasDiscountsAdmin::calculate_discount($total_personas_con_plaza, $subtotal, 'total');

            if ($discount_info['discount_applied']) {
                $descuento_grupo = $discount_info['discount_amount'];
                $descuento_total += $descuento_grupo;
                $regla_aplicada = $discount_info;
            }
        }

        $precio_final = $precio_base - $descuento_total;
        if ($precio_final < 0) $precio_final = 0;

        // Respuesta en el mismo formato que el frontend
        $response_data = array(
            'precio_base' => round($precio_base, 2),
            'descuento' => round($descuento_total, 2),
            'total' => round($precio_final, 2),
            'regla_descuento_aplicada' => $regla_aplicada,
            'is_agency' => $is_agency,
            'descuento_aplicado' => !$is_agency
        );

        wp_send_json_success($response_data);
    }


    /**
     * Obtener formulario de reserva rápida para ADMINISTRADORES
     */
    public function get_reserva_rapida_form()
    {
        error_log('=== GET RESERVA RAPIDA FORM (ADMIN) START ===');
        header('Content-Type: application/json');

        try {
            if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'reservas_nonce')) {
                wp_send_json_error('Error de seguridad');
                return;
            }

            if (!session_id()) {
                session_start();
            }

            if (!isset($_SESSION['reservas_user'])) {
                wp_send_json_error('Sesión expirada. Recarga la página e inicia sesión nuevamente.');
                return;
            }

            $user = $_SESSION['reservas_user'];

            // Solo super_admin y admin pueden usar reserva rápida
            if (!in_array($user['role'], ['super_admin', 'admin'])) {
                wp_send_json_error('Sin permisos para usar reserva rápida');
                return;
            }

            // En lugar de generar HTML, devolver señal para inicializar JavaScript
            wp_send_json_success(array(
                'action' => 'initialize_admin_reserva_rapida',
                'user' => $user,
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('reservas_nonce')
            ));
        } catch (Exception $e) {
            error_log('❌ RESERVA RAPIDA FORM EXCEPTION: ' . $e->getMessage());
            wp_send_json_error('Error del servidor: ' . $e->getMessage());
        }
    }

    public function get_agency_reserva_rapida_form()
    {
        error_log('=== GET AGENCY RESERVA RAPIDA FORM START ===');
        header('Content-Type: application/json');

        try {
            if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'reservas_nonce')) {
                wp_send_json_error('Error de seguridad');
                return;
            }

            if (!session_id()) {
                session_start();
            }

            if (!isset($_SESSION['reservas_user'])) {
                wp_send_json_error('Sesión expirada. Recarga la página e inicia sesión nuevamente.');
                return;
            }

            $user = $_SESSION['reservas_user'];

            if ($user['role'] !== 'agencia') {
                wp_send_json_error('Sin permisos para usar reserva rápida de agencias');
                return;
            }

            // En lugar de generar HTML, devolver señal para inicializar JavaScript
            wp_send_json_success(array(
                'action' => 'initialize_agency_reserva_rapida',
                'user' => $user,
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('reservas_nonce')
            ));
        } catch (Exception $e) {
            error_log('❌ AGENCY RESERVA RAPIDA FORM EXCEPTION: ' . $e->getMessage());
            wp_send_json_error('Error del servidor: ' . $e->getMessage());
        }
    }


    /**
     * Obtener servicios disponibles para los próximos días
     */
    private function get_upcoming_services()
    {
        global $wpdb;
        $table_servicios = $wpdb->prefix . 'reservas_servicios';

        // Obtener fecha actual y configuración de días de anticipación
        $today = date('Y-m-d');

        // Obtener días de anticipación mínima desde configuración
        $dias_anticipacion = 1; // Por defecto
        if (class_exists('ReservasConfigurationAdmin')) {
            $dias_anticipacion = intval(ReservasConfigurationAdmin::get_config('dias_anticipacion_minima', '1'));
        }

        $fecha_inicio = date('Y-m-d', strtotime("+{$dias_anticipacion} days"));
        $fecha_fin = date('Y-m-d', strtotime('+60 days')); // Extender a 60 días

        error_log("Buscando servicios desde {$fecha_inicio} hasta {$fecha_fin}");

        $servicios = $wpdb->get_results($wpdb->prepare(
            "SELECT id, fecha, hora, plazas_disponibles, precio_adulto, precio_nino, precio_residente, plazas_totales
         FROM $table_servicios 
         WHERE fecha BETWEEN %s AND %s 
         AND status = 'active'
         AND plazas_disponibles > 0
         ORDER BY fecha ASC, hora ASC",
            $fecha_inicio,
            $fecha_fin
        ));

        error_log("Query ejecutada: " . $wpdb->last_query);
        error_log("Servicios encontrados: " . count($servicios));

        if ($wpdb->last_error) {
            error_log("Error en query: " . $wpdb->last_error);
        }

        return $servicios;
    }





    /**
     * Procesar reserva rápida para ADMINISTRADORES
     */
    public function process_reserva_rapida()
    {
        // Limpiar cualquier output buffer
        if (ob_get_level()) {
            ob_clean();
        }

        header('Content-Type: application/json');

        try {
            error_log('=== INICIANDO PROCESS_RESERVA_RAPIDA (ADMIN) ===');
            error_log('🎯🎯🎯 PROCESS_RESERVA_RAPIDA (ADMIN) LLAMADO CORRECTAMENTE');
            error_log('POST action: ' . ($_POST['action'] ?? 'NO ACTION'));
            error_log('POST data completo: ' . print_r($_POST, true));

            if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'reservas_nonce')) {
                wp_send_json_error('Error de seguridad');
                return;
            }

            if (!session_id()) {
                session_start();
            }

            if (!isset($_SESSION['reservas_user'])) {
                wp_send_json_error('Sesión expirada');
                return;
            }

            $user = $_SESSION['reservas_user'];

            if (!in_array($user['role'], ['super_admin', 'admin'])) {
                wp_send_json_error('Sin permisos para crear reservas rápidas');
                return;
            }

            // ✅ USAR VALIDACIÓN COMÚN PERO CON DATOS DEL FORMULARIO ADMIN
            $datos = array(
                'nombre' => sanitize_text_field($_POST['nombre'] ?? ''),
                'apellidos' => sanitize_text_field($_POST['apellidos'] ?? ''),
                'email' => sanitize_email($_POST['email'] ?? ''),
                'telefono' => sanitize_text_field($_POST['telefono'] ?? ''),
                'service_id' => intval($_POST['service_id'] ?? 0),
                'adultos' => intval($_POST['adultos'] ?? 0),
                'residentes' => intval($_POST['residentes'] ?? 0),
                'ninos_5_12' => intval($_POST['ninos_5_12'] ?? 0),
                'ninos_menores' => intval($_POST['ninos_menores'] ?? 0)
            );

            // Procesar usando método común
            $this->process_common_reserva_rapida($datos, $user, 'admin');
        } catch (Exception $e) {
            error_log('❌ RESERVA RAPIDA ADMIN EXCEPTION: ' . $e->getMessage());
            wp_send_json_error('Error interno del servidor: ' . $e->getMessage());
        }
    }

    public function process_agency_reserva_rapida()
    {
        // Limpiar cualquier output buffer
        if (ob_get_level()) {
            ob_clean();
        }

        header('Content-Type: application/json');

        try {
            error_log('=== INICIANDO PROCESS_AGENCY_RESERVA_RAPIDA ===');

            if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'reservas_nonce')) {
                wp_send_json_error('Error de seguridad');
                return;
            }

            if (!session_id()) {
                session_start();
            }

            if (!isset($_SESSION['reservas_user'])) {
                wp_send_json_error('Sesión expirada');
                return;
            }

            $user = $_SESSION['reservas_user'];

            if ($user['role'] !== 'agencia') {
                wp_send_json_error('Sin permisos para crear reservas rápidas de agencias');
                return;
            }

            // ✅ USAR VALIDACIÓN COMÚN PERO CON DATOS DEL FORMULARIO AGENCIA
            $datos = array(
                'nombre' => sanitize_text_field($_POST['nombre'] ?? ''),
                'apellidos' => sanitize_text_field($_POST['apellidos'] ?? ''),
                'email' => sanitize_email($_POST['email'] ?? ''), // Puede estar vacío
                'telefono' => sanitize_text_field($_POST['telefono'] ?? ''),
                'service_id' => intval($_POST['service_id'] ?? 0),
                'adultos' => intval($_POST['adultos'] ?? 0),
                'residentes' => intval($_POST['residentes'] ?? 0),
                'ninos_5_12' => intval($_POST['ninos_5_12'] ?? 0),
                'ninos_menores' => intval($_POST['ninos_menores'] ?? 0)
            );

            // Procesar usando método común
            $this->process_common_reserva_rapida($datos, $user, 'agency');
        } catch (Exception $e) {
            error_log('❌ RESERVA RAPIDA AGENCY EXCEPTION: ' . $e->getMessage());
            wp_send_json_error('Error interno del servidor: ' . $e->getMessage());
        }
    }

    private function process_common_reserva_rapida($datos, $user, $user_type)
    {
        // Validar datos del formulario
        $validation_result = $this->validate_reserva_rapida_data();
        if (!$validation_result['valid']) {
            wp_send_json_error($validation_result['error']);
            return;
        }

        $datos = $validation_result['data'];

        // Verificar disponibilidad
        $availability_check = $this->check_service_availability($datos['service_id'], $datos['total_personas']);
        if (!$availability_check['available']) {
            wp_send_json_error($availability_check['error']);
            return;
        }

        // Calcular precio
        $price_calculation = $this->calculate_final_price($datos, $user_type);
        if (!$price_calculation['valid']) {
            wp_send_json_error($price_calculation['error']);
            return;
        }

        // Crear reserva (YA incluye es_reserva_rapida = 1)
        $reservation_result = $this->create_reservation($datos, $price_calculation['price_data'], $user, $user_type);

        if (!$reservation_result['success']) {
            wp_send_json_error($reservation_result['error']);
            return;
        }

        // Actualizar plazas disponibles
        $update_result = $this->update_available_seats($datos['service_id'], $datos['total_personas']);
        if (!$update_result['success']) {
            // Rollback: eliminar reserva creada
            $this->delete_reservation($reservation_result['reservation_id']);
            wp_send_json_error('Error actualizando disponibilidad. Reserva cancelada.');
            return;
        }

        // Enviar emails de confirmación
        $this->send_confirmation_emails($reservation_result['reservation_id'], $user, $user_type);

        // ✅ MENSAJE ESPECÍFICO PARA AGENCIAS
        $mensaje = $user_type === 'agency' ?
            'Reserva rápida de agencia procesada correctamente (precio sin descuentos)' :
            'Reserva rápida procesada correctamente';

        // Respuesta exitosa
        $response_data = array(
            'mensaje' => $mensaje,
            'localizador' => $reservation_result['localizador'],
            'reserva_id' => $reservation_result['reservation_id'],
            'admin_user' => $user['username'],
            'user_type' => $user_type,
            'is_agency' => ($user_type === 'agency'),
            'detalles' => array(
                'fecha' => $datos['fecha'],
                'hora' => $datos['hora'],
                'personas' => $datos['total_personas'],
                'precio_final' => $price_calculation['price_data']['precio_final']
            )
        );

        error_log('✅ RESERVA RAPIDA COMPLETADA EXITOSAMENTE');
        wp_send_json_success($response_data);
    }

    private function validate_reserva_rapida_data()
    {
        $nombre = sanitize_text_field($_POST['nombre'] ?? '');
        $apellidos = sanitize_text_field($_POST['apellidos'] ?? '');
        $email = sanitize_email($_POST['email'] ?? ''); // ✅ PUEDE ESTAR VACÍO PARA AGENCIAS
        $telefono = sanitize_text_field($_POST['telefono'] ?? '');
        $service_id = intval($_POST['service_id'] ?? 0);
        $adultos = intval($_POST['adultos'] ?? 0);
        $residentes = intval($_POST['residentes'] ?? 0);
        $ninos_5_12 = intval($_POST['ninos_5_12'] ?? 0);
        $ninos_menores = intval($_POST['ninos_menores'] ?? 0);

        // Validaciones
        if (empty($nombre) || strlen($nombre) < 2) {
            return array('valid' => false, 'error' => 'El nombre es obligatorio (mínimo 2 caracteres)');
        }

        if (empty($apellidos) || strlen($apellidos) < 2) {
            return array('valid' => false, 'error' => 'Los apellidos son obligatorios (mínimo 2 caracteres)');
        }

        // ✅ EMAIL OPCIONAL PARA AGENCIAS
        if (!empty($email) && !is_email($email)) {
            return array('valid' => false, 'error' => 'Email no válido');
        }

        if (empty($telefono) || strlen($telefono) < 9) {
            return array('valid' => false, 'error' => 'Teléfono debe tener al menos 9 dígitos');
        }

        if ($service_id <= 0) {
            return array('valid' => false, 'error' => 'Debe seleccionar un servicio válido');
        }

        $total_personas = $adultos + $residentes + $ninos_5_12;

        if ($total_personas <= 0) {
            return array('valid' => false, 'error' => 'Debe haber al menos una persona que ocupe plaza');
        }

        if ($ninos_5_12 > 0 && ($adultos + $residentes) <= 0) {
            return array('valid' => false, 'error' => 'Debe haber al menos un adulto si hay niños');
        }

        // Obtener datos del servicio
        global $wpdb;
        $table_servicios = $wpdb->prefix . 'reservas_servicios';

        $servicio = $wpdb->get_row($wpdb->prepare(
            "SELECT fecha, hora FROM $table_servicios WHERE id = %d AND status = 'active'",
            $service_id
        ));

        if (!$servicio) {
            return array('valid' => false, 'error' => 'Servicio seleccionado no válido');
        }

        return array(
            'valid' => true,
            'data' => array(
                'nombre' => $nombre,
                'apellidos' => $apellidos,
                'email' => $email, // ✅ PUEDE SER VACÍO
                'telefono' => $telefono,
                'service_id' => $service_id,
                'fecha' => $servicio->fecha,
                'hora' => $servicio->hora,
                'adultos' => $adultos,
                'residentes' => $residentes,
                'ninos_5_12' => $ninos_5_12,
                'ninos_menores' => $ninos_menores,
                'total_personas' => $total_personas,
                'total_viajeros' => $total_personas + $ninos_menores
            )
        );
    }

    /**
     * Verificar disponibilidad del servicio
     */
    private function check_service_availability($service_id, $personas_necesarias)
    {
        global $wpdb;

        $table_servicios = $wpdb->prefix . 'reservas_servicios';

        $servicio = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_servicios WHERE id = %d AND status = 'active'",
            $service_id
        ));

        if (!$servicio) {
            return array('available' => false, 'error' => 'Servicio no encontrado');
        }

        if ($servicio->plazas_disponibles < $personas_necesarias) {
            return array(
                'available' => false,
                'error' => "Solo quedan {$servicio->plazas_disponibles} plazas disponibles, necesitas {$personas_necesarias}"
            );
        }

        return array('available' => true, 'service' => $servicio);
    }

    /**
     * Calcular precio final
     */
    private function calculate_final_price($datos, $user_type = null)
    {
        global $wpdb;

        $table_servicios = $wpdb->prefix . 'reservas_servicios';

        $servicio = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_servicios WHERE id = %d",
            $datos['service_id']
        ));

        if (!$servicio) {
            return array('valid' => false, 'error' => 'Servicio no encontrado para cálculo');
        }

        // ✅ VERIFICAR SI ES AGENCIA
        $is_agency = ($user_type === 'agency');

        $total_personas_con_plaza = $datos['adultos'] + $datos['residentes'] + $datos['ninos_5_12'];

        // ✅ CALCULAR PRECIO BASE CORRECTO
        $precio_base = 0;

        // ❌ AQUÍ ESTÁ EL SEGUNDO ERROR - CAMBIAR ESTO:
        // ✅ TANTO PARA AGENCIAS COMO PARA ADMIN: CADA TIPO PAGA SU TARIFA
        $precio_base += $datos['adultos'] * $servicio->precio_adulto;
        $precio_base += $datos['residentes'] * $servicio->precio_residente;  // ✅ CORREGIDO
        $precio_base += $datos['ninos_5_12'] * $servicio->precio_nino;       // ✅ CORREGIDO

        $descuento_total = 0;
        $descuento_grupo = 0;
        $regla_aplicada = null;

        // ✅ PARA AGENCIAS: NO APLICAR DESCUENTOS POR GRUPO
        if (!$is_agency && $total_personas_con_plaza > 0) {
            if (!class_exists('ReservasDiscountsAdmin')) {
                require_once RESERVAS_PLUGIN_PATH . 'includes/class-discounts-admin.php';
            }

            $subtotal = $precio_base;
            $discount_info = ReservasDiscountsAdmin::calculate_discount($total_personas_con_plaza, $subtotal, 'total');

            if ($discount_info['discount_applied']) {
                $descuento_grupo = $discount_info['discount_amount'];
                $descuento_total += $descuento_grupo;
                $regla_aplicada = $discount_info;
            }
        }

        $precio_final = $precio_base - $descuento_total;
        if ($precio_final < 0) $precio_final = 0;

        return array(
            'valid' => true,
            'price_data' => array(
                'precio_base' => round($precio_base, 2),
                'descuento_total' => round($descuento_total, 2),
                'precio_final' => round($precio_final, 2),
                'regla_descuento_aplicada' => $regla_aplicada,
                'is_agency' => $is_agency
            )
        );
    }

    private function create_reservation($datos, $price_data, $user, $user_type)
    {
        global $wpdb;

        $table_reservas = $wpdb->prefix . 'reservas_reservas';

        // ✅ GENERAR LOCALIZADOR CON SOPORTE PARA AGENCIAS
        $localizador = $this->generate_localizador($user, $user_type);

        $reserva_data = array(
            'localizador' => $localizador,
            'servicio_id' => $datos['service_id'],
            'fecha' => $datos['fecha'],
            'hora' => $datos['hora'],
            'nombre' => $datos['nombre'],
            'apellidos' => $datos['apellidos'],
            'email' => $datos['email'],
            'telefono' => $datos['telefono'],
            'adultos' => $datos['adultos'],
            'residentes' => $datos['residentes'],
            'ninos_5_12' => $datos['ninos_5_12'],
            'ninos_menores' => $datos['ninos_menores'],
            'total_personas' => $datos['total_personas'],
            'precio_base' => $price_data['precio_base'],
            'descuento_total' => $price_data['descuento_total'],
            'precio_final' => $price_data['precio_final'],
            'regla_descuento_aplicada' => $price_data['regla_descuento_aplicada'] ? json_encode($price_data['regla_descuento_aplicada']) : null,
            'estado' => 'confirmada',
            'metodo_pago' => $user_type === 'agency' ? 'reserva_rapida_agencia' : 'reserva_rapida_admin',
            'es_reserva_rapida' => 1 // ✅ MARCAR COMO RESERVA RÁPIDA
        );

        // ✅ AÑADIR AGENCY_ID SI ES UNA AGENCIA
        if ($user_type === 'agency' && isset($user['id'])) {
            $reserva_data['agency_id'] = $user['id'];
        }

        // ✅✅✅ FORMATOS EXPLÍCITOS PARA wpdb->insert()
        $data_format = array(
            '%s', // localizador
            '%d', // servicio_id
            '%s', // fecha
            '%s', // hora
            '%s', // nombre
            '%s', // apellidos
            '%s', // email
            '%s', // telefono
            '%d', // adultos
            '%d', // residentes
            '%d', // ninos_5_12
            '%d', // ninos_menores
            '%d', // total_personas
            '%f', // precio_base
            '%f', // descuento_total
            '%f', // precio_final
            '%s', // regla_descuento_aplicada
            '%s', // estado
            '%s', // metodo_pago
            '%d'  // es_reserva_rapida
        );

        // Si hay agency_id, añadir su formato
        if ($user_type === 'agency' && isset($user['id'])) {
            $data_format[] = '%d'; // agency_id
        }

        // ✅✅✅ LOG ANTES DE INSERTAR
        error_log('=== DATOS ANTES DE INSERTAR RESERVA RÁPIDA ===');
        error_log('es_reserva_rapida = ' . $reserva_data['es_reserva_rapida']);
        error_log('metodo_pago = ' . $reserva_data['metodo_pago']);
        error_log('user_type = ' . $user_type);

        $resultado = $wpdb->insert($table_reservas, $reserva_data, $data_format);

        if ($resultado === false) {
            error_log('❌ ERROR AL INSERTAR: ' . $wpdb->last_error);
            error_log('❌ QUERY: ' . $wpdb->last_query);
            return array('success' => false, 'error' => 'Error guardando la reserva: ' . $wpdb->last_error);
        }

        $reserva_id = $wpdb->insert_id;

        // ✅✅✅ LOG DESPUÉS DE INSERTAR - VERIFICAR QUÉ SE GUARDÓ REALMENTE
        $reserva_verificacion = $wpdb->get_row($wpdb->prepare(
            "SELECT es_reserva_rapida, metodo_pago FROM $table_reservas WHERE id = %d",
            $reserva_id
        ));

        error_log('=== VERIFICACIÓN DESPUÉS DE INSERTAR ===');
        error_log('Reserva ID: ' . $reserva_id);
        error_log('es_reserva_rapida en BD = ' . $reserva_verificacion->es_reserva_rapida);
        error_log('metodo_pago en BD = ' . $reserva_verificacion->metodo_pago);

        if ($reserva_verificacion->es_reserva_rapida != 1) {
            error_log('❌❌❌ PROBLEMA: El campo no se guardó como 1, intentando UPDATE manual');

            // Forzar update si no se guardó correctamente
            $wpdb->update(
                $table_reservas,
                array('es_reserva_rapida' => 1),
                array('id' => $reserva_id),
                array('%d'),
                array('%d')
            );

            error_log('UPDATE manual ejecutado. Query: ' . $wpdb->last_query);
        }

        return array(
            'success' => true,
            'reservation_id' => $reserva_id,
            'localizador' => $localizador
        );
    }



    /**
     * Actualizar plazas disponibles
     */
    private function update_available_seats($service_id, $personas_ocupadas)
    {
        global $wpdb;

        $table_servicios = $wpdb->prefix . 'reservas_servicios';

        $resultado = $wpdb->query($wpdb->prepare(
            "UPDATE $table_servicios 
             SET plazas_disponibles = plazas_disponibles - %d 
             WHERE id = %d AND plazas_disponibles >= %d",
            $personas_ocupadas,
            $service_id,
            $personas_ocupadas
        ));

        if ($resultado === false) {
            return array('success' => false, 'error' => 'Error actualizando plazas disponibles');
        }

        if ($resultado === 0) {
            return array('success' => false, 'error' => 'No hay suficientes plazas disponibles');
        }

        return array('success' => true);
    }

    /**
     * Eliminar reserva (rollback)
     */
    private function delete_reservation($reservation_id)
    {
        global $wpdb;

        $table_reservas = $wpdb->prefix . 'reservas_reservas';
        $wpdb->delete($table_reservas, array('id' => $reservation_id));
    }

    private function send_confirmation_emails($reservation_id, $user, $user_type)
    {
        error_log('=== ENVIANDO EMAILS DE RESERVA RAPIDA (' . strtoupper($user_type) . ') ===');

        if (!class_exists('ReservasEmailService')) {
            require_once RESERVAS_PLUGIN_PATH . 'includes/class-email-service.php';
        }

        // Obtener datos de la reserva
        global $wpdb;
        $table_reservas = $wpdb->prefix . 'reservas_reservas';

        $reserva = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_reservas WHERE id = %d",
            $reservation_id
        ));

        if (!$reserva) {
            error_log('❌ No se encontró la reserva para enviar emails');
            return;
        }

        // Obtener datos del servicio para precios
        $table_servicios = $wpdb->prefix . 'reservas_servicios';
        $servicio = $wpdb->get_row($wpdb->prepare(
            "SELECT precio_adulto, precio_nino, precio_residente, hora_vuelta FROM $table_servicios WHERE id = %d",
            $reserva->servicio_id
        ));

        // Preparar datos para emails
        $reserva_array = (array) $reserva;
        if ($servicio) {
            $reserva_array['precio_adulto'] = $servicio->precio_adulto;
            $reserva_array['precio_nino'] = $servicio->precio_nino;
            $reserva_array['precio_residente'] = $servicio->precio_residente;
            $reserva_array['hora_vuelta'] = $servicio->hora_vuelta;
        }

        // 1. Email al cliente (SOLO SI TIENE EMAIL)
        if (!empty($reserva->email)) {
            $customer_result = ReservasEmailService::send_customer_confirmation($reserva_array);
            if ($customer_result['success']) {
                error_log('✅ Email enviado al cliente correctamente');
            } else {
                error_log('❌ Error enviando email al cliente: ' . $customer_result['message']);
            }
        } else {
            error_log('ℹ️ No se envió email al cliente (email vacío)');
        }

        // 2. Emails específicos según tipo de usuario
        if ($user_type === 'admin') {
            // Para administradores: email al super_admin
            $admin_result = ReservasEmailService::send_admin_agency_reservation_notification($reserva_array, $user);
            if ($admin_result['success']) {
                error_log('✅ Email enviado al super_admin correctamente');
            } else {
                error_log('❌ Error enviando email al super_admin: ' . $admin_result['message']);
            }
        } elseif ($user_type === 'agency') {
            // ✅ NUEVA LÓGICA PARA AGENCIAS: OBTENER DATOS COMPLETOS
            if (!class_exists('ReservasAgenciesAdmin')) {
                require_once RESERVAS_PLUGIN_PATH . 'includes/class-agencies-admin.php';
            }

            $agency_data = ReservasAgenciesAdmin::get_agency_info($user['id']);

            if ($agency_data) {
                error_log("=== DATOS DE AGENCIA OBTENIDOS ===");
                error_log("Agency name: " . $agency_data->agency_name);
                error_log("Agency email: " . $agency_data->email);
                error_log("Email notificaciones: " . ($agency_data->email_notificaciones ?? 'NO CONFIGURADO'));

                // ✅ DETERMINAR EMAIL DE DESTINO PARA LA AGENCIA
                $agency_email = !empty($agency_data->email_notificaciones) ?
                    $agency_data->email_notificaciones :
                    $agency_data->email;

                error_log("Email final para agencia: " . $agency_email);

                // ✅ A) EMAIL A LA PROPIA AGENCIA (usando su plantilla específica)
                $agency_notification_result = ReservasEmailService::send_agency_self_notification($reserva_array, (array)$agency_data);

                if ($agency_notification_result['success']) {
                    error_log('✅ Email enviado a la agencia: ' . $agency_email);
                } else {
                    error_log('❌ Error enviando email a la agencia: ' . $agency_notification_result['message']);
                }

                // ✅ B) EMAIL AL SUPER_ADMIN (usando plantilla de notificación de agencia)
                $super_admin_data = array(
                    'username' => $agency_data->agency_name,
                    'role' => 'agencia',
                    'agency_name' => $agency_data->agency_name,
                    'email' => $agency_data->email,
                    'commission_percentage' => $agency_data->commission_percentage ?? 0
                );

                $super_admin_result = ReservasEmailService::send_agency_reservation_notification($reserva_array, $super_admin_data);

                if ($super_admin_result['success']) {
                    error_log('✅ Email enviado al super_admin sobre reserva de agencia');
                } else {
                    error_log('❌ Error enviando email al super_admin sobre agencia: ' . $super_admin_result['message']);
                }
            } else {
                error_log('❌ No se pudieron obtener datos completos de la agencia ID: ' . $user['id']);
            }
        }
    }

    private function generate_localizador($user = null, $user_type = null)
    {
        global $wpdb;

        $table_reservas = $wpdb->prefix . 'reservas_reservas';
        $table_config = $wpdb->prefix . 'reservas_configuration';
        $table_agencies = $wpdb->prefix . 'reservas_agencies';

        $año_actual = date('Y');

        // ✅ DETERMINAR SI ES AGENCIA Y OBTENER INICIAL
        $inicial_agencia = '';
        $is_agency = false;

        if ($user_type === 'agency' && isset($user['id'])) {
            $is_agency = true;

            // Obtener inicial de localizador de la agencia
            $agency_data = $wpdb->get_row($wpdb->prepare(
                "SELECT inicial_localizador FROM $table_agencies WHERE id = %d",
                $user['id']
            ));

            if ($agency_data && !empty($agency_data->inicial_localizador)) {
                $inicial_agencia = strtoupper(trim($agency_data->inicial_localizador));
            } else {
                $inicial_agencia = 'A'; // Fallback por defecto
            }

            error_log("=== LOCALIZADOR AGENCIA ===");
            error_log("Agency ID: " . $user['id']);
            error_log("Inicial encontrada: " . $inicial_agencia);
        }

        // ✅ CONFIGURAR CLAVE DE CONFIGURACIÓN
        if ($is_agency) {
            $config_key = "ultimo_localizador_agencia_{$inicial_agencia}_{$año_actual}";
        } else {
            $config_key = "ultimo_localizador_$año_actual";
        }

        error_log("Config key: " . $config_key);

        // Obtener el último número de localizador
        $ultimo_numero = $wpdb->get_var($wpdb->prepare(
            "SELECT config_value FROM $table_config WHERE config_key = %s",
            $config_key
        ));

        if ($ultimo_numero === null) {
            // Primer localizador, empezar desde 1
            $nuevo_numero = 1;

            $description = $is_agency ?
                "Último número de localizador usado por agencia con inicial '$inicial_agencia' en el año $año_actual" :
                "Último número de localizador usado en el año $año_actual";

            // Insertar configuración inicial
            $wpdb->insert(
                $table_config,
                array(
                    'config_key' => $config_key,
                    'config_value' => '1',
                    'config_group' => 'localizadores',
                    'description' => $description
                )
            );
        } else {
            $nuevo_numero = intval($ultimo_numero) + 1;

            // Verificar límite (100000 para cada serie)
            if ($nuevo_numero > 100000) {
                $numero_disponible = $this->buscar_numero_disponible_agencia($año_actual, $inicial_agencia, $is_agency);
                if ($numero_disponible === false) {
                    throw new Exception('Se ha alcanzado el límite máximo de reservas para esta serie (100000)');
                }
                $nuevo_numero = $numero_disponible;
            }

            // Actualizar el contador
            $wpdb->update(
                $table_config,
                array('config_value' => $nuevo_numero),
                array('config_key' => $config_key)
            );
        }

        // ✅ GENERAR LOCALIZADOR CON FORMATO CORRECTO
        if ($is_agency) {
            // Formato: [INICIAL][NUMERO_6_CIFRAS] - Ejemplo: C000001
            $localizador = $inicial_agencia . str_pad($nuevo_numero, 6, '0', STR_PAD_LEFT);
        } else {
            // Formato tradicional: [NUMERO_6_CIFRAS] - Ejemplo: 000001
            $localizador = str_pad($nuevo_numero, 6, '0', STR_PAD_LEFT);
        }

        // Verificar que no exista ya (por seguridad)
        $existe = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_reservas WHERE localizador = %s",
            $localizador
        ));

        if ($existe > 0) {
            // Si existe, intentar con el siguiente número
            return $this->generate_localizador_recursivo_agencia($año_actual, $nuevo_numero + 1, $inicial_agencia, $is_agency);
        }

        error_log("✅ Localizador generado: $localizador (número $nuevo_numero para " . ($is_agency ? "agencia $inicial_agencia" : "admin") . " año $año_actual)");

        return $localizador;
    }

    private function buscar_numero_disponible_agencia($año, $inicial_agencia, $is_agency)
    {
        global $wpdb;
        $table_reservas = $wpdb->prefix . 'reservas_reservas';

        // Buscar el primer número no usado entre 1 y 100000
        for ($i = 1; $i <= 100000; $i++) {
            if ($is_agency) {
                $localizador_test = $inicial_agencia . str_pad($i, 6, '0', STR_PAD_LEFT);
            } else {
                $localizador_test = str_pad($i, 6, '0', STR_PAD_LEFT);
            }

            $existe = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM $table_reservas WHERE localizador = %s",
                $localizador_test
            ));

            if ($existe == 0) {
                return $i;
            }
        }

        return false; // No hay números disponibles
    }

    private function generate_localizador_recursivo_agencia($año, $numero, $inicial_agencia, $is_agency)
    {
        global $wpdb;
        $table_reservas = $wpdb->prefix . 'reservas_reservas';
        $table_config = $wpdb->prefix . 'reservas_configuration';

        if ($numero > 100000) {
            $numero_disponible = $this->buscar_numero_disponible_agencia($año, $inicial_agencia, $is_agency);
            if ($numero_disponible === false) {
                throw new Exception('Se ha alcanzado el límite máximo de reservas para esta serie (100000)');
            }
            $numero = $numero_disponible;
        }

        if ($is_agency) {
            $localizador = $inicial_agencia . str_pad($numero, 6, '0', STR_PAD_LEFT);
            $config_key = "ultimo_localizador_agencia_{$inicial_agencia}_{$año}";
        } else {
            $localizador = str_pad($numero, 6, '0', STR_PAD_LEFT);
            $config_key = "ultimo_localizador_$año";
        }

        $existe = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_reservas WHERE localizador = %s",
            $localizador
        ));

        if ($existe > 0) {
            return $this->generate_localizador_recursivo_agencia($año, $numero + 1, $inicial_agencia, $is_agency);
        }

        // Actualizar contador
        $wpdb->update(
            $table_config,
            array('config_value' => $numero),
            array('config_key' => $config_key)
        );

        return $localizador;
    }


    /**
     * Obtener formulario de reserva retroactiva (solo super_admin)
     */
    public function get_reserva_retroactiva_form()
    {
        error_log('=== GET RESERVA RETROACTIVA FORM START ===');
        header('Content-Type: application/json');

        try {
            if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'reservas_nonce')) {
                wp_send_json_error('Error de seguridad');
                return;
            }

            if (!session_id()) {
                session_start();
            }

            if (!isset($_SESSION['reservas_user'])) {
                wp_send_json_error('Sesión expirada. Recarga la página e inicia sesión nuevamente.');
                return;
            }

            $user = $_SESSION['reservas_user'];

            // ✅ SOLO SUPER_ADMIN PUEDE USAR RESERVA RETROACTIVA
            if ($user['role'] !== 'super_admin') {
                wp_send_json_error('Solo el Super Administrador puede crear reservas retroactivas');
                return;
            }

            wp_send_json_success(array(
                'action' => 'initialize_admin_reserva_retroactiva',
                'user' => $user,
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('reservas_nonce')
            ));
        } catch (Exception $e) {
            error_log('❌ RESERVA RETROACTIVA FORM EXCEPTION: ' . $e->getMessage());
            wp_send_json_error('Error del servidor: ' . $e->getMessage());
        }
    }

    /**
     * Obtener servicios disponibles para reserva retroactiva (TODAS LAS FECHAS)
     */
    public function get_available_services_retroactiva()
    {
        if (!wp_verify_nonce($_POST['nonce'], 'reservas_nonce')) {
            wp_send_json_error('Error de seguridad');
            return;
        }

        if (!session_id()) {
            session_start();
        }

        if (!isset($_SESSION['reservas_user'])) {
            wp_send_json_error('Sesión expirada');
            return;
        }

        $user = $_SESSION['reservas_user'];

        // ✅ SOLO SUPER_ADMIN
        if ($user['role'] !== 'super_admin') {
            wp_send_json_error('Sin permisos para reserva retroactiva');
            return;
        }

        $month = isset($_POST['month']) ? intval($_POST['month']) : date('n');
        $year = isset($_POST['year']) ? intval($_POST['year']) : date('Y');

        global $wpdb;
        $table_name = $wpdb->prefix . 'reservas_servicios';

        $first_day = sprintf('%04d-%02d-01', $year, $month);
        $last_day = date('Y-m-t', strtotime($first_day));

        // ✅ NO FILTRAR POR PLAZAS DISPONIBLES NI FECHA - MOSTRAR TODOS LOS SERVICIOS
        $servicios = $wpdb->get_results($wpdb->prepare(
            "SELECT id, fecha, hora, plazas_totales, plazas_disponibles, 
            precio_adulto, precio_nino, precio_residente,
            tiene_descuento, porcentaje_descuento
    FROM $table_name 
    WHERE fecha BETWEEN %s AND %s 
    AND status = 'active'
    ORDER BY fecha, hora",
            $first_day,
            $last_day
        ));

        // Organizar por fecha
        $calendar_data = array();
        foreach ($servicios as $servicio) {
            if (!isset($calendar_data[$servicio->fecha])) {
                $calendar_data[$servicio->fecha] = array();
            }

            $calendar_data[$servicio->fecha][] = array(
                'id' => $servicio->id,
                'hora' => substr($servicio->hora, 0, 5),
                'plazas_totales' => $servicio->plazas_totales,
                'plazas_disponibles' => $servicio->plazas_disponibles,
                'precio_adulto' => $servicio->precio_adulto,
                'precio_nino' => $servicio->precio_nino,
                'precio_residente' => $servicio->precio_residente,
                'tiene_descuento' => $servicio->tiene_descuento,
                'porcentaje_descuento' => $servicio->porcentaje_descuento
            );
        }

        wp_send_json_success($calendar_data);
    }

    /**
     * Procesar reserva retroactiva - VERSIÓN CORREGIDA
     */
    public function process_reservation_retroactiva()
    {
        if (ob_get_level()) {
            ob_clean();
        }

        header('Content-Type: application/json');

        try {
            error_log('=== INICIANDO PROCESS_RESERVATION_RETROACTIVA ===');

            if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'reservas_nonce')) {
                wp_send_json_error('Error de seguridad');
                return;
            }

            if (!session_id()) {
                session_start();
            }

            if (!isset($_SESSION['reservas_user'])) {
                wp_send_json_error('Sesión expirada');
                return;
            }

            $user = $_SESSION['reservas_user'];

            if ($user['role'] !== 'super_admin') {
                wp_send_json_error('Solo el Super Administrador puede crear reservas retroactivas');
                return;
            }

            global $wpdb;

            // ✅ MEJORAR OBTENCIÓN Y DECODIFICACIÓN DEL JSON
            $reservation_data_json = stripslashes($_POST['reservation_data'] ?? '');

            if (empty($reservation_data_json)) {
                error_log('❌ reservation_data está vacío');
                error_log('POST data: ' . print_r($_POST, true));
                wp_send_json_error('Datos de reserva no proporcionados');
                return;
            }

            error_log('=== JSON RECIBIDO ===');
            error_log('Raw JSON length: ' . strlen($reservation_data_json));
            error_log('Raw JSON: ' . $reservation_data_json);

            $reservation_data = json_decode($reservation_data_json, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                error_log('❌ ERROR JSON: ' . json_last_error_msg());
                error_log('❌ JSON problemático: ' . $reservation_data_json);
                wp_send_json_error('Error decodificando datos de reserva: ' . json_last_error_msg());
                return;
            }

            if (!$reservation_data || !is_array($reservation_data)) {
                error_log('❌ JSON decodificado no es un array válido');
                wp_send_json_error('Datos de reserva no válidos');
                return;
            }

            error_log('=== JSON DECODIFICADO CORRECTAMENTE ===');
            error_log(print_r($reservation_data, true));

            // ✅ VALIDAR CAMPOS OBLIGATORIOS DEL JSON CON MAYOR DETALLE
            $required_fields = ['fecha', 'service_id', 'hora_ida', 'adultos', 'residentes', 'ninos_5_12', 'ninos_menores'];

            foreach ($required_fields as $field) {
                if (!isset($reservation_data[$field])) {
                    error_log("❌ Campo faltante en JSON: $field");
                    wp_send_json_error("Campo obligatorio faltante: $field");
                    return;
                }
            }

            // Extraer y validar datos del JSON
            $fecha = sanitize_text_field($reservation_data['fecha']);
            $service_id = intval($reservation_data['service_id']);
            $hora_ida = sanitize_text_field($reservation_data['hora_ida']);
            $adultos = intval($reservation_data['adultos']);
            $residentes = intval($reservation_data['residentes']);
            $ninos_5_12 = intval($reservation_data['ninos_5_12']);
            $ninos_menores = intval($reservation_data['ninos_menores']);
            $precio_adulto = floatval($reservation_data['precio_adulto'] ?? 0);
            $precio_nino = floatval($reservation_data['precio_nino'] ?? 0);
            $precio_residente = floatval($reservation_data['precio_residente'] ?? 0);
            $total_price = floatval($reservation_data['total_price'] ?? 0);
            $descuento_grupo = floatval($reservation_data['descuento_grupo'] ?? 0);

            // ✅ MANEJO MEJORADO DE AGENCY_ID
            $selected_agency_id = null;
            if (isset($reservation_data['selected_agency_id']) && !empty($reservation_data['selected_agency_id']) && $reservation_data['selected_agency_id'] !== 'null') {
                $selected_agency_id = intval($reservation_data['selected_agency_id']);
            }

            error_log('=== DATOS EXTRAÍDOS DEL JSON ===');
            error_log("Fecha: $fecha");
            error_log("Service ID: $service_id");
            error_log("Agency ID: " . ($selected_agency_id ?? 'null'));
            error_log("Total personas: " . ($adultos + $residentes + $ninos_5_12));

            // Validar campos obligatorios
            if (empty($fecha) || $service_id <= 0 || empty($hora_ida)) {
                wp_send_json_error('Datos de servicio incompletos - fecha: ' . $fecha . ', service_id: ' . $service_id . ', hora: ' . $hora_ida);
                return;
            }

            // Validar datos del cliente
            $nombre = sanitize_text_field($_POST['nombre'] ?? '');
            $apellidos = sanitize_text_field($_POST['apellidos'] ?? '');
            $email = sanitize_email($_POST['email'] ?? '');
            $telefono = sanitize_text_field($_POST['telefono'] ?? '');

            if (empty($nombre) || empty($apellidos) || empty($email) || empty($telefono)) {
                wp_send_json_error('Todos los campos del cliente son obligatorios');
                return;
            }

            if (!is_email($email)) {
                wp_send_json_error('Email no válido');
                return;
            }

            // Validar agencia si está seleccionada
            if ($selected_agency_id) {
                $table_agencies = $wpdb->prefix . 'reservas_agencies';
                $agency_exists = $wpdb->get_var($wpdb->prepare(
                    "SELECT COUNT(*) FROM $table_agencies WHERE id = %d",
                    $selected_agency_id
                ));

                if (!$agency_exists) {
                    wp_send_json_error('La agencia seleccionada no existe');
                    return;
                }

                error_log("✅ Agencia validada correctamente: ID $selected_agency_id");
            }

            // Obtener información del servicio
            $table_servicios = $wpdb->prefix . 'reservas_servicios';

            $servicio = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM $table_servicios WHERE id = %d",
                $service_id
            ));

            if (!$servicio) {
                wp_send_json_error('Servicio no encontrado');
                return;
            }

            $total_personas = $adultos + $residentes + $ninos_5_12;

            // ✅ GENERAR LOCALIZADOR SEGÚN LA AGENCIA
            if ($selected_agency_id) {
                $agency_user = array('id' => $selected_agency_id);
                $localizador = $this->generate_localizador($agency_user, 'agency');
                error_log("✅ Localizador generado para agencia: $localizador");
            } else {
                $localizador = $this->generate_localizador($user, 'admin');
                error_log("✅ Localizador generado para admin: $localizador");
            }

            // ✅ PROCESAR REGLA DE DESCUENTO SI EXISTE
            $regla_descuento_json = null;
            if (isset($reservation_data['regla_descuento_aplicada']) && $reservation_data['regla_descuento_aplicada'] !== null) {
                $regla_descuento_json = json_encode($reservation_data['regla_descuento_aplicada']);
            }

            // ✅ CREAR RESERVA RETROACTIVA
            $table_reservas = $wpdb->prefix . 'reservas_reservas';

            $reserva_data = array(
                'localizador' => $localizador,
                'servicio_id' => $service_id,
                'fecha' => $fecha,
                'hora' => $hora_ida,
                'nombre' => $nombre,
                'apellidos' => $apellidos,
                'email' => $email,
                'telefono' => $telefono,
                'adultos' => $adultos,
                'residentes' => $residentes,
                'ninos_5_12' => $ninos_5_12,
                'ninos_menores' => $ninos_menores,
                'total_personas' => $total_personas,
                'precio_base' => $precio_adulto * $adultos + $precio_residente * $residentes + $precio_nino * $ninos_5_12,
                'descuento_total' => $descuento_grupo,
                'precio_final' => $total_price,
                'regla_descuento_aplicada' => $regla_descuento_json,
                'estado' => 'confirmada',
                'metodo_pago' => 'reserva_retroactiva_admin',
                'es_reserva_rapida' => 1, // ✅ MARCAR COMO RESERVA RÁPIDA
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql')
            );

            // ✅ AÑADIR AGENCY_ID SI ESTÁ SELECCIONADA
            if ($selected_agency_id) {
                $reserva_data['agency_id'] = $selected_agency_id;
                error_log("✅ Asignando reserva a agencia ID: $selected_agency_id");
            }

            error_log('=== DATOS DE RESERVA RETROACTIVA ===');
            error_log(print_r($reserva_data, true));

            $resultado = $wpdb->insert($table_reservas, $reserva_data);

            if ($resultado === false) {
                error_log('❌ ERROR SQL: ' . $wpdb->last_error);
                wp_send_json_error('Error guardando la reserva retroactiva: ' . $wpdb->last_error);
                return;
            }

            $reserva_id = $wpdb->insert_id;
            error_log('✅ Reserva retroactiva creada con ID: ' . $reserva_id);

            // ✅ ENVIAR EMAILS CONSIDERANDO LA AGENCIA
            try {
                if ($selected_agency_id) {
                    if (!class_exists('ReservasAgenciesAdmin')) {
                        require_once RESERVAS_PLUGIN_PATH . 'includes/class-agencies-admin.php';
                    }

                    $agency_data = ReservasAgenciesAdmin::get_agency_info($selected_agency_id);

                    if ($agency_data) {
                        $agency_user = array(
                            'id' => $selected_agency_id,
                            'role' => 'agencia',
                            'username' => $agency_data->agency_name,
                            'agency_name' => $agency_data->agency_name,
                            'email' => $agency_data->email
                        );
                        $this->send_confirmation_emails($reserva_id, $agency_user, 'agency');
                        error_log('✅ Emails enviados considerando agencia');
                    } else {
                        error_log('⚠️ No se pudieron cargar datos de la agencia para emails');
                    }
                } else {
                    $this->send_confirmation_emails($reserva_id, $user, 'admin');
                    error_log('✅ Emails enviados como admin');
                }
            } catch (Exception $e) {
                error_log('⚠️ Error enviando emails: ' . $e->getMessage());
                // No fallar por emails, la reserva ya está creada
            }

            // ✅ PREPARAR RESPUESTA FINAL
            $mensaje = $selected_agency_id ?
                'Reserva retroactiva asignada a agencia procesada correctamente' :
                'Reserva retroactiva procesada correctamente';

            $response_data = array(
                'mensaje' => $mensaje,
                'localizador' => $localizador,
                'reserva_id' => $reserva_id,
                'admin_user' => $user['username'],
                'user_type' => 'admin_retroactiva',
                'assigned_agency_id' => $selected_agency_id,
                'detalles' => array(
                    'fecha' => date('d/m/Y', strtotime($fecha)),
                    'hora' => substr($hora_ida, 0, 5),
                    'personas' => $total_personas . ' persona' . ($total_personas > 1 ? 's' : ''),
                    'precio_final' => number_format($total_price, 2, ',', '.')
                )
            );

            error_log('✅ RESERVA RETROACTIVA COMPLETADA EXITOSAMENTE');
            error_log('Localizador final: ' . $localizador);
            error_log('Asignada a agencia: ' . ($selected_agency_id ? 'Sí (ID: ' . $selected_agency_id . ')' : 'No'));

            wp_send_json_success($response_data);
        } catch (Exception $e) {
            error_log('❌ RESERVA RETROACTIVA EXCEPTION: ' . $e->getMessage());
            error_log('❌ STACK TRACE: ' . $e->getTraceAsString());
            wp_send_json_error('Error interno del servidor: ' . $e->getMessage());
        }
    }

    /**
     * Obtener agencias disponibles para asignar reservas
     */
    public function get_agencies_for_reservation()
    {
        if (!wp_verify_nonce($_POST['nonce'], 'reservas_nonce')) {
            wp_send_json_error('Error de seguridad');
            return;
        }

        if (!session_id()) {
            session_start();
        }

        if (!isset($_SESSION['reservas_user'])) {
            wp_send_json_error('Sesión expirada');
            return;
        }

        $user = $_SESSION['reservas_user'];

        // Solo super_admin puede asignar a agencias
        if ($user['role'] !== 'super_admin') {
            wp_send_json_error('Sin permisos');
            return;
        }

        global $wpdb;
        $table_agencies = $wpdb->prefix . 'reservas_agencies';

        $agencies = $wpdb->get_results(
            "SELECT id, agency_name, inicial_localizador, status 
         FROM $table_agencies 
         ORDER BY agency_name ASC"
        );

        wp_send_json_success($agencies);
    }
}
