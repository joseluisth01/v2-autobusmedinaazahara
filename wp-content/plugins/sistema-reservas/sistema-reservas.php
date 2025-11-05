<?php

/**
 * Plugin Name: Sistema de Reservas
 * Description: Sistema completo de reservas para servicios de transporte - CON RECORDATORIOS AUTOMÁTICOS
 * Version: 1.000
 */

if (!defined('ABSPATH')) {
    exit;
}

define('RESERVAS_PLUGIN_URL', plugin_dir_url(__FILE__));
define('RESERVAS_PLUGIN_PATH', plugin_dir_path(__FILE__));

class SistemaReservas
{
    private $dashboard;
    private $calendar_admin;
    private $discounts_admin;
    private $configuration_admin;
    private $reports_admin;
    private $agencies_admin;
    private $conductor_admin;
    private $visitas_reports_admin;

    public function __construct()
    {
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
        add_action('init', array($this, 'init'));
        add_action('wp_ajax_test_pdf_generation', array($this, 'test_pdf_generation'));
        add_action('wp_ajax_nopriv_test_pdf_generation', array($this, 'test_pdf_generation'));
    }



    public function test_pdf_generation()
    {
        if (!class_exists('ReservasPDFGenerator')) {
            require_once RESERVAS_PLUGIN_PATH . 'includes/class-pdf-generator.php';
        }

        $test_data = array(
            'localizador' => 'TEST1234',
            'fecha' => '2025-07-20',
            'hora' => '10:00:00',
            'nombre' => 'Test',
            'apellidos' => 'Usuario',
            'email' => 'test@test.com',
            'telefono' => '123456789',
            'adultos' => 2,
            'residentes' => 0,
            'ninos_5_12' => 1,
            'ninos_menores' => 0,
            'total_personas' => 3,
            'precio_base' => 25.00,
            'descuento_total' => 0.00,
            'precio_final' => 25.00,
            'precio_adulto' => 10.00,
            'precio_nino' => 5.00,
            'precio_residente' => 5.00,
            'created_at' => date('Y-m-d H:i:s')
        );

        try {
            $pdf_generator = new ReservasPDFGenerator();
            $pdf_path = $pdf_generator->generate_ticket_pdf($test_data);

            wp_send_json_success(array(
                'message' => 'PDF generado correctamente',
                'path' => $pdf_path,
                'exists' => file_exists($pdf_path),
                'size' => file_exists($pdf_path) ? filesize($pdf_path) : 0
            ));
        } catch (Exception $e) {
            wp_send_json_error(array(
                'message' => 'Error: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ));
        }
    }

    public function init()
    {
        // ✅ VERIFICAR QUE WORDPRESS ESTÁ COMPLETAMENTE CARGADO
        if (!did_action('wp_loaded')) {
            add_action('wp_loaded', array($this, 'init'));
            return;
        }

        // ✅ ASEGURAR QUE LAS SESIONES FUNCIONAN CORRECTAMENTE
        if (!session_id() && !headers_sent()) {
            session_start();
        }

        // Cargar dependencias
        $this->load_dependencies();

        // Inicializar clases
        $this->initialize_classes();

        // Registrar reglas de reescritura
        $this->add_rewrite_rules();

        // Añadir query vars
        add_filter('query_vars', array($this, 'add_query_vars'));

        // Manejar template redirect
        add_action('template_redirect', array($this, 'template_redirect'));

        // ✅ AÑADIR DEBUG PARA AJAX
        add_action('wp_ajax_debug_session', array($this, 'debug_session_info'));
    }

    public function debug_session_info()
    {
        if (!session_id()) {
            session_start();
        }

        $debug_info = array(
            'session_id' => session_id(),
            'session_data' => $_SESSION,
            'user_ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce_check' => wp_verify_nonce($_POST['nonce'] ?? '', 'reservas_nonce')
        );

        wp_send_json_success($debug_info);
    }

    private function load_dependencies()
    {
        $files = array(
            'includes/class-database.php',
            'includes/class-auth.php',
            'includes/class-admin.php',
            'includes/class-dashboard.php',
            'includes/class-calendar-admin.php',
            'includes/class-discounts-admin.php',
            'includes/class-configuration-admin.php',
            'includes/class-reports-admin.php',
            'includes/class-visitas-reports-admin.php',  // ✅ AÑADIR ESTA LÍNEA
            'includes/class-agencies-admin.php',
            'includes/class-agency-profile-admin.php',
            'includes/class-reservas-processor.php',
            'includes/class-email-service.php',
            'includes/class-frontend.php',
            'includes/class-reserva-rapida-admin.php',
            'includes/class-redsys-handler.php',
            'includes/class-conductor-admin.php',
            'includes/class-agency-services-admin.php',
            'includes/class-agency-services-frontend.php',
            'includes/class-visitas-report-pdf-generator.php',
        );

        foreach ($files as $file) {
            $path = RESERVAS_PLUGIN_PATH . $file;
            if (file_exists($path)) {
                require_once $path;
                error_log("✅ Cargado: $file");
            } else {
                error_log("❌ RESERVAS ERROR: No se pudo cargar $file");
            }
        }
    }

    private function initialize_classes()
    {
        // Inicializar clases básicas
        if (class_exists('ReservasAuth')) {
            new ReservasAuth();
        }

        if (class_exists('ReservasDashboard')) {
            $this->dashboard = new ReservasDashboard();
        }

        if (class_exists('ReservasAgencyServicesFrontend')) {
            new ReservasAgencyServicesFrontend();
        }

        if (class_exists('ReservasCalendarAdmin')) {
            $this->calendar_admin = new ReservasCalendarAdmin();
        }

        if (class_exists('ReservasAgencyServicesAdmin')) {
            new ReservasAgencyServicesAdmin();
        }

        // Inicializar clase de descuentos
        if (class_exists('ReservasDiscountsAdmin')) {
            $this->discounts_admin = new ReservasDiscountsAdmin();
        }


        // Inicializar configuración con recordatorios
        if (class_exists('ReservasConfigurationAdmin')) {
            $this->configuration_admin = new ReservasConfigurationAdmin();
        }

        // Inicializar clase de informes
        if (class_exists('ReservasReportsAdmin')) {
            $this->reports_admin = new ReservasReportsAdmin();
        }

        if (class_exists('ReservasAgenciesAdmin')) {
            $this->agencies_admin = new ReservasAgenciesAdmin();
        }

        // Inicializar procesador de reservas CON EMAILS
        if (class_exists('ReservasProcessor')) {
            new ReservasProcessor();
        }

        // Inicializar servicio de emails con recordatorios
        if (class_exists('ReservasEmailService')) {
            new ReservasEmailService();
        }

        if (class_exists('ReservasFrontend')) {
            new ReservasFrontend();
        }

        if (class_exists('ReservasReservaRapidaAdmin')) {
            new ReservasReservaRapidaAdmin();
        }

        if (class_exists('ReservasAgencyProfileAdmin')) {
            new ReservasAgencyProfileAdmin();
        }
        if (class_exists('ReservasAgencyProfileAdmin')) {
            new ReservasAgencyProfileAdmin();
        }

        if (class_exists('ReservasReservaRapidaAdmin')) {
            new ReservasReservaRapidaAdmin();
        }

        if (class_exists('ReservasConductorAdmin')) {
            $this->conductor_admin = new ReservasConductorAdmin();
        }

        if (class_exists('ReservasVisitasReportsAdmin')) {
            $this->visitas_reports_admin = new ReservasVisitasReportsAdmin();
        }
    }

    public function add_rewrite_rules()
    {
        add_rewrite_rule('^reservas-login/?$', 'index.php?reservas_page=login', 'top');
        add_rewrite_rule('^reservas-admin/?$', 'index.php?reservas_page=dashboard', 'top');
        add_rewrite_rule('^reservas-admin/([^/]+)/?$', 'index.php?reservas_page=dashboard&reservas_section=$matches[1]', 'top');
        add_rewrite_rule('^reservas-change-password/?$', 'index.php?reservas_page=change_password', 'top'); // ✅ NUEVA LÍNEA
    }

    public function add_query_vars($vars)
    {
        $vars[] = 'reservas_page';
        $vars[] = 'reservas_section';
        return $vars;
    }

    public function template_redirect()
    {
        $page = get_query_var('reservas_page');

        // Manejar logout
        if (isset($_GET['logout']) && $_GET['logout'] == '1') {
            if ($this->dashboard) {
                $this->dashboard->handle_logout();
            }
        }

        if ($page === 'login') {
            if ($this->dashboard) {
                $this->dashboard->show_login();
            }
            exit;
        }

        if ($page === 'dashboard') {
            if ($this->dashboard) {
                $this->dashboard->show_dashboard();
            }
            exit;
        }

        // ✅ NUEVO: Manejar página de cambio de contraseña
        if ($page === 'change_password') {
            if ($this->dashboard) {
                $this->dashboard->handle_change_password();
            }
            exit;
        }
    }

    public function activate()
    {
        // Crear tablas de base de datos
        $this->create_tables();

        // ✅ AÑADIR ESTA LÍNEA - Crear tabla de servicios de agencias
        $this->create_agency_services_table();

        // ✅ FORZAR ACTUALIZACIÓN DE TABLAS EXISTENTES
        $this->maybe_update_existing_tables();

        $this->init_localizador_counter();

        // ✅ CREAR USUARIO CONDUCTOR AL ACTIVAR
        $this->create_conductor_user();

        // Flush rewrite rules para activar las nuevas URLs
        flush_rewrite_rules();

        // ✅ PROGRAMAR CRON JOB PARA RECORDATORIOS
        if (!wp_next_scheduled('reservas_send_reminders')) {
            wp_schedule_event(time(), 'hourly', 'reservas_send_reminders');
            error_log('✅ Cron job de recordatorios programado');
        }

        if (!wp_next_scheduled('reservas_reset_localizadores')) {
            $next_year = mktime(0, 0, 0, 1, 1, date('Y') + 1);
            wp_schedule_event($next_year, 'yearly', 'reservas_reset_localizadores');
            error_log('✅ Programado reinicio anual de localizadores para: ' . date('Y-m-d H:i:s', $next_year));
        }
    }

    // ✅ AÑADIR ESTE NUEVO MÉTODO
    private function create_agency_services_table()
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

        // Verificar que se creó
        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name;
        if ($table_exists) {
            error_log('✅ Tabla de servicios de agencias creada correctamente en activación');
        } else {
            error_log('❌ ERROR: No se pudo crear la tabla de servicios de agencias');
            error_log('SQL: ' . $sql);
            error_log('wpdb error: ' . $wpdb->last_error);
        }
    }

    private function create_conductor_user()
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
            $result = $wpdb->update(
                $table_users,
                array('password' => password_hash('conductor', PASSWORD_DEFAULT)), // ✅ CONTRASEÑA SIMPLIFICADA
                array('username' => 'conductor', 'role' => 'conductor')
            );

            if ($result !== false) {
                error_log('✅ Contraseña de conductor actualizada: conductor / conductor');
            } else {
                error_log('❌ Error actualizando contraseña conductor: ' . $wpdb->last_error);
            }
        }
    }

    public function deactivate()
    {
        // Limpiar rewrite rules
        flush_rewrite_rules();

        // ✅ LIMPIAR CRON JOB DE RECORDATORIOS
        wp_clear_scheduled_hook('reservas_send_reminders');
        error_log('✅ Cron job de recordatorios eliminado');
    }

    private function create_tables()
    {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        // Tabla de usuarios
        $table_users = $wpdb->prefix . 'reservas_users';
        $sql_users = "CREATE TABLE $table_users (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            username varchar(50) NOT NULL UNIQUE,
            email varchar(100) NOT NULL UNIQUE,
            password varchar(255) NOT NULL,
            role varchar(20) NOT NULL DEFAULT 'usuario',
            status varchar(20) NOT NULL DEFAULT 'active',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_users);

        $table_agencies = $wpdb->prefix . 'reservas_agencies';
        $sql_agencies = "CREATE TABLE $table_agencies (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        agency_name varchar(100) NOT NULL,
        contact_person varchar(100) NOT NULL,
        email varchar(100) NOT NULL UNIQUE,
        phone varchar(20),
        address text,
        username varchar(50) NOT NULL UNIQUE,
        password varchar(255) NOT NULL,
        commission_percentage decimal(5,2) DEFAULT 0.00,
        max_credit_limit decimal(10,2) DEFAULT 0.00,
        current_balance decimal(10,2) DEFAULT 0.00,
        status enum('active', 'inactive', 'suspended') DEFAULT 'active',
        notes text,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY username (username),
        KEY email (email),
        KEY status (status)
    ) $charset_collate;";

        dbDelta($sql_agencies);

        // Tabla de servicios
        $table_servicios = $wpdb->prefix . 'reservas_servicios';
        $sql_servicios = "CREATE TABLE $table_servicios (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        fecha date NOT NULL,
        hora time NOT NULL,
        hora_vuelta time NOT NULL, // Añade esta línea
        plazas_totales int(11) NOT NULL,
        plazas_disponibles int(11) NOT NULL,
        plazas_bloqueadas int(11) DEFAULT 0,
        precio_adulto decimal(10,2) NOT NULL,
        precio_nino decimal(10,2) NOT NULL,
        precio_residente decimal(10,2) NOT NULL,
        tiene_descuento tinyint(1) DEFAULT 0,
        porcentaje_descuento decimal(5,2) DEFAULT 0.00,
        status enum('active', 'inactive') DEFAULT 'active',
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY fecha_hora (fecha, hora),
        KEY fecha (fecha),
        KEY status (status)
    ) $charset_collate;";



        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_servicios);

        // Tabla de reservas rápidas (tracking)
        $table_rapidas = $wpdb->prefix . 'reservas_rapidas';
        $sql_rapidas = "CREATE TABLE $table_rapidas (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    reserva_id mediumint(9) NOT NULL,
    localizador varchar(20) NOT NULL,
    user_id mediumint(9) NOT NULL,
    username varchar(50) NOT NULL,
    user_type varchar(20) NOT NULL,
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY reserva_id (reserva_id),
    KEY localizador (localizador),
    KEY user_id (user_id),
    KEY user_type (user_type)
) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_rapidas);


        // Tabla de reservas de visitas guiadas
        $table_visitas = $wpdb->prefix . 'reservas_visitas';
        $sql_visitas = "CREATE TABLE $table_visitas (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    localizador varchar(20) NOT NULL UNIQUE,
    service_id mediumint(9) NOT NULL,
    agency_id mediumint(9) NOT NULL,
    fecha date NOT NULL,
    hora time NOT NULL,
    nombre varchar(100) NOT NULL,
    apellidos varchar(100) NOT NULL,
    email varchar(100) NOT NULL,
    telefono varchar(20) NOT NULL,
    adultos int(11) DEFAULT 0,
    ninos int(11) DEFAULT 0,
    ninos_menores int(11) DEFAULT 0,
    total_personas int(11) NOT NULL,
    precio_total decimal(10,2) NOT NULL,
    estado enum('pendiente', 'confirmada', 'cancelada') DEFAULT 'confirmada',
    metodo_pago varchar(50) DEFAULT 'pendiente_tpv',
    transaction_id varchar(100) NULL,
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY service_id (service_id),
    KEY agency_id (agency_id),
    KEY fecha (fecha),
    KEY estado (estado),
    KEY localizador (localizador)
) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_visitas);

        $table_horarios_disabled = $wpdb->prefix . 'reservas_agency_horarios_disabled';
        $sql_horarios_disabled = "CREATE TABLE $table_horarios_disabled (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    agency_id mediumint(9) NOT NULL,
    dia varchar(20) NOT NULL,
    hora time NOT NULL,
    disabled_at datetime DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY agency_dia_hora (agency_id, dia, hora),
    KEY agency_id (agency_id)
) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_horarios_disabled);

        // ✅ TABLA DE RESERVAS ACTUALIZADA CON CAMPO DE RECORDATORIO
        $table_reservas = $wpdb->prefix . 'reservas_reservas';
        $sql_reservas = "CREATE TABLE $table_reservas (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            localizador varchar(20) NOT NULL UNIQUE,
            servicio_id mediumint(9) NOT NULL,
            fecha date NOT NULL,
            hora time NOT NULL,
            nombre varchar(100) NOT NULL,
            apellidos varchar(100) NOT NULL,
            email varchar(100) NOT NULL,
            telefono varchar(20) NOT NULL,
            adultos int(11) DEFAULT 0,
            residentes int(11) DEFAULT 0,
            ninos_5_12 int(11) DEFAULT 0,
            ninos_menores int(11) DEFAULT 0,
            total_personas int(11) NOT NULL,
            precio_base decimal(10,2) NOT NULL,
            descuento_total decimal(10,2) DEFAULT 0.00,
            precio_final decimal(10,2) NOT NULL,
            regla_descuento_aplicada TEXT NULL,
            recordatorio_enviado tinyint(1) DEFAULT 0,
            estado enum('pendiente', 'confirmada', 'cancelada') DEFAULT 'confirmada',
            metodo_pago varchar(50) DEFAULT 'directo',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY servicio_id (servicio_id),
            KEY fecha (fecha),
            KEY estado (estado),
            KEY localizador (localizador),
            KEY recordatorio_enviado (recordatorio_enviado)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_reservas);

        // Tabla de reglas de descuento
        $table_discounts = $wpdb->prefix . 'reservas_discount_rules';
        $sql_discounts = "CREATE TABLE $table_discounts (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            rule_name varchar(100) NOT NULL,
            minimum_persons int(11) NOT NULL,
            discount_percentage decimal(5,2) NOT NULL,
            apply_to enum('total', 'adults_only', 'all_paid') DEFAULT 'total',
            rule_description text,
            is_active tinyint(1) DEFAULT 1,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY is_active (is_active),
            KEY minimum_persons (minimum_persons)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_discounts);

        // Tabla de reservas de visitas guiadas
        $table_visitas = $wpdb->prefix . 'reservas_visitas';
        $sql_visitas = "CREATE TABLE $table_visitas (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    localizador varchar(20) NOT NULL UNIQUE,
    service_id mediumint(9) NOT NULL,
    agency_id mediumint(9) NOT NULL,
    fecha date NOT NULL,
    hora time NOT NULL,
    nombre varchar(100) NOT NULL,
    apellidos varchar(100) NOT NULL,
    email varchar(100) NOT NULL,
    telefono varchar(20) NOT NULL,
    adultos int(11) DEFAULT 0,
    ninos int(11) DEFAULT 0,
    total_personas int(11) NOT NULL,
    precio_total decimal(10,2) NOT NULL,
    estado enum('pendiente', 'confirmada', 'cancelada') DEFAULT 'confirmada',
    metodo_pago varchar(50) DEFAULT 'pendiente_tpv',
    transaction_id varchar(100) NULL,
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY service_id (service_id),
    KEY agency_id (agency_id),
    KEY fecha (fecha),
    KEY estado (estado),
    KEY localizador (localizador)
) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_visitas);

        // ✅ TABLA DE CONFIGURACIÓN CON NUEVOS CAMPOS
        $table_configuration = $wpdb->prefix . 'reservas_configuration';
        $sql_configuration = "CREATE TABLE $table_configuration (
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
        dbDelta($sql_configuration);

        // Crear usuario super admin inicial
        $this->create_super_admin();

        // Crear regla de descuento por defecto
        $this->create_default_discount_rule();

        // ✅ CREAR CONFIGURACIÓN CON NUEVOS CAMPOS
        $this->create_default_configuration();

        // ✅ ACTUALIZAR TABLA EXISTENTE SI ES NECESARIO
        $this->maybe_update_existing_tables();
    }

    private function maybe_add_enabled_field()
    {
        global $wpdb;
        $table_servicios = $wpdb->prefix . 'reservas_servicios';

        // Verificar si el campo enabled existe
        $enabled_exists = $wpdb->get_results("SHOW COLUMNS FROM $table_servicios LIKE 'enabled'");

        if (empty($enabled_exists)) {
            // Añadir columna enabled
            $wpdb->query("ALTER TABLE $table_servicios ADD COLUMN enabled TINYINT(1) DEFAULT 1 AFTER status");
            $wpdb->query("ALTER TABLE $table_servicios ADD INDEX enabled (enabled)");
            error_log('✅ Columna enabled añadida a tabla de servicios');
        }
    }

    // ✅ NUEVA FUNCIÓN PARA ACTUALIZAR TABLAS EXISTENTES
    private function maybe_update_existing_tables()
    {
        global $wpdb;

        $table_reservas = $wpdb->prefix . 'reservas_reservas';
        $table_servicios = $wpdb->prefix . 'reservas_servicios';
        $table_configuration = $wpdb->prefix . 'reservas_configuration';

        // ✅ NUEVO: ELIMINAR RESTRICCIÓN ÚNICA FECHA_HORA PARA PERMITIR MÚLTIPLES SERVICIOS
        error_log('=== VERIFICANDO RESTRICCIÓN ÚNICA fecha_hora ===');

        // Verificar si existe la clave única fecha_hora
        $indexes = $wpdb->get_results("SHOW INDEX FROM $table_servicios WHERE Key_name = 'fecha_hora'");

        if (!empty($indexes)) {
            $wpdb->query("ALTER TABLE $table_servicios DROP INDEX fecha_hora");
            error_log('✅ Restricción única fecha_hora eliminada - ahora se permiten múltiples servicios por día/hora');
        } else {
            error_log('ℹ️ La restricción única fecha_hora ya no existe o nunca existió');
        }

        // Crear índices separados para optimizar consultas (solo si no existen)
        $existing_indexes = $wpdb->get_results("SHOW INDEX FROM $table_servicios");
        $index_names = array_column($existing_indexes, 'Key_name');

        if (!in_array('idx_fecha', $index_names)) {
            $wpdb->query("ALTER TABLE $table_servicios ADD INDEX idx_fecha (fecha)");
            error_log('✅ Índice idx_fecha creado');
        }

        if (!in_array('idx_hora', $index_names)) {
            $wpdb->query("ALTER TABLE $table_servicios ADD INDEX idx_hora (hora)");
            error_log('✅ Índice idx_hora creado');
        }

        if (!in_array('idx_fecha_hora', $index_names)) {
            $wpdb->query("ALTER TABLE $table_servicios ADD INDEX idx_fecha_hora (fecha, hora)");
            error_log('✅ Índice idx_fecha_hora creado');
        }


        // ✅ VERIFICAR Y AÑADIR CAMPO ENABLED A SERVICIOS
        $enabled_exists = $wpdb->get_results("SHOW COLUMNS FROM $table_servicios LIKE 'enabled'");

        if (empty($enabled_exists)) {
            $wpdb->query("ALTER TABLE $table_servicios ADD COLUMN enabled TINYINT(1) DEFAULT 1 AFTER status");
            $wpdb->query("ALTER TABLE $table_servicios ADD INDEX enabled (enabled)");
            error_log('✅ Columna enabled añadida a tabla de servicios');
        }

        $table_visitas = $wpdb->prefix . 'reservas_visitas';
        $idioma_exists = $wpdb->get_results("SHOW COLUMNS FROM $table_visitas LIKE 'idioma'");

        if (empty($idioma_exists)) {
            $wpdb->query("ALTER TABLE $table_visitas ADD COLUMN idioma VARCHAR(50) DEFAULT 'espanol' AFTER total_personas");
            error_log('✅ Columna idioma añadida a tabla de visitas');
        }

        // ✅ VERIFICAR Y AÑADIR CAMPOS DE DESCUENTO ESPECÍFICO POR SERVICIO
        $descuento_tipo_exists = $wpdb->get_results("SHOW COLUMNS FROM $table_servicios LIKE 'descuento_tipo'");

        if (empty($descuento_tipo_exists)) {
            $wpdb->query("ALTER TABLE $table_servicios ADD COLUMN descuento_tipo ENUM('fijo', 'por_grupo') DEFAULT 'fijo' AFTER porcentaje_descuento");
            $wpdb->query("ALTER TABLE $table_servicios ADD COLUMN descuento_minimo_personas INT(11) DEFAULT 1 AFTER descuento_tipo");
            error_log('✅ Campos de descuento específico por servicio añadidos');
        }

        $acumulable_exists = $wpdb->get_results("SHOW COLUMNS FROM $table_servicios LIKE 'descuento_acumulable'");

        if (empty($acumulable_exists)) {
            $wpdb->query("ALTER TABLE $table_servicios ADD COLUMN descuento_acumulable TINYINT(1) DEFAULT 0 AFTER descuento_minimo_personas");
            $wpdb->query("ALTER TABLE $table_servicios ADD COLUMN descuento_prioridad ENUM('servicio', 'grupo') DEFAULT 'servicio' AFTER descuento_acumulable");
            error_log('✅ Campos de acumulación de descuentos añadidos');
        }

        $es_reserva_rapida_exists = $wpdb->get_results("SHOW COLUMNS FROM $table_reservas LIKE 'es_reserva_rapida'");

        if (empty($es_reserva_rapida_exists)) {
            $wpdb->query("ALTER TABLE $table_reservas ADD COLUMN es_reserva_rapida TINYINT(1) DEFAULT 0 AFTER metodo_pago");
            $wpdb->query("ALTER TABLE $table_reservas ADD INDEX es_reserva_rapida (es_reserva_rapida)");
            error_log('✅ Campo es_reserva_rapida añadido a tabla de reservas');
        }

        // ✅ VERIFICAR Y AÑADIR CAMPO HORA_VUELTA A SERVICIOS
        $hora_vuelta_servicios_exists = $wpdb->get_results("SHOW COLUMNS FROM $table_servicios LIKE 'hora_vuelta'");

        if (empty($hora_vuelta_servicios_exists)) {
            $wpdb->query("ALTER TABLE $table_servicios ADD COLUMN hora_vuelta TIME NULL AFTER hora");
            error_log('✅ Columna hora_vuelta añadida a tabla de servicios');
        }

        // ✅ ACTUALIZAR TABLA DE RESERVAS

        // Verificar campo recordatorio_enviado
        $recordatorio_exists = $wpdb->get_results("SHOW COLUMNS FROM $table_reservas LIKE 'recordatorio_enviado'");

        if (empty($recordatorio_exists)) {
            $wpdb->query("ALTER TABLE $table_reservas ADD COLUMN recordatorio_enviado TINYINT(1) DEFAULT 0");
            $wpdb->query("ALTER TABLE $table_reservas ADD INDEX recordatorio_enviado (recordatorio_enviado)");
            error_log('✅ Columna recordatorio_enviado añadida a tabla de reservas');
        }

        // Verificar campo hora_vuelta en reservas
        $hora_vuelta_exists = $wpdb->get_results("SHOW COLUMNS FROM $table_reservas LIKE 'hora_vuelta'");

        if (empty($hora_vuelta_exists)) {
            $wpdb->query("ALTER TABLE $table_reservas ADD COLUMN hora_vuelta TIME NULL AFTER hora");
            error_log('✅ Columna hora_vuelta añadida a tabla de reservas');
        }

        // Verificar campo agency_id
        $agency_column_exists = $wpdb->get_results("SHOW COLUMNS FROM $table_reservas LIKE 'agency_id'");

        if (empty($agency_column_exists)) {
            $wpdb->query("ALTER TABLE $table_reservas ADD COLUMN agency_id MEDIUMINT(9) NULL DEFAULT NULL");
            $wpdb->query("ALTER TABLE $table_reservas ADD INDEX agency_id (agency_id)");
            error_log('✅ Columna agency_id añadida a tabla de reservas');
        }

        // Verificar campos de cancelación
        $cancel_column_exists = $wpdb->get_results("SHOW COLUMNS FROM $table_reservas LIKE 'motivo_cancelacion'");

        if (empty($cancel_column_exists)) {
            $wpdb->query("ALTER TABLE $table_reservas ADD COLUMN motivo_cancelacion TEXT NULL");
            $wpdb->query("ALTER TABLE $table_reservas ADD COLUMN fecha_cancelacion DATETIME NULL");
            error_log('✅ Columnas de cancelación añadidas a tabla de reservas');
        }

        // ✅ ACTUALIZAR CONFIGURACIÓN

        // Verificar si existe el campo email_reservas
        $email_reservas_exists = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_configuration WHERE config_key = %s",
            'email_reservas'
        ));

        if ($email_reservas_exists == 0) {
            $wpdb->insert(
                $table_configuration,
                array(
                    'config_key' => 'email_reservas',
                    'config_value' => get_option('admin_email'),
                    'config_group' => 'notificaciones',
                    'description' => 'Email donde se recibirán las notificaciones de nuevas reservas'
                )
            );
            error_log('✅ Configuración email_reservas añadida');
        }

        // Actualizar descripción del email remitente
        $wpdb->update(
            $table_configuration,
            array('description' => 'Email remitente para todas las notificaciones del sistema (NO MODIFICAR sin conocimientos técnicos)'),
            array('config_key' => 'email_remitente')
        );

        $table_agency_services = $wpdb->prefix . 'reservas_agency_services';

        $precio_nino_menor_exists = $wpdb->get_results("SHOW COLUMNS FROM $table_agency_services LIKE 'precio_nino_menor'");

        if (empty($precio_nino_menor_exists)) {
            $wpdb->query("ALTER TABLE $table_agency_services ADD COLUMN precio_nino_menor DECIMAL(10,2) DEFAULT 0.00 AFTER precio_nino");
            error_log('✅ Columna precio_nino_menor añadida a servicios de agencias');
        }

        $table_visitas = $wpdb->prefix . 'reservas_visitas';

        $ninos_menores_exists = $wpdb->get_results("SHOW COLUMNS FROM $table_visitas LIKE 'ninos_menores'");

        if (empty($ninos_menores_exists)) {
            $wpdb->query("ALTER TABLE $table_visitas ADD COLUMN ninos_menores INT(11) DEFAULT 0 AFTER ninos");
            error_log('✅ Columna ninos_menores añadida a reservas de visitas');
        }

        // ✅ VERIFICAR Y AÑADIR CAMPO email_notificaciones A AGENCIAS
        $table_agencies = $wpdb->prefix . 'reservas_agencies';

        // Verificar si la tabla de agencias existe
        $agencies_table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_agencies'") == $table_agencies;

        if ($agencies_table_exists) {
            $email_notif_column_exists = $wpdb->get_results("SHOW COLUMNS FROM $table_agencies LIKE 'email_notificaciones'");

            if (empty($email_notif_column_exists)) {
                $wpdb->query("ALTER TABLE $table_agencies ADD COLUMN email_notificaciones varchar(100) AFTER email");
                error_log('✅ Columna email_notificaciones añadida a tabla de agencias');
            }
        }

        error_log('=== ACTUALIZACIÓN DE TABLAS COMPLETADA ===');
    }

    private function create_super_admin()
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'reservas_users';

        // Verificar si ya existe
        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name WHERE username = %s",
            'administrador'
        ));

        if ($existing == 0) {
            $wpdb->insert(
                $table_name,
                array(
                    'username' => 'administrador',
                    'email' => 'admin@' . parse_url(home_url(), PHP_URL_HOST),
                    'password' => password_hash('busmedina', PASSWORD_DEFAULT),
                    'role' => 'super_admin',
                    'status' => 'active',
                    'created_at' => current_time('mysql')
                )
            );
        } else {
            // Si ya existe, actualizar la contraseña
            $wpdb->update(
                $table_name,
                array(
                    'username' => 'administrador',
                    'password' => password_hash('busmedina', PASSWORD_DEFAULT)
                ),
                array('role' => 'super_admin')
            );
        }
    }

    private function create_default_discount_rule()
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'reservas_discount_rules';

        // Verificar si ya hay reglas
        $existing_rules = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");

        if ($existing_rules == 0) {
            $wpdb->insert(
                $table_name,
                array(
                    'rule_name' => 'Descuento Grupo Grande',
                    'minimum_persons' => 10,
                    'discount_percentage' => 15.00,
                    'apply_to' => 'total',
                    'rule_description' => 'Descuento automático para grupos de 10 o más personas',
                    'is_active' => 1
                )
            );
        }
    }

    // ✅ FUNCIÓN CON CONFIGURACIÓN ACTUALIZADA
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
            // ✅ NUEVO CAMPO: Email de reservas
            array(
                'config_key' => 'email_reservas',
                'config_value' => get_option('admin_email'),
                'config_group' => 'notificaciones',
                'description' => 'Email donde se recibirán las notificaciones de nuevas reservas'
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
                $result = $wpdb->insert($table_name, $config);
                if ($result === false) {
                    error_log("Error insertando configuración: " . $config['config_key'] . " - " . $wpdb->last_error);
                }
            }
        }
    }

    private function init_localizador_counter()
    {
        global $wpdb;

        $table_config = $wpdb->prefix . 'reservas_configuration';
        $año_actual = date('Y');
        $config_key = "ultimo_localizador_$año_actual";

        // Verificar si ya existe configuración para este año
        $exists = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_config WHERE config_key = %s",
            $config_key
        ));

        if ($exists == 0) {
            // Insertar configuración inicial para el año actual
            $wpdb->insert(
                $table_config,
                array(
                    'config_key' => $config_key,
                    'config_value' => '0',
                    'config_group' => 'localizadores',
                    'description' => "Último número de localizador usado en el año $año_actual (se reinicia cada año)"
                )
            );
            error_log("✅ Inicializado contador de localizadores para el año $año_actual");
        }
    }
}



