<?php

/**
 * Clase para gestionar el perfil de agencias
 * Archivo: wp-content/plugins/sistema-reservas/includes/class-agency-profile-admin.php
 */
class ReservasAgencyProfileAdmin
{

    public function __construct()
    {
        // Hooks AJAX para gestión de perfil de agencias
        add_action('wp_ajax_get_agency_profile', array($this, 'get_agency_profile'));
        add_action('wp_ajax_nopriv_get_agency_profile', array($this, 'get_agency_profile'));

        add_action('wp_ajax_save_agency_profile', array($this, 'save_agency_profile'));
        add_action('wp_ajax_nopriv_save_agency_profile', array($this, 'save_agency_profile'));

        add_action('wp_ajax_refresh_session_data', array($this, 'refresh_session_data'));
        add_action('wp_ajax_nopriv_refresh_session_data', array($this, 'refresh_session_data'));

        add_action('wp_ajax_get_agency_visitas_config', array($this, 'get_agency_visitas_config'));
        add_action('wp_ajax_nopriv_get_agency_visitas_config', array($this, 'get_agency_visitas_config'));

        add_action('wp_ajax_toggle_visita_horario', array($this, 'toggle_visita_horario'));
        add_action('wp_ajax_nopriv_toggle_visita_horario', array($this, 'toggle_visita_horario'));
    }

