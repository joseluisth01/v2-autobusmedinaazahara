<?php

/**
 * Clase para procesar reservas - CON ENVÍO DE EMAILS
 * Archivo: wp-content/plugins/sistema-reservas/includes/class-reservas-processor.php
 */

class ReservasProcessor
{

    public function __construct()
    {
        // Hooks AJAX para procesar reservas
        add_action('wp_ajax_process_reservation', array($this, 'process_reservation'));
        add_action('wp_ajax_nopriv_process_reservation', array($this, 'process_reservation'));
    }

    /**
     * Procesar una nueva reserva - CON EMAILS
     */
public function process_reservation()
{
    // Limpiar cualquier output buffer que pueda interferir
    if (ob_get_level()) {
        ob_clean();
    }

    // Headers para asegurar JSON correcto
    header('Content-Type: application/json');

    try {
        error_log('=== INICIANDO PROCESS_RESERVATION CON EMAILS ===');

        // Verificar nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'reservas_nonce')) {
            wp_send_json_error('Error de seguridad');
            return;
        }

        // Validar y sanitizar datos del formulario
        $datos_personales = $this->validar_datos_personales();
        if (!$datos_personales['valido']) {
            wp_send_json_error($datos_personales['error']);
            return;
        }

        // Validar y sanitizar datos de la reserva
        $datos_reserva = $this->validar_datos_reserva();
        if (!$datos_reserva['valido']) {
            wp_send_json_error($datos_reserva['error']);
            return;
        }

        // Verificar disponibilidad del servicio
        $servicio = $this->verificar_disponibilidad($datos_reserva['datos']['service_id'], $datos_reserva['datos']['total_personas']);
        if (!$servicio['disponible']) {
            wp_send_json_error($servicio['error']);
            return;
        }

        // Recalcular precios
        $calculo_precio = $this->recalcular_precio($datos_reserva['datos']);
        if (!$calculo_precio['valido']) {
            wp_send_json_error($calculo_precio['error']);
            return;
        }

        // ✅ CREAR LA RESERVA DIRECTAMENTE Y REDIRIGIR
        $resultado_reserva = $this->crear_reserva(
            $datos_personales['datos'],
            $datos_reserva['datos'],
            $calculo_precio['precio']
        );

        if (!$resultado_reserva['exito']) {
            wp_send_json_error($resultado_reserva['error']);
            return;
        }

        // Actualizar plazas disponibles
        $actualizacion = $this->actualizar_plazas_disponibles(
            $datos_reserva['datos']['service_id'],
            $datos_reserva['datos']['total_personas']
        );

        if (!$actualizacion['exito']) {
            $this->eliminar_reserva($resultado_reserva['reserva_id']);
            wp_send_json_error('Error actualizando disponibilidad. Reserva cancelada.');
            return;
        }

        // ✅ ENVIAR EMAILS DE CONFIRMACIÓN
        $this->send_confirmation_emails($resultado_reserva['reserva_id']);

        // ✅ RESPUESTA CORREGIDA CON LOCALIZADOR EN URL
        $response_data = array(
            'mensaje' => 'Reserva procesada correctamente',
            'redirect_url' => home_url('/confirmacion-reserva/?localizador=' . $resultado_reserva['localizador']),
            'localizador' => $resultado_reserva['localizador'],
            'reserva_id' => $resultado_reserva['reserva_id'],
            'detalles' => array(
                'fecha' => $datos_reserva['datos']['fecha'],
                'hora' => $datos_reserva['datos']['hora_ida'],
                'personas' => $datos_reserva['datos']['total_personas'],
                'precio_final' => $calculo_precio['precio']['precio_final']
            )
        );

        error_log('SUCCESS: Reserva completada directamente, redirigiendo a confirmación con localizador: ' . $resultado_reserva['localizador']);
        wp_send_json_success($response_data);
        
    } catch (Exception $e) {
        error_log('EXCEPTION: ' . $e->getMessage());
        wp_send_json_error('Error interno del servidor: ' . $e->getMessage());
    }
}

    /**
     * ✅ FUNCIÓN PARA ENVIAR EMAILS DE CONFIRMACIÓN
     */
    private function send_confirmation_emails($reserva_id)
    {
        error_log('=== ENVIANDO EMAILS DE CONFIRMACIÓN ===');

        // Cargar clase de emails
        if (!class_exists('ReservasEmailService')) {
            require_once RESERVAS_PLUGIN_PATH . 'includes/class-email-service.php';
        }

        // Obtener datos de la reserva
        global $wpdb;
        $table_reservas = $wpdb->prefix . 'reservas_reservas';

        $reserva = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_reservas WHERE id = %d",
            $reserva_id
        ));

        if (!$reserva) {
            error_log('ERROR: No se encontró la reserva para enviar emails');
            return;
        }

        // Convertir a array
        $reserva_array = (array) $reserva;

        // Enviar email al cliente
        $customer_result = ReservasEmailService::send_customer_confirmation($reserva_array);
        if ($customer_result['success']) {
            error_log('✅ Email enviado al cliente correctamente');
        } else {
            error_log('❌ Error enviando email al cliente: ' . $customer_result['message']);
        }

        // Enviar email al administrador
        $admin_result = ReservasEmailService::send_admin_notification($reserva_array);
        if ($admin_result['success']) {
            error_log('✅ Email enviado al administrador correctamente');
        } else {
            error_log('❌ Error enviando email al administrador: ' . $admin_result['message']);
        }
    }

    /**
     * Validar datos personales del formulario
     */
    private function validar_datos_personales()
    {
        error_log('=== VALIDANDO DATOS PERSONALES ===');

        $nombre = sanitize_text_field($_POST['nombre'] ?? '');
        $apellidos = sanitize_text_field($_POST['apellidos'] ?? '');
        $email = sanitize_email($_POST['email'] ?? '');
        $telefono = sanitize_text_field($_POST['telefono'] ?? '');

        error_log("Datos recibidos - Nombre: '$nombre', Apellidos: '$apellidos', Email: '$email', Teléfono: '$telefono'");

        // Validaciones
        if (empty($nombre) || strlen($nombre) < 2) {
            return array('valido' => false, 'error' => 'El nombre es obligatorio (mínimo 2 caracteres)');
        }

        if (empty($apellidos) || strlen($apellidos) < 2) {
            return array('valido' => false, 'error' => 'Los apellidos son obligatorios (mínimo 2 caracteres)');
        }

        if (empty($email) || !is_email($email)) {
            return array('valido' => false, 'error' => 'Email no válido');
        }

        if (empty($telefono) || strlen($telefono) < 9) {
            return array('valido' => false, 'error' => 'Teléfono no válido (mínimo 9 dígitos)');
        }

        return array(
            'valido' => true,
            'datos' => array(
                'nombre' => $nombre,
                'apellidos' => $apellidos,
                'email' => $email,
                'telefono' => $telefono
            )
        );
    }

    /**
     * Validar datos de reserva desde sessionStorage
     */
    private function validar_datos_reserva()
    {
        error_log('=== VALIDANDO DATOS DE RESERVA ===');

        // Verificar que tenemos los datos de reserva
        if (!isset($_POST['reservation_data'])) {
            return array('valido' => false, 'error' => 'Faltan datos de reserva');
        }

        // Decodificar datos de reserva
        $reserva_data_json = stripslashes($_POST['reservation_data']);
        error_log('JSON recibido: ' . $reserva_data_json);

        $reserva_data = json_decode($reserva_data_json, true);

        if (!$reserva_data || json_last_error() !== JSON_ERROR_NONE) {
            error_log('ERROR JSON: ' . json_last_error_msg());
            return array('valido' => false, 'error' => 'Datos de reserva no válidos - JSON corrupto');
        }

        error_log('Datos de reserva decodificados: ' . print_r($reserva_data, true));

        // Validar campos obligatorios
        $campos_requeridos = ['fecha', 'service_id', 'hora_ida', 'adultos', 'residentes', 'ninos_5_12', 'ninos_menores'];
        foreach ($campos_requeridos as $campo) {
            if (!isset($reserva_data[$campo])) {
                return array('valido' => false, 'error' => "Campo '$campo' faltante en datos de reserva");
            }
        }

        // Calcular total de personas que ocupan plaza
        $adultos = intval($reserva_data['adultos']);
        $residentes = intval($reserva_data['residentes']);
        $ninos_5_12 = intval($reserva_data['ninos_5_12']);
        $ninos_menores = intval($reserva_data['ninos_menores']);
        $total_personas = $adultos + $residentes + $ninos_5_12; // Los menores de 5 no ocupan plaza

        error_log("Personas calculadas - Adultos: $adultos, Residentes: $residentes, Niños 5-12: $ninos_5_12, Menores: $ninos_menores, Total con plaza: $total_personas");

        if ($total_personas <= 0) {
            return array('valido' => false, 'error' => 'Debe haber al menos una persona que ocupe plaza');
        }

        if ($ninos_5_12 > 0 && ($adultos + $residentes) <= 0) {
            return array('valido' => false, 'error' => 'Debe haber al menos un adulto si hay niños');
        }

        // Agregar totales calculados
        $reserva_data['total_personas'] = $total_personas;
        $reserva_data['total_viajeros'] = $adultos + $residentes + $ninos_5_12 + $ninos_menores;

        return array('valido' => true, 'datos' => $reserva_data);
    }

    /**
     * Verificar disponibilidad del servicio
     */
    private function verificar_disponibilidad($service_id, $personas_necesarias)
    {
        error_log('=== VERIFICANDO DISPONIBILIDAD ===');
        error_log("Service ID: $service_id, Personas necesarias: $personas_necesarias");

        global $wpdb;

        $table_servicios = $wpdb->prefix . 'reservas_servicios';

        $servicio = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_servicios WHERE id = %d AND status = 'active'",
            $service_id
        ));

        if (!$servicio) {
            return array('disponible' => false, 'error' => 'Servicio no encontrado');
        }

        error_log('Servicio encontrado: ' . print_r($servicio, true));

        if ($servicio->plazas_disponibles < $personas_necesarias) {
            return array(
                'disponible' => false,
                'error' => "Solo quedan {$servicio->plazas_disponibles} plazas disponibles, necesitas {$personas_necesarias}"
            );
        }

        // ✅ NUEVO: VALIDACIÓN MEJORADA QUE PERMITE RESERVAR EL MISMO DÍA SI NO HA PASADO LA HORA
        $fecha_servicio = $servicio->fecha;
        $hora_servicio = $servicio->hora;
        $fecha_hoy = date('Y-m-d');
        $hora_actual = date('H:i:s');

        // No permitir fechas pasadas
        if ($fecha_servicio < $fecha_hoy) {
            return array('disponible' => false, 'error' => 'No se puede reservar para fechas pasadas');
        }

        // Si es hoy, verificar que la hora no haya pasado
        if ($fecha_servicio === $fecha_hoy && $hora_servicio <= $hora_actual) {
            return array('disponible' => false, 'error' => 'No se puede reservar para horarios que ya han pasado');
        }

        return array('disponible' => true, 'servicio' => $servicio);
    }

    /**
     * Procesar reserva cuando el pago viene de Redsys
     */
    public function process_reservation_payment($data)
{
    error_log('=== PROCESANDO RESERVA CON PAGO REDSYS ===');
    error_log('Datos recibidos: ' . print_r($data, true));

    try {
        global $wpdb;

        // Decodificar datos de reserva
        $reservation_data = json_decode($data['reservation_data'], true);
        if (!$reservation_data) {
            throw new Exception('Datos de reserva inválidos');
        }

        error_log('Datos de reserva decodificados: ' . print_r($reservation_data, true));

        // ✅ VERIFICAR DISPONIBILIDAD ANTES DE PROCESAR
        $verification = $this->verificar_disponibilidad(
            $reservation_data['service_id'],
            ($reservation_data['adultos'] + $reservation_data['residentes'] + $reservation_data['ninos_5_12'])
        );

        if (!$verification['disponible']) {
            throw new Exception('Servicio ya no disponible: ' . $verification['error']);
        }

        // Obtener datos del servicio
        $table_servicios = $wpdb->prefix . 'reservas_servicios';
        $servicio = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_servicios WHERE id = %d",
            $reservation_data['service_id']
        ));

        if (!$servicio) {
            throw new Exception('Servicio no encontrado');
        }

        // Generar localizador
        $localizador = $this->generar_localizador();
        error_log('✅ Localizador generado: ' . $localizador);

        // ✅ PREPARAR DATOS PARA INSERTAR
        $total_personas = intval($reservation_data['adultos']) + intval($reservation_data['residentes']) + intval($reservation_data['ninos_5_12']);

        $reserva_insert = array(
            'localizador' => $localizador,
            'redsys_order_id' => $data['order_id'] ?? null,
            'servicio_id' => $reservation_data['service_id'],
            'fecha' => $reservation_data['fecha'],
            'hora' => $reservation_data['hora_ida'],
            'hora_vuelta' => $reservation_data['hora_vuelta'] ?? null,
            'nombre' => $data['nombre'],
            'apellidos' => $data['apellidos'],
            'email' => $data['email'],
            'telefono' => $data['telefono'],
            'adultos' => intval($reservation_data['adultos']),
            'residentes' => intval($reservation_data['residentes']),
            'ninos_5_12' => intval($reservation_data['ninos_5_12']),
            'ninos_menores' => intval($reservation_data['ninos_menores']),
            'total_personas' => $total_personas,
            'precio_base' => floatval($reservation_data['total_price']) + floatval($reservation_data['descuento_grupo'] ?? 0),
            'descuento_total' => floatval($reservation_data['descuento_grupo'] ?? 0),
            'precio_final' => floatval($reservation_data['total_price']),
            'regla_descuento_aplicada' => isset($reservation_data['regla_descuento_aplicada']) ?
                json_encode($reservation_data['regla_descuento_aplicada']) : null,
            'estado' => 'confirmada',
            'metodo_pago' => $data['metodo_pago'] ?? 'redsys',
            'created_at' => current_time('mysql')
        );

        error_log('✅ Datos preparados para insertar: ' . print_r($reserva_insert, true));

        // ✅ INSERTAR RESERVA
        $table_reservas = $wpdb->prefix . 'reservas_reservas';
        $result = $wpdb->insert($table_reservas, $reserva_insert);

        if ($result === false) {
            throw new Exception('Error insertando reserva: ' . $wpdb->last_error);
        }

        $reserva_id = $wpdb->insert_id;
        error_log('✅ Reserva insertada con ID: ' . $reserva_id);

        // ✅ ACTUALIZAR PLAZAS DISPONIBLES
        $update_result = $wpdb->query($wpdb->prepare(
            "UPDATE $table_servicios SET plazas_disponibles = plazas_disponibles - %d WHERE id = %d",
            $total_personas,
            $reservation_data['service_id']
        ));

        if ($update_result === false) {
            // Si falla la actualización, eliminar la reserva
            $wpdb->delete($table_reservas, array('id' => $reserva_id));
            throw new Exception('Error actualizando plazas disponibles');
        }

        error_log('✅ Plazas actualizadas correctamente');

        // ✅ PREPARAR DATOS COMPLETOS PARA EMAILS
        $reserva_completa = array_merge($reserva_insert, array(
            'id' => $reserva_id,
            'precio_adulto' => $servicio->precio_adulto,
            'precio_nino' => $servicio->precio_nino,
            'precio_residente' => $servicio->precio_residente
        ));

        // ✅ ENVIAR EMAILS
        error_log('✅ Enviando emails de confirmación...');
        $this->send_confirmation_emails_array($reserva_completa);

        // ✅ PREPARAR RESPUESTA
        $response_data = array(
            'localizador' => $localizador,
            'reserva_id' => $reserva_id,
            'detalles' => array(
                'fecha' => $reservation_data['fecha'],
                'hora' => $reservation_data['hora_ida'],
                'personas' => $total_personas,
                'precio_final' => $reservation_data['total_price']
            )
        );

        error_log('✅ Reserva procesada exitosamente: ' . $localizador);

        return array(
            'success' => true,
            'data' => $response_data
        );

    } catch (Exception $e) {
        error_log('❌ Error procesando reserva: ' . $e->getMessage());
        return array(
            'success' => false,
            'message' => $e->getMessage()
        );
    }
}

