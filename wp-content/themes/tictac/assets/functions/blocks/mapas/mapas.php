<?php
if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
        'key' => 'group_1',
        'title' => 'Mapas',
        'fields' => array(
            array(
                'key' => 'mapas_1',
                'label' => 'Repeater',
                'type' => 'repeater',
                'instructions' => 'Agrega una fila para cada item',
                'required' => 1,
                'min' => 1,
                'layout' => 'block',
                'sub_fields' => array(
                    array(
                        'key' => 'mapas_mapa',
                        'label' => 'Mapa',
                        'name' => 'mapa',
                        'type' => 'text',
                    ),
                ),
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/mapa',
                ),
            ),
        ),
    ));
}
function mapa_acf()
{
    acf_register_block_type(array(
        'name' => 'mapa',
        'title' => 'mapa',
        'description' => 'mapa',
        'category' => 'formatting',
        'icon' => 'star-filled',
        'keywords' => array('mapa', 'acf'),
        'render_callback' => 'mapa',
    ));
}
add_action('acf/init', 'mapa_acf');

function mapa()
{
    $titulo = get_field("titulo");
?>
    <link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/functions/blocks/mapas/mapas.min.css">
    <div class="mapa">
        <div class="row d-flex justify-content-center">
            <?php
            if (have_rows('mapas_1')) {
                while (have_rows('mapas_1')) {
                    the_row();
                    $mapa = get_sub_field('mapa');
            ?>
                    <div class="col-12">
                        <div class="iframe"><?php echo $mapa; ?></div>
                    </div>
            <?php
                }
            }
            ?>
        </div>
    </div>

<?php

}
