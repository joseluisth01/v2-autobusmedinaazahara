<?php

/**
 * Clase para gestionar los descuentos del sistema de reservas
 * Archivo: wp-content/plugins/sistema-reservas/includes/class-discounts-admin.php
 */
class ReservasDiscountsAdmin
{

    public function __construct()
    {
        // Hooks AJAX para descuentos
        add_action('wp_ajax_get_discount_rules', array($this, 'get_discount_rules'));
        add_action('wp_ajax_nopriv_get_discount_rules', array($this, 'get_discount_rules'));

        add_action('wp_ajax_save_discount_rule', array($this, 'save_discount_rule'));
        add_action('wp_ajax_nopriv_save_discount_rule', array($this, 'save_discount_rule'));

        add_action('wp_ajax_delete_discount_rule', array($this, 'delete_discount_rule'));
        add_action('wp_ajax_nopriv_delete_discount_rule', array($this, 'delete_discount_rule'));

        add_action('wp_ajax_get_discount_rule_details', array($this, 'get_discount_rule_details'));
        add_action('wp_ajax_nopriv_get_discount_rule_details', array($this, 'get_discount_rule_details'));

        // Hook para activación del plugin (crear tabla)
        add_action('init', array($this, 'maybe_create_table'));
    }

    /**
     * Crear tabla de descuentos si no existe
     */
    public function maybe_create_table()
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'reservas_discount_rules';

        // Verificar si la tabla existe
        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name;