add_shortcode('confirmacion_reserva', 'confirmacion_reserva_shortcode');

function confirmacion_reserva_shortcode()
{
    ob_start();
?>
    <style>
        .confirmacion-container {
            margin: 50px auto;
            padding: 0;
            border-radius: 20px;
        }

        .additional-services-section {

            padding: 50px;
            text-align: center;
            border-radius: 20px;
            box-shadow: 0px 0px 15px 0px rgba(46, 45, 44, .2);
            backdrop-filter: blur(3px);
            margin-bottom: 50px;
        }

        .service-card-destacado {
            max-width: 100%;
        }

        .service-card-destacado .service-name {
            font-size: 32px !important;
            margin-bottom: 20px;
        }

        .service-card-destacado .service-description {
            font-size: 18px !important;
            margin-bottom: 30px;
        }

        .service-card-destacado .horarios-boton2 {
            padding: 18px 250px;
            font-size: 20px;
        }

        @media (max-width: 768px) {
            .service-card-destacado .horarios-boton2 {
                padding: 15px 50px;
                font-size: 16px;
            }

            .service-card-destacado .service-name {
                font-size: 24px !important;
            }
        }

        .services-title {
            text-align: center;
            color: #8B4513;
            font-size: 32px;
            font-weight: bold;
            margin: 0 0 20px 0;
            font-family: 'Georgia', serif;
        }

        .services-subtitle {
            text-align: center;
            color: #2D2D2D;
            font-size: 16px;
            margin: 0 0 10px 0;
            line-height: 1.6;
        }

        .services-description {
            text-align: center;
            color: #2D2D2D;
            font-size: 15px;
            margin: 0 0 40px 0;
            line-height: 1.6;
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .service-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        .service-image {
            width: 100%;
            height: 250px;
            overflow: hidden;
            padding: 20px;

        }

        .service-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 10px;
        }

        .service-content {
            text-align: center;
            padding: 20px;

        }

        .service-name {
            color: #871727;
            font-size: 25px;
            margin-bottom: 15px;
            line-height: 1.3;
            font-family: 'Duran-Medium';
            text-transform: uppercase;
            letter-spacing: 4px;
        }

        .service-description {
            color: #666;
            font-size: 16px;
            margin: 0 0 20px 0;
            line-height: 1.5;
            text-transform: uppercase;
        }

        .horarios-boton2 {
            background-color: #efcf4b;
            color: #2e2d2c;
            padding: 15px 200px;
            border-radius: 10px;
            text-decoration: none;
            font-family: "Duran-Medium";
            font-weight: bold;
            font-size: 18px;
            letter-spacing: 2px;
            transition: all .3s ease;
            display: inline-block;
            box-shadow: 0px 2px 4px 0px rgba(0, 0, 0, .8);
            border: 0px !important;
        }

        .horarios-boton2:hover {
            transform: translateY(-2px);
            text-decoration: none;
            background-color: #efcf4b;
        }

        .horarios-boton23 {
            background-color: #efcf4b;
            color: #2e2d2c;
            padding: 15px 100px;
            width: 100%;
            border-radius: 10px;
            text-decoration: none;
            font-family: "Duran-Medium";
            font-weight: bold;
            font-size: 18px;
            letter-spacing: 2px;
            transition: all .3s ease;
            display: inline-block;
            box-shadow: 0px 2px 4px 0px rgba(0, 0, 0, .8);
            border: 0px !important;
        }

        .horarios-boton23:hover {
            transform: translateY(-2px);
            text-decoration: none;
            background-color: #efcf4b;
        }

        .service-button {
            background: #EFCF4B;
            color: #2E2D2C;
            border: none;
            padding: 12px 30px;
            font-size: 16px;
            font-weight: bold;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .service-button:hover {
            background: #E5C13F;
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(239, 207, 75, 0.4);
        }

        @media (max-width: 768px) {


            .services-title {
                font-size: 24px;
            }

            .services-subtitle,
            .services-description {
                font-size: 14px;
            }

            .services-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .service-image {
                height: 200px;
            }

            .confirmacion-container .action-buttons {
                flex-direction: column;
            }

            .service-content {
                padding: 20px !important;
            }

            .success-banner h1 {
                font-size: 22px !important;
                margin: 0px !important;
            }

            .horarios-boton23 {
                width: 100% !important;
                padding: 20px !important;
            }

            .horarios-boton2 {
                width: 100% !important;
                padding: 20px !important;
            }
        }

        .success-banner {
            background: #DB7461;
            color: white;
            text-align: center;
            margin: 0;
            padding: 15px;
            font-size: 24px;
            letter-spacing: 2px;
            border-top-left-radius: 20px;
            border-top-right-radius: 20px;
            box-shadow: 0px 0px 15px 0px rgba(46, 45, 44, .2);
        }

        .success-banner h1 {
            background: #DB7461;
            color: white;
            text-align: center;
            margin: 0;
            padding: 15px;
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 2px;
            border-top-left-radius: 20px;
            border-top-right-radius: 20px;
            font-family: 'Duran-Regular';
        }

        .content-section {
            background: #FFFFFF;
            padding: 40px 30px;
            text-align: center;
            border-bottom-left-radius: 20px;
            border-bottom-right-radius: 20px;
            box-shadow: 0px 0px 15px 0px rgba(46, 45, 44, .2);
        }

        .thank-you-message {
            margin-bottom: 40px;
        }

        .thank-you-message p {
            font-size: 16px;
            color: #2D2D2D;
            line-height: 1.6;
            margin: 0 0 10px 0;
        }

        .remember-text {
            color: #2D2D2D;
            font-weight: 600;
            font-size: 16px;
            margin: 20px 0;
            font-family: 'Duran-Regular';
        }

        .boarding-message {
            color: #2D2D2D;
            font-size: 16px;
            margin: 20px 0;
            font-family: 'Duran-Regular';
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            align-items: center;
            margin-top: 30px;
            justify-content: space-between;
        }

        .arrival-info {
            margin: 20px 0;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            font-weight: 600;
        }

        .complete-btn {
            background: #EFCF4B;
            border: none;
            padding: 15px 100px;
            font-size: 20px;
            font-weight: bold;
            color: #2E2D2C;
            cursor: pointer;
            transition: all 0.3s;
            min-width: 44%;
            font-family: 'Duran-Medium';
            text-transform: uppercase;
            border-radius: 10px;
            letter-spacing: 1px;
            margin: 0 auto;
            box-shadow: 0px 2px 4px 0px rgba(0, 0, 0, .8);



        }

        .complete-btn:hover {
            transform: translateY(-2px);
            text-decoration: none;
            background-color: #efcf4b;
        }

        .back-btn {
            color: black;
            border: none;
            font-size: 16px;
            cursor: pointer;
            text-transform: uppercase;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background: none !important;
            margin-bottom: 10px;
        }

        .loading-state {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            color: #666;
            font-style: italic;
        }

        .error-state {
            color: #E74C3C;
            font-weight: 600;
        }

        .action-buttons {
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .action-buttons.loaded {
            opacity: 1;
        }

        @media (max-width: 600px) {
            .confirmacion-container {
                margin: 20px;
                max-width: none;
            }

            .success-banner {
                padding: 0px;
            }

            .success-banner h1 {
                font-size: 22px;
            }

            .content-section {
                padding: 30px 20px;
            }

            .complete-btn {
                min-width: 200px;
                padding: 15px 50px;
            }
        }
    </style>

    <div class="confirmacion-container container">
        <button type="button" class="back-btn" onclick="goBackInicio()">
            <img src="https://autobusmedinaazahara.com/wp-content/uploads/2025/07/Vector-15.svg" alt="">VOLVER AL INICIO
        </button>
        <div class="success-banner">
            <h1>¡GRACIAS POR TU RESERVA!</h1>
        </div>

        <div class="content-section">
            <div class="thank-you-message">
                <p>En <strong>Autocares Bravo</strong> estamos agradecidos por tu confianza para el viaje a las ruinas de <strong>Medina Azahara</strong>. Nos hace ilusión acompañarte: tú disfruta de la experiencia, nosotros cuidamos del trayecto.</p>
            </div>

            <div class="remember-text">
                Recuerda presentarte en la parada 10 minutos antes de la salida.
            </div>

            <div style="display:none !important" class="arrival-info" id="arrival-info">
                <span class="loading-state">📍 Cargando información del viaje...</span>
            </div>

            <div class="boarding-message">
                ¡Nos vemos a bordo!
            </div>

            <div class="action-buttons" id="action-buttons">
                <button class="complete-btn" onclick="viewTicket()">
                    VER COMPROBANTE
                </button>
                <button class="complete-btn" onclick="downloadTicket()">
                    DESCARGAR COMPROBANTE
                </button>
            </div>
        </div>
    </div>

    <script>
        let reservationData = null;
        const ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';

        // ✅ MEJORAR LA CARGA INICIAL
        window.addEventListener('DOMContentLoaded', function() {
            console.log('=== PÁGINA DE CONFIRMACIÓN CARGADA ===');

            // Primero cargar los datos de la reserva
            loadReservationData();

            // ✅ ESPERAR A QUE SE CARGUEN LOS DATOS ANTES DE CARGAR SERVICIOS
            const checkReservationData = setInterval(function() {
                if (reservationData && reservationData.detalles) {
                    console.log('✅ Datos de reserva disponibles, cargando servicios...');
                    clearInterval(checkReservationData);
                    loadAvailableServices();
                }
            }, 100); // Revisar cada 100ms

            // ✅ TIMEOUT DE SEGURIDAD: Si después de 5 segundos no hay datos, intentar cargar servicios igual
            setTimeout(function() {
                if (!reservationData || !reservationData.detalles) {
                    console.warn('⚠️ Timeout esperando datos de reserva, intentando cargar servicios de todas formas');
                    clearInterval(checkReservationData);

                    // Intentar obtener datos de la URL
                    const urlParams = new URLSearchParams(window.location.search);
                    const localizador = urlParams.get('localizador');

                    if (localizador) {
                        console.log('🔄 Reintentando carga de datos con localizador:', localizador);
                        loadReservationData();
                    }
                }
            }, 5000);
        });

        function goBackInicio() {
            window.location.href = '<?php echo home_url('/'); ?>';
        }

        function loadAvailableServices() {
            console.log('=== INTENTANDO CARGAR SERVICIOS DISPONIBLES ===');

            if (!reservationData || !reservationData.detalles) {
                console.log('❌ No hay datos de reserva para cargar servicios');
                console.log('reservationData:', reservationData);
                return;
            }

            console.log('✅ Datos de reserva encontrados');
            console.log('Fecha:', reservationData.detalles.fecha);
            console.log('Hora:', reservationData.detalles.hora);

            fetch(ajaxurl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        action: 'get_available_services_for_confirmation',
                        fecha: reservationData.detalles.fecha,
                        hora: reservationData.detalles.hora
                    })
                })
                .then(response => response.json())
                .then(data => {
                    console.log('📡 Respuesta de servicios:', data);

                    if (data.success && data.data && data.data.length > 0) {
                        console.log('✅ Servicios encontrados:', data.data.length);
                        renderAvailableServices(data.data);
                    } else {
                        console.log('⚠️ No hay servicios disponibles para esta fecha/hora');
                        // Mostrar mensaje al usuario
                        showNoServicesMessage();
                    }
                })
                .catch(error => {
                    console.error('❌ Error cargando servicios:', error);
                    showServicesError();
                });
        }



        // ✅ NUEVA FUNCIÓN: Mostrar error al cargar servicios
        function showServicesError() {
            const mainContainer = document.querySelector('.confirmacion-container.container');
            if (mainContainer) {
                const errorHtml = `
            <div class="additional-services-section container" style="padding: 40px; text-align: center;">
                <h2 class="horarios-titulo" style="color: #E74C3C;">Error cargando servicios</h2>
                <p style="font-size: 16px; color: #666; margin-top: 15px;">
                    Hubo un problema al cargar los servicios de visitas guiadas. Por favor, recarga la página.
                </p>
                <button class="complete-btn" onclick="window.location.reload()" style="margin-top: 20px;">
                    RECARGAR PÁGINA
                </button>
            </div>
        `;
                mainContainer.insertAdjacentHTML('afterend', messageHtml);
            }
        }

        function renderAvailableServices(services) {
            if (!services || services.length === 0) {
                console.log('❌ No hay servicios para mostrar');
                return;
            }

            console.log('=== RENDERIZANDO SERVICIOS ===');
            console.log('Total servicios:', services.length);

            // ✅ LIMPIAR SERVICIOS EXISTENTES PRIMERO
            const existingServicesSection = document.querySelector('.additional-services-section');
            if (existingServicesSection) {
                console.log('🧹 Limpiando servicios anteriores');
                existingServicesSection.remove();
            }

            // ✅ GUARDAR SERVICIOS GLOBALMENTE
            window.availableServices = services;
            console.log('✅ Servicios guardados globalmente:', window.availableServices.length);

            // ✅ DEBUG: Ver idiomas de cada servicio
            services.forEach((s, i) => {
                console.log(`Servicio ${i+1} ID ${s.id}:`, {
                    nombre: s.agency_name,
                    idiomas_disponibles: s.idiomas_disponibles,
                    tipo: typeof s.idiomas_disponibles
                });
            });

            // Separar servicio destacado (prioridad 1) del resto
            const destacado = services.find(s => parseInt(s.orden_prioridad) === 1);
            const otros = services.filter(s => parseInt(s.orden_prioridad) !== 1);

            let servicesHtml = `
        <div class="additional-services-section container">
            <h2 class="horarios-titulo">Reserva aquí tu visita guiada a Medina Azahara</h2>
            <p class="services-subtitle">
                Completa la experiencia con un <strong>tour guiado por expertos</strong> y descubre cada secreto de Medina Azahara.
            </p>
            <p class="services-description">
                Elige la opción que más se adapte a ti entre nuestras empresas colaboradoras... 
                <strong>¡Y disfruta de una experiencia 100 % inmersiva!</strong>
            </p>
    `;

            // Si hay servicio destacado (prioridad 1)
            if (destacado) {
                servicesHtml += `
            <div class="service-card service-card-destacado" style="grid-column: 1 / -1; margin-bottom: 30px;" data-service-id="${destacado.id}">
                ${destacado.portada_url ? `
                    <div class="service-image" style="height: 250px;">
                        <img src="${destacado.portada_url}" alt="${destacado.titulo || destacado.agency_name}">
                    </div>
                ` : ''}
                
                <div class="service-content" style="padding: 40px;">
                    <h3 class="service-name">${(destacado.titulo || destacado.agency_name).toUpperCase()}</h3>
                    
                    <p class="service-description">
                        ${destacado.descripcion || 'DURACIÓN 3 HORAS APROX.'}
                    </p>
                    
                    <button class="horarios-boton2" onclick="selectService(${destacado.id})">
                        DESDE ${parseFloat(destacado.precio_adulto).toFixed(0)}€
                    </button>
                </div>
            </div>
        `;
            }

            // Otros servicios (grid)
            if (otros.length > 0) {
                servicesHtml += `<div class="services-grid">`;

                otros.forEach(service => {
                    servicesHtml += `
                <div class="service-card" data-service-id="${service.id}">
                    ${service.portada_url ? `
                        <div class="service-image">
                            <img src="${service.portada_url}" alt="${service.titulo || service.agency_name}">
                        </div>
                    ` : ''}
                    
                    <div class="service-content">
                        <h3 class="service-name">${(service.titulo || service.agency_name).toUpperCase()}</h3>
                        
                        <p class="service-description">
                            ${service.descripcion || 'DURACIÓN 3 HORAS APROX.'}
                        </p>
                        
                        <button class="horarios-boton23" onclick="selectService(${service.id})">
                            DESDE ${parseFloat(service.precio_adulto).toFixed(0)}€
                        </button>
                    </div>
                </div>
            `;
                });

                servicesHtml += `</div>`;
            }

            servicesHtml += `</div>`;

            // Insertar HTML
            const mainContainer = document.querySelector('.confirmacion-container.container');
            if (mainContainer) {
                mainContainer.insertAdjacentHTML('afterend', servicesHtml);
                console.log('✅ HTML de servicios insertado en el DOM');
            } else {
                console.error('❌ No se encontró .confirmacion-container');
            }
        }

        function selectService(serviceId) {
            console.log('=== SELECT SERVICE ===');
            console.log('ID recibido:', serviceId);

            if (!window.availableServices || window.availableServices.length === 0) {
                console.error('❌ No hay servicios disponibles');
                alert('Error: No se encontraron servicios disponibles');
                return;
            }

            const service = window.availableServices.find(s => parseInt(s.id) === parseInt(serviceId));

            if (!service) {
                console.error('❌ No se encontró el servicio con ID:', serviceId);
                alert('Error: No se encontraron datos del servicio');
                return;
            }

            console.log('✅ Servicio encontrado:', service);

            // ✅ PREPARAR idiomas_disponibles CORRECTAMENTE
            let idiomasDisponibles = service.idiomas_disponibles;

            if (!idiomasDisponibles || idiomasDisponibles === 'null' || idiomasDisponibles === '') {
                console.log('⚠️ Idiomas NULL/undefined/vacío, usando objeto vacío');
                idiomasDisponibles = '{}';
            } else if (typeof idiomasDisponibles === 'string') {
                console.log('✅ Idiomas ya es string JSON:', idiomasDisponibles);
                try {
                    JSON.parse(idiomasDisponibles);
                    console.log('✅ String JSON válido');
                } catch (e) {
                    console.error('❌ String JSON inválido, usando objeto vacío');
                    idiomasDisponibles = '{}';
                }
            } else if (typeof idiomasDisponibles === 'object') {
                console.log('✅ Idiomas es objeto, convirtiendo a string');
                idiomasDisponibles = JSON.stringify(idiomasDisponibles);
            }

            console.log('💾 Idiomas final a guardar:', idiomasDisponibles);

            const serviceData = {
                id: service.id,
                agency_id: service.agency_id,
                agency_name: service.agency_name,
                titulo: service.titulo || service.agency_name,
                descripcion: service.descripcion || '',
                portada_url: service.portada_url || '',
                logo_url: service.logo_url || '',
                precio_adulto: parseFloat(service.precio_adulto),
                precio_nino: parseFloat(service.precio_nino),
                precio_nino_menor: parseFloat(service.precio_nino_menor),
                idiomas_disponibles: idiomasDisponibles,
                fecha: reservationData.detalles.fecha,
                hora: reservationData.detalles.hora,
                email: service.email,
                phone: service.phone
            };

            console.log('💾 Objeto serviceData completo:', serviceData);

            // Guardar en sessionStorage
            sessionStorage.setItem('selectedServiceData', JSON.stringify(serviceData));
            console.log('✅ Datos guardados en sessionStorage');

            // ✅ GUARDAR TAMBIÉN EL LOCALIZADOR ACTUAL
            if (reservationData && reservationData.localizador) {
                sessionStorage.setItem('current_localizador', reservationData.localizador);
                console.log('✅ Localizador guardado:', reservationData.localizador);
            }

            // ✅ CONSTRUIR URL INCLUYENDO LOCALIZADOR
            const currentPath = window.location.pathname;
            let targetUrl;

            if (currentPath.includes('/')) {
                const pathParts = currentPath.split('/').filter(part => part !== '');
                if (pathParts.length > 0 && pathParts[0] !== 'detalles-reserva-visita') {
                    targetUrl = window.location.origin + '/' + pathParts[0] + '/detalles-reserva-visita/';
                } else {
                    targetUrl = window.location.origin + '/detalles-reserva-visita/';
                }
            } else {
                targetUrl = window.location.origin + '/detalles-reserva-visita/';
            }

            // ✅ AÑADIR LOCALIZADOR A LA URL
            if (reservationData && reservationData.localizador) {
                targetUrl += '?localizador=' + reservationData.localizador;
            }

            console.log('🔄 Redirigiendo a:', targetUrl);
            window.location.href = targetUrl;
        }

        function contactService(email, phone, agencyName) {
            // En lugar de alert, redirigir a la página de detalles
            console.log('Servicio seleccionado:', agencyName);

            // Buscar los datos completos del servicio en los servicios renderizados
            const serviceCards = document.querySelectorAll('.service-card');
            let selectedService = null;

            serviceCards.forEach(card => {
                const cardName = card.querySelector('.service-name').textContent.trim();
                if (cardName === agencyName.toUpperCase()) {
                    // Extraer datos del servicio
                    const serviceButton = card.querySelector('button[onclick*="contactService"]');
                    const onclickAttr = serviceButton.getAttribute('onclick');

                    // Parsear los parámetros
                    const params = onclickAttr.match(/contactService\('([^']*)',\s*'([^']*)',\s*'([^']*)'/);

                    if (params) {
                        selectedService = {
                            agency_name: params[3],
                            email: params[1],
                            phone: params[2]
                        };
                    }
                }
            });

            if (!selectedService) {
                alert('Error al cargar los datos del servicio');
                return;
            }

            // Guardar datos en sessionStorage
            const serviceData = {
                id: selectedService.id || Date.now(), // ID temporal si no existe
                agency_id: selectedService.agency_id,
                agency_name: selectedService.agency_name,
                titulo: selectedService.titulo || selectedService.agency_name,
                descripcion: selectedService.descripcion || '',
                portada_url: selectedService.portada_url || '',
                logo_url: selectedService.logo_url || '',
                precio_adulto: selectedService.precio_adulto,
                precio_nino: selectedService.precio_nino,
                fecha: reservationData.detalles.fecha,
                hora: reservationData.detalles.hora,
                email: selectedService.email,
                phone: selectedService.phone
            };

            console.log('Guardando datos del servicio:', serviceData);
            sessionStorage.setItem('selectedServiceData', JSON.stringify(serviceData));

            // Redirigir a la página de detalles
            window.location.href = '/detalles-reserva-visita/';
        }

        // Llamar a loadAvailableServices después de cargar los datos de reserva
        window.addEventListener('DOMContentLoaded', function() {
            loadReservationData();
            // Esperar a que se carguen los datos de reserva
            setTimeout(loadAvailableServices, 1000);
        });

        function loadReservationData() {
            console.log('=== INTENTANDO CARGAR DATOS DE RESERVA ===');

            const urlParams = new URLSearchParams(window.location.search);
            const localizador = urlParams.get('localizador');
            const order_id = urlParams.get('order');

            console.log('Localizador desde URL:', localizador);
            console.log('Order ID desde URL:', order_id);

            // ✅ GUARDAR LOCALIZADOR INMEDIATAMENTE
            if (localizador) {
                sessionStorage.setItem('current_localizador', localizador);
                window.currentLocalizador = localizador; // ✅ TAMBIÉN COMO VARIABLE GLOBAL
                console.log('✅ Localizador guardado:', localizador);
            }

            if (!localizador && !order_id) {
                console.error('❌ No se encontró localizador ni order_id en la URL');
                showErrorInfo();
                enableActionButtons();
                return;
            }

            // ✅ PREPARAR DATOS PARA LA BÚSQUEDA
            const searchData = {
                action: 'get_confirmed_reservation_data',
                nonce: '<?php echo wp_create_nonce('reservas_nonce'); ?>'
            };

            // Añadir el parámetro disponible
            if (localizador) {
                searchData.localizador = localizador;
                console.log('🔍 Buscando por localizador:', localizador);
            } else if (order_id) {
                searchData.order_id = order_id;
                console.log('🔍 Buscando por order_id:', order_id);
            }

            // ✅ SOLICITAR DATOS
            fetch(ajaxurl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams(searchData)
                })
                .then(response => response.json())
                .then(data => {
                    console.log('📡 Respuesta del servidor:', data);

                    if (data.success && data.data) {
                        reservationData = data.data;
                        console.log('✅ Datos de reserva cargados correctamente');
                        updateArrivalInfo();
                        enableActionButtons();
                    } else {
                        console.error('❌ Error del servidor:', data.data || 'Error desconocido');
                        showErrorInfo();
                        enableActionButtons();
                    }
                })
                .catch(error => {
                    console.error('❌ Error de conexión:', error);
                    showErrorInfo();
                    enableActionButtons();
                });
        }

        function updateArrivalInfo() {
            if (!reservationData || !reservationData.detalles) {
                showGenericInfo();
                return;
            }

            const detalles = reservationData.detalles;
            const fecha = detalles.fecha || 'Fecha no disponible';
            const hora = detalles.hora || 'Hora no disponible';

            let fechaFormateada = fecha;
            try {
                const fechaObj = new Date(fecha + 'T00:00:00');
                fechaFormateada = fechaObj.toLocaleDateString('es-ES', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
                fechaFormateada = fechaFormateada.charAt(0).toUpperCase() + fechaFormateada.slice(1);
            } catch (e) {
                console.log('No se pudo formatear la fecha');
            }

            const arrivalText = `📍 ${fechaFormateada} a las ${hora}`;
            document.getElementById('arrival-info').innerHTML = arrivalText;
        }

        function showGenericInfo() {
            document.getElementById('arrival-info').innerHTML = '📍 Consulta tu email para ver los detalles del viaje';
        }

        function showErrorInfo() {
            document.getElementById('arrival-info').innerHTML = '<span class="error-state">❌ No se encontraron datos de la reserva. Consulta tu email.</span>';
        }

        function enableActionButtons() {
            const actionButtons = document.getElementById('action-buttons');
            actionButtons.classList.add('loaded');
        }

        function viewTicket() {
            console.log('🎫 Solicitando ver comprobante');

            // ✅ INTENTAR OBTENER LOCALIZADOR DE MÚLTIPLES FUENTES
            let localizador = null;

            if (reservationData && reservationData.localizador) {
                localizador = reservationData.localizador;
            } else {
                // Intentar desde sessionStorage
                localizador = sessionStorage.getItem('current_localizador');

                if (!localizador) {
                    // Intentar desde variable global
                    localizador = window.currentLocalizador;
                }

                if (!localizador) {
                    // Último intento: desde URL
                    const urlParams = new URLSearchParams(window.location.search);
                    localizador = urlParams.get('localizador');
                }
            }

            if (!localizador) {
                alert('No se encontraron datos de la reserva. Por favor, revisa tu email para ver el comprobante.');
                return;
            }

            console.log('✅ Localizador encontrado:', localizador);

            showLoadingModal('Generando comprobante...');
            generateAndViewPDF(localizador); // ✅ PASAR LOCALIZADOR COMO PARÁMETRO
        }

        function downloadTicket() {
            console.log('⬇️ Solicitando descargar comprobante');

            // ✅ INTENTAR OBTENER LOCALIZADOR DE MÚLTIPLES FUENTES
            let localizador = null;

            if (reservationData && reservationData.localizador) {
                localizador = reservationData.localizador;
            } else {
                localizador = sessionStorage.getItem('current_localizador');

                if (!localizador) {
                    localizador = window.currentLocalizador;
                }

                if (!localizador) {
                    const urlParams = new URLSearchParams(window.location.search);
                    localizador = urlParams.get('localizador');
                }
            }

            if (!localizador) {
                alert('No se encontraron datos de la reserva. Por favor, revisa tu email para descargar el comprobante.');
                return;
            }

            console.log('✅ Localizador encontrado:', localizador);

            showLoadingModal('Preparando descarga...');
            generateAndDownloadPDF(localizador); // ✅ PASAR LOCALIZADOR COMO PARÁMETRO
        }

        function generateAndViewPDF(localizadorParam) { // ✅ CAMBIAR NOMBRE DEL PARÁMETRO
            console.log('📋 Requesting PDF generation for view...');
            console.log('🔍 Using localizador:', localizadorParam);

            fetch(ajaxurl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        action: 'generate_ticket_pdf_view',
                        localizador: localizadorParam, // ✅ USAR EL PARÁMETRO
                        nonce: '<?php echo wp_create_nonce('reservas_nonce'); ?>'
                    })
                })
                .then(response => {
                    console.log('📡 Raw response:', response);
                    return response.json();
                })
                .then(data => {
                    console.log('📊 Parsed response:', data);
                    hideLoadingModal();

                    if (data.success && data.data.pdf_url) {
                        console.log('✅ PDF URL received:', data.data.pdf_url);
                        console.log('📁 File exists:', data.data.file_exists);
                        console.log('📏 File size:', data.data.file_size);

                        // Abrir PDF en nueva ventana
                        window.open(data.data.pdf_url, '_blank');
                    } else {
                        console.error('❌ Error in response:', data);
                        alert('Error generando el comprobante: ' + (data.data || 'Error desconocido'));
                    }
                })
                .catch(error => {
                    hideLoadingModal();
                    console.error('❌ Fetch error:', error);
                    alert('Error de conexión al generar el comprobante');
                });
        }

        function generateAndDownloadPDF(localizadorParam) { // ✅ CAMBIAR NOMBRE DEL PARÁMETRO
            console.log('⬇️ Requesting PDF generation for download...');
            console.log('🔍 Using localizador:', localizadorParam);

            fetch(ajaxurl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        action: 'generate_ticket_pdf_download',
                        localizador: localizadorParam, // ✅ USAR EL PARÁMETRO
                        nonce: '<?php echo wp_create_nonce('reservas_nonce'); ?>'
                    })
                })
                .then(response => {
                    console.log('📡 Raw response:', response);
                    return response.json();
                })
                .then(data => {
                    console.log('📊 Parsed response:', data);
                    hideLoadingModal();

                    if (data.success && data.data.pdf_url) {
                        console.log('✅ PDF URL received:', data.data.pdf_url);
                        console.log('📁 File exists:', data.data.file_exists);
                        console.log('📏 File size:', data.data.file_size);

                        // Crear enlace de descarga
                        const link = document.createElement('a');
                        link.href = data.data.pdf_url;
                        link.download = `billete_${localizadorParam}.pdf`; // ✅ USAR EL PARÁMETRO
                        link.target = '_blank';
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);

                        console.log('✅ Download triggered');
                    } else {
                        console.error('❌ Error in response:', data);
                        alert('Error preparando la descarga: ' + (data.data || 'Error desconocido'));
                    }
                })
                .catch(error => {
                    hideLoadingModal();
                    console.error('❌ Fetch error:', error);
                    alert('Error de conexión al preparar la descarga');
                });
        }

        function showLoadingModal(message) {
            let modal = document.getElementById('loading-modal');
            if (!modal) {
                modal = document.createElement('div');
                modal.id = 'loading-modal';
                modal.style.cssText = `
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0,0,0,0.7);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    z-index: 10000;
                `;

                const content = document.createElement('div');
                content.style.cssText = `
                    background: white;
                    padding: 30px;
                    border-radius: 10px;
                    text-align: center;
                    max-width: 300px;
                `;

                content.innerHTML = `
                    <div style="font-size: 24px; margin-bottom: 15px;">⏳</div>
                    <div id="loading-message" style="font-size: 16px; color: #333;">${message}</div>
                `;

                modal.appendChild(content);
                document.body.appendChild(modal);
            } else {
                document.getElementById('loading-message').textContent = message;
                modal.style.display = 'flex';
            }
        }

        function hideLoadingModal() {
            const modal = document.getElementById('loading-modal');
            if (modal) {
                modal.style.display = 'none';
            }
        }
    </script>
