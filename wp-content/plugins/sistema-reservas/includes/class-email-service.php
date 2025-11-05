<?php

/**
 * Clase para gestionar envío de emails del sistema de reservas - CON RECORDATORIOS AUTOMÁTICOS
 * Archivo: wp-content/plugins/sistema-reservas/includes/class-email-service.php
 */
class ReservasEmailService
{
    public function __construct()
    {
        // No se necesitan hooks aquí, será llamado desde otras clases
    }

    /**
     * Enviar solicitud de cancelación al administrador
     */
    public static function send_cancellation_request_to_admin($data)
    {
        try {
            $reserva = $data['reserva'];
            $agency_name = $data['agency_name'];
            $motivo = $data['motivo_cancelacion'];

            // ✅ CORREGIR: Usar la clase correcta para obtener configuración
            $email_admin = ReservasConfigurationAdmin::get_config('email_reservas', get_option('admin_email'));
            $nombre_remitente = ReservasConfigurationAdmin::get_config('nombre_remitente', get_bloginfo('name'));
            $email_remitente = ReservasConfigurationAdmin::get_config('email_remitente', get_option('admin_email'));

            $subject = "Solicitud de Cancelación - Reserva {$reserva['localizador']}";

            $fecha_formateada = date('d/m/Y', strtotime($reserva['fecha']));

            $body = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.1);'>
            
            <div style='background: #dc3545; color: white; padding: 20px; text-align: center;'>
                <h1 style='margin: 0; font-size: 24px;'>⚠️ SOLICITUD DE CANCELACIÓN</h1>
            </div>
            
            <div style='padding: 30px;'>
                <div style='background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin-bottom: 25px;'>
                    <p style='margin: 0; color: #856404; font-weight: bold;'>
                        La agencia <strong>{$agency_name}</strong> ha solicitado la cancelación de una reserva.
                    </p>
                </div>
                
                <h2 style='color: #dc3545; margin-bottom: 20px;'>Detalles de la Reserva</h2>
                
                <table style='width: 100%; border-collapse: collapse; margin-bottom: 25px;'>
                    <tr>
                        <td style='padding: 8px 0; border-bottom: 1px solid #eee; font-weight: bold; width: 150px;'>Localizador:</td>
                        <td style='padding: 8px 0; border-bottom: 1px solid #eee; color: #0073aa; font-weight: bold;'>{$reserva['localizador']}</td>
                    </tr>
                    <tr>
                        <td style='padding: 8px 0; border-bottom: 1px solid #eee; font-weight: bold;'>Cliente:</td>
                        <td style='padding: 8px 0; border-bottom: 1px solid #eee;'>{$reserva['nombre']} {$reserva['apellidos']}</td>
                    </tr>
                    <tr>
                        <td style='padding: 8px 0; border-bottom: 1px solid #eee; font-weight: bold;'>Email:</td>
                        <td style='padding: 8px 0; border-bottom: 1px solid #eee;'>{$reserva['email']}</td>
                    </tr>
                    <tr>
                        <td style='padding: 8px 0; border-bottom: 1px solid #eee; font-weight: bold;'>Fecha servicio:</td>
                        <td style='padding: 8px 0; border-bottom: 1px solid #eee;'>{$fecha_formateada} a las {$reserva['hora']}</td>
                    </tr>
                    <tr>
                        <td style='padding: 8px 0; border-bottom: 1px solid #eee; font-weight: bold;'>Personas:</td>
                        <td style='padding: 8px 0; border-bottom: 1px solid #eee;'>{$reserva['total_personas']} personas</td>
                    </tr>
                    <tr>
                        <td style='padding: 8px 0; border-bottom: 1px solid #eee; font-weight: bold;'>Precio:</td>
                       <td style='padding: 8px 0; border-bottom: 1px solid #eee; color: #28a745; font-weight: bold;'>{$reserva['precio_final']}€</td>
                   </tr>
                   <tr>
                       <td style='padding: 8px 0; font-weight: bold;'>Agencia:</td>
                       <td style='padding: 8px 0; color: #7b1fa2; font-weight: bold;'>{$agency_name}</td>
                   </tr>
               </table>
               
               <div style='background: #f8d7da; border-left: 4px solid #dc3545; padding: 15px; margin-bottom: 25px;'>
                   <h3 style='margin: 0 0 10px 0; color: #721c24;'>Motivo de la Solicitud:</h3>
                   <p style='margin: 0; color: #721c24; font-style: italic;'>\"{$motivo}\"</p>
               </div>
               
               <div style='text-align: center; margin-top: 30px;'>
                   <p style='color: #666; margin-bottom: 20px;'>Accede al dashboard para gestionar esta solicitud</p>
                   <a href='" . home_url('/reservas-admin/') . "' style='display: inline-block; background: #0073aa; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; font-weight: bold;'>Ir al Dashboard</a>
               </div>
           </div>
           
           <div style='background: #f8f9fa; padding: 15px; text-align: center; font-size: 12px; color: #666;'>
               <p style='margin: 0;'>Este email fue enviado automáticamente por el sistema de reservas</p>
               <p style='margin: 5px 0 0 0;'>Fecha: " . date('d/m/Y H:i') . "</p>
           </div>
       </div>";

            // ✅ CORREGIR: Usar wp_mail directamente en lugar de send_email
            $headers = array(
                'Content-Type: text/html; charset=UTF-8',
                'From: ' . $nombre_remitente . ' <' . $email_remitente . '>'
            );

            $sent = wp_mail($email_admin, $subject, $body, $headers);

            if ($sent) {
                error_log("✅ Email de cancelación enviado al admin: " . $email_admin);
                return array('success' => true, 'message' => 'Email enviado al administrador correctamente');
            } else {
                error_log("❌ Error enviando email de cancelación al admin: " . $email_admin);
                return array('success' => false, 'message' => 'Error enviando email al administrador');
            }
        } catch (Exception $e) {
            error_log('Error enviando solicitud de cancelación: ' . $e->getMessage());
            return array(
                'success' => false,
                'message' => 'Error enviando email: ' . $e->getMessage()
            );
        }
    }


    /**
     * FUNCIÓN TEMPORAL - Enviar email SIN PDF para testing
     */
    public static function send_customer_confirmation_no_pdf($reserva_data)
    {
        error_log("=== TESTING EMAIL SIN PDF ===");

        $config = self::get_email_config();

        $to = $reserva_data['email'];
        $subject = "TEST - Confirmación de Reserva SIN PDF - Localizador: " . $reserva_data['localizador'];

        $message = self::build_customer_email_template($reserva_data);

        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . $config['nombre_remitente'] . ' <' . $config['email_remitente'] . '>'
        );

        error_log("=== ENVIANDO EMAIL SIN PDF ===");
        error_log("To: " . $to);
        error_log("From: " . $config['email_remitente']);

        $sent = wp_mail($to, $subject, $message, $headers);

        error_log("Email SIN PDF enviado: " . ($sent ? 'SÍ' : 'NO'));

        if ($sent) {
            error_log("✅ Email SIN PDF enviado correctamente");
            return array('success' => true, 'message' => 'Email sin PDF enviado correctamente');
        } else {
            error_log("❌ Error enviando email sin PDF");
            return array('success' => false, 'message' => 'Error enviando email sin PDF');
        }
    }

    /**
     * Enviar email de confirmación al cliente CON PDF ADJUNTO
     */
public static function send_customer_confirmation($reserva_data)
{
    error_log("=== INICIANDO ENVÍO EMAIL CLIENTE CON PDF ===");
    error_log("Email destino: " . $reserva_data['email']);
    
    $config = self::get_email_config();
    error_log("Configuración email: " . print_r($config, true));

    $to = $reserva_data['email'];
    $subject = "Confirmación de Reserva - Localizador: " . $reserva_data['localizador'];

    $message = self::build_customer_email_template($reserva_data);

    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        'From: ' . $config['nombre_remitente'] . ' <' . $config['email_remitente'] . '>'
    );

    // ✅ GENERAR PDF CON MANEJO DE ERRORES MEJORADO
    $attachments = array();
    $pdf_generated = false;
    
    try {
        error_log('=== INICIANDO GENERACIÓN DE PDF ===');
        
        // Verificar que tenemos todos los datos necesarios
        if (!isset($reserva_data['localizador']) || empty($reserva_data['localizador'])) {
            throw new Exception('Localizador no disponible para generar PDF');
        }
        
        $pdf_path = self::generate_ticket_pdf($reserva_data);
        error_log('PDF generado en: ' . $pdf_path);

        if ($pdf_path && file_exists($pdf_path)) {
            $file_size = filesize($pdf_path);
            error_log("✅ PDF existe - Tamaño: $file_size bytes");

            if ($file_size > 1000) { // PDF debe tener al menos 1KB
                $attachments[] = $pdf_path;
                $pdf_generated = true;
                error_log("✅ PDF añadido a attachments: " . $pdf_path);
            } else {
                error_log("❌ PDF está vacío o muy pequeño: $file_size bytes");
            }
        } else {
            error_log("❌ PDF no existe en: " . ($pdf_path ?? 'path undefined'));
        }
    } catch (Exception $e) {
        error_log("❌ Error generando PDF: " . $e->getMessage());
        error_log("❌ Stack trace: " . $e->getTraceAsString());
        
        // ✅ DECIDIR SI CONTINUAR SIN PDF O FALLAR
        // Por ahora, continuamos sin PDF para asegurar que el email se envíe
        error_log("⚠️ Continuando envío de email sin PDF debido a error");
    }

    // ✅ ENVIAR EMAIL CON O SIN PDF
    error_log("=== ENVIANDO EMAIL ===");
    error_log("To: " . $to);
    error_log("Subject: " . $subject);
    error_log("Attachments: " . ($pdf_generated ? "PDF incluido" : "SIN PDF"));

    $sent = wp_mail($to, $subject, $message, $headers, $attachments);

    // ✅ LIMPIAR ARCHIVOS TEMPORALES
    if (!empty($attachments)) {
        foreach ($attachments as $attachment) {
            if (file_exists($attachment)) {
                // Programar eliminación después de 5 minutos para dar tiempo al envío
                wp_schedule_single_event(time() + 300, 'delete_temp_pdf', array($attachment));
                error_log("📅 Programada eliminación de PDF temporal: " . $attachment);
            }
        }
    }

    error_log("Email enviado: " . ($sent ? 'SÍ' : 'NO'));

    if ($sent) {
        $success_msg = $pdf_generated ? 
            "Email enviado al cliente CON PDF adjunto: " . $to :
            "Email enviado al cliente SIN PDF (error generando PDF): " . $to;
        error_log("✅ " . $success_msg);
        return array('success' => true, 'message' => $success_msg);
    } else {
        error_log("❌ Error enviando email al cliente: " . $to);
        return array('success' => false, 'message' => 'Error enviando email al cliente');
    }
}

    /**
     * ✅ NUEVA FUNCIÓN: Generar PDF del billete
     */
