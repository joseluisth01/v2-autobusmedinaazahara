<?php

/**
 * Clase para gestionar los informes y reservas del sistema - ACTUALIZADA CON EMAILS
 * Archivo: wp-content/plugins/sistema-reservas/includes/class-reports-admin.php
 */
class ReservasReportsAdmin
{

    public function __construct()
    {
        // ✅ HOOKS AJAX COMPLETOS PARA INFORMES
        add_action('wp_ajax_get_reservations_report', array($this, 'get_reservations_report'));
        add_action('wp_ajax_nopriv_get_reservations_report', array($this, 'get_reservations_report'));

        add_action('wp_ajax_search_reservations', array($this, 'search_reservations'));
        add_action('wp_ajax_nopriv_search_reservations', array($this, 'search_reservations'));

        add_action('wp_ajax_get_reservation_details', array($this, 'get_reservation_details'));
        add_action('wp_ajax_nopriv_get_reservation_details', array($this, 'get_reservation_details'));

        add_action('wp_ajax_update_reservation_email', array($this, 'update_reservation_email'));
        add_action('wp_ajax_nopriv_update_reservation_email', array($this, 'update_reservation_email'));

        add_action('wp_ajax_resend_confirmation_email', array($this, 'resend_confirmation_email'));
        add_action('wp_ajax_nopriv_resend_confirmation_email', array($this, 'resend_confirmation_email'));

        add_action('wp_ajax_get_date_range_stats', array($this, 'get_date_range_stats'));
        add_action('wp_ajax_nopriv_get_date_range_stats', array($this, 'get_date_range_stats'));

        add_action('wp_ajax_get_quick_stats', array($this, 'get_quick_stats'));
        add_action('wp_ajax_nopriv_get_quick_stats', array($this, 'get_quick_stats'));

        add_action('wp_ajax_cancel_reservation', array($this, 'cancel_reservation'));
        add_action('wp_ajax_nopriv_cancel_reservation', array($this, 'cancel_reservation'));

        add_action('wp_ajax_get_available_services_for_edit', array($this, 'get_available_services_for_edit'));
        add_action('wp_ajax_nopriv_get_available_services_for_edit', array($this, 'get_available_services_for_edit'));

        add_action('wp_ajax_update_reservation_service', array($this, 'update_reservation_service'));
        add_action('wp_ajax_nopriv_update_reservation_service', array($this, 'update_reservation_service'));

        add_action('wp_ajax_get_agencies_for_filter', array($this, 'get_agencies_for_filter'));
        add_action('wp_ajax_nopriv_get_agencies_for_filter', array($this, 'get_agencies_for_filter'));

        // ✅ AÑADIR TAMBIÉN EL DEBUG
        add_action('wp_ajax_debug_agencies_data', array($this, 'debug_agencies_data'));
        add_action('wp_ajax_nopriv_debug_agencies_data', array($this, 'debug_agencies_data'));

        add_action('wp_ajax_generate_ticket_pdf_from_reports', array($this, 'generate_ticket_pdf_from_reports'));
        add_action('wp_ajax_nopriv_generate_ticket_pdf_from_reports', array($this, 'generate_ticket_pdf_from_reports'));

        add_action('wp_ajax_get_agency_reservations_report', array($this, 'get_agency_reservations_report'));
        add_action('wp_ajax_nopriv_get_agency_reservations_report', array($this, 'get_agency_reservations_report'));

        add_action('wp_ajax_search_agency_reservations', array($this, 'search_agency_reservations'));
        add_action('wp_ajax_nopriv_search_agency_reservations', array($this, 'search_agency_reservations'));

        add_action('wp_ajax_get_agency_date_range_stats', array($this, 'get_agency_date_range_stats'));
        add_action('wp_ajax_nopriv_get_agency_date_range_stats', array($this, 'get_agency_date_range_stats'));

        add_action('wp_ajax_get_agency_quick_stats', array($this, 'get_agency_quick_stats'));
        add_action('wp_ajax_nopriv_get_agency_quick_stats', array($this, 'get_agency_quick_stats'));

        add_action('wp_ajax_get_agency_reservation_details', array($this, 'get_agency_reservation_details'));
        add_action('wp_ajax_nopriv_get_agency_reservation_details', array($this, 'get_agency_reservation_details'));

        add_action('wp_ajax_generate_agency_ticket_pdf', array($this, 'generate_agency_ticket_pdf'));
        add_action('wp_ajax_nopriv_generate_agency_ticket_pdf', array($this, 'generate_agency_ticket_pdf'));


        add_action('wp_ajax_check_agency_cancellation_allowed', array($this, 'check_agency_cancellation_allowed'));
        add_action('wp_ajax_nopriv_check_agency_cancellation_allowed', array($this, 'check_agency_cancellation_allowed'));

        add_action('wp_ajax_process_agency_direct_cancellation', array($this, 'process_agency_direct_cancellation'));
        add_action('wp_ajax_nopriv_process_agency_direct_cancellation', array($this, 'process_agency_direct_cancellation'));

        add_action('wp_ajax_generate_reservations_pdf_report', array($this, 'generate_reservations_pdf_report'));
        add_action('wp_ajax_nopriv_generate_reservations_pdf_report', array($this, 'generate_reservations_pdf_report'));

        add_action('wp_ajax_get_available_schedules_for_pdf', array($this, 'get_available_schedules_for_pdf'));
        add_action('wp_ajax_nopriv_get_available_schedules_for_pdf', array($this, 'get_available_schedules_for_pdf'));

        add_action('wp_ajax_update_reservation_data', array($this, 'update_reservation_data'));
        add_action('wp_ajax_nopriv_update_reservation_data', array($this, 'update_reservation_data'));
    }

