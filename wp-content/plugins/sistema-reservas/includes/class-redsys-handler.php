<?php
require_once __DIR__ . '/redsys-api.php';

function generar_formulario_redsys($reserva_data) {
    error_log('=== INICIANDO GENERACIÓN FORMULARIO REDSYS ===');
    error_log('Datos recibidos: ' . print_r($reserva_data, true));
    
    $miObj = new RedsysAPI();

    if (is_production_environment()) {
        $clave = 'Q+2780shKFbG3vkPXS2+kY6RWQLQnWD9';
        $codigo_comercio = '014591697';
        $terminal = '001';
        error_log('🟢 USANDO CONFIGURACIÓN DE PRODUCCIÓN');
    } else {
        $clave = 'sq7HjrUOBfKmC576ILgskD5srU870gJ7';
        $codigo_comercio = '999008881';
        $terminal = '001';
        error_log('🟡 USANDO CONFIGURACIÓN DE PRUEBAS');
    }
    
    // ✅ VERIFICAR FIRMA DIGITAL ANTES DE PROCEDER
    if (!isset($reserva_data['calculo_completo']) || !isset($reserva_data['calculo_completo']['firma'])) {
        error_log('❌ INTENTO DE MANIPULACIÓN: No hay firma digital');
        throw new Exception('Error de seguridad: precio no validado');
    }
    
    $firma_recibida = $reserva_data['calculo_completo']['firma'];
    $firma_data = $reserva_data['calculo_completo']['firma_data'];
    
    // ✅ RECALCULAR FIRMA PARA VERIFICAR
    $firma_calculada = hash_hmac('sha256', json_encode($firma_data), wp_salt('nonce'));
    
    if ($firma_recibida !== $firma_calculada) {
        error_log('❌ INTENTO DE MANIPULACIÓN: Firma digital no coincide');
        error_log('Firma recibida: ' . $firma_recibida);
        error_log('Firma calculada: ' . $firma_calculada);
        throw new Exception('Error de seguridad: precio manipulado');
    }
    
    // ✅ VERIFICAR TIMESTAMP (máximo 30 minutos)
    if ((time() - $firma_data['timestamp']) > 1800) {
        error_log('❌ Firma expirada');
        throw new Exception('La sesión ha expirado. Por favor, vuelve a calcular el precio.');
    }
    
    // ✅ USAR PRECIO FIRMADO, NO EL QUE VIENE EN total_price
    $total_price = floatval($reserva_data['calculo_completo']['precio_final']);
    
    error_log('✅ Firma verificada correctamente. Precio validado: ' . $total_price . '€');
    
    if (!$total_price || $total_price <= 0) {
        throw new Exception('El importe debe ser mayor que 0. Recibido: ' . $total_price);
    }
    
    $importe = intval($total_price * 100);
    
    $timestamp = time();
    $random = rand(100, 999);
    $pedido = date('ymdHis') . str_pad($random, 3, '0', STR_PAD_LEFT);
    
    if (strlen($pedido) > 12) {
        $pedido = substr($pedido, 0, 12);
    }
    
    $miObj->setParameter("DS_MERCHANT_AMOUNT", $importe);
    $miObj->setParameter("DS_MERCHANT_ORDER", $pedido);
    $miObj->setParameter("DS_MERCHANT_MERCHANTCODE", $codigo_comercio);
    $miObj->setParameter("DS_MERCHANT_CURRENCY", "978");
    $miObj->setParameter("DS_MERCHANT_TRANSACTIONTYPE", "0");
    $miObj->setParameter("DS_MERCHANT_TERMINAL", $terminal);
    
    $base_url = home_url();
    $miObj->setParameter("DS_MERCHANT_MERCHANTURL", $base_url . '/wp-admin/admin-ajax.php?action=redsys_notification');
    $miObj->setParameter("DS_MERCHANT_URLOK", $base_url . '/confirmacion-reserva/?status=ok&order=' . $pedido);
    $miObj->setParameter("DS_MERCHANT_URLKO", $base_url . '/error-pago/?status=ko&order=' . $pedido);
    
    $descripcion = "Reserva Medina Azahara - " . ($reserva_data['fecha'] ?? date('Y-m-d'));
    $miObj->setParameter("DS_MERCHANT_PRODUCTDESCRIPTION", $descripcion);
    
    if (isset($reserva_data['nombre']) && isset($reserva_data['apellidos'])) {
        $miObj->setParameter("DS_MERCHANT_TITULAR", $reserva_data['nombre'] . ' ' . $reserva_data['apellidos']);
    }

    $params = $miObj->createMerchantParameters();
    $signature = $miObj->createMerchantSignature($clave);
    $version = "HMAC_SHA256_V1";

    $redsys_url = is_production_environment() ? 
        'https://sis.redsys.es/sis/realizarPago' :
        'https://sis-t.redsys.es:25443/sis/realizarPago';

    $html = '<div id="redsys-overlay" style="position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.8);display:flex;align-items:center;justify-content:center;z-index:99999;">';
    $html .= '<div style="background:white;padding:30px;border-radius:10px;text-align:center;max-width:400px;">';
    $html .= '<h3 style="margin:0 0 20px 0;color:#333;">Redirigiendo al banco...</h3>';
    $html .= '<div style="margin:20px 0;">Por favor, espere...</div>';
    $html .= '<p style="font-size:14px;color:#666;margin:20px 0 0 0;">Sera redirigido automaticamente a la pasarela de pago segura.</p>';
    $html .= '</div></div>';
    $html .= '<form id="formulario_redsys" action="' . $redsys_url . '" method="POST" style="display:none;">';
    $html .= '<input type="hidden" name="Ds_SignatureVersion" value="' . $version . '">';
    $html .= '<input type="hidden" name="Ds_MerchantParameters" value="' . $params . '">';
    $html .= '<input type="hidden" name="Ds_Signature" value="' . $signature . '">';
    $html .= '</form>';
    $html .= '<script type="text/javascript">';
    $html .= 'setTimeout(function() {';
    $html .= 'var form = document.getElementById("formulario_redsys");';
    $html .= 'if(form) { form.submit(); } else { alert("Error inicializando pago"); }';
    $html .= '}, 1000);';
    $html .= '</script>';

    guardar_datos_pedido($pedido, $reserva_data);
    return $html;
}