private static function generate_ticket_pdf($reserva_data)
{
    // Cargar la clase del generador de PDF
    if (!class_exists('ReservasPDFGenerator')) {
        require_once RESERVAS_PLUGIN_PATH . 'includes/class-pdf-generator.php';
    }

    try {
        $pdf_generator = new ReservasPDFGenerator();
        $pdf_path = $pdf_generator->generate_ticket_pdf($reserva_data);
        
        // ✅ VALIDACIONES ADICIONALES
        if (!$pdf_path) {
            throw new Exception('PDF generator returned null path');
        }
        
        if (!file_exists($pdf_path)) {
            throw new Exception('PDF file was not created at: ' . $pdf_path);
        }
        
        $file_size = filesize($pdf_path);
        if ($file_size === false || $file_size < 1000) {
            throw new Exception('PDF file is empty or too small: ' . $file_size . ' bytes');
        }
        
        error_log("✅ PDF validado correctamente: {$pdf_path} ({$file_size} bytes)");
        return $pdf_path;
        
    } catch (Exception $e) {
        error_log('❌ Error en generación PDF desde email service: ' . $e->getMessage());
        throw new Exception('Error generando PDF: ' . $e->getMessage());
    }
}

private static function get_admin_email_by_service($reserva_data)
{
    $is_visita = isset($reserva_data['is_visita']) && $reserva_data['is_visita'] === true;
    
    if ($is_visita) {
        return ReservasConfigurationAdmin::get_config('email_visitas', get_option('admin_email'));
    } else {
        return ReservasConfigurationAdmin::get_config('email_reservas', get_option('admin_email'));
    }
}

/**
 * Enviar email de notificación al administrador (SIN PDF)
 * ✅ ACTUALIZADO: Detecta tipo de servicio y envía al email correcto
 */
public static function send_admin_notification($reserva_data)
{
    $config = self::get_email_config();

    // ✅ OBTENER EMAIL CORRECTO SEGÚN TIPO DE SERVICIO
    $admin_email = self::get_admin_email_by_service($reserva_data);

    if (empty($admin_email)) {
        error_log("❌ No hay email configurado para este tipo de servicio");
        return array('success' => false, 'message' => 'Email de notificaciones no configurado');
    }

    $is_visita = isset($reserva_data['is_visita']) && $reserva_data['is_visita'] === true;
    $tipo_servicio = $is_visita ? 'Visita Guiada' : 'Autobús';

    $to = $admin_email;
    $subject = "Nueva Reserva Recibida ({$tipo_servicio}) - " . $reserva_data['localizador'];

    $message = self::build_admin_email_template($reserva_data);

    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        'From: ' . $config['nombre_remitente'] . ' <' . $config['email_remitente'] . '>'
    );

    $sent = wp_mail($to, $subject, $message, $headers);

    if ($sent) {
        error_log("✅ Email enviado al administrador ({$tipo_servicio}): " . $to);
        return array('success' => true, 'message' => 'Email enviado al administrador correctamente');
    } else {
        error_log("❌ Error enviando email al administrador ({$tipo_servicio}): " . $to);
        return array('success' => false, 'message' => 'Error enviando email al administrador');
    }
}

public static function send_reminder_email($reserva_data)
{
    $config = self::get_email_config();

    $to = $reserva_data['email'];
    $fecha_servicio = date('d/m/Y', strtotime($reserva_data['fecha']));
    $subject = "Recordatorio - Tu viaje es mañana - Localizador: " . $reserva_data['localizador'];

    $message = self::build_reminder_email_template($reserva_data);

    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        'From: ' . $config['nombre_remitente'] . ' <' . $config['email_remitente'] . '>'
    );

    // ✅ ADJUNTAR PDF TAMBIÉN EN RECORDATORIOS CON MANEJO DE ERRORES
    $attachments = array();
    try {
        $pdf_path = self::generate_ticket_pdf($reserva_data);
        if ($pdf_path && file_exists($pdf_path) && filesize($pdf_path) > 1000) {
            $attachments[] = $pdf_path;
            error_log("✅ PDF generado para recordatorio: " . $pdf_path);
        }
    } catch (Exception $e) {
        error_log("❌ Error generando PDF para recordatorio: " . $e->getMessage());
        // Continuar sin PDF
    }

    $sent = wp_mail($to, $subject, $message, $headers, $attachments);

    // Limpiar archivo temporal
    if (!empty($attachments)) {
        foreach ($attachments as $attachment) {
            if (file_exists($attachment)) {
                wp_schedule_single_event(time() + 300, 'delete_temp_pdf', array($attachment));
            }
        }
    }

    if ($sent) {
        error_log("✅ Email de recordatorio enviado al cliente: " . $to);
        return array('success' => true, 'message' => 'Recordatorio enviado correctamente');
    } else {
        error_log("❌ Error enviando recordatorio al cliente: " . $to);
        return array('success' => false, 'message' => 'Error enviando recordatorio');
    }
}

private static function get_email_config()
{
    if (!class_exists('ReservasConfigurationAdmin')) {
        require_once RESERVAS_PLUGIN_PATH . 'includes/class-configuration-admin.php';
    }

    return array(
        'email_remitente' => ReservasConfigurationAdmin::get_config('email_remitente', get_option('admin_email')),
        'nombre_remitente' => ReservasConfigurationAdmin::get_config('nombre_remitente', get_bloginfo('name')),
        'email_reservas' => ReservasConfigurationAdmin::get_config('email_reservas', get_option('admin_email')),
        'email_visitas' => ReservasConfigurationAdmin::get_config('email_visitas', get_option('admin_email')),
    );
}

