<?php
add_action('acf/include_fields', function () {
    if (!function_exists('acf_add_local_field_group')) return;

    acf_add_local_field_group(array(
        'key' => 'group_textoalternado2',
        'title' => 'textoalternado2',
        'fields' => array(
            array(
                'key' => 'field_ta2_titulo',
                'label' => 'Título principal',
                'name' => 'titulo_textoalternado2',
                'type' => 'text',
            ),
            array(
                'key' => 'field_ta2_repetidor',
                'label' => 'Bloques alternados',
                'name' => 'bloques_alternados2',
                'type' => 'repeater',
                'layout' => 'block',
                'button_label' => 'Agregar bloque',
                'sub_fields' => array(
                    array(
                        'key' => 'field_ta2_tipo',
                        'label' => 'Tipo de bloque',
                        'name' => 'tipo_bloque',
                        'type' => 'select',
                        'choices' => array(
                            'imagen_izquierda' => 'Imagen izquierda',
                            'imagen_derecha' => 'Imagen derecha',
                            'slider' => 'Slider de imágenes',
                            'doble' => 'Bloque doble (dos columnas)',
                        ),
                        'default_value' => 'imagen_izquierda',
                    ),
                    array(
                        'key' => 'field_ta2_lado_imagen',
                        'label' => '¿Dónde aparece la imagen?',
                        'name' => 'lado_imagen',
                        'type' => 'select',
                        'choices' => array(
                            'izquierda' => 'Izquierda',
                            'derecha' => 'Derecha',
                        ),
                        'default_value' => 'izquierda',
                        'conditional_logic' => array(
                            array(
                                array('field' => 'field_ta2_tipo', 'operator' => '!=', 'value' => 'slider'),
                                array('field' => 'field_ta2_tipo', 'operator' => '!=', 'value' => 'doble')
                            )
                        )
                    ),
                    array(
                        'key' => 'field_ta2_imagen',
                        'label' => 'Imagen',
                        'name' => 'imagen',
                        'type' => 'image',
                        'return_format' => 'array',
                        'conditional_logic' => array(
                            array(
                                array('field' => 'field_ta2_tipo', 'operator' => '!=', 'value' => 'slider'),
                                array('field' => 'field_ta2_tipo', 'operator' => '!=', 'value' => 'doble')
                            )
                        )
                    ),
                    array(
                        'key' => 'field_ta2_titulo_item',
                        'label' => 'Título del bloque',
                        'name' => 'titulo_item',
                        'type' => 'text',
                        'conditional_logic' => array(
                            array(
                                array('field' => 'field_ta2_tipo', 'operator' => '!=', 'value' => 'slider'),
                                array('field' => 'field_ta2_tipo', 'operator' => '!=', 'value' => 'doble')
                            )
                        )
                    ),
                    array(
                        'key' => 'field_ta2_parrafo_item',
                        'label' => 'Párrafo del bloque',
                        'name' => 'parrafo_item',
                        'type' => 'wysiwyg',
                        'conditional_logic' => array(
                            array(
                                array('field' => 'field_ta2_tipo', 'operator' => '!=', 'value' => 'slider'),
                                array('field' => 'field_ta2_tipo', 'operator' => '!=', 'value' => 'doble')
                            )
                        )
                    ),
                    array(
                        'key' => 'field_ta2_slider_imagenes',
                        'label' => 'Imágenes del slider',
                        'name' => 'slider_imagenes',
                        'type' => 'repeater',
                        'conditional_logic' => array(
                            array(
                                array('field' => 'field_ta2_tipo', 'operator' => '==', 'value' => 'slider')
                            )
                        ),
                        'sub_fields' => array(
                            array(
                                'key' => 'field_ta2_slider_img',
                                'label' => 'Imagen',
                                'name' => 'imagen',
                                'type' => 'image',
                                'return_format' => 'array',
                                'library' => 'all',
                                'preview_size' => 'medium',
                            )
                        )
                    ),
                    array(
                        'key' => 'field_ta2_columna1',
                        'label' => 'Columna 1',
                        'name' => 'columna_1',
                        'type' => 'group',
                        'conditional_logic' => array(
                            array(
                                array('field' => 'field_ta2_tipo', 'operator' => '==', 'value' => 'doble')
                            )
                        ),
                        'sub_fields' => array(
                            array('key' => 'field_ta2_c1_titulo', 'label' => 'Título', 'name' => 'titulo', 'type' => 'text'),
                            array('key' => 'field_ta2_c1_imagen', 'label' => 'Imagen', 'name' => 'imagen', 'type' => 'image', 'return_format' => 'array'),
                            array('key' => 'field_ta2_c1_parrafo', 'label' => 'Texto', 'name' => 'parrafo', 'type' => 'wysiwyg'),
                        )
                    ),
                    array(
                        'key' => 'field_ta2_columna2',
                        'label' => 'Columna 2',
                        'name' => 'columna_2',
                        'type' => 'group',
                        'conditional_logic' => array(
                            array(
                                array('field' => 'field_ta2_tipo', 'operator' => '==', 'value' => 'doble')
                            )
                        ),
                        'sub_fields' => array(
                            array('key' => 'field_ta2_c2_titulo', 'label' => 'Título', 'name' => 'titulo', 'type' => 'text'),
                            array('key' => 'field_ta2_c2_imagen', 'label' => 'Imagen', 'name' => 'imagen', 'type' => 'image', 'return_format' => 'array'),
                            array('key' => 'field_ta2_c2_parrafo', 'label' => 'Texto', 'name' => 'parrafo', 'type' => 'wysiwyg'),
                        )
                    ),
                )
            )
        ),
        'location' => array(
            array(
                array('param' => 'block', 'operator' => '==', 'value' => 'acf/textoalternado2')
            )
        )
    ));
});

