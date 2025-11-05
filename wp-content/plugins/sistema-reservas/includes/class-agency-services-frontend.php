<?php

/**
 * Clase para gestionar el frontend de servicios de agencias
 * Archivo: wp-content/plugins/sistema-reservas/includes/class-agency-services-frontend.php
 */
class ReservasAgencyServicesFrontend
{
    public function __construct()
    {
        // Registrar shortcodes
        add_shortcode('reservas_detalles_visita', array($this, 'render_detalles_visita'));
        add_shortcode('confirmacion_reserva_visita', array($this, 'render_confirmacion_visita'));

        // Enqueue assets
        add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));

        // AJAX para procesar reserva de visita
        add_action('wp_ajax_process_visita_reservation', array($this, 'process_visita_reservation'));
        add_action('wp_ajax_nopriv_process_visita_reservation', array($this, 'process_visita_reservation'));

        // ✅ NUEVOS ENDPOINTS PARA PDF DE VISITAS
        add_action('wp_ajax_generate_visita_pdf_view', array($this, 'generate_visita_pdf_view'));
        add_action('wp_ajax_nopriv_generate_visita_pdf_view', array($this, 'generate_visita_pdf_view'));

        add_action('wp_ajax_generate_visita_pdf_download', array($this, 'generate_visita_pdf_download'));
        add_action('wp_ajax_nopriv_generate_visita_pdf_download', array($this, 'generate_visita_pdf_download'));

        add_action('wp_ajax_calculate_visita_price_secure', array($this, 'calculate_visita_price_secure'));
        add_action('wp_ajax_nopriv_calculate_visita_price_secure', array($this, 'calculate_visita_price_secure'));
    }

    public function enqueue_assets()
    {
        global $post;

        if (is_a($post, 'WP_Post') && (
            has_shortcode($post->post_content, 'reservas_detalles_visita') ||
            has_shortcode($post->post_content, 'confirmacion_reserva_visita')
        )) {
            wp_enqueue_style(
                'reservas-visita-style',
                RESERVAS_PLUGIN_URL . 'assets/css/visita-style.css',
                array(),
                '1.0.0'
            );

            wp_enqueue_script(
                'reservas-visita-script',
                RESERVAS_PLUGIN_URL . 'assets/js/visita-script.js',
                array('jquery'),
                '1.0.0',
                true
            );

            wp_enqueue_script(
                'reservas-visitas-reports-script',
                RESERVAS_PLUGIN_URL . 'assets/js/visitas-reports-script.js',
                array('jquery'),
                '1.0.0',
                true
            );

            wp_localize_script('reservas-visita-script', 'reservasVisitaAjax', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('reservas_nonce')
            ));
        }
    }

    public function render_detalles_visita()
    {
        ob_start();
?>
        <!-- Hero con imagen de portada y título -->
        <div id="service-hero" class="service-hero">
            <img id="hero-image" src="" alt="">
            <button type="button" class="back-btn container" onclick="goBackToServices()">
                <img style="width:10px !important" src="https://autobusmedinaazahara.com/wp-content/uploads/2025/07/Vector-15.svg" alt="">
                VOLVER A SERVICIOS
            </button>
            <div class="hero-overlay container">
                <h1 id="service-title" class="service-hero-title"></h1>
            </div>
        </div>

        <div class="visita-container container">
            <!-- Detalles de compra (precios dinámicos) -->
            <div class="visita-details-section">
                <h2>DETALLES DE COMPRA DE VISITA GUIADA</h2>
                <div class="containerdetalles" style="padding:30px 60px;">

                    <div class="details-info-box">
                        <div style="background-color:#DB7461; display:flex; align-items:center; justify-content: space-around; flex-wrap:wrap">
                            <div class="info-row adultos">
                                <span class="label">ADULTOS (MAYORES DE 12 AÑOS):</span>
                                <span class="price" id="precio-adulto-info">-€</span>
                            </div>
                            <div class="info-row ninos">
                                <span class="label">NIÑOS (DE 5 A 12 AÑOS):</span>
                                <span class="price" id="precio-nino-info">-€</span>
                            </div>
                            <div class="info-row menores">
                                <span class="label">NIÑOS (-5 AÑOS):</span>
                                <span class="price" id="precio-nino-menor-info">-€</span>
                            </div>
                        </div>

                        <div class="info-notes">
                            <img style="width: 30px;" src="https://dev-tictac.com/bravobravo2parte/wp-content/uploads/2025/10/Vector-20.svg" alt="">
                            <div>
                                <p>*Visita guiada de 3 horas y media aprox.</p>
                                <p>*Sistema de radioguías para grupos con más de 10 componentes</p>
                            </div>
                        </div>
                    </div>

                    <div class="details-grid-visita">
                        <!-- Columna izquierda: Fechas y Personas -->
                        <div class="details-column-left">
                            <div style="width:50%">
                                <div class="section-title">
                                    <h3>FECHAS Y HORAS</h3>
                                </div>

                                <div class="details-card">
                                    <div class="detail-row">
                                        <span class="label"><span>FECHA</span> INICIO VISITA GUIADA:</span>
                                        <span class="value" id="fecha-visita">-</span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="label">HORA INICIO VISITA GUIADA:</span>
                                        <span class="value" id="hora-inicio">-</span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="label">FECHA FIN VISITA GUIADA:</span>
                                        <span class="value" id="fecha-fin">-</span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="label">HORA FIN VISITA GUIADA:</span>
                                        <span class="value" id="hora-fin">-</span>
                                    </div>
                                </div>
                            </div>
                            <div style="width:50%">
                                <div class="section-title">
                                    <h3>ENTRADAS, PERSONAS Y PRECIO</h3>
                                </div>

                                <div class="details-card">
                                    <div class="person-selector">
                                        <label>NÚMERO DE ADULTOS (>12 AÑOS):</label>
                                        <input type="number" id="adultos-visita" min="1" max="999" value="1" class="person-input">
                                    </div>

                                    <div class="person-selector">
                                        <label>NÚMERO DE NIÑOS (5/12 AÑOS):</label>
                                        <input type="number" id="ninos-visita" min="0" max="999" value="0" class="person-input">
                                    </div>

                                    <div class="person-selector">
                                        <label>NÚMERO DE NIÑOS (-5 AÑOS):</label>
                                        <input type="number" id="ninos-menores-visita" min="0" max="999" value="0" class="person-input">
                                    </div>

                                    <!-- ✅ AÑADIR ESTE CONTENEDOR PARA EL SELECTOR DE IDIOMA -->
                                    <div id="idioma-selector-container"></div>

                                    <div class="total-price-visita">
                                        <div class="total-row">
                                            <span class="label">TOTAL COMPRA:</span>
                                            <span class="value" id="total-visita">0,00€</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Columna derecha: Datos Personales -->
                        <div class="details-column-right">
                            <div class="section-title">
                                <h3>DATOS PERSONALES</h3>
                            </div>

                            <div class="details-card">
                                <form id="visita-personal-data-form">
                                    <div class="form-group">
                                        <input type="text" name="nombre" placeholder="NOMBRE" required>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" name="apellidos" placeholder="APELLIDOS" required>
                                    </div>
                                    <div class="form-group">
                                        <input type="email" name="email" placeholder="EMAIL" required>
                                    </div>
                                    <div class="form-group">
                                        <input type="tel" name="telefono" placeholder="MÓVIL O TELÉFONO" required>
                                    </div>

                                    <div class="privacy-policy-section">
                                        <label for="privacy-policy-visita">
                                            <input type="checkbox" id="privacy-policy-visita" name="privacy-policy" required>
                                            <span>Acepto haber leído y estar conforme con la <a href="https://autobusmedinaazahara.com/politica-de-privacidad/" target="_blank">política de privacidad</a></span>
                                        </label>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="final-buttons">
                        <button type="button" class="complete-btn" onclick="processVisitaReservation()">
                            COMPLETA COMPRA
                        </button>
                    </div>
                </div>
            </div>
        </div>
    <?php
        return ob_get_clean();
    }


    public function calculate_visita_price_secure()
{
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'reservas_nonce')) {
        wp_send_json_error('Error de seguridad');
        return;
    }

    $service_id = intval($_POST['service_id'] ?? 0);
    $adultos = max(0, intval($_POST['adultos'] ?? 0));
    $ninos = max(0, intval($_POST['ninos'] ?? 0));
    $ninos_menores = max(0, intval($_POST['ninos_menores'] ?? 0));

    if ($service_id <= 0) {
        wp_send_json_error('Service ID inválido');
        return;
    }

    global $wpdb;
    $table_services = $wpdb->prefix . 'reservas_agency_services';

    $servicio = $wpdb->get_row($wpdb->prepare(
        "SELECT precio_adulto, precio_nino, precio_nino_menor 
         FROM $table_services 
         WHERE id = %d AND servicio_activo = 1",
        $service_id
    ));

    if (!$servicio) {
        wp_send_json_error('Servicio no encontrado');
        return;
    }

    $precio_adulto = floatval($servicio->precio_adulto);
    $precio_nino = floatval($servicio->precio_nino);
    $precio_nino_menor = floatval($servicio->precio_nino_menor);

    $precio_final = ($adultos * $precio_adulto) +
                   ($ninos * $precio_nino) +
                   ($ninos_menores * $precio_nino_menor);

    // ✅ GENERAR FIRMA DIGITAL
    $secret_key = 'reservas_visitas_secret_' . wp_salt('auth');
    $timestamp = time();

    $firma_data = array(
        'service_id' => $service_id,
        'adultos' => $adultos,
        'ninos' => $ninos,
        'ninos_menores' => $ninos_menores,
        'precio_final' => round($precio_final, 2),
        'timestamp' => $timestamp
    );

    $firma = hash_hmac('sha256', json_encode($firma_data), $secret_key);

    wp_send_json_success(array(
        'precio_final' => round($precio_final, 2),
        'firma' => $firma,
        'firma_data' => $firma_data,
        'timestamp' => $timestamp
    ));
}

    /**
     * Renderizar página de confirmación de reserva de visita
     */
    public function render_confirmacion_visita()
    {
        ob_start();
    ?>
        <style>
            .confirmacion-visita-container {
                max-width: 800px;
                margin: 50px auto;
                padding: 0;
            }

            .back-btn {
                color: black;
                border: none;
                font-size: 14px;
                cursor: pointer;
                text-transform: uppercase;
                display: flex;
                align-items: center;
                gap: 10px;
                background: none !important;
                margin-bottom: 20px;
                padding: 0;
                font-family: 'Duran-Regular';
                font-weight: 600;
            }

            .back-btn img {
                width: 10px;
            }

            .success-banner {
                background: #DB7461;
                color: white;
                text-align: center;
                padding: 20px;
                letter-spacing: 2px;
                border-top-left-radius: 15px;
                border-top-right-radius: 15px;
                font-family: 'Duran-Medium';
                text-transform: uppercase;
            }

            .content-section {
                background: #FFFFFF;
                padding: 50px 60px;
                text-align: center;
                border-bottom-left-radius: 15px;
                border-bottom-right-radius: 15px;
                box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.1);
            }

            .todo-listo {
                font-size: 18px;
                font-weight: bold;
                color: #333;
                margin-bottom: 25px;
                font-family: 'Duran-Regular';
            }

            .thank-you-message {
                margin-bottom: 30px;
            }

            .thank-you-message p {
                font-size: 16px;
                color: #2D2D2D;
                line-height: 1.8;
                margin: 0 0 15px 0;
                font-family: 'Duran-Regular';
            }

            .thank-you-message p strong {
                font-family: 'Duran-Medium';
            }

            .memorable-text {
                font-size: 16px;
                color: #2D2D2D;
                margin: 30px 0;
                font-family: 'Duran-Regular';
            }

            .action-buttons {
                display: flex;
                gap: 15px;
                align-items: center;
                margin-top: 30px;
                justify-content: space-between;
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

            @media (max-width: 768px) {
                .confirmacion-visita-container {
                    margin: 20px;
                }

                .content-section {
                    padding: 40px 30px;
                }

                .action-buttons {
                    flex-direction: column;
                    gap: 15px;
                }

                .complete-btn {
                    width: 100%;
                    padding: 15px 30px;
                }
            }
        </style>

        <div class="confirmacion-visita-container container">
            <button type="button" class="back-btn" onclick="goBackInicio()">
                <img src="https://autobusmedinaazahara.com/wp-content/uploads/2025/07/Vector-15.svg" alt="">
                VOLVER A INICIO
            </button>

            <div class="success-banner">
                ¡GRACIAS POR TU COMPRA!
            </div>

            <div class="content-section">
                <div class="todo-listo">
                    ¡Todo listo!
                </div>

                <div class="thank-you-message">
                    <p><strong>Gracias por confiar en Autocares BRAVO y en nuestros guías colaboradores para vivir Medina Azahara al completo.</strong> Ahora solo te queda relajarte, dejarte llevar y disfrutar de cada historia que emergerá entre sus columnas califales.</p>
                </div>

                <div class="memorable-text">
                    ¡Que tu recorrido sea tan memorable como la ciudad que vas a descubrir!
                </div>

                <div class="action-buttons">
                    <button class="complete-btn" onclick="viewVisitaTicket()">
                        VER COMPROBANTE
                    </button>
                    <button class="complete-btn" onclick="downloadVisitaTicket()">
                        DESCARGAR COMPROBANTE
                    </button>
                </div>
            </div>
        </div>

        <script>
            // Cargar datos de confirmación al cargar la página
            window.addEventListener('DOMContentLoaded', function() {
                console.log('=== PÁGINA DE CONFIRMACIÓN DE VISITA CARGADA ===');

                // Obtener localizador de la URL
                const urlParams = new URLSearchParams(window.location.search);
                const localizador = urlParams.get('localizador');

                console.log('Localizador desde URL:', localizador);

                if (localizador) {
                    // Guardar localizador globalmente para los botones
                    window.visitaLocalizador = localizador;
                    console.log('✅ Localizador guardado para usar en botones:', localizador);
                }
            });

            /**
             * Volver al inicio
             */
            function goBackInicio() {
                console.log('=== VOLVIENDO AL INICIO ===');

                // Limpiar todo el sessionStorage relacionado con la reserva
                sessionStorage.removeItem('selectedServiceData');
                sessionStorage.removeItem('reservationData');

                // ✅ CONSTRUIR URL DE INICIO CORRECTAMENTE
                const currentPath = window.location.pathname;
                let targetUrl;

                // Si estamos en un subdirectorio
                if (currentPath.includes('/')) {
                    const pathParts = currentPath.split('/').filter(part => part !== '');

                    // Si hay al menos una parte en la ruta (subdirectorio)
                    if (pathParts.length > 0) {
                        // Usar el primer segmento como base
                        targetUrl = window.location.origin + '/' + pathParts[0] + '/';
                    } else {
                        // Estamos en la raíz
                        targetUrl = window.location.origin + '/';
                    }
                } else {
                    // Estamos en la raíz
                    targetUrl = window.location.origin + '/';
                }

                console.log('🏠 Volviendo al inicio:', targetUrl);
                window.location.href = targetUrl;
            }

            function viewVisitaTicket() {
                console.log('🎫 Ver comprobante de visita - Localizador:', window.visitaLocalizador);

                if (!window.visitaLocalizador) {
                    alert('No se encontró el localizador de la visita');
                    return;
                }

                showLoadingModal('Generando comprobante...');

                jQuery.post(reservasVisitaAjax.ajax_url, {
                    action: 'generate_visita_pdf_view',
                    localizador: window.visitaLocalizador,
                    nonce: reservasVisitaAjax.nonce
                }, function(response) {
                    hideLoadingModal();

                    if (response.success && response.data.pdf_url) {
                        console.log('✅ PDF de visita generado:', response.data.pdf_url);
                        window.open(response.data.pdf_url, '_blank');
                    } else {
                        console.error('❌ Error:', response);
                        alert('Error generando el comprobante: ' + (response.data || 'Error desconocido'));
                    }
                }).fail(function(error) {
                    hideLoadingModal();
                    console.error('❌ Error AJAX:', error);
                    alert('Error de conexión');
                });
            }

            function downloadVisitaTicket() {
                console.log('⬇️ Descargar comprobante de visita - Localizador:', window.visitaLocalizador);

                if (!window.visitaLocalizador) {
                    alert('No se encontró el localizador de la visita');
                    return;
                }

                showLoadingModal('Preparando descarga...');

                jQuery.post(reservasVisitaAjax.ajax_url, {
                    action: 'generate_visita_pdf_download',
                    localizador: window.visitaLocalizador,
                    nonce: reservasVisitaAjax.nonce
                }, function(response) {
                    hideLoadingModal();

                    if (response.success && response.data.pdf_url) {
                        console.log('✅ PDF de visita listo para descarga:', response.data.pdf_url);

                        const link = document.createElement('a');
                        link.href = response.data.pdf_url;
                        link.download = `visita_${window.visitaLocalizador}.pdf`;
                        link.target = '_blank';
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                    } else {
                        console.error('❌ Error:', response);
                        alert('Error preparando la descarga: ' + (response.data || 'Error desconocido'));
                    }
                }).fail(function(error) {
                    hideLoadingModal();
                    console.error('❌ Error AJAX:', error);
                    alert('Error de conexión');
                });
            }

            function showLoadingModal(message) {
                let modal = jQuery('#loading-modal');
                if (modal.length === 0) {
                    modal = jQuery('<div id="loading-modal" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); display: flex; align-items: center; justify-content: center; z-index: 10000;"><div style="background: white; padding: 30px; border-radius: 10px; text-align: center; max-width: 300px;"><div style="font-size: 24px; margin-bottom: 15px;">⏳</div><div id="loading-message" style="font-size: 16px; color: #333;"></div></div></div>');
                    jQuery('body').append(modal);
                }
                modal.find('#loading-message').text(message);
                modal.show();
            }

            function hideLoadingModal() {
                jQuery('#loading-modal').hide();
            }
        </script>
<?php
        return ob_get_clean();
    }

    public function process_visita_reservation()
{
    header('Content-Type: application/json');

    try {
        error_log('=== INICIANDO PROCESS_VISITA_RESERVATION ===');

        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'reservas_nonce')) {
            wp_send_json_error('Error de seguridad');
            return;
        }

        $service_id = intval($_POST['service_id']);
        $agency_id = intval($_POST['agency_id']);
        $fecha = sanitize_text_field($_POST['fecha']);
        $hora = sanitize_text_field($_POST['hora']);
        $adultos = intval($_POST['adultos']);
        $ninos = intval($_POST['ninos']);
        $ninos_menores = intval($_POST['ninos_menores']);
        $nombre = sanitize_text_field($_POST['nombre']);
        $apellidos = sanitize_text_field($_POST['apellidos']);
        $email = sanitize_email($_POST['email']);
        $telefono = sanitize_text_field($_POST['telefono']);
        $idioma = sanitize_text_field($_POST['idioma'] ?? 'español');

        // ✅ CALCULAR PRECIO DIRECTAMENTE EN EL SERVIDOR
        global $wpdb;
        $table_services = $wpdb->prefix . 'reservas_agency_services';

        $servicio = $wpdb->get_row($wpdb->prepare(
            "SELECT precio_adulto, precio_nino, precio_nino_menor FROM $table_services WHERE id = %d",
            $service_id
        ));

        if (!$servicio) {
            wp_send_json_error('Servicio no encontrado');
            return;
        }

        // Calcular precio total
        $precio_total = ($adultos * floatval($servicio->precio_adulto)) +
                       ($ninos * floatval($servicio->precio_nino)) +
                       ($ninos_menores * floatval($servicio->precio_nino_menor));

        error_log("✅ Precio calculado: $precio_total €");

        // Validaciones básicas
        if ($adultos < 1) {
            wp_send_json_error('Debe haber al menos un adulto en la reserva');
            return;
        }

        if (!is_email($email)) {
            wp_send_json_error('Email no válido');
            return;
        }

        // ✅ PREPARAR DATOS PARA REDSYS
        $reservation_data = array(
            'service_id' => $service_id,
            'agency_id' => $agency_id,
            'fecha' => $fecha,
            'hora' => $hora,
            'adultos' => $adultos,
            'ninos' => $ninos,
            'ninos_menores' => $ninos_menores,
            'nombre' => $nombre,
            'apellidos' => $apellidos,
            'email' => $email,
            'telefono' => $telefono,
            'idioma' => $idioma,
            'precio_total' => $precio_total,
            'is_visita' => true
        );

        error_log('Datos de reserva preparados para Redsys: ' . print_r($reservation_data, true));

        // ✅ GENERAR FORMULARIO REDSYS
        if (!function_exists('generar_formulario_redsys')) {
            require_once RESERVAS_PLUGIN_PATH . 'includes/class-redsys-handler.php';
        }

        $formulario_html = generar_formulario_redsys($reservation_data);
        
        // ✅ LOG ADICIONAL PARA VERIFICAR
        error_log('✅ HTML generado (primeros 200 caracteres): ' . substr($formulario_html, 0, 200));

        wp_send_json_success($formulario_html);

    } catch (Exception $e) {
        error_log('ERROR procesando reserva visita: ' . $e->getMessage());
        wp_send_json_error('Error interno: ' . $e->getMessage());
    }
}


    private function generar_localizador_visita($agency_id, $inicial_agencia)
    {
        global $wpdb;
        $table_visitas = $wpdb->prefix . 'reservas_visitas';
        $table_config = $wpdb->prefix . 'reservas_configuration';

        $año_actual = date('Y');
        $config_key = "ultimo_localizador_visita_{$agency_id}_{$año_actual}";

        // Obtener el último número
        $ultimo_numero = $wpdb->get_var($wpdb->prepare(
            "SELECT config_value FROM $table_config WHERE config_key = %s",
            $config_key
        ));

        if ($ultimo_numero === null) {
            $nuevo_numero = 1;

            $wpdb->insert(
                $table_config,
                array(
                    'config_key' => $config_key,
                    'config_value' => '1',
                    'config_group' => 'localizadores_visitas',
                    'description' => "Último localizador de visita para agencia $agency_id en $año_actual"
                )
            );
        } else {
            $nuevo_numero = intval($ultimo_numero) + 1;

            $wpdb->update(
                $table_config,
                array('config_value' => $nuevo_numero),
                array('config_key' => $config_key)
            );
        }

        // ✅ FORMATO: VIS + INICIAL_AGENCIA + NÚMERO (6 dígitos)
        $localizador = 'VIS' . strtoupper($inicial_agencia) . str_pad($nuevo_numero, 6, '0', STR_PAD_LEFT);

        // Verificar que no exista
        $existe = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_visitas WHERE localizador = %s",
            $localizador
        ));

        if ($existe > 0) {
            // Recursivo si ya existe
            return $this->generar_localizador_visita($agency_id, $inicial_agencia);
        }

        error_log("✅ Localizador visita generado: $localizador");

        return $localizador;
    }


    private function enviar_email_confirmacion_visita($reserva_data)
    {
        if (!class_exists('ReservasEmailService')) {
            require_once RESERVAS_PLUGIN_PATH . 'includes/class-email-service.php';
        }

        // Enviar email al cliente CON PDF
        $customer_result = ReservasEmailService::send_customer_confirmation($reserva_data);

        if ($customer_result['success']) {
            error_log('✅ Email enviado al cliente de visita guiada: ' . $reserva_data['email']);
        } else {
            error_log('❌ Error enviando email al cliente de visita: ' . $customer_result['message']);
        }

        // Enviar email al administrador
        $admin_result = ReservasEmailService::send_admin_notification($reserva_data);

        if ($admin_result['success']) {
            error_log('✅ Email enviado al admin sobre visita guiada');
        } else {
            error_log('❌ Error enviando email al admin: ' . $admin_result['message']);
        }
    }

    /**
     * ✅ RECURSIVO PARA BUSCAR LOCALIZADOR DISPONIBLE
     */
    private function generar_localizador_visita_recursivo($agency_id, $inicial, $numero, $año)
    {
        global $wpdb;

        $table_visitas = $wpdb->prefix . 'reservas_visitas';
        $table_config = $wpdb->prefix . 'reservas_configuration';

        if ($numero > 999999) {
            throw new Exception('Se alcanzó el límite de localizadores para esta agencia este año');
        }

        $localizador = 'VIS' . strtoupper($inicial) . str_pad($numero, 6, '0', STR_PAD_LEFT);

        $existe = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_visitas WHERE localizador = %s",
            $localizador
        ));

        if ($existe > 0) {
            return $this->generar_localizador_visita_recursivo($agency_id, $inicial, $numero + 1, $año);
        }

        // Actualizar contador
        $config_key = "ultimo_localizador_visita_{$inicial}_{$año}";
        $wpdb->update(
            $table_config,
            array('config_value' => $numero),
            array('config_key' => $config_key)
        );

        return $localizador;
    }


    /**
     * ✅ GENERAR PDF PARA VER
     */
    public function generate_visita_pdf_view()
    {
        $this->handle_visita_pdf_request('view');
    }

    /**
     * ✅ GENERAR PDF PARA DESCARGAR
     */
    public function generate_visita_pdf_download()
    {
        $this->handle_visita_pdf_request('download');
    }

    /**
     * ✅ MANEJAR SOLICITUD DE PDF DE VISITA
     */
    private function handle_visita_pdf_request($mode = 'view')
    {
        error_log("=== PDF VISITA REQUEST: $mode ===");

        // Verificar nonce
        if (!wp_verify_nonce($_POST['nonce'], 'reservas_nonce')) {
            wp_send_json_error('Error de seguridad');
            return;
        }

        $localizador = sanitize_text_field($_POST['localizador'] ?? '');

        if (empty($localizador)) {
            wp_send_json_error('Localizador no proporcionado');
            return;
        }

        try {
            global $wpdb;
            $table_visitas = $wpdb->prefix . 'reservas_visitas';
            $table_services = $wpdb->prefix . 'reservas_agency_services';

            // Buscar la reserva de visita
            $reserva = $wpdb->get_row($wpdb->prepare(
                "SELECT v.*, s.logo_url, s.precio_adulto, s.precio_nino, s.precio_nino_menor, a.agency_name, a.inicial_localizador
             FROM $table_visitas v
             INNER JOIN $table_services s ON v.service_id = s.id
             INNER JOIN {$wpdb->prefix}reservas_agencies a ON v.agency_id = a.id
             WHERE v.localizador = %s",
                $localizador
            ));

            if (!$reserva) {
                wp_send_json_error('Reserva de visita no encontrada');
                return;
            }

            $reserva_data = array(
                'localizador' => $reserva->localizador,
                'fecha' => $reserva->fecha,
                'hora' => $reserva->hora,
                'hora_vuelta' => '',
                'nombre' => $reserva->nombre,
                'apellidos' => $reserva->apellidos,
                'email' => $reserva->email,
                'telefono' => $reserva->telefono,
                'adultos' => $reserva->adultos,
                'residentes' => 0,
                'ninos_5_12' => $reserva->ninos,
                'ninos_menores' => $reserva->ninos_menores,
                'total_personas' => $reserva->total_personas,
                'precio_base' => $reserva->precio_total,
                'descuento_total' => 0,
                'precio_final' => $reserva->precio_total,
                'precio_adulto' => $reserva->precio_adulto,
                'precio_nino' => $reserva->precio_nino,
                'precio_residente' => 0,
                'created_at' => $reserva->created_at,
                'metodo_pago' => $reserva->metodo_pago,
                'idioma' => $reserva->idioma ?? 'espanol', // ✅ AÑADIR ESTA LÍNEA
                'is_visita' => true,
                'agency_logo_url' => $reserva->logo_url,
                'agency_name' => $reserva->agency_name
            );

            // Generar PDF
            if (!class_exists('ReservasPDFGenerator')) {
                require_once RESERVAS_PLUGIN_PATH . 'includes/class-pdf-generator.php';
            }

            $pdf_generator = new ReservasPDFGenerator();
            $pdf_path = $pdf_generator->generate_ticket_pdf($reserva_data);

            if (!$pdf_path || !file_exists($pdf_path)) {
                wp_send_json_error('Error generando el PDF');
                return;
            }

            // Crear URL público
            $upload_dir = wp_upload_dir();
            $relative_path = str_replace($upload_dir['basedir'], '', $pdf_path);
            $pdf_url = $upload_dir['baseurl'] . $relative_path;

            // Programar eliminación
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
            error_log('❌ Error generando PDF de visita: ' . $e->getMessage());
            wp_send_json_error('Error interno: ' . $e->getMessage());
        }
    }
}
