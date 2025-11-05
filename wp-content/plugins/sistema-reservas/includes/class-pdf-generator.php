<?php

/**
 * Clase para generar PDFs de billetes - VERSIÓN ARREGLADA
 * Archivo: wp-content/plugins/sistema-reservas/includes/class-pdf-generator.php
 */

require_once(ABSPATH . 'wp-admin/includes/file.php');

class ReservasPDFGenerator
{
    private $reserva_data;
    private $tcpdf_loaded = false;

    public function __construct()
    {
        // ✅ CARGAR TCPDF CON MÚLTIPLES FALLBACKS
        $this->load_tcpdf_with_fallbacks();
    }

    /**
     * ✅ FUNCIÓN MEJORADA PARA CARGAR TCPDF CON FALLBACKS
     */
    private function load_tcpdf_with_fallbacks()
    {
        error_log('=== CARGANDO TCPDF CON FALLBACKS ===');

        // ✅ Verificar si ya está disponible
        if (class_exists('TCPDF')) {
            error_log('✅ TCPDF ya estaba disponible');
            $this->tcpdf_loaded = true;
            return;
        }

        // ✅ FALLBACK 1: Intentar cargar desde vendor/autoload.php
        $autoload_path = RESERVAS_PLUGIN_PATH . 'vendor/autoload.php';
        if (file_exists($autoload_path)) {
            error_log("Intentando cargar desde autoload: $autoload_path");
            try {
                require_once($autoload_path);
                if (class_exists('TCPDF')) {
                    error_log('✅ TCPDF cargado desde autoload');
                    $this->tcpdf_loaded = true;
                    return;
                }
            } catch (Exception $e) {
                error_log("❌ Error cargando autoload: " . $e->getMessage());
            }
        }

        // ✅ FALLBACK 2: Buscar TCPDF directamente
        $possible_tcpdf_paths = array(
            RESERVAS_PLUGIN_PATH . 'vendor/tecnickcom/tcpdf/tcpdf.php',
            RESERVAS_PLUGIN_PATH . 'vendor/tcpdf/tcpdf.php',
            RESERVAS_PLUGIN_PATH . 'includes/tcpdf/tcpdf.php',
            ABSPATH . 'wp-content/plugins/tcpdf/tcpdf.php'
        );

        foreach ($possible_tcpdf_paths as $path) {
            if (file_exists($path)) {
                error_log("Encontrado TCPDF en: $path");
                try {
                    require_once($path);
                    if (class_exists('TCPDF')) {
                        error_log('✅ TCPDF cargado desde: ' . $path);
                        $this->tcpdf_loaded = true;
                        return;
                    }
                } catch (Exception $e) {
                    error_log("❌ Error cargando desde $path: " . $e->getMessage());
                }
            }
        }

        // ✅ FALLBACK 3: Usar TCPDF de WordPress si está disponible
        if (function_exists('wp_upload_dir')) {
            $wp_tcpdf_paths = array(
                ABSPATH . 'wp-includes/class-pdf.php',
                ABSPATH . 'wp-content/mu-plugins/tcpdf/tcpdf.php'
            );

            foreach ($wp_tcpdf_paths as $path) {
                if (file_exists($path)) {
                    try {
                        require_once($path);
                        if (class_exists('TCPDF')) {
                            error_log('✅ TCPDF cargado desde WordPress: ' . $path);
                            $this->tcpdf_loaded = true;
                            return;
                        }
                    } catch (Exception $e) {
                        error_log("❌ Error cargando TCPDF de WordPress: " . $e->getMessage());
                    }
                }
            }
        }

        error_log('❌ TCPDF no se pudo cargar desde ninguna ubicación');
        $this->tcpdf_loaded = false;
    }