function textoalternado2_acf()
{
    acf_register_block_type([
        'name' => 'textoalternado2',
        'title' => __('Texto Alternado 2', 'tictac'),
        'description' => __('Bloques flexibles: imagen/texto, slider y doble columna', 'tictac'),
        'render_callback' => 'textoalternado2_render',
        'mode' => 'preview',
        'icon' => 'columns',
        'keywords' => ['bloques', 'flexible', 'imagen', 'slider', 'doble']
    ]);
}
add_action('acf/init', 'textoalternado2_acf');

function textoalternado2_render($block)
{
    $titulo = get_field('titulo_textoalternado2');
    $bloques = get_field('bloques_alternados2');
    $unique_id = 'slider_' . uniqid();
?>
    <div class="container textoalternado">
        <?php if ($titulo): ?>
            <h2 class="textoalternado-titulo"><?= esc_html($titulo) ?></h2>
        <?php endif; ?>

        <?php if ($bloques): ?>
            <div class="textoalternado-wrapper">
                <?php foreach ($bloques as $item):
                    $tipo = $item['tipo_bloque'];
                    if ($tipo === 'slider') {
                        $imagenes = $item['slider_imagenes'];
                        if (!empty($imagenes)) : ?>
                            <div class="textoslider-slider-container" id="<?= $unique_id ?>">
                                <div class="textoslider-slider">
                                    <div class="textoslider-track">
                                        <?php foreach ($imagenes as $img): ?>
                                            <div class="textoslider-slide">
                                                <img src="<?= esc_url($img['imagen']['url']) ?>" alt="<?= esc_attr($img['imagen']['alt']) ?>">
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <div class="textoslider-pagination"></div>
                            </div>
                            <script>
                                jQuery(document).ready(function($) {
                                    const container = $('#<?= $unique_id ?>');
                                    const track = container.find('.textoslider-track');
                                    const slides = container.find('.textoslider-slide');
                                    const pagination = container.find('.textoslider-pagination');
                                    const total = slides.length;
                                    let currentIndex = 0;
                                    let autoplayInterval;

                                    function getItemsPerView() {
                                        return window.innerWidth <= 800 ? 1 : 3;
                                    }

                                    function updateSlider() {
                                        const itemsPerView = getItemsPerView();
                                        const maxIndex = Math.max(0, total - itemsPerView);
                                        
                                        // Ajustar el índice actual si es mayor que el máximo permitido
                                        if (currentIndex > maxIndex) {
                                            currentIndex = maxIndex;
                                        }

                                        // Actualizar el ancho de cada slide según el viewport
                                        const slideWidth = 100 / itemsPerView;
                                        slides.css('flex', `0 0 ${slideWidth}%`);

                                        // Calcular y aplicar la transformación
                                        const translateX = -(currentIndex * slideWidth);
                                        track.css('transform', `translateX(${translateX}%)`);

                                        // Recrear paginación
                                        pagination.empty();
                                        for (let i = 0; i <= maxIndex; i++) {
                                            pagination.append(`<span class="textoslider-dot" data-slide="${i}"></span>`);
                                        }

                                        // Actualizar estado activo de los dots
                                        const dots = container.find('.textoslider-dot');
                                        dots.removeClass('active').eq(currentIndex).addClass('active');

                                        // Reattach click events
                                        dots.off('click').on('click', function() {
                                            currentIndex = parseInt($(this).data('slide'));
                                            updateSlider();
                                            resetAutoplay();
                                        });
                                    }

                                    function nextSlide() {
                                        const itemsPerView = getItemsPerView();
                                        const maxIndex = Math.max(0, total - itemsPerView);
                                        currentIndex = currentIndex < maxIndex ? currentIndex + 1 : 0;
                                        updateSlider();
                                    }

                                    function resetAutoplay() {
                                        clearInterval(autoplayInterval);
                                        autoplayInterval = setInterval(nextSlide, 5000);
                                    }

                                    // Inicializar
                                    updateSlider();
                                    resetAutoplay();

                                    // Actualizar en resize
                                    $(window).on('resize', function() {
                                        updateSlider();
                                    });
                                });
                            </script>
                        <?php endif;
                    } elseif ($tipo === 'doble') {
                        ?>
                        <div class="bloque doble">
                            <div class="bloque-texto">
                                <h4 class="bloque-titulo2"><?= esc_html($item['columna_1']['titulo']) ?></h4>
                                <div class="bloque-imagen">
                                    <img src="<?= esc_url($item['columna_1']['imagen']['url']) ?>" alt="">
                                </div>
                                <div class="bloque-parrafo"> <?= wpautop($item['columna_1']['parrafo']) ?> </div>
                            </div>
                            <div class="bloque-texto">
                                <h4 class="bloque-titulo2"><?= esc_html($item['columna_2']['titulo']) ?></h4>
                                <div class="bloque-imagen">
                                    <img src="<?= esc_url($item['columna_2']['imagen']['url']) ?>" alt="">
                                </div>
                                <div class="bloque-parrafo"> <?= wpautop($item['columna_2']['parrafo']) ?> </div>
                            </div>
                        </div>
                    <?php
                    } else {
                        $imagen = $item['imagen'];
                        $lado = $item['lado_imagen'] ?? 'izquierda';
                        $titulo_item = $item['titulo_item'];
                        $parrafo_item = $item['parrafo_item'];
                        $clase_lado = $imagen ? ($lado === 'derecha' ? 'invertido' : '') : '';
                    ?>
                        <div class="bloque <?= $clase_lado ?>">
                            <?php if ($imagen): ?>
                                <div class="bloque-imagen">
                                    <img src="<?= esc_url($imagen['url']) ?>" alt="">
                                </div>
                            <?php endif; ?>
                            <div class="bloque-texto <?= !$imagen ? 'ancho-completo' : '' ?>">
                                <?php if ($titulo_item): ?>
                                    <h3 class="bloque-titulo"> <?= esc_html($titulo_item) ?> </h3>
                                <?php endif; ?>
                                <?php if ($parrafo_item): ?>
                                    <div class="bloque-parrafo"> <?= wpautop($parrafo_item) ?> </div>
                                <?php endif; ?>
                            </div>
                        </div>
                <?php
                    }
                endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <style>
        .textoalternado {
            box-shadow: 0px 0px 15px 0px #2E2D2C33;
            backdrop-filter: blur(3px);
            border-radius: 20px;
            padding: 50px;
            margin-top: 50px;
        }

        .textoalternado-titulo {
            text-align: center;
            font-size: 2.2rem;
            color: #871727;
            font-family: 'manhaj' !important;
            margin-bottom: 50px;
        }

        .textoalternado-wrapper {
            display: flex;
            flex-direction: column;
            gap: 60px;
        }

        .bloque-titulo2 {
            font-size: 20px;
            color: #DB7461;
            font-family: 'Duran-Medium';
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 20px;
            text-align: center;
        }

        .bloque-parrafo h4 {
            font-size: 20px;
            color: #DB7461;
            font-family: 'Duran-Medium';
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 20px;
            text-align: center;
        }

        .bloque {
            display: flex;
            align-items: stretch;
            gap: 40px;
        }

        .bloque.invertido {
            flex-direction: row-reverse;
        }

        .bloque-imagen {
            flex: 1 1 45%;
        }

        .bloque-imagen img {
            width: 100%;
            border-radius: 10px;
            height: 100%;
            object-fit: cover;
        }

        .doble img {
            width: 100%;
            height: 300px;
            object-fit: cover;
            border-radius: 10px;
        }

        .bloque-texto {
            flex: 1 1 50%;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .bloque-texto.ancho-completo {
            flex: 1 1 100%;
            text-align: center;
        }

        .bloque-titulo {
            font-size: 25px;
            color: #DB7461;
            font-family: 'Duran-Medium';
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 20px;
            text-align: center;
        }

        .bloque-parrafo {
            margin-top: 20px;
        }

        .textoslider-slide {
            padding: 0 10px;
            box-sizing: border-box;
        }

        .textoslider-slide img {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 15px;
        }

        .textoslider-track {
            display: flex;
            transition: transform 0.8s ease-in-out;
            width: 100%;
        }

        .textoslider-slider {
            width: 100%;
            overflow: hidden;
            border-radius: 15px;
        }

        .textoslider-pagination {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin-top: 25px;
        }

        .textoslider-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: #ccc;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .doble {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 50px;
            flex-wrap: nowrap;
        }

        .doble .bloque-texto {
            width: 45%;
        }

        .textoslider-dot.active {
            background-color: #DB7461;
            transform: scale(1.2);
        }

        .textoslider-dot:hover {
            background-color: #DB7461;
            transform: scale(1.1);
        }

        @media (max-width: 800px) {
            .textoslider-slide img {
                height: 300px;
            }
        }

        @media (max-width: 768px) {
            .bloque {
                flex-direction: column !important;
                text-align: center;
            }

            .bloque-imagen,
            .bloque-texto {
                flex: 1 1 100%;
            }

            .doble {
                flex-direction: column;
                gap: 30px;
            }

            .doble .bloque-texto {
                width: 100%;
            }
        }

        @media (max-width: 480px) {
            .textoslider-slide img {
                height: 250px;
            }

            .textoalternado {
                padding: 30px;
            }
        }
    </style>
<?php
}
?>