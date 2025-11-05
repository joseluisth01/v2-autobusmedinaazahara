<?php
class ReservasCalendar {
    
    public function __construct() {
        add_action('wp_ajax_get_month_services', array($this, 'get_month_services'));
        add_action('wp_ajax_get_service_stats', array($this, 'get_service_stats'));
    }
    
    public function get_month_services() {
        if (!wp_verify_nonce($_POST['nonce'], 'reservas_nonce')) {
            wp_die('Error de seguridad');
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'reservas_servicios';
        
        $month = intval($_POST['month']);
        $year = intval($_POST['year']);
        
        $start_date = "$year-$month-01";
        $end_date = date('Y-m-t', strtotime($start_date));
        
        $servicios = $wpdb->get_results($wpdb->prepare(
            "SELECT s.*, 
                    COUNT(r.id) as reservas_count,
                    SUM(r.total_plazas) as plazas_reservadas
             FROM $table_name s
             LEFT JOIN {$wpdb->prefix}reservas_reservas r ON s.id = r.servicio_id
             WHERE s.fecha BETWEEN %s AND %s
             GROUP BY s.id
             ORDER BY s.fecha, s.hora",
            $start_date, $end_date
        ));
        
        wp_send_json_success($servicios);
    }
    
    public function get_service_stats() {
        if (!wp_verify_nonce($_POST['nonce'], 'reservas_nonce')) {
            wp_die('Error de seguridad');
        }
        
        global $wpdb;
        $service_id = intval($_POST['service_id']);
        
        $stats = $wpdb->get_row($wpdb->prepare(
            "SELECT s.*,
                    COUNT(r.id) as total_reservas,
                    SUM(r.total_plazas) as plazas_ocupadas,
                    SUM(r.adultos) as adultos_reservados,
                    SUM(r.ninos) as ninos_reservados,
                    SUM(r.bebes) as bebes_reservados,
                    SUM(r.residentes) as residentes_reservados,
                    SUM(r.precio_total) as ingresos_totales
             FROM {$wpdb->prefix}reservas_servicios s
             LEFT JOIN {$wpdb->prefix}reservas_reservas r ON s.id = r.servicio_id
             WHERE s.id = %d
             GROUP BY s.id",
            $service_id
        ));
        
        if ($stats) {
            $stats->plazas_libres = $stats->plazas_totales - ($stats->plazas_ocupadas ?: 0);
            $stats->ocupacion_porcentaje = round(($stats->plazas_ocupadas / $stats->plazas_totales) * 100, 2);
            wp_send_json_success($stats);
        } else {
            wp_send_json_error('Servicio no encontrado');
        }
    }
    
    public static function get_services_by_date_range($start_date, $end_date) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'reservas_servicios';
        
        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_name 
             WHERE fecha BETWEEN %s AND %s 
             AND status = 'active'
             ORDER BY fecha, hora",
            $start_date, $end_date
        ));
    }
    
    public static function get_available_services($fecha = null) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'reservas_servicios';
        
        $where_clause = "WHERE status = 'active' AND plazas_disponibles > 0";
        $params = array();
        
        if ($fecha) {
            $where_clause .= " AND fecha = %s";
            $params[] = $fecha;
        } else {
            $where_clause .= " AND fecha >= %s";
            $params[] = date('Y-m-d');
        }
        
        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_name 
             $where_clause
             ORDER BY fecha, hora",
            ...$params
        ));
    }
}