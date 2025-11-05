<?php

if (function_exists('acf_add_local_field_group')) :

    acf_add_local_field_group(array(
        'key' => 'group_62b33d4f2e0cc',
        'title' => 'banner paginas',
        'fields' => array(
            array(
                'key' => 'field_62b33d5ff5e19',
                'label' => 'Imagen banner',
                'name' => 'imagen_banner',
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
                'preview_size' => 'medium',
                'library' => 'all',
                'min_width' => '',
                'min_height' => '',
                'min_size' => '',
                'max_width' => '',
                'max_height' => '',
                'max_size' => '',
                'mime_types' => '',
            ),
            array(
                'key' => 'field_62b33d68f5e1a',
                'label' => 'Titulo banner',
                'name' => 'titulo_banner',
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
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'maxlength' => '',
            ),
            array(
                'key' => 'field_62b33d68f5e9z',
                'label' => 'subtitulo banner',
                'name' => 'subtitulo_banner',
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
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'maxlength' => '',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/banner-paginas',
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

acf_register_block_type([
    'name'        => 'Banner paginas',
    'title'        => __('Banner paginas', 'tictac'),
    'description'    => __('Banner paginas', 'tictac'),
    'render_callback'  => 'banner_paginas',
    'mode'        => 'preview',
    'icon'        => 'image-filter',
    'keywords'      => ['bloque', 'banner', 'paginas'],
]);

function banner_scripts()
{
  if (!is_admin()) {
    wp_enqueue_style('banner', get_stylesheet_directory_uri().'/assets/functions/blocks/banner/banner.min.css');
  }
}
add_action('wp_enqueue_scripts', 'banner_scripts');

function banner_paginas($block)
{
    $titulo_pagina = get_field("titulo_banner");
    $subtitulo_banner = get_field("subtitulo_banner");
    $envio_gratuito = get_field("envio_gratuito","options");
    if (!$titulo_pagina) {
        $titulo_pagina = get_the_title();
    }
    $banner_pagina = get_field("imagen_banner");
    if (!$banner_pagina) {
        $banner_pagina['url'] = get_stylesheet_directory_uri() . "/assets/images/bp.webp";
        $banner_pagina['alt'] = $titulo_pagina;
    }
?>
    <div class="pagebg <?php if(isset($block['className'])){ echo $block['className']; } ?>">
        <img src="<?= $banner_pagina['url']; ?>" alt="<?= $banner_pagina['alt']; ?>">
        <div class="content">
        <div class="title">
            <?= $titulo_pagina; ?>
        </div>
        <?php
        if ($subtitulo_banner) {
            echo "<!-- div class='subtitulo'>".$subtitulo_banner."</div -->";
        }
        ?>
        <div class="form-banner">
            <div class="titulo"><?= __("NOSOTROS TE LLAMAMOS","tictac"); ?></div>
            <?php echo do_shortcode('[contact-form-7 id="fa997da" title="banner"]'); ?>
        </div>
        </div>
    </div>
<?php
}