    /**
     * Actualizar datos de reserva (solo super_admin) - VERSIÓN CORREGIDA
     */
    public function update_reservation_data()
    {
        error_log('=== UPDATE_RESERVATION_DATA INICIADO ===');

        if (!wp_verify_nonce($_POST['nonce'], 'reservas_nonce')) {
            wp_send_json_error('Error de seguridad');
            return;
        }

        if (!session_id()) {
            session_start();
        }

        if (!isset($_SESSION['reservas_user']) || $_SESSION['reservas_user']['role'] !== 'super_admin') {
            wp_send_json_error('Solo los Super Administradores pueden editar datos de reservas');
            return;
        }

        global $wpdb;
        $table_reservas = $wpdb->prefix . 'reservas_reservas';
        $table_servicios = $wpdb->prefix . 'reservas_servicios';

        $reserva_id = intval($_POST['reserva_id']);
        $adultos = intval($_POST['adultos']);
        $residentes = intval($_POST['residentes']);
        $ninos_5_12 = intval($_POST['ninos_5_12']);
        $ninos_menores = intval($_POST['ninos_menores']);
        $precio_base = floatval($_POST['precio_base']);
        $descuento_total = floatval($_POST['descuento_total']);
        $precio_final = floatval($_POST['precio_final']);
        $motivo_cambio = sanitize_text_field($_POST['motivo_cambio']);

        error_log('Datos recibidos: ' . print_r($_POST, true));

        // Validaciones
        if (($ninos_5_12 + $ninos_menores) > 0 && ($adultos + $residentes) === 0) {
            wp_send_json_error('Debe haber al menos un adulto si hay niños en la reserva');
            return;
        }

        if (empty($motivo_cambio)) {
            wp_send_json_error('Es obligatorio especificar el motivo del cambio');
            return;
        }

        // Obtener datos actuales de la reserva
        $reserva_actual = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_reservas WHERE id = %d",
            $reserva_id
        ));

        if (!$reserva_actual) {
            error_log('Reserva no encontrada con ID: ' . $reserva_id);
            wp_send_json_error('Reserva no encontrada');
            return;
        }

        error_log('Reserva actual: ' . print_r($reserva_actual, true));

        // Calcular nuevo total de personas con plaza
        $nuevo_total_personas = $adultos + $residentes + $ninos_5_12;
        $diferencia_personas = $nuevo_total_personas - $reserva_actual->total_personas;

        error_log("Diferencia de personas: {$diferencia_personas} (nuevo: {$nuevo_total_personas}, anterior: {$reserva_actual->total_personas})");

        // Iniciar transacción
        $wpdb->query('START TRANSACTION');

        try {
            // 1. Verificar disponibilidad de plazas si se aumenta el número de personas
            if ($diferencia_personas > 0) {
                $plazas_disponibles = $wpdb->get_var($wpdb->prepare(
                    "SELECT plazas_disponibles FROM $table_servicios WHERE id = %d",
                    $reserva_actual->servicio_id
                ));

                error_log("Plazas disponibles en servicio {$reserva_actual->servicio_id}: {$plazas_disponibles}");

                if ($plazas_disponibles < $diferencia_personas) {
                    throw new Exception("No hay suficientes plazas disponibles. Se necesitan {$diferencia_personas} plazas adicionales, pero solo hay {$plazas_disponibles} disponibles.");
                }
            }

            // 2. Actualizar plazas en el servicio
            if ($diferencia_personas != 0) {
                $query_plazas = "UPDATE $table_servicios 
                           SET plazas_disponibles = plazas_disponibles - %d 
                           WHERE id = %d";

                error_log("Query plazas: " . $wpdb->prepare($query_plazas, $diferencia_personas, $reserva_actual->servicio_id));

                $resultado_plazas = $wpdb->query($wpdb->prepare($query_plazas, $diferencia_personas, $reserva_actual->servicio_id));

                if ($resultado_plazas === false) {
                    error_log('Error SQL en actualización de plazas: ' . $wpdb->last_error);
                    throw new Exception('Error actualizando plazas del servicio: ' . $wpdb->last_error);
                }

                error_log("Plazas actualizadas correctamente. Filas afectadas: {$resultado_plazas}");
            }

            // 3. Verificar qué columnas existen en la tabla antes de actualizar
            $columns = $wpdb->get_col("DESCRIBE {$table_reservas}");
            error_log('Columnas disponibles en tabla reservas: ' . print_r($columns, true));

            // 4. Preparar datos de actualización - solo columnas que existen
            $update_data = array(
                'adultos' => $adultos,
                'residentes' => $residentes,
                'ninos_5_12' => $ninos_5_12,
                'ninos_menores' => $ninos_menores,
                'total_personas' => $nuevo_total_personas,
                'precio_base' => $precio_base,
                'descuento_total' => $descuento_total,
                'precio_final' => $precio_final,
                'updated_at' => current_time('mysql')
            );

            // Añadir motivo solo si la columna existe
            if (in_array('motivo_ultima_modificacion', $columns)) {
                $update_data['motivo_ultima_modificacion'] = $motivo_cambio;
            }

            error_log('Datos a actualizar: ' . print_r($update_data, true));
            error_log('WHERE condition: id = ' . $reserva_id);

            // 5. Actualizar datos de la reserva
            $resultado_reserva = $wpdb->update(
                $table_reservas,
                $update_data,
                array('id' => $reserva_id),
                array('%d', '%d', '%d', '%d', '%d', '%f', '%f', '%f', '%s'), // Formatos para los valores
                array('%d') // Formato para WHERE
            );

            error_log('Resultado de wpdb->update: ' . var_export($resultado_reserva, true));

            if ($wpdb->last_error) {
                error_log('Error SQL en actualización de reserva: ' . $wpdb->last_error);
            }

            if ($resultado_reserva === false) {
                throw new Exception('Error actualizando los datos de la reserva: ' . ($wpdb->last_error ?: 'Error desconocido'));
            }

            // 6. Registrar el cambio en un log
            $admin_user = $_SESSION['reservas_user']['username'] ?? 'super_admin';
            error_log("RESERVA EDITADA - ID: {$reserva_id} - Admin: {$admin_user} - Motivo: {$motivo_cambio}");

            // 7. Enviar email de confirmación con los nuevos datos
            $reserva_actualizada = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM $table_reservas WHERE id = %d",
                $reserva_id
            ));

            if ($reserva_actualizada && $reserva_actualizada->email) {
                if (!class_exists('ReservasEmailService')) {
                    require_once RESERVAS_PLUGIN_PATH . 'includes/class-email-service.php';
                }

                $result = ReservasEmailService::send_customer_confirmation((array) $reserva_actualizada);

                if (!$result['success']) {
                    error_log('Error enviando email de confirmación después de editar reserva: ' . $result['message']);
                } else {
                    error_log('Email de confirmación enviado correctamente');
                }
            }

            // Confirmar transacción
            $wpdb->query('COMMIT');
            error_log('Transacción confirmada exitosamente');

            wp_send_json_success('Reserva actualizada correctamente. Se ha enviado una nueva confirmación al cliente.');
        } catch (Exception $e) {
            // Rollback en caso de error
            $wpdb->query('ROLLBACK');
            error_log('Exception en update_reservation_data: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
            wp_send_json_error('Error actualizando la reserva: ' . $e->getMessage());
        }
    }

    /**
     * Obtener informe de reservas por fechas - CON FILTROS MEJORADOS Y AGENCIAS CORREGIDAS
     */
    public function get_reservations_report()
    {
        // ✅ DEBUGGING MEJORADO
        error_log('=== REPORTS AJAX REQUEST START ===');
        header('Content-Type: application/json');

        try {
            // ✅ VERIFICACIÓN SIMPLIFICADA TEMPORAL
            if (!session_id()) {
                session_start();
            }

            if (!isset($_SESSION['reservas_user'])) {
                wp_send_json_error('Sesión expirada. Recarga la página e inicia sesión nuevamente.');
                return;
            }

            $user = $_SESSION['reservas_user'];
            if (!in_array($user['role'], ['super_admin', 'admin'])) {
                wp_send_json_error('Sin permisos');
                return;
            }

            global $wpdb;
            $table_reservas = $wpdb->prefix . 'reservas_reservas';
            $table_agencies = $wpdb->prefix . 'reservas_agencies';
            $table_servicios = $wpdb->prefix . 'reservas_servicios';

            // ✅ NUEVOS PARÁMETROS DE FILTRO INCLUYENDO AGENCIAS Y HORARIOS
            $fecha_inicio = sanitize_text_field($_POST['fecha_inicio'] ?? date('Y-m-d'));
            $fecha_fin = sanitize_text_field($_POST['fecha_fin'] ?? date('Y-m-d'));
            $tipo_fecha = sanitize_text_field($_POST['tipo_fecha'] ?? 'servicio'); // 'servicio' o 'compra'
            $estado_filtro = sanitize_text_field($_POST['estado_filtro'] ?? 'confirmadas'); // 'todas', 'confirmadas', 'canceladas'
            $agency_filter = sanitize_text_field($_POST['agency_filter'] ?? 'todas'); // 'todas', 'sin_agencia', o ID de agencia
            $selected_schedules = $_POST['selected_schedules'] ?? ''; // ✅ NUEVO FILTRO
            $reserva_rapida_filter = sanitize_text_field($_POST['reserva_rapida_filter'] ?? 'todas');


            $page = intval($_POST['page'] ?? 1);
            $per_page = 20;
            $offset = ($page - 1) * $per_page;

            // ✅ FUNCIÓN PARA CONSTRUIR CONDICIONES WHERE REUTILIZABLE
            function build_where_conditions($fecha_inicio, $fecha_fin, $tipo_fecha, $estado_filtro, $agency_filter, $selected_schedules, $reserva_rapida_filter, &$query_params)
            {
                $where_conditions = array();
                $query_params = array();

                // Filtro por tipo de fecha
                if ($tipo_fecha === 'compra') {
                    $where_conditions[] = "DATE(r.created_at) BETWEEN %s AND %s";
                } else {
                    $where_conditions[] = "r.fecha BETWEEN %s AND %s";
                }
                $query_params[] = $fecha_inicio;
                $query_params[] = $fecha_fin;

                // ✅ FILTRO DE ESTADO
                switch ($estado_filtro) {
                    case 'confirmadas':
                        $where_conditions[] = "r.estado = 'confirmada'";
                        break;
                    case 'canceladas':
                        $where_conditions[] = "r.estado = 'cancelada'";
                        break;
                    case 'todas':
                        // No añadir condición, mostrar todas
                        break;
                }

                // ✅ FILTRO POR AGENCIAS
                switch ($agency_filter) {
                    case 'sin_agencia':
                        $where_conditions[] = "r.agency_id IS NULL";
                        break;
                    case 'todas':
                        // No añadir condición, mostrar todas
                        break;
                    default:
                        if (is_numeric($agency_filter) && $agency_filter > 0) {
                            $where_conditions[] = "r.agency_id = %d";
                            $query_params[] = intval($agency_filter);
                        }
                        break;
                }

                switch ($reserva_rapida_filter) {
                    case 'solo_rapidas':
                        $where_conditions[] = "r.es_reserva_rapida = 1";
                        break;
                    case 'sin_rapidas':
                        $where_conditions[] = "r.es_reserva_rapida = 0";
                        break;
                    case 'todas':
                    default:
                        // No añadir condición
                        break;
                }

                // ✅ FILTRO POR HORARIOS SELECCIONADOS
                if (!empty($selected_schedules)) {
                    error_log('=== APLICANDO FILTRO DE HORARIOS ===');
                    error_log('Selected schedules raw: ' . $selected_schedules);

                    $selected_schedules_json = $selected_schedules;
                    if (strpos($selected_schedules_json, '\\') !== false) {
                        $selected_schedules_json = stripslashes($selected_schedules_json);
                    }

                    $selected_schedules_array = json_decode($selected_schedules_json, true);

                    if (is_array($selected_schedules_array) && !empty($selected_schedules_array)) {
                        $schedule_conditions = array();

                        foreach ($selected_schedules_array as $schedule) {
                            if (!empty($schedule['hora'])) {
                                $hora_normalizada = date('H:i:s', strtotime($schedule['hora']));

                                if (
                                    !empty($schedule['hora_vuelta']) &&
                                    $schedule['hora_vuelta'] !== 'null' &&
                                    $schedule['hora_vuelta'] !== '' &&
                                    $schedule['hora_vuelta'] !== '00:00:00'
                                ) {
                                    // Horario con vuelta específica
                                    $vuelta_normalizada = date('H:i:s', strtotime($schedule['hora_vuelta']));
                                    $schedule_conditions[] = "(s.hora = %s AND s.hora_vuelta = %s)";
                                    $query_params[] = $hora_normalizada;
                                    $query_params[] = $vuelta_normalizada;
                                } else {
                                    // Solo horario de ida
                                    $schedule_conditions[] = "(s.hora = %s)";
                                    $query_params[] = $hora_normalizada;
                                }
                            }
                        }

                        if (!empty($schedule_conditions)) {
                            $horarios_where = '(' . implode(' OR ', $schedule_conditions) . ')';
                            $where_conditions[] = $horarios_where;
                        }
                    }
                }

                return $where_conditions;
            }




            // ✅ CONSTRUIR CONDICIONES WHERE PARA LISTADO
            $listado_params = array();
            $where_conditions = build_where_conditions($fecha_inicio, $fecha_fin, $tipo_fecha, $estado_filtro, $agency_filter, $selected_schedules, $reserva_rapida_filter, $listado_params);
            $where_clause = '';
            if (!empty($where_conditions)) {
                $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
            }

            // ✅ QUERY PRINCIPAL PARA LISTADO DE RESERVAS
            $query = "SELECT r.*, s.hora as servicio_hora, s.hora_vuelta as servicio_hora_vuelta, a.agency_name, a.email as agency_email
             FROM $table_reservas r
             INNER JOIN {$wpdb->prefix}reservas_servicios s ON r.servicio_id = s.id
             LEFT JOIN $table_agencies a ON r.agency_id = a.id
             $where_clause
             ORDER BY r.fecha DESC, s.hora DESC
             LIMIT %d OFFSET %d";

            $listado_params[] = $per_page;
            $listado_params[] = $offset;

            $reservas = $wpdb->get_results($wpdb->prepare($query, ...$listado_params));

            if ($wpdb->last_error) {
                error_log('❌ Database error in reports: ' . $wpdb->last_error);
                die(json_encode(['success' => false, 'data' => 'Database error: ' . $wpdb->last_error]));
            }

            // Contar total de reservas con los mismos filtros
            $count_query = "SELECT COUNT(*) FROM $table_reservas r 
                   INNER JOIN {$wpdb->prefix}reservas_servicios s ON r.servicio_id = s.id
                   LEFT JOIN $table_agencies a ON r.agency_id = a.id
                   $where_clause";
            $count_params = array_slice($listado_params, 0, -2); // Quitar LIMIT y OFFSET
            $total_reservas = $wpdb->get_var($wpdb->prepare($count_query, ...$count_params));

            // ✅ ESTADÍSTICAS GENERALES - USAR LA MISMA FUNCIÓN DE CONDICIONES
            $stats_params = array();
            $stats_conditions = build_where_conditions($fecha_inicio, $fecha_fin, $tipo_fecha, $estado_filtro, $agency_filter, $selected_schedules, $reserva_rapida_filter, $stats_params);

            // ✅ ESTADÍSTICAS DE CONTEO (RESPETAN EL FILTRO DE ESTADO)
            $stats_count_where = 'WHERE ' . implode(' AND ', $stats_conditions);

            // ✅ ESTADÍSTICAS DE INGRESOS (SIEMPRE SOLO CONFIRMADAS)
            $stats_revenue_conditions = build_where_conditions($fecha_inicio, $fecha_fin, $tipo_fecha, 'confirmadas', $agency_filter, $selected_schedules, $reserva_rapida_filter, $revenue_params);
            $stats_revenue_where = 'WHERE ' . implode(' AND ', $stats_revenue_conditions);

            $stats_count = $wpdb->get_row($wpdb->prepare(
                "SELECT 
            COUNT(*) as total_reservas,
            SUM(r.adultos) as total_adultos,
            SUM(r.residentes) as total_residentes,
            SUM(r.ninos_5_12) as total_ninos_5_12,
            SUM(r.ninos_menores) as total_ninos_menores,
            SUM(r.total_personas) as total_personas_con_plaza,
            SUM(r.descuento_total) as descuentos_totales
         FROM $table_reservas r
         INNER JOIN {$wpdb->prefix}reservas_servicios s ON r.servicio_id = s.id
         $stats_count_where",
                ...$stats_params
            ));

            $stats_revenue = $wpdb->get_row($wpdb->prepare(
                "SELECT 
            SUM(r.precio_final) as ingresos_totales
         FROM $table_reservas r
         INNER JOIN {$wpdb->prefix}reservas_servicios s ON r.servicio_id = s.id
         $stats_revenue_where",
                ...$revenue_params
            ));

            // Combinar estadísticas
            $stats = (object) array(
                'total_reservas' => $stats_count->total_reservas ?? 0,
                'total_adultos' => $stats_count->total_adultos ?? 0,
                'total_residentes' => $stats_count->total_residentes ?? 0,
                'total_ninos_5_12' => $stats_count->total_ninos_5_12 ?? 0,
                'total_ninos_menores' => $stats_count->total_ninos_menores ?? 0,
                'total_personas_con_plaza' => $stats_count->total_personas_con_plaza ?? 0,
                'descuentos_totales' => $stats_count->descuentos_totales ?? 0,
                'ingresos_totales' => $stats_revenue->ingresos_totales ?? 0
            );

            // ✅ ESTADÍSTICAS POR ESTADO (SOLO SI SE SELECCIONA "TODAS")
            $stats_por_estado = null;
            if ($estado_filtro === 'todas') {
                $estado_params = array();
                $estado_conditions = build_where_conditions($fecha_inicio, $fecha_fin, $tipo_fecha, 'todas', $agency_filter, $selected_schedules, $reserva_rapida_filter, $estado_params);
                $estado_where = 'WHERE ' . implode(' AND ', $estado_conditions);

                $stats_por_estado = $wpdb->get_results($wpdb->prepare(
                    "SELECT 
                    estado,
                    COUNT(*) as total,
                    SUM(CASE WHEN r.estado = 'confirmada' THEN r.precio_final ELSE 0 END) as ingresos
                 FROM $table_reservas r
                 INNER JOIN {$wpdb->prefix}reservas_servicios s ON r.servicio_id = s.id
                 $estado_where
                 GROUP BY r.estado
                 ORDER BY total DESC",
                    ...$estado_params
                ));
            }

            // ✅ ESTADÍSTICAS POR AGENCIAS CORREGIDAS - USAR EXACTAMENTE LOS MISMOS FILTROS
            $stats_por_agencias = null;
            if ($agency_filter === 'todas') {
                error_log('=== CONSTRUYENDO ESTADÍSTICAS DE AGENCIAS ===');

                // ✅ USAR EXACTAMENTE LA MISMA FUNCIÓN PARA OBTENER CONDICIONES
                $agencias_params = array();
                $agencias_conditions = build_where_conditions($fecha_inicio, $fecha_fin, $tipo_fecha, $estado_filtro, 'todas', $selected_schedules, $reserva_rapida_filter, $agencias_params);
                $agencias_where = 'WHERE ' . implode(' AND ', $agencias_conditions);

                error_log('Condiciones para agencias: ' . $agencias_where);
                error_log('Parámetros para agencias: ' . print_r($agencias_params, true));

                // ✅ QUERY SIMPLIFICADA PARA AGENCIAS
                $agency_stats_query = "
                SELECT 
                    r.agency_id,
                    COALESCE(a.agency_name, 'Sin Agencia') as agency_name,
                    COUNT(*) as total_reservas,
                    SUM(r.total_personas) as total_personas,
                    SUM(r.adultos) as total_adultos,
                    SUM(r.residentes) as total_residentes,
                    SUM(r.ninos_5_12) as total_ninos_5_12,
                    SUM(r.ninos_menores) as total_ninos_menores,
                    -- Solo sumar ingresos de reservas confirmadas
                    SUM(CASE WHEN r.estado = 'confirmada' THEN r.precio_final ELSE 0 END) as ingresos_total
                FROM $table_reservas r
                INNER JOIN {$wpdb->prefix}reservas_servicios s ON r.servicio_id = s.id
                LEFT JOIN $table_agencies a ON r.agency_id = a.id
                $agencias_where
                GROUP BY r.agency_id, a.agency_name
                ORDER BY total_reservas DESC
                
            ";

                error_log('=== QUERY DE AGENCIAS ===');
                error_log('Query: ' . $agency_stats_query);

                $stats_por_agencias_raw = $wpdb->get_results($wpdb->prepare($agency_stats_query, ...$agencias_params));

                if ($wpdb->last_error) {
                    error_log('❌ Error en query de agencias: ' . $wpdb->last_error);
                }

                error_log('=== RESULTADOS AGENCIAS ===');
                error_log('Número de agencias encontradas: ' . count($stats_por_agencias_raw));
                foreach ($stats_por_agencias_raw as $stat) {
                    error_log("Agencia: {$stat->agency_name} (ID: {$stat->agency_id}) - Reservas: {$stat->total_reservas}");
                }

                // Formatear resultados
                $stats_por_agencias = array();
                foreach ($stats_por_agencias_raw as $stat) {
                    $stats_por_agencias[] = (object) array(
                        'agency_name' => $stat->agency_name,
                        'agency_id' => $stat->agency_id,
                        'total_reservas' => (int) $stat->total_reservas,
                        'total_personas' => (int) $stat->total_personas,
                        'ingresos_total' => (float) $stat->ingresos_total,
                        'total_adultos' => (int) ($stat->total_adultos ?? 0),
                        'total_residentes' => (int) ($stat->total_residentes ?? 0),
                        'total_ninos_5_12' => (int) ($stat->total_ninos_5_12 ?? 0),
                        'total_ninos_menores' => (int) ($stat->total_ninos_menores ?? 0)
                    );
                }

                error_log('✅ Agencias procesadas para mostrar: ' . count($stats_por_agencias));
            }

            $response_data = array(
                'reservas' => $reservas,
                'stats' => $stats,
                'stats_por_estado' => $stats_por_estado,
                'stats_por_agencias' => $stats_por_agencias,
                'pagination' => array(
                    'current_page' => $page,
                    'total_pages' => ceil($total_reservas / $per_page),
                    'total_items' => $total_reservas,
                    'per_page' => $per_page
                ),
                'filtros' => array(
                    'fecha_inicio' => $fecha_inicio,
                    'fecha_fin' => $fecha_fin,
                    'tipo_fecha' => $tipo_fecha,
                    'estado_filtro' => $estado_filtro,
                    'agency_filter' => $agency_filter,
                    'selected_schedules' => $selected_schedules,
                    'reserva_rapida_filter' => $reserva_rapida_filter // ✅ NUEVO
                ),
            );

            error_log('✅ Reports data loaded successfully with all filters including schedules');
            die(json_encode(['success' => true, 'data' => $response_data]));
        } catch (Exception $e) {
            error_log('❌ REPORTS EXCEPTION: ' . $e->getMessage());
            die(json_encode(['success' => false, 'data' => 'Server error: ' . $e->getMessage()]));
        }
    }


    /**
     * Obtener horarios disponibles para filtro de PDF
     */
    public function get_available_schedules_for_pdf()
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
        $table_reservas = $wpdb->prefix . 'reservas_reservas';
        $table_agencies = $wpdb->prefix . 'reservas_agencies';
        $table_servicios = $wpdb->prefix . 'reservas_servicios';

        // Obtener filtros
        $fecha_inicio = sanitize_text_field($_POST['fecha_inicio'] ?? date('Y-m-d'));
        $fecha_fin = sanitize_text_field($_POST['fecha_fin'] ?? date('Y-m-d'));
        $tipo_fecha = sanitize_text_field($_POST['tipo_fecha'] ?? 'servicio');
        $estado_filtro = sanitize_text_field($_POST['estado_filtro'] ?? 'confirmadas');
        $agency_filter = sanitize_text_field($_POST['agency_filter'] ?? 'todas');
        $reserva_rapida_filter = sanitize_text_field($_POST['reserva_rapida_filter'] ?? 'todas');
        $selected_schedules = $_POST['selected_schedules'] ?? '';



        try {
            // Construir condiciones WHERE para obtener servicios únicos
            $where_conditions = array();
            $query_params = array();

            // Filtro por tipo de fecha
            if ($tipo_fecha === 'compra') {
                $where_conditions[] = "DATE(r.created_at) BETWEEN %s AND %s";
            } else {
                $where_conditions[] = "s.fecha BETWEEN %s AND %s";
            }
            $query_params[] = $fecha_inicio;
            $query_params[] = $fecha_fin;

            // Filtro de estado
            switch ($estado_filtro) {
                case 'confirmadas':
                    $where_conditions[] = "r.estado = 'confirmada'";
                    break;
                case 'canceladas':
                    $where_conditions[] = "r.estado = 'cancelada'";
                    break;
                case 'todas':
                    // No añadir condición
                    break;
            }

            // Filtro por agencias
            switch ($agency_filter) {
                case 'sin_agencia':
                    $where_conditions[] = "r.agency_id IS NULL";
                    break;
                case 'todas':
                    // No añadir condición
                    break;
                default:
                    if (is_numeric($agency_filter) && $agency_filter > 0) {
                        $where_conditions[] = "r.agency_id = %d";
                        $query_params[] = intval($agency_filter);
                    }
                    break;
            }

            $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);

            // Query para obtener horarios únicos con estadísticas
            $schedules_query = "
            SELECT 
                s.hora,
                s.hora_vuelta,
                COUNT(DISTINCT r.id) as count,
                COUNT(DISTINCT s.fecha) as days_count
            FROM $table_reservas r
            INNER JOIN $table_servicios s ON r.servicio_id = s.id
            LEFT JOIN $table_agencies a ON r.agency_id = a.id
            $where_clause
            GROUP BY s.hora, s.hora_vuelta
            ORDER BY s.hora ASC
        ";

            $schedules = $wpdb->get_results($wpdb->prepare($schedules_query, ...$query_params));

            if ($wpdb->last_error) {
                error_log('❌ Database error in get_available_schedules_for_pdf: ' . $wpdb->last_error);
                wp_send_json_error('Error de base de datos: ' . $wpdb->last_error);
                return;
            }

            // Obtener estadísticas generales
            $stats_query = "
            SELECT 
                COUNT(DISTINCT r.id) as total_services,
                COUNT(DISTINCT s.fecha) as days_with_services
            FROM $table_reservas r
            INNER JOIN $table_servicios s ON r.servicio_id = s.id
            LEFT JOIN $table_agencies a ON r.agency_id = a.id
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
                    'agency_filter' => $agency_filter,
                    'selected_schedules' => $selected_schedules,
                    'reserva_rapida_filter' => $reserva_rapida_filter // ✅ AÑADIR
                ),
            );

            wp_send_json_success($response_data);
        } catch (Exception $e) {
            error_log('❌ Exception in get_available_schedules_for_pdf: ' . $e->getMessage());
            wp_send_json_error('Error del servidor: ' . $e->getMessage());
        }
    }


    /**
     * Verificar si una agencia puede cancelar una reserva
     */
    public function check_agency_cancellation_allowed()
    {
        if (!wp_verify_nonce($_POST['nonce'], 'reservas_nonce')) {
            wp_send_json_error('Error de seguridad');
            return;
        }

        if (!session_id()) {
            session_start();
        }

        if (!isset($_SESSION['reservas_user']) || $_SESSION['reservas_user']['role'] !== 'agencia') {
            wp_send_json_error('Sin permisos');
            return;
        }

        global $wpdb;
        $table_reservas = $wpdb->prefix . 'reservas_reservas';
        $table_agencies = $wpdb->prefix . 'reservas_agencies';

        $reserva_id = intval($_POST['reserva_id']);
        $agency_id = $_SESSION['reservas_user']['id'];

        // Obtener datos de la reserva y la agencia
        $query = "SELECT r.*, a.horas_cancelacion_previa, a.agency_name
              FROM $table_reservas r
              INNER JOIN $table_agencies a ON r.agency_id = a.id
              WHERE r.id = %d AND r.agency_id = %d AND r.estado = 'confirmada'";

        $data = $wpdb->get_row($wpdb->prepare($query, $reserva_id, $agency_id));

        if (!$data) {
            wp_send_json_error('Reserva no encontrada, sin permisos o ya cancelada');
            return;
        }

        // Calcular tiempo límite para cancelación
        $horas_limite = intval($data->horas_cancelacion_previa ?? 24);

        // Crear fecha/hora límite para cancelación
        $fecha_hora_servicio = $data->fecha . ' ' . $data->hora;
        $fecha_limite_cancelacion = date('Y-m-d H:i:s', strtotime($fecha_hora_servicio . " -{$horas_limite} hours"));
        $ahora = current_time('mysql');

        // Verificar si aún se puede cancelar
        $puede_cancelar = ($ahora <= $fecha_limite_cancelacion);

        // Calcular horas restantes
        $horas_restantes = 0;
        if ($puede_cancelar) {
            $timestamp_limite = strtotime($fecha_limite_cancelacion);
            $timestamp_ahora = strtotime($ahora);
            $segundos_restantes = $timestamp_limite - $timestamp_ahora;
            $horas_restantes = max(0, $segundos_restantes / 3600);
        }

        if ($puede_cancelar) {
            wp_send_json_success([
                'can_cancel' => true,
                'hours_remaining' => $horas_restantes,
                'hours_limit' => $horas_limite,
                'message' => "Puedes cancelar esta reserva. Tiempo restante: " . number_format($horas_restantes, 1) . " horas."
            ]);
        } else {
            $fecha_servicio_formateada = date('d/m/Y H:i', strtotime($fecha_hora_servicio));
            $fecha_limite_formateada = date('d/m/Y H:i', strtotime($fecha_limite_cancelacion));

            wp_send_json_success([
                'can_cancel' => false,
                'hours_limit' => $horas_limite,
                'message' => "El tiempo límite para cancelar ha expirado.\n\nServicio: {$fecha_servicio_formateada}\nLímite era: {$fecha_limite_formateada}"
            ]);
        }
    }

    /**
     * Procesar cancelación directa de agencia
     */
    public function process_agency_direct_cancellation()
    {
        if (!wp_verify_nonce($_POST['nonce'], 'reservas_nonce')) {
            wp_send_json_error('Error de seguridad');
            return;
        }

        if (!session_id()) {
            session_start();
        }

        if (!isset($_SESSION['reservas_user']) || $_SESSION['reservas_user']['role'] !== 'agencia') {
            wp_send_json_error('Sin permisos');
            return;
        }

        global $wpdb;
        $table_reservas = $wpdb->prefix . 'reservas_reservas';
        $table_agencies = $wpdb->prefix . 'reservas_agencies';
        $table_servicios = $wpdb->prefix . 'reservas_servicios';

        $reserva_id = intval($_POST['reserva_id']);
        $motivo_cancelacion = sanitize_text_field($_POST['motivo_cancelacion'] ?? 'Cancelación por agencia');
        $agency_id = $_SESSION['reservas_user']['id'];

        // VERIFICAR NUEVAMENTE QUE SE PUEDE CANCELAR (por seguridad)
        $query = "SELECT r.*, a.horas_cancelacion_previa, a.agency_name
              FROM $table_reservas r
              INNER JOIN $table_agencies a ON r.agency_id = a.id
              WHERE r.id = %d AND r.agency_id = %d AND r.estado = 'confirmada'";

        $data = $wpdb->get_row($wpdb->prepare($query, $reserva_id, $agency_id));

        if (!$data) {
            wp_send_json_error('Reserva no encontrada, sin permisos o ya cancelada');
            return;
        }

        // Verificar tiempo límite nuevamente
        $horas_limite = intval($data->horas_cancelacion_previa ?? 24);
        $fecha_hora_servicio = $data->fecha . ' ' . $data->hora;
        $fecha_limite_cancelacion = date('Y-m-d H:i:s', strtotime($fecha_hora_servicio . " -{$horas_limite} hours"));
        $ahora = current_time('mysql');

        if ($ahora > $fecha_limite_cancelacion) {
            wp_send_json_error('El tiempo límite para cancelar ha expirado');
            return;
        }

        // Iniciar transacción
        $wpdb->query('START TRANSACTION');

        try {
            // 1. Actualizar estado de la reserva
            $update_reserva = $wpdb->update(
                $table_reservas,
                array(
                    'estado' => 'cancelada',
                    'motivo_cancelacion' => $motivo_cancelacion,
                    'fecha_cancelacion' => current_time('mysql')
                ),
                array('id' => $reserva_id)
            );

            if ($update_reserva === false) {
                throw new Exception('Error actualizando reserva');
            }

            // 2. Liberar las plazas en el servicio
            $update_plazas = $wpdb->query($wpdb->prepare(
                "UPDATE $table_servicios 
             SET plazas_disponibles = plazas_disponibles + %d 
             WHERE id = %d",
                $data->total_personas,
                $data->servicio_id
            ));

            if ($update_plazas === false) {
                throw new Exception('Error liberando plazas');
            }

            // 3. Enviar email de cancelación al cliente
            if (!class_exists('ReservasEmailService')) {
                require_once RESERVAS_PLUGIN_PATH . 'includes/class-email-service.php';
            }

            $reserva_array = (array) $data;
            $reserva_array['motivo_cancelacion'] = $motivo_cancelacion;
            $reserva_array['cancelada_por'] = 'Agencia: ' . $data->agency_name;

            $email_result = ReservasEmailService::send_cancellation_email($reserva_array);


            // Confirmar transacción
            $wpdb->query('COMMIT');

            $message = 'Reserva cancelada correctamente por agencia';
            if ($email_result['success']) {
                $message .= ' y email enviado al cliente';
            } else {
                $message .= ' (email no enviado: ' . $email_result['message'] . ')';
            }

            wp_send_json_success($message);
        } catch (Exception $e) {
            // Rollback en caso de error
            $wpdb->query('ROLLBACK');
            wp_send_json_error('Error cancelando reserva: ' . $e->getMessage());
        }
    }



    /**
     * Generar PDF de ticket para descarga desde reports - FUNCIÓN CORREGIDA
     */
    public function generate_ticket_pdf_from_reports()
    {
        error_log('=== GENERATE_TICKET_PDF_FROM_REPORTS INICIADO ===');

        if (!wp_verify_nonce($_POST['nonce'], 'reservas_nonce')) {
            error_log('❌ Error de nonce en generate_ticket_pdf_from_reports');
            wp_send_json_error('Error de seguridad');
            return;
        }

        if (!session_id()) {
            session_start();
        }

        if (!isset($_SESSION['reservas_user']) || !in_array($_SESSION['reservas_user']['role'], ['super_admin', 'admin'])) {
            error_log('❌ Sin permisos en generate_ticket_pdf_from_reports');
            wp_send_json_error('Sin permisos');
            return;
        }

        $reserva_id = intval($_POST['reserva_id']);

        if (!$reserva_id) {
            error_log('❌ ID de reserva no válido: ' . $reserva_id);
            wp_send_json_error('ID de reserva no válido');
            return;
        }

        error_log('🔍 Procesando reserva ID: ' . $reserva_id);

        try {
            global $wpdb;
            $table_reservas = $wpdb->prefix . 'reservas_reservas';
            $table_servicios = $wpdb->prefix . 'reservas_servicios';

            // Obtener datos completos de la reserva
            $reserva = $wpdb->get_row($wpdb->prepare(
                "SELECT r.*, s.precio_adulto, s.precio_nino, s.precio_residente, s.hora_vuelta 
             FROM $table_reservas r
             LEFT JOIN $table_servicios s ON r.servicio_id = s.id
             WHERE r.id = %d",
                $reserva_id
            ));

            if (!$reserva) {
                error_log('❌ Reserva no encontrada: ' . $reserva_id);
                wp_send_json_error('Reserva no encontrada');
                return;
            }

            error_log('✅ Reserva encontrada: ' . print_r($reserva, true));

            // Preparar datos para el PDF
            $reserva_data = array(
                'localizador' => $reserva->localizador,
                'fecha' => $reserva->fecha,
                'hora' => $reserva->hora,
                'hora_vuelta' => $reserva->hora_vuelta ?? '',
                'nombre' => $reserva->nombre,
                'apellidos' => $reserva->apellidos,
                'email' => $reserva->email,
                'telefono' => $reserva->telefono,
                'adultos' => $reserva->adultos,
                'residentes' => $reserva->residentes,
                'ninos_5_12' => $reserva->ninos_5_12,
                'ninos_menores' => $reserva->ninos_menores,
                'total_personas' => $reserva->total_personas,
                'precio_base' => $reserva->precio_base,
                'descuento_total' => $reserva->descuento_total,
                'precio_final' => $reserva->precio_final,
                'precio_adulto' => $reserva->precio_adulto ?? 10.00,
                'precio_nino' => $reserva->precio_nino ?? 5.00,
                'precio_residente' => $reserva->precio_residente ?? 5.00,
                'created_at' => $reserva->created_at,
                'metodo_pago' => $reserva->metodo_pago ?? 'directo'
            );

            error_log('📋 Datos preparados para PDF: ' . print_r($reserva_data, true));

            // Generar PDF
            if (!class_exists('ReservasPDFGenerator')) {
                require_once RESERVAS_PLUGIN_PATH . 'includes/class-pdf-generator.php';
            }

            $pdf_generator = new ReservasPDFGenerator();
            $pdf_path = $pdf_generator->generate_ticket_pdf($reserva_data);

            if (!$pdf_path || !file_exists($pdf_path)) {
                error_log('❌ PDF no se generó correctamente');
                wp_send_json_error('Error generando el PDF');
                return;
            }

            error_log('✅ PDF generado: ' . $pdf_path);
            error_log('📁 Tamaño del archivo: ' . filesize($pdf_path) . ' bytes');

            // ✅ CREAR URL PÚBLICO CORRECTO - IGUAL QUE EN LAS OTRAS FUNCIONES
            $upload_dir = wp_upload_dir();
            $relative_path = str_replace($upload_dir['basedir'], '', $pdf_path);
            $pdf_url = $upload_dir['baseurl'] . $relative_path;

            error_log('🌐 URL del PDF: ' . $pdf_url);

            // Programar eliminación del archivo después de 1 hora
            wp_schedule_single_event(time() + 3600, 'delete_temp_pdf', array($pdf_path));

            wp_send_json_success(array(
                'pdf_url' => $pdf_url,
                'pdf_path' => $pdf_path,
                'localizador' => $reserva->localizador,
                'filename' => 'billete_' . $reserva->localizador . '.pdf',
                'file_exists' => file_exists($pdf_path),
                'file_size' => filesize($pdf_path)
            ));
        } catch (Exception $e) {
            error_log('❌ Exception en generate_ticket_pdf_from_reports: ' . $e->getMessage());
            error_log('❌ Stack trace: ' . $e->getTraceAsString());
            wp_send_json_error('Error interno generando el PDF: ' . $e->getMessage());
        }
    }

    /**
     * Obtener lista de agencias para el filtro
     */
    public function get_agencies_for_filter()
    {
        error_log('=== GET_AGENCIES_FOR_FILTER INICIADO ===');

        if (!wp_verify_nonce($_POST['nonce'], 'reservas_nonce')) {
            error_log('❌ Error de nonce en get_agencies_for_filter');
            wp_send_json_error('Error de seguridad');
            return;
        }

        if (!session_id()) {
            session_start();
        }

        if (!isset($_SESSION['reservas_user'])) {
            error_log('❌ Sesión no encontrada en get_agencies_for_filter');
            wp_send_json_error('Sesión expirada');
            return;
        }

        $user = $_SESSION['reservas_user'];
        if (!in_array($user['role'], ['super_admin', 'admin'])) {
            error_log('❌ Sin permisos en get_agencies_for_filter');
            wp_send_json_error('Sin permisos');
            return;
        }

        global $wpdb;
        $table_agencies = $wpdb->prefix . 'reservas_agencies';
        $table_reservas = $wpdb->prefix . 'reservas_reservas';

        error_log("🔍 Consultando tabla: $table_agencies");

        // Verificar que la tabla existe
        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_agencies'") == $table_agencies;
        if (!$table_exists) {
            error_log("❌ La tabla $table_agencies no existe");
            wp_send_json_error('Tabla de agencias no encontrada');
            return;
        }

        // ✅ CONSULTA ACTUALIZADA CON INICIAL_LOCALIZADOR
        $agencies = $wpdb->get_results(
            "SELECT id, agency_name, status, inicial_localizador, email, contact_person 
         FROM $table_agencies 
         ORDER BY status ASC, agency_name ASC"
        );

        if ($wpdb->last_error) {
            error_log("❌ Error SQL en agencias: " . $wpdb->last_error);
            wp_send_json_error('Error de base de datos: ' . $wpdb->last_error);
            return;
        }

        error_log("📊 Agencias encontradas: " . count($agencies));

        // Obtener estadísticas de uso de cada agencia
        foreach ($agencies as &$agency) {
            $reservas_count = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM $table_reservas WHERE agency_id = %d",
                $agency->id
            ));
            $agency->reservas_count = intval($reservas_count);

            error_log("📈 Agencia '{$agency->agency_name}' (ID: {$agency->id}): {$agency->reservas_count} reservas");
        }

        error_log("✅ Enviando " . count($agencies) . " agencias al frontend");
        wp_send_json_success($agencies);
    }