        if (!$table_exists) {
            $this->create_discount_table();
        }
    }

    /**
     * Crear tabla de reglas de descuento
     */
    private function create_discount_table()
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'reservas_discount_rules';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
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
        dbDelta($sql);

        // Crear regla por defecto si no existe ninguna
        $this->create_default_rule();
    }

    /**
     * Crear regla de descuento por defecto
     */
    private function create_default_rule()
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

    /**
     * Obtener todas las reglas de descuento
     */
    public function get_discount_rules()
    {
        // ✅ DEBUGGING MEJORADO
        error_log('=== DISCOUNT RULES AJAX REQUEST START ===');
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
            $table_name = $wpdb->prefix . 'reservas_discount_rules';

            $rules = $wpdb->get_results(
                "SELECT * FROM $table_name ORDER BY minimum_persons ASC, discount_percentage DESC"
            );

            if ($wpdb->last_error) {
                error_log('❌ Database error in discount rules: ' . $wpdb->last_error);
                die(json_encode(['success' => false, 'data' => 'Database error: ' . $wpdb->last_error]));
            }

            error_log('✅ Found ' . count($rules) . ' discount rules');
            die(json_encode(['success' => true, 'data' => $rules]));
        } catch (Exception $e) {
            error_log('❌ DISCOUNT RULES EXCEPTION: ' . $e->getMessage());
            die(json_encode(['success' => false, 'data' => 'Server error: ' . $e->getMessage()]));
        }
    }
    /**
     * Guardar regla de descuento (crear o actualizar)
     */
    public function save_discount_rule()
    {
        // ✅ DEBUGGING MEJORADO
        error_log('=== SAVE DISCOUNT RULE AJAX REQUEST START ===');
        header('Content-Type: application/json');

        try {
            // ✅ VERIFICACIÓN SIMPLIFICADA TEMPORAL
            if (!session_id()) {
                session_start();
            }

            error_log('=== SAVE DISCOUNT RULE DEBUG ===');
            error_log('Session data: ' . print_r($_SESSION ?? [], true));
            error_log('POST data: ' . print_r($_POST, true));

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
            $table_name = $wpdb->prefix . 'reservas_discount_rules';

            // Sanitizar datos
            $rule_name = sanitize_text_field($_POST['rule_name']);
            $minimum_persons = intval($_POST['minimum_persons']);
            $discount_percentage = floatval($_POST['discount_percentage']);
            $apply_to = sanitize_text_field($_POST['apply_to']);
            $rule_description = sanitize_textarea_field($_POST['rule_description']);
            $is_active = isset($_POST['is_active']) ? 1 : 0;
            $rule_id = isset($_POST['discount_id']) ? intval($_POST['discount_id']) : 0;

            // Validaciones
            if (empty($rule_name)) {
                wp_send_json_error('El nombre de la regla es obligatorio');
            }

            if ($minimum_persons < 1 || $minimum_persons > 1000) {
                wp_send_json_error('El mínimo de personas debe estar entre 1 y 1000');
            }

            if ($discount_percentage < 0.1 || $discount_percentage > 100) {
                wp_send_json_error('El porcentaje debe estar entre 0.1% y 100%');
            }

            $valid_apply_to = array('total', 'adults_only', 'all_paid');
            if (!in_array($apply_to, $valid_apply_to)) {
                wp_send_json_error('Valor de "aplicar a" no válido');
            }

            $data = array(
                'rule_name' => $rule_name,
                'minimum_persons' => $minimum_persons,
                'discount_percentage' => $discount_percentage,
                'apply_to' => $apply_to,
                'rule_description' => $rule_description,
                'is_active' => $is_active
            );

            if ($rule_id > 0) {
                // Actualizar regla existente
                $result = $wpdb->update($table_name, $data, array('id' => $rule_id));

                if ($result !== false) {
                    wp_send_json_success('Regla actualizada correctamente');
                } else {
                    wp_send_json_error('Error al actualizar la regla: ' . $wpdb->last_error);
                }
            } else {
                // Verificar que no exista una regla similar
                $existing = $wpdb->get_var($wpdb->prepare(
                    "SELECT COUNT(*) FROM $table_name WHERE minimum_persons = %d AND is_active = 1",
                    $minimum_persons
                ));

                if ($existing > 0) {
                    wp_send_json_error('Ya existe una regla activa para ese número mínimo de personas');
                }

                // Crear nueva regla
                $result = $wpdb->insert($table_name, $data);

                if ($result !== false) {
                    wp_send_json_success('Regla creada correctamente');
                } else {
                    wp_send_json_error('Error al crear la regla: ' . $wpdb->last_error);
                }
            }
        } catch (Exception $e) {
            error_log('❌ SAVE DISCOUNT RULE EXCEPTION: ' . $e->getMessage());
            wp_send_json_error('Server error: ' . $e->getMessage());
        }
    }

    /**
     * Obtener detalles de una regla específica
     */
    public function get_discount_rule_details()
    {
        // ✅ VERIFICACIÓN SIMPLIFICADA TEMPORAL
        if (!session_id()) {
            session_start();
        }

        error_log('=== SAVE DISCOUNT RULE DEBUG ===');
        error_log('Session data: ' . print_r($_SESSION ?? [], true));
        error_log('POST data: ' . print_r($_POST, true));

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
        $table_name = $wpdb->prefix . 'reservas_discount_rules';

        $rule_id = intval($_POST['rule_id']);

        $rule = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE id = %d",
            $rule_id
        ));

        if ($rule) {
            wp_send_json_success($rule);
        } else {
            wp_send_json_error('Regla no encontrada');
        }
    }

    /**
     * Eliminar regla de descuento
     */
    public function delete_discount_rule()
    {
        // ✅ VERIFICACIÓN SIMPLIFICADA TEMPORAL
        if (!session_id()) {
            session_start();
        }

        error_log('=== SAVE DISCOUNT RULE DEBUG ===');
        error_log('Session data: ' . print_r($_SESSION ?? [], true));
        error_log('POST data: ' . print_r($_POST, true));

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
        $table_name = $wpdb->prefix . 'reservas_discount_rules';

        $rule_id = intval($_POST['rule_id']);

        $result = $wpdb->delete($table_name, array('id' => $rule_id));

        if ($result !== false) {
            wp_send_json_success('Regla eliminada correctamente');
        } else {
            wp_send_json_error('Error al eliminar la regla');
        }
    }

    /**
     * Método estático para calcular descuentos
     * 
     * @param int $total_persons Total de personas que ocupan plaza
     * @param float $base_amount Cantidad base sobre la que aplicar descuento
     * @param string $apply_to Tipo de aplicación (total, adults_only, all_paid)
     * @return array Array con información del descuento aplicado
     */
    public static function calculate_discount($total_persons, $base_amount, $apply_to = 'total')
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'reservas_discount_rules';

        // Buscar reglas aplicables (activas y con mínimo de personas menor o igual al total)
        $applicable_rules = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_name 
             WHERE is_active = 1 
             AND minimum_persons <= %d 
             AND apply_to = %s
             ORDER BY minimum_persons DESC, discount_percentage DESC
             LIMIT 1",
            $total_persons,
            $apply_to
        ));

        if (empty($applicable_rules)) {
            return array(
                'discount_applied' => false,
                'discount_amount' => 0,
                'discount_percentage' => 0,
                'rule_name' => '',
                'minimum_persons' => 0
            );
        }

        $rule = $applicable_rules[0];
        $discount_amount = ($base_amount * $rule->discount_percentage) / 100;

        return array(
            'discount_applied' => true,
            'discount_amount' => $discount_amount,
            'discount_percentage' => $rule->discount_percentage,
            'rule_name' => $rule->rule_name,
            'minimum_persons' => $rule->minimum_persons,
            'rule_id' => $rule->id
        );
    }

    /**
     * Método estático para obtener todas las reglas activas
     */
    public static function get_active_rules()
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'reservas_discount_rules';

        return $wpdb->get_results(
            "SELECT * FROM $table_name 
             WHERE is_active = 1 
             ORDER BY minimum_persons ASC"
        );
    }
}
