<?php

/**
 * Clase para gestionar la configuración del sistema de reservas - CON RECORDATORIOS AUTOMÁTICOS
 * Archivo: wp-content/plugins/sistema-reservas/includes/class-configuration-admin.php
 */
class ReservasConfigurationAdmin
{

    public function __construct()
    {
        // Hooks AJAX para configuración
        add_action('wp_ajax_get_configuration', array($this, 'get_configuration'));
        add_action('wp_ajax_nopriv_get_configuration', array($this, 'get_configuration'));

        add_action('wp_ajax_save_configuration', array($this, 'save_configuration'));
        add_action('wp_ajax_nopriv_save_configuration', array($this, 'save_configuration'));

        // ✅ NUEVO: Hook para programar recordatorios automáticos
        add_action('wp', array($this, 'schedule_reminder_cron'));

        // ✅ NUEVO: Hook para ejecutar recordatorios
        add_action('reservas_send_reminders', array($this, 'send_reminder_emails'));
    }

    /**
     * ✅ NUEVO: Programar cron job para recordatorios
     */
    public function schedule_reminder_cron()
    {
        if (!wp_next_scheduled('reservas_send_reminders')) {
            // Programar para que se ejecute cada hora
            wp_schedule_event(time(), 'hourly', 'reservas_send_reminders');
        }
    }

    /**
     * ✅ NUEVO: Enviar emails de recordatorio automáticamente
     */
    public function send_reminder_emails()
    {
        error_log('=== EJECUTANDO RECORDATORIOS AUTOMÁTICOS ===');

        // Verificar si los recordatorios están activos
        $recordatorios_activos = self::get_config('email_recordatorio_activo', '0');
        if ($recordatorios_activos != '1') {
            error_log('Recordatorios desactivados en configuración');
            return;
        }

        $horas_anticipacion = intval(self::get_config('horas_recordatorio', '24'));
        error_log("Buscando reservas para recordar con $horas_anticipacion horas de anticipación");

        global $wpdb;
        $table_reservas = $wpdb->prefix . 'reservas_reservas';

        // Calcular el momento exacto para enviar recordatorios
        $fecha_hora_limite = date('Y-m-d H:i:s', strtotime("+$horas_anticipacion hours"));
        $fecha_hora_limite_fin = date('Y-m-d H:i:s', strtotime("+$horas_anticipacion hours +1 hour"));

        // Buscar reservas que necesiten recordatorio
        $reservas_para_recordar = $wpdb->get_results($wpdb->prepare(
            "SELECT r.*, s.precio_adulto, s.precio_nino, s.precio_residente 
             FROM $table_reservas r
             LEFT JOIN {$wpdb->prefix}reservas_servicios s ON r.servicio_id = s.id
             WHERE r.estado = 'confirmada'
             AND CONCAT(r.fecha, ' ', r.hora) BETWEEN %s AND %s
             AND (r.recordatorio_enviado IS NULL OR r.recordatorio_enviado = 0)",
            $fecha_hora_limite,
            $fecha_hora_limite_fin
        ));

        error_log('Encontradas ' . count($reservas_para_recordar) . ' reservas para recordar');

        if (!class_exists('ReservasEmailService')) {
            require_once RESERVAS_PLUGIN_PATH . 'includes/class-email-service.php';
        }

        $recordatorios_enviados = 0;

        foreach ($reservas_para_recordar as $reserva) {
            $reserva_array = (array) $reserva;

            // Enviar recordatorio
            $resultado = ReservasEmailService::send_reminder_email($reserva_array);

            if ($resultado['success']) {
                // Marcar como recordatorio enviado
                $wpdb->update(
                    $table_reservas,
                    array('recordatorio_enviado' => 1),
                    array('id' => $reserva->id)
                );
                $recordatorios_enviados++;
                error_log("✅ Recordatorio enviado para reserva {$reserva->localizador}");
            } else {
                error_log("❌ Error enviando recordatorio para reserva {$reserva->localizador}: " . $resultado['message']);
            }
        }

        error_log("=== RECORDATORIOS COMPLETADOS: $recordatorios_enviados enviados ===");
    }

    /**
     * Crear tabla de configuración si no existe
     */
    public function maybe_create_table()
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'reservas_configuration';

        // Verificar si la tabla existe
        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name;

        if (!$table_exists) {
            $this->create_configuration_table();
        }

