<?php

add_action('acf/include_fields', function () {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    acf_add_local_field_group(array(
        'key' => 'group_64917a0b89338',
        'title' => 'Banner normal',
        'fields' => array(
            array(
                'key' => 'field_64917a0cd9678',
                'label' => 'Foto',
                'name' => 'foto_normal',
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
                'key' => 'field_64917a1fd9679',
                'label' => 'Titulo',
                'name' => 'titulo_normal',
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
                'key' => 'field_64917a2ad967a',
                'label' => 'Texto',
                'name' => 'texto_normal',
                'aria-label' => '',
                'type' => 'wysiwyg',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'tabs' => 'all',
                'toolbar' => 'full',
                'media_upload' => 1,
                'delay' => 0,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/banner-normal',
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



acf_register_block_type([
    'name'        => 'banner_normal',
    'title'        => __('banner normal', 'tictac'),
    'description'    => __('banner normal', 'tictac'),
    'render_callback'  => 'banner_normal',
    'mode'        => 'preview',
    'icon'        => 'star-filled',
    'keywords'      => ['bloque', 'banner'],
]);

function banner_normal_scripts()
{
    if (!is_admin()) {
        wp_enqueue_style('banner_normal', get_stylesheet_directory_uri() . '/assets/functions/blocks/banner_normal/banner_normal.min.css');
    }
}
add_action('wp_enqueue_scripts', 'banner_normal_scripts');

function banner_normal()
{
    $foto_normal = get_field("foto_normal");
    $titulo_normal = get_field("titulo_normal");
    $texto_normal = get_field("texto_normal");
?>
    <div class="banner_normal d-flex flex-wrap justify-content-center align-content-center py-4">
        <img src="<?= $foto_normal["url"]; ?>" alt="<?= $foto_normal["alt"]; ?>">
        <div class="container text-center text-white">
            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="titulo mb-3"><?= $titulo_normal; ?></div>
                    <div class="texto"><?= $texto_normal; ?></div>
                </div>
            </div>
        </div>
    </div>
<?php
}
