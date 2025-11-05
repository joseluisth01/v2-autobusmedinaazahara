<?php
class ReservasDatabase {
    
    public static function create_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // Tabla de usuarios del sistema de reservas
        $table_users = $wpdb->prefix . 'reservas_users';
        $sql_users = "CREATE TABLE $table_users (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            username varchar(50) NOT NULL,
            email varchar(100) NOT NULL,
            password varchar(255) NOT NULL,
            role enum('super_admin', 'admin', 'agencia', 'conductor') NOT NULL,
            status enum('active', 'inactive') DEFAULT 'active',
            agencia_id mediumint(9) NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY username (username),
            UNIQUE KEY email (email)
        ) $charset_collate;";
        
        // Tabla de servicios/horarios
        $table_servicios = $wpdb->prefix . 'reservas_servicios';
        $sql_servicios = "CREATE TABLE $table_servicios (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            fecha date NOT NULL,
            hora time NOT NULL,
            plazas_totales int(11) NOT NULL,
            plazas_disponibles int(11) NOT NULL,
            plazas_bloqueadas int(11) DEFAULT 0,
            precio_adulto decimal(10,2) NOT NULL,
            precio_nino decimal(10,2) NOT NULL,
            precio_residente decimal(10,2) NOT NULL,
            status enum('active', 'inactive') DEFAULT 'active',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY fecha_hora (fecha, hora)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_users);
        dbDelta($sql_servicios);
    }
    
    public static function get_config($key, $default = '') {
        return $default;
    }
    
    public static function update_config($key, $value) {
        return true;
    }
}