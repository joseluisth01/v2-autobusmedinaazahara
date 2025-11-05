<?php
add_action('acf/include_fields', function () {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    acf_add_local_field_group(array(
        'key' => 'group_64901c23b39a8',
        'title' => 'Banner CTA',
        'fields' => array(
            array(
                'key' => 'field_64901c2426aa7',
                'label' => 'Titulo CTA',
                'name' => 'titulo_cta',
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
                'key' => 'field_64901c6126aa8',
                'label' => 'Foto CTA',
                'name' => 'foto_cta',
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
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/cta',
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


function cta_acf()
{
    acf_register_block_type([
        'name'        => 'cta',
        'title'        => __('Banner cta', 'tictac'),
        'description'    => __('Banner cta', 'tictac'),
        'render_callback'  => 'cta',
        'mode'        => 'preview',
        'icon'        => 'star-filled',
        'keywords'      => ['custom', 'cta', 'bloque', 'call to action', 'banner'],
    ]);
}

add_action('acf/init', 'cta_acf');

function cta_scripts()
{
    if (!is_admin()) {
        wp_enqueue_style('cta', get_stylesheet_directory_uri() . '/assets/functions/blocks/cta/cta.min.css');
    }
}

add_action('wp_enqueue_scripts', 'cta_scripts');

function cta()
{
    $titulo_cta = get_field("titulo_cta");
    $foto_cta = get_field("foto_cta");
?>
    <div class="banner_cta d-flex flex-wrap justify-content-center align-content-start pt-4">
        <img src="<?= $foto_cta["url"]; ?>" alt="<?= $foto_cta["alt"]; ?>">
        <div class="container text-center text-white">
            <div class="titulo"><?= $titulo_cta; ?></div>
        </div>

    </div>

<?php
}