private function send_confirmation_emails_array($reserva_array)
{
    error_log('=== ENVIANDO EMAILS DE CONFIRMACIÓN (ARRAY) ===');

    // Cargar clase de emails
    if (!class_exists('ReservasEmailService')) {
        require_once RESERVAS_PLUGIN_PATH . 'includes/class-email-service.php';
    }

    // Enviar email al cliente
    $customer_result = ReservasEmailService::send_customer_confirmation($reserva_array);
    if ($customer_result['success']) {
        error_log('✅ Email enviado al cliente correctamente');
    } else {
        error_log('❌ Error enviando email al cliente: ' . $customer_result['message']);
    }

    // Enviar email al administrador
    $admin_result = ReservasEmailService::send_admin_notification($reserva_array);
    if ($admin_result['success']) {
        error_log('✅ Email enviado al administrador correctamente');
    } else {
        error_log('❌ Error enviando email al administrador: ' . $admin_result['message']);
    }
}


    /**
     * Recalcular precio para verificar
     */
    private function recalcular_precio($datos_reserva)
{
    error_log('=== RECALCULANDO PRECIO ===');

    global $wpdb;

    $table_servicios = $wpdb->prefix . 'reservas_servicios';

    $servicio = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table_servicios WHERE id = %d",
        $datos_reserva['service_id']
    ));

    if (!$servicio) {
        return array('valido' => false, 'error' => 'Servicio no encontrado para cálculo');
    }

    $adultos = intval($datos_reserva['adultos']);
    $residentes = intval($datos_reserva['residentes']);
    $ninos_5_12 = intval($datos_reserva['ninos_5_12']);
    $ninos_menores = intval($datos_reserva['ninos_menores']);

    // Calcular total de personas que ocupan plaza
    $total_personas_con_plaza = $adultos + $residentes + $ninos_5_12;

    error_log("Personas calculadas - Adultos: $adultos, Residentes: $residentes, Niños 5-12: $ninos_5_12, Menores: $ninos_menores");
    error_log("Total personas con plaza: $total_personas_con_plaza");

    // ✅ CALCULAR PRECIO BASE CORRECTO (cada tipo de persona paga su tarifa)
    $precio_base = 0;
    $precio_base += $adultos * $servicio->precio_adulto;      // Adultos pagan precio adulto
    $precio_base += $residentes * $servicio->precio_residente; // ✅ Residentes pagan precio residente
    $precio_base += $ninos_5_12 * $servicio->precio_nino;     // ✅ Niños pagan precio niño

    error_log("Precio base calculado CORRECTO: $precio_base");
    error_log("- Adultos ($adultos × {$servicio->precio_adulto}): " . ($adultos * $servicio->precio_adulto));
    error_log("- Residentes ($residentes × {$servicio->precio_residente}): " . ($residentes * $servicio->precio_residente));
    error_log("- Niños ($ninos_5_12 × {$servicio->precio_nino}): " . ($ninos_5_12 * $servicio->precio_nino));

    // ✅ NO HAY DESCUENTOS INDIVIDUALES PORQUE YA APLICAMOS LAS TARIFAS CORRECTAS
    $descuento_total = 0;

    // Calcular descuento por grupo (solo si hay suficientes personas)
    $descuento_grupo = 0;
    $regla_aplicada = null;

    if ($total_personas_con_plaza > 0) {
        if (!class_exists('ReservasDiscountsAdmin')) {
            require_once RESERVAS_PLUGIN_PATH . 'includes/class-discounts-admin.php';
        }

        // El subtotal es el precio base (ya con tarifas correctas)
        $subtotal = $precio_base;
        error_log("Subtotal para descuento por grupo: $subtotal");

        $discount_info = ReservasDiscountsAdmin::calculate_discount($total_personas_con_plaza, $subtotal, 'total');

        error_log("Información de descuento por grupo: " . print_r($discount_info, true));

        if ($discount_info['discount_applied']) {
            $descuento_grupo = $discount_info['discount_amount'];
            $descuento_total += $descuento_grupo;
            $regla_aplicada = $discount_info;
            error_log("✅ Descuento por grupo aplicado: $descuento_grupo");
        } else {
            error_log("❌ No se aplicó descuento por grupo (insuficientes personas: $total_personas_con_plaza)");
        }
    }

    // Descuento específico del servicio
    $descuento_servicio = 0;
    if ($servicio->tiene_descuento && floatval($servicio->porcentaje_descuento) > 0) {
        $subtotal_actual = $precio_base - $descuento_total;
        $descuento_servicio = ($subtotal_actual * floatval($servicio->porcentaje_descuento)) / 100;
        $descuento_total += $descuento_servicio;
        error_log("Descuento del servicio: $descuento_servicio");
    }

    // Precio final
    $precio_final = $precio_base - $descuento_total;
    if ($precio_final < 0)
        $precio_final = 0;

    $precio_info = array(
        'precio_base' => round($precio_base, 2),
        'descuento_total' => round($descuento_total, 2),
        'descuento_residentes' => 0, // ✅ Ya no hay descuento separado
        'descuento_ninos' => 0,      // ✅ Ya no hay descuento separado
        'descuento_grupo' => round($descuento_grupo, 2),
        'descuento_servicio' => round($descuento_servicio, 2),
        'precio_final' => round($precio_final, 2),
        'regla_descuento_aplicada' => $regla_aplicada,
        'total_personas_con_plaza' => $total_personas_con_plaza
    );

    error_log('Precio calculado final CORRECTO: ' . print_r($precio_info, true));

    return array('valido' => true, 'precio' => $precio_info);
}

    /**
     * Crear reserva en la base de datos - VERSIÓN CORREGIDA
     */
    private function crear_reserva($datos_personales, $datos_reserva, $calculo_precio)
    {
        error_log('=== CREANDO RESERVA ===');

        global $wpdb;
        $table_reservas = $wpdb->prefix . 'reservas_reservas';

        // Verificar que la tabla existe
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_reservas'") != $table_reservas) {
            return array('exito' => false, 'error' => 'Tabla de reservas no existe');
        }

        // Generar localizador único
        $localizador = $this->generar_localizador();
        error_log('Localizador generado: ' . $localizador);

        // ✅ OBTENER HORA_VUELTA DEL SERVICIO
        $table_servicios = $wpdb->prefix . 'reservas_servicios';
        $servicio = $wpdb->get_row($wpdb->prepare(
            "SELECT hora_vuelta FROM $table_servicios WHERE id = %d",
            $datos_reserva['service_id']
        ));

        // ✅ PREPARAR DATOS PARA INSERTAR SIN REDSYS
        $reserva_data = array(
            'localizador' => $localizador,
            'redsys_order_id' => null, // No hay order ID de Redsys
            'servicio_id' => $datos_reserva['service_id'],
            'fecha' => $datos_reserva['fecha'],
            'hora' => $datos_reserva['hora_ida'],
            'hora_vuelta' => $servicio ? $servicio->hora_vuelta : null,
            'nombre' => $datos_personales['nombre'],
            'apellidos' => $datos_personales['apellidos'],
            'email' => $datos_personales['email'],
            'telefono' => $datos_personales['telefono'],
            'adultos' => $datos_reserva['adultos'],
            'residentes' => $datos_reserva['residentes'],
            'ninos_5_12' => $datos_reserva['ninos_5_12'],
            'ninos_menores' => $datos_reserva['ninos_menores'],
            'total_personas' => $datos_reserva['total_personas'],
            'precio_base' => $calculo_precio['precio_base'],
            'descuento_total' => $calculo_precio['descuento_total'],
            'precio_final' => $calculo_precio['precio_final'],
            'regla_descuento_aplicada' => $calculo_precio['regla_descuento_aplicada'] ? json_encode($calculo_precio['regla_descuento_aplicada']) : null,
            'estado' => 'confirmada',
            'metodo_pago' => 'directo', // ✅ CAMBIAR A DIRECTO
            'created_at' => current_time('mysql')
        );

        error_log('Datos de reserva a insertar: ' . print_r($reserva_data, true));

        $resultado = $wpdb->insert($table_reservas, $reserva_data);

        if ($resultado === false) {
            error_log('ERROR DB: ' . $wpdb->last_error);
            error_log('QUERY: ' . $wpdb->last_query);
            return array('exito' => false, 'error' => 'Error guardando la reserva: ' . $wpdb->last_error);
        }

        $reserva_id = $wpdb->insert_id;
        error_log('✅ Reserva insertada con ID: ' . $reserva_id);

        return array(
            'exito' => true,
            'reserva_id' => $reserva_id,
            'localizador' => $localizador
        );
    }

    /**
     * Actualizar plazas disponibles del servicio
     */
    private function actualizar_plazas_disponibles($service_id, $personas_ocupadas)
    {
        error_log('=== ACTUALIZANDO PLAZAS DISPONIBLES ===');
        error_log("Service ID: $service_id, Personas: $personas_ocupadas");

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

        error_log('Query ejecutada: ' . $wpdb->last_query);
        error_log('Filas afectadas: ' . $resultado);

        if ($resultado === false) {
            error_log('ERROR actualizando plazas: ' . $wpdb->last_error);
            return array('exito' => false, 'error' => 'Error actualizando plazas disponibles');
        }

        if ($resultado === 0) {
            error_log('ERROR: No hay suficientes plazas disponibles');
            return array('exito' => false, 'error' => 'No hay suficientes plazas disponibles');
        }

        return array('exito' => true);
    }

    /**
     * Eliminar reserva (en caso de error)
     */
    private function eliminar_reserva($reserva_id)
    {
        error_log('=== ELIMINANDO RESERVA POR ERROR ===');
        error_log('ID de reserva a eliminar: ' . $reserva_id);

        global $wpdb;

        $table_reservas = $wpdb->prefix . 'reservas_reservas';

        $wpdb->delete($table_reservas, array('id' => $reserva_id));
        error_log('Reserva eliminada');
    }

    /**
     * Generar localizador único
     */
    private function generar_localizador()
    {
        global $wpdb;

        $table_reservas = $wpdb->prefix . 'reservas_reservas';
        $table_config = $wpdb->prefix . 'reservas_configuration';

        $año_actual = date('Y');

        // Obtener el último número de localizador para este año
        $config_key = "ultimo_localizador_$año_actual";

        $ultimo_numero = $wpdb->get_var($wpdb->prepare(
            "SELECT config_value FROM $table_config WHERE config_key = %s",
            $config_key
        ));

        if ($ultimo_numero === null) {
            // Primer localizador del año, empezar desde 1
            $nuevo_numero = 1;

            // Insertar configuración inicial para este año
            $wpdb->insert(
                $table_config,
                array(
                    'config_key' => $config_key,
                    'config_value' => '1',
                    'config_group' => 'localizadores',
                    'description' => "Último número de localizador usado en el año $año_actual"
                )
            );
        } else {
            $nuevo_numero = intval($ultimo_numero) + 1;

            // Verificar que no exceda 100000
            if ($nuevo_numero > 100000) {
                // Si se alcanza el límite, buscar números disponibles
                $nuevo_numero = $this->buscar_numero_disponible($año_actual);
                if ($nuevo_numero === false) {
                    throw new Exception('Se ha alcanzado el límite máximo de reservas para este año (100000)');
                }
            }

            // Actualizar el contador
            $wpdb->update(
                $table_config,
                array('config_value' => $nuevo_numero),
                array('config_key' => $config_key)
            );
        }

        // Generar localizador con formato de 6 cifras
        $localizador = str_pad($nuevo_numero, 6, '0', STR_PAD_LEFT);

        // Verificar que no exista ya (por seguridad)
        $existe = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_reservas WHERE localizador = %s",
            $localizador
        ));

        if ($existe > 0) {
            // Si por alguna razón ya existe, buscar el siguiente disponible
            return $this->generar_localizador_recursivo($año_actual, $nuevo_numero + 1);
        }

        error_log("Localizador generado: $localizador (número $nuevo_numero para año $año_actual)");

        return $localizador;
    }

    private function buscar_numero_disponible($año)
    {
        global $wpdb;

        $table_reservas = $wpdb->prefix . 'reservas_reservas';

        // Buscar el primer número no usado entre 1 y 100000
        for ($i = 1; $i <= 100000; $i++) {
            $localizador_test = str_pad($i, 6, '0', STR_PAD_LEFT);

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

    private function generar_localizador_recursivo($año, $numero)
    {
        global $wpdb;

        $table_reservas = $wpdb->prefix . 'reservas_reservas';
        $table_config = $wpdb->prefix . 'reservas_configuration';

        if ($numero > 100000) {
            // Buscar hueco disponible
            $numero_disponible = $this->buscar_numero_disponible($año);
            if ($numero_disponible === false) {
                throw new Exception('Se ha alcanzado el límite máximo de reservas para este año (100000)');
            }
            $numero = $numero_disponible;
        }

        $localizador = str_pad($numero, 6, '0', STR_PAD_LEFT);

        $existe = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_reservas WHERE localizador = %s",
            $localizador
        ));

        if ($existe > 0) {
            return $this->generar_localizador_recursivo($año, $numero + 1);
        }

        // Actualizar contador
        $config_key = "ultimo_localizador_$año";
        $wpdb->update(
            $table_config,
            array('config_value' => $numero),
            array('config_key' => $config_key)
        );

        return $localizador;
    }
}
