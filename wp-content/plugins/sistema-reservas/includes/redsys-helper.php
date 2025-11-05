<?php
/**
 * Helper para Redsys - Funciones principales
 */

require_once __DIR__ . '/redsys-api.php';

function generar_formulario_redsys($reserva_data) {
    error_log('=== INICIANDO GENERACIÓN FORMULARIO REDSYS ===');
    error_log('Datos recibidos: ' . print_r($reserva_data, true));
    
    $miObj = new RedsysAPI();

    // ✅ CONFIGURACIÓN PARA PRUEBAS
    if (is_production_environment()) {
        // PRODUCCIÓN (cuando esté listo)
        $clave = 'Q+2780shKFbG3vkPXS2+kY6RWQLQnWD9';
        $codigo_comercio = '014591697';
        $terminal = '001';
        $redsys_url = 'https://sis.redsys.es/sis/realizarPago';
        error_log('🟢 USANDO CONFIGURACIÓN DE PRODUCCIÓN');
    } else {
        // PRUEBAS
        $clave = 'sq7HjrUOBfKmC576ILgskD5srU870gJ7';
        $codigo_comercio = '999008881'; // ✅ CÓDIGO DE COMERCIO DE PRUEBAS
        $terminal = '001';
        $redsys_url = 'https://sis-t.redsys.es:25443/sis/realizarPago';
        error_log('🟡 USANDO CONFIGURACIÓN DE PRUEBAS');
    }
    
    $total_price = null;
    if (isset($reserva_data['total_price'])) {
        $total_price = $reserva_data['total_price'];
    } elseif (isset($reserva_data['precio_final'])) {
        $total_price = $reserva_data['precio_final'];
    }
    
    if ($total_price) {
        $total_price = str_replace(['€', ' ', ','], ['', '', '.'], $total_price);
        $total_price = floatval($total_price);
    }
    
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

    error_log("URL de Redsys: " . $redsys_url);
    error_log("Pedido: " . $pedido);
    error_log("Importe: " . $importe);

    // ✅ NUEVO ENFOQUE: SCRIPT QUE SE EJECUTA INMEDIATAMENTE
    $html = '
    <div id="redsys-overlay" style="position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.8);display:flex;align-items:center;justify-content:center;z-index:99999;">
        <div style="background:white;padding:30px;border-radius:10px;text-align:center;max-width:400px;">
            <h3 style="margin:0 0 20px 0;color:#333;">Redirigiendo al banco...</h3>
            <div style="margin:20px 0;">⏳ Por favor, espere...</div>
            <p style="font-size:14px;color:#666;margin:20px 0 0 0;">Será redirigido automáticamente a la pasarela de pago segura.</p>
        </div>
    </div>
    <form id="formulario_redsys" action="' . $redsys_url . '" method="POST">
        <input type="hidden" name="Ds_SignatureVersion" value="' . $version . '">
        <input type="hidden" name="Ds_MerchantParameters" value="' . $params . '">
        <input type="hidden" name="Ds_Signature" value="' . $signature . '">
    </form>
    <script>
        console.log("🏦 Ejecutando redirección inmediata a Redsys...");
        console.log("URL destino: ' . $redsys_url . '");
        console.log("Pedido: ' . $pedido . '");
        console.log("Importe: ' . $importe . ' céntimos");
        
        // ✅ EJECUTAR INMEDIATAMENTE SIN TIMEOUT
        (function() {
            var form = document.getElementById("formulario_redsys");
            if (form) {
                console.log("✅ Formulario encontrado, enviando...");
                form.submit();
            } else {
                console.error("❌ No se encontró el formulario");
                alert("Error: No se pudo inicializar el pago. Refresca la página e inténtalo de nuevo.");
                // Eliminar overlay en caso de error
                var overlay = document.getElementById("redsys-overlay");
                if (overlay) overlay.remove();
            }
        })();
    </script>';

    guardar_datos_pedido($pedido, $reserva_data);
    return $html;
}

function is_production_environment() {
    // ✅ CAMBIAR A TRUE PARA ACTIVAR PRODUCCIÓN
    return true; // ← CAMBIO: false = PRUEBAS, true = PRODUCCIÓN
}



function process_successful_payment($order_id, $redsys_params) {
    error_log('=== PROCESANDO PAGO EXITOSO ===');
    error_log("Order ID: $order_id");
    error_log('Parámetros Redsys: ' . print_r($redsys_params, true));

    // Verificar si ya existe una reserva con este order_id
    global $wpdb;
    $table_reservas = $wpdb->prefix . 'reservas_reservas';
    
    $existing = $wpdb->get_var($wpdb->prepare(
        "SELECT id FROM $table_reservas WHERE redsys_order_id = %s",
        $order_id
    ));

    if ($existing) {
        error_log("⚠️ Ya existe reserva para order_id: $order_id");
        return true;
    }

    // Obtener datos del pedido guardados
    if (!session_id()) {
        session_start();
    }
    
    $reservation_data = $_SESSION['pending_orders'][$order_id] ?? null;
    
    if (!$reservation_data) {
        error_log("❌ No se encontraron datos para order_id: $order_id");
        return false;
    }

    try {
        // Procesar la reserva usando la clase existente
        if (!class_exists('ReservasProcessor')) {
            require_once RESERVAS_PLUGIN_PATH . 'includes/class-reservas-processor.php';
        }

        $processor = new ReservasProcessor();
        
        // Preparar datos para el procesador
        $payment_data = array(
            'order_id' => $order_id,
            'nombre' => $reservation_data['nombre'],
            'apellidos' => $reservation_data['apellidos'],
            'email' => $reservation_data['email'],
            'telefono' => $reservation_data['telefono'],
            'reservation_data' => json_encode($reservation_data),
            'metodo_pago' => 'redsys'
        );

        $result = $processor->process_reservation_payment($payment_data);

        if ($result['success']) {
            error_log("✅ Reserva procesada exitosamente: " . $result['data']['localizador']);
            
            // Limpiar datos temporales
            unset($_SESSION['pending_orders'][$order_id]);
            
            return true;
        } else {
            error_log("❌ Error procesando reserva: " . $result['message']);
            return false;
        }

    } catch (Exception $e) {
        error_log("❌ Excepción procesando pago: " . $e->getMessage());
        return false;
    }
}