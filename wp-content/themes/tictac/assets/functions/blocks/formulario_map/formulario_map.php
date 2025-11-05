<?php

add_action('acf/include_fields', function () {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    acf_add_local_field_group(array(
        'key' => 'group_64e5cb3dasf3ae29',
        'title' => 'Formulario e map',
        'fields' => array(
            array(
                'key' => 'field_64e5cb3sdad17d37',
                'label' => 'Formulario',
                'name' => 'formulario_map',
                'aria-label' => '',
                'type' => 'text',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'maxlength' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
            ),
            array(
                'key' => 'field_64e5cwerbewbb7217d3a',
                'label' => 'Imagen',
                'name' => 'fondo_formulario_imagen',
                'aria-label' => '',
                'type' => 'image',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'return_format' => 'array',
                'library' => 'all',
                'min_width' => '',
                'min_height' => '',
                'min_size' => '',
                'max_width' => '',
                'max_height' => '',
                'max_size' => '',
                'mime_types' => '',
                'preview_size' => 'medium',
            ),
            array(
                'key' => 'video_form333',
                'label' => 'Texto',
                'name' => 'video_formulario',
                'type' => 'text',
                'required' => 1,
            ),
            // Hemos eliminado los campos de ACF para el mapa ya que ahora usamos el iframe directamente
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/formulario-map',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => '',
        'show_in_rest' => 0,
    ));
});

acf_register_block_type(array(
    'name' => 'formulario_map',
    'title' => __('Formulario e Map', 'tictac'),
    'description' => __('Formulario e Map', 'tictac'),
    'render_callback' => 'formulario_map',
    'mode' => 'edit',
    'icon' => 'star-filled',
    'keywords' => array('bloque', 'formulario', 'imagen', 'custom'),
));

function formulario_map_scripts()
{
    if (!is_admin()) {
        wp_enqueue_style('formulario_map', get_stylesheet_directory_uri() . '/assets/functions/blocks/formulario_map/formulario_map.min.css');
    }
}
add_action('wp_enqueue_scripts', 'formulario_map_scripts');


function formulario_map()
{
    $fondo_formulario_imagen = get_field("fondo_formulario_imagen");
    $formulario_map = get_field("formulario_map");
    $texto_formulario = get_field("video_formulario");
    // Ya no necesitamos estas variables porque usamos directamente la URL del iframe
    $map_height = 450; // Altura fija para el mapa
?>
    <div class="fila_formulario_map position-relative">
        <div class="container m-auto p-0">
            <div class="formulario_index_mid">
                <div class="fila_formulario_container">
                    <!-- Columna del formulario -->
                    <div class="formulario_columna">
                        <?php 
                        if (!empty($formulario_map)) {
                            echo do_shortcode($formulario_map);
                        }
                        ?>
                    </div>
                    
                    <!-- Columna de la imagen y horarios -->
                    <div class="tienda_columna" style="background-image: url('<?= !empty($fondo_formulario_imagen) ? esc_url($fondo_formulario_imagen["url"]) : ''; ?>');">
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.wpcf7-submit').forEach(function(button) {
        button.value = "CONTACT US";
    });
});
    </script>
    <br>

<?php
}
?>