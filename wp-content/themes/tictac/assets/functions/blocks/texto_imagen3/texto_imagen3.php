<?php
if (function_exists('acf_add_local_field_group')) :

    acf_add_local_field_group(array(
        'key' => 'group_62950ergfe',
        'title' => 'Texto e imagen3',
        'fields' => array(
            array(
                'key' => 'field_62b0bwefds',
                'label' => 'Titulo',
                'name' => 'titulo_texto_e_imagen3',
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
                'key' => 'field_62b0bd418wefdws',
                'label' => 'Subtitulo',
                'name' => 'subtitulo_texto_e_imagen3',
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
                'key' => 'field_6295wefcsd',
                'label' => 'Texto',
                'name' => 'texto_bloque3',
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
                'key' => 'field_62950e34fwefdc',
                'label' => 'Imagen',
                'name' => 'imagen_bloque3',
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
                'key' => 'field_imagen_superior3',
                'label' => 'Imagen Superior',
                'name' => 'imagen_superior3',
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
                'key' => 'field_62950a4wwscdef',
                'label' => 'Invertir',
                'name' => 'invertir3',
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
                'key' => 'field_6296251wefw',
                'label' => 'Enlace',
                'name' => 'enlace_texto_imagen3',
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
                'key' => 'texto_fondo3',
                'label' => 'fondo',
                'name' => 'fondo3',
                'type' => 'color_picker',
                'required' => 0,
                'conditional_logic' => 0,
                'library' => 'all',
                'layout' => 'block',
            ),
            array(
                'key' => 'field_color_titulo3',
                'label' => 'Color del texto del título',
                'name' => 'color_titulo3',
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
                    'value' => 'acf/texto-y-foto3',
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
    'name'        => 'Texto y foto3',
    'title'        => __('Texto y foto3', 'tictac'),
    'description'    => __('Texto y foto3', 'tictac'),
    'render_callback'  => 'texto_foto3',
    'mode'        => 'preview',
    'icon'        => 'image-filter',
    'keywords'      => ['texto', 'bloque', 'foto3'],
]);

function texto_foto3($block)
{
    $texto_bloque = get_field("texto_bloque3");
    $imagen_bloque = get_field("imagen_bloque3");
    $imagen_superior = get_field("imagen_superior3");
    $invertir = get_field("invertir3");
    $titulo_texto_e_imagen = get_field("titulo_texto_e_imagen3");
    $subtitulo_texto_e_imagen = get_field("subtitulo_texto_e_imagen3");
    $enlace_texto_imagen = get_field("enlace_texto_imagen3");
    $fondo = get_field("fondo3");
    $color_titulo = get_field("color_titulo3");
    
    // Añadir clase si no hay imagen
    $no_imagen_clase = !$imagen_bloque ? 'sin-imagen' : '';
    
    if ($invertir) {
        $invertir = "inv";
    } else {
        $invertir = "";
    }
?>
    <link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/functions/blocks/texto_imagen3/texto_imagen3.min.css">
    
    <?php if ($imagen_superior) : ?>
    <div class="imagen-superior">
        <img src="<?php echo esc_url($imagen_superior['url']); ?>" alt="<?php echo esc_attr($imagen_superior['alt']); ?>" />
    </div>
    <?php endif; ?>
    
    <div class="<?php if (isset($block['className'])) {
                    echo $block['className'];
                } ?> texto_imagen3 <?= $invertir; ?> <?= $no_imagen_clase; ?> <?php if ($fondo) {
                                                        echo "white";
                                                    } ?>" style="<?php if ($fondo) {
                                                                    echo "background-color:" . $fondo;
                                                                } ?>">
        <div class="container">
            <?php if ($imagen_bloque) : ?>
            <div class="imagen">
                <img class="" src="<?php echo esc_url($imagen_bloque['url']); ?>" alt="<?php echo esc_attr($imagen_bloque['alt']); ?>" />
            </div>
            <?php endif; ?>
            <div class="texto <?php echo !$imagen_bloque ? 'texto-completo' : ''; ?>">
                <?php if ($titulo_texto_e_imagen) { ?>
                    <div class="titulo">
                        <h1 style="<?php if ($color_titulo) { echo 'color:' . $color_titulo . ';'; } ?>">
                            <?= $titulo_texto_e_imagen; ?>
                        </h1>
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