<?php

/**
 * Clase para gestionar los servicios adicionales de las agencias
 * Archivo: wp-content/plugins/sistema-reservas/includes/class-agency-services-admin.php
 */
class ReservasAgencyServicesAdmin
{
    public function __construct()
    {
        // ✅ Asegurar sesión muy temprano
        add_action('init', array($this, 'ensure_session'), 1);

        // ✅ HOOKS AJAX - PERMITIR AMBOS (wp_ajax y wp_ajax_nopriv)
        // Esto permite que funcione sin estar logueado en WordPress
        add_action('wp_ajax_save_agency_service', array($this, 'save_agency_service'));
        add_action('wp_ajax_nopriv_save_agency_service', array($this, 'save_agency_service'));

        add_action('wp_ajax_delete_agency_service', array($this, 'delete_agency_service'));
        add_action('wp_ajax_nopriv_delete_agency_service', array($this, 'delete_agency_service'));

        add_action('wp_ajax_get_agency_service', array($this, 'get_agency_service'));
        add_action('wp_ajax_nopriv_get_agency_service', array($this, 'get_agency_service')); // ✅ CRÍTICO

        add_action('wp_ajax_debug_agency_services_table', array($this, 'debug_table_status'));
        add_action('wp_ajax_nopriv_debug_agency_services_table', array($this, 'debug_table_status'));

        add_action('wp_ajax_get_available_services_for_confirmation', array($this, 'get_available_services_ajax'));
        add_action('wp_ajax_nopriv_get_available_services_for_confirmation', array($this, 'get_available_services_ajax'));

        add_action('init', array($this, 'maybe_create_table'));
        add_action('init', array($this, 'maybe_update_services_table'));
    }

