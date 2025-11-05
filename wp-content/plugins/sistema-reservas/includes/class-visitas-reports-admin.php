<?php

/**
 * Clase para gestionar los informes de visitas guiadas
 * Archivo: wp-content/plugins/sistema-reservas/includes/class-visitas-reports-admin.php
 */
class ReservasVisitasReportsAdmin
{
    public function __construct()
    {
        // Hooks AJAX para informes de visitas
        add_action('wp_ajax_get_visitas_report', array($this, 'get_visitas_report'));
        add_action('wp_ajax_nopriv_get_visitas_report', array($this, 'get_visitas_report'));

        add_action('wp_ajax_search_visitas', array($this, 'search_visitas'));
        add_action('wp_ajax_nopriv_search_visitas', array($this, 'search_visitas'));

        add_action('wp_ajax_get_visita_details', array($this, 'get_visita_details'));
        add_action('wp_ajax_nopriv_get_visita_details', array($this, 'get_visita_details'));

        add_action('wp_ajax_cancel_visita', array($this, 'cancel_visita'));
        add_action('wp_ajax_nopriv_cancel_visita', array($this, 'cancel_visita'));

        add_action('wp_ajax_generate_visita_pdf_download', array($this, 'generate_visita_pdf_download'));
        add_action('wp_ajax_nopriv_generate_visita_pdf_download', array($this, 'generate_visita_pdf_download'));

        add_action('wp_ajax_update_visita_data', array($this, 'update_visita_data'));
        add_action('wp_ajax_nopriv_update_visita_data', array($this, 'update_visita_data'));

        add_action('wp_ajax_resend_visita_confirmation', array($this, 'resend_visita_confirmation'));
        add_action('wp_ajax_nopriv_resend_visita_confirmation', array($this, 'resend_visita_confirmation'));

            add_action('wp_ajax_get_available_schedules_for_visitas_pdf', array($this, 'get_available_schedules_for_visitas_pdf'));
    add_action('wp_ajax_nopriv_get_available_schedules_for_visitas_pdf', array($this, 'get_available_schedules_for_visitas_pdf'));
    
    add_action('wp_ajax_generate_visitas_pdf_report', array($this, 'generate_visitas_pdf_report'));
    add_action('wp_ajax_nopriv_generate_visitas_pdf_report', array($this, 'generate_visitas_pdf_report'));

    }