<?php
    return ob_get_clean();
}

// ✅ AGREGAR NUEVAS FUNCIONES AJAX PARA MANEJAR PDF
add_action('wp_ajax_generate_ticket_pdf_view', 'handle_pdf_view_request');
add_action('wp_ajax_nopriv_generate_ticket_pdf_view', 'handle_pdf_view_request');

add_action('wp_ajax_generate_ticket_pdf_download', 'handle_pdf_download_request');
add_action('wp_ajax_nopriv_generate_ticket_pdf_download', 'handle_pdf_download_request');

function handle_pdf_view_request()
{
    handle_pdf_request('view');
}

function handle_pdf_download_request()
{
    handle_pdf_request('download');
}

function handle_pdf_request($mode = 'view')
{
    error_log("=== PDF REQUEST: $mode ===");

    // Verificar nonce
    if (!wp_verify_nonce($_POST['nonce'], 'reservas_nonce')) {
        error_log('❌ Nonce verification failed');
        wp_send_json_error('Error de seguridad');
        return;
    }

    $localizador = sanitize_text_field($_POST['localizador'] ?? '');

    if (empty($localizador)) {
        error_log('❌ No localizador provided');
        wp_send_json_error('Localizador no proporcionado');
        return;
    }

    error_log("🔍 Searching for reservation: $localizador");

    try {
        // Buscar la reserva
        global $wpdb;
        $table_reservas = $wpdb->prefix . 'reservas_reservas';

        $reserva = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_reservas WHERE localizador = %s",
            $localizador
        ));

        if (!$reserva) {
            error_log("❌ Reservation not found: $localizador");
            wp_send_json_error('Reserva no encontrada');
            return;
        }

        error_log("✅ Reservation found: " . print_r($reserva, true));

        // Obtener datos del servicio
        $table_servicios = $wpdb->prefix . 'reservas_servicios';
        $servicio = $wpdb->get_row($wpdb->prepare(
            "SELECT precio_adulto, precio_nino, precio_residente, hora_vuelta FROM $table_servicios WHERE id = %d",
            $reserva->servicio_id
        ));

        // Preparar datos para el PDF
        $reserva_data = array(
            'localizador' => $reserva->localizador,
            'fecha' => $reserva->fecha,
            'hora' => $reserva->hora,
            'hora_vuelta' => $servicio->hora_vuelta ?? $reserva->hora_vuelta ?? '',
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
            'precio_adulto' => $servicio->precio_adulto ?? 10.00,
            'precio_nino' => $servicio->precio_nino ?? 5.00,
            'precio_residente' => $servicio->precio_residente ?? 5.00,
            'created_at' => $reserva->created_at,
            'metodo_pago' => $reserva->metodo_pago ?? 'directo'
        );

        error_log("📋 PDF data prepared: " . print_r($reserva_data, true));

        // Generar PDF
        if (!class_exists('ReservasPDFGenerator')) {
            require_once RESERVAS_PLUGIN_PATH . 'includes/class-pdf-generator.php';
        }

        $pdf_generator = new ReservasPDFGenerator();
        $pdf_path = $pdf_generator->generate_ticket_pdf($reserva_data);

        if (!$pdf_path || !file_exists($pdf_path)) {
            error_log('❌ PDF file not created or not found');
            wp_send_json_error('Error generando el PDF');
            return;
        }

        error_log("✅ PDF generated: $pdf_path");
        error_log("📁 File size: " . filesize($pdf_path) . " bytes");

        // ✅ CREAR URL PÚBLICO CORRECTO
        $upload_dir = wp_upload_dir();
        $relative_path = str_replace($upload_dir['basedir'], '', $pdf_path);
        $pdf_url = $upload_dir['baseurl'] . $relative_path;

        error_log("🌐 PDF URL created: $pdf_url");

        // Programar eliminación del archivo después de 1 hora
        wp_schedule_single_event(time() + 3600, 'delete_temp_pdf', array($pdf_path));

        wp_send_json_success(array(
            'pdf_url' => $pdf_url,
            'pdf_path' => $pdf_path,
            'mode' => $mode,
            'localizador' => $localizador,
            'file_exists' => file_exists($pdf_path),
            'file_size' => filesize($pdf_path)
        ));
    } catch (Exception $e) {
        error_log('❌ Exception in PDF generation: ' . $e->getMessage());
        error_log('❌ Stack trace: ' . $e->getTraceAsString());
        wp_send_json_error('Error interno generando el PDF: ' . $e->getMessage());
    }
}