    /**
     * Generar PDF del billete con manejo de errores mejorado
     */
    public function generate_ticket_pdf($reserva_data)
    {
        $this->reserva_data = $reserva_data;

        error_log('=== INICIANDO GENERACIÓN DE PDF ===');
        error_log('Localizador: ' . $reserva_data['localizador']);

        // ✅ VERIFICAR QUE TCPDF ESTÁ DISPONIBLE
        if (!$this->tcpdf_loaded || !class_exists('TCPDF')) {
            error_log('❌ TCPDF no está disponible');
            throw new Exception('TCPDF no está disponible. No se puede generar el PDF.');
        }

        // Al inicio del método generate_ticket_pdf, después de recibir $reserva_data
        $hide_prices = isset($reserva_data['hide_prices']) && $reserva_data['hide_prices'] === true;
        $is_agency_pdf = isset($reserva_data['is_agency_pdf']) && $reserva_data['is_agency_pdf'] === true;

        try {
            // ✅ CREAR DIRECTORIO TEMPORAL SEGURO
            $temp_dir = $this->create_secure_temp_dir();
            if (!$temp_dir) {
                throw new Exception('No se pudo crear directorio temporal para PDF');
            }

            // Crear instancia de TCPDF con configuración específica
            $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

            // ✅ CONFIGURACIÓN MEJORADA DEL DOCUMENTO
            $pdf->SetCreator('Sistema de Reservas - Autocares Bravo');
            $pdf->SetAuthor('Autocares Bravo Palacios');
            $pdf->SetTitle('Billete - ' . $reserva_data['localizador']);
            $pdf->SetSubject('Billete de reserva Medina Azahara');
            $pdf->SetKeywords('billete, reserva, medina azahara, autobus');

            // Configurar márgenes
            $pdf->SetMargins(10, 10, 10);
            $pdf->SetAutoPageBreak(false, 10);

            // ✅ DESHABILITAR HEADER Y FOOTER AUTOMÁTICOS
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);

            // Añadir página
            $pdf->AddPage();

            // Generar contenido del billete
            $this->generate_ticket_content($pdf);

            // ✅ NOMBRE DE ARCHIVO ÚNICO Y SEGURO
            $timestamp = date('YmdHis');
            $random = substr(md5(uniqid()), 0, 8);
            $filename = "billete_{$reserva_data['localizador']}_{$timestamp}_{$random}.pdf";
            $temp_path = $temp_dir . '/' . $filename;

            error_log("Generando PDF en: $temp_path");

            // ✅ GENERAR PDF CON MANEJO DE ERRORES
            $pdf->Output($temp_path, 'F');

            // ✅ VERIFICACIONES DE SEGURIDAD
            if (!file_exists($temp_path)) {
                throw new Exception('El archivo PDF no se creó correctamente');
            }

            $file_size = filesize($temp_path);
            if ($file_size === false || $file_size < 1000) { // PDF debe tener al menos 1KB
                throw new Exception('PDF generado está vacío o corrupto (tamaño: ' . $file_size . ' bytes)');
            }

            error_log("✅ PDF generado exitosamente: $temp_path (Tamaño: $file_size bytes)");

            return $temp_path;
        } catch (Exception $e) {
            error_log('❌ Error en generate_ticket_pdf: ' . $e->getMessage());
            error_log('❌ Stack trace: ' . $e->getTraceAsString());
            throw new Exception('Error generando PDF: ' . $e->getMessage());
        }
    }

    /**
     * ✅ CREAR DIRECTORIO TEMPORAL SEGURO
     */
    private function create_secure_temp_dir()
    {
        // Usar directorio de uploads de WordPress
        $upload_dir = wp_upload_dir();
        $base_temp_dir = $upload_dir['basedir'] . '/reservas-temp';

        // Crear directorio si no existe
        if (!file_exists($base_temp_dir)) {
            if (!wp_mkdir_p($base_temp_dir)) {
                error_log('❌ No se pudo crear directorio base: ' . $base_temp_dir);
                return false;
            }
        }

        // Crear subdirectorio con fecha actual
        $today_dir = $base_temp_dir . '/' . date('Y-m-d');
        if (!file_exists($today_dir)) {
            if (!wp_mkdir_p($today_dir)) {
                error_log('❌ No se pudo crear directorio del día: ' . $today_dir);
                return false;
            }
        }

        // Verificar que es escribible
        if (!is_writable($today_dir)) {
            error_log('❌ Directorio no es escribible: ' . $today_dir);
            return false;
        }

        return $today_dir;
    }

    /**
     * Generar el contenido del billete (sin cambios)
     */
    private function generate_ticket_content($pdf)
    {
        // Configurar fuente por defecto
        $pdf->SetFont('helvetica', '', 9);

        $hide_prices = isset($this->reserva_data['hide_prices']) && $this->reserva_data['hide_prices'] === true;

        // ✅ DETECTAR SI ES VISITA GUIADA
        $is_visita = isset($this->reserva_data['is_visita']) && $this->reserva_data['is_visita'] === true;

        // ========== SECCIÓN PRINCIPAL DEL BILLETE ==========
        $this->generate_main_ticket_section($pdf, $hide_prices);

        // ========== SECCIÓN DEL TALÓN (DESPRENDIBLE) ==========
        $this->generate_stub_section($pdf, $hide_prices);

        // ========== CONDICIONES DE COMPRA ==========
        $this->generate_conditions_section($pdf, $is_visita);

        // ✅ CÓDIGO DE BARRAS
        $this->generate_simple_barcode($pdf, 270, $hide_prices);
    }

    /**
     * Sección principal del billete (sin cambios)
     */
    private function generate_main_ticket_section($pdf, $hide_prices = false)
    {
        $y_start = 15;

        // ✅ DETECTAR SI ES VISITA GUIADA
        $is_visita = isset($this->reserva_data['is_visita']) && $this->reserva_data['is_visita'] === true;

        // ✅ TABLA DE PRODUCTOS Y PRECIOS (SOLO SI NO ES AGENCIA)
        if (!$hide_prices) {
            $pdf->SetFont('helvetica', 'B', 9);
            $pdf->SetXY(15, $y_start);

            // Headers de la tabla
            $pdf->Cell(15, 6, 'Unidades', 1, 0, 'C', false);
            $pdf->Cell(80, 6, 'Plazas:', 1, 0, 'L', false);
            $pdf->Cell(25, 6, 'Precio:', 1, 0, 'C', false);
            $pdf->Cell(25, 6, 'Total:', 1, 1, 'C', false);

            $pdf->SetFont('helvetica', '', 9);

            // Línea de adultos
            if ($this->reserva_data['adultos'] > 0) {
                $precio_adulto = $this->get_precio_adulto();
                $total_adultos = $this->reserva_data['adultos'] * $precio_adulto;

                $pdf->SetX(15);
                $pdf->Cell(15, 5, $this->reserva_data['adultos'], 1, 0, 'C');
                $pdf->Cell(80, 5, 'Adultos', 1, 0, 'L');
                $pdf->Cell(25, 5, number_format($precio_adulto, 2) . ' €', 1, 0, 'C');
                $pdf->Cell(25, 5, number_format($total_adultos, 2) . ' €', 1, 1, 'C');
            }

            // Línea de residentes
            if ($this->reserva_data['residentes'] > 0) {
                $precio_residente = $this->get_precio_residente();
                $total_residentes = $this->reserva_data['residentes'] * $precio_residente;

                $pdf->SetX(15);
                $pdf->Cell(15, 5, $this->reserva_data['residentes'], 1, 0, 'C');
                $pdf->Cell(80, 5, 'Residentes', 1, 0, 'L');
                $pdf->Cell(25, 5, number_format($precio_residente, 2) . ' €', 1, 0, 'C');
                $pdf->Cell(25, 5, number_format($total_residentes, 2) . ' €', 1, 1, 'C');
            }

            // Línea de niños (5-12)
            if ($this->reserva_data['ninos_5_12'] > 0) {
                $precio_nino = $this->get_precio_nino();
                $total_ninos = $this->reserva_data['ninos_5_12'] * $precio_nino;

                $pdf->SetX(15);
                $pdf->Cell(15, 5, $this->reserva_data['ninos_5_12'], 1, 0, 'C');
                $pdf->Cell(80, 5, 'Niños (5 a 12 años) (5-12 a.)', 1, 0, 'L');
                $pdf->Cell(25, 5, number_format($precio_nino, 2) . ' €', 1, 0, 'C');
                $pdf->Cell(25, 5, number_format($total_ninos, 2) . ' €', 1, 1, 'C');
            }

            // Línea de niños menores (gratis) - si existen
            if (isset($this->reserva_data['ninos_menores']) && $this->reserva_data['ninos_menores'] > 0) {
                $pdf->SetX(15);
                $pdf->Cell(15, 5, $this->reserva_data['ninos_menores'], 1, 0, 'C');
                $pdf->Cell(80, 5, 'Niños (menores 5 años)', 1, 0, 'L');
                $pdf->Cell(25, 5, '0,00 €', 1, 0, 'C');
                $pdf->Cell(25, 5, '0,00 €', 1, 1, 'C');
            }

            // FILA DEL TOTAL
            $pdf->SetFont('helvetica', 'B', 11);
            $pdf->SetX(95);
            $pdf->Cell(50, 8, number_format($this->reserva_data['precio_final'], 2) . ' €', 1, 1, 'C');

            $y_current = $pdf->GetY() + 5;
        } else {
            // ✅ PARA AGENCIAS: SOLO MOSTRAR DISTRIBUCIÓN SIN PRECIOS
            $pdf->SetFont('helvetica', 'B', 12);
            $pdf->SetXY(15, $y_start);
            $pdf->Cell(0, 6, 'DISTRIBUCIÓN DE VIAJEROS', 0, 1, 'C');

            $y_current = $pdf->GetY() + 5;

            $pdf->SetFont('helvetica', 'B', 9);
            $pdf->SetXY(15, $y_current);
            $pdf->Cell(70, 6, 'Tipo de Viajero', 1, 0, 'C', false);
            $pdf->Cell(30, 6, 'Cantidad', 1, 1, 'C', false);

            $pdf->SetFont('helvetica', '', 9);

            if ($this->reserva_data['adultos'] > 0) {
                $pdf->SetX(15);
                $pdf->Cell(70, 5, 'Adultos', 1, 0, 'L');
                $pdf->Cell(30, 5, $this->reserva_data['adultos'], 1, 1, 'C');
            }

            if ($this->reserva_data['residentes'] > 0) {
                $pdf->SetX(15);
                $pdf->Cell(70, 5, 'Residentes', 1, 0, 'L');
                $pdf->Cell(30, 5, $this->reserva_data['residentes'], 1, 1, 'C');
            }

            if ($this->reserva_data['ninos_5_12'] > 0) {
                $pdf->SetX(15);
                $pdf->Cell(70, 5, 'Niños (5-12 años)', 1, 0, 'L');
                $pdf->Cell(30, 5, $this->reserva_data['ninos_5_12'], 1, 1, 'C');
            }

            if (isset($this->reserva_data['ninos_menores']) && $this->reserva_data['ninos_menores'] > 0) {
                $pdf->SetX(15);
                $pdf->Cell(70, 5, 'Niños menores 5 años', 1, 0, 'L');
                $pdf->Cell(30, 5, $this->reserva_data['ninos_menores'], 1, 1, 'C');
            }

            $y_current = $pdf->GetY() + 5;
        }

        // ✅ TÍTULO DEL PRODUCTO - CAMBIAR SEGÚN TIPO
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->SetXY(15, $y_current);

        if ($is_visita) {
            // Para visitas guiadas
            $producto_titulo = 'Visita Guiada Medina Azahara (' . substr($this->reserva_data['hora'], 0, 5) . ' hrs)';
        } else {
            // Para autobús
            $producto_titulo = 'TAQ BUS Madinat Al-Zahra + Lanzadera (' . substr($this->reserva_data['hora'], 0, 5) . ' hrs)';
        }

        $pdf->Cell(0, 6, $producto_titulo, 0, 1, 'L');

        $y_current = $pdf->GetY() + 3;

        // INFORMACIÓN DEL SERVICIO EN DOS COLUMNAS
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetXY(15, $y_current);
        $pdf->Cell(30, 5, 'Fecha Visita:', 0, 0, 'L');
        $pdf->SetFont('helvetica', '', 9);
        $pdf->Cell(40, 5, $this->format_date($this->reserva_data['fecha']), 0, 0, 'L');

        // Localizador en la esquina superior derecha
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetXY(120, $y_current);
        $pdf->Cell(30, 5, 'Localizador/Localizer:', 0, 1, 'L');
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->SetX(150);
        $pdf->Cell(30, 5, $this->reserva_data['localizador'], 0, 1, 'L');

        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetX(15);
        $pdf->Cell(30, 5, 'Hora de Salida:', 0, 0, 'L');
        $pdf->SetFont('helvetica', '', 9);
        $pdf->Cell(40, 5, substr($this->reserva_data['hora'], 0, 5) . ' hrs', 0, 1, 'L');

        // ✅ HORA DE VUELTA - SOLO PARA AUTOBÚS
        if (!$is_visita) {
            $pdf->SetFont('helvetica', 'B', 9);
            $pdf->SetX(15);
            $pdf->Cell(30, 5, 'Hora de Vuelta:', 0, 0, 'L');
            $pdf->SetFont('helvetica', '', 9);
            $pdf->Cell(40, 5, substr($this->reserva_data['hora_vuelta'] ?? '', 0, 5) . ' hrs', 0, 1, 'L');
        }

        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetX(15);
        $pdf->Cell(30, 5, 'Idioma:', 0, 0, 'L');
        $pdf->SetFont('helvetica', '', 9);

        // ✅ OBTENER IDIOMA REAL DE LOS DATOS DE RESERVA
        $idioma = isset($this->reserva_data['idioma']) ? ucfirst($this->reserva_data['idioma']) : 'Español';

        // ✅ MAPEAR VALORES DE BD A TEXTO LEGIBLE
        $idiomas_map = array(
            'espanol' => 'Español',
            'ingles' => 'Inglés',
            'frances' => 'Francés'
        );

        // Aplicar mapeo si existe
        if (isset($idiomas_map[strtolower($idioma)])) {
            $idioma = $idiomas_map[strtolower($idioma)];
        }

        $pdf->Cell(40, 5, $idioma, 0, 1, 'L');

        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetX(15);
        $pdf->Cell(30, 5, 'Producto:', 0, 0, 'L');
        $pdf->SetFont('helvetica', '', 9);

        // ✅ PRODUCTO - CAMBIAR SEGÚN TIPO
        if ($is_visita) {
            $pdf->MultiCell(80, 5, 'Visita Guiada Medina Azahara (' . substr($this->reserva_data['hora'], 0, 5) . ' hrs)', 0, 'L');
        } else {
            $pdf->MultiCell(80, 5, 'TAQ BUS Madinat Al-Zahra + Lanzadera (' . substr($this->reserva_data['hora'], 0, 5) . ' hrs)', 0, 'L');
        }

        $y_current = $pdf->GetY() + 3;

        // FECHA DE COMPRA
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetXY(15, $y_current);
        $pdf->Cell(30, 5, 'Fecha Compra:', 0, 0, 'L');
        $pdf->SetFont('helvetica', '', 9);
        $pdf->Cell(40, 5, $this->format_date($this->reserva_data['created_at'] ?? date('Y-m-d')), 0, 1, 'L');

        $y_current = $pdf->GetY() + 5;

        // ✅ PUNTO DE ENCUENTRO - SOLO PARA AUTOBÚS
        if (!$is_visita) {
            $pdf->SetFont('helvetica', 'B', 9);
            $pdf->SetXY(15, $y_current);
            $pdf->Cell(35, 5, 'Punto de Encuentro:', 0, 1, 'L');

            $pdf->SetFont('helvetica', '', 8);
            $pdf->SetX(15);
            $pdf->Cell(0, 4, '1-Paseo de la Victoria (glorieta Hospital Cruz Roja)', 0, 1, 'L');
            $pdf->SetX(15);
            $pdf->Cell(0, 4, '2-Paseo de la Victoria (frente Mercado Victoria)', 0, 1, 'L');

            $y_current = $pdf->GetY() + 3;
        }

        // ✅ CLIENTE/AGENTE - SOLO PARA AUTOBÚS
        if (!$is_visita) {
            $pdf->SetFont('helvetica', 'B', 9);
            $pdf->SetXY(15, $y_current);
            $pdf->Cell(25, 5, 'Cliente/Agente:', 0, 0, 'L');
            $pdf->SetFont('helvetica', '', 8);
            $pdf->Cell(0, 5, 'TAQUILLA BRAVO BUS - FRANCISCO BRAVO', 0, 1, 'L');

            $y_current = $pdf->GetY() + 3;
        }

        // ✅ ORGANIZA - CAMBIAR SEGÚN TIPO
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetXY(15, $y_current);
        $pdf->Cell(20, 5, 'Organiza:', 0, 1, 'L');

        if ($is_visita) {
            // ✅ PARA VISITAS: MOSTRAR DATOS FISCALES DE LA AGENCIA
            $agency_name = $this->reserva_data['agency_name'] ?? 'Agencia Colaboradora';
            $agency_razon_social = $this->reserva_data['agency_razon_social'] ?? '';
            $agency_cif = $this->reserva_data['agency_cif'] ?? '';
            $agency_domicilio = $this->reserva_data['agency_domicilio_fiscal'] ?? '';
            $agency_phone = $this->reserva_data['agency_phone'] ?? '';

            // Nombre de la agencia (usar razón social si está disponible, si no el nombre comercial)
            $pdf->SetFont('helvetica', 'B', 9);
            $pdf->SetX(15);
            $nombre_mostrar = !empty($agency_razon_social) ? strtoupper($agency_razon_social) : strtoupper($agency_name);
            $pdf->Cell(0, 4, $nombre_mostrar, 0, 1, 'L');

            // Información fiscal en una línea
            $pdf->SetFont('helvetica', '', 7);
            $pdf->SetX(15);

            $info_fiscal = array();
            if (!empty($agency_domicilio)) {
                $info_fiscal[] = $agency_domicilio;
            }
            if (!empty($agency_cif)) {
                $info_fiscal[] = 'CIF: ' . $agency_cif;
            }
            if (!empty($agency_phone)) {
                $info_fiscal[] = 'Teléfono: ' . $agency_phone;
            }

            if (!empty($info_fiscal)) {
                $pdf->Cell(0, 4, implode(' - ', $info_fiscal), 0, 1, 'L');
            } else {
                // Si no hay datos fiscales, mostrar al menos el nombre
                $pdf->Cell(0, 4, 'Agencia colaboradora de Medina Azahara', 0, 1, 'L');
            }
        } else {
            // PARA AUTOBÚS: MOSTRAR DATOS DE AUTOCARES BRAVO
            $pdf->SetFont('helvetica', 'B', 9);
            $pdf->SetX(15);
            $pdf->Cell(0, 4, 'AUTOCARES BRAVO PALACIOS,S.L.', 0, 1, 'L');

            $pdf->SetFont('helvetica', '', 7);
            $pdf->SetX(15);
            $pdf->Cell(0, 4, 'INGENIERO BARBUDO, S/N - CORDOBA - CIF: B14485817 - Teléfono: 957429034', 0, 1, 'L');
        }
    }

    private function generate_stub_section($pdf, $hide_prices = false)
{
    $y_start = 95;

    // ✅ DETECTAR SI ES VISITA GUIADA
    $is_visita = isset($this->reserva_data['is_visita']) && $this->reserva_data['is_visita'] === true;

    // MARCO DEL TALÓN (lado derecho)
    $pdf->Rect(125, $y_start, 70, 55);

    // LOCALIZADOR GRANDE EN EL TALÓN
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetXY(127, $y_start + 5);
    $pdf->Cell(66, 6, 'Localizador/Localizer:', 0, 1, 'C');

    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->SetX(127);
    $pdf->Cell(66, 8, $this->reserva_data['localizador'], 0, 1, 'C');

    // INFORMACIÓN DEL TALÓN
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->SetXY(127, $y_start + 20);
    $pdf->Cell(25, 4, 'Fecha Compra:', 0, 0, 'L');
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(40, 4, $this->format_date($this->reserva_data['created_at'] ?? date('Y-m-d')), 0, 1, 'L');

    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->SetX(127);
    $pdf->Cell(25, 4, 'Producto:', 0, 0, 'L');
    $pdf->SetFont('helvetica', '', 7);
    $pdf->SetX(152);

    // ✅ PRODUCTO EN TALÓN - CAMBIAR SEGÚN TIPO
    if ($is_visita) {
        $pdf->MultiCell(40, 3, 'Visita Guiada Medina Azahara (' . substr($this->reserva_data['hora'], 0, 5) . ' hrs)', 0, 'L');
    } else {
        $pdf->MultiCell(40, 3, 'TAQ BUS Madinat Al-Zahra + Lanzadera (' . substr($this->reserva_data['hora'], 0, 5) . ' / ' . substr($this->reserva_data['hora_vuelta'] ?? '', 0, 5) . ' hrs)', 0, 'L');
    }

    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->SetXY(127, $y_start + 30);
    $pdf->Cell(25, 4, 'Fecha Visita:', 0, 0, 'L');
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(40, 4, $this->format_date($this->reserva_data['fecha']), 0, 1, 'L');

    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->SetX(127);
    $pdf->Cell(25, 4, 'Hora de Salida:', 0, 0, 'L');
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(40, 4, substr($this->reserva_data['hora'], 0, 5) . ' hrs', 0, 1, 'L');

    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->SetX(127);
    $pdf->Cell(25, 4, 'Idioma:', 0, 0, 'L');
    $pdf->SetFont('helvetica', '', 8);

    // ✅ OBTENER IDIOMA REAL DE LOS DATOS DE RESERVA
    $idioma = isset($this->reserva_data['idioma']) && !empty($this->reserva_data['idioma']) 
        ? $this->reserva_data['idioma'] 
        : 'espanol';

    // ✅ MAPEAR VALORES DE BD A TEXTO LEGIBLE
    $idiomas_map = array(
        'espanol' => 'Español',
        'ingles' => 'Inglés',
        'frances' => 'Francés'
    );

    // Aplicar mapeo
    $idioma_display = isset($idiomas_map[strtolower($idioma)]) 
        ? $idiomas_map[strtolower($idioma)] 
        : ucfirst($idioma);

    error_log('🌍 PDF - Idioma en datos: ' . ($this->reserva_data['idioma'] ?? 'NULL'));
    error_log('🌍 PDF - Idioma a mostrar: ' . $idioma_display);

    $pdf->Cell(40, 4, $idioma_display, 0, 1, 'L');

    // ✅ TOTAL EN EL TALÓN - SOLO SI NO ES AGENCIA
    if (!$hide_prices) {
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->SetXY(127, $y_start + 48);
        $pdf->Cell(66, 6, 'Total: ' . number_format($this->reserva_data['precio_final'], 2) . ' €', 0, 0, 'C');
    } else {
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetXY(127, $y_start + 48);
        $pdf->Cell(66, 6, 'RESERVA CONFIRMADA', 0, 0, 'C');
    }
}

    /**
     * Sección de condiciones de compra - CORREGIDO PARA VISITAS
     */
    private function generate_conditions_section($pdf, $is_visita = false)
    {
        $y_start = 155;

        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetXY(15, $y_start);
        $pdf->Cell(0, 5, 'CONDICIONES DE COMPRA', 0, 1, 'C');

        // ✅ TEXTO ESPECÍFICO PARA VISITAS
        if ($is_visita) {
            $conditions_text = "La adquisición de la entrada supone la aceptación de las siguientes condiciones- No se admiten devoluciones ni cambios de entradas.- La Organización no garantiza la autenticidad de la entrada si ésta no ha sido adquirida en los puntos oficiales de venta.- En caso de suspensión del servicio de visita guiada, la devolución se efectuará por la Organización dentro del plazo de 15 días de la fecha de celebración.- En caso de suspensión del servicio, una vez iniciado, no habrá derecho a devolución del importe de la entrada.- La Organización no se responsabiliza de posibles demoras ajenas a su voluntad.- Es potestad de la Organización permitir la entrada al servicio una vez haya empezado.- La admisión se supedita a la disposición de la entrada en buenas condiciones.- Debe de estar en el punto de encuentro 10 minutos antes de la hora prevista de inicio de la visita.";
        } else {
            $conditions_text = "La adquisición de la entrada supone la aceptación de las siguientes condiciones- No se admiten devoluciones ni cambios de entradas.- La Organización no garantiza la autenticidad de la entrada si ésta no ha sido adquirida en los puntos oficiales de venta.- En caso de suspensión del servicio, la devolución se efectuará por la Organización dentro del plazo de 15 días de la fecha de celebración.- En caso de suspensión del servicio, una vez iniciado, no habrá derecho a devolución del importe de la entrada.- La Organización no se responsabiliza de posibles demoras ajenas a su voluntad.- Es potestad de la Organización permitir la entrada al servicio una vez haya empezado.- La admisión se supedita a la disposición de la entrada en buenas condiciones.- Debe de estar en el punto de salida 10 minutos antes de la hora prevista de partida.";
        }

        $pdf->SetFont('helvetica', '', 6);
        $pdf->SetXY(15, $y_start + 7);
        $pdf->MultiCell(180, 3, $conditions_text, 1, 'J');

        // ✅ AJUSTAR ESPACIADO PARA EVITAR SUPERPOSICIÓN
        $y_after_conditions = $pdf->GetY() + 5; // Espacio después de las condiciones

        $pdf->SetFont('helvetica', 'B', 7);
        $pdf->SetXY(15, $y_after_conditions);
        $pdf->Cell(0, 3, 'Mantenga la integridad de toda la hoja, sin cortar ninguna de las zonas impresas.', 0, 1, 'C');

        // ✅ IMAGEN ESPECÍFICA CON MÁS ESPACIO
        $this->add_bottom_image($pdf, $y_after_conditions + 5, $is_visita);
    }

    private function add_bottom_image($pdf, $y_position)
    {
        // ✅ DETECTAR SI ES UN BILLETE DE VISITA GUIADA
        $is_visita = isset($this->reserva_data['is_visita']) && $this->reserva_data['is_visita'] === true;

        if ($is_visita && !empty($this->reserva_data['agency_logo_url'])) {
            // ✅ USAR LOGO DE LA AGENCIA PARA VISITAS GUIADAS (TAMAÑO REDUCIDO)
            $image_url = $this->reserva_data['agency_logo_url'];
            error_log('📸 Usando logo de agencia para visita: ' . $image_url);
            $max_width = 80; // ✅ REDUCIDO DE 180 A 80
            $max_height = 60; // ✅ REDUCIDO PARA MANTENER PROPORCIÓN
        } else {
            // ✅ USAR IMAGEN POR DEFECTO PARA BILLETES DE BUS
            $image_url = 'https://autobusmedinaazahara.com/wp-content/uploads/2025/08/Vector-10-1.png';
            error_log('📸 Usando imagen por defecto de bus');
            $max_width = 180;
            $max_height = 60;
        }

        try {
            // ✅ DESCARGAR IMAGEN DE FORMA SEGURA
            $image_data = $this->download_image_safely($image_url);

            if ($image_data === false) {
                error_log('❌ No se pudo descargar la imagen desde: ' . $image_url);
                return;
            }

            // ✅ CREAR ARCHIVO TEMPORAL EN DIRECTORIO SEGURO
            $temp_dir = $this->create_secure_temp_dir();
            if (!$temp_dir) {
                error_log('❌ No se pudo crear directorio temporal para imagen');
                return;
            }

            $temp_image = $temp_dir . '/footer_image_' . uniqid() . '.png';

            if (file_put_contents($temp_image, $image_data) === false) {
                error_log('❌ No se pudo crear archivo temporal para la imagen');
                return;
            }

            // Obtener dimensiones de la imagen
            $image_info = @getimagesize($temp_image);
            if ($image_info === false) {
                error_log('❌ No se pudieron obtener las dimensiones de la imagen');
                @unlink($temp_image);
                return;
            }

            $original_width = $image_info[0];
            $original_height = $image_info[1];

            // ✅ CALCULAR DIMENSIONES PARA EL PDF CON LÍMITES MÁXIMOS
            $pdf_width = $max_width;
            $pdf_height = ($original_height * $pdf_width) / $original_width;

            // Si la altura excede el máximo, recalcular basándose en altura
            if ($pdf_height > $max_height) {
                $pdf_height = $max_height;
                $pdf_width = ($original_width * $pdf_height) / $original_height;
            }

            // Verificar que no se salga de la página
            $available_height = 297 - $y_position - 10;

            if ($pdf_height > $available_height) {
                $pdf_height = $available_height;
                $pdf_width = ($original_width * $pdf_height) / $original_height;
            }

            // ✅ CENTRAR LA IMAGEN HORIZONTALMENTE
            $x_position = 15 + (180 - $pdf_width) / 2;

            // Insertar imagen en el PDF
            $pdf->Image($temp_image, $x_position, $y_position, $pdf_width, $pdf_height, 'PNG');

            // Limpiar archivo temporal
            @unlink($temp_image);

            error_log('✅ Imagen añadida al PDF correctamente (ancho: ' . $pdf_width . ', alto: ' . $pdf_height . ')');
        } catch (Exception $e) {
            error_log('❌ Error añadiendo imagen al PDF: ' . $e->getMessage());

            // Limpiar archivo temporal si existe
            if (isset($temp_image) && file_exists($temp_image)) {
                @unlink($temp_image);
            }
        }
    }

    /**
     * ✅ DESCARGAR IMAGEN DE FORMA SEGURA
     */
    private function download_image_safely($url, $timeout = 10)
    {
        // Intentar con cURL primero
        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; Sistema Reservas PDF Generator)');

            $data = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($data !== false && $http_code === 200) {
                return $data;
            }
        }

        // Fallback a file_get_contents con contexto
        $context = stream_context_create([
            'http' => [
                'timeout' => $timeout,
                'user_agent' => 'Mozilla/5.0 (compatible; Sistema Reservas PDF Generator)'
            ]
        ]);

        return @file_get_contents($url, false, $context);
    }

    /**
     * Generar código de barras simple - sin cambios
     */
    private function generate_simple_barcode($pdf, $y_start, $hide_prices = false)
    {
        // ✅ USAR LOCALIZADOR NUMÉRICO + FECHA (formato YYYYMMDD)
        $fecha_formato = date('Ymd', strtotime($this->reserva_data['fecha']));
        $barcode_data = $this->reserva_data['localizador'] . $fecha_formato;

        error_log("Generando código de barras: Localizador=" . $this->reserva_data['localizador'] . " + Fecha=" . $fecha_formato . " = " . $barcode_data);

        // Posicionar código de barras
        $pdf->SetXY(15, $y_start);

        // ✅ USAR CODE 128 (mejor para combinación de números)
        $style = array(
            'border' => false,
            'hpadding' => 0,
            'vpadding' => 0,
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false,
            'text' => true,
            'font' => 'helvetica',
            'fontsize' => 8,
            'stretchtext' => 4
        );

        try {
            // Generar código de barras CODE 128
            $pdf->write1DBarcode($barcode_data, 'C128', 15, $y_start, 120, 15, 0.4, $style, 'N');
        } catch (Exception $e) {
            error_log('❌ Error generando código de barras: ' . $e->getMessage());
            // Continuar sin código de barras si falla
        }

        // ✅ TOTAL A LA DERECHA DEL CÓDIGO DE BARRAS - SOLO SI NO ES AGENCIA
        if (!$hide_prices) {
            $pdf->SetFont('helvetica', 'B', 12);
            $pdf->SetXY(150, $y_start + 5);
            $pdf->Cell(40, 6, 'Total: ' . number_format($this->reserva_data['precio_final'], 2) . ' €', 0, 1, 'R');
        }
    }

    /**
     * Métodos auxiliares - sin cambios
     */
    private function format_date($date_string)
    {
        if (empty($date_string)) {
            return date('d/m/Y');
        }

        // Si es solo fecha (Y-m-d)
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_string)) {
            $date = DateTime::createFromFormat('Y-m-d', $date_string);
            return $date ? $date->format('d/m/Y') : date('d/m/Y');
        }

        // Si es datetime completo
        try {
            $date = new DateTime($date_string);
            return $date->format('d/m/Y');
        } catch (Exception $e) {
            return date('d/m/Y');
        }
    }

    private function get_precio_adulto()
    {
        return isset($this->reserva_data['precio_adulto']) ?
            floatval($this->reserva_data['precio_adulto']) : 10.00;
    }

    private function get_precio_nino()
    {
        return isset($this->reserva_data['precio_nino']) ?
            floatval($this->reserva_data['precio_nino']) : 5.00;
    }

    private function get_precio_residente()
    {
        return isset($this->reserva_data['precio_residente']) ?
            floatval($this->reserva_data['precio_residente']) : 5.00;
    }
}