    /**
     * Obtener informe de visitas por fechas
     */
    public function get_visitas_report()
    {
        error_log('=== VISITAS REPORTS AJAX REQUEST START ===');
    header('Content-Type: application/json');

    try {
        if (!session_id()) {
            session_start();
        }

        if (!isset($_SESSION['reservas_user'])) {
            wp_send_json_error('Sesión expirada. Recarga la página e inicia sesión nuevamente.');
            return;
        }

        $user = $_SESSION['reservas_user'];
        
        // ✅ MODIFICADO: Permitir tanto admin como agencia
        if (!in_array($user['role'], ['super_admin', 'admin', 'agencia'])) {
            wp_send_json_error('Sin permisos');
            return;
        }

        global $wpdb;
        $table_visitas = $wpdb->prefix . 'reservas_visitas';
        $table_agencies = $wpdb->prefix . 'reservas_agencies';

        // Parámetros de filtro
        $fecha_inicio = sanitize_text_field($_POST['fecha_inicio'] ?? date('Y-m-d'));
        $fecha_fin = sanitize_text_field($_POST['fecha_fin'] ?? date('Y-m-d'));
        $tipo_fecha = sanitize_text_field($_POST['tipo_fecha'] ?? 'servicio');
        $estado_filtro = sanitize_text_field($_POST['estado_filtro'] ?? 'confirmadas');
        $agency_filter = sanitize_text_field($_POST['agency_filter'] ?? 'todas');

        // ✅ NUEVO: Si es agencia, forzar su propio ID
        if ($user['role'] === 'agencia') {
            $agency_filter = $user['id'];
            error_log('🔒 Filtro aplicado automáticamente para agencia ID: ' . $agency_filter);
        }

        $page = intval($_POST['page'] ?? 1);
        $per_page = 20;
        $offset = ($page - 1) * $per_page;

            // Construir condiciones WHERE
            $where_conditions = array();
            $query_params = array();

            // Filtro por tipo de fecha
            if ($tipo_fecha === 'compra') {
                $where_conditions[] = "DATE(v.created_at) BETWEEN %s AND %s";
            } else {
                $where_conditions[] = "v.fecha BETWEEN %s AND %s";
            }
            $query_params[] = $fecha_inicio;
            $query_params[] = $fecha_fin;

            // Filtro de estado
            switch ($estado_filtro) {
                case 'confirmadas':
                    $where_conditions[] = "v.estado = 'confirmada'";
                    break;
                case 'canceladas':
                    $where_conditions[] = "v.estado = 'cancelada'";
                    break;
                case 'todas':
                    break;
            }

            // Filtro por agencias
            if ($agency_filter !== 'todas' && is_numeric($agency_filter)) {
                $where_conditions[] = "v.agency_id = %d";
                $query_params[] = intval($agency_filter);
            }

            $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);

            // Query principal
            $query = "SELECT v.*, a.agency_name, a.email as agency_email
                     FROM $table_visitas v
                     LEFT JOIN $table_agencies a ON v.agency_id = a.id
                     $where_clause
                     ORDER BY v.fecha DESC, v.hora DESC
                     LIMIT %d OFFSET %d";

            $query_params[] = $per_page;
            $query_params[] = $offset;

            $visitas = $wpdb->get_results($wpdb->prepare($query, ...$query_params));

            if ($wpdb->last_error) {
                error_log('❌ Database error: ' . $wpdb->last_error);
                wp_send_json_error('Database error: ' . $wpdb->last_error);
                return;
            }

            // Contar total
            $count_query = "SELECT COUNT(*) FROM $table_visitas v 
                           LEFT JOIN $table_agencies a ON v.agency_id = a.id
                           $where_clause";
            $count_params = array_slice($query_params, 0, -2);
            $total_visitas = $wpdb->get_var($wpdb->prepare($count_query, ...$count_params));

            // Estadísticas generales
            $stats = $wpdb->get_row($wpdb->prepare(
                "SELECT 
                    COUNT(*) as total_visitas,
                    SUM(v.adultos) as total_adultos,
                    SUM(v.ninos) as total_ninos,
                    SUM(v.ninos_menores) as total_ninos_menores,
                    SUM(v.total_personas) as total_personas,
                    SUM(v.precio_total) as ingresos_totales
                 FROM $table_visitas v
                 $where_clause",
                ...$count_params
            ));

            $stats_por_agencias = null;
        
        // ✅ MODIFICADO: Solo calcular estadísticas por agencia para super_admin
        if ($agency_filter === 'todas' && $user['role'] === 'super_admin') {
            $stats_por_agencias = $wpdb->get_results($wpdb->prepare(
                "SELECT 
                    v.agency_id,
                    COALESCE(a.agency_name, 'Sin Agencia') as agency_name,
                    COUNT(*) as total_visitas,
                    SUM(v.total_personas) as total_personas,
                    SUM(CASE WHEN v.estado = 'confirmada' THEN v.precio_total ELSE 0 END) as ingresos_total
                 FROM $table_visitas v
                 LEFT JOIN $table_agencies a ON v.agency_id = a.id
                 $where_clause
                 GROUP BY v.agency_id, a.agency_name
                 ORDER BY total_visitas DESC",
                ...$count_params
            ));
        }

            $response_data = array(
                'visitas' => $visitas,
                'stats' => $stats,
                'stats_por_agencias' => $stats_por_agencias,
                'pagination' => array(
                    'current_page' => $page,
                    'total_pages' => ceil($total_visitas / $per_page),
                    'total_items' => $total_visitas,
                    'per_page' => $per_page
                ),
                'filtros' => array(
                    'fecha_inicio' => $fecha_inicio,
                    'fecha_fin' => $fecha_fin,
                    'tipo_fecha' => $tipo_fecha,
                    'estado_filtro' => $estado_filtro,
                    'agency_filter' => $agency_filter
                )
            );

            error_log('✅ Visitas reports data loaded successfully');
            wp_send_json_success($response_data);
        } catch (Exception $e) {
            error_log('❌ VISITAS REPORTS EXCEPTION: ' . $e->getMessage());
            wp_send_json_error('Server error: ' . $e->getMessage());
        }
    }

    /**
     * Buscar visitas por criterios
     */
    public function search_visitas()
    {
        if (!session_id()) {
            session_start();
        }

        if (!isset($_SESSION['reservas_user'])) {
            wp_send_json_error('Sesión expirada');
            return;
        }

        $user = $_SESSION['reservas_user'];
        if (!in_array($user['role'], ['super_admin', 'admin'])) {
            wp_send_json_error('Sin permisos');
            return;
        }

        global $wpdb;
        $table_visitas = $wpdb->prefix . 'reservas_visitas';

        $search_type = sanitize_text_field($_POST['search_type']);
        $search_value = sanitize_text_field($_POST['search_value']);

        $where_clause = '';
        $search_params = array();

        switch ($search_type) {
            case 'localizador':
                $where_clause = "WHERE v.localizador LIKE %s";
                $search_params[] = '%' . $search_value . '%';
                break;
            case 'email':
                $where_clause = "WHERE v.email LIKE %s";
                $search_params[] = '%' . $search_value . '%';
                break;
            case 'telefono':
                $where_clause = "WHERE v.telefono LIKE %s";
                $search_params[] = '%' . $search_value . '%';
                break;
            case 'fecha_servicio':
                $where_clause = "WHERE v.fecha = %s";
                $search_params[] = $search_value;
                break;
            case 'nombre':
                $where_clause = "WHERE (v.nombre LIKE %s OR v.apellidos LIKE %s)";
                $search_params[] = '%' . $search_value . '%';
                $search_params[] = '%' . $search_value . '%';
                break;
            default:
                wp_send_json_error('Tipo de búsqueda no válido');
        }

        $query = "SELECT v.*, a.agency_name
                  FROM $table_visitas v
                  LEFT JOIN {$wpdb->prefix}reservas_agencies a ON v.agency_id = a.id
                  $where_clause
                  ORDER BY v.created_at DESC
                  LIMIT 50";

        $visitas = $wpdb->get_results($wpdb->prepare($query, ...$search_params));

        wp_send_json_success(array(
            'visitas' => $visitas,
            'search_type' => $search_type,
            'search_value' => $search_value,
            'total_found' => count($visitas)
        ));
    }

    public function get_visita_details()
{
    if (!session_id()) {
        session_start();
    }

    if (!isset($_SESSION['reservas_user'])) {
        wp_send_json_error('Sesión expirada');
        return;
    }

    $user = $_SESSION['reservas_user'];
    
    // ✅ MODIFICADO: Permitir agencias
    if (!in_array($user['role'], ['super_admin', 'admin', 'agencia'])) {
        wp_send_json_error('Sin permisos');
        return;
    }

    global $wpdb;
    $table_visitas = $wpdb->prefix . 'reservas_visitas';

    $visita_id = intval($_POST['visita_id']);

    // ✅ MODIFICADO: Si es agencia, verificar que la visita le pertenece
    if ($user['role'] === 'agencia') {
        $visita = $wpdb->get_row($wpdb->prepare(
            "SELECT v.*, a.agency_name
             FROM $table_visitas v
             LEFT JOIN {$wpdb->prefix}reservas_agencies a ON v.agency_id = a.id
             WHERE v.id = %d AND v.agency_id = %d",
            $visita_id,
            $user['id']
        ));
    } else {
        // Para admin/super_admin, sin restricción
        $visita = $wpdb->get_row($wpdb->prepare(
            "SELECT v.*, a.agency_name
             FROM $table_visitas v
             LEFT JOIN {$wpdb->prefix}reservas_agencies a ON v.agency_id = a.id
             WHERE v.id = %d",
            $visita_id
        ));
    }

    if ($visita) {
        wp_send_json_success($visita);
    } else {
        wp_send_json_error('Visita no encontrada o no tiene permisos para verla');
    }
}