public function search_reservations()
{
    // ✅ VERIFICACIÓN SIMPLIFICADA TEMPORAL
    if (!session_id()) {
        session_start();
    }

    if (!isset($_SESSION['reservas_user'])) {
        wp_send_json_error('Sesión expirada. Recarga la página e inicia sesión nuevamente.');
        return;
    }

    $user = $_SESSION['reservas_user'];
    if (!in_array($user['role'], ['super_admin', 'admin'])) {
        wp_send_json_error('Sin permisos');
        return;
    }

    global $wpdb;
    $table_reservas = $wpdb->prefix . 'reservas_reservas';

    $search_type = sanitize_text_field($_POST['search_type']);
    $search_value = sanitize_text_field($_POST['search_value']);
    
    $enable_date_filter = isset($_POST['enable_date_filter']) && $_POST['enable_date_filter'] === '1';
    $fecha_inicio = sanitize_text_field($_POST['fecha_inicio'] ?? '');
    $fecha_fin = sanitize_text_field($_POST['fecha_fin'] ?? '');

    $where_conditions = array();
    $search_params = array();

    // ✅ CONDICIÓN PRINCIPAL (SIEMPRE REQUERIDA)
    switch ($search_type) {
        case 'localizador':
            $where_conditions[] = "r.localizador LIKE %s";
            $search_params[] = '%' . $search_value . '%';
            break;

        case 'email':
            $where_conditions[] = "r.email LIKE %s";
            $search_params[] = '%' . $search_value . '%';
            break;

        case 'telefono':
            $where_conditions[] = "r.telefono LIKE %s";
            $search_params[] = '%' . $search_value . '%';
            break;

        case 'fecha_emision':
            $where_conditions[] = "DATE(r.created_at) = %s";
            $search_params[] = $search_value;
            break;

        case 'fecha_servicio':
            $where_conditions[] = "r.fecha = %s";
            $search_params[] = $search_value;
            break;

        case 'nombre':
            $where_conditions[] = "(r.nombre LIKE %s OR r.apellidos LIKE %s)";
            $search_params[] = '%' . $search_value . '%';
            $search_params[] = '%' . $search_value . '%';
            break;

        case 'localizador':
            $where_conditions[] = "r.localizador LIKE %s";
            $search_params[] = '%' . $search_value . '%';
            break;

        default:
            wp_send_json_error('Tipo de búsqueda no válido');
            return;
    }

    if ($enable_date_filter && !empty($fecha_inicio) && !empty($fecha_fin)) {
        $where_conditions[] = "r.fecha BETWEEN %s AND %s";
        $search_params[] = $fecha_inicio;
        $search_params[] = $fecha_fin;
    }

    $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);

    $query = "SELECT r.*, s.hora as servicio_hora 
              FROM $table_reservas r
              LEFT JOIN {$wpdb->prefix}reservas_servicios s ON r.servicio_id = s.id
              $where_clause
              ORDER BY r.created_at DESC
              LIMIT 100";

    $reservas = $wpdb->get_results($wpdb->prepare($query, ...$search_params));

    wp_send_json_success(array(
        'reservas' => $reservas,
        'search_type' => $search_type,
        'search_value' => $search_value,
        'fecha_inicio' => $fecha_inicio,
        'fecha_fin' => $fecha_fin,
        'enable_date_filter' => $enable_date_filter,
        'total_found' => count($reservas)
    ));
}

    /**
     * Obtener detalles de una reserva específica - CON FECHA DE COMPRA
     */
    public function get_reservation_details()
    {
        // ✅ VERIFICACIÓN SIMPLIFICADA TEMPORAL
        if (!session_id()) {
            session_start();
        }

        if (!isset($_SESSION['reservas_user'])) {
            wp_send_json_error('Sesión expirada. Recarga la página e inicia sesión nuevamente.');
            return;
        }

        $user = $_SESSION['reservas_user'];
        if (!in_array($user['role'], ['super_admin', 'admin'])) {
            wp_send_json_error('Sin permisos');
            return;
        }

        global $wpdb;
        $table_reservas = $wpdb->prefix . 'reservas_reservas';

        $reserva_id = intval($_POST['reserva_id']);

        // ✅ AÑADIR JOIN CON SERVICIOS PARA OBTENER PRECIOS
        $reserva = $wpdb->get_row($wpdb->prepare(
            "SELECT r.*, 
                s.hora as servicio_hora, 
                s.precio_adulto, 
                s.precio_nino, 
                s.precio_residente
         FROM $table_reservas r
         LEFT JOIN {$wpdb->prefix}reservas_servicios s ON r.servicio_id = s.id
         WHERE r.id = %d",
            $reserva_id
        ));

        if (!$reserva) {
            wp_send_json_error('Reserva no encontrada');
        }

        // Decodificar regla de descuento si existe
        if ($reserva->regla_descuento_aplicada) {
            $reserva->regla_descuento_aplicada = json_decode($reserva->regla_descuento_aplicada, true);
        }

        // ✅ AÑADIR INFORMACIÓN ADICIONAL DE FECHAS
        $reserva->fecha_compra_formateada = date('d/m/Y H:i', strtotime($reserva->created_at));
        $reserva->fecha_servicio_formateada = date('d/m/Y', strtotime($reserva->fecha));

        if ($reserva->updated_at && $reserva->updated_at !== $reserva->created_at) {
            $reserva->fecha_actualizacion_formateada = date('d/m/Y H:i', strtotime($reserva->updated_at));
        }

        wp_send_json_success($reserva);
    }

    /**
     * Actualizar email de una reserva
     */
    public function update_reservation_email()
    {
        if (!wp_verify_nonce($_POST['nonce'], 'reservas_nonce')) {
            wp_send_json_error('Error de seguridad');
        }

        if (!session_id()) {
            session_start();
        }

        if (!isset($_SESSION['reservas_user']) || !in_array($_SESSION['reservas_user']['role'], ['super_admin', 'admin'])) {
            wp_send_json_error('Sin permisos');
        }

        global $wpdb;
        $table_reservas = $wpdb->prefix . 'reservas_reservas';

        $reserva_id = intval($_POST['reserva_id']);
        $new_email = sanitize_email($_POST['new_email']);

        if (!is_email($new_email)) {
            wp_send_json_error('Email no válido');
        }

        $result = $wpdb->update(
            $table_reservas,
            array('email' => $new_email),
            array('id' => $reserva_id)
        );

        if ($result !== false) {
            wp_send_json_success('Email actualizado correctamente');
        } else {
            wp_send_json_error('Error actualizando el email: ' . $wpdb->last_error);
        }
    }

    /**
     * ✅ REENVIAR EMAIL DE CONFIRMACIÓN - FUNCIÓN IMPLEMENTADA
     */
    public function resend_confirmation_email()
    {
        if (!wp_verify_nonce($_POST['nonce'], 'reservas_nonce')) {
            wp_send_json_error('Error de seguridad');
        }

        if (!session_id()) {
            session_start();
        }

        if (!isset($_SESSION['reservas_user']) || !in_array($_SESSION['reservas_user']['role'], ['super_admin', 'admin'])) {
            wp_send_json_error('Sin permisos');
        }

        $reserva_id = intval($_POST['reserva_id']);

        // ✅ CARGAR CLASE DE EMAILS
        if (!class_exists('ReservasEmailService')) {
            require_once RESERVAS_PLUGIN_PATH . 'includes/class-email-service.php';
        }

        // ✅ REENVIAR EMAIL USANDO LA CLASE DE EMAILS
        $result = ReservasEmailService::resend_confirmation($reserva_id);

        if ($result['success']) {
            wp_send_json_success($result['message']);
        } else {
            wp_send_json_error($result['message']);
        }
    }

    /**
     * Obtener estadísticas por rango de fechas
     */
    public function get_date_range_stats()
    {
        if (!wp_verify_nonce($_POST['nonce'], 'reservas_nonce')) {
            wp_send_json_error('Error de seguridad');
        }

        if (!session_id()) {
            session_start();
        }

        if (!isset($_SESSION['reservas_user']) || !in_array($_SESSION['reservas_user']['role'], ['super_admin', 'admin'])) {
            wp_send_json_error('Sin permisos');
        }

        global $wpdb;
        $table_reservas = $wpdb->prefix . 'reservas_reservas';

        $range_type = sanitize_text_field($_POST['range_type']);

        $fecha_inicio = '';
        $fecha_fin = date('Y-m-d');

        switch ($range_type) {
            case '7_days':
                $fecha_inicio = date('Y-m-d', strtotime('-7 days'));
                break;
            case '30_days':
                $fecha_inicio = date('Y-m-d', strtotime('-30 days'));
                break;
            case '60_days':
                $fecha_inicio = date('Y-m-d', strtotime('-60 days'));
                break;
            case '90_days':
                $fecha_inicio = date('Y-m-d', strtotime('-90 days'));
                break;
            case 'this_month':
                $fecha_inicio = date('Y-m-01');
                break;
            case 'last_month':
                $fecha_inicio = date('Y-m-01', strtotime('first day of last month'));
                $fecha_fin = date('Y-m-t', strtotime('last day of last month'));
                break;
            case 'this_year':
                $fecha_inicio = date('Y-01-01');
                break;
            case 'custom':
                $fecha_inicio = sanitize_text_field($_POST['fecha_inicio']);
                $fecha_fin = sanitize_text_field($_POST['fecha_fin']);
                break;
            default:
                wp_send_json_error('Rango de fechas no válido');
        }

        $stats = $wpdb->get_row($wpdb->prepare(
            "SELECT 
        COUNT(*) as total_reservas,
        SUM(adultos) as total_adultos,
        SUM(residentes) as total_residentes,
        SUM(ninos_5_12) as total_ninos_5_12,
        SUM(ninos_menores) as total_ninos_menores,
        SUM(total_personas) as total_personas_con_plaza,
        SUM(precio_final) as ingresos_totales,
 SUM(descuento_total) as descuentos_totales,
 AVG(precio_final) as precio_promedio
     FROM $table_reservas 
     WHERE fecha BETWEEN %s AND %s 
     AND estado = 'confirmada'",
            $fecha_inicio,
            $fecha_fin
        ));

        // Obtener reservas por día para gráfico
        $reservas_por_dia = $wpdb->get_results($wpdb->prepare(
            "SELECT 
                fecha,
                COUNT(*) as reservas_dia,
                SUM(total_personas) as personas_dia,
                SUM(precio_final) as ingresos_dia
             FROM $table_reservas 
             WHERE fecha BETWEEN %s AND %s 
             AND estado = 'confirmada'
             GROUP BY fecha
             ORDER BY fecha",
            $fecha_inicio,
            $fecha_fin
        ));

        wp_send_json_success(array(
            'stats' => $stats,
            'reservas_por_dia' => $reservas_por_dia,
            'fecha_inicio' => $fecha_inicio,
            'fecha_fin' => $fecha_fin,
            'range_type' => $range_type
        ));
    }

    /**
     * Método estático para obtener estadísticas rápidas
     */
    public function get_quick_stats()
    {
        if (!wp_verify_nonce($_POST['nonce'], 'reservas_nonce')) {
            wp_send_json_error('Error de seguridad');
        }

        if (!session_id()) {
            session_start();
        }

        if (!isset($_SESSION['reservas_user']) || !in_array($_SESSION['reservas_user']['role'], ['super_admin', 'admin'])) {
            wp_send_json_error('Sin permisos');
        }

        global $wpdb;
        $table_reservas = $wpdb->prefix . 'reservas_reservas';
        $table_servicios = $wpdb->prefix . 'reservas_servicios';

        $today = date('Y-m-d');
        $this_month_start = date('Y-m-01');
        $last_month_start = date('Y-m-01', strtotime('first day of last month'));
        $last_month_end = date('Y-m-t', strtotime('last day of last month'));

        // 1. RESERVAS DE HOY
        $reservas_hoy = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_reservas WHERE fecha = %s AND estado = 'confirmada'",
            $today
        ));

        $ingresos_mes_actual = $wpdb->get_var($wpdb->prepare(
            "SELECT SUM(precio_final) FROM $table_reservas
 WHERE fecha >= %s AND estado = 'confirmada'",
            $this_month_start
        )) ?: 0;

        // 3. INGRESOS DEL MES PASADO (para comparar)
        $ingresos_mes_pasado = $wpdb->get_var($wpdb->prepare(
            "SELECT SUM(precio_final) FROM $table_reservas
 WHERE fecha BETWEEN %s AND %s AND estado = 'confirmada'",
            $last_month_start,
            $last_month_end
        )) ?: 0;

        // 4. CRECIMIENTO PORCENTUAL
        $crecimiento = 0;
        if ($ingresos_mes_pasado > 0) {
            $crecimiento = (($ingresos_mes_actual - $ingresos_mes_pasado) / $ingresos_mes_pasado) * 100;
        } elseif ($ingresos_mes_actual > 0) {
            $crecimiento = 100; // Si mes pasado = 0 y este mes > 0, es 100% crecimiento
        }

        // 5. TOP 3 DÍAS CON MÁS RESERVAS ESTE MES
        $top_dias = $wpdb->get_results($wpdb->prepare(
            "SELECT fecha, COUNT(*) as total_reservas, SUM(total_personas) as total_personas 
         FROM $table_reservas 
         WHERE fecha >= %s AND estado = 'confirmada'
         GROUP BY fecha 
         ORDER BY total_reservas DESC 
         LIMIT 3",
            $this_month_start
        ));

        // 6. OCUPACIÓN PROMEDIO (este mes)
        $ocupacion_data = $wpdb->get_row($wpdb->prepare(
            "SELECT 
            SUM(s.plazas_totales) as plazas_totales,
            SUM(s.plazas_totales - s.plazas_disponibles) as plazas_ocupadas
         FROM $table_servicios s
         WHERE s.fecha >= %s AND s.status = 'active'",
            $this_month_start
        ));

        $ocupacion_porcentaje = 0;
        if ($ocupacion_data && $ocupacion_data->plazas_totales > 0) {
            $ocupacion_porcentaje = ($ocupacion_data->plazas_ocupadas / $ocupacion_data->plazas_totales) * 100;
        }

        // 7. CLIENTE MÁS FRECUENTE (último mes)
        $cliente_frecuente = $wpdb->get_row($wpdb->prepare(
            "SELECT email, CONCAT(nombre, ' ', apellidos) as nombre_completo, COUNT(*) as total_reservas
         FROM $table_reservas 
         WHERE created_at >= %s AND estado = 'confirmada'
         GROUP BY email 
         ORDER BY total_reservas DESC 
         LIMIT 1",
            date('Y-m-d', strtotime('-30 days'))
        ));

        // 8. PRÓXIMOS SERVICIOS CON ALTA OCUPACIÓN (>80%)
        $servicios_alta_ocupacion = $wpdb->get_results($wpdb->prepare(
            "SELECT fecha, hora, plazas_totales, plazas_disponibles,
                ((plazas_totales - plazas_disponibles) / plazas_totales * 100) as ocupacion
         FROM $table_servicios 
         WHERE fecha >= %s AND status = 'active'
         AND ((plazas_totales - plazas_disponibles) / plazas_totales * 100) > 80
         ORDER BY fecha, hora 
         LIMIT 5",
            $today
        ));

        // 9. ESTADÍSTICAS DE TIPOS DE CLIENTE (este mes)
        $tipos_cliente = $wpdb->get_row($wpdb->prepare(
            "SELECT 
            SUM(adultos) as total_adultos,
            SUM(residentes) as total_residentes,
            SUM(ninos_5_12) as total_ninos,
            SUM(ninos_menores) as total_bebes
         FROM $table_reservas 
         WHERE fecha >= %s AND estado = 'confirmada'",
            $this_month_start
        ));

        // PREPARAR RESPUESTA
        $stats = array(
            'hoy' => array(
                'reservas' => intval($reservas_hoy),
                'fecha' => $today
            ),
            'ingresos' => array(
                'mes_actual' => floatval($ingresos_mes_actual),
                'mes_pasado' => floatval($ingresos_mes_pasado),
                'crecimiento' => round($crecimiento, 1),
                'mes_nombre' => date('F Y', strtotime($this_month_start))
            ),
            'top_dias' => $top_dias,
            'ocupacion' => array(
                'porcentaje' => round($ocupacion_porcentaje, 1),
                'plazas_totales' => intval($ocupacion_data->plazas_totales ?? 0),
                'plazas_ocupadas' => intval($ocupacion_data->plazas_ocupadas ?? 0)
            ),
            'cliente_frecuente' => $cliente_frecuente,
            'servicios_alta_ocupacion' => $servicios_alta_ocupacion,
            'tipos_cliente' => $tipos_cliente
        );

        wp_send_json_success($stats);
    }

    // Añadir esta función en la clase ReservasReportsAdmin

    /**
     * Obtener reservas de una agencia específica - CON FILTROS MEJORADOS
     */
    public function get_agency_reservations_report()
    {
        error_log('=== AGENCY RESERVATIONS AJAX REQUEST START ===');
        header('Content-Type: application/json');

        try {
            if (!wp_verify_nonce($_POST['nonce'], 'reservas_nonce')) {
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
                wp_send_json_error('Sin permisos para ver reservas de agencia');
                return;
            }

            $agency_id = $user['id'];

            global $wpdb;
            $table_reservas = $wpdb->prefix . 'reservas_reservas';
            $table_agencies = $wpdb->prefix . 'reservas_agencies';

            // Parámetros de filtro
            $fecha_inicio = sanitize_text_field($_POST['fecha_inicio'] ?? date('Y-m-d'));
            $fecha_fin = sanitize_text_field($_POST['fecha_fin'] ?? date('Y-m-d'));
            $tipo_fecha = sanitize_text_field($_POST['tipo_fecha'] ?? 'servicio');
            $estado_filtro = sanitize_text_field($_POST['estado_filtro'] ?? 'confirmadas');

            $page = intval($_POST['page'] ?? 1);
            $per_page = 20;
            $offset = ($page - 1) * $per_page;

            // Construir condiciones WHERE
            $where_conditions = array();
            $query_params = array();

            // SIEMPRE filtrar por la agencia actual
            $where_conditions[] = "r.agency_id = %d";
            $query_params[] = $agency_id;

            // Filtro por tipo de fecha
            if ($tipo_fecha === 'compra') {
                $where_conditions[] = "DATE(r.created_at) BETWEEN %s AND %s";
            } else {
                $where_conditions[] = "r.fecha BETWEEN %s AND %s";
            }
            $query_params[] = $fecha_inicio;
            $query_params[] = $fecha_fin;

            // Filtro de estado
            switch ($estado_filtro) {
                case 'confirmadas':
                    $where_conditions[] = "r.estado = 'confirmada'";
                    break;
                case 'canceladas':
                    $where_conditions[] = "r.estado = 'cancelada'";
                    break;
                case 'todas':
                    // No añadir condición, mostrar todas
                    break;
            }

            $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);

            // Query principal
            $query = "SELECT r.*, s.hora as servicio_hora, a.agency_name, a.email as agency_email
                 FROM $table_reservas r
                 LEFT JOIN {$wpdb->prefix}reservas_servicios s ON r.servicio_id = s.id
                 LEFT JOIN $table_agencies a ON r.agency_id = a.id
                 $where_clause
                 ORDER BY r.fecha DESC, r.hora DESC
                 LIMIT %d OFFSET %d";

            $query_params[] = $per_page;
            $query_params[] = $offset;

            $reservas = $wpdb->get_results($wpdb->prepare($query, ...$query_params));

            if ($wpdb->last_error) {
                error_log('❌ Database error in agency reports: ' . $wpdb->last_error);
                die(json_encode(['success' => false, 'data' => 'Database error: ' . $wpdb->last_error]));
            }

            // Contar total de reservas con los mismos filtros
            $count_query = "SELECT COUNT(*) FROM $table_reservas r 
                       LEFT JOIN $table_agencies a ON r.agency_id = a.id
                       $where_clause";
            $count_params = array_slice($query_params, 0, -2); // Quitar LIMIT y OFFSET
            $total_reservas = $wpdb->get_var($wpdb->prepare($count_query, ...$count_params));

            // Estadísticas según el filtro aplicado
            $stats_where_conditions = array();
            $stats_params = array();

            // SIEMPRE filtrar por la agencia actual en estadísticas
            $stats_where_conditions[] = "r.agency_id = %d";
            $stats_params[] = $agency_id;

            // Aplicar filtros de fecha y estado
            if ($tipo_fecha === 'compra') {
                $stats_where_conditions[] = "DATE(r.created_at) BETWEEN %s AND %s";
            } else {
                $stats_where_conditions[] = "r.fecha BETWEEN %s AND %s";
            }
            $stats_params[] = $fecha_inicio;
            $stats_params[] = $fecha_fin;

            switch ($estado_filtro) {
                case 'confirmadas':
                    $stats_where_conditions[] = "r.estado = 'confirmada'";
                    break;
                case 'canceladas':
                    $stats_where_conditions[] = "r.estado = 'cancelada'";
                    break;
                case 'todas':
                    // No añadir condición para estadísticas generales
                    break;
            }

            $stats_where_clause = 'WHERE ' . implode(' AND ', $stats_where_conditions);

            $stats = $wpdb->get_row($wpdb->prepare(
                "SELECT 
                COUNT(*) as total_reservas,
                SUM(adultos) as total_adultos,
                SUM(residentes) as total_residentes,
                SUM(ninos_5_12) as total_ninos_5_12,
                SUM(ninos_menores) as total_ninos_menores,
                SUM(total_personas) as total_personas_con_plaza,
                SUM(precio_final) as ingresos_totales,
                SUM(descuento_total) as descuentos_totales
             FROM $table_reservas r
             $stats_where_clause",
                ...$stats_params
            ));

            // Estadísticas por estado (solo si es "todas")
            $stats_por_estado = null;
            if ($estado_filtro === 'todas') {
                $estado_where_conditions = array();
                $estado_params = array();

                $estado_where_conditions[] = "r.agency_id = %d";
                $estado_params[] = $agency_id;

                if ($tipo_fecha === 'compra') {
                    $estado_where_conditions[] = "DATE(r.created_at) BETWEEN %s AND %s";
                } else {
                    $estado_where_conditions[] = "r.fecha BETWEEN %s AND %s";
                }
                $estado_params[] = $fecha_inicio;
                $estado_params[] = $fecha_fin;

                $estado_where_clause = 'WHERE ' . implode(' AND ', $estado_where_conditions);

                $stats_por_estado = $wpdb->get_results($wpdb->prepare(
                    "SELECT 
                        estado,
                        COUNT(*) as total,
                        SUM(CASE WHEN r.estado = 'confirmada' THEN r.precio_final ELSE 0 END) as ingresos
                    FROM $table_reservas r
                    INNER JOIN {$wpdb->prefix}reservas_servicios s ON r.servicio_id = s.id
                    $estado_where_clause
                    GROUP BY r.estado
                    ORDER BY total DESC",
                    ...$estado_params
                ));
            }

            $response_data = array(
                'reservas' => $reservas,
                'stats' => $stats,
                'stats_por_estado' => $stats_por_estado,
                'stats_por_agencias' => null, // No aplica para agencias
                'pagination' => array(
                    'current_page' => $page,
                    'total_pages' => ceil($total_reservas / $per_page),
                    'total_items' => $total_reservas,
                    'per_page' => $per_page
                ),
                'filtros' => array(
                    'fecha_inicio' => $fecha_inicio,
                    'fecha_fin' => $fecha_fin,
                    'tipo_fecha' => $tipo_fecha,
                    'estado_filtro' => $estado_filtro,
                    'agency_filter' => $agency_id
                )
            );

            error_log('✅ Agency reports data loaded successfully');
            die(json_encode(['success' => true, 'data' => $response_data]));
        } catch (Exception $e) {
            error_log('❌ AGENCY REPORTS EXCEPTION: ' . $e->getMessage());
            die(json_encode(['success' => false, 'data' => 'Server error: ' . $e->getMessage()]));
        }
    }

    public function cancel_reservation()
    {
        if (!wp_verify_nonce($_POST['nonce'], 'reservas_nonce')) {
            wp_send_json_error('Error de seguridad');
        }

        if (!session_id()) {
            session_start();
        }

        if (!isset($_SESSION['reservas_user']) || !in_array($_SESSION['reservas_user']['role'], ['super_admin', 'admin'])) {
            wp_send_json_error('Sin permisos');
        }

        global $wpdb;
        $table_reservas = $wpdb->prefix . 'reservas_reservas';
        $table_servicios = $wpdb->prefix . 'reservas_servicios';

        $reserva_id = intval($_POST['reserva_id']);
        $motivo_cancelacion = sanitize_text_field($_POST['motivo_cancelacion'] ?? 'Cancelación administrativa');

        // Obtener datos de la reserva
        $reserva = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_reservas WHERE id = %d AND estado != 'cancelada'",
            $reserva_id
        ));

        if (!$reserva) {
            wp_send_json_error('Reserva no encontrada o ya cancelada');
        }

        // Iniciar transacción
        $wpdb->query('START TRANSACTION');

        try {
            // 1. Actualizar estado de la reserva
            $update_reserva = $wpdb->update(
                $table_reservas,
                array(
                    'estado' => 'cancelada',
                    'motivo_cancelacion' => $motivo_cancelacion,
                    'fecha_cancelacion' => current_time('mysql')
                ),
                array('id' => $reserva_id)
            );

            if ($update_reserva === false) {
                throw new Exception('Error actualizando reserva');
            }

            // 2. Liberar las plazas en el servicio
            $update_plazas = $wpdb->query($wpdb->prepare(
                "UPDATE $table_servicios 
             SET plazas_disponibles = plazas_disponibles + %d 
             WHERE id = %d",
                $reserva->total_personas,
                $reserva->servicio_id
            ));

            if ($update_plazas === false) {
                throw new Exception('Error liberando plazas');
            }

            // 3. Enviar email de cancelación al cliente
            if (!class_exists('ReservasEmailService')) {
                require_once RESERVAS_PLUGIN_PATH . 'includes/class-email-service.php';
            }

            $reserva_array = (array) $reserva;
            $reserva_array['motivo_cancelacion'] = $motivo_cancelacion;

            // ✅ LLAMAR CORRECTAMENTE A LA CLASE DE EMAILS
            $email_result = ReservasEmailService::send_cancellation_email($reserva_array);

            // Confirmar transacción
            $wpdb->query('COMMIT');

            $message = 'Reserva cancelada correctamente';
            if ($email_result['success']) {
                $message .= ' y email enviado al cliente';
            } else {
                $message .= ' (email no enviado: ' . $email_result['message'] . ')';
            }

            wp_send_json_success($message);
        } catch (Exception $e) {
            // Rollback en caso de error
            $wpdb->query('ROLLBACK');
            wp_send_json_error('Error cancelando reserva: ' . $e->getMessage());
        }
    }

    public function get_available_services_for_edit()
    {
        if (!wp_verify_nonce($_POST['nonce'], 'reservas_nonce')) {
            wp_send_json_error('Error de seguridad');
        }

        if (!session_id()) {
            session_start();
        }

        if (!isset($_SESSION['reservas_user']) || !in_array($_SESSION['reservas_user']['role'], ['super_admin', 'admin'])) {
            wp_send_json_error('Sin permisos');
        }

        global $wpdb;
        $table_servicios = $wpdb->prefix . 'reservas_servicios';

        $month = intval($_POST['month']);
        $year = intval($_POST['year']);
        $current_reservation_id = intval($_POST['current_reservation_id']);

        // Obtener datos de la reserva actual para excluir esas plazas del cálculo
        $table_reservas = $wpdb->prefix . 'reservas_reservas';
        $current_reservation = $wpdb->get_row($wpdb->prepare(
            "SELECT total_personas FROM $table_reservas WHERE id = %d",
            $current_reservation_id
        ));

        if (!$current_reservation) {
            wp_send_json_error('Reserva no encontrada');
        }

        $personas_actuales = $current_reservation->total_personas;

        // Calcular fecha mínima (hoy + días de anticipación)
        if (!class_exists('ReservasConfigurationAdmin')) {
            require_once RESERVAS_PLUGIN_PATH . 'includes/class-configuration-admin.php';
        }

        $dias_anticipacion = ReservasConfigurationAdmin::get_config('dias_anticipacion_minima', '1');
        $fecha_minima = date('Y-m-d', strtotime("+{$dias_anticipacion} days"));

        // Obtener servicios del mes que tengan plazas suficientes
        $servicios = $wpdb->get_results($wpdb->prepare(
            "SELECT id, fecha, hora, hora_vuelta, plazas_disponibles, precio_adulto, precio_nino, precio_residente,
                tiene_descuento, porcentaje_descuento, descuento_tipo, descuento_minimo_personas
         FROM $table_servicios 
         WHERE YEAR(fecha) = %d 
         AND MONTH(fecha) = %d 
         AND fecha >= %s
         AND status = 'active'
         AND enabled = 1
         AND (plazas_disponibles + %d) >= %d
         ORDER BY fecha, hora",
            $year,
            $month,
            $fecha_minima,
            $personas_actuales, // Sumar las plazas de la reserva actual
            $personas_actuales  // Para verificar que hay suficientes plazas
        ));

        // Agrupar servicios por fecha
        $servicios_por_fecha = array();
        foreach ($servicios as $servicio) {
            $fecha = $servicio->fecha;
            if (!isset($servicios_por_fecha[$fecha])) {
                $servicios_por_fecha[$fecha] = array();
            }
            $servicios_por_fecha[$fecha][] = $servicio;
        }

        wp_send_json_success($servicios_por_fecha);
    }

    /**
     * Actualizar servicio de una reserva
     */
    public function update_reservation_service()
    {
        if (!wp_verify_nonce($_POST['nonce'], 'reservas_nonce')) {
            wp_send_json_error('Error de seguridad');
        }

        if (!session_id()) {
            session_start();
        }

        if (!isset($_SESSION['reservas_user']) || !in_array($_SESSION['reservas_user']['role'], ['super_admin', 'admin'])) {
            wp_send_json_error('Sin permisos');
        }

        global $wpdb;
        $table_reservas = $wpdb->prefix . 'reservas_reservas';
        $table_servicios = $wpdb->prefix . 'reservas_servicios';

        $reserva_id = intval($_POST['reserva_id']);
        $nuevo_servicio_id = intval($_POST['nuevo_servicio_id']);

        // Obtener datos de la reserva actual
        $reserva_actual = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_reservas WHERE id = %d",
            $reserva_id
        ));

        if (!$reserva_actual) {
            wp_send_json_error('Reserva no encontrada');
        }

        // Obtener datos del nuevo servicio
        $nuevo_servicio = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_servicios WHERE id = %d AND status = 'active' AND enabled = 1",
            $nuevo_servicio_id
        ));

        if (!$nuevo_servicio) {
            wp_send_json_error('Servicio no encontrado o no disponible');
        }

        // Verificar que el nuevo servicio tiene suficientes plazas
        if ($nuevo_servicio->plazas_disponibles < $reserva_actual->total_personas) {
            wp_send_json_error('El servicio seleccionado no tiene suficientes plazas disponibles');
        }

        // Iniciar transacción
        $wpdb->query('START TRANSACTION');

        try {
            // 1. Liberar plazas del servicio anterior (si es diferente)
            if ($reserva_actual->servicio_id != $nuevo_servicio_id) {
                $wpdb->query($wpdb->prepare(
                    "UPDATE $table_servicios 
                 SET plazas_disponibles = plazas_disponibles + %d 
                 WHERE id = %d",
                    $reserva_actual->total_personas,
                    $reserva_actual->servicio_id
                ));

                // 2. Ocupar plazas en el nuevo servicio
                $wpdb->query($wpdb->prepare(
                    "UPDATE $table_servicios 
                 SET plazas_disponibles = plazas_disponibles - %d 
                 WHERE id = %d",
                    $reserva_actual->total_personas,
                    $nuevo_servicio_id
                ));
            }

            // 3. Actualizar la reserva
            $result = $wpdb->update(
                $table_reservas,
                array(
                    'servicio_id' => $nuevo_servicio_id,
                    'fecha' => $nuevo_servicio->fecha,
                    'hora' => $nuevo_servicio->hora,
                    'hora_vuelta' => $nuevo_servicio->hora_vuelta,
                    'updated_at' => current_time('mysql')
                ),
                array('id' => $reserva_id)
            );

            if ($result === false) {
                throw new Exception('Error actualizando la reserva');
            }

            // 4. Enviar email de confirmación con los nuevos datos
            $this->send_update_confirmation_email($reserva_id);

            // Confirmar transacción
            $wpdb->query('COMMIT');

            wp_send_json_success('Reserva actualizada correctamente. Se ha enviado un email de confirmación al cliente.');
        } catch (Exception $e) {
            // Rollback en caso de error
            $wpdb->query('ROLLBACK');
            wp_send_json_error('Error actualizando la reserva: ' . $e->getMessage());
        }
    }



    /**
     * Enviar email de confirmación después de actualizar reserva
     */
    private function send_update_confirmation_email($reserva_id)
    {
        global $wpdb;
        $table_reservas = $wpdb->prefix . 'reservas_reservas';

        $reserva = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_reservas WHERE id = %d",
            $reserva_id
        ));

        if (!$reserva) {
            error_log('No se encontró la reserva para enviar email de actualización');
            return;
        }

        // Cargar clase de emails
        if (!class_exists('ReservasEmailService')) {
            require_once RESERVAS_PLUGIN_PATH . 'includes/class-email-service.php';
        }

        // Convertir a array y enviar usando la misma función de confirmación
        $reserva_array = (array) $reserva;

        $result = ReservasEmailService::send_customer_confirmation($reserva_array);

        if ($result['success']) {
            error_log('✅ Email de actualización enviado al cliente: ' . $reserva->email);
        } else {
            error_log('❌ Error enviando email de actualización: ' . $result['message']);
        }
    }


    // Añadir estos métodos en el constructor de ReservasReportsAdmin:



    // Y añadir estos métodos a la clase:

    /**
     * Buscar reservas de agencia por diferentes criterios
     */
    public function search_agency_reservations()
    {
        if (!session_id()) {
            session_start();
        }

        if (!isset($_SESSION['reservas_user'])) {
            wp_send_json_error('Sesión expirada. Recarga la página e inicia sesión nuevamente.');
            return;
        }

        $user = $_SESSION['reservas_user'];
        if ($user['role'] !== 'agencia') {
            wp_send_json_error('Sin permisos');
            return;
        }

        global $wpdb;
        $table_reservas = $wpdb->prefix . 'reservas_reservas';

        $agency_id = $user['id'];
        $search_type = sanitize_text_field($_POST['search_type']);
        $search_value = sanitize_text_field($_POST['search_value']);

        $where_clause = 'WHERE r.agency_id = %d';
        $search_params = array($agency_id);

        switch ($search_type) {
            case 'localizador':
                $where_clause .= " AND r.localizador LIKE %s";
                $search_params[] = '%' . $search_value . '%';
                break;

            case 'email':
                $where_clause .= " AND r.email LIKE %s";
                $search_params[] = '%' . $search_value . '%';
                break;

            case 'telefono':
                $where_clause .= " AND r.telefono LIKE %s";
                $search_params[] = '%' . $search_value . '%';
                break;

            case 'fecha_emision':
                $where_clause .= " AND DATE(r.created_at) = %s";
                $search_params[] = $search_value;
                break;

            case 'fecha_servicio':
                $where_clause .= " AND r.fecha = %s";
                $search_params[] = $search_value;
                break;

            case 'nombre':
                $where_clause .= " AND (r.nombre LIKE %s OR r.apellidos LIKE %s)";
                $search_params[] = '%' . $search_value . '%';
                $search_params[] = '%' . $search_value . '%';
                break;

            default:
                wp_send_json_error('Tipo de búsqueda no válido');
        }

        $query = "SELECT r.*, s.hora as servicio_hora 
              FROM $table_reservas r
              LEFT JOIN {$wpdb->prefix}reservas_servicios s ON r.servicio_id = s.id
              $where_clause
              ORDER BY r.created_at DESC
              LIMIT 50";

        $reservas = $wpdb->get_results($wpdb->prepare($query, ...$search_params));

        wp_send_json_success(array(
            'reservas' => $reservas,
            'search_type' => $search_type,
            'search_value' => $search_value,
            'total_found' => count($reservas)
        ));
    }

    /**
     * Obtener estadísticas por rango de fechas para agencia
     */
    public function get_agency_date_range_stats()
    {
        if (!wp_verify_nonce($_POST['nonce'], 'reservas_nonce')) {
            wp_send_json_error('Error de seguridad');
        }

        if (!session_id()) {
            session_start();
        }

        if (!isset($_SESSION['reservas_user']) || $_SESSION['reservas_user']['role'] !== 'agencia') {
            wp_send_json_error('Sin permisos');
        }

        global $wpdb;
        $table_reservas = $wpdb->prefix . 'reservas_reservas';

        $agency_id = $_SESSION['reservas_user']['id'];
        $range_type = sanitize_text_field($_POST['range_type']);

        $fecha_inicio = '';
        $fecha_fin = date('Y-m-d');

        switch ($range_type) {
            case '7_days':
                $fecha_inicio = date('Y-m-d', strtotime('-7 days'));
                break;
            case '30_days':
                $fecha_inicio = date('Y-m-d', strtotime('-30 days'));
                break;
            case '60_days':
                $fecha_inicio = date('Y-m-d', strtotime('-60 days'));
                break;
            case 'this_month':
                $fecha_inicio = date('Y-m-01');
                break;
            case 'last_month':
                $fecha_inicio = date('Y-m-01', strtotime('first day of last month'));
                $fecha_fin = date('Y-m-t', strtotime('last day of last month'));
                break;
            case 'this_year':
                $fecha_inicio = date('Y-01-01');
                break;
            case 'custom':
                $fecha_inicio = sanitize_text_field($_POST['fecha_inicio']);
                $fecha_fin = sanitize_text_field($_POST['fecha_fin']);
                break;
            default:
                wp_send_json_error('Rango de fechas no válido');
        }

        // Obtener estadísticas del período para esta agencia
        $stats = $wpdb->get_row($wpdb->prepare(
            "SELECT 
            COUNT(*) as total_reservas,
            SUM(adultos) as total_adultos,
            SUM(residentes) as total_residentes,
            SUM(ninos_5_12) as total_ninos_5_12,
            SUM(ninos_menores) as total_ninos_menores,
            SUM(total_personas) as total_personas_con_plaza,
            SUM(precio_final) as ingresos_totales,
            SUM(descuento_total) as descuentos_totales,
            AVG(precio_final) as precio_promedio
         FROM $table_reservas 
         WHERE agency_id = %d
         AND fecha BETWEEN %s AND %s 
         AND estado = 'confirmada'",
            $agency_id,
            $fecha_inicio,
            $fecha_fin
        ));

        // Obtener reservas por día para gráfico
        $reservas_por_dia = $wpdb->get_results($wpdb->prepare(
            "SELECT 
            fecha,
            COUNT(*) as reservas_dia,
            SUM(total_personas) as personas_dia,
            SUM(precio_final) as ingresos_dia
         FROM $table_reservas 
         WHERE agency_id = %d
         AND fecha BETWEEN %s AND %s 
         AND estado = 'confirmada'
         GROUP BY fecha
         ORDER BY fecha",
            $agency_id,
            $fecha_inicio,
            $fecha_fin
        ));

        wp_send_json_success(array(
            'stats' => $stats,
            'reservas_por_dia' => $reservas_por_dia,
            'fecha_inicio' => $fecha_inicio,
            'fecha_fin' => $fecha_fin,
            'range_type' => $range_type
        ));
    }

    /**
     * Obtener estadísticas rápidas para agencia
     */
    public function get_agency_quick_stats()
    {
        if (!wp_verify_nonce($_POST['nonce'], 'reservas_nonce')) {
            wp_send_json_error('Error de seguridad');
        }

        if (!session_id()) {
            session_start();
        }

        if (!isset($_SESSION['reservas_user']) || $_SESSION['reservas_user']['role'] !== 'agencia') {
            wp_send_json_error('Sin permisos');
        }

        global $wpdb;
        $table_reservas = $wpdb->prefix . 'reservas_reservas';

        $agency_id = $_SESSION['reservas_user']['id'];
        $today = date('Y-m-d');
        $this_month_start = date('Y-m-01');
        $last_month_start = date('Y-m-01', strtotime('first day of last month'));
        $last_month_end = date('Y-m-t', strtotime('last day of last month'));

        // 1. RESERVAS DE HOY
        $reservas_hoy = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_reservas 
        WHERE agency_id = %d AND fecha = %s AND estado = 'confirmada'",
            $agency_id,
            $today
        ));

        // 2. INGRESOS DEL MES ACTUAL
        $ingresos_mes_actual = $wpdb->get_var($wpdb->prepare(
            "SELECT SUM(precio_final) FROM $table_reservas 
        WHERE agency_id = %d AND fecha >= %s AND estado = 'confirmada'",
            $agency_id,
            $this_month_start
        )) ?: 0;

        // 3. INGRESOS DEL MES PASADO (para comparar)
        $ingresos_mes_pasado = $wpdb->get_var($wpdb->prepare(
            "SELECT SUM(precio_final) FROM $table_reservas 
        WHERE agency_id = %d AND fecha BETWEEN %s AND %s AND estado = 'confirmada'",
            $agency_id,
            $last_month_start,
            $last_month_end
        )) ?: 0;

        // 4. CRECIMIENTO PORCENTUAL
        $crecimiento = 0;
        if ($ingresos_mes_pasado > 0) {
            $crecimiento = (($ingresos_mes_actual - $ingresos_mes_pasado) / $ingresos_mes_pasado) * 100;
        } elseif ($ingresos_mes_actual > 0) {
            $crecimiento = 100;
        }

        // 5. TOP 3 DÍAS CON MÁS RESERVAS ESTE MES
        $top_dias = $wpdb->get_results($wpdb->prepare(
            "SELECT fecha, COUNT(*) as total_reservas, SUM(total_personas) as total_personas 
        FROM $table_reservas 
        WHERE agency_id = %d AND fecha >= %s AND estado = 'confirmada'
        GROUP BY fecha 
        ORDER BY total_reservas DESC 
        LIMIT 3",
            $agency_id,
            $this_month_start
        ));

        // 6. ESTADÍSTICAS DE TIPOS DE CLIENTE (este mes)
        $tipos_cliente = $wpdb->get_row($wpdb->prepare(
            "SELECT 
           SUM(adultos) as total_adultos,
           SUM(residentes) as total_residentes,
           SUM(ninos_5_12) as total_ninos,
           SUM(ninos_menores) as total_bebes,
           SUM(total_personas) as total_personas
        FROM $table_reservas 
        WHERE agency_id = %d AND fecha >= %s AND estado = 'confirmada'",
            $agency_id,
            $this_month_start
        ));

        // PREPARAR RESPUESTA
        $stats = array(
            'hoy' => array(
                'reservas' => intval($reservas_hoy),
                'fecha' => $today
            ),
            'ingresos' => array(
                'mes_actual' => floatval($ingresos_mes_actual),
                'mes_pasado' => floatval($ingresos_mes_pasado),
                'crecimiento' => round($crecimiento, 1),
                'mes_nombre' => date('F Y', strtotime($this_month_start))
            ),
            'top_dias' => $top_dias,
            'tipos_cliente' => $tipos_cliente
        );

        wp_send_json_success($stats);
    }

    /**
     * Obtener detalles de una reserva específica para agencias
     */
    public function get_agency_reservation_details()
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
        if ($user['role'] !== 'agencia') {
            wp_send_json_error('Sin permisos');
            return;
        }

        global $wpdb;
        $table_reservas = $wpdb->prefix . 'reservas_reservas';

        $reserva_id = intval($_POST['reserva_id']);
        $agency_id = $user['id'];

        $reserva = $wpdb->get_row($wpdb->prepare(
            "SELECT r.*, s.hora as servicio_hora, s.precio_adulto, s.precio_nino, s.precio_residente
         FROM $table_reservas r
         LEFT JOIN {$wpdb->prefix}reservas_servicios s ON r.servicio_id = s.id
         WHERE r.id = %d AND r.agency_id = %d",
            $reserva_id,
            $agency_id
        ));

        if (!$reserva) {
            wp_send_json_error('Reserva no encontrada o sin permisos');
        }

        // Decodificar regla de descuento si existe
        if ($reserva->regla_descuento_aplicada) {
            $reserva->regla_descuento_aplicada = json_decode($reserva->regla_descuento_aplicada, true);
        }

        wp_send_json_success($reserva);
    }

    /**
     * Generar PDF de informe de reservas - NUEVO
     */
    public function generate_reservations_pdf_report()
    {
        if (!wp_verify_nonce($_POST['nonce'], 'reservas_nonce')) {
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
        if (!in_array($user['role'], ['super_admin', 'admin'])) {
            wp_send_json_error('Sin permisos');
            return;
        }

        try {
            // ✅ LOGS DETALLADOS PARA DEBUG
            error_log('=== GENERANDO PDF CON FILTROS ===');
            error_log('POST data: ' . print_r($_POST, true));

            // Obtener filtros aplicados
            $fecha_inicio = sanitize_text_field($_POST['fecha_inicio'] ?? date('Y-m-d'));
            $fecha_fin = sanitize_text_field($_POST['fecha_fin'] ?? date('Y-m-d'));
            $tipo_fecha = sanitize_text_field($_POST['tipo_fecha'] ?? 'servicio');
            $estado_filtro = sanitize_text_field($_POST['estado_filtro'] ?? 'confirmadas');
            $agency_filter = sanitize_text_field($_POST['agency_filter'] ?? 'todas');
            $selected_schedules = $_POST['selected_schedules'] ?? '';

            error_log('Selected schedules recibido: ' . $selected_schedules);

            // Cargar clase generadora de PDF
            if (!class_exists('ReservasReportPDFGenerator')) {
                require_once RESERVAS_PLUGIN_PATH . 'includes/class-report-pdf-generator.php';
            }

            $pdf_generator = new ReservasReportPDFGenerator();

            $filtros = array(
                'fecha_inicio' => $fecha_inicio,
                'fecha_fin' => $fecha_fin,
                'tipo_fecha' => $tipo_fecha,
                'estado_filtro' => $estado_filtro,
                'agency_filter' => $agency_filter,
                'selected_schedules' => $selected_schedules // ✅ PASAR TAL COMO SE RECIBE
            );

            error_log('Filtros que se envían al PDF: ' . print_r($filtros, true));

            $pdf_path = $pdf_generator->generate_report_pdf($filtros);

            if (!$pdf_path || !file_exists($pdf_path)) {
                wp_send_json_error('Error generando el PDF');
                return;
            }

            // Crear URL público para el PDF
            $upload_dir = wp_upload_dir();
            $pdf_url = str_replace($upload_dir['path'], $upload_dir['url'], $pdf_path);

            // Programar eliminación del archivo después de 2 horas
            wp_schedule_single_event(time() + 7200, 'delete_temp_pdf', array($pdf_path));

            $filename = 'informe_reservas_' . $fecha_inicio . '_' . $fecha_fin . '.pdf';

            wp_send_json_success(array(
                'pdf_url' => $pdf_url,
                'filename' => $filename,
                'filtros_aplicados' => $filtros
            ));
        } catch (Exception $e) {
            error_log('Error generando PDF de informe: ' . $e->getMessage());
            wp_send_json_error('Error interno generando el PDF: ' . $e->getMessage());
        }
    }

    /**
     * Generar PDF de ticket para agencias - FUNCIÓN CORREGIDA
     */
    public function generate_agency_ticket_pdf()
    {
        error_log('=== GENERATE_AGENCY_TICKET_PDF INICIADO ===');

        if (!wp_verify_nonce($_POST['nonce'], 'reservas_nonce')) {
            error_log('❌ Error de nonce en generate_agency_ticket_pdf');
            wp_send_json_error('Error de seguridad');
            return;
        }

        if (!session_id()) {
            session_start();
        }

        if (!isset($_SESSION['reservas_user']) || $_SESSION['reservas_user']['role'] !== 'agencia') {
            error_log('❌ Sin permisos en generate_agency_ticket_pdf');
            wp_send_json_error('Sin permisos');
            return;
        }

        $reserva_id = intval($_POST['reserva_id']);
        $agency_id = $_SESSION['reservas_user']['id'];

        if (!$reserva_id) {
            error_log('❌ ID de reserva no válido: ' . $reserva_id);
            wp_send_json_error('ID de reserva no válido');
            return;
        }

        error_log('🔍 Procesando reserva ID: ' . $reserva_id . ' para agencia ID: ' . $agency_id);

        try {
            global $wpdb;
            $table_reservas = $wpdb->prefix . 'reservas_reservas';
            $table_servicios = $wpdb->prefix . 'reservas_servicios';

            // Verificar que la reserva pertenece a la agencia
            $reserva = $wpdb->get_row($wpdb->prepare(
                "SELECT r.*, s.precio_adulto, s.precio_nino, s.precio_residente, s.hora_vuelta 
         FROM $table_reservas r
         LEFT JOIN $table_servicios s ON r.servicio_id = s.id
         WHERE r.id = %d AND r.agency_id = %d",
                $reserva_id,
                $agency_id
            ));

            if (!$reserva) {
                error_log('❌ Reserva no encontrada o sin permisos: reserva_id=' . $reserva_id . ', agency_id=' . $agency_id);
                wp_send_json_error('Reserva no encontrada o sin permisos');
                return;
            }

            error_log('✅ Reserva encontrada para agencia: ' . print_r($reserva, true));

            $reserva_data = array(
                'localizador' => $reserva->localizador,
                'fecha' => $reserva->fecha,
                'hora' => $reserva->hora,
                'hora_vuelta' => $reserva->hora_vuelta ?? '',
                'nombre' => $reserva->nombre,
                'apellidos' => $reserva->apellidos,
                'email' => $reserva->email,
                'telefono' => $reserva->telefono,
                'adultos' => $reserva->adultos,
                'residentes' => $reserva->residentes,
                'ninos_5_12' => $reserva->ninos_5_12,
                'ninos_menores' => $reserva->ninos_menores,
                'total_personas' => $reserva->total_personas,
                'created_at' => $reserva->created_at,
                'metodo_pago' => 'agencia',
                // ✅ AÑADIR FLAGS PARA OCULTAR PRECIOS
                'hide_prices' => true,
                'is_agency_pdf' => true,
                // ✅ AÑADIR VALORES POR DEFECTO PARA EVITAR ERRORES
                'precio_base' => 0,
                'descuento_total' => 0,
                'precio_final' => 0
            );

            error_log('📋 Datos preparados para PDF de agencia (sin precios): ' . print_r($reserva_data, true));

            // Generar PDF
            if (!class_exists('ReservasPDFGenerator')) {
                require_once RESERVAS_PLUGIN_PATH . 'includes/class-pdf-generator.php';
            }

            $pdf_generator = new ReservasPDFGenerator();
            $pdf_path = $pdf_generator->generate_ticket_pdf($reserva_data);

            if (!$pdf_path || !file_exists($pdf_path)) {
                error_log('❌ PDF no se generó correctamente para agencia');
                wp_send_json_error('Error generando el PDF');
                return;
            }

            error_log('✅ PDF generado para agencia: ' . $pdf_path);
            error_log('📁 Tamaño del archivo: ' . filesize($pdf_path) . ' bytes');

            // ✅ CREAR URL PÚBLICO CORRECTO - IGUAL QUE EN LAS OTRAS FUNCIONES
            $upload_dir = wp_upload_dir();
            $relative_path = str_replace($upload_dir['basedir'], '', $pdf_path);
            $pdf_url = $upload_dir['baseurl'] . $relative_path;

            error_log('🌐 URL del PDF para agencia: ' . $pdf_url);

            // Programar eliminación del archivo después de 1 hora
            wp_schedule_single_event(time() + 3600, 'delete_temp_pdf', array($pdf_path));

            wp_send_json_success(array(
                'pdf_url' => $pdf_url,
                'pdf_path' => $pdf_path,
                'localizador' => $reserva->localizador,
                'filename' => 'billete_' . $reserva->localizador . '.pdf',
                'file_exists' => file_exists($pdf_path),
                'file_size' => filesize($pdf_path),
                'agency_id' => $agency_id
            ));
        } catch (Exception $e) {
            error_log('❌ Exception en generate_agency_ticket_pdf: ' . $e->getMessage());
            error_log('❌ Stack trace: ' . $e->getTraceAsString());
            wp_send_json_error('Error interno generando el PDF: ' . $e->getMessage());
        }
    }

    /**
     * Solicitar cancelación de reserva por parte de agencia
     */
    public function request_agency_cancellation()
    {
        if (!wp_verify_nonce($_POST['nonce'], 'reservas_nonce')) {
            wp_send_json_error('Error de seguridad');
            return;
        }

        if (!session_id()) {
            session_start();
        }

        if (!isset($_SESSION['reservas_user']) || $_SESSION['reservas_user']['role'] !== 'agencia') {
            wp_send_json_error('Sin permisos');
            return;
        }

        global $wpdb;
        $table_reservas = $wpdb->prefix . 'reservas_reservas';

        $reserva_id = intval($_POST['reserva_id']);
        $motivo_cancelacion = sanitize_text_field($_POST['motivo_cancelacion']);
        $agency_id = $_SESSION['reservas_user']['id'];
        $agency_name = $_SESSION['reservas_user']['agency_name'];

        // Verificar que la reserva pertenece a la agencia y no está cancelada
        $reserva = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_reservas WHERE id = %d AND agency_id = %d AND estado != 'cancelada'",
            $reserva_id,
            $agency_id
        ));

        if (!$reserva) {
            wp_send_json_error('Reserva no encontrada, sin permisos o ya cancelada');
            return;
        }

        try {
            // Enviar email al administrador
            if (!class_exists('ReservasEmailService')) {
                require_once RESERVAS_PLUGIN_PATH . 'includes/class-email-service.php';
            }

            $result = ReservasEmailService::send_cancellation_request_to_admin([
                'reserva' => (array) $reserva,
                'agency_name' => $agency_name,
                'motivo_cancelacion' => $motivo_cancelacion
            ]);

            if ($result['success']) {
                wp_send_json_success('Solicitud de cancelación enviada correctamente al administrador. Te contactarán pronto.');
            } else {
                wp_send_json_error('Error enviando la solicitud: ' . $result['message']);
            }
        } catch (Exception $e) {
            error_log('Error en solicitud de cancelación: ' . $e->getMessage());
            wp_send_json_error('Error interno procesando la solicitud');
        }
    }
}
