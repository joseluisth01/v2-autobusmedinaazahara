<?php
if (function_exists('acf_add_local_field_group')) :

    acf_add_local_field_group(array(
        'key' => 'grupo_repeater',
        'title' => 'Repeater iconos',
        'fields' => array(
            array(
                'key' => 'repeater_iconos',
                'label' => 'Repeater iconos',
                'name' => 'repeater_iconos',
                'type' => 'repeater',
                'layout' => 'table',
                'button_label' => 'Agregar Fila',
                'sub_fields' => array(
                    array(
                        'key' => 'icono',
                        'label' => 'Icono',
                        'name' => 'icono',
                        'type' => 'image',
                        'required' => 1,
                    ),
                    array(
                        'key' => 'titulo',
                        'label' => 'Título',
                        'name' => 'titulo',
                        'type' => 'text',
                        'required' => 1,
                    ),
                    array(
                        'key' => 'texto',
                        'label' => 'Texto',
                        'name' => 'texto',
                        'type' => 'wysiwyg',
                        'required' => 1,
                    ),
                ),
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/iconos',
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

endif;

function iconos_acf()
{
    acf_register_block_type([
        'name'        => 'Iconos',
        'title'        => __('Iconos', 'tictac'),
        'description'    => __('Iconos', 'tictac'),
        'render_callback'  => 'iconos',
        'mode'        => 'preview',
        'icon'        => 'star-filled',
        'keywords'      => ['custom', 'iconos', 'bloque'],
    ]);
}

add_action('acf/init', 'iconos_acf');

function iconos_scripts()
{
    if (!is_admin()) {
        wp_enqueue_style('iconos', get_stylesheet_directory_uri() . '/assets/functions/blocks/fila_iconos/fila_iconos.min.css');
    }
}
add_action('wp_enqueue_scripts', 'iconos_scripts');

function iconos($block)
{
?>
    <div class="<?php if(isset($block['className'])){ echo $block['className']; } ?> fila_iconos">
        <div class="container">

            <div class="row align-items-start justify-content-center justify-content-xl-between">
                <?php
                if (have_rows('repeater_iconos')) {
                    while (have_rows('repeater_iconos')) {
                        the_row();
                        $icono = get_sub_field('icono');
                        $titulo = get_sub_field('titulo');
                        $texto = get_sub_field('texto');
                ?>

                        <div class="icono-box p-3 col-12 col-md-3 col-xl-3 d-flex flex-wrap mb-3 justify-content-center align-self-stretch">
                            <div class="icono col-12 text-center d-flex justify-content-center align-items-end"><img src="<?= $icono["url"]; ?>" alt="<?= $icono["alt"]; ?>"></div>
                            <div class="subtitulo mb-2 col-12 text-center"><?= $titulo; ?></div>
                            <div class="texto col-12 text-center"><?= $texto; ?></div>
                        </div>
                <?php
                    }
                }
                ?>
            </div>
        </div>
    </div>
<?php
}