public function cancel_visita()
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
    
    // ✅ MODIFICADO: Solo permitir a super_admin cancelar visitas
    if ($user['role'] !== 'super_admin') {
        wp_send_json_error('Sin permisos para cancelar visitas');
        return;
    }

    global $wpdb;
    $table_visitas = $wpdb->prefix . 'reservas_visitas';

    $visita_id = intval($_POST['visita_id']);
    $motivo_cancelacion = sanitize_text_field($_POST['motivo_cancelacion'] ?? 'Cancelación administrativa');

    // ✅ NUEVO: Si es agencia, verificar que la visita le pertenece
    if ($user['role'] === 'agencia') {
        $visita = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_visitas WHERE id = %d AND agency_id = %d",
            $visita_id,
            $user['id']
        ));

        if (!$visita) {
            wp_send_json_error('No tiene permisos para cancelar esta visita');
            return;
        }
    }

    // Actualizar estado
    $result = $wpdb->update(
        $table_visitas,
        array(
            'estado' => 'cancelada',
            'updated_at' => current_time('mysql')
        ),
        array('id' => $visita_id)
    );

    if ($result !== false) {
        // ✅ REGISTRAR QUIÉN CANCELÓ
        error_log("✅ Visita ID {$visita_id} cancelada por {$user['username']} ({$user['role']}). Motivo: {$motivo_cancelacion}");
        
        wp_send_json_success('Visita cancelada correctamente');
    } else {
        wp_send_json_error('Error cancelando la visita: ' . $wpdb->last_error);
    }
}


    /**
     * Generar PDF de visita para descarga
     */
    public function generate_visita_pdf_download()
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
        if (!in_array($user['role'], ['super_admin', 'admin'])) {
            wp_send_json_error('Sin permisos');
            return;
        }

        $visita_id = intval($_POST['visita_id']);
        $localizador = sanitize_text_field($_POST['localizador']);

        try {
            global $wpdb;
            $table_visitas = $wpdb->prefix . 'reservas_visitas';
            $table_services = $wpdb->prefix . 'reservas_agency_services';

            $visita = $wpdb->get_row($wpdb->prepare(
                "SELECT v.*, s.logo_url, s.precio_adulto, s.precio_nino, s.precio_nino_menor, 
                    a.agency_name, a.cif, a.razon_social, a.domicilio_fiscal, a.email as agency_email, a.phone
             FROM $table_visitas v
             INNER JOIN $table_services s ON v.service_id = s.id
             INNER JOIN {$wpdb->prefix}reservas_agencies a ON v.agency_id = a.id
             WHERE v.id = %d",
                $visita_id
            ));

            if (!$visita) {
                wp_send_json_error('Visita no encontrada');
                return;
            }

            // Preparar datos para el PDF
            $reserva_data = array(
                'localizador' => $visita->localizador,
                'fecha' => $visita->fecha,
                'hora' => $visita->hora,
                'hora_vuelta' => '',
                'nombre' => $visita->nombre,
                'apellidos' => $visita->apellidos,
                'email' => $visita->email,
                'telefono' => $visita->telefono,
                'adultos' => $visita->adultos,
                'residentes' => 0,
                'ninos_5_12' => $visita->ninos,
                'ninos_menores' => $visita->ninos_menores,
                'total_personas' => $visita->total_personas,
                'precio_base' => $visita->precio_total,
                'descuento_total' => 0,
                'precio_final' => $visita->precio_total,
                'precio_adulto' => $visita->precio_adulto,
                'precio_nino' => $visita->precio_nino,
                'created_at' => $visita->created_at,
                'metodo_pago' => $visita->metodo_pago,
                'is_visita' => true,
                'agency_logo_url' => $visita->logo_url,
                'agency_name' => $visita->agency_name,
                'agency_cif' => $visita->cif ?? '',
                'agency_razon_social' => $visita->razon_social ?? '',
                'agency_domicilio_fiscal' => $visita->domicilio_fiscal ?? '',
                'agency_email' => $visita->agency_email ?? '',
                'agency_phone' => $visita->phone ?? ''
            );

            // Generar PDF
            if (!class_exists('ReservasPDFGenerator')) {
                require_once RESERVAS_PLUGIN_PATH . 'includes/class-pdf-generator.php';
            }

            $pdf_generator = new ReservasPDFGenerator();
            $pdf_path = $pdf_generator->generate_ticket_pdf($reserva_data);

            if (!$pdf_path || !file_exists($pdf_path)) {
                wp_send_json_error('Error generando el PDF');
                return;
            }

            // Crear URL público
            $upload_dir = wp_upload_dir();
            $relative_path = str_replace($upload_dir['basedir'], '', $pdf_path);
            $pdf_url = $upload_dir['baseurl'] . $relative_path;

            // Programar eliminación
            wp_schedule_single_event(time() + 3600, 'delete_temp_pdf', array($pdf_path));

            wp_send_json_success(array(
                'pdf_url' => $pdf_url,
                'filename' => 'visita_' . $localizador . '.pdf',
                'file_exists' => file_exists($pdf_path),
                'file_size' => filesize($pdf_path)
            ));
        } catch (Exception $e) {
            error_log('Error generando PDF de visita: ' . $e->getMessage());
            wp_send_json_error('Error interno: ' . $e->getMessage());
        }
    }

    public function update_visita_data()
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

    // ✅ MODIFICADO: Permitir agencias editar sus propias visitas
    if (!in_array($user['role'], ['super_admin', 'admin', 'agencia'])) {
        wp_send_json_error('Sin permisos');
        return;
    }

    global $wpdb;
    $table_visitas = $wpdb->prefix . 'reservas_visitas';

    $visita_id = intval($_POST['visita_id']);

    // ✅ NUEVO: Si es agencia, verificar que la visita le pertenece
    if ($user['role'] === 'agencia') {
        $visita_actual = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_visitas WHERE id = %d AND agency_id = %d",
            $visita_id,
            $user['id']
        ));

        if (!$visita_actual) {
            wp_send_json_error('No tiene permisos para editar esta visita');
            return;
        }
    } else {
        // Para admin, obtener sin restricción
        $visita_actual = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_visitas WHERE id = %d",
            $visita_id
        ));
    }

    if (!$visita_actual) {
        wp_send_json_error('Visita no encontrada');
        return;
    }

    $nombre = sanitize_text_field($_POST['nombre']);
    $apellidos = sanitize_text_field($_POST['apellidos']);
    $email = sanitize_email($_POST['email']);
    $telefono = sanitize_text_field($_POST['telefono']);
    $adultos = intval($_POST['adultos']);
    $ninos = intval($_POST['ninos']);
    $ninos_menores = intval($_POST['ninos_menores']);
    $motivo = sanitize_textarea_field($_POST['motivo']);

    // Validaciones
    if (empty($nombre) || strlen($nombre) < 2) {
        wp_send_json_error('El nombre debe tener al menos 2 caracteres');
        return;
    }

    if (empty($apellidos) || strlen($apellidos) < 2) {
        wp_send_json_error('Los apellidos deben tener al menos 2 caracteres');
        return;
    }

    if (!is_email($email)) {
        wp_send_json_error('Email no válido');
        return;
    }

    if (empty($telefono) || strlen($telefono) < 9) {
        wp_send_json_error('Teléfono debe tener al menos 9 dígitos');
        return;
    }

    if ($adultos < 1) {
        wp_send_json_error('Debe haber al menos 1 adulto');
        return;
    }

    if (empty($motivo) || strlen($motivo) < 5) {
        wp_send_json_error('El motivo debe tener al menos 5 caracteres');
        return;
    }

    // Calcular nuevo total de personas
    $total_personas = $adultos + $ninos + $ninos_menores;

    // Actualizar datos
    $result = $wpdb->update(
        $table_visitas,
        array(
            'nombre' => $nombre,
            'apellidos' => $apellidos,
            'email' => $email,
            'telefono' => $telefono,
            'adultos' => $adultos,
            'ninos' => $ninos,
            'ninos_menores' => $ninos_menores,
            'total_personas' => $total_personas,
            'updated_at' => current_time('mysql')
        ),
        array('id' => $visita_id)
    );

    if ($result === false) {
        wp_send_json_error('Error actualizando la visita: ' . $wpdb->last_error);
        return;
    }

    // Registrar cambio en log
    $admin_user = $user['username'] ?? 'admin';
    error_log("VISITA EDITADA - ID: {$visita_id} - Usuario: {$admin_user} ({$user['role']}) - Motivo: {$motivo}");

    // Enviar email de confirmación con nuevos datos si es necesario
    $visita_actualizada = $wpdb->get_row($wpdb->prepare(
        "SELECT v.*, s.precio_adulto, s.precio_nino, s.precio_nino_menor, s.logo_url,
            a.agency_name, a.cif, a.razon_social, a.domicilio_fiscal, a.email as agency_email, a.phone
         FROM $table_visitas v
         INNER JOIN {$wpdb->prefix}reservas_agency_services s ON v.service_id = s.id
         INNER JOIN {$wpdb->prefix}reservas_agencies a ON v.agency_id = a.id
         WHERE v.id = %d",
        $visita_id
    ));

    if ($visita_actualizada && $visita_actualizada->email) {
        if (!class_exists('ReservasEmailService')) {
            require_once RESERVAS_PLUGIN_PATH . 'includes/class-email-service.php';
        }

        $email_data = array(
            'localizador' => $visita_actualizada->localizador,
            'fecha' => $visita_actualizada->fecha,
            'hora' => $visita_actualizada->hora,
            'hora_vuelta' => '',
            'nombre' => $visita_actualizada->nombre,
            'apellidos' => $visita_actualizada->apellidos,
            'email' => $visita_actualizada->email,
            'telefono' => $visita_actualizada->telefono,
            'adultos' => $visita_actualizada->adultos,
            'residentes' => 0,
            'ninos_5_12' => $visita_actualizada->ninos,
            'ninos_menores' => $visita_actualizada->ninos_menores,
            'total_personas' => $visita_actualizada->total_personas,
            'precio_base' => $visita_actualizada->precio_total,
            'descuento_total' => 0,
            'precio_final' => $visita_actualizada->precio_total,
            'precio_adulto' => $visita_actualizada->precio_adulto,
            'precio_nino' => $visita_actualizada->precio_nino,
            'created_at' => $visita_actualizada->created_at,
            'metodo_pago' => $visita_actualizada->metodo_pago,
            'is_visita' => true,
            'agency_logo_url' => $visita_actualizada->logo_url,
            'agency_name' => $visita_actualizada->agency_name
        );

        ReservasEmailService::send_customer_confirmation($email_data);
    }

    wp_send_json_success('Datos actualizados correctamente y email enviado al cliente');
}


    /**
 * Obtener horarios disponibles para filtro de PDF de visitas
 */