function is_production_environment() {
    return true;
}

function process_successful_payment($order_id, $params) {
    error_log('=== PROCESANDO PAGO EXITOSO CON REDSYS ===');
    error_log("Order ID: $order_id");
    error_log("Params: " . print_r($params, true));
    
    if (!session_id()) {
        session_start();
    }
    
    global $wpdb;
    $table_reservas = $wpdb->prefix . 'reservas_reservas';
    
    $existing = $wpdb->get_var($wpdb->prepare(
        "SELECT id FROM $table_reservas WHERE redsys_order_id = %s",
        $order_id
    ));

    if ($existing) {
        error_log("⚠️ Reserva ya procesada para order_id: $order_id (ID: $existing)");
        return true;
    }

    // ✅ RECUPERAR DATOS DE MÚLTIPLES FUENTES
    $reservation_data = null;
    
    // 1. Desde transient
    $reservation_data = get_transient('redsys_order_' . $order_id);
    if ($reservation_data) {
        error_log('✅ Datos encontrados en transient');
    }
    
    // 2. Desde sesión
    if (!$reservation_data && isset($_SESSION['pending_orders'][$order_id])) {
        $reservation_data = $_SESSION['pending_orders'][$order_id];
        error_log('✅ Datos encontrados en sesión');
    }
    
    // 3. Desde option
    if (!$reservation_data) {
        $reservation_data = get_option('pending_order_' . $order_id);
        if ($reservation_data) {
            error_log('✅ Datos encontrados en option (backup)');
        }
    }
    
    // 4. Último intento: buscar por timestamp
    if (!$reservation_data) {
        error_log('❌ No se encontraron datos de reserva para pedido: ' . $order_id);
        $reservation_data = find_reservation_by_timestamp($order_id);
        if (!$reservation_data) {
            // ✅ ENVIAR ALERTA AL ADMIN
            send_lost_payment_alert($order_id, $params);
            return false;
        }
    }

    try {
        if (!class_exists('ReservasProcessor')) {
            require_once RESERVAS_PLUGIN_PATH . 'includes/class-reservas-processor.php';
        }

        $processor = new ReservasProcessor();
        
        $processed_data = array(
            'nombre' => $reservation_data['nombre'] ?? '',
            'apellidos' => $reservation_data['apellidos'] ?? '',
            'email' => $reservation_data['email'] ?? '',
            'telefono' => $reservation_data['telefono'] ?? '',
            'reservation_data' => json_encode($reservation_data),
            'metodo_pago' => 'redsys',
            'transaction_id' => $params['Ds_AuthorisationCode'] ?? '',
            'order_id' => $order_id
        );

        $result = $processor->process_reservation_payment($processed_data);
        
        if ($result['success']) {
            error_log('✅ Reserva procesada exitosamente: ' . $result['data']['localizador']);
            
            if (!session_id()) session_start();
            $_SESSION['confirmed_reservation'] = $result['data'];
            set_transient('confirmed_reservation_' . $order_id, $result['data'], 3600);
            set_transient('confirmed_reservation_loc_' . $result['data']['localizador'], $result['data'], 3600);
            set_transient('order_to_localizador_' . $order_id, $result['data']['localizador'], 3600);
            
            delete_transient('redsys_order_' . $order_id);
            delete_option('pending_order_' . $order_id);
            if (isset($_SESSION['pending_orders'][$order_id])) {
                unset($_SESSION['pending_orders'][$order_id]);
            }
            
            return true;
        } else {
            error_log('❌ Error procesando reserva: ' . $result['message']);
            send_lost_payment_alert($order_id, $params, $reservation_data);
            return false;
        }
        
    } catch (Exception $e) {
        error_log('❌ Excepción procesando pago exitoso: ' . $e->getMessage());
        send_lost_payment_alert($order_id, $params, $reservation_data);
        return false;
    }
}