private static function build_customer_email_template($reserva)
{
    $fecha_formateada = date('d/m/Y', strtotime($reserva['fecha']));
    $fecha_creacion = date('d/m/Y H:i', strtotime($reserva['created_at'] ?? 'now'));

    // ✅ DETECTAR SI ES VISITA GUIADA
    $is_visita = isset($reserva['is_visita']) && $reserva['is_visita'] === true;

    $personas_detalle = "";
    if ($reserva['adultos'] > 0) $personas_detalle .= "Adultos: " . $reserva['adultos'] . "<br>";
    if ($reserva['residentes'] > 0) $personas_detalle .= "Residentes: " . $reserva['residentes'] . "<br>";
    if ($reserva['ninos_5_12'] > 0) $personas_detalle .= "Niños (5-12 años): " . $reserva['ninos_5_12'] . "<br>";
    if ($reserva['ninos_menores'] > 0) $personas_detalle .= "Niños menores (gratis): " . $reserva['ninos_menores'] . "<br>";

    $descuento_info = "";
    if ($reserva['descuento_total'] > 0) {
        $descuento_info = "<tr>
        <td style='padding: 15px 25px; border-bottom: 1px solid #E0E0E0; background: #FFF8DC; font-weight: 600; color: #871727;'>Descuentos aplicados:</td>
        <td style='padding: 15px 25px; border-bottom: 1px solid #E0E0E0; background: #FFF8DC; text-align: right; color: #871727; font-weight: bold; font-size: 16px;'>-" . number_format($reserva['descuento_total'], 2) . "€</td>
    </tr>";
    }

    // ✅ DEFINIR NOMBRE DEL PRODUCTO SEGÚN TIPO
    if ($is_visita) {
        $producto_nombre = 'Visita Guiada Medina Azahara';
        $producto_detalle = 'Visita Guiada Medina Azahara (' . substr($reserva['hora'], 0, 5) . ' hrs)';
    } else {
        $producto_nombre = 'TAQ BUS Madinat Al-Zahra + Lanzadera';
        $producto_detalle = 'TAQ BUS Madinat Al-Zahra + Lanzadera (' . substr($reserva['hora'], 0, 5) . ' hrs)';
    }

    return "
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>Confirmación de Reserva - Medina Azahara</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
    </style>
</head>
<body style='font-family: \"Inter\", -apple-system, BlinkMacSystemFont, sans-serif; line-height: 1.6; color: #2D2D2D; max-width: 600px; margin: 0 auto; padding: 0; background: #FAFAFA;'>
    
    <!-- Header -->
    <div style='background: linear-gradient(135deg, #871727 0%, #A91D33 100%); color: #FFFFFF; text-align: center; padding: 50px 30px;'>
        <h1 style='margin: 0; font-size: 32px; font-weight: 700; letter-spacing: -0.5px;'>RESERVA CONFIRMADA</h1>
        <div style='width: 60px; height: 3px; background: #EFCF4B; margin: 20px auto; border-radius: 2px;'></div>
        <p style='margin: 0; font-size: 18px; font-weight: 500; opacity: 0.95;'>Tu " . ($is_visita ? 'visita guiada' : 'viaje') . " a Medina Azahara está asegurado</p>
    </div>

    <!-- Contenido principal -->
    <div style='background: #FFFFFF; padding: 0;'>
        
        <!-- Localizador destacado -->
        <div style='background: #EFCF4B; padding: 30px; text-align: center; border-bottom: 1px solid #E0E0E0;'>
            <h2 style='margin: 0 0 10px 0; font-size: 16px; font-weight: 600; color: #2D2D2D; text-transform: uppercase; letter-spacing: 1px;'>LOCALIZADOR DE RESERVA</h2>
            <div style='font-size: 28px; font-weight: 700; color: #871727; letter-spacing: 3px; font-family: monospace; margin: 10px 0;'>" . $reserva['localizador'] . "</div>
            <p style='margin: 0; font-size: 14px; color: #2D2D2D; font-weight: 500;'>Presenta este código al " . ($is_visita ? 'iniciar la visita' : 'subir al autobús') . "</p>
        </div>

        <!-- Información de la reserva -->
        <div style='padding: 40px 30px; border-bottom: 1px solid #E0E0E0;'>
            <h3 style='margin: 0 0 25px 0; font-size: 20px; font-weight: 700; color: #871727; text-align: center;'>Detalles de tu Reserva</h3>
            
            <table style='width: 100%; border-collapse: collapse; background: #FFFFFF; border: 2px solid #EFCF4B; border-radius: 8px; overflow: hidden;'>
                <tr>
                    <td style='padding: 15px 25px; border-bottom: 1px solid #E0E0E0; font-weight: 600; color: #2D2D2D;'>Fecha " . ($is_visita ? 'de la visita' : 'del viaje') . ":</td>
                    <td style='padding: 15px 25px; border-bottom: 1px solid #E0E0E0; text-align: right; font-weight: 700; color: #871727;'>" . $fecha_formateada . "</td>
                </tr>
                <tr>
                    <td style='padding: 15px 25px; border-bottom: 1px solid #E0E0E0; font-weight: 600; color: #2D2D2D;'>Hora de " . ($is_visita ? 'inicio' : 'salida') . ":</td>
                    <td style='padding: 15px 25px; border-bottom: 1px solid #E0E0E0; text-align: right; font-weight: 700; color: #871727; font-size: 18px;'>" . substr($reserva['hora'], 0, 5) . "</td>
                </tr>";
    
    // ✅ HORA DE VUELTA - SOLO PARA AUTOBÚS
    if (!$is_visita) {
        $return_html = "
                <tr>
                    <td style='padding: 15px 25px; border-bottom: 1px solid #E0E0E0; font-weight: 600; color: #2D2D2D;'>Hora de vuelta:</td>
                    <td style='padding: 15px 25px; border-bottom: 1px solid #E0E0E0; text-align: right; font-weight: 700; color: #871727;'>" . substr($reserva['hora_vuelta'] ?? '', 0, 5) . "</td>
                </tr>";
    } else {
        $return_html = "";
    }
    
    $html_content = $return_html . "
                <tr>
                    <td style='padding: 15px 25px; border-bottom: 1px solid #E0E0E0; font-weight: 600; color: #2D2D2D;'>Fecha de reserva:</td>
                    <td style='padding: 15px 25px; border-bottom: 1px solid #E0E0E0; text-align: right; color: #666666;'>" . $fecha_creacion . "</td>
                </tr>
            </table>
        </div>

        <!-- Datos del cliente -->
        <div style='padding: 40px 30px; background: #F8F9FA; border-bottom: 1px solid #E0E0E0;'>
            <h3 style='margin: 0 0 25px 0; font-size: 20px; font-weight: 700; color: #871727; text-align: center;'>Datos del Viajero</h3>
            
            <div style='background: #FFFFFF; padding: 25px; border-radius: 8px; border: 1px solid #E0E0E0;'>
                <p style='margin: 8px 0; color: #2D2D2D; font-size: 16px;'><strong style='color: #871727;'>Nombre completo:</strong> " . $reserva['nombre'] . " " . $reserva['apellidos'] . "</p>
                <p style='margin: 8px 0; color: #2D2D2D; font-size: 16px;'><strong style='color: #871727;'>Email:</strong> " . $reserva['email'] . "</p>
                <p style='margin: 8px 0; color: #2D2D2D; font-size: 16px;'><strong style='color: #871727;'>Teléfono:</strong> " . $reserva['telefono'] . "</p>
            </div>
        </div>

        <!-- Distribución de personas -->
        <div style='padding: 40px 30px; border-bottom: 1px solid #E0E0E0;'>
            <h3 style='margin: 0 0 25px 0; font-size: 20px; font-weight: 700; color: #871727; text-align: center;'>Distribución de Viajeros</h3>
            
            <div style='background: #F8F9FA; padding: 25px; border-radius: 8px; border: 1px solid #E0E0E0;'>
                <div style='font-size: 16px; color: #2D2D2D; line-height: 1.8;'>
                    " . $personas_detalle . "
                </div>
                <div style='margin-top: 20px; padding-top: 20px; border-top: 2px solid #EFCF4B; text-align: center;'>
                    <p style='margin: 0; font-weight: 700; color: #871727; font-size: 18px;'>Total personas con plaza: " . $reserva['total_personas'] . "</p>
                </div>
            </div>
        </div>

        <!-- Resumen de precios -->
        <div style='padding: 40px 30px; background: #F8F9FA;'>
            <h3 style='margin: 0 0 25px 0; font-size: 20px; font-weight: 700; color: #871727; text-align: center;'>Resumen de Precios</h3>
            
            <table style='width: 100%; border-collapse: collapse; background: #FFFFFF; border: 2px solid #EFCF4B; border-radius: 8px; overflow: hidden;'>
                <tr>
                    <td style='padding: 15px 25px; border-bottom: 1px solid #E0E0E0; font-weight: 600; color: #2D2D2D;'>Precio base:</td>
                    <td style='padding: 15px 25px; border-bottom: 1px solid #E0E0E0; text-align: right; font-weight: 600; color: #2D2D2D;'>" . number_format($reserva['precio_base'], 2) . "€</td>
                </tr>
                " . $descuento_info . "
                <tr style='background: #871727;'>
                    <td style='padding: 20px 25px; font-size: 20px; font-weight: 700; color: #FFFFFF;'>TOTAL PAGADO:</td>
                    <td style='padding: 20px 25px; text-align: right; font-size: 24px; font-weight: 700; color: #FFFFFF;'>" . number_format($reserva['precio_final'], 2) . "€</td>
                </tr>
            </table>
        </div>

        <!-- Información importante -->
        <div style='padding: 40px 30px; background: #FFFFFF;'>
            <h3 style='margin: 0 0 25px 0; font-size: 20px; font-weight: 700; color: #871727; text-align: center;'>Información Importante</h3>
            
            <div style='background: #F8F9FA; padding: 30px; border-radius: 8px; border-left: 4px solid #EFCF4B;'>
                <ul style='margin: 0; padding-left: 25px; color: #2D2D2D; line-height: 1.8; font-size: 16px;'>
                    <li style='margin: 12px 0;'><strong style='color: #871727;'>Presenta tu localizador:</strong> <span style='background: #EFCF4B; color: #2D2D2D; padding: 3px 8px; border-radius: 4px; font-weight: 700; font-family: monospace;'>" . $reserva['localizador'] . "</span> al " . ($is_visita ? 'iniciar la visita' : 'subir al autobús') . "</li>
                    <li style='margin: 12px 0;'><strong style='color: #871727;'>Puntualidad:</strong> Preséntate 15 minutos antes de la hora de " . ($is_visita ? 'inicio' : 'salida') . "</li>";
    
    // ✅ INFORMACIÓN ESPECÍFICA SEGÚN TIPO
    if (!$is_visita) {
        $html_content .= "
                    <li style='margin: 12px 0;'><strong style='color: #871727;'>Residentes:</strong> Deben presentar documento acreditativo de residencia en Córdoba</li>
                    <li style='margin: 12px 0;'><strong style='color: #871727;'>Niños menores:</strong> Los menores de 5 años viajan gratis sin ocupar plaza</li>";
    } else {
        $html_content .= "
                    <li style='margin: 12px 0;'><strong style='color: #871727;'>Duración:</strong> Aproximadamente 3 horas y media</li>
                    <li style='margin: 12px 0;'><strong style='color: #871727;'>Niños menores:</strong> Los menores de 5 años no pagan entrada</li>";
    }
    
    $html_content .= "
                    <li style='margin: 12px 0;'><strong style='color: #871727;'>Contacto:</strong> Para cualquier consulta, contacta con nosotros</li>
                </ul>
            </div>
            
            <!-- Mensaje final -->
            <div style='text-align: center; margin-top: 40px; padding: 30px; background: #871727; border-radius: 8px;'>
                <p style='margin: 0; color: #FFFFFF; font-size: 20px; font-weight: 700;'>
                    ¡Disfruta de tu " . ($is_visita ? 'visita guiada a' : 'viaje a') . " Medina Azahara!
                </p>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div style='text-align: center; padding: 40px 30px; background: #2D2D2D; color: #FFFFFF;'>
        <div style='width: 40px; height: 2px; background: #EFCF4B; margin: 0 auto 20px;'></div>
        <p style='margin: 0 0 15px 0; font-size: 14px; opacity: 0.8; line-height: 1.6;'>
            Este es un email automático de confirmación de tu reserva.<br>
            Si tienes alguna duda, ponte en contacto con nosotros.
        </p>
        <p style='margin: 0; color: #EFCF4B; font-weight: 600; font-size: 16px;'>
            Gracias por elegir nuestros servicios
        </p>
    </div>

</body>
</html>";

    return $html_content;
}

    private static function build_reminder_email_template($reserva)
    {
        $fecha_formateada = date('d/m/Y', strtotime($reserva['fecha']));
        $dia_semana = date('l', strtotime($reserva['fecha']));
        $dias_semana_es = array(
            'Monday' => 'Lunes',
            'Tuesday' => 'Martes',
            'Wednesday' => 'Miércoles',
            'Thursday' => 'Jueves',
            'Friday' => 'Viernes',
            'Saturday' => 'Sábado',
            'Sunday' => 'Domingo'
        );
        $dia_semana_es = $dias_semana_es[$dia_semana] ?? $dia_semana;

        $personas_detalle = "";
        if ($reserva['adultos'] > 0) $personas_detalle .= "Adultos: " . $reserva['adultos'] . "<br>";
        if ($reserva['residentes'] > 0) $personas_detalle .= "Residentes: " . $reserva['residentes'] . "<br>";
        if ($reserva['ninos_5_12'] > 0) $personas_detalle .= "Niños (5-12 años): " . $reserva['ninos_5_12'] . "<br>";
        if ($reserva['ninos_menores'] > 0) $personas_detalle .= "Niños menores (gratis): " . $reserva['ninos_menores'] . "<br>";

        return "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <title>Recordatorio de Viaje - Medina Azahara</title>
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        </style>
    </head>
    <body style='font-family: \"Inter\", -apple-system, BlinkMacSystemFont, sans-serif; line-height: 1.6; color: #2D2D2D; max-width: 600px; margin: 0 auto; padding: 0; background: #FAFAFA;'>
        
        <!-- Header -->
        <div style='background: linear-gradient(135deg, #871727 0%, #A91D33 100%); color: #FFFFFF; text-align: center; padding: 50px 30px;'>
            <h1 style='margin: 0; font-size: 32px; font-weight: 700; letter-spacing: -0.5px;'>RECORDATORIO DE VIAJE</h1>
            <div style='width: 60px; height: 3px; background: #EFCF4B; margin: 20px auto; border-radius: 2px;'></div>
            <p style='margin: 0; font-size: 18px; font-weight: 500; opacity: 0.95;'>Tu visita a Medina Azahara es muy pronto</p>
        </div>

        <!-- Contenido principal -->
        <div style='background: #FFFFFF; padding: 0;'>
            
            <!-- Localizador destacado -->
            <div style='background: #EFCF4B; padding: 30px; text-align: center; border-bottom: 1px solid #E0E0E0;'>
                <h2 style='margin: 0 0 10px 0; font-size: 16px; font-weight: 600; color: #2D2D2D; text-transform: uppercase; letter-spacing: 1px;'>LOCALIZADOR DE RESERVA</h2>
                <div style='font-size: 28px; font-weight: 700; color: #871727; letter-spacing: 3px; font-family: monospace; margin: 10px 0;'>" . $reserva['localizador'] . "</div>
                <p style='margin: 0; font-size: 14px; color: #2D2D2D; font-weight: 500;'>Tu viaje es mañana</p>
            </div>

            <!-- Información del viaje -->
            <div style='padding: 40px 30px; border-bottom: 1px solid #E0E0E0;'>
                <h3 style='margin: 0 0 25px 0; font-size: 20px; font-weight: 700; color: #871727; text-align: center;'>Detalles de tu Viaje</h3>
                
                <table style='width: 100%; border-collapse: collapse; background: #FFFFFF; border: 2px solid #EFCF4B; border-radius: 8px; overflow: hidden;'>
                    <tr>
                        <td style='padding: 15px 25px; border-bottom: 1px solid #E0E0E0; font-weight: 600; color: #2D2D2D;'>Fecha:</td>
                        <td style='padding: 15px 25px; border-bottom: 1px solid #E0E0E0; text-align: right; font-weight: 700; color: #871727;'>" . $dia_semana_es . ", " . $fecha_formateada . "</td>
                    </tr>
                    <tr>
    <td style='padding: 15px 25px; border-bottom: 1px solid #E0E0E0; font-weight: 600; color: #2D2D2D;'>Hora de salida:</td>
    <td style='padding: 15px 25px; border-bottom: 1px solid #E0E0E0; text-align: right; font-weight: 700; color: #871727;'>" . substr($reserva['hora'], 0, 5) . "</td>
</tr>
<tr>
    <td style='padding: 15px 25px; font-weight: 600; color: #2D2D2D;'>Hora de vuelta:</td>
    <td style='padding: 15px 25px; text-align: right; font-weight: 700; color: #871727;'>" . substr($reserva['hora_vuelta'] ?? '', 0, 5) . "</td>
</tr>
                    <tr>
                        <td style='padding: 15px 25px; border-bottom: 1px solid #E0E0E0; font-weight: 600; color: #2D2D2D;'>Cliente:</td>
                        <td style='padding: 15px 25px; border-bottom: 1px solid #E0E0E0; text-align: right; color: #666666;'>" . $reserva['nombre'] . " " . $reserva['apellidos'] . "</td>
                    </tr>
                    <tr>
                        <td style='padding: 15px 25px; font-weight: 600; color: #2D2D2D;'>Teléfono:</td>
                        <td style='padding: 15px 25px; text-align: right; color: #666666;'>" . $reserva['telefono'] . "</td>
                    </tr>
                </table>
            </div>

            <!-- Distribución de personas -->
            <div style='padding: 40px 30px; background: #F8F9FA; border-bottom: 1px solid #E0E0E0;'>
                <h3 style='margin: 0 0 25px 0; font-size: 20px; font-weight: 700; color: #871727; text-align: center;'>Personas en tu Reserva</h3>
                
                <div style='background: #FFFFFF; padding: 25px; border-radius: 8px; border: 1px solid #E0E0E0;'>
                    <div style='font-size: 16px; color: #2D2D2D; line-height: 1.8;'>
                        " . $personas_detalle . "
                    </div>
                    <div style='margin-top: 20px; padding-top: 20px; border-top: 2px solid #EFCF4B; text-align: center;'>
                        <p style='margin: 0; font-weight: 700; color: #871727; font-size: 18px;'>Total personas con plaza: " . $reserva['total_personas'] . "</p>
                    </div>
                </div>
            </div>

            <!-- Recordatorios importantes -->
            <div style='padding: 40px 30px; background: #FFFFFF; border-bottom: 1px solid #E0E0E0;'>
                <h3 style='margin: 0 0 25px 0; font-size: 20px; font-weight: 700; color: #871727; text-align: center;'>Recordatorios Importantes</h3>
                
                <div style='background: #F8F9FA; padding: 30px; border-radius: 8px; border-left: 4px solid #EFCF4B;'>
                    <ul style='margin: 0; padding-left: 25px; color: #2D2D2D; line-height: 1.8; font-size: 16px;'>
                        <li style='margin: 12px 0;'><strong style='color: #871727;'>Presenta tu localizador:</strong> <span style='background: #EFCF4B; color: #2D2D2D; padding: 3px 8px; border-radius: 4px; font-weight: 700; font-family: monospace;'>" . $reserva['localizador'] . "</span> al subir al autobús</li>
                        <li style='margin: 12px 0;'><strong style='color: #871727;'>Puntualidad:</strong> Llega 15 minutos antes de las " . substr($reserva['hora'], 0, 5) . "</li>
                        <li style='margin: 12px 0;'><strong style='color: #871727;'>Residentes:</strong> Deben presentar documento acreditativo de residencia en Córdoba</li>
                        <li style='margin: 12px 0;'><strong style='color: #871727;'>Niños menores:</strong> Los menores de 5 años viajan gratis sin ocupar plaza</li>
                        <li style='margin: 12px 0;'><strong style='color: #871727;'>Punto de encuentro:</strong> Paseo de la Victoria (glorieta Hospital Cruz Roja)</li>
                    </ul>
                </div>
            </div>

            <!-- Total de la reserva -->
            <div style='padding: 40px 30px; background: #F8F9FA;'>
                <h3 style='margin: 0 0 25px 0; font-size: 20px; font-weight: 700; color: #871727; text-align: center;'>Total de tu Reserva</h3>
                
                <table style='width: 100%; border-collapse: collapse; background: #FFFFFF; border: 2px solid #EFCF4B; border-radius: 8px; overflow: hidden;'>
                    <tr style='background: #871727;'>
                        <td style='padding: 20px 25px; font-size: 20px; font-weight: 700; color: #FFFFFF;'>TOTAL PAGADO:</td>
                        <td style='padding: 20px 25px; text-align: right; font-size: 24px; font-weight: 700; color: #FFFFFF;'>" . number_format($reserva['precio_final'], 2) . "€</td>
                    </tr>
                </table>
                
                <div style='text-align: center; margin-top: 30px; padding: 25px; background: #FFFFFF; border: 1px solid #E0E0E0; border-radius: 8px;'>
                    <p style='margin: 0; color: #28a745; font-weight: 700; font-size: 16px;'>Reserva confirmada y pagada</p>
                </div>
            </div>

            <!-- Mensaje final -->
            <div style='padding: 40px 30px; background: #FFFFFF;'>
                <div style='text-align: center; padding: 30px; background: #871727; border-radius: 8px;'>
                    <h3 style='color: #FFFFFF; margin: 0 0 15px 0; font-size: 24px; font-weight: 700;'>¿Todo preparado?</h3>
                    <p style='margin: 0 0 15px 0; color: #FFFFFF; font-size: 18px; font-weight: 500;'>
                        Te esperamos mañana para descubrir juntos las maravillas de Medina Azahara
                    </p>
                    <p style='margin: 0; color: #EFCF4B; font-size: 16px; font-weight: 600;'>
                        Si tienes alguna duda de última hora, no dudes en contactarnos
                    </p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div style='text-align: center; padding: 40px 30px; background: #2D2D2D; color: #FFFFFF;'>
            <div style='width: 40px; height: 2px; background: #EFCF4B; margin: 0 auto 20px;'></div>
            <p style='margin: 0 0 15px 0; font-size: 14px; opacity: 0.8; line-height: 1.6;'>
                Este es un recordatorio automático de tu reserva para mañana.<br>
                ¡Te deseamos un viaje fantástico!
            </p>
            <p style='margin: 0; color: #EFCF4B; font-weight: 600; font-size: 16px;'>
                Medina Azahara te espera
            </p>
        </div>

    </body>
    </html>";
    }

    /**
     * Template de email para el administrador
     */
    private static function build_admin_email_template($reserva)
    {
        $fecha_formateada = date('d/m/Y', strtotime($reserva['fecha']));
        $fecha_creacion = date('d/m/Y H:i', strtotime($reserva['created_at'] ?? 'now'));

        $personas_detalle = "";
        if ($reserva['adultos'] > 0) $personas_detalle .= "Adultos: " . $reserva['adultos'] . "<br>";
        if ($reserva['residentes'] > 0) $personas_detalle .= "Residentes: " . $reserva['residentes'] . "<br>";
        if ($reserva['ninos_5_12'] > 0) $personas_detalle .= "Niños (5-12 años): " . $reserva['ninos_5_12'] . "<br>";
        if ($reserva['ninos_menores'] > 0) $personas_detalle .= "Niños menores (gratis): " . $reserva['ninos_menores'] . "<br>";

        $descuento_info = "";
        if ($reserva['descuento_total'] > 0) {
            $descuento_info = "<tr>
            <td style='padding: 15px 25px; border-bottom: 1px solid #E0E0E0; background: #FFF8DC; font-weight: 600; color: #871727;'>Descuentos aplicados:</td>
            <td style='padding: 15px 25px; border-bottom: 1px solid #E0E0E0; background: #FFF8DC; text-align: right; color: #871727; font-weight: bold; font-size: 16px;'>-" . number_format($reserva['descuento_total'], 2) . "€</td>
        </tr>";
        }

        return "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <title>Nueva Reserva Recibida - Sistema Medina Azahara</title>
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        </style>
    </head>
    <body style='font-family: \"Inter\", -apple-system, BlinkMacSystemFont, sans-serif; line-height: 1.6; color: #2D2D2D; max-width: 700px; margin: 0 auto; padding: 0; background: #FAFAFA;'>
        
        <!-- Header Administrativo -->
        <div style='background: linear-gradient(135deg, #871727 0%, #A91D33 100%); color: #FFFFFF; text-align: center; padding: 50px 30px;'>
            <h1 style='margin: 0; font-size: 32px; font-weight: 700; letter-spacing: -0.5px;'>NUEVA RESERVA RECIBIDA</h1>
            <div style='width: 60px; height: 3px; background: #EFCF4B; margin: 20px auto; border-radius: 2px;'></div>
            <p style='margin: 0; font-size: 18px; font-weight: 500; opacity: 0.95;'>Se ha procesado una nueva reserva en el sistema</p>
        </div>

        <!-- Contenido principal -->
        <div style='background: #FFFFFF; padding: 0;'>
            
            <!-- Localizador destacado -->
            <div style='background: #EFCF4B; padding: 30px; text-align: center; border-bottom: 1px solid #E0E0E0;'>
                <h2 style='margin: 0 0 10px 0; font-size: 16px; font-weight: 600; color: #2D2D2D; text-transform: uppercase; letter-spacing: 1px;'>LOCALIZADOR DE RESERVA</h2>
                <div style='font-size: 28px; font-weight: 700; color: #871727; letter-spacing: 3px; font-family: monospace; margin: 10px 0;'>" . $reserva['localizador'] . "</div>
                <p style='margin: 0; font-size: 14px; color: #2D2D2D; font-weight: 500;'>Nueva reserva para revisar</p>
            </div>

            <!-- Información de la reserva -->
            <div style='padding: 40px 30px; border-bottom: 1px solid #E0E0E0;'>
                <h3 style='margin: 0 0 25px 0; font-size: 20px; font-weight: 700; color: #871727; text-align: center;'>Información de la Reserva</h3>
                
                <table style='width: 100%; border-collapse: collapse; background: #FFFFFF; border: 2px solid #EFCF4B; border-radius: 8px; overflow: hidden;'>
                    <tr>
    <td style='padding: 15px 25px; border-bottom: 1px solid #E0E0E0; font-weight: 600; color: #2D2D2D;'>Fecha del servicio:</td>
    <td style='padding: 15px 25px; border-bottom: 1px solid #E0E0E0; text-align: right; font-weight: 700; color: #871727;'>" . $fecha_formateada . " - Salida: " . substr($reserva['hora'], 0, 5) . " - Vuelta: " . substr($reserva['hora_vuelta'] ?? '', 0, 5) . "</td>
</tr>
                    <tr>
                        <td style='padding: 15px 25px; border-bottom: 1px solid #E0E0E0; font-weight: 600; color: #2D2D2D;'>Fecha de reserva:</td>
                        <td style='padding: 15px 25px; border-bottom: 1px solid #E0E0E0; text-align: right; color: #666666;'>" . $fecha_creacion . "</td>
                    </tr>
                    <tr>
                        <td style='padding: 15px 25px; border-bottom: 1px solid #E0E0E0; font-weight: 600; color: #2D2D2D;'>Total personas:</td>
                        <td style='padding: 15px 25px; border-bottom: 1px solid #E0E0E0; text-align: right; font-weight: 700; color: #871727; font-size: 18px;'>" . $reserva['total_personas'] . " plazas ocupadas</td>
                    </tr>
                    <tr>
                        <td style='padding: 15px 25px; border-bottom: 1px solid #E0E0E0; font-weight: 600; color: #2D2D2D;'>Precio base:</td>
                        <td style='padding: 15px 25px; border-bottom: 1px solid #E0E0E0; text-align: right; font-weight: 600; color: #2D2D2D;'>" . number_format($reserva['precio_base'], 2) . "€</td>
                    </tr>
                    " . $descuento_info . "
                    <tr style='background: #871727;'>
                        <td style='padding: 20px 25px; font-size: 20px; font-weight: 700; color: #FFFFFF;'>TOTAL PAGADO:</td>
                        <td style='padding: 20px 25px; text-align: right; font-size: 24px; font-weight: 700; color: #FFFFFF;'>" . number_format($reserva['precio_final'], 2) . "€</td>
                    </tr>
                </table>
            </div>

            <!-- Datos del cliente -->
            <div style='padding: 40px 30px; background: #F8F9FA; border-bottom: 1px solid #E0E0E0;'>
                <h3 style='margin: 0 0 25px 0; font-size: 20px; font-weight: 700; color: #871727; text-align: center;'>Datos del Cliente</h3>
                
                <div style='background: #FFFFFF; padding: 25px; border-radius: 8px; border: 1px solid #E0E0E0;'>
                    <p style='margin: 8px 0; color: #2D2D2D; font-size: 16px;'><strong style='color: #871727;'>Nombre completo:</strong> " . $reserva['nombre'] . " " . $reserva['apellidos'] . "</p>
                    <p style='margin: 8px 0; color: #2D2D2D; font-size: 16px;'><strong style='color: #871727;'>Email:</strong> <a href='mailto:" . $reserva['email'] . "' style='color: #871727; text-decoration: none;'>" . $reserva['email'] . "</a></p>
                    <p style='margin: 8px 0; color: #2D2D2D; font-size: 16px;'><strong style='color: #871727;'>Teléfono:</strong> <a href='tel:" . $reserva['telefono'] . "' style='color: #871727; text-decoration: none;'>" . $reserva['telefono'] . "</a></p>
                </div>
            </div>

            <!-- Distribución de personas -->
            <div style='padding: 40px 30px; border-bottom: 1px solid #E0E0E0;'>
                <h3 style='margin: 0 0 25px 0; font-size: 20px; font-weight: 700; color: #871727; text-align: center;'>Distribución de Viajeros</h3>
                
                <div style='background: #F8F9FA; padding: 25px; border-radius: 8px; border: 1px solid #E0E0E0;'>
                    <div style='font-size: 16px; color: #2D2D2D; line-height: 1.8;'>
                        " . $personas_detalle . "
                    </div>
                    <div style='margin-top: 20px; padding-top: 20px; border-top: 2px solid #EFCF4B; text-align: center;'>
                        <p style='margin: 0; font-weight: 700; color: #871727; font-size: 18px;'>Total personas con plaza: " . $reserva['total_personas'] . "</p>
                    </div>
                </div>
            </div>

            <!-- Acciones recomendadas -->
            <div style='padding: 40px 30px; background: #FFFFFF;'>
                <h3 style='margin: 0 0 25px 0; font-size: 20px; font-weight: 700; color: #871727; text-align: center;'>Acciones Recomendadas</h3>
                
                <div style='background: #F8F9FA; padding: 30px; border-radius: 8px; border-left: 4px solid #EFCF4B;'>
                    <ul style='margin: 0; padding-left: 25px; color: #2D2D2D; line-height: 1.8; font-size: 16px;'>
                        <li style='margin: 12px 0;'><strong style='color: #871727;'>Verificar disponibilidad:</strong> Comprobar plazas disponibles para la fecha</li>
                        <li style='margin: 12px 0;'><strong style='color: #871727;'>Revisar documentación:</strong> Confirmar documentos de residentes si aplica</li>
                        <li style='margin: 12px 0;'><strong style='color: #871727;'>Gestionar reserva:</strong> Acceder al panel de administración</li>
                        <li style='margin: 12px 0;'><strong style='color: #871727;'>Contactar cliente:</strong> Si necesitas aclarar algún detalle</li>
                    </ul>
                </div>
                
                <!-- Mensaje final -->
                <div style='text-align: center; margin-top: 40px; padding: 30px; background: #871727; border-radius: 8px;'>
                    <p style='margin: 0; color: #FFFFFF; font-size: 20px; font-weight: 700;'>
                        Reserva lista para procesar
                    </p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div style='text-align: center; padding: 40px 30px; background: #2D2D2D; color: #FFFFFF;'>
            <div style='width: 40px; height: 2px; background: #EFCF4B; margin: 0 auto 20px;'></div>
            <p style='margin: 0 0 15px 0; font-size: 14px; opacity: 0.8; line-height: 1.6;'>
                Este es un email automático del sistema de reservas.<br>
                Puedes gestionar esta reserva desde el panel de administración.
            </p>
            <p style='margin: 0; color: #EFCF4B; font-weight: 600; font-size: 16px;'>
                Sistema de Reservas - Medina Azahara
            </p>
        </div>

    </body>
    </html>";
    }

    /**
     * Reenviar email de confirmación
     */
    public static function resend_confirmation($reserva_id)
    {
        global $wpdb;

        $table_reservas = $wpdb->prefix . 'reservas_reservas';

        $reserva = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_reservas WHERE id = %d",
            $reserva_id
        ));

        if (!$reserva) {
            return array('success' => false, 'message' => 'Reserva no encontrada');
        }

        // Convertir objeto a array para el template
        $reserva_array = (array) $reserva;

        return self::send_customer_confirmation($reserva_array);
    }

    public static function send_cancellation_email($reserva_data)
    {
        $config = self::get_email_config();

        $to = $reserva_data['email'];
        $subject = "Reserva Cancelada - Localizador: " . $reserva_data['localizador'];

        $message = self::build_cancellation_email_template($reserva_data);

        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . $config['nombre_remitente'] . ' <' . $config['email_remitente'] . '>'
        );

        $sent = wp_mail($to, $subject, $message, $headers);

        if ($sent) {
            error_log("✅ Email de cancelación enviado al cliente: " . $to);
            return array('success' => true, 'message' => 'Email de cancelación enviado correctamente');
        } else {
            error_log("❌ Error enviando email de cancelación al cliente: " . $to);
            return array('success' => false, 'message' => 'Error enviando email de cancelación');
        }
    }

    private static function build_cancellation_email_template($reserva)
    {
        $fecha_formateada = date('d/m/Y', strtotime($reserva['fecha']));
        $motivo = $reserva['motivo_cancelacion'] ?? 'Cancelación administrativa';
        $fecha_cancelacion = date('d/m/Y H:i', strtotime($reserva['fecha_cancelacion'] ?? 'now'));

        return "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <title>Reserva Cancelada - Medina Azahara</title>
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        </style>
    </head>
    <body style='font-family: \"Inter\", -apple-system, BlinkMacSystemFont, sans-serif; line-height: 1.6; color: #2D2D2D; max-width: 600px; margin: 0 auto; padding: 0; background: #FAFAFA;'>
        
        <!-- Header -->
        <div style='background: linear-gradient(135deg, #871727 0%, #A91D33 100%); color: #FFFFFF; text-align: center; padding: 50px 30px;'>
            <h1 style='margin: 0; font-size: 32px; font-weight: 700; letter-spacing: -0.5px;'>RESERVA CANCELADA</h1>
            <div style='width: 60px; height: 3px; background: #EFCF4B; margin: 20px auto; border-radius: 2px;'></div>
            <p style='margin: 0; font-size: 18px; font-weight: 500; opacity: 0.95;'>Tu reserva ha sido cancelada</p>
        </div>

        <!-- Contenido principal -->
        <div style='background: #FFFFFF; padding: 0;'>
            
            <!-- Localizador destacado -->
            <div style='background: #EFCF4B; padding: 30px; text-align: center; border-bottom: 1px solid #E0E0E0;'>
                <h2 style='margin: 0 0 10px 0; font-size: 16px; font-weight: 600; color: #2D2D2D; text-transform: uppercase; letter-spacing: 1px;'>LOCALIZADOR CANCELADO</h2>
                <div style='font-size: 28px; font-weight: 700; color: #871727; letter-spacing: 3px; font-family: monospace; margin: 10px 0;'>" . $reserva['localizador'] . "</div>
                <p style='margin: 0; font-size: 14px; color: #2D2D2D; font-weight: 500;'>Estado: CANCELADA</p>
            </div>

            <!-- Información de la reserva cancelada -->
            <div style='padding: 40px 30px; border-bottom: 1px solid #E0E0E0;'>
                <h3 style='margin: 0 0 25px 0; font-size: 20px; font-weight: 700; color: #871727; text-align: center;'>Detalles de la Reserva Cancelada</h3>
                
                <table style='width: 100%; border-collapse: collapse; background: #FFFFFF; border: 2px solid #EFCF4B; border-radius: 8px; overflow: hidden;'>
                    <tr>
                        <td style='padding: 15px 25px; border-bottom: 1px solid #E0E0E0; font-weight: 600; color: #2D2D2D;'>Fecha del viaje:</td>
                        <td style='padding: 15px 25px; border-bottom: 1px solid #E0E0E0; text-align: right; font-weight: 700; color: #871727;'>" . $fecha_formateada . "</td>
                    </tr>
                    <tr>
                        <td style='padding: 15px 25px; border-bottom: 1px solid #E0E0E0; font-weight: 600; color: #2D2D2D;'>Hora de salida:</td>
                        <td style='padding: 15px 25px; border-bottom: 1px solid #E0E0E0; text-align: right; font-weight: 700; color: #871727; font-size: 18px;'>" . substr($reserva['hora'], 0, 5) . "</td>
                    </tr>
                    <tr>
                        <td style='padding: 15px 25px; border-bottom: 1px solid #E0E0E0; font-weight: 600; color: #2D2D2D;'>Cliente:</td>
                        <td style='padding: 15px 25px; border-bottom: 1px solid #E0E0E0; text-align: right; color: #666666;'>" . $reserva['nombre'] . " " . $reserva['apellidos'] . "</td>
                    </tr>
                    <tr>
                        <td style='padding: 15px 25px; border-bottom: 1px solid #E0E0E0; font-weight: 600; color: #2D2D2D;'>Fecha de cancelación:</td>
                        <td style='padding: 15px 25px; border-bottom: 1px solid #E0E0E0; text-align: right; color: #871727; font-weight: 600;'>" . $fecha_cancelacion . "</td>
                    </tr>
                </table>
            </div>

            <!-- Motivo de cancelación -->
            <div style='padding: 40px 30px; background: #F8F9FA; border-bottom: 1px solid #E0E0E0;'>
                <h3 style='margin: 0 0 25px 0; font-size: 20px; font-weight: 700; color: #871727; text-align: center;'>Motivo de la Cancelación</h3>
                
                <div style='background: #FFFFFF; padding: 25px; border-radius: 8px; border: 1px solid #E0E0E0; border-left: 4px solid #EFCF4B;'>
                    <p style='margin: 0; color: #2D2D2D; font-size: 16px; line-height: 1.6;'>" . $motivo . "</p>
                </div>
            </div>

            <!-- Información importante -->
            <div style='padding: 40px 30px; background: #FFFFFF;'>
                <h3 style='margin: 0 0 25px 0; font-size: 20px; font-weight: 700; color: #871727; text-align: center;'>Información Importante</h3>
                
                <div style='background: #F8F9FA; padding: 30px; border-radius: 8px; border-left: 4px solid #EFCF4B;'>
                    <ul style='margin: 0; padding-left: 25px; color: #2D2D2D; line-height: 1.8; font-size: 16px;'>
                        <li style='margin: 12px 0;'><strong style='color: #871727;'>Nueva reserva:</strong> Puedes realizar una nueva reserva cuando lo desees</li>
                        <li style='margin: 12px 0;'><strong style='color: #871727;'>Reembolso:</strong> Si pagaste online, el reembolso se procesará según nuestras condiciones</li>
                        <li style='margin: 12px 0;'><strong style='color: #871727;'>Soporte:</strong> Para cualquier consulta, contacta con nuestro servicio de atención al cliente</li>
                        <li style='margin: 12px 0;'><strong style='color: #871727;'>Disculpas:</strong> Lamentamos las molestias ocasionadas por esta cancelación</li>
                    </ul>
                </div>
                
                <!-- Mensaje final -->
                <div style='text-align: center; margin-top: 40px; padding: 30px; background: #871727; border-radius: 8px;'>
                    <p style='margin: 0; color: #FFFFFF; font-size: 20px; font-weight: 700;'>
                        ¡Esperamos verte pronto en Medina Azahara!
                    </p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div style='text-align: center; padding: 40px 30px; background: #2D2D2D; color: #FFFFFF;'>
            <div style='width: 40px; height: 2px; background: #EFCF4B; margin: 0 auto 20px;'></div>
            <p style='margin: 0 0 15px 0; font-size: 14px; opacity: 0.8; line-height: 1.6;'>
                Este es un email automático de notificación de cancelación.<br>
                Si tienes alguna duda, ponte en contacto con nosotros.
            </p>
            <p style='margin: 0; color: #EFCF4B; font-weight: 600; font-size: 16px;'>
                Gracias por tu comprensión
            </p>
        </div>

    </body>
    </html>";
    }