public function get_available_schedules_for_visitas_pdf()
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
    if (!in_array($user['role'], ['super_admin', 'admin'])) {
        wp_send_json_error('Sin permisos');
        return;
    }

    global $wpdb;
    $table_visitas = $wpdb->prefix . 'reservas_visitas';
    $table_agencies = $wpdb->prefix . 'reservas_agencies';

    // Obtener filtros
    $fecha_inicio = sanitize_text_field($_POST['fecha_inicio'] ?? date('Y-m-d'));
    $fecha_fin = sanitize_text_field($_POST['fecha_fin'] ?? date('Y-m-d'));
    $tipo_fecha = sanitize_text_field($_POST['tipo_fecha'] ?? 'servicio');
    $estado_filtro = sanitize_text_field($_POST['estado_filtro'] ?? 'confirmadas');
    $agency_filter = sanitize_text_field($_POST['agency_filter'] ?? 'todas');

    try {
        // Construir condiciones WHERE
        $where_conditions = array();
        $query_params = array();

        // Filtro por tipo de fecha
        if ($tipo_fecha === 'compra') {
            $where_conditions[] = "DATE(v.created_at) BETWEEN %s AND %s";
        } else {
            $where_conditions[] = "v.fecha BETWEEN %s AND %s";
        }
        $query_params[] = $fecha_inicio;
        $query_params[] = $fecha_fin;

        // Filtro de estado
        switch ($estado_filtro) {
            case 'confirmadas':
                $where_conditions[] = "v.estado = 'confirmada'";
                break;
            case 'canceladas':
                $where_conditions[] = "v.estado = 'cancelada'";
                break;
            case 'todas':
                break;
        }

        // Filtro por agencias
        if ($agency_filter !== 'todas' && is_numeric($agency_filter)) {
            $where_conditions[] = "v.agency_id = %d";
            $query_params[] = intval($agency_filter);
        }

        $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);

        // Query para obtener horarios únicos
        $schedules_query = "
            SELECT 
                v.hora,
                COUNT(DISTINCT v.id) as count,
                COUNT(DISTINCT v.fecha) as days_count
            FROM $table_visitas v
            LEFT JOIN $table_agencies a ON v.agency_id = a.id
            $where_clause
            GROUP BY v.hora
            ORDER BY v.hora ASC
        ";

        $schedules = $wpdb->get_results($wpdb->prepare($schedules_query, ...$query_params));

        if ($wpdb->last_error) {
            error_log('❌ Database error: ' . $wpdb->last_error);
            wp_send_json_error('Error de base de datos');
            return;
        }

        // Obtener estadísticas generales
        $stats_query = "
            SELECT 
                COUNT(DISTINCT v.id) as total_services,
                COUNT(DISTINCT v.fecha) as days_with_services
            FROM $table_visitas v
            LEFT JOIN $table_agencies a ON v.agency_id = a.id
            $where_clause
        ";

        $stats = $wpdb->get_row($wpdb->prepare($stats_query, ...$query_params));

        $response_data = array(
            'schedules' => $schedules,
            'total_services' => intval($stats->total_services ?? 0),
            'days_with_services' => intval($stats->days_with_services ?? 0),
            'filtros' => array(
                'fecha_inicio' => $fecha_inicio,
                'fecha_fin' => $fecha_fin,
                'tipo_fecha' => $tipo_fecha,
                'estado_filtro' => $estado_filtro,
                'agency_filter' => $agency_filter
            )
        );

        wp_send_json_success($response_data);
    } catch (Exception $e) {
        error_log('❌ Exception: ' . $e->getMessage());
        wp_send_json_error('Error del servidor');
    }
}

