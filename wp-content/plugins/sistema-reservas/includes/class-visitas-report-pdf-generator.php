<?php

/**
 * Generador de PDF para informes de visitas guiadas
 * Archivo: wp-content/plugins/sistema-reservas/includes/class-visitas-report-pdf-generator.php
 */

require_once RESERVAS_PLUGIN_PATH . 'vendor/tecnickcom/tcpdf/tcpdf.php';

class ReservasVisitasReportPDFGenerator
{
    private $pdf;

    public function __construct()
    {
        $this->pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $this->setup_pdf();
    }

    private function setup_pdf()
    {
        $this->pdf->SetCreator('Sistema de Reservas');
        $this->pdf->SetAuthor('Autocares Bravo');
        $this->pdf->SetTitle('Listado de Visitas Guiadas');
        $this->pdf->SetMargins(10, 15, 10);
        $this->pdf->SetHeaderMargin(10);
        $this->pdf->SetFooterMargin(10);
        $this->pdf->SetAutoPageBreak(TRUE, 15);
        $this->pdf->SetFont('helvetica', '', 9);
    }

    public function generate_report_pdf($filtros)
    {
        try {
            $data = $this->get_report_data($filtros);

            if (empty($data['visitas_agrupadas'])) {
                throw new Exception('No se encontraron visitas para el período seleccionado');
            }

            $this->pdf->AddPage();
            $this->add_header($filtros);
            $this->add_visitas_content($data);
            $this->add_totales_agencias($data);

            // Guardar archivo
            $upload_dir = wp_upload_dir();
            $filename = 'informe_visitas_' . time() . '_' . wp_generate_password(8, false) . '.pdf';
            $pdf_path = $upload_dir['path'] . '/' . $filename;

            $this->pdf->Output($pdf_path, 'F');

            return $pdf_path;
        } catch (Exception $e) {
            error_log('Error en generación de PDF de visitas: ' . $e->getMessage());
            throw $e;
        }
    }

    private function get_report_data($filtros)
    {
        global $wpdb;
        $table_visitas = $wpdb->prefix . 'reservas_visitas';
        $table_agencies = $wpdb->prefix . 'reservas_agencies';

        // Construir condiciones WHERE
        $where_conditions = array();
        $query_params = array();

        // Filtro por tipo de fecha
        if ($filtros['tipo_fecha'] === 'compra') {
            $where_conditions[] = "DATE(v.created_at) BETWEEN %s AND %s";
        } else {
            $where_conditions[] = "v.fecha BETWEEN %s AND %s";
        }
        $query_params[] = $filtros['fecha_inicio'];
        $query_params[] = $filtros['fecha_fin'];

        // Filtro de estado
        switch ($filtros['estado_filtro']) {
            case 'confirmadas':
                $where_conditions[] = "v.estado = 'confirmada'";
                break;
            case 'canceladas':
                $where_conditions[] = "v.estado = 'cancelada'";
                break;
        }

        // Filtro por agencias
        if ($filtros['agency_filter'] !== 'todas' && is_numeric($filtros['agency_filter'])) {
            $where_conditions[] = "v.agency_id = %d";
            $query_params[] = intval($filtros['agency_filter']);
        }

        // ✅ FILTRO POR HORARIOS SELECCIONADOS
        if (!empty($filtros['selected_schedules'])) {
            $selected_schedules_json = $filtros['selected_schedules'];
            
            if (strpos($selected_schedules_json, '\\') !== false) {
                $selected_schedules_json = stripslashes($selected_schedules_json);
            }
            
            $selected_schedules = json_decode($selected_schedules_json, true);
            
            if (is_array($selected_schedules) && !empty($selected_schedules)) {
                $schedule_conditions = array();
                
                foreach ($selected_schedules as $schedule) {
                    if (!empty($schedule['hora'])) {
                        $hora_normalizada = date('H:i:s', strtotime($schedule['hora']));
                        $schedule_conditions[] = "(v.hora = %s)";
                        $query_params[] = $hora_normalizada;
                    }
                }
                
                if (!empty($schedule_conditions)) {
                    $horarios_where = '(' . implode(' OR ', $schedule_conditions) . ')';
                    $where_conditions[] = $horarios_where;
                }
            }
        }

        $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);

        // Query principal
        $query = "SELECT v.*, a.agency_name, a.inicial_localizador
                 FROM $table_visitas v
                 LEFT JOIN $table_agencies a ON v.agency_id = a.id
                 $where_clause
                 ORDER BY v.fecha ASC, v.hora ASC, v.agency_id ASC";

