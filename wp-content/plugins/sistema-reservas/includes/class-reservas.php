<?php
class ReservasReservas {
    
    public function __construct() {
        // Hooks para reservas
        add_action('wp_ajax_create_reservation', array($this, 'create_reservation'));
        add_action('wp_ajax_nopriv_create_reservation', array($this, 'create_reservation'));
    }
    
    public function create_reservation() {
        // Funcionalidad de reservas se implementará después
        wp_send_json_success('Función de reservas en desarrollo');
    }
    
    public static function generate_localizador() {
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
            $nuevo_numero = self::buscar_numero_disponible_static($año_actual);
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
        // Si por alguna razón ya existe, intentar con el siguiente
        return self::generar_localizador_recursivo_static($año_actual, $nuevo_numero + 1);
    }
    
    return $localizador;
}


private static function buscar_numero_disponible_static($año)
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
    
    return false;
}

private static function generar_localizador_recursivo_static($año, $numero)
{
    global $wpdb;
    
    $table_reservas = $wpdb->prefix . 'reservas_reservas';
    $table_config = $wpdb->prefix . 'reservas_configuration';
    
    if ($numero > 100000) {
        $numero_disponible = self::buscar_numero_disponible_static($año);
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
        return self::generar_localizador_recursivo_static($año, $numero + 1);
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
    
    public static function calculate_total_price($adultos, $ninos, $bebes, $residentes, $servicio_id) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'reservas_servicios';
        $servicio = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE id = %d",
            $servicio_id
        ));
        
        if (!$servicio) {
            return 0;
        }
        
        $total = 0;
        $total += $adultos * $servicio->precio_adulto;
        $total += $ninos * $servicio->precio_nino;
        $total += $residentes * $servicio->precio_residente;
        // Los bebés no pagan
        
        return $total;
    }
}