/**
 * Generar PDF de informe de visitas
 */
public function generate_visitas_pdf_report()
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
    if (!in_array($user['role'], ['super_admin', 'admin'])) {
        wp_send_json_error('Sin permisos');
        return;
    }

    try {
        error_log('=== GENERANDO PDF DE VISITAS CON FILTROS ===');
        error_log('POST data: ' . print_r($_POST, true));

        // Obtener filtros
        $fecha_inicio = sanitize_text_field($_POST['fecha_inicio'] ?? date('Y-m-d'));
        $fecha_fin = sanitize_text_field($_POST['fecha_fin'] ?? date('Y-m-d'));
        $tipo_fecha = sanitize_text_field($_POST['tipo_fecha'] ?? 'servicio');
        $estado_filtro = sanitize_text_field($_POST['estado_filtro'] ?? 'confirmadas');
        $agency_filter = sanitize_text_field($_POST['agency_filter'] ?? 'todas');
        $selected_schedules = $_POST['selected_schedules'] ?? '';

        error_log('Selected schedules recibido: ' . $selected_schedules);

        // Cargar clase generadora de PDF de visitas
        if (!class_exists('ReservasVisitasReportPDFGenerator')) {
            require_once RESERVAS_PLUGIN_PATH . 'includes/class-visitas-report-pdf-generator.php';
        }

        $pdf_generator = new ReservasVisitasReportPDFGenerator();

        $filtros = array(
            'fecha_inicio' => $fecha_inicio,
            'fecha_fin' => $fecha_fin,
            'tipo_fecha' => $tipo_fecha,
            'estado_filtro' => $estado_filtro,
            'agency_filter' => $agency_filter,
            'selected_schedules' => $selected_schedules
        );

        error_log('Filtros enviados al PDF: ' . print_r($filtros, true));

        $pdf_path = $pdf_generator->generate_report_pdf($filtros);

        if (!$pdf_path || !file_exists($pdf_path)) {
            wp_send_json_error('Error generando el PDF');
            return;
        }

        // Crear URL público
        $upload_dir = wp_upload_dir();
        $pdf_url = str_replace($upload_dir['path'], $upload_dir['url'], $pdf_path);

        // Programar eliminación después de 2 horas
        wp_schedule_single_event(time() + 7200, 'delete_temp_pdf', array($pdf_path));

        $filename = 'informe_visitas_' . $fecha_inicio . '_' . $fecha_fin . '.pdf';

        wp_send_json_success(array(
            'pdf_url' => $pdf_url,
            'filename' => $filename,
            'filtros_aplicados' => $filtros
        ));
    } catch (Exception $e) {
        error_log('Error generando PDF de visitas: ' . $e->getMessage());
        wp_send_json_error('Error interno: ' . $e->getMessage());
    }
}

    /**
 * Reenviar email de confirmación de visita
 */