    /**
     * Obtener datos del perfil de la agencia actual
     */
    public function get_agency_profile()
    {
        error_log('=== GET AGENCY PROFILE START ===');
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

            // Solo las agencias pueden acceder a su perfil
            if ($user['role'] !== 'agencia') {
                wp_send_json_error('Sin permisos para acceder al perfil de agencia');
                return;
            }

            global $wpdb;
            $table_name = $wpdb->prefix . 'reservas_agencies';

            $agency = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM $table_name WHERE id = %d",
                $user['id']
            ));

            if (!$agency) {
                wp_send_json_error('Agencia no encontrada');
                return;
            }

            // No enviar la contraseña por seguridad
            unset($agency->password);

            error_log('✅ Agency profile loaded successfully');
            wp_send_json_success($agency);
        } catch (Exception $e) {
            error_log('❌ GET AGENCY PROFILE EXCEPTION: ' . $e->getMessage());
            wp_send_json_error('Error del servidor: ' . $e->getMessage());
        }
    }

    /**
     * Obtener configuración de visitas guiadas de la agencia
     */
    public function get_agency_visitas_config()
    {
        error_log('=== GET AGENCY VISITAS CONFIG ===');

        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'reservas_nonce')) {
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
        $table_services = $wpdb->prefix . 'reservas_agency_services';
        $table_disabled = $wpdb->prefix . 'reservas_agency_horarios_disabled';
        $agency_id = $_SESSION['reservas_user']['id'];

        $service = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_services WHERE agency_id = %d",
            $agency_id
        ));

        if (!$service || $service->servicio_activo != 1) {
            wp_send_json_success(array(
                'has_service' => false,
                'message' => 'No tienes visitas guiadas configuradas'
            ));
            return;
        }

        // ✅ OBTENER HORARIOS DESHABILITADOS
        $disabled_horarios = $wpdb->get_results($wpdb->prepare(
            "SELECT dia, hora FROM $table_disabled WHERE agency_id = %d",
            $agency_id
        ), ARRAY_A);

        error_log('Horarios deshabilitados: ' . print_r($disabled_horarios, true));

        wp_send_json_success(array(
            'has_service' => true,
            'service' => $service,
            'disabled_horarios' => $disabled_horarios // ✅ AÑADIR ESTO
        ));
    }


    public function toggle_visita_horario()
    {
        error_log('=== TOGGLE VISITA HORARIO ===');

        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'reservas_nonce')) {
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

        $dia = sanitize_text_field($_POST['dia']);
        $hora = sanitize_text_field($_POST['hora']);
        $enable = intval($_POST['enable']); // ✅ 1 = habilitar, 0 = deshabilitar
        $agency_id = $_SESSION['reservas_user']['id'];

        global $wpdb;
        $table_disabled = $wpdb->prefix . 'reservas_agency_horarios_disabled';

        if ($enable) {
            // ✅ HABILITAR: Eliminar de la tabla de deshabilitados
            $result = $wpdb->delete(
                $table_disabled,
                array(
                    'agency_id' => $agency_id,
                    'dia' => $dia,
                    'hora' => $hora
                )
            );

            if ($result !== false) {
                wp_send_json_success("Visita de $dia a las $hora habilitada correctamente");
            } else {
                wp_send_json_error('Error habilitando la visita: ' . $wpdb->last_error);
            }
        } else {
            // ✅ DESHABILITAR: Insertar en la tabla de deshabilitados
            $result = $wpdb->insert(
                $table_disabled,
                array(
                    'agency_id' => $agency_id,
                    'dia' => $dia,
                    'hora' => $hora
                )
            );

            if ($result !== false) {
                wp_send_json_success("Visita de $dia a las $hora deshabilitada correctamente");
            } else {
                // Si falla, puede ser porque ya existe (unique key)
                if (strpos($wpdb->last_error, 'Duplicate') !== false) {
                    wp_send_json_success("Visita ya estaba deshabilitada");
                } else {
                    wp_send_json_error('Error deshabilitando la visita: ' . $wpdb->last_error);
                }
            }
        }
    }


    /**
     * Guardar cambios del perfil de la agencia
     */
    public function save_agency_profile()
    {
        error_log('=== SAVE AGENCY PROFILE START ===');
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
                wp_send_json_error('Sin permisos para modificar el perfil de agencia');
                return;
            }

            global $wpdb;
            $table_name = $wpdb->prefix . 'reservas_agencies';

            // Sanitizar datos (SIN address y notes)
            $agency_name = sanitize_text_field($_POST['agency_name']);
            $contact_person = sanitize_text_field($_POST['contact_person']);
            $email = sanitize_email($_POST['email']);
            $phone = sanitize_text_field($_POST['phone']);
            $email_notificaciones = sanitize_email($_POST['email_notificaciones']);

            // ✅ CAMPOS FISCALES
            $razon_social = sanitize_text_field($_POST['razon_social']);
            $cif = sanitize_text_field($_POST['cif']);
            $domicilio_fiscal = sanitize_text_field($_POST['domicilio_fiscal']); // ✅ Ahora es input, no textarea

            // Validaciones
            if (empty($agency_name) || strlen($agency_name) < 2) {
                wp_send_json_error('El nombre de la agencia debe tener al menos 2 caracteres');
                return;
            }

            if (empty($contact_person) || strlen($contact_person) < 2) {
                wp_send_json_error('El nombre del contacto debe tener al menos 2 caracteres');
                return;
            }

            if (empty($email) || !is_email($email)) {
                wp_send_json_error('Email de contacto no válido');
                return;
            }

            if (!empty($email_notificaciones) && !is_email($email_notificaciones)) {
                wp_send_json_error('El email de notificaciones no es válido');
                return;
            }

            if (!empty($phone) && strlen($phone) < 9) {
                wp_send_json_error('El teléfono debe tener al menos 9 dígitos');
                return;
            }

            // ✅ VALIDACIONES FISCALES
            if (!empty($cif) && strlen($cif) < 8) {
                wp_send_json_error('El CIF debe tener al menos 8 caracteres');
                return;
            }

            if (!empty($razon_social) && strlen($razon_social) < 3) {
                wp_send_json_error('La razón social debe tener al menos 3 caracteres');
                return;
            }

            // Verificar email único
            $existing_email = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM $table_name WHERE email = %s AND id != %d",
                $email,
                $user['id']
            ));

            if ($existing_email > 0) {
                wp_send_json_error('Ya existe otra agencia con ese email');
                return;
            }

            // ✅ DATOS SIN address y notes
            $update_data = array(
                'agency_name' => $agency_name,
                'contact_person' => $contact_person,
                'email' => $email,
                'phone' => $phone,
                'razon_social' => $razon_social,
                'cif' => $cif,
                'domicilio_fiscal' => $domicilio_fiscal,
                'updated_at' => current_time('mysql')
            );

            $column_exists = $wpdb->get_results("SHOW COLUMNS FROM $table_name LIKE 'email_notificaciones'");
            if (!empty($column_exists)) {
                $update_data['email_notificaciones'] = $email_notificaciones;
            }

            // Actualizar en la base de datos
            $result = $wpdb->update(
                $table_name,
                $update_data,
                array('id' => $user['id'])
            );

            if ($result === false) {
                error_log('❌ Database error updating agency profile: ' . $wpdb->last_error);
                wp_send_json_error('Error actualizando el perfil: ' . $wpdb->last_error);
                return;
            }

            // Actualizar datos de sesión
            $_SESSION['reservas_user']['agency_name'] = $agency_name;
            $_SESSION['reservas_user']['email'] = $email;

            error_log('✅ Agency profile updated successfully');
            wp_send_json_success('Perfil actualizado correctamente');
        } catch (Exception $e) {
            error_log('❌ SAVE AGENCY PROFILE EXCEPTION: ' . $e->getMessage());
            wp_send_json_error('Error del servidor: ' . $e->getMessage());
        }
    }

    /**
     * Refrescar datos de sesión
     */
    public function refresh_session_data()
    {
        error_log('=== REFRESH SESSION DATA START ===');
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
                wp_send_json_error('Sesión expirada');
                return;
            }

            $user = $_SESSION['reservas_user'];

            // Solo para agencias
            if ($user['role'] !== 'agencia') {
                wp_send_json_error('Sin permisos');
                return;
            }

            global $wpdb;
            $table_name = $wpdb->prefix . 'reservas_agencies';

            // Obtener datos actualizados
            $agency = $wpdb->get_row($wpdb->prepare(
                "SELECT id, username, agency_name, email, commission_percentage, max_credit_limit, current_balance, status FROM $table_name WHERE id = %d",
                $user['id']
            ));

            if ($agency) {
                // Actualizar datos de sesión
                $_SESSION['reservas_user'] = array(
                    'id' => $agency->id,
                    'username' => $agency->username,
                    'agency_name' => $agency->agency_name,
                    'email' => $agency->email,
                    'role' => 'agencia',
                    'commission_percentage' => $agency->commission_percentage,
                    'max_credit_limit' => $agency->max_credit_limit,
                    'current_balance' => $agency->current_balance,
                    'status' => $agency->status,
                    'user_type' => 'agency',
                    'login_time' => $user['login_time'] // Mantener tiempo de login original
                );

                error_log('✅ Session data refreshed successfully');
                wp_send_json_success('Datos de sesión actualizados');
            } else {
                wp_send_json_error('Agencia no encontrada');
            }
        } catch (Exception $e) {
            error_log('❌ REFRESH SESSION DATA EXCEPTION: ' . $e->getMessage());
            wp_send_json_error('Error del servidor: ' . $e->getMessage());
        }
    }

    /**
     * Método estático para obtener datos de perfil de agencia por ID
     */
    public static function get_agency_profile_by_id($agency_id)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'reservas_agencies';

        $agency = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE id = %d",
            $agency_id
        ));

        if ($agency) {
            // No incluir contraseña
            unset($agency->password);
        }

        return $agency;
    }

    /**
     * Método estático para validar datos de perfil
     */
    public static function validate_profile_data($data)
    {
        $errors = array();

        // Validar nombre de agencia
        if (empty($data['agency_name']) || strlen($data['agency_name']) < 2) {
            $errors[] = 'El nombre de la agencia debe tener al menos 2 caracteres';
        }

        // Validar persona de contacto
        if (empty($data['contact_person']) || strlen($data['contact_person']) < 2) {
            $errors[] = 'El nombre del contacto debe tener al menos 2 caracteres';
        }

        // Validar email principal
        if (empty($data['email']) || !is_email($data['email'])) {
            $errors[] = 'Email de contacto no válido';
        }

        // Validar email de notificaciones si está presente
        if (!empty($data['email_notificaciones']) && !is_email($data['email_notificaciones'])) {
            $errors[] = 'El email de notificaciones no es válido';
        }

        // Validar teléfono si está presente
        if (!empty($data['phone']) && strlen($data['phone']) < 9) {
            $errors[] = 'El teléfono debe tener al menos 9 dígitos';
        }

        return array(
            'valid' => empty($errors),
            'errors' => $errors
        );
    }

    /**
     * Método estático para verificar si el email está disponible
     */
    public static function is_email_available($email, $exclude_agency_id = null)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'reservas_agencies';

        $query = "SELECT COUNT(*) FROM $table_name WHERE email = %s";
        $params = array($email);

        if ($exclude_agency_id) {
            $query .= " AND id != %d";
            $params[] = $exclude_agency_id;
        }

        $count = $wpdb->get_var($wpdb->prepare($query, $params));

        return $count == 0;
    }

    /**
     * Método estático para obtener historial de cambios (futuro)
     */
    public static function log_profile_change($agency_id, $field, $old_value, $new_value, $changed_by = null)
    {
        // Implementar sistema de logging de cambios si es necesario
        error_log("PROFILE CHANGE - Agency ID: $agency_id, Field: $field, Old: $old_value, New: $new_value, Changed by: $changed_by");
    }
}