// ✅ FUNCIÓN AUXILIAR: Buscar reserva por timestamp
function find_reservation_by_timestamp($order_id) {
    error_log('🔍 Buscando reserva por timestamp cercano a order_id: ' . $order_id);
    
    if (strlen($order_id) >= 12) {
        $timestamp_str = substr($order_id, 0, 12);
        
        try {
            $year = '20' . substr($timestamp_str, 0, 2);
            $month = substr($timestamp_str, 2, 2);
            $day = substr($timestamp_str, 4, 2);
            $hour = substr($timestamp_str, 6, 2);
            $min = substr($timestamp_str, 8, 2);
            $sec = substr($timestamp_str, 10, 2);
            
            $datetime = "$year-$month-$day $hour:$min:$sec";
            error_log('📅 Timestamp extraído: ' . $datetime);
            
            global $wpdb;
            $options = $wpdb->get_results(
                "SELECT option_name, option_value 
                 FROM $wpdb->options 
                 WHERE option_name LIKE 'pending_order_%'
                 AND option_name != 'pending_order_$order_id'
                 LIMIT 20"
            );
            
            foreach ($options as $option) {
                $data = maybe_unserialize($option->option_value);
                if (is_array($data) && isset($data['email'])) {
                    error_log('✅ Encontrada reserva pendiente: ' . $option->option_name);
                    return $data;
                }
            }
        } catch (Exception $e) {
            error_log('❌ Error parseando timestamp: ' . $e->getMessage());
        }
    }
    
    return null;
}

// ✅ FUNCIÓN AUXILIAR: Enviar alerta de pago perdido
function send_lost_payment_alert($order_id, $params, $reservation_data = null) {
    error_log('🚨 ENVIANDO ALERTA DE PAGO PERDIDO');
    
    if (!class_exists('ReservasConfigurationAdmin')) {
        require_once RESERVAS_PLUGIN_PATH . 'includes/class-configuration-admin.php';
    }
    
    $admin_email = ReservasConfigurationAdmin::get_config('email_reservas', get_option('admin_email'));
    
    $subject = '🚨 ALERTA: Pago Redsys sin reserva - Order: ' . $order_id;
    
    $message = "Se ha detectado un pago exitoso en Redsys pero NO se pudo crear la reserva.\n\n";
    $message .= "⚠️ ACCIÓN REQUERIDA: Crear reserva manualmente desde el dashboard\n\n";
    $message .= "═══════════════════════════════════════\n";
    $message .= "DATOS DEL PAGO:\n";
    $message .= "═══════════════════════════════════════\n";
    $message .= "Order ID: $order_id\n";
    $message .= "Código autorización: " . ($params['Ds_AuthorisationCode'] ?? 'N/A') . "\n";
    $message .= "Fecha/Hora: " . date('d/m/Y H:i:s') . "\n\n";
    
    if ($reservation_data) {
        $message .= "═══════════════════════════════════════\n";
        $message .= "DATOS DEL CLIENTE:\n";
        $message .= "═══════════════════════════════════════\n";
        $message .= "Nombre: " . ($reservation_data['nombre'] ?? 'N/A') . " " . ($reservation_data['apellidos'] ?? '') . "\n";
        $message .= "Email: " . ($reservation_data['email'] ?? 'N/A') . "\n";
        $message .= "Teléfono: " . ($reservation_data['telefono'] ?? 'N/A') . "\n\n";
        
        $message .= "═══════════════════════════════════════\n";
        $message .= "DATOS DEL SERVICIO:\n";
        $message .= "═══════════════════════════════════════\n";
        $message .= "Fecha: " . ($reservation_data['fecha'] ?? 'N/A') . "\n";
        $message .= "Hora: " . ($reservation_data['hora_ida'] ?? 'N/A') . "\n";
        $message .= "Adultos: " . ($reservation_data['adultos'] ?? 0) . "\n";
        $message .= "Residentes: " . ($reservation_data['residentes'] ?? 0) . "\n";
        $message .= "Niños (5-12): " . ($reservation_data['ninos_5_12'] ?? 0) . "\n";
        $message .= "Niños menores: " . ($reservation_data['ninos_menores'] ?? 0) . "\n";
        $message .= "Importe: " . ($reservation_data['total_price'] ?? 'N/A') . "€\n\n";
    } else {
        $message .= "⚠️ NO SE ENCONTRARON DATOS DE RESERVA\n";
        $message .= "Contacta con el cliente para obtener los datos.\n\n";
    }
    
    $message .= "═══════════════════════════════════════\n";
    $message .= "ACCIONES A REALIZAR:\n";
    $message .= "═══════════════════════════════════════\n";
    $message .= "1. Accede al dashboard: " . home_url('/reservas-admin/') . "\n";
    $message .= "2. Ve a 'Reserva Rápida' para crear la reserva manualmente\n";
    $message .= "3. Contacta con el cliente para confirmar los datos\n";
    $message .= "4. El cliente ya ha pagado - NO cobrar de nuevo\n\n";
    
    $message .= "Este email se envió automáticamente por el sistema de alertas.\n";
    
    $headers = array('Content-Type: text/plain; charset=UTF-8');
    
    $sent = wp_mail($admin_email, $subject, $message, $headers);
    
    if ($sent) {
        error_log('✅ Alerta enviada al administrador: ' . $admin_email);
    } else {
        error_log('❌ Error enviando alerta al administrador');
    }
}