        // ✅ VERIFICAR SI NECESITAMOS ACTUALIZAR LA TABLA DE RESERVAS PARA RECORDATORIOS
        $this->maybe_update_reservas_table();
    }

    /**
     * ✅ NUEVO: Actualizar tabla de reservas para añadir campo de recordatorio
     */
    private function maybe_update_reservas_table()
    {
        global $wpdb;

        $table_reservas = $wpdb->prefix . 'reservas_reservas';

        // Verificar si el campo recordatorio_enviado existe
        $column_exists = $wpdb->get_results("SHOW COLUMNS FROM $table_reservas LIKE 'recordatorio_enviado'");

        if (empty($column_exists)) {
            // Añadir columna para tracking de recordatorios
            $wpdb->query("ALTER TABLE $table_reservas ADD COLUMN recordatorio_enviado TINYINT(1) DEFAULT 0");
            error_log('✅ Columna recordatorio_enviado añadida a tabla de reservas');
        }
    }

    /**
     * Crear tabla de configuración
     */
    private function create_configuration_table()
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'reservas_configuration';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            config_key varchar(100) NOT NULL UNIQUE,
            config_value longtext,
            config_group varchar(50) DEFAULT 'general',
            description text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY config_key (config_key),
            KEY config_group (config_group)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        // Crear configuración por defecto
        $this->create_default_configuration();
    }

    /**
     * ✅ ACTUALIZADO: Crear configuración por defecto - SIN CHECKBOX DE CONFIRMACIÓN + NUEVO CAMPO
     */
    private function create_default_configuration()
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'reservas_configuration';

        $default_configs = array(
            // Precios por defecto
            array(
                'config_key' => 'precio_adulto_defecto',
                'config_value' => '10.00',
                'config_group' => 'precios',
                'description' => 'Precio por defecto para adultos al crear nuevos servicios'
            ),
            array(
                'config_key' => 'precio_nino_defecto',
                'config_value' => '5.00',
                'config_group' => 'precios',
                'description' => 'Precio por defecto para niños (5-12 años) al crear nuevos servicios'
            ),
            array(
                'config_key' => 'precio_residente_defecto',
                'config_value' => '5.00',
                'config_group' => 'precios',
                'description' => 'Precio por defecto para residentes al crear nuevos servicios'
            ),

            // Configuración de servicios
            array(
                'config_key' => 'plazas_defecto',
                'config_value' => '50',
                'config_group' => 'servicios',
                'description' => 'Número de plazas por defecto al crear nuevos servicios'
            ),
            array(
                'config_key' => 'dias_anticipacion_minima',
                'config_value' => '1',
                'config_group' => 'servicios',
                'description' => 'Días de anticipación mínima para poder reservar (bloquea fechas en calendario)'
            ),

            // ✅ CONFIGURACIÓN DE EMAILS ACTUALIZADA
            array(
                'config_key' => 'email_recordatorio_activo',
                'config_value' => '1', // ✅ ACTIVO POR DEFECTO
                'config_group' => 'notificaciones',
                'description' => 'Activar recordatorios automáticos antes del viaje'
            ),
            array(
                'config_key' => 'horas_recordatorio',
                'config_value' => '24',
                'config_group' => 'notificaciones',
                'description' => 'Horas antes del viaje para enviar recordatorio automático'
            ),
            array(
                'config_key' => 'email_remitente',
                'config_value' => get_option('admin_email'),
                'config_group' => 'notificaciones',
                'description' => 'Email remitente para todas las notificaciones del sistema (NO MODIFICAR sin conocimientos técnicos)'
            ),
            array(
                'config_key' => 'nombre_remitente',
                'config_value' => get_bloginfo('name'),
                'config_group' => 'notificaciones',
                'description' => 'Nombre del remitente para notificaciones'
            ),
            // ✅ NUEVO CAMPO: Email de reservas separado del técnico
            array(
                'config_key' => 'email_reservas',
                'config_value' => get_option('admin_email'),
                'config_group' => 'notificaciones',
                'description' => 'Email donde se recibirán las notificaciones de nuevas reservas'
            ),
            array(
                'config_key' => 'email_visitas',
                'config_value' => get_option('admin_email'),
                'config_group' => 'notificaciones',
                'description' => 'Email donde se recibirán las notificaciones de reservas de visitas guiadas'
            ),

            // General
            array(
                'config_key' => 'zona_horaria',
                'config_value' => 'Europe/Madrid',
                'config_group' => 'general',
                'description' => 'Zona horaria del sistema'
            ),
            array(
                'config_key' => 'moneda',
                'config_value' => 'EUR',
                'config_group' => 'general',
                'description' => 'Moneda utilizada en el sistema'
            ),
            array(
                'config_key' => 'simbolo_moneda',
                'config_value' => '€',
                'config_group' => 'general',
                'description' => 'Símbolo de la moneda'
            )
        );

        foreach ($default_configs as $config) {
            // Verificar si ya existe
            $existing = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM $table_name WHERE config_key = %s",
                $config['config_key']
            ));

            if ($existing == 0) {
                $wpdb->insert($table_name, $config);
            }
        }
    }

    /**
     * Obtener toda la configuración
     */
    public function get_configuration()
    {
        // ✅ DEBUGGING MEJORADO
        error_log('=== CONFIGURATION AJAX REQUEST START ===');
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
            if ($user['role'] !== 'super_admin') {
                wp_send_json_error('Sin permisos');
                return;
            }

            global $wpdb;
            $table_name = $wpdb->prefix . 'reservas_configuration';

            $configs = $wpdb->get_results(
                "SELECT * FROM $table_name ORDER BY config_group, config_key"
            );

            if ($wpdb->last_error) {
                error_log('❌ Database error in configuration: ' . $wpdb->last_error);
                die(json_encode(['success' => false, 'data' => 'Database error: ' . $wpdb->last_error]));
            }

            // Organizar por grupos
            $grouped_configs = array();
            foreach ($configs as $config) {
                if (!isset($grouped_configs[$config->config_group])) {
                    $grouped_configs[$config->config_group] = array();
                }
                $grouped_configs[$config->config_group][$config->config_key] = array(
                    'value' => $config->config_value,
                    'description' => $config->description
                );
            }

            error_log('✅ Configuration loaded successfully');
            die(json_encode(['success' => true, 'data' => $grouped_configs]));
        } catch (Exception $e) {
            error_log('❌ CONFIGURATION EXCEPTION: ' . $e->getMessage());
            die(json_encode(['success' => false, 'data' => 'Server error: ' . $e->getMessage()]));
        }
    }

    /**
     * ✅ ACTUALIZADO: Guardar configuración - SIN CHECKBOX + NUEVO CAMPO
     */
    public function save_configuration()
    {
        // ✅ DEBUGGING MEJORADO
        error_log('=== SAVE CONFIGURATION AJAX REQUEST START ===');
        header('Content-Type: application/json');

        try {
            if (!session_id()) {
                session_start();
            }

            error_log('=== SAVE CONFIGURATION DEBUG ===');
            error_log('Session data: ' . print_r($_SESSION ?? [], true));
            error_log('POST data keys: ' . print_r(array_keys($_POST), true));

            if (!isset($_SESSION['reservas_user'])) {
                error_log('❌ No hay usuario en sesión');
                wp_send_json_error('Sesión expirada. Recarga la página e inicia sesión nuevamente.');
                return;
            }

            $user = $_SESSION['reservas_user'];
            if ($user['role'] !== 'super_admin') {
                error_log('❌ Usuario sin permisos: ' . $user['role']);
                wp_send_json_error('Sin permisos');
                return;
            }

            error_log('✅ Usuario validado: ' . $user['username']);

            global $wpdb;
            $table_name = $wpdb->prefix . 'reservas_configuration';

            // Obtener datos del formulario
            $configs_to_save = array();

            // Precios (validación mejorada)
            if (isset($_POST['precio_adulto_defecto'])) {
                $precio_adulto = floatval($_POST['precio_adulto_defecto']);
                if ($precio_adulto < 0) {
                    wp_send_json_error('El precio de adulto no puede ser negativo');
                }
                $configs_to_save['precio_adulto_defecto'] = $precio_adulto;
            }
            if (isset($_POST['precio_nino_defecto'])) {
                $precio_nino = floatval($_POST['precio_nino_defecto']);
                if ($precio_nino < 0) {
                    wp_send_json_error('El precio de niño no puede ser negativo');
                }
                $configs_to_save['precio_nino_defecto'] = $precio_nino;
            }
            if (isset($_POST['precio_residente_defecto'])) {
                $precio_residente = floatval($_POST['precio_residente_defecto']);
                if ($precio_residente < 0) {
                    wp_send_json_error('El precio de residente no puede ser negativo');
                }
                $configs_to_save['precio_residente_defecto'] = $precio_residente;
            }

            // Servicios (validación mejorada)
            if (isset($_POST['plazas_defecto'])) {
                $plazas = intval($_POST['plazas_defecto']);
                if ($plazas < 1 || $plazas > 200) {
                    wp_send_json_error('Las plazas por defecto deben estar entre 1 y 200');
                }
                $configs_to_save['plazas_defecto'] = $plazas;
            }
            if (isset($_POST['dias_anticipacion_minima'])) {
                $dias_anticipacion = intval($_POST['dias_anticipacion_minima']);
                if ($dias_anticipacion < 0 || $dias_anticipacion > 30) {
                    wp_send_json_error('Los días de anticipación deben estar entre 0 y 30');
                }
                $configs_to_save['dias_anticipacion_minima'] = $dias_anticipacion;
            }

            // ✅ NOTIFICACIONES - SIN CHECKBOX DE CONFIRMACIÓN
            $configs_to_save['email_recordatorio_activo'] = isset($_POST['email_recordatorio_activo']) ? 1 : 0;

            if (isset($_POST['horas_recordatorio'])) {
                $horas = intval($_POST['horas_recordatorio']);
                if ($horas < 1 || $horas > 168) { // Máximo una semana
                    wp_send_json_error('Las horas de recordatorio deben estar entre 1 y 168 (una semana)');
                }
                $configs_to_save['horas_recordatorio'] = $horas;
            }
            if (isset($_POST['email_remitente'])) {
                $email = sanitize_email($_POST['email_remitente']);
                if (empty($email) || !is_email($email)) {
                    wp_send_json_error('El email remitente no es válido');
                }
                $configs_to_save['email_remitente'] = $email;
            }
            if (isset($_POST['nombre_remitente'])) {
                $nombre = sanitize_text_field($_POST['nombre_remitente']);
                if (empty($nombre)) {
                    wp_send_json_error('El nombre del remitente no puede estar vacío');
                }
                $configs_to_save['nombre_remitente'] = $nombre;
            }
            // ✅ NUEVO CAMPO: Email de reservas
            if (isset($_POST['email_reservas'])) {
                $email_reservas = sanitize_email($_POST['email_reservas']);
                if (empty($email_reservas) || !is_email($email_reservas)) {
                    wp_send_json_error('El email de reservas no es válido');
                }
                $configs_to_save['email_reservas'] = $email_reservas;
            }

            // ✅ NUEVO CAMPO: Email de visitas guiadas
if (isset($_POST['email_visitas'])) {
    $email_visitas = sanitize_email($_POST['email_visitas']);
    if (empty($email_visitas) || !is_email($email_visitas)) {
        wp_send_json_error('El email de visitas no es válido');
        return;
    }
    $configs_to_save['email_visitas'] = $email_visitas;
}

            // General
            if (isset($_POST['zona_horaria'])) {
                $configs_to_save['zona_horaria'] = sanitize_text_field($_POST['zona_horaria']);
            }
            if (isset($_POST['moneda'])) {
                $configs_to_save['moneda'] = sanitize_text_field($_POST['moneda']);
            }
            if (isset($_POST['simbolo_moneda'])) {
                $simbolo = sanitize_text_field($_POST['simbolo_moneda']);
                if (strlen($simbolo) > 3) {
                    wp_send_json_error('El símbolo de moneda no puede tener más de 3 caracteres');
                }
                $configs_to_save['simbolo_moneda'] = $simbolo;
            }

            // Guardar cada configuración
            $saved_count = 0;
            $errors = array();

            foreach ($configs_to_save as $key => $value) {
                $result = $wpdb->update(
                    $table_name,
                    array('config_value' => $value),
                    array('config_key' => $key)
                );

                if ($result !== false) {
                    $saved_count++;
                } else {
                    $errors[] = "Error guardando $key: " . $wpdb->last_error;
                }
            }

            if (count($errors) > 0) {
                wp_send_json_error('Errores al guardar: ' . implode(', ', $errors));
            }

            wp_send_json_success("Configuración guardada correctamente. $saved_count elementos actualizados.");
        } catch (Exception $e) {
            error_log('❌ SAVE CONFIGURATION EXCEPTION: ' . $e->getMessage());
            wp_send_json_error('Server error: ' . $e->getMessage());
        }
    }

    /**
     * Método estático para obtener un valor de configuración
     */
    public static function get_config($key, $default = null)
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'reservas_configuration';

        $value = $wpdb->get_var($wpdb->prepare(
            "SELECT config_value FROM $table_name WHERE config_key = %s",
            $key
        ));

        return $value !== null ? $value : $default;
    }

    /**
     * Método estático para establecer un valor de configuración
     */
    public static function set_config($key, $value, $group = 'general', $description = '')
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'reservas_configuration';

        // Intentar actualizar primero
        $updated = $wpdb->update(
            $table_name,
            array('config_value' => $value),
            array('config_key' => $key)
        );

        // Si no se actualizó nada, insertar
        if ($updated === 0) {
            $wpdb->insert(
                $table_name,
                array(
                    'config_key' => $key,
                    'config_value' => $value,
                    'config_group' => $group,
                    'description' => $description
                )
            );
        }

        return true;
    }

    /**
     * Método estático para obtener configuración de precios por defecto
     */
    public static function get_default_prices()
    {
        return array(
            'precio_adulto' => self::get_config('precio_adulto_defecto', '10.00'),
            'precio_nino' => self::get_config('precio_nino_defecto', '5.00'),
            'precio_residente' => self::get_config('precio_residente_defecto', '5.00')
        );
    }

    /**
     * Método estático para obtener plazas por defecto
     */
    public static function get_default_plazas()
    {
        return intval(self::get_config('plazas_defecto', '50'));
    }

    /**
     * Método estático para obtener días de anticipación mínima
     */
    public static function get_dias_anticipacion_minima()
    {
        return intval(self::get_config('dias_anticipacion_minima', '1'));
    }
}