add_action('delete_temp_pdf', 'delete_temporary_pdf_file');

function delete_temporary_pdf_file($pdf_path)
{
    if (file_exists($pdf_path)) {
        if (unlink($pdf_path)) {
            error_log('✅ PDF temporal eliminado: ' . $pdf_path);
        } else {
            error_log('❌ Error eliminando PDF temporal: ' . $pdf_path);
        }
    } else {
        error_log('ℹ️ PDF temporal ya no existe: ' . $pdf_path);
    }
}

add_action('wp', 'schedule_pdf_cleanup');

function schedule_pdf_cleanup()
{
    if (!wp_next_scheduled('daily_pdf_cleanup')) {
        wp_schedule_event(time(), 'daily', 'daily_pdf_cleanup');
    }
}

add_action('daily_pdf_cleanup', 'cleanup_old_temp_pdfs');

// ✅ FUNCIÓN DE PRUEBA PARA DEBUG PDF
add_action('wp_ajax_test_pdf_frontend', 'test_pdf_frontend');
add_action('wp_ajax_nopriv_test_pdf_frontend', 'test_pdf_frontend');

function test_pdf_frontend()
{
    error_log('=== TEST PDF FRONTEND ===');

    // Obtener la reserva más reciente
    global $wpdb;
    $table_reservas = $wpdb->prefix . 'reservas_reservas';

    $reserva = $wpdb->get_row(
        "SELECT * FROM $table_reservas ORDER BY created_at DESC LIMIT 1"
    );

    if (!$reserva) {
        wp_send_json_error('No hay reservas para probar');
        return;
    }

    error_log('Reserva de prueba: ' . print_r($reserva, true));

    try {
        if (!class_exists('ReservasPDFGenerator')) {
            require_once RESERVAS_PLUGIN_PATH . 'includes/class-pdf-generator.php';
        }

        $reserva_data = (array) $reserva;
        $reserva_data['precio_adulto'] = 10.00;
        $reserva_data['precio_nino'] = 5.00;
        $reserva_data['precio_residente'] = 5.00;

        $pdf_generator = new ReservasPDFGenerator();
        $pdf_path = $pdf_generator->generate_ticket_pdf($reserva_data);

        if ($pdf_path && file_exists($pdf_path)) {
            $upload_dir = wp_upload_dir();
            $relative_path = str_replace($upload_dir['basedir'], '', $pdf_path);
            $pdf_url = $upload_dir['baseurl'] . $relative_path;

            wp_send_json_success(array(
                'pdf_path' => $pdf_path,
                'pdf_url' => $pdf_url,
                'file_exists' => file_exists($pdf_path),
                'file_size' => filesize($pdf_path),
                'localizador' => $reserva->localizador
            ));
        } else {
            wp_send_json_error('PDF no se generó correctamente');
        }
    } catch (Exception $e) {
        error_log('Error en test: ' . $e->getMessage());
        wp_send_json_error('Error: ' . $e->getMessage());
    }
}