public function resend_visita_confirmation()
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
    if (!in_array($user['role'], ['super_admin', 'admin'])) {
        wp_send_json_error('Sin permisos');
        return;
    }

    $visita_id = intval($_POST['visita_id']);

    global $wpdb;
    $table_visitas = $wpdb->prefix . 'reservas_visitas';
    $table_services = $wpdb->prefix . 'reservas_agency_services';
    $table_agencies = $wpdb->prefix . 'reservas_agencies';

    $visita = $wpdb->get_row($wpdb->prepare(
        "SELECT v.*, s.precio_adulto, s.precio_nino, s.precio_nino_menor, s.logo_url,
                a.agency_name, a.cif, a.razon_social, a.domicilio_fiscal, a.email as agency_email, a.phone
         FROM $table_visitas v
         INNER JOIN $table_services s ON v.service_id = s.id
         INNER JOIN $table_agencies a ON v.agency_id = a.id
         WHERE v.id = %d",
        $visita_id
    ));

    if (!$visita) {
        wp_send_json_error('Visita no encontrada');
        return;
    }

    // Preparar datos para el email
    $email_data = array(
        'localizador' => $visita->localizador,
        'fecha' => $visita->fecha,
        'hora' => $visita->hora,
        'hora_vuelta' => '',
        'nombre' => $visita->nombre,
        'apellidos' => $visita->apellidos,
        'email' => $visita->email,
        'telefono' => $visita->telefono,
        'adultos' => $visita->adultos,
        'residentes' => 0,
        'ninos_5_12' => $visita->ninos,
        'ninos_menores' => $visita->ninos_menores,
        'total_personas' => $visita->total_personas,
        'precio_base' => $visita->precio_total,
        'descuento_total' => 0,
        'precio_final' => $visita->precio_total,
        'precio_adulto' => $visita->precio_adulto,
        'precio_nino' => $visita->precio_nino,
        'created_at' => $visita->created_at,
        'metodo_pago' => $visita->metodo_pago,
        'is_visita' => true,
        'agency_logo_url' => $visita->logo_url,
        'agency_name' => $visita->agency_name,
        'agency_cif' => $visita->cif ?? '',
        'agency_razon_social' => $visita->razon_social ?? '',
        'agency_domicilio_fiscal' => $visita->domicilio_fiscal ?? '',
        'agency_email' => $visita->agency_email ?? '',
        'agency_phone' => $visita->phone ?? ''
    );

    // Enviar email
    if (!class_exists('ReservasEmailService')) {
        require_once RESERVAS_PLUGIN_PATH . 'includes/class-email-service.php';
    }

    $result = ReservasEmailService::send_customer_confirmation($email_data);

    if ($result['success']) {
        wp_send_json_success('Email reenviado correctamente');
    } else {
        wp_send_json_error('Error enviando el email: ' . $result['message']);
    }
}
}
