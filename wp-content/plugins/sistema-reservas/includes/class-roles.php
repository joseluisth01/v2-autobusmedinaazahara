<?php
class ReservasRoles {
    
    public static function get_roles() {
        return array(
            'super_admin' => 'Super Administrador',
            'admin' => 'Administrador',
            'agencia' => 'Agencia',
            'conductor' => 'Conductor'
        );
    }
    
    public static function get_role_permissions($role) {
        $permissions = array(
            'super_admin' => array(
                'manage_users',
                'manage_agencies',
                'manage_services',
                'manage_reservations',
                'view_reports',
                'manage_config'
            ),
            'admin' => array(
                'manage_services',
                'manage_reservations',
                'view_reports'
            ),
            'agencia' => array(
                'create_reservations',
                'view_own_reservations',
                'cancel_reservations'
            ),
            'conductor' => array(
                'view_assigned_services',
                'verify_tickets'
            )
        );
        
        return isset($permissions[$role]) ? $permissions[$role] : array();
    }
    
    public static function user_can($permission, $user_role = null) {
        if (!$user_role) {
            $current_user = ReservasAuth::get_current_user();
            $user_role = $current_user ? $current_user['role'] : null;
        }
        
        if (!$user_role) {
            return false;
        }
        
        $user_permissions = self::get_role_permissions($user_role);
        return in_array($permission, $user_permissions);
    }
}