function cleanup_old_temp_pdfs()
{
    $upload_dir = wp_upload_dir();
    $temp_base_dir = $upload_dir['basedir'] . '/reservas-temp';

    if (!is_dir($temp_base_dir)) {
        return;
    }

    $cleaned = 0;
    $errors = 0;

    // Buscar directorios de fechas anteriores a hoy
    $dirs = glob($temp_base_dir . '/????-??-??', GLOB_ONLYDIR);
    $today = date('Y-m-d');

    foreach ($dirs as $dir) {
        $dir_date = basename($dir);

        // Si el directorio es de días anteriores, eliminar todo su contenido
        if ($dir_date < $today) {
            $files = glob($dir . '/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    if (unlink($file)) {
                        $cleaned++;
                    } else {
                        $errors++;
                    }
                }
            }

            // Intentar eliminar el directorio vacío
            @rmdir($dir);
        }
    }

    if ($cleaned > 0 || $errors > 0) {
        error_log("🧹 Limpieza de PDFs temporales: $cleaned eliminados, $errors errores");
    }
}


// ✅ AÑADIR DESPUÉS DE LA CLASE SistemaReservas

// Hook para reiniciar contadores de localizadores cada año
add_action('reservas_reset_localizadores', 'reset_yearly_localizadores');

function reset_yearly_localizadores()
{
    global $wpdb;

    $table_config = $wpdb->prefix . 'reservas_configuration';
    $año_actual = date('Y');
    $config_key = "ultimo_localizador_$año_actual";

    // Insertar o actualizar contador para el nuevo año
    $wpdb->query($wpdb->prepare(
        "INSERT INTO $table_config (config_key, config_value, config_group, description) 
         VALUES (%s, '0', 'localizadores', %s)
         ON DUPLICATE KEY UPDATE config_value = '0', updated_at = NOW()",
        $config_key,
        "Último número de localizador usado en el año $año_actual (se reinicia cada año)"
    ));

    // Programar para el siguiente año
    $next_year = mktime(0, 0, 0, 1, 1, date('Y') + 1);
    wp_schedule_event($next_year, 'yearly', 'reservas_reset_localizadores');

    error_log("✅ Contador de localizadores reiniciado para el año $año_actual");
}