function get_reservation_data_for_confirmation() {
    error_log('=== INTENTANDO RECUPERAR DATOS DE CONFIRMACIÓN ===');
    
    if (isset($_GET['order']) && !empty($_GET['order'])) {
        $order_id = sanitize_text_field($_GET['order']);
        error_log('Order ID desde URL: ' . $order_id);
        
        $data = get_transient('confirmed_reservation_' . $order_id);
        if ($data) {
            error_log('✅ Datos encontrados en transient por order_id');
            return $data;
        }
        
        $data = get_option('temp_reservation_' . $order_id);
        if ($data) {
            error_log('✅ Datos encontrados en options por order_id');
            delete_option('temp_reservation_' . $order_id);
            return $data;
        }
    }
    
    if (!session_id()) {
        session_start();
    }
    
    if (isset($_SESSION['confirmed_reservation'])) {
        error_log('✅ Datos encontrados en sesión');
        $data = $_SESSION['confirmed_reservation'];
        unset($_SESSION['confirmed_reservation']);
        return $data;
    }
    
    global $wpdb;
    $table_reservas = $wpdb->prefix . 'reservas_reservas';
    
    $recent_reservation = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table_reservas 
         WHERE created_at >= DATE_SUB(NOW(), INTERVAL 2 MINUTE)
         AND metodo_pago = 'redsys'
         ORDER BY created_at DESC 
         LIMIT 1"
    ));
    
    if ($recent_reservation) {
        error_log('✅ Reserva reciente encontrada en BD: ' . $recent_reservation->localizador);
        
        return array(
            'localizador' => $recent_reservation->localizador,
            'reserva_id' => $recent_reservation->id,
            'detalles' => array(
                'fecha' => $recent_reservation->fecha,
                'hora' => $recent_reservation->hora,
                'personas' => $recent_reservation->total_personas,
                'precio_final' => $recent_reservation->precio_final
            )
        );
    }
    
    error_log('❌ No se encontraron datos de confirmación por ningún método');
    return null;
}

function guardar_datos_pedido($order_id, $reserva_data) {
    error_log('=== GUARDANDO DATOS DEL PEDIDO ===');
    error_log("Order ID: $order_id");
    
    if (!session_id()) {
        session_start();
    }
    
    if (!isset($_SESSION['pending_orders'])) {
        $_SESSION['pending_orders'] = array();
    }
    
    $_SESSION['pending_orders'][$order_id] = $reserva_data;
    
    set_transient('redsys_order_' . $order_id, $reserva_data, 7200);
    update_option('pending_order_' . $order_id, $reserva_data, false);
    
    error_log("✅ Datos guardados en 3 ubicaciones para order: $order_id");
}