    /**
     * Asegurar que la sesión está activa
     */
    public function ensure_session()
    {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
            error_log('✅ Sesión asegurada en AgencyServicesAdmin');
        }
    }

    /**
     * Crear tabla de servicios de agencias si no existe
     */
    public function maybe_create_table()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'reservas_agency_services';

        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name;

        if (!$table_exists) {
            $this->create_services_table();
        }
    }

    public function get_available_services_ajax()
    {
        error_log('=== AJAX GET AVAILABLE SERVICES ===');

        $fecha = sanitize_text_field($_POST['fecha'] ?? '');
        $hora = sanitize_text_field($_POST['hora'] ?? '');

        if (empty($fecha) || empty($hora)) {
            wp_send_json_error('Fecha y hora son requeridos');
            return;
        }

        $services = self::get_available_services($fecha, $hora);

        wp_send_json_success($services);
    }

    /**
     * Actualizar tabla para añadir campo de horarios
     */
    public function maybe_update_services_table()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'reservas_agency_services';

        // Verificar si existe el campo horarios_disponibles
        $column_exists = $wpdb->get_results("SHOW COLUMNS FROM $table_name LIKE 'horarios_disponibles'");

        if (empty($column_exists)) {
            $wpdb->query("ALTER TABLE $table_name ADD COLUMN horarios_disponibles TEXT NULL AFTER dias_disponibles");
            error_log('✅ Columna horarios_disponibles añadida a tabla de servicios de agencias');
        }

        $fechas_excluidas_exists = $wpdb->get_results("SHOW COLUMNS FROM $table_name LIKE 'fechas_excluidas'");

        if (empty($fechas_excluidas_exists)) {
            $wpdb->query("ALTER TABLE $table_name ADD COLUMN fechas_excluidas TEXT NULL AFTER horarios_disponibles");
            error_log('✅ Columna fechas_excluidas añadida a tabla de servicios de agencias');
        }
    }

    public function debug_table_status()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'reservas_agency_services';

        error_log('=== DEBUG TABLE STATUS ===');
        error_log('Table name: ' . $table_name);

        // Verificar si existe
        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name;
        error_log('Table exists: ' . ($table_exists ? 'YES' : 'NO'));

        if ($table_exists) {
            // Obtener estructura
            $columns = $wpdb->get_results("SHOW COLUMNS FROM $table_name");
            error_log('Columns: ' . print_r($columns, true));

            // Contar registros
            $count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
            error_log('Total records: ' . $count);
        }
    }

    /**
     * Crear tabla de servicios
     */
    private function create_services_table()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'reservas_agency_services';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            agency_id mediumint(9) NOT NULL,
            servicio_activo tinyint(1) DEFAULT 0,
            dias_disponibles varchar(100) DEFAULT NULL,
            precio_adulto decimal(10,2) DEFAULT 0.00,
            precio_nino decimal(10,2) DEFAULT 0.00,
            logo_url varchar(255) DEFAULT NULL,
            portada_url varchar(255) DEFAULT NULL,
            descripcion text DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY agency_id (agency_id),
            KEY servicio_activo (servicio_activo)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        error_log('✅ Tabla de servicios de agencias creada correctamente');
    }

    /**
     * Guardar servicio de agencia
     */
    public function save_agency_service()
    {
        error_log('=== SAVE AGENCY SERVICE START ===');

        // ✅ INICIAR SESIÓN
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Verificar nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'reservas_nonce')) {
            wp_send_json_error('Error de seguridad');
            return;
        }

        // ✅ VERIFICAR SESIÓN DE RESERVAS (NO DE WORDPRESS)
        if (!isset($_SESSION['reservas_user'])) {
            wp_send_json_error('Sesión expirada');
            return;
        }

        // ✅ VERIFICAR PERMISOS DEL SISTEMA DE RESERVAS
        if ($_SESSION['reservas_user']['role'] !== 'super_admin') {
            wp_send_json_error('Sin permisos para gestionar servicios de agencias');
            return;
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'reservas_agency_services';

        $agency_id = intval($_POST['agency_id']);
        $servicio_activo = isset($_POST['servicio_activo']) ? 1 : 0;

        // Validar que la agencia existe
        $table_agencies = $wpdb->prefix . 'reservas_agencies';
        $agency_exists = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_agencies WHERE id = %d",
            $agency_id
        ));

        if (!$agency_exists) {
            wp_send_json_error('Agencia no encontrada');
            return;
        }

        $data = array(
            'agency_id' => $agency_id,
            'servicio_activo' => $servicio_activo
        );

        // Solo procesar los demás campos si el servicio está activo
        if ($servicio_activo) {
            // ✅ VALIDAR Y PROCESAR HORARIOS (SIN CAMBIOS)
            if (!isset($_POST['horarios']) || empty($_POST['horarios'])) {
                wp_send_json_error('Debes seleccionar al menos un día con horarios');
                return;
            }

            $horarios_data = $_POST['horarios'];
            $horarios_json = array();
            $dias_disponibles = array();

            foreach ($horarios_data as $dia => $horas) {
                if (!empty($horas) && is_array($horas)) {
                    $horas_validas = array_filter($horas, function ($hora) {
                        return !empty($hora);
                    });

                    if (!empty($horas_validas)) {
                        $horarios_json[$dia] = array_values($horas_validas);
                        $dias_disponibles[] = $dia;
                    }
                }
            }

            if (empty($horarios_json)) {
                wp_send_json_error('Debes añadir al menos un horario para los días seleccionados');
                return;
            }

            $data['dias_disponibles'] = implode(',', $dias_disponibles);
            $data['horarios_disponibles'] = json_encode($horarios_json);

            // ✅ PROCESAR IDIOMAS (CON NORMALIZACIÓN)
            $idiomas_data = array();
            if (isset($_POST['idiomas']) && is_array($_POST['idiomas'])) {
                error_log('📝 Procesando idiomas recibidos: ' . print_r($_POST['idiomas'], true));

                foreach ($_POST['idiomas'] as $dia => $idiomas) {
                    if (!empty($idiomas) && is_array($idiomas)) {
                        $idiomas_validos = array_filter($idiomas, function ($idioma) {
                            // ✅ ACEPTAR TANTO español COMO espanol
                            return in_array($idioma, array('espanol', 'español', 'ingles', 'frances'));
                        });

                        if (!empty($idiomas_validos)) {
                            // ✅ NORMALIZAR: Convertir español a espanol
                            $idiomas_normalizados = array_map(function ($idioma) {
                                return $idioma === 'español' ? 'espanol' : $idioma;
                            }, $idiomas_validos);

                            $idiomas_data[$dia] = array_values($idiomas_normalizados);
                            error_log("✅ Idiomas válidos y normalizados para $dia: " . implode(', ', $idiomas_normalizados));
                        }
                    }
                }

                error_log('📋 Idiomas finales procesados: ' . print_r($idiomas_data, true));
            }

            // ✅ SIEMPRE GUARDAR, INCLUSO SI ESTÁ VACÍO (NULL)
            $data['idiomas_disponibles'] = !empty($idiomas_data) ? json_encode($idiomas_data, JSON_UNESCAPED_UNICODE) : null;

            $fechas_excluidas = array();
            if (isset($_POST['fechas_excluidas']) && !empty($_POST['fechas_excluidas'])) {
                $fechas_raw = $_POST['fechas_excluidas'];

                if (is_array($fechas_raw)) {
                    foreach ($fechas_raw as $dia => $fechas) {
                        if (!empty($fechas) && is_array($fechas)) {
                            $fechas_validas = array_filter($fechas, function ($fecha) {
                                return !empty($fecha) && strtotime($fecha) !== false;
                            });

                            if (!empty($fechas_validas)) {
                                $fechas_excluidas[$dia] = array_values($fechas_validas);
                            }
                        }
                    }
                }
            }

            $data['fechas_excluidas'] = !empty($fechas_excluidas) ? json_encode($fechas_excluidas) : null;

            // ✅ VALIDAR PRECIOS (AHORA CON PRECIO NIÑOS MENORES)
            $precio_adulto = floatval($_POST['precio_adulto'] ?? 0);
            $precio_nino = floatval($_POST['precio_nino'] ?? 0);
            $precio_nino_menor = floatval($_POST['precio_nino_menor'] ?? 0); // ✅ NUEVO

            if ($precio_adulto <= 0) {
                wp_send_json_error('El precio de adulto debe ser mayor a 0');
                return;
            }

            if ($precio_nino < 0) {
                wp_send_json_error('El precio de niño no puede ser negativo');
                return;
            }

            if ($precio_nino_menor < 0) { // ✅ NUEVO
                wp_send_json_error('El precio de niño menor no puede ser negativo');
                return;
            }

            $data['precio_adulto'] = $precio_adulto;
            $data['precio_nino'] = $precio_nino;
            $data['precio_nino_menor'] = $precio_nino_menor; // ✅ NUEVO
            $data['descripcion'] = sanitize_textarea_field($_POST['descripcion'] ?? '');
            $data['titulo'] = sanitize_text_field($_POST['titulo'] ?? '');
            $data['orden_prioridad'] = intval($_POST['orden_prioridad'] ?? 999);

            // Procesar imágenes si se subieron (SIN CAMBIOS)
            if (!empty($_FILES['logo_image']['name'])) {
                $logo_upload = $this->handle_image_upload($_FILES['logo_image'], 'logo', $agency_id);
                if (is_wp_error($logo_upload)) {
                    wp_send_json_error('Error subiendo logo: ' . $logo_upload->get_error_message());
                    return;
                }
                $data['logo_url'] = $logo_upload;
            }

            if (!empty($_FILES['portada_image']['name'])) {
                $portada_upload = $this->handle_image_upload($_FILES['portada_image'], 'portada', $agency_id);
                if (is_wp_error($portada_upload)) {
                    wp_send_json_error('Error subiendo portada: ' . $portada_upload->get_error_message());
                    return;
                }
                $data['portada_url'] = $portada_upload;
            }
        }

        // Verificar si ya existe un servicio para esta agencia
        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM $table_name WHERE agency_id = %d",
            $agency_id
        ));

        if ($existing) {
            $result = $wpdb->update($table_name, $data, array('agency_id' => $agency_id));
        } else {
            $result = $wpdb->insert($table_name, $data);
        }

        if ($result !== false) {
            error_log('✅ Servicio de agencia guardado correctamente');
            wp_send_json_success('Servicio guardado correctamente');
        } else {
            error_log('❌ Error guardando servicio: ' . $wpdb->last_error);
            wp_send_json_error('Error al guardar el servicio: ' . $wpdb->last_error);
        }
    }

    public static function get_available_services($fecha, $hora)
    {
        error_log('=== GET AVAILABLE SERVICES ===');
        error_log('Fecha: ' . $fecha);
        error_log('Hora: ' . $hora);

        global $wpdb;
        $table_services = $wpdb->prefix . 'reservas_agency_services';
        $table_agencies = $wpdb->prefix . 'reservas_agencies';

        // Obtener día de la semana en español
        $fecha_obj = new DateTime($fecha);
        $dia_numero = $fecha_obj->format('N'); // 1 (lunes) a 7 (domingo)

        $dias_semana = [
            1 => 'lunes',
            2 => 'martes',
            3 => 'miercoles',
            4 => 'jueves',
            5 => 'viernes',
            6 => 'sabado',
            7 => 'domingo'
        ];

        $dia_nombre = $dias_semana[$dia_numero];
        error_log('Día de la semana: ' . $dia_nombre);

        // Obtener todos los servicios activos
        $services = $wpdb->get_results("
        SELECT s.*, a.agency_name, a.contact_person, a.email, a.phone
        FROM $table_services s
        INNER JOIN $table_agencies a ON s.agency_id = a.id
        WHERE s.servicio_activo = 1
        AND a.status = 'active'
        ORDER BY s.orden_prioridad ASC, s.created_at ASC
    ");

        error_log('Total servicios activos: ' . count($services));

        $available_services = array();

        foreach ($services as $service) {
            // ✅ DEBUG DE IDIOMAS
            error_log('🔍 Servicio ID ' . $service->id . ' - Idiomas en BD: ' . ($service->idiomas_disponibles ?? 'NULL'));

            // Verificar si el servicio está disponible este día
            $dias_disponibles = explode(',', $service->dias_disponibles);

            if (!in_array($dia_nombre, $dias_disponibles)) {
                continue;
            }

            // Verificar fechas excluidas
            if (!empty($service->fechas_excluidas)) {
                $fechas_excluidas = json_decode($service->fechas_excluidas, true);

                if (isset($fechas_excluidas[$dia_nombre]) && is_array($fechas_excluidas[$dia_nombre])) {
                    if (in_array($fecha, $fechas_excluidas[$dia_nombre])) {
                        error_log('❌ Servicio excluido para esta fecha: ' . $service->agency_name . ' - ' . $fecha);
                        continue;
                    }
                }
            }

            // Verificar horarios
            $horarios = json_decode($service->horarios_disponibles, true);

            if (!isset($horarios[$dia_nombre])) {
                continue;
            }

            // Verificar si la hora coincide
            $hora_coincide = false;
            foreach ($horarios[$dia_nombre] as $horario_disponible) {
                $hora_reserva = substr($hora, 0, 5);
                $hora_servicio = substr($horario_disponible, 0, 5);

                if ($hora_reserva === $hora_servicio) {
                    $hora_coincide = true;
                    break;
                }
            }


            $table_disabled = $wpdb->prefix . 'reservas_agency_horarios_disabled';
            $is_disabled = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM $table_disabled 
             WHERE agency_id = %d AND dia = %s AND hora = %s",
                $service->agency_id,
                $dia_nombre,
                $hora
            ));

            if ($is_disabled > 0) {
                error_log('❌ Horario deshabilitado: ' . $service->agency_name . ' - ' . $dia_nombre . ' ' . $hora);
                continue; // ✅ SALTAR ESTE SERVICIO
            }


            if ($hora_coincide) {
                $available_services[] = $service;
            }
        }

        error_log('Total servicios disponibles: ' . count($available_services));

        return $available_services;
    }

    public function get_agency_service()
    {
        error_log('=== GET AGENCY SERVICE START ===');

        // ✅ INICIAR SESIÓN SI NO ESTÁ ACTIVA
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // ✅ VERIFICAR NONCE
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'reservas_nonce')) {
            error_log('❌ Nonce inválido');
            wp_send_json_error('Error de seguridad');
            return;
        }

        // ✅ VERIFICAR SESIÓN DE RESERVAS (NO DE WORDPRESS)
        if (!isset($_SESSION['reservas_user'])) {
            error_log('❌ No hay sesión de reservas activa');
            wp_send_json_error('Sesión expirada. Por favor, recarga la página.');
            return;
        }

        $user = $_SESSION['reservas_user'];

        // ✅ VERIFICAR PERMISOS DEL SISTEMA DE RESERVAS (NO DE WP)
        if (!isset($user['role']) || $user['role'] !== 'super_admin') {
            error_log('❌ Usuario sin permisos en sistema de reservas');
            wp_send_json_error('Sin permisos para gestionar servicios');
            return;
        }

        if (!isset($_POST['agency_id'])) {
            wp_send_json_error('ID de agencia no proporcionado');
            return;
        }

        $agency_id = intval($_POST['agency_id']);

        if ($agency_id <= 0) {
            wp_send_json_error('ID de agencia inválido');
            return;
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'reservas_agency_services';

        $service = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE agency_id = %d",
            $agency_id
        ));

        if ($wpdb->last_error) {
            error_log('❌ Error en query: ' . $wpdb->last_error);
            wp_send_json_error('Error de base de datos');
            return;
        }

        if ($service) {
            // ✅ LOGGING PARA DEBUG
            error_log('📋 Servicio encontrado en BD:');
            error_log('- ID: ' . $service->id);
            error_log('- Agency ID: ' . $service->agency_id);
            error_log('- Horarios (raw): ' . $service->horarios_disponibles);
            error_log('- Idiomas (raw): ' . ($service->idiomas_disponibles ?? 'NULL'));
            error_log('- Fechas excluidas (raw): ' . ($service->fechas_excluidas ?? 'NULL'));

            $response_data = array(
                'id' => $service->id,
                'agency_id' => $service->agency_id,
                'servicio_activo' => intval($service->servicio_activo),
                'horarios_disponibles' => $service->horarios_disponibles ?? '{}',
                'idiomas_disponibles' => $service->idiomas_disponibles ?? '{}', // ✅ CRÍTICO: No debe ser NULL
                'fechas_excluidas' => $service->fechas_excluidas ?? '{}',
                'precio_adulto' => floatval($service->precio_adulto ?? 0),
                'precio_nino' => floatval($service->precio_nino ?? 0),
                'precio_nino_menor' => floatval($service->precio_nino_menor ?? 0),
                'logo_url' => $service->logo_url ?? '',
                'portada_url' => $service->portada_url ?? '',
                'descripcion' => $service->descripcion ?? '',
                'titulo' => $service->titulo ?? '',
                'orden_prioridad' => intval($service->orden_prioridad ?? 999),
                'created_at' => $service->created_at,
                'updated_at' => $service->updated_at
            );

            // ✅ LOGGING DE LO QUE SE ENVÍA
            error_log('📤 Datos que se envían al frontend:');
            error_log('- Idiomas enviados: ' . $response_data['idiomas_disponibles']);
            error_log('✅ Servicio encontrado para agency_id ' . $agency_id);

            wp_send_json_success($response_data);
        } else {
            error_log('ℹ️ No hay servicio configurado para agency_id ' . $agency_id);

            wp_send_json_success(array(
                'servicio_activo' => 0,
                'horarios_disponibles' => '{}',
                'idiomas_disponibles' => '{}', // ✅ NO NULL
                'fechas_excluidas' => '{}',
                'precio_adulto' => 0,
                'precio_nino' => 0,
                'precio_nino_menor' => 0,
                'logo_url' => '',
                'portada_url' => '',
                'descripcion' => '',
                'titulo' => '',
                'orden_prioridad' => 999
            ));
        }
    }

    /**
     * Manejar subida de imágenes
     */
    private function handle_image_upload($file, $type, $agency_id)
    {
        if (!function_exists('wp_handle_upload')) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
        }

        // Validar tipo de archivo
        $allowed_types = array('image/jpeg', 'image/jpg', 'image/png', 'image/gif');
        if (!in_array($file['type'], $allowed_types)) {
            return new WP_Error('invalid_type', 'Solo se permiten imágenes (JPG, PNG, GIF)');
        }

        // Validar tamaño (máximo 2MB)
        if ($file['size'] > 2097152) {
            return new WP_Error('file_too_large', 'La imagen no puede superar los 2MB');
        }

        $upload_overrides = array(
            'test_form' => false,
            'unique_filename_callback' => function ($dir, $name, $ext) use ($agency_id, $type) {
                return "agency_{$agency_id}_{$type}" . $ext;
            }
        );

        $uploaded = wp_handle_upload($file, $upload_overrides);

        if (isset($uploaded['error'])) {
            return new WP_Error('upload_error', $uploaded['error']);
        }

        return $uploaded['url'];
    }

    /**
     * Eliminar servicio de agencia (desactivar)
     */
    public function delete_agency_service()
    {
        if (!session_id()) {
            session_start();
        }

        if (!isset($_SESSION['reservas_user']) || $_SESSION['reservas_user']['role'] !== 'super_admin') {
            wp_send_json_error('Sin permisos');
            return;
        }

        $agency_id = intval($_POST['agency_id']);

        global $wpdb;
        $table_name = $wpdb->prefix . 'reservas_agency_services';

        // Solo desactivar, no eliminar
        $result = $wpdb->update(
            $table_name,
            array('servicio_activo' => 0),
            array('agency_id' => $agency_id)
        );

        if ($result !== false) {
            wp_send_json_success('Servicio desactivado correctamente');
        } else {
            wp_send_json_error('Error al desactivar el servicio');
        }
    }

    /**
     * Método estático para obtener servicios disponibles por día
     */
    public static function get_services_by_day($day_name)
    {
        global $wpdb;
        $table_services = $wpdb->prefix . 'reservas_agency_services';
        $table_agencies = $wpdb->prefix . 'reservas_agencies';

        $services = $wpdb->get_results($wpdb->prepare(
            "SELECT s.*, a.agency_name, a.contact_person 
             FROM $table_services s
             INNER JOIN $table_agencies a ON s.agency_id = a.id
             WHERE s.servicio_activo = 1
             AND a.status = 'active'
             AND FIND_IN_SET(%s, s.dias_disponibles) > 0",
            $day_name
        ));

        return $services;
    }
}