// ✅ SHORTCODES SIN CAMBIOS

// Shortcode para uso en páginas de WordPress (alternativa)
add_shortcode('reservas_login', 'reservas_login_shortcode');

function reservas_login_shortcode()
{
    // Procesar login si se envía el formulario
    if ($_POST && isset($_POST['shortcode_login'])) {
        $username = sanitize_text_field($_POST['username']);
        $password = $_POST['password'];

        global $wpdb;
        $table_name = $wpdb->prefix . 'reservas_users';

        $user = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE username = %s AND status = 'active'",
            $username
        ));

        if ($user && password_verify($password, $user->password)) {
            if (!session_id()) {
                session_start();
            }

            $_SESSION['reservas_user'] = array(
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'role' => $user->role
            );

            return '<div style="padding: 20px; background: #edfaed; border-left: 4px solid #00a32a; color: #00a32a;">
                        <strong>✅ Login exitoso!</strong> 
                        <br>Ahora puedes ir al <a href="' . home_url('/reservas-admin/') . '">dashboard</a>
                    </div>';
        } else {
            return '<div style="padding: 20px; background: #fbeaea; border-left: 4px solid #d63638; color: #d63638;">
                        <strong>❌ Error:</strong> Usuario o contraseña incorrectos
                    </div>';
        }
    }

    ob_start();
?>
    <div
        style="max-width: 400px; margin: 0 auto; padding: 20px; background: white; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <h2 style="text-align: center; color: #23282d;">Sistema de Reservas - Login</h2>
        <form method="post">
            <input type="hidden" name="shortcode_login" value="1">
            <p>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">Usuario:</label>
                <input type="text" name="username"
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;"
                    required>
            </p>
            <p>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">Contraseña:</label>
                <input type="password" name="password"
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;"
                    required>
            </p>
            <p>
                <input type="submit" value="Iniciar Sesión"
                    style="width: 100%; padding: 12px; background: #0073aa; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: 600;">
            </p>
        </form>
        <div style="background: #f0f0f1; padding: 15px; margin-top: 15px; border-radius: 4px;">
            <p style="margin: 5px 0; font-size: 14px;"><strong>Usuario:</strong> superadmin</p>
            <p style="margin: 5px 0; font-size: 14px;"><strong>Contraseña:</strong> admin123</p>
        </div>
    </div>