public static function send_admin_agency_reservation_notification($reserva_data, $admin_user)
{
    $config = self::get_email_config();

    // ✅ OBTENER EMAIL CORRECTO SEGÚN TIPO DE SERVICIO
    $superadmin_email = self::get_admin_email_by_service($reserva_data);

    $is_visita = isset($reserva_data['is_visita']) && $reserva_data['is_visita'] === true;
    $tipo_servicio = $is_visita ? 'Visita Guiada' : 'Autobús';

    $subject = "Reserva Rápida realizada por Administrador ({$tipo_servicio}) - " . $reserva_data['localizador'];

    $message = self::build_admin_agency_notification_template($reserva_data, $admin_user);

    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        'From: ' . $config['nombre_remitente'] . ' <' . $config['email_remitente'] . '>'
    );

    $sent = wp_mail($superadmin_email, $subject, $message, $headers);

    if ($sent) {
        error_log("✅ Email de notificación enviado al super_admin sobre reserva de administrador ({$tipo_servicio})");
        return array('success' => true, 'message' => 'Email enviado al super_admin');
    } else {
        error_log("❌ Error enviando email al super_admin sobre reserva de administrador ({$tipo_servicio})");
        return array('success' => false, 'message' => 'Error enviando email al super_admin');
    }
}

    /**
     * Template de email para notificar al super_admin sobre reserva hecha por administrador
     */
    private static function build_admin_agency_notification_template($reserva, $admin_user)
    {
        $fecha_formateada = date('d/m/Y', strtotime($reserva['fecha']));
        $fecha_creacion = date('d/m/Y H:i', strtotime($reserva['created_at'] ?? 'now'));

        $personas_detalle = "";
        if ($reserva['adultos'] > 0) $personas_detalle .= "Adultos: " . $reserva['adultos'] . "<br>";
        if ($reserva['residentes'] > 0) $personas_detalle .= "Residentes: " . $reserva['residentes'] . "<br>";
        if ($reserva['ninos_5_12'] > 0) $personas_detalle .= "Niños (5-12 años): " . $reserva['ninos_5_12'] . "<br>";
        if ($reserva['ninos_menores'] > 0) $personas_detalle .= "Niños menores (gratis): " . $reserva['ninos_menores'] . "<br>";

        $descuento_info = "";
        if ($reserva['descuento_total'] > 0) {
            $descuento_info = "<tr>
        <td style='padding: 15px 25px; border-bottom: 1px solid #E0E0E0; background: #FFF8DC; font-weight: 600; color: #871727;'>Descuentos aplicados:</td>
        <td style='padding: 15px 25px; border-bottom: 1px solid #E0E0E0; background: #FFF8DC; text-align: right; color: #871727; font-weight: bold; font-size: 16px;'>-" . number_format($reserva['descuento_total'], 2) . "€</td>
    </tr>";
        }

        // Determinar tipo de usuario
        $admin_role_text = '';
        switch ($admin_user['role']) {
            case 'super_admin':
                $admin_role_text = 'Super Administrador';
                break;
            case 'admin':
                $admin_role_text = 'Administrador';
                break;
            default:
                $admin_role_text = ucfirst($admin_user['role']);
        }

        return "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <title>Reserva Rápida por Administrador - Sistema Medina Azahara</title>
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        </style>
    </head>
    <body style='font-family: \"Inter\", -apple-system, BlinkMacSystemFont, sans-serif; line-height: 1.6; color: #2D2D2D; max-width: 700px; margin: 0 auto; padding: 0; background: #FAFAFA;'>
        
        <!-- Header Administrativo -->
        <div style='background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: #FFFFFF; text-align: center; padding: 50px 30px;'>
            <h1 style='margin: 0; font-size: 32px; font-weight: 700; letter-spacing: -0.5px;'>RESERVA RÁPIDA REALIZADA</h1>
            <div style='width: 60px; height: 3px; background: #EFCF4B; margin: 20px auto; border-radius: 2px;'></div>
            <p style='margin: 0; font-size: 18px; font-weight: 500; opacity: 0.95;'>Un administrador ha procesado una nueva reserva</p>
        </div>

        <!-- Contenido principal -->
        <div style='background: #FFFFFF; padding: 0;'>
            
            <!-- Información del Administrador -->
            <div style='background: #E8F5E8; padding: 30px; border-bottom: 1px solid #E0E0E0;'>
                <h2 style='margin: 0 0 15px 0; font-size: 18px; font-weight: 700; color: #28a745; text-align: center;'>INFORMACIÓN DEL ADMINISTRADOR</h2>
                
                <div style='background: #FFFFFF; padding: 25px; border-radius: 8px; border: 2px solid #28a745;'>
                    <div style='display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;'>
                        <div style='flex: 1; min-width: 200px;'>
                            <p style='margin: 8px 0; color: #2D2D2D; font-size: 16px;'><strong style='color: #28a745;'>Administrador:</strong> " . esc_html($admin_user['username']) . "</p>
                            <p style='margin: 8px 0; color: #2D2D2D; font-size: 16px;'><strong style='color: #28a745;'>Rol:</strong> " . $admin_role_text . "</p>
                        </div>
                        <div style='flex: 1; min-width: 200px; text-align: right;'>
                            <p style='margin: 8px 0; color: #2D2D2D; font-size: 16px;'><strong style='color: #28a745;'>Fecha procesamiento:</strong> " . $fecha_creacion . "</p>
                            <p style='margin: 8px 0; color: #2D2D2D; font-size: 16px;'><strong style='color: #28a745;'>Método:</strong> Reserva Rápida</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Localizador destacado -->
            <div style='background: #EFCF4B; padding: 30px; text-align: center; border-bottom: 1px solid #E0E0E0;'>
                <h2 style='margin: 0 0 10px 0; font-size: 16px; font-weight: 600; color: #2D2D2D; text-transform: uppercase; letter-spacing: 1px;'>LOCALIZADOR DE RESERVA</h2>
                <div style='font-size: 28px; font-weight: 700; color: #871727; letter-spacing: 3px; font-family: monospace; margin: 10px 0;'>" . $reserva['localizador'] . "</div>
                <p style='margin: 0; font-size: 14px; color: #2D2D2D; font-weight: 500;'>Reserva procesada por administrador</p>
            </div>

            <!-- Información de la reserva -->
            <div style='padding: 40px 30px; border-bottom: 1px solid #E0E0E0;'>
                <h3 style='margin: 0 0 25px 0; font-size: 20px; font-weight: 700; color: #871727; text-align: center;'>Información de la Reserva</h3>
                
                <table style='width: 100%; border-collapse: collapse; background: #FFFFFF; border: 2px solid #EFCF4B; border-radius: 8px; overflow: hidden;'>
                    <tr>
                        <td style='padding: 15px 25px; border-bottom: 1px solid #E0E0E0; font-weight: 600; color: #2D2D2D;'>Fecha del servicio:</td>
                        <td style='padding: 15px 25px; border-bottom: 1px solid #E0E0E0; text-align: right; font-weight: 700; color: #871727;'>" . $fecha_formateada . " a las " . substr($reserva['hora'], 0, 5) . "</td>
                    </tr>
                    <tr>
                        <td style='padding: 15px 25px; border-bottom: 1px solid #E0E0E0; font-weight: 600; color: #2D2D2D;'>Fecha de creación:</td>
                        <td style='padding: 15px 25px; border-bottom: 1px solid #E0E0E0; text-align: right; color: #666666;'>" . $fecha_creacion . "</td>
                    </tr>
                    <tr>
                        <td style='padding: 15px 25px; border-bottom: 1px solid #E0E0E0; font-weight: 600; color: #2D2D2D;'>Total personas:</td>
                        <td style='padding: 15px 25px; border-bottom: 1px solid #E0E0E0; text-align: right; font-weight: 700; color: #871727; font-size: 18px;'>" . $reserva['total_personas'] . " plazas ocupadas</td>
                    </tr>
                    <tr>
                        <td style='padding: 15px 25px; border-bottom: 1px solid #E0E0E0; font-weight: 600; color: #2D2D2D;'>Precio base:</td>
                        <td style='padding: 15px 25px; border-bottom: 1px solid #E0E0E0; text-align: right; font-weight: 600; color: #2D2D2D;'>" . number_format($reserva['precio_base'], 2) . "€</td>
                    </tr>
                    " . $descuento_info . "
                    <tr style='background: #28a745;'>
                        <td style='padding: 20px 25px; font-size: 20px; font-weight: 700; color: #FFFFFF;'>TOTAL PROCESADO:</td>
                        <td style='padding: 20px 25px; text-align: right; font-size: 24px; font-weight: 700; color: #FFFFFF;'>" . number_format($reserva['precio_final'], 2) . "€</td>
                    </tr>
                </table>
            </div>

            <!-- Datos del cliente -->
            <div style='padding: 40px 30px; background: #F8F9FA; border-bottom: 1px solid #E0E0E0;'>
                <h3 style='margin: 0 0 25px 0; font-size: 20px; font-weight: 700; color: #871727; text-align: center;'>Datos del Cliente</h3>
                
                <div style='background: #FFFFFF; padding: 25px; border-radius: 8px; border: 1px solid #E0E0E0;'>
                    <p style='margin: 8px 0; color: #2D2D2D; font-size: 16px;'><strong style='color: #871727;'>Nombre completo:</strong> " . $reserva['nombre'] . " " . $reserva['apellidos'] . "</p>
                    <p style='margin: 8px 0; color: #2D2D2D; font-size: 16px;'><strong style='color: #871727;'>Email:</strong> <a href='mailto:" . $reserva['email'] . "' style='color: #871727; text-decoration: none;'>" . $reserva['email'] . "</a></p>
                    <p style='margin: 8px 0; color: #2D2D2D; font-size: 16px;'><strong style='color: #871727;'>Teléfono:</strong> <a href='tel:" . $reserva['telefono'] . "' style='color: #871727; text-decoration: none;'>" . $reserva['telefono'] . "</a></p>
                </div>
            </div>

            <!-- Distribución de personas -->
            <div style='padding: 40px 30px; border-bottom: 1px solid #E0E0E0;'>
                <h3 style='margin: 0 0 25px 0; font-size: 20px; font-weight: 700; color: #871727; text-align: center;'>Distribución de Viajeros</h3>
                
                <div style='background: #F8F9FA; padding: 25px; border-radius: 8px; border: 1px solid #E0E0E0;'>
                    <div style='font-size: 16px; color: #2D2D2D; line-height: 1.8;'>
                        " . $personas_detalle . "
                    </div>
                    <div style='margin-top: 20px; padding-top: 20px; border-top: 2px solid #EFCF4B; text-align: center;'>
                        <p style='margin: 0; font-weight: 700; color: #871727; font-size: 18px;'>Total personas con plaza: " . $reserva['total_personas'] . "</p>
                    </div>
                </div>
            </div>

            <!-- Información importante -->
            <div style='padding: 40px 30px; background: #FFFFFF;'>
                <h3 style='margin: 0 0 25px 0; font-size: 20px; font-weight: 700; color: #28a745; text-align: center;'>Información Importante</h3>
                
                <div style='background: #E8F5E8; padding: 30px; border-radius: 8px; border-left: 4px solid #28a745;'>
                    <ul style='margin: 0; padding-left: 25px; color: #2D2D2D; line-height: 1.8; font-size: 16px;'>
                        <li style='margin: 12px 0;'><strong style='color: #28a745;'>Reserva procesada por:</strong> " . esc_html($admin_user['username']) . " (" . $admin_role_text . ")</li>
                        <li style='margin: 12px 0;'><strong style='color: #28a745;'>Estado de la reserva:</strong> Confirmada automáticamente</li>
                        <li style='margin: 12px 0;'><strong style='color: #28a745;'>Email enviado al cliente:</strong> Sí, con billete PDF adjunto</li>
                        <li style='margin: 12px 0;'><strong style='color: #28a745;'>Plazas actualizadas:</strong> Automáticamente descontadas del servicio</li>
                        <li style='margin: 12px 0;'><strong style='color: #28a745;'>Gestión desde panel:</strong> Disponible en la sección de Informes y Reservas</li>
                    </ul>
                </div>
                
                <!-- Acciones disponibles -->
                <div style='background: #F8F9FA; padding: 25px; border-radius: 8px; margin-top: 20px; border: 1px solid #E0E0E0;'>
                    <h4 style='margin: 0 0 15px 0; color: #28a745; font-size: 16px;'>Acciones Disponibles:</h4>
                    <ul style='margin: 0; padding-left: 20px; color: #2D2D2D; line-height: 1.6;'>
                        <li>Buscar la reserva por localizador: <strong>" . $reserva['localizador'] . "</strong></li>
                        <li>Reenviar email de confirmación al cliente si es necesario</li>
                        <li>Cancelar la reserva desde el panel de administración</li>
                        <li>Ver estadísticas y reportes del administrador</li>
                    </ul>
                </div>
                
                <!-- Mensaje final -->
                <div style='text-align: center; margin-top: 40px; padding: 30px; background: #28a745; border-radius: 8px;'>
                    <p style='margin: 0; color: #FFFFFF; font-size: 20px; font-weight: 700;'>
                        Reserva procesada exitosamente
                    </p>
                    <p style='margin: 10px 0 0 0; color: #FFFFFF; font-size: 16px; opacity: 0.9;'>
                        El cliente ha recibido su confirmación por email
                    </p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div style='text-align: center; padding: 40px 30px; background: #2D2D2D; color: #FFFFFF;'>
            <div style='width: 40px; height: 2px; background: #28a745; margin: 0 auto 20px;'></div>
            <p style='margin: 0 0 15px 0; font-size: 14px; opacity: 0.8; line-height: 1.6;'>
                Esta es una notificación automática de reserva rápida procesada por administrador.<br>
                Puedes gestionar esta reserva desde el panel de administración.
            </p>
            <p style='margin: 0; color: #28a745; font-weight: 600; font-size: 16px;'>
                Sistema de Reservas - Medina Azahara
            </p>
        </div>

    </body>
    </html>";
    }


 public static function send_agency_reservation_notification($reserva_data, $agency_user)
{
    $config = self::get_email_config();

    // ✅ OBTENER EMAIL CORRECTO SEGÚN TIPO DE SERVICIO
    $superadmin_email = self::get_admin_email_by_service($reserva_data);

    $is_visita = isset($reserva_data['is_visita']) && $reserva_data['is_visita'] === true;
    $tipo_servicio = $is_visita ? 'Visita Guiada' : 'Autobús';

    $subject = "Reserva Rápida realizada por Agencia ({$tipo_servicio}) - " . $reserva_data['localizador'];

    $message = self::build_agency_reservation_notification_template($reserva_data, $agency_user);

    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        'From: ' . $config['nombre_remitente'] . ' <' . $config['email_remitente'] . '>'
    );

    $sent = wp_mail($superadmin_email, $subject, $message, $headers);

    if ($sent) {
        error_log("✅ Email enviado al super_admin sobre reserva de agencia ({$tipo_servicio})");
        return array('success' => true, 'message' => 'Email enviado al super_admin');
    } else {
        error_log("❌ Error enviando email al super_admin sobre reserva de agencia ({$tipo_servicio})");
        return array('success' => false, 'message' => 'Error enviando email al super_admin');
    }
}

    /**
     * Enviar email a la propia agencia sobre su reserva
     */
    public static function send_agency_self_notification($reserva_data, $agency_user)
    {
        $config = self::get_email_config();

        // ✅ OBTENER EMAIL DE NOTIFICACIONES DE LA AGENCIA
        $agency_email = null;

        if (is_array($agency_user)) {
            $agency_email = !empty($agency_user['email_notificaciones']) ?
                $agency_user['email_notificaciones'] :
                $agency_user['email'];
        } else {
            // Si es objeto
            $agency_email = !empty($agency_user->email_notificaciones) ?
                $agency_user->email_notificaciones :
                $agency_user->email;
        }

        if (empty($agency_email)) {
            error_log("❌ No hay email configurado para la agencia");
            return array('success' => false, 'message' => 'Email de agencia no configurado');
        }

        error_log("📧 Enviando email a agencia: " . $agency_email);

        $agency_name = is_array($agency_user) ? $agency_user['agency_name'] : $agency_user->agency_name;
        $subject = "Confirmación de Reserva Rápida - " . $reserva_data['localizador'] . " - " . $agency_name;

        $message = self::build_agency_self_notification_template($reserva_data, $agency_user);

        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . $config['nombre_remitente'] . ' <' . $config['email_remitente'] . '>'
        );

        $sent = wp_mail($agency_email, $subject, $message, $headers);

        if ($sent) {
            error_log("✅ Email enviado a la agencia: " . $agency_email);
            return array('success' => true, 'message' => 'Email enviado a la agencia');
        } else {
            error_log("❌ Error enviando email a la agencia: " . $agency_email);
            return array('success' => false, 'message' => 'Error enviando email a la agencia');
        }
    }

    /**
     * Template para notificar al super_admin sobre reserva de agencia
     */
    private static function build_agency_reservation_notification_template($reserva, $agency_user)
    {
        $fecha_formateada = date('d/m/Y', strtotime($reserva['fecha']));
        $fecha_creacion = date('d/m/Y H:i', strtotime($reserva['created_at'] ?? 'now'));

        $personas_detalle = "";
        if ($reserva['adultos'] > 0) $personas_detalle .= "Adultos: " . $reserva['adultos'] . "<br>";
        if ($reserva['residentes'] > 0) $personas_detalle .= "Residentes: " . $reserva['residentes'] . "<br>";
        if ($reserva['ninos_5_12'] > 0) $personas_detalle .= "Niños (5-12 años): " . $reserva['ninos_5_12'] . "<br>";
        if ($reserva['ninos_menores'] > 0) $personas_detalle .= "Niños menores (gratis): " . $reserva['ninos_menores'] . "<br>";

        return "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <title>Reserva de Agencia - Sistema Medina Azahara</title>
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        </style>
    </head>
    <body style='font-family: \"Inter\", -apple-system, BlinkMacSystemFont, sans-serif; line-height: 1.6; color: #2D2D2D; max-width: 700px; margin: 0 auto; padding: 0; background: #FAFAFA;'>
        
        <!-- Header -->
        <div style='background: linear-gradient(135deg, #0073aa 0%, #005177 100%); color: #FFFFFF; text-align: center; padding: 50px 30px;'>
            <h1 style='margin: 0; font-size: 32px; font-weight: 700; letter-spacing: -0.5px;'>RESERVA RÁPIDA DE AGENCIA</h1>
            <div style='width: 60px; height: 3px; background: #EFCF4B; margin: 20px auto; border-radius: 2px;'></div>
            <p style='margin: 0; font-size: 18px; font-weight: 500; opacity: 0.95;'>Una agencia ha procesado una nueva reserva</p>
        </div>

        <!-- Información de la Agencia -->
        <div style='background: #E8F4F8; padding: 30px; border-bottom: 1px solid #E0E0E0;'>
            <h2 style='margin: 0 0 15px 0; font-size: 18px; font-weight: 700; color: #0073aa; text-align: center;'>🏢 INFORMACIÓN DE LA AGENCIA</h2>
            
            <div style='background: #FFFFFF; padding: 25px; border-radius: 8px; border: 2px solid #0073aa;'>
                <div style='display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;'>
                    <div style='flex: 1; min-width: 200px;'>
                        <p style='margin: 8px 0; color: #2D2D2D; font-size: 16px;'><strong style='color: #0073aa;'>Agencia:</strong> " . esc_html($agency_user['agency_name']) . "</p>
                        <p style='margin: 8px 0; color: #2D2D2D; font-size: 16px;'><strong style='color: #0073aa;'>Usuario:</strong> " . esc_html($agency_user['username']) . "</p>
                    </div>
                    <div style='flex: 1; min-width: 200px; text-align: right;'>
                        <p style='margin: 8px 0; color: #2D2D2D; font-size: 16px;'><strong style='color: #0073aa;'>Email:</strong> " . esc_html($agency_user['email']) . "</p>
                        <p style='margin: 8px 0; color: #2D2D2D; font-size: 16px;'><strong style='color: #0073aa;'>Comisión:</strong> " . number_format($agency_user['commission_percentage'], 1) . "%</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Localizador destacado -->
        <div style='background: #EFCF4B; padding: 30px; text-align: center; border-bottom: 1px solid #E0E0E0;'>
            <h2 style='margin: 0 0 10px 0; font-size: 16px; font-weight: 600; color: #2D2D2D; text-transform: uppercase; letter-spacing: 1px;'>LOCALIZADOR DE RESERVA</h2>
            <div style='font-size: 28px; font-weight: 700; color: #871727; letter-spacing: 3px; font-family: monospace; margin: 10px 0;'>" . $reserva['localizador'] . "</div>
            <p style='margin: 0; font-size: 14px; color: #2D2D2D; font-weight: 500;'>Reserva procesada por agencia</p>
        </div>

        <!-- Información de la reserva -->
        <div style='padding: 40px 30px; border-bottom: 1px solid #E0E0E0;'>
            <h3 style='margin: 0 0 25px 0; font-size: 20px; font-weight: 700; color: #871727; text-align: center;'>Información de la Reserva</h3>
            
            <table style='width: 100%; border-collapse: collapse; background: #FFFFFF; border: 2px solid #EFCF4B; border-radius: 8px; overflow: hidden;'>
                <tr>
                    <td style='padding: 15px 25px; border-bottom: 1px solid #E0E0E0; font-weight: 600; color: #2D2D2D;'>Fecha del servicio:</td>
                    <td style='padding: 15px 25px; border-bottom: 1px solid #E0E0E0; text-align: right; font-weight: 700; color: #871727;'>" . $fecha_formateada . " a las " . substr($reserva['hora'], 0, 5) . "</td>
                </tr>
                <tr>
                    <td style='padding: 15px 25px; border-bottom: 1px solid #E0E0E0; font-weight: 600; color: #2D2D2D;'>Total personas:</td>
                    <td style='padding: 15px 25px; border-bottom: 1px solid #E0E0E0; text-align: right; font-weight: 700; color: #871727; font-size: 18px;'>" . $reserva['total_personas'] . " plazas ocupadas</td>
                </tr>
                <tr style='background: #0073aa;'>
                    <td style='padding: 20px 25px; font-size: 20px; font-weight: 700; color: #FFFFFF;'>TOTAL PROCESADO:</td>
                    <td style='padding: 20px 25px; text-align: right; font-size: 24px; font-weight: 700; color: #FFFFFF;'>" . number_format($reserva['precio_final'], 2) . "€</td>
                </tr>
            </table>
        </div>

        <!-- Datos del cliente -->
        <div style='padding: 40px 30px; background: #F8F9FA; border-bottom: 1px solid #E0E0E0;'>
            <h3 style='margin: 0 0 25px 0; font-size: 20px; font-weight: 700; color: #871727; text-align: center;'>Datos del Cliente</h3>
            
            <div style='background: #FFFFFF; padding: 25px; border-radius: 8px; border: 1px solid #E0E0E0;'>
                <p style='margin: 8px 0; color: #2D2D2D; font-size: 16px;'><strong style='color: #871727;'>Cliente:</strong> " . $reserva['nombre'] . " " . $reserva['apellidos'] . "</p>
                <p style='margin: 8px 0; color: #2D2D2D; font-size: 16px;'><strong style='color: #871727;'>Email:</strong> " . $reserva['email'] . "</p>
                <p style='margin: 8px 0; color: #2D2D2D; font-size: 16px;'><strong style='color: #871727;'>Teléfono:</strong> " . $reserva['telefono'] . "</p>
            </div>
        </div>

        <!-- Información importante -->
        <div style='padding: 40px 30px; background: #FFFFFF;'>
            <div style='background: #E8F4F8; padding: 30px; border-radius: 8px; border-left: 4px solid #0073aa;'>
                <ul style='margin: 0; padding-left: 25px; color: #2D2D2D; line-height: 1.8; font-size: 16px;'>
                    <li style='margin: 12px 0;'><strong style='color: #0073aa;'>Reserva procesada por:</strong> " . esc_html($agency_user['agency_name']) . "</li>
                    <li style='margin: 12px 0;'><strong style='color: #0073aa;'>Estado:</strong> Confirmada automáticamente</li>
                    <li style='margin: 12px 0;'><strong style='color: #0073aa;'>Emails enviados:</strong> Cliente y agencia notificados</li>
                    <li style='margin: 12px 0;'><strong style='color: #0073aa;'>Comisión agencia:</strong> " . number_format($agency_user['commission_percentage'], 1) . "%</li>
                </ul>
            </div>
        </div>

        <!-- Footer -->
        <div style='text-align: center; padding: 40px 30px; background: #2D2D2D; color: #FFFFFF;'>
            <p style='margin: 0; color: #0073aa; font-weight: 600; font-size: 16px;'>
                Sistema de Reservas - Medina Azahara
            </p>
        </div>
    </body>
    </html>";
    }

    /**
     * Template para notificar a la agencia sobre su propia reserva
     */
    private static function build_agency_self_notification_template($reserva, $agency_user)
{
    $fecha_formateada = date('d/m/Y', strtotime($reserva['fecha']));
    $fecha_creacion = date('d/m/Y H:i', strtotime($reserva['created_at'] ?? 'now'));

    // ✅ PREPARAR DETALLES DE PERSONAS SIN PRECIOS
    $personas_detalle = "";
    if ($reserva['adultos'] > 0) $personas_detalle .= "Adultos: " . $reserva['adultos'] . "<br>";
    if ($reserva['residentes'] > 0) $personas_detalle .= "Residentes: " . $reserva['residentes'] . "<br>";
    if ($reserva['ninos_5_12'] > 0) $personas_detalle .= "Niños (5-12 años): " . $reserva['ninos_5_12'] . "<br>";
    if ($reserva['ninos_menores'] > 0) $personas_detalle .= "Niños menores (gratis): " . $reserva['ninos_menores'] . "<br>";

    return "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <title>Confirmación Reserva Rápida - " . esc_html($agency_user['agency_name']) . "</title>
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        </style>
    </head>
    <body style='font-family: \"Inter\", -apple-system, BlinkMacSystemFont, sans-serif; line-height: 1.6; color: #2D2D2D; max-width: 600px; margin: 0 auto; padding: 0; background: #FAFAFA;'>
        
        <!-- Header -->
        <div style='background: linear-gradient(135deg, #0073aa 0%, #005177 100%); color: #FFFFFF; text-align: center; padding: 50px 30px;'>
            <h1 style='margin: 0; font-size: 32px; font-weight: 700; letter-spacing: -0.5px;'>RESERVA PROCESADA</h1>
            <div style='width: 60px; height: 3px; background: #EFCF4B; margin: 20px auto; border-radius: 2px;'></div>
            <p style='margin: 0; font-size: 18px; font-weight: 500; opacity: 0.95;'>" . esc_html($agency_user['agency_name']) . "</p>
        </div>

        <!-- Localizador -->
        <div style='background: #EFCF4B; padding: 30px; text-align: center; border-bottom: 1px solid #E0E0E0;'>
            <h2 style='margin: 0 0 10px 0; font-size: 16px; font-weight: 600; color: #2D2D2D; text-transform: uppercase; letter-spacing: 1px;'>LOCALIZADOR</h2>
            <div style='font-size: 28px; font-weight: 700; color: #871727; letter-spacing: 3px; font-family: monospace; margin: 10px 0;'>" . $reserva['localizador'] . "</div>
            <p style='margin: 0; font-size: 14px; color: #2D2D2D; font-weight: 500;'>Reserva confirmada</p>
        </div>

        <!-- Resumen SIN PRECIOS -->
        <div style='padding: 40px 30px; background: #FFFFFF;'>
            <h3 style='margin: 0 0 25px 0; font-size: 20px; font-weight: 700; color: #0073aa; text-align: center;'>Resumen de la Operación</h3>
            
            <table style='width: 100%; border-collapse: collapse; background: #FFFFFF; border: 2px solid #0073aa; border-radius: 8px; overflow: hidden;'>
                <tr>
                    <td style='padding: 15px 25px; border-bottom: 1px solid #E0E0E0; font-weight: 600; color: #2D2D2D;'>Cliente:</td>
                    <td style='padding: 15px 25px; border-bottom: 1px solid #E0E0E0; text-align: right; color: #666666;'>" . $reserva['nombre'] . " " . $reserva['apellidos'] . "</td>
                </tr>
                <tr>
                    <td style='padding: 15px 25px; border-bottom: 1px solid #E0E0E0; font-weight: 600; color: #2D2D2D;'>Fecha servicio:</td>
                    <td style='padding: 15px 25px; border-bottom: 1px solid #E0E0E0; text-align: right; font-weight: 700; color: #0073aa;'>" . $fecha_formateada . " - " . substr($reserva['hora'], 0, 5) . "</td>
                </tr>
                <tr>
                    <td style='padding: 15px 25px; border-bottom: 1px solid #E0E0E0; font-weight: 600; color: #2D2D2D;'>Hora de vuelta:</td>
                    <td style='padding: 15px 25px; border-bottom: 1px solid #E0E0E0; text-align: right; font-weight: 700; color: #0073aa;'>" . substr($reserva['hora_vuelta'] ?? '', 0, 5) . "</td>
                </tr>
                <tr>
                    <td style='padding: 15px 25px; font-weight: 600; color: #2D2D2D;'>Total personas:</td>
                    <td style='padding: 15px 25px; text-align: right; font-weight: 700; color: #0073aa;'>" . $reserva['total_personas'] . "</td>
                </tr>
            </table>

            <!-- ✅ DISTRIBUCIÓN DE VIAJEROS SIN PRECIOS -->
            <div style='margin-top: 30px; padding: 25px; background: #F8F9FA; border-radius: 8px; border: 1px solid #E0E0E0;'>
                <h4 style='margin: 0 0 15px 0; color: #0073aa; font-size: 16px;'>Distribución de Viajeros:</h4>
                <div style='font-size: 16px; color: #2D2D2D; line-height: 1.8;'>
                    " . $personas_detalle . "
                </div>
                <div style='margin-top: 15px; padding-top: 15px; border-top: 2px solid #0073aa; text-align: center;'>
                    <p style='margin: 0; font-weight: 700; color: #0073aa; font-size: 18px;'>Total plazas ocupadas: " . $reserva['total_personas'] . "</p>
                </div>
            </div>

            <div style='background: #E8F4F8; padding: 25px; border-radius: 8px; margin-top: 25px; border-left: 4px solid #0073aa;'>
                <h4 style='margin: 0 0 15px 0; color: #0073aa;'>✅ Acciones Completadas:</h4>
                <ul style='margin: 0; padding-left: 20px; color: #2D2D2D;'>
                    <li>Reserva confirmada automáticamente</li>
                    <li>Email enviado al cliente con billete PDF</li>
                    <li>Plazas actualizadas en el sistema</li>
                    <li>Administración notificada</li>
                </ul>
            </div>
        </div>

        <!-- Footer -->
        <div style='text-align: center; padding: 40px 30px; background: #2D2D2D; color: #FFFFFF;'>
            <p style='margin: 0; color: #0073aa; font-weight: 600; font-size: 16px;'>
                Gracias por usar nuestro sistema
            </p>
        </div>
    </body>
    </html>";
}
}