        $visitas = $wpdb->get_results($wpdb->prepare($query, ...$query_params));

        if ($wpdb->last_error) {
            throw new Exception('Error en consulta SQL: ' . $wpdb->last_error);
        }

        // Agrupar datos
        $visitas_agrupadas = $this->group_visitas($visitas);
        $totales_agencias = $this->calculate_agency_totals($visitas);

        return array(
            'visitas_agrupadas' => $visitas_agrupadas,
            'totales_agencias' => $totales_agencias,
            'filtros' => $filtros
        );
    }

    private function group_visitas($visitas)
    {
        $grouped = array();

        foreach ($visitas as $visita) {
            $fecha = $visita->fecha;
            $turno = $this->get_turno_name($visita->hora);
            $origen = $this->get_origen_name($visita);

            if (!isset($grouped[$fecha])) {
                $grouped[$fecha] = array();
            }

            if (!isset($grouped[$fecha][$turno])) {
                $grouped[$fecha][$turno] = array();
            }

            if (!isset($grouped[$fecha][$turno][$origen])) {
                $grouped[$fecha][$turno][$origen] = array(
                    'visitas' => array(),
                    'totales' => array(
                        'adultos' => 0,
                        'ninos' => 0,
                        'ninos_menores' => 0,
                        'importe' => 0
                    )
                );
            }

            $grouped[$fecha][$turno][$origen]['visitas'][] = $visita;

            $totales = &$grouped[$fecha][$turno][$origen]['totales'];
            $totales['adultos'] += $visita->adultos;
            $totales['ninos'] += $visita->ninos;
            $totales['ninos_menores'] += $visita->ninos_menores;
            $totales['importe'] += $visita->precio_total;
        }

        return $grouped;
    }

    private function get_turno_name($hora)
    {
        return "Hora: " . date('H:i', strtotime($hora));
    }

    private function get_origen_name($visita)
    {
        if ($visita->agency_id && !empty($visita->agency_name)) {
            if (!empty($visita->inicial_localizador)) {
                return $visita->agency_name . ' (' . $visita->inicial_localizador . ')';
            }
            return $visita->agency_name;
        }
        return 'Sin Agencia';
    }

    private function calculate_agency_totals($visitas)
    {
        $totales = array();

        foreach ($visitas as $visita) {
            $origen = $this->get_origen_name($visita);

            if (!isset($totales[$origen])) {
                $totales[$origen] = array(
                    'adultos' => 0,
                    'ninos' => 0,
                    'ninos_menores' => 0,
                    'importe' => 0,
                    'count' => 0
                );
            }

            $totales[$origen]['adultos'] += $visita->adultos;
            $totales[$origen]['ninos'] += $visita->ninos;
            $totales[$origen]['ninos_menores'] += $visita->ninos_menores;
            $totales[$origen]['importe'] += $visita->precio_total;
            $totales[$origen]['count'] += 1;
        }

        return $totales;
    }

    private function add_header($filtros)
    {
        $this->pdf->SetFont('helvetica', 'B', 16);
        $this->pdf->Cell(0, 10, 'Listado de Visitas Guiadas', 0, 1, 'C');
        $this->pdf->Ln(5);

        $fecha_inicio_formateada = date('d/m/Y', strtotime($filtros['fecha_inicio']));
        $fecha_fin_formateada = date('d/m/Y', strtotime($filtros['fecha_fin']));

        $this->pdf->SetFont('helvetica', '', 12);
        $this->pdf->Cell(0, 8, "Período: $fecha_inicio_formateada - $fecha_fin_formateada", 0, 1, 'C');

        $this->pdf->SetFont('helvetica', '', 10);
        $filtros_texto = array();
        $filtros_texto[] = "Tipo fecha: " . ($filtros['tipo_fecha'] === 'compra' ? 'Compra' : 'Servicio');
        $filtros_texto[] = "Estado: " . ucfirst($filtros['estado_filtro']);

        if ($filtros['agency_filter'] !== 'todas') {
            $filtros_texto[] = "Agencia: " . $filtros['agency_filter'];
        }

        // Mostrar horarios filtrados
        if (!empty($filtros['selected_schedules'])) {
            $selected_schedules = json_decode($filtros['selected_schedules'], true);
            if (is_array($selected_schedules) && !empty($selected_schedules)) {
                $horarios_texto = array();
                foreach ($selected_schedules as $schedule) {
                    if (!empty($schedule['hora'])) {
                        $horarios_texto[] = date('H:i', strtotime($schedule['hora']));
                    }
                }
                if (!empty($horarios_texto)) {
                    $filtros_texto[] = "Horarios: " . implode(', ', $horarios_texto);
                }
            }
        }

        $this->pdf->Cell(0, 6, implode(' | ', $filtros_texto), 0, 1, 'C');
        $this->pdf->Ln(8);

        $this->add_table_header();
    }

    private function add_table_header()
    {
        $this->pdf->SetFont('helvetica',
        'B', 8);
        $this->pdf->SetFillColor(220, 220, 220);

        $this->pdf->Cell(25, 8, 'Fecha', 1, 0, 'C', true);
        $this->pdf->Cell(50, 8, 'Horario', 1, 0, 'C', true);
        $this->pdf->Cell(18, 8, 'Adultos', 1, 0, 'C', true);
        $this->pdf->Cell(18, 8, 'Niños', 1, 0, 'C', true);
        $this->pdf->Cell(18, 8, 'Bebés', 1, 0, 'C', true);
        $this->pdf->Cell(25, 8, 'Importe Total', 1, 1, 'C', true);

        $this->pdf->SetFont('helvetica', '', 8);
    }

    private function add_visitas_content($data)
    {
        $visitas_agrupadas = $data['visitas_agrupadas'];

        $total_general = array(
            'adultos' => 0,
            'ninos' => 0,
            'ninos_menores' => 0,
            'importe' => 0
        );

        foreach ($visitas_agrupadas as $fecha => $turnos) {
            $fecha_formateada = date('d/m/Y', strtotime($fecha));

            // Fecha como separador
            $this->pdf->SetFont('helvetica', 'B', 10);
            $this->pdf->SetFillColor(240, 240, 240);
            $this->pdf->Cell(0, 8, $fecha_formateada, 1, 1, 'L', true);

            $total_dia = array(
                'adultos' => 0,
                'ninos' => 0,
                'ninos_menores' => 0,
                'importe' => 0
            );

            foreach ($turnos as $turno => $origenes) {
                // Turno
                $this->pdf->SetFont('helvetica', 'B', 9);
                $this->pdf->Cell(70, 6, '    ' . $turno, 0, 1, 'L');

                $total_turno = array(
                    'adultos' => 0,
                    'ninos' => 0,
                    'ninos_menores' => 0,
                    'importe' => 0
                );

                foreach ($origenes as $origen => $datos) {
                    $this->add_origen_section($origen, $datos, $total_turno);
                }

                // Total turno
                $this->add_subtotal_row("Total Turno:", $total_turno);
                $this->add_to_total($total_dia, $total_turno);
                $this->pdf->Ln(3);
            }

            // Total día
            $this->add_subtotal_row("Total del día:", $total_dia, true);
            $this->add_to_total($total_general, $total_dia);
            $this->pdf->Ln(5);
        }

        // Total general
        $this->pdf->SetFont('helvetica', 'B', 10);
        $this->add_subtotal_row("Total listado:", $total_general, true);
    }

    private function add_origen_section($origen, $datos, &$total_turno)
    {
        $this->pdf->SetFont('helvetica', '', 8);

        // Nombre del origen
        $this->pdf->Cell(75, 5, '        ' . $origen, 0, 0, 'L');

        $totales = $datos['totales'];

        $this->pdf->Cell(18, 5, number_format($totales['adultos']), 1, 0, 'C');
        $this->pdf->Cell(18, 5, number_format($totales['ninos']), 1, 0, 'C');
        $this->pdf->Cell(18, 5, number_format($totales['ninos_menores']), 1, 0, 'C');
        $this->pdf->Cell(25, 5, number_format($totales['importe'], 2) . ' €', 1, 1, 'R');

        // Mostrar localizadores
        $localizadores = array();
        foreach ($datos['visitas'] as $visita) {
            $localizadores[] = $visita->localizador;
        }

        if (!empty($localizadores)) {
            $this->pdf->SetFont('helvetica', '', 7);

            $chunks = array_chunk($localizadores, 6);
            foreach ($chunks as $chunk) {
                $this->pdf->Cell(75, 3, '            ' . implode(', ', $chunk), 0, 1, 'L');
            }

            $this->pdf->Ln(1);
        }

        $this->add_to_total($total_turno, $totales);
    }

    private function add_subtotal_row($label, $totales, $bold = false)
    {
        if ($bold) {
            $this->pdf->SetFont('helvetica', 'B', 9);
            $this->pdf->SetFillColor(230, 230, 230);
        } else {
            $this->pdf->SetFont('helvetica', 'B', 8);
            $this->pdf->SetFillColor(245, 245, 245);
        }

        $this->pdf->Cell(75, 6, $label, 1, 0, 'R', true);
        $this->pdf->Cell(18, 6, number_format($totales['adultos']), 1, 0, 'C', true);
        $this->pdf->Cell(18, 6, number_format($totales['ninos']), 1, 0, 'C', true);
        $this->pdf->Cell(18, 6, number_format($totales['ninos_menores']), 1, 0, 'C', true);
        $this->pdf->Cell(25, 6, number_format($totales['importe'], 2) . '€', 1, 1, 'R', true);
    }

    private function add_to_total(&$total_destino, $datos_origen)
    {
        foreach ($datos_origen as $key => $value) {
            if (isset($total_destino[$key])) {
                $total_destino[$key] += $value;
            }
        }
    }

    private function add_totales_agencias($data)
    {
        $this->pdf->AddPage();

        // Título
        $this->pdf->SetFont('helvetica', 'B', 14);
        $this->pdf->Cell(0, 10, 'TOTALES POR AGENCIA', 0, 1, 'L');
        $this->pdf->Ln(5);

        // Cabecera
        $this->pdf->SetFont('helvetica', 'B', 8);
        $this->pdf->SetFillColor(220, 220, 220);

        $this->pdf->Cell(50, 8, 'Origen', 1, 0, 'L', true);
        $this->pdf->Cell(20, 8, 'Adultos', 1, 0, 'C', true);
        $this->pdf->Cell(20, 8, 'Niños', 1, 0, 'C', true);
        $this->pdf->Cell(20, 8, 'Bebés', 1, 0, 'C', true);
        $this->pdf->Cell(30, 8, 'Importe Total', 1, 0, 'C', true);
        $this->pdf->Cell(20, 8, 'Visitas', 1, 1, 'C', true);

        // Datos por agencia
        $this->pdf->SetFont('helvetica', '', 8);

        foreach ($data['totales_agencias'] as $origen => $totales) {
            $origen_truncado = strlen($origen) > 30 ? substr($origen, 0, 27) . '...' : $origen;

            $this->pdf->Cell(50, 6, $origen_truncado, 1, 0, 'L');
            $this->pdf->Cell(20, 6, number_format($totales['adultos']), 1, 0, 'C');
            $this->pdf->Cell(20, 6, number_format($totales['ninos']), 1, 0, 'C');
            $this->pdf->Cell(20, 6, number_format($totales['ninos_menores']), 1, 0, 'C');
            $this->pdf->Cell(30, 6, number_format($totales['importe'], 2) . '€', 1, 0, 'R');
            $this->pdf->Cell(20, 6, number_format($totales['count']), 1, 1, 'C');
        }

        // Total general
        $this->add_grand_total($data['totales_agencias']);
    }

    private function add_grand_total($totales_agencias)
    {
        $gran_total = array(
            'adultos' => 0,
            'ninos' => 0,
            'ninos_menores' => 0,
            'importe' => 0,
            'count' => 0
        );

        foreach ($totales_agencias as $totales) {
            $gran_total['adultos'] += $totales['adultos'];
            $gran_total['ninos'] += $totales['ninos'];
            $gran_total['ninos_menores'] += $totales['ninos_menores'];
            $gran_total['importe'] += $totales['importe'];
            $gran_total['count'] += $totales['count'];
        }

        $this->pdf->Ln(3);

        $this->pdf->SetFont('helvetica', 'B', 9);
        $this->pdf->SetFillColor(200, 200, 200);

        $this->pdf->Cell(50, 8, 'TOTAL GENERAL', 1, 0, 'R', true);
        $this->pdf->Cell(20, 8, number_format($gran_total['adultos']), 1, 0, 'C', true);
        $this->pdf->Cell(20, 8, number_format($gran_total['ninos']), 1, 0, 'C', true);
        $this->pdf->Cell(20, 8, number_format($gran_total['ninos_menores']), 1, 0, 'C', true);
        $this->pdf->Cell(30, 8, number_format($gran_total['importe'], 2) . '€', 1, 0, 'R', true);
        $this->pdf->Cell(20, 8, number_format($gran_total['count']), 1, 1, 'C', true);
    }
}