<?php
    return ob_get_clean();
}

add_action('admin_init', function () {
    global $wpdb;
    $table_name = $wpdb->prefix . 'reservas_agencies';

    // Verificar si el campo email_notificaciones existe
    $column_exists = $wpdb->get_results("SHOW COLUMNS FROM $table_name LIKE 'email_notificaciones'");

    if (empty($column_exists)) {
        $wpdb->query("ALTER TABLE $table_name ADD COLUMN email_notificaciones varchar(100) AFTER email");
        error_log('✅ Columna email_notificaciones añadida a tabla de agencias');
    }
});



add_action('wp_ajax_generar_formulario_pago_redsys', 'ajax_generar_formulario_pago_redsys');
add_action('wp_ajax_nopriv_generar_formulario_pago_redsys', 'ajax_generar_formulario_pago_redsys');

// ✅ FUNCIÓN AJAX REDSYS CORREGIDA
function ajax_generar_formulario_pago_redsys()
{
    error_log('=== FUNCIÓN REDSYS EJECUTADA ===');

    try {
        // Verificar nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'reservas_nonce')) {
            wp_send_json_error('Error de seguridad');
            return;
        }

        // Decodificar datos de reserva
        $reservation_data_json = stripslashes($_POST['reservation_data']);
        $reserva = json_decode($reservation_data_json, true);

        if (!$reserva) {
            wp_send_json_error('Datos de reserva inválidos');
            return;
        }

        error_log('Datos recibidos para Redsys: ' . print_r($reserva, true));

        // ✅ USAR LA FUNCIÓN EXISTENTE DE REDSYS
        if (!function_exists('generar_formulario_redsys')) {
            require_once RESERVAS_PLUGIN_PATH . 'includes/class-redsys-handler.php';
        }

        $formulario_html = generar_formulario_redsys($reserva);

        error_log("✅ Formulario Redsys generado correctamente");
        wp_send_json_success($formulario_html);
    } catch (Exception $e) {
        error_log('❌ Excepción en Redsys: ' . $e->getMessage());
        wp_send_json_error('Error: ' . $e->getMessage());
    }
}





add_action('wp_ajax_redsys_notification', 'handle_redsys_notification');
add_action('wp_ajax_nopriv_redsys_notification', 'handle_redsys_notification');


add_action('wp_ajax_debug_redsys_flow', 'debug_redsys_flow');
add_action('wp_ajax_nopriv_debug_redsys_flow', 'debug_redsys_flow');

function debug_redsys_flow()
{
    if (!session_id()) {
        session_start();
    }

    $order_id = $_POST['order_id'] ?? '';

    error_log('=== DEBUG FLUJO REDSYS ===');
    error_log('Order ID recibido: ' . $order_id);
    error_log('Session ID: ' . session_id());
    error_log('Session data: ' . print_r($_SESSION, true));

    // Verificar si hay datos en transient
    if ($order_id) {
        $transient_data = get_transient('redsys_order_' . $order_id);
        error_log('Datos en transient: ' . print_r($transient_data, true));
    }

    // Verificar últimas reservas
    global $wpdb;
    $table_reservas = $wpdb->prefix . 'reservas_reservas';

    $recent_reservas = $wpdb->get_results(
        "SELECT * FROM $table_reservas 
         WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR)
         ORDER BY created_at DESC 
         LIMIT 5"
    );

    error_log('Reservas recientes: ' . print_r($recent_reservas, true));

    wp_send_json_success(array(
        'order_id' => $order_id,
        'session_id' => session_id(),
        'session_data' => $_SESSION,
        'transient_data' => $transient_data ?? null,
        'recent_reservas' => $recent_reservas
    ));
}
function handle_redsys_notification()
{
    error_log('🔁 Recibida notificación de Redsys (MerchantURL)');
    error_log('POST data: ' . print_r($_POST, true));
    error_log('GET data: ' . print_r($_GET, true));

    $params = $_POST['Ds_MerchantParameters'] ?? '';
    $signature = $_POST['Ds_Signature'] ?? '';

    if (!$params || !$signature) {
        error_log('❌ Faltan parámetros en notificación');
        status_header(400);
        exit('ERROR: Missing parameters');
    }

    if (!class_exists('RedsysAPI')) {
        require_once RESERVAS_PLUGIN_PATH . 'includes/redsys-api.php';
    }

    $redsys = new RedsysAPI();

    try {
        $decoded = $redsys->getParametersFromResponse($params);
        error_log('✅ Parámetros decodificados: ' . print_r($decoded, true));
    } catch (Exception $e) {
        error_log('❌ Error decodificando parámetros: ' . $e->getMessage());
        status_header(400);
        exit('ERROR: Invalid parameters');
    }

    $order_id = $decoded['Ds_Order'] ?? null;
    $response_code = $decoded['Ds_Response'] ?? null;

    if (!$order_id) {
        error_log('❌ No se pudo obtener el ID del pedido');
        status_header(400);
        exit('ERROR: No order ID');
    }

    // ✅ VERIFICAR QUE EL PAGO FUE EXITOSO
    $response_code = intval($response_code);
    if ($response_code < 0 || $response_code > 99) {
        error_log("❌ Pago fallido. Código de respuesta: $response_code");
        status_header(400);
        exit('ERROR: Payment failed');
    }

    error_log('✅ Pago exitoso, procesando reserva...');

    // ✅ CARGAR FUNCIÓN DE PROCESAMIENTO CORREGIDA
    if (!function_exists('process_successful_payment')) {
        require_once RESERVAS_PLUGIN_PATH . 'includes/class-redsys-handler.php';
    }

    $ok = process_successful_payment($order_id, $decoded);

    if ($ok) {
        error_log("✅ Reserva procesada correctamente desde notificación para order: $order_id");
        status_header(200);
        echo 'OK';
    } else {
        error_log("❌ Fallo procesando reserva desde notificación para order: $order_id");
        status_header(500);
        echo 'ERROR';
    }

    exit;
}

// ✅ NUEVA FUNCIÓN: Procesar pago cuando llega por URL GET
add_action('template_redirect', 'check_redsys_return_url');

function check_redsys_return_url()
{
    // Solo ejecutar en la página de confirmación
    if (!is_page() || get_the_title() !== 'Confirmacion Reserva') {
        return;
    }

    $status = $_GET['status'] ?? '';
    $order = $_GET['order'] ?? '';

    error_log('=== VERIFICANDO URL DE RETORNO REDSYS ===');
    error_log('Status: ' . $status);
    error_log('Order: ' . $order);

    if ($status === 'ok' && !empty($order)) {
        // Verificar si ya procesamos esta reserva
        global $wpdb;
        $table_reservas = $wpdb->prefix . 'reservas_reservas';

        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM $table_reservas WHERE redsys_order_id = %s",
            $order
        ));

        if (!$existing) {
            error_log('🔄 Reserva no encontrada, procesando desde URL...');

            // Cargar función de procesamiento
            if (!function_exists('process_successful_payment')) {
                require_once RESERVAS_PLUGIN_PATH . 'includes/redsys-helper.php';
            }

            // Simular parámetros de Redsys para el procesamiento
            $mock_params = array(
                'Ds_Order' => $order,
                'Ds_Response' => '0000', // Código de éxito
                'Ds_AuthorisationCode' => 'URL_SUCCESS_' . time()
            );

            $result = process_successful_payment($order, $mock_params);

            if ($result) {
                error_log("✅ Reserva procesada desde URL para order: $order");
            } else {
                error_log("❌ Error procesando reserva desde URL para order: $order");
            }
        } else {
            error_log("ℹ️ Reserva ya existe para order: $order (ID: $existing)");
        }
    }
}

// ✅ VERIFICAR Y CREAR CAMPO redsys_order_id SI NO EXISTE
add_action('wp_loaded', 'ensure_redsys_order_id_field');

function ensure_redsys_order_id_field()
{
    global $wpdb;
    $table_reservas = $wpdb->prefix . 'reservas_reservas';

    // Verificar si el campo existe
    $field_exists = $wpdb->get_results("SHOW COLUMNS FROM $table_reservas LIKE 'redsys_order_id'");

    if (empty($field_exists)) {
        $wpdb->query("ALTER TABLE $table_reservas ADD COLUMN redsys_order_id VARCHAR(20) NULL AFTER localizador");
        $wpdb->query("ALTER TABLE $table_reservas ADD INDEX redsys_order_id (redsys_order_id)");
        error_log('✅ Campo redsys_order_id añadido a tabla de reservas');
    }
}

// Nuevo endpoint para cargar datos de reserva confirmada
add_action('wp_ajax_get_confirmed_reservation_data', 'get_confirmed_reservation_data');
add_action('wp_ajax_nopriv_get_confirmed_reservation_data', 'get_confirmed_reservation_data');

function get_confirmed_reservation_data()
{
    // Verificar si hay datos en la sesión
    if (isset($_SESSION['reserva_confirmada'])) {
        $reserva_data = $_SESSION['reserva_confirmada'];
        // Limpiar la sesión después de obtener los datos
        unset($_SESSION['reserva_confirmada']);
        return $reserva_data;
    }

    // Si no hay datos en sesión, verificar si hay un ID de reserva en la URL
    if (isset($_GET['reserva_id'])) {
        global $wpdb;
        $reserva_id = intval($_GET['reserva_id']);

        $reserva = $wpdb->get_row($wpdb->prepare("
            SELECT r.*, s.nombre as servicio_nombre, s.horario
            FROM {$wpdb->prefix}reservas_reservas r
            JOIN {$wpdb->prefix}reservas_servicios s ON r.servicio_id = s.id
            WHERE r.id = %d
        ", $reserva_id));

        if ($reserva) {
            // Obtener pasajeros
            $pasajeros = $wpdb->get_results($wpdb->prepare("
                SELECT * FROM {$wpdb->prefix}reservas_pasajeros 
                WHERE reserva_id = %d
            ", $reserva_id));

            return array(
                'reserva' => $reserva,
                'pasajeros' => $pasajeros
            );
        }
    }

    return null;
}

add_action('wp_ajax_get_confirmed_reservation_data', 'ajax_get_confirmed_reservation_data');
add_action('wp_ajax_nopriv_get_confirmed_reservation_data', 'ajax_get_confirmed_reservation_data');

function ajax_get_confirmed_reservation_data()
{
    if (!wp_verify_nonce($_POST['nonce'], 'reservas_nonce')) {
        wp_send_json_error('Error de seguridad');
        return;
    }

    // ✅ OBTENER PARÁMETROS DE BÚSQUEDA
    $localizador = sanitize_text_field($_POST['localizador'] ?? '');
    $order_id = sanitize_text_field($_POST['order_id'] ?? '');

    error_log('=== BUSCANDO DATOS DE CONFIRMACIÓN ===');
    error_log('Localizador recibido: ' . $localizador);
    error_log('Order ID recibido: ' . $order_id);

    // ✅ VERIFICAR QUE TENEMOS AL MENOS UN PARÁMETRO
    if (empty($localizador) && empty($order_id)) {
        error_log('❌ No se proporcionó localizador ni order_id');
        wp_send_json_error('No se proporcionó localizador ni order_id válido');
        return;
    }

    global $wpdb;
    $table_reservas = $wpdb->prefix . 'reservas_reservas';

    $reserva = null;

    // ✅ BUSCAR POR LOCALIZADOR PRIMERO
    if (!empty($localizador)) {
        $reserva = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_reservas WHERE localizador = %s AND estado = 'confirmada' LIMIT 1",
            $localizador
        ));

        if ($reserva) {
            error_log('✅ Reserva encontrada por localizador: ' . $reserva->localizador);
        }
    }

    // ✅ SI NO SE ENCUENTRA, BUSCAR POR ORDER_ID
    if (!$reserva && !empty($order_id)) {
        $reserva = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_reservas WHERE redsys_order_id = %s AND estado = 'confirmada' LIMIT 1",
            $order_id
        ));

        if ($reserva) {
            error_log('✅ Reserva encontrada por order_id: ' . $reserva->localizador . ' (Order: ' . $order_id . ')');
        }
    }

    if ($reserva) {
        $data = array(
            'localizador' => $reserva->localizador,
            'reserva_id' => $reserva->id,
            'detalles' => array(
                'fecha' => $reserva->fecha,
                'hora' => $reserva->hora,
                'personas' => $reserva->total_personas,
                'precio_final' => $reserva->precio_final
            )
        );

        wp_send_json_success($data);
        return;
    }

    error_log('❌ No se encontró reserva con localizador: ' . $localizador . ' ni order_id: ' . $order_id);
    wp_send_json_error('No se encontró reserva con esos datos');
}

add_action('wp_ajax_get_most_recent_reservation', 'ajax_get_most_recent_reservation');
add_action('wp_ajax_nopriv_get_most_recent_reservation', 'ajax_get_most_recent_reservation');

function ajax_get_most_recent_reservation()
{
    if (!wp_verify_nonce($_POST['nonce'], 'reservas_nonce')) {
        wp_send_json_error('Error de seguridad');
        return;
    }

    error_log('=== BUSCANDO RESERVA MÁS RECIENTE ===');

    global $wpdb;
    $table_reservas = $wpdb->prefix . 'reservas_reservas';

    // ✅ CONSULTA CORREGIDA SIN ERROR DE prepare()
    $recent_reservation = $wpdb->get_row(
        "SELECT * FROM $table_reservas 
         WHERE created_at >= DATE_SUB(NOW(), INTERVAL 10 MINUTE)
         AND metodo_pago = 'redsys'
         AND estado = 'confirmada'
         ORDER BY created_at DESC 
         LIMIT 1"
    );

    if ($recent_reservation) {
        error_log('✅ Reserva reciente encontrada en BD: ' . $recent_reservation->localizador);

        $data = array(
            'localizador' => $recent_reservation->localizador,
            'reserva_id' => $recent_reservation->id,
            'detalles' => array(
                'fecha' => $recent_reservation->fecha,
                'hora' => $recent_reservation->hora,
                'personas' => $recent_reservation->total_personas,
                'precio_final' => $recent_reservation->precio_final
            )
        );

        wp_send_json_success($data);
    } else {
        error_log('❌ No se encontraron reservas recientes');
        wp_send_json_error('No se encontraron reservas recientes');
    }
}

// Añadir al final de sistema-reservas.php, antes de "new SistemaReservas();"

function add_redsys_order_id_field()
{
    global $wpdb;
    $table_reservas = $wpdb->prefix . 'reservas_reservas';

    // Verificar si el campo existe
    $field_exists = $wpdb->get_results("SHOW COLUMNS FROM $table_reservas LIKE 'redsys_order_id'");

    if (empty($field_exists)) {
        $wpdb->query("ALTER TABLE $table_reservas ADD COLUMN redsys_order_id VARCHAR(20) NULL AFTER localizador");
        $wpdb->query("ALTER TABLE $table_reservas ADD INDEX redsys_order_id (redsys_order_id)");
        error_log('✅ Campo redsys_order_id añadido a tabla de reservas');
    }
}

// Ejecutar al activar el plugin
register_activation_hook(__FILE__, 'add_redsys_order_id_field');

// También ejecutar al cargar admin (por si ya está activado)
add_action('admin_init', 'add_redsys_order_id_field');

// ✅ FUNCIÓN DE DEBUG PARA VERIFICAR RESERVAS
add_action('wp_ajax_debug_reservas_recientes', 'debug_reservas_recientes');
add_action('wp_ajax_nopriv_debug_reservas_recientes', 'debug_reservas_recientes');

function debug_reservas_recientes()
{
    global $wpdb;
    $table_reservas = $wpdb->prefix . 'reservas_reservas';

    $reservas = $wpdb->get_results(
        "SELECT id, localizador, redsys_order_id, metodo_pago, estado, created_at 
         FROM $table_reservas 
         WHERE created_at >= DATE_SUB(NOW(), INTERVAL 60 MINUTE)
         ORDER BY created_at DESC"
    );

    error_log('=== DEBUG RESERVAS RECIENTES ===');
    error_log('Total reservas última hora: ' . count($reservas));

    foreach ($reservas as $reserva) {
        error_log("- ID: {$reserva->id}, Localizador: {$reserva->localizador}, Order: {$reserva->redsys_order_id}, Método: {$reserva->metodo_pago}, Estado: {$reserva->estado}, Fecha: {$reserva->created_at}");
    }

    wp_send_json_success(array(
        'total' => count($reservas),
        'reservas' => $reservas
    ));
}


// ✅ FUNCIÓN MANUAL PARA CREAR USUARIO CONDUCTOR (añadir al final de sistema-reservas.php)

add_action('wp_ajax_create_conductor_user_manual', 'create_conductor_user_manual');
add_action('wp_ajax_nopriv_create_conductor_user_manual', 'create_conductor_user_manual');

function create_conductor_user_manual()
{
    // Solo permitir en desarrollo o con permisos de administrador
    if (!current_user_can('administrator') && !isset($_GET['force'])) {
        wp_die('No tienes permisos para esta acción');
    }

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
            echo '✅ Usuario conductor creado exitosamente<br>';
            echo '<strong>Usuario:</strong> conductor<br>';
            echo '<strong>Contraseña:</strong> conductorbusmedina<br>';
            echo '<a href="' . home_url('/reservas-login/') . '">Ir al login</a>';
        } else {
            echo '❌ Error creando el usuario: ' . $wpdb->last_error;
        }
    } else {
        // Actualizar contraseña si ya existe
        $result = $wpdb->update(
            $table_users,
            array('password' => password_hash('conductorbusmedina', PASSWORD_DEFAULT)),
            array('username' => 'conductor', 'role' => 'conductor')
        );

        if ($result !== false) {
            echo '✅ Usuario conductor ya existía, contraseña actualizada<br>';
            echo '<strong>Usuario:</strong> conductor<br>';
            echo '<strong>Contraseña:</strong> conductorbusmedina<br>';
            echo '<a href="' . home_url('/reservas-login/') . '">Ir al login</a>';
        } else {
            echo '❌ Error actualizando la contraseña: ' . $wpdb->last_error;
        }
    }
}

// ✅ FUNCIÓN PARA VERIFICAR QUE EL CONDUCTOR PUEDE INICIAR SESIÓN
add_action('wp_ajax_test_conductor_login', 'test_conductor_login');
add_action('wp_ajax_nopriv_test_conductor_login', 'test_conductor_login');

function test_conductor_login()
{
    global $wpdb;
    $table_users = $wpdb->prefix . 'reservas_users';

    $user = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table_users WHERE username = %s AND role = 'conductor'",
        'conductor'
    ));

    if ($user) {
        echo '✅ Usuario conductor encontrado en la base de datos<br>';
        echo '<strong>ID:</strong> ' . $user->id . '<br>';
        echo '<strong>Username:</strong> ' . $user->username . '<br>';
        echo '<strong>Email:</strong> ' . $user->email . '<br>';
        echo '<strong>Role:</strong> ' . $user->role . '<br>';
        echo '<strong>Status:</strong> ' . $user->status . '<br>';
        echo '<strong>Creado:</strong> ' . $user->created_at . '<br>';

        // Verificar contraseña
        $password_check = password_verify('conductorbusmedina', $user->password);
        echo '<strong>Contraseña verificada:</strong> ' . ($password_check ? '✅ Correcta' : '❌ Incorrecta') . '<br>';

        echo '<br><a href="' . home_url('/reservas-login/') . '">Probar login manual</a>';
    } else {
        echo '❌ Usuario conductor no encontrado en la base de datos<br>';
        echo '<a href="' . admin_url('admin-ajax.php?action=create_conductor_user_manual&force=1') . '">Crear usuario conductor</a>';
    }
}

add_action('wp_ajax_emergency_create_conductor', 'emergency_create_conductor');
add_action('wp_ajax_nopriv_emergency_create_conductor', 'emergency_create_conductor');

function emergency_create_conductor()
{
    global $wpdb;
    $table_users = $wpdb->prefix . 'reservas_users';

    echo '<h2>🔧 Creación de Usuario Conductor</h2>';

    // Verificar tabla
    $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_users'") == $table_users;
    echo '<p><strong>Tabla existe:</strong> ' . ($table_exists ? '✅ SÍ' : '❌ NO') . '</p>';

    if (!$table_exists) {
        echo '<p style="color: red;">❌ La tabla de usuarios no existe. Reactiva el plugin.</p>';
        return;
    }

    // Buscar conductor existente
    $existing = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table_users WHERE username = %s",
        'conductor'
    ));

    if ($existing) {
        echo '<p><strong>Usuario encontrado:</strong> ✅ SÍ</p>';
        echo '<p><strong>ID:</strong> ' . $existing->id . '</p>';
        echo '<p><strong>Role:</strong> ' . $existing->role . '</p>';
        echo '<p><strong>Status:</strong> ' . $existing->status . '</p>';
        echo '<p><strong>Email:</strong> ' . $existing->email . '</p>';
        echo '<p><strong>Creado:</strong> ' . $existing->created_at . '</p>';

        // Actualizar contraseña
        $update_result = $wpdb->update(
            $table_users,
            array('password' => password_hash('conductor', PASSWORD_DEFAULT)),
            array('id' => $existing->id)
        );

        echo '<p><strong>Contraseña actualizada:</strong> ' . ($update_result !== false ? '✅ SÍ' : '❌ NO') . '</p>';

        if ($update_result === false) {
            echo '<p style="color: red;">Error: ' . $wpdb->last_error . '</p>';
        }
    } else {
        echo '<p><strong>Usuario encontrado:</strong> ❌ NO - Creando...</p>';

        $insert_result = $wpdb->insert(
            $table_users,
            array(
                'username' => 'conductor',
                'email' => 'conductor@' . parse_url(home_url(), PHP_URL_HOST),
                'password' => password_hash('conductor', PASSWORD_DEFAULT),
                'role' => 'conductor',
                'status' => 'active',
                'created_at' => current_time('mysql')
            )
        );

        echo '<p><strong>Usuario creado:</strong> ' . ($insert_result ? '✅ SÍ' : '❌ NO') . '</p>';

        if (!$insert_result) {
            echo '<p style="color: red;">Error: ' . $wpdb->last_error . '</p>';
        } else {
            echo '<p style="color: green;">✅ Usuario conductor creado correctamente con ID: ' . $wpdb->insert_id . '</p>';
        }
    }

    echo '<hr>';
    echo '<h3>📋 Credenciales del Conductor</h3>';
    echo '<p><strong>Usuario:</strong> <code>conductor</code></p>';
    echo '<p><strong>Contraseña:</strong> <code>conductor</code></p>';
    echo '<p><strong>URL de Login:</strong> <a href="' . home_url('/reservas-login/') . '" target="_blank">' . home_url('/reservas-login/') . '</a></p>';

    exit;
}

add_action('wp_ajax_force_create_visitas_table', 'force_create_visitas_table');
add_action('wp_ajax_nopriv_force_create_visitas_table', 'force_create_visitas_table');

function force_create_visitas_table()
{
    global $wpdb;
    $table_visitas = $wpdb->prefix . 'reservas_visitas';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_visitas (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        localizador varchar(20) NOT NULL UNIQUE,
        service_id mediumint(9) NOT NULL,
        agency_id mediumint(9) NOT NULL,
        fecha date NOT NULL,
        hora time NOT NULL,
        nombre varchar(100) NOT NULL,
        apellidos varchar(100) NOT NULL,
        email varchar(100) NOT NULL,
        telefono varchar(20) NOT NULL,
        adultos int(11) DEFAULT 0,
        ninos int(11) DEFAULT 0,
        ninos_menores int(11) DEFAULT 0,
        total_personas int(11) NOT NULL,
        precio_total decimal(10,2) NOT NULL,
        estado enum('pendiente', 'confirmada', 'cancelada') DEFAULT 'confirmada',
        metodo_pago varchar(50) DEFAULT 'pendiente_tpv',
        transaction_id varchar(100) NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY service_id (service_id),
        KEY agency_id (agency_id),
        KEY fecha (fecha),
        KEY estado (estado),
        KEY localizador (localizador)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_visitas'") == $table_visitas;

    if ($table_exists) {
        echo '✅ Tabla wp_reservas_visitas creada correctamente';
    } else {
        echo '❌ Error: La tabla no se pudo crear. Error: ' . $wpdb->last_error;
    }

    exit;
}


// ✅ FUNCIÓN TEMPORAL PARA ACTUALIZAR TABLA
add_action('wp_ajax_force_update_services_table', 'force_update_services_table_manual');
function force_update_services_table_manual()
{
    if (!current_user_can('administrator')) {
        wp_die('Sin permisos');
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'reservas_agency_services';

    // Verificar si existe el campo
    $column_exists = $wpdb->get_results("SHOW COLUMNS FROM $table_name LIKE 'horarios_disponibles'");

    if (empty($column_exists)) {
        $result = $wpdb->query("ALTER TABLE $table_name ADD COLUMN horarios_disponibles TEXT NULL AFTER dias_disponibles");

        if ($result !== false) {
            echo '✅ Columna horarios_disponibles añadida correctamente';
        } else {
            echo '❌ Error: ' . $wpdb->last_error;
        }
    } else {
        echo 'ℹ️ La columna ya existe';
    }

    // Verificar estructura final
    $columns = $wpdb->get_results("SHOW COLUMNS FROM $table_name");
    echo '<h3>Estructura actual de la tabla:</h3><pre>';
    print_r($columns);
    echo '</pre>';

    exit;
}



// Inicializar el plugin
new SistemaReservas();
