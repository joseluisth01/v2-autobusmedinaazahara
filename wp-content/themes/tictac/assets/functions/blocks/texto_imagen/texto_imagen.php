<?php
if (function_exists('acf_add_local_field_group')) :

    acf_add_local_field_group(array(
        'key' => 'group_629509fb8ergdf',
        'title' => 'Texto e imagen',
        'fields' => array(
            array(
                'key' => 'field_62b0bd4gvsde',
                'label' => 'Titulo',
                'name' => 'titulo_texto_e_imagen',
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
                'key' => 'field_629wefvv',
                'label' => 'Texto',
                'name' => 'texto_bloque',
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
                'key' => 'field_62950e34fdcergg',
                'label' => 'Imagen',
                'name' => 'imagen_bloque',
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
                'key' => 'field_segunda_imagen',
                'label' => 'Segunda Imagen',
                'name' => 'segunda_imagen_bloque',
                'aria-label' => '',
                'type' => 'image',
                'instructions' => 'Si se añade esta imagen, cambiará la estructura del bloque',
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
                'key' => 'field_62950a432drth',
                'label' => 'Invertir',
                'name' => 'invertir',
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
                'key' => 'field_629625123drtyh',
                'label' => 'Enlace Principal',
                'name' => 'enlace_texto_imagen',
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
                'key' => 'field_6296treg',
                'label' => 'Enlace Secundario',
                'name' => 'enlace_texto_imagen_secundario',
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
                'key' => 'field_629addt3',
                'label' => 'Enlace Adicional',
                'name' => 'enlace_texto_imagen_adicional',
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
                'key' => 'field_fondo_titulo',
                'label' => 'Color de fondo del título',
                'name' => 'fondo_titulo',
                'type' => 'color_picker',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
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
                    'value' => 'acf/texto-y-foto',
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
    'name'        => 'Texto y foto',
    'title'        => __('Texto y foto', 'tictac'),
    'description'    => __('Texto y foto', 'tictac'),
    'render_callback'  => 'texto_foto',
    'mode'        => 'preview',
    'icon'        => 'image-filter',
    'keywords'      => ['texto', 'bloque', 'foto'],
]);

function texto_foto($block)
{
    $texto_bloque = get_field("texto_bloque");
    $imagen_bloque = get_field("imagen_bloque");
    $segunda_imagen_bloque = get_field("segunda_imagen_bloque");
    $invertir = get_field("invertir");
    $titulo_texto_e_imagen = get_field("titulo_texto_e_imagen");
    $enlace_texto_imagen = get_field("enlace_texto_imagen");
    $enlace_texto_imagen_secundario = get_field("enlace_texto_imagen_secundario");
    $enlace_texto_imagen_adicional = get_field("enlace_texto_imagen_adicional");
    $fondo_titulo = get_field("fondo_titulo");
    $color_titulo = get_field("color_titulo");

    if ($invertir) {
        $invertir = "inv";
    } else {
        $invertir = "";
    }

    // Agregar clase para estructura modificada si existe segunda imagen
    $estructura_clase = $segunda_imagen_bloque ? "estructura-triple" : "";
?>
    <link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/functions/blocks/texto_imagen/texto_imagen.min.css?v=<?php echo time(); ?>">

    <!-- Estilos inline para garantizar la visualización correcta -->
    <style>
        /* Estilos para la estructura con tres columnas cuando hay segunda imagen */
        .texto_imagen.estructura-triple .container {
            display: flex;
            justify-content: space-between;
            gap: 20px;
        }

        .texto_imagen.estructura-triple .imagen,
        .texto_imagen.estructura-triple .segunda-imagen,
        .texto_imagen.estructura-triple .texto {
            width: 33%;
        }

        .texto_imagen.estructura-triple .imagen img,
        .texto_imagen.estructura-triple .segunda-imagen img {
            width: 100% !important;
            height: 400px !important;
            object-fit: cover !important;
            border-radius: 20px !important;
            box-shadow: 0px 0px 6px 0px #00000059 !important;
        }

        /* Ajustes para invertir el orden cuando se usa la clase inv */
        .texto_imagen.estructura-triple.inv .imagen {
            order: 2;
        }

        .texto_imagen.estructura-triple.inv .segunda-imagen {
            order: 3;
        }

        .texto_imagen.estructura-triple.inv .texto {
            order: 1;
        }

        @media (max-width: 1000px) {
            .texto_imagen.estructura-triple .container {
                flex-wrap: wrap;
            }

            

            .texto_imagen.estructura-triple .texto {
                width: 100% !important;
                order: 3 !important;
            }

            .texto_imagen.estructura-triple.inv .imagen {
                order: 1;
            }

            .texto_imagen.estructura-triple.inv .segunda-imagen {
                order: 2;
            }
        }

        @media (max-width: 767px) {

            .texto_imagen.estructura-triple .imagen,
            .texto_imagen.estructura-triple .segunda-imagen {
                width: 100% !important;
            }
        }
    </style>

    <div class="<?php if (isset($block['className'])) {
                    echo $block['className'];
                } ?> texto_imagen <?= $invertir; ?> <?= $estructura_clase; ?>">
        <div class="container">
            <div class="imagen">
                <?php if ($imagen_bloque) : ?>
                    <img src="<?php echo esc_url($imagen_bloque['url']); ?>" alt="<?php echo esc_attr($imagen_bloque['alt']); ?>" />
                <?php endif; ?>
            </div>

            <?php if ($segunda_imagen_bloque) : ?>
                <div class="segunda-imagen">
                    <img src="<?php echo esc_url($segunda_imagen_bloque['url']); ?>" alt="<?php echo esc_attr($segunda_imagen_bloque['alt']); ?>" />
                </div>
            <?php endif; ?>

            <div class="texto">
                <?php if ($titulo_texto_e_imagen) { ?>
                    <div class="titulo" style="<?php
                                                if ($fondo_titulo) {
                                                    echo 'background-color:' . $fondo_titulo . ';';
                                                }
                                                ?>">
                        <h3 style="<?php
                                    if ($color_titulo) {
                                        echo 'color:' . $color_titulo . ';';
                                    }
                                    ?>">
                            <?= $titulo_texto_e_imagen; ?>
                        </h3>
                    </div>
                <?php } ?>
                <div class="contenidotexto">
                <div class="contenido-texto">
                    <?= $texto_bloque; ?>
                </div>
                <div class="enlaces">
                    <?php if ($enlace_texto_imagen) { ?>
                        <div class="enlace enlace-principal">
                            <a class="btn custom black" href="<?= $enlace_texto_imagen['url']; ?>"><?= $enlace_texto_imagen['title']; ?></a>
                        </div>
                    <?php } ?>
                    <?php if ($enlace_texto_imagen_secundario) { ?>
                        <div class="enlace enlace-secundario">
                            <a class="btn custom black" href="<?= $enlace_texto_imagen_secundario['url']; ?>"><?= $enlace_texto_imagen_secundario['title']; ?></a>
                        </div>
                    <?php } ?>
                    <?php if ($enlace_texto_imagen_adicional) { ?>
                        <div class="enlace enlace-adicional">
                            <a class="btn custom black" href="<?= $enlace_texto_imagen_adicional['url']; ?>"><?= $enlace_texto_imagen_adicional['title']; ?></a>
                        </div>
                    <?php } ?>
                </div>
                </div>
            </div>
        </div>
    </div>
<?php
}
