<?php
if (function_exists('acf_add_local_field_group')) :

    acf_add_local_field_group(array(
        'key' => 'group_629509fb8843fg',
        'title' => 'Texto e imagen2',
        'fields' => array(
            array(
                'key' => 'field_62b0bd418ewfc',
                'label' => 'Titulo',
                'name' => 'titulo_texto_e_imagen2',
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
                'key' => 'field_62b0bd4183fe4c',
                'label' => 'Subtitulo',
                'name' => 'subtitulo_texto_e_imagen2',
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
                'key' => 'field_62950af4e3cw',
                'label' => 'Texto',
                'name' => 'texto_bloque2',
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
            array(
                'key' => 'field_62950e34fdcwsdc',
                'label' => 'Imagen',
                'name' => 'imagen_bloque2',
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
                'key' => 'field_imagen_superior',
                'label' => 'Imagen Superior',
                'name' => 'imagen_superior',
                'aria-label' => '',
                'type' => 'image',
                'instructions' => 'Imagen que aparecerá encima del div con background',
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
                'key' => 'field_62950a432dsdxc',
                'label' => 'Invertir',
                'name' => 'invertir2',
                'aria-label' => '',
                'type' => 'true_false',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'message' => '',
                'default_value' => 0,
                'ui' => 0,
                'ui_on_text' => '',
                'ui_off_text' => '',
            ),
            array(
                'key' => 'field_629625123dsdx',
                'label' => 'Enlace',
                'name' => 'enlace_texto_imagen2',
                'aria-label' => '',
                'type' => 'link',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'return_format' => 'array',
            ),
            array(
                'key' => 'texto_fondo2',
                'label' => 'fondo',
                'name' => 'fondo2',
                'type' => 'color_picker',
                'required' => 0,
                'conditional_logic' => 0,
                'library' => 'all',
                'layout' => 'block',
            ),
            array(
                'key' => 'field_color_titulo',
                'label' => 'Color del texto del título',
                'name' => 'color_titulo',
                'type' => 'color_picker',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/texto-y-foto2',
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
    'name'        => 'Texto y foto2',
    'title'        => __('Texto y foto2', 'tictac'),
    'description'    => __('Texto y foto2', 'tictac'),
    'render_callback'  => 'texto_foto2',
    'mode'        => 'preview',
    'icon'        => 'image-filter',
    'keywords'      => ['texto', 'bloque', 'foto2'],
]);

function texto_foto2($block)
{
    $texto_bloque = get_field("texto_bloque2");
    $imagen_bloque = get_field("imagen_bloque2");
    $imagen_superior = get_field("imagen_superior");
    $invertir = get_field("invertir2");
    $titulo_texto_e_imagen = get_field("titulo_texto_e_imagen2");
    $subtitulo_texto_e_imagen = get_field("subtitulo_texto_e_imagen2");
    $enlace_texto_imagen = get_field("enlace_texto_imagen2");
    $fondo = get_field("fondo2");
    $color_titulo = get_field("color_titulo");
    
    if ($invertir) {
        $invertir = "inv";
    } else {
        $invertir = "";
    }
?>
    <link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/functions/blocks/texto_imagen2/texto_imagen2.min.css">
    
    <?php if ($imagen_superior) : ?>
    <div class="imagen-superior2">
        <img src="<?php echo esc_url($imagen_superior['url']); ?>" alt="<?php echo esc_attr($imagen_superior['alt']); ?>" />
    </div>
    <?php endif; ?>
    
    <div class="<?php if (isset($block['className'])) {
                    echo $block['className'];
                } ?> texto_imagen2 <?= $invertir; ?> <?php if ($fondo) {
                                                        echo "white";
                                                    } ?>" style="<?php if ($fondo) {
                                                                    echo "background-color:" . $fondo;
                                                                } ?>">
        <div class="container">
            <div class="imagen">
                <?php if ($imagen_bloque) : ?>
                    <img class="" src="<?php echo esc_url($imagen_bloque['url']); ?>" alt="<?php echo esc_attr($imagen_bloque['alt']); ?>" />
                <?php endif; ?>
            </div>
            <div class="texto">
                <?php if ($titulo_texto_e_imagen) { ?>
                    <div class="titulo">
                        <h2 style="<?php if ($color_titulo) { echo 'color:' . $color_titulo . ';'; } ?>">
                            <?= $titulo_texto_e_imagen; ?>
                        </h2>
                    </div>
                <?php } ?>
                <?php if ($subtitulo_texto_e_imagen) { ?>
                    <div class="subtitulo">
                        <?= $subtitulo_texto_e_imagen; ?>
                    </div>
                    <div class="bg-2"></div>
                <?php } ?>
                <div class="contenido-texto">
                    <?= $texto_bloque; ?>
                </div>
            </div>
            
            <?php if ($enlace_texto_imagen) { ?>
                <div class="enlace-container">
                    <a class="btn custom black" href="<?= $enlace_texto_imagen['url']; ?>"><?= $enlace_texto_imagen['title']; ?></a>
                </div>
            <?php } ?>
        </div>
    </div>
<?php
}