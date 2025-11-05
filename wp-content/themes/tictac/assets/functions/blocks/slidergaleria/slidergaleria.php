<?php
if (function_exists('acf_add_local_field_group')) :

    acf_add_local_field_group(array(
        'key' => 'group_slidergaleria',
        'title' => 'Slider Galería',
        'fields' => array(
            array(
                'key' => 'field_titulo_slidergaleria',
                'label' => 'Título',
                'name' => 'titulo_slidergaleria',
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
                'key' => 'field_imagenes_slidergaleria',
                'label' => 'Imágenes del Slider',
                'name' => 'imagenes_slidergaleria',
                'aria-label' => '',
                'type' => 'repeater',
                'instructions' => 'Añade las imágenes para el slider',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'layout' => 'block',
                'min' => 0,
                'max' => 0,
                'button_label' => 'Añadir Imagen',
                'sub_fields' => array(
                    array(
                        'key' => 'field_imagen_slidergaleria',
                        'label' => 'Imagen',
                        'name' => 'imagen',
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
            ),
            array(
                'key' => 'field_autoplay_slidergaleria',
                'label' => 'Reproducción automática',
                'name' => 'autoplay_slidergaleria',
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
                'default_value' => 1,
                'ui' => 1,
                'ui_on_text' => 'Activado',
                'ui_off_text' => 'Desactivado',
            ),
            array(
                'key' => 'field_enlace_galeria',
                'label' => 'Enlace debajo de la galería',
                'name' => 'enlace_galeria',
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
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/slidergaleria',
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
    'name'        => 'slidergaleria',
    'title'        => __('Slider Galería', 'tictac'),
    'description'    => __('Bloque de slider para galería de imágenes', 'tictac'),
    'render_callback'  => 'slidergaleria_block',
    'mode'        => 'preview',
    'icon'        => 'images-alt2',
    'keywords'      => ['slider', 'galeria', 'imagenes'],
]);

function slidergaleria_block($block)
{
    $titulo = get_field('titulo_slidergaleria');
    $imagenes = get_field('imagenes_slidergaleria');
    $autoplay = get_field('autoplay_slidergaleria');
    $enlace = get_field('enlace_galeria');

    // Generar un ID único para el slider
    $slider_id = 'slider-galeria-' . $block['id'];
    $modal_id = 'modal-galeria-' . $block['id'];

    // Clase adicional si viene del bloque
    $clase_adicional = '';
    if (isset($block['className'])) {
        $clase_adicional = $block['className'];
    }

    // Opción de autoplay
    $autoplay_option = $autoplay ? 'true' : 'false';

    // Asegurarnos de cargar los scripts y estilos de Slick
    wp_enqueue_style('slick', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css');
    wp_enqueue_style('slick-theme', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css');
    wp_enqueue_script('slick', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js', array('jquery'), '1.8.1', true);
?>
    <link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/functions/blocks/slidergaleria/slidergaleria.min.css">

    <!-- Incluir directamente los estilos de Slick en caso de que wp_enqueue no funcione -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css" />
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css" />

    <!-- Estilo inline para el título -->
    <style>
        .slidergaleria-block h2 {
            text-align: center;
            margin-bottom: 50px;
        }
    </style>

    <div class="<?php echo $clase_adicional; ?> slidergaleria-block">
        <?php 
                $upload_dir = wp_upload_dir();

        ?>
        <div class="container">
            <?php if ($titulo) : ?>
                <h2><?php echo $titulo; ?></h2>
            <?php endif; ?>

            <?php if ($imagenes && count($imagenes) > 0) : ?>
                <!-- Slider para navegar entre los grupos de imágenes -->
                <div id="<?php echo $slider_id; ?>" class="slider-galeria">
                    <?php
                    $total_imagenes = count($imagenes);
                    $indice = 0;
                    $contador_img = 0;

                    // Creamos grupos de 5 imágenes (o menos para el último grupo)
                    while ($indice < $total_imagenes) {
                        // Verificar si tenemos suficientes imágenes para un grupo completo
                        $imagenes_restantes = $total_imagenes - $indice;
                        $tiene_suficientes = $imagenes_restantes >= 5;

                        // Si no hay suficientes imágenes, ajustamos la clase para el estilo
                        $clase_grupo = $tiene_suficientes ? '' : 'grupo-incompleto';

                        // Slide para cada grupo de imágenes
                        echo '<div class="slide-grupo">';
                        echo '<div class="grupo-imagenes ' . $clase_grupo . '">';

                        // Primera imagen grande
                        if ($indice < $total_imagenes) {
                            echo '<div class="imagen-grande">';
                            echo '<img src="' . esc_url($imagenes[$indice]['imagen']['url']) . '" alt="' . esc_attr($imagenes[$indice]['imagen']['alt']) . '" data-img-id="' . $contador_img . '" />';
                            echo '<div class="ampliar-icono" data-img-url="' . esc_url($imagenes[$indice]['imagen']['url']) . '" data-img-id="' . $contador_img . '"></div>';
                            echo '</div>';
                            $indice++;
                            $contador_img++;
                        }

                        if ($indice < $total_imagenes) {
                            // Determinar cuántas imágenes pequeñas habrá
                            $imagenes_pequenas = min(4, $total_imagenes - $indice);
                            
                            // Añadir clase adicional si hay menos de 4 imágenes
                            $clase_flex_column = ($imagenes_pequenas < 4) ? 'columna' : '';
                            
                            echo '<div class="imagenes-pequenas ' . $clase_flex_column . '">';
                            
                            // Procesar las imágenes pequeñas disponibles
                            for ($i = 0; $i < $imagenes_pequenas; $i++) {
                                echo '<div class="imagen-pequena">';
                                echo '<img src="' . esc_url($imagenes[$indice]['imagen']['url']) . '" alt="' . esc_attr($imagenes[$indice]['imagen']['alt']) . '" data-img-id="' . $contador_img . '" />';
                                echo '<div class="ampliar-icono" data-img-url="' . esc_url($imagenes[$indice]['imagen']['url']) . '" data-img-id="' . $contador_img . '"></div>';
                                echo '</div>';
                                $indice++;
                                $contador_img++;
                            }
                            
                            // No añadimos placeholders si estamos en modo columna
                            if ($imagenes_pequenas == 4) {
                                // Solo añadir placeholders en el modo grid normal (cuando hay 4 imágenes)
                                if ($imagenes_pequenas < 4) {
                                    for ($i = 0; $i < (4 - $imagenes_pequenas); $i++) {
                                        echo '<div class="imagen-pequena placeholder"></div>';
                                    }
                                }
                            }
                            
                            echo '</div>'; // Cierre de imagenes-pequenas
                        }

                        echo '</div>'; // Cierre de grupo-imagenes
                        echo '</div>'; // Cierre de slide-grupo
                    }
                    ?>
                </div>

                <!-- Modal para mostrar la imagen ampliada -->
                <div id="<?php echo $modal_id; ?>" class="modal-galeria">
                    <div class="modal-contenido">
                        <span class="cerrar-modal">&times;</span>
                        <img id="imagen-ampliada" src="" alt="Imagen ampliada">
                    </div>
                </div>
            <?php endif; ?>

            <img class="swipehand" src="<?php echo $upload_dir['baseurl']; ?>/2025/05/system-regular-157-swipe-hover-swipe.gif" alt="">
            <?php if ($enlace) : ?>
                <div class="enlace-galeria-container">
                    <a href="<?php echo esc_url($enlace['url']); ?>" class="btn-enlace-galeria" <?php echo $enlace['target'] ? 'target="' . esc_attr($enlace['target']) . '"' : ''; ?>>
                        <?php echo esc_html($enlace['title']); ?>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Incluir jQuery si no está ya cargado -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Incluir Slick directamente en caso de que wp_enqueue no funcione -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>

    <script>
        // Verificar que jQuery esté disponible
        if (typeof jQuery !== 'undefined') {
            jQuery(document).ready(function($) {
                // Verificar que el elemento exista antes de inicializar
                if ($('#<?php echo $slider_id; ?>').length > 0) {
                    // Inicializar después de un breve retraso para asegurar que todos los elementos estén cargados
                    setTimeout(function() {
                        $('#<?php echo $slider_id; ?>').slick({
                            dots: false,
                            arrows: false,
                            infinite: false,
                            speed: 300,
                            slidesToShow: 1,
                            slidesToScroll: 1,
                            autoplay: <?php echo $autoplay_option; ?>,
                            autoplaySpeed: 4000,
                            cssEase: 'linear',
                            adaptiveHeight: true,
                            draggable: true,
                            swipe: true,
                            swipeToSlide: true
                        });

                        console.log('Slick inicializado para: #<?php echo $slider_id; ?>');
                    }, 500);
                }

                // Funcionalidad para ampliar imágenes
                var modal = document.getElementById('<?php echo $modal_id; ?>');
                var modalImg = document.getElementById('imagen-ampliada');
                
                // Cuando se hace clic en el icono de ampliar
                $('.ampliar-icono').click(function() {
                    var imgUrl = $(this).data('img-url');
                    modalImg.src = imgUrl;
                    modal.style.display = "flex";
                });
                
                // Cerrar el modal cuando se hace clic en X
                $('.cerrar-modal').click(function() {
                    modal.style.display = "none";
                });
                
                // Cerrar el modal cuando se hace clic fuera de la imagen
                $(modal).click(function(e) {
                    if (e.target === modal) {
                        modal.style.display = "none";
                    }
                });
            });
        }
    </script>

    <!-- Solución alternativa si la inicialización normal no funciona -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Verificar si jQuery y Slick están disponibles
            if (typeof jQuery !== 'undefined' && typeof jQuery.fn.slick !== 'undefined') {
                var sliderElement = document.getElementById('<?php echo $slider_id; ?>');

                if (sliderElement) {
                    jQuery('#<?php echo $slider_id; ?>').slick({
                        dots: false,
                        arrows: false,
                        infinite: false,
                        speed: 300,
                        slidesToShow: 1,
                        slidesToScroll: 1,
                        autoplay: <?php echo $autoplay_option; ?>,
                        autoplaySpeed: 4000,
                        cssEase: 'linear',
                        adaptiveHeight: true,
                        draggable: true,
                        swipe: true,
                        swipeToSlide: true
                    });

                    console.log('Slick inicializado (método alternativo)');
                }

                // Funcionalidad para ampliar imágenes (versión alternativa)
                var modal = document.getElementById('<?php echo $modal_id; ?>');
                var modalImg = document.getElementById('imagen-ampliada');
                var iconos = document.querySelectorAll('.ampliar-icono');
                
                // Añadir evento clic a todos los iconos
                for (var i = 0; i < iconos.length; i++) {
                    iconos[i].addEventListener('click', function() {
                        var imgUrl = this.getAttribute('data-img-url');
                        modalImg.src = imgUrl;
                        modal.style.display = "flex";
                    });
                }
                
                // Cerrar modal
                document.querySelector('.cerrar-modal').addEventListener('click', function() {
                    modal.style.display = "none";
                });
                
                // Cerrar al hacer clic fuera de la imagen
                modal.addEventListener('click', function(e) {
                    if (e.target === modal) {
                        modal.style.display = "none";
                    }
                });
            } else {
                // Código para cargar scripts dinámicamente (sin cambios)
                // ...
            }
        });
    </script>
<?php
}
?>