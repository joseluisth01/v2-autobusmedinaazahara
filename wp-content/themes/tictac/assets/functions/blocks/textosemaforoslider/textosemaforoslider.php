<?php
add_action('acf/include_fields', function () {
    if (! function_exists('acf_add_local_field_group')) {
        return;
    }

    acf_add_local_field_group(array(
        'key' => 'group_textosemaforoslider_001',
        'title' => 'textosemaforoslider',
        'fields' => array(
            array(
                'key' => 'field_textosemaforoslider_titulo',
                'label' => 'titulo_textosemaforoslider',
                'name' => 'titulo_textosemaforoslider',
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
                'key' => 'field_textosemaforoslider_parrafo_inicial',
                'label' => 'parrafo_inicial',
                'name' => 'parrafo_inicial',
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
                'placeholder' => '',
                'maxlength' => '',
                'rows' => 4,
                'new_lines' => 'wpautop',
            ),
            array(
                'key' => 'field_textosemaforoslider_slider',
                'label' => 'slider_semaforo',
                'name' => 'slider_semaforo',
                'aria-label' => '',
                'type' => 'repeater',
                'instructions' => 'Cada elemento será una slide del slider',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'layout' => 'block',
                'pagination' => 0,
                'min' => 0,
                'max' => 0,
                'collapsed' => '',
                'button_label' => 'Agregar Slide',
                'rows_per_page' => 20,
                'sub_fields' => array(
                    array(
                        'key' => 'field_textosemaforoslider_titulo_slide',
                        'label' => 'titulo_slide',
                        'name' => 'titulo_slide',
                        'aria-label' => '',
                        'type' => 'text',
                        'instructions' => 'Título H3 de cada slide',
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
                        'parent_repeater' => 'field_textosemaforoslider_slider',
                    ),
                    array(
                        'key' => 'field_textosemaforoslider_pasos',
                        'label' => 'pasos_semaforo',
                        'name' => 'pasos_semaforo',
                        'aria-label' => '',
                        'type' => 'repeater',
                        'instructions' => 'Pasos del semáforo para esta slide',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'layout' => 'table',
                        'pagination' => 0,
                        'min' => 0,
                        'max' => 0,
                        'collapsed' => '',
                        'button_label' => 'Agregar Paso',
                        'rows_per_page' => 20,
                        'sub_fields' => array(
                            array(
                                'key' => 'field_textosemaforoslider_titulo_paso',
                                'label' => 'titulo_paso',
                                'name' => 'titulo_paso',
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
                                'parent_repeater' => 'field_textosemaforoslider_pasos',
                            ),
                            array(
                                'key' => 'field_textosemaforoslider_parrafo_paso',
                                'label' => 'parrafo_paso',
                                'name' => 'parrafo_paso',
                                'aria-label' => '',
                                'type' => 'textarea',
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
                                'rows' => 3,
                                'placeholder' => '',
                                'parent_repeater' => 'field_textosemaforoslider_pasos',
                            )
                        ),
                        'parent_repeater' => 'field_textosemaforoslider_slider',
                    )
                ),
            ),
            array(
                'key' => 'field_textosemaforoslider_parrafo_final',
                'label' => 'parrafo_final',
                'name' => 'parrafo_final',
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
                'placeholder' => '',
                'maxlength' => '',
                'rows' => 4,
                'new_lines' => 'wpautop',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/textosemaforoslider',
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

function textosemaforoslider_acf()
{
    acf_register_block_type([
        'name'        => 'textosemaforoslider',
        'title'        => __('textosemaforoslider', 'tictac'),
        'description'    => __('Bloque con título, párrafo, slider de semáforos y párrafo final', 'tictac'),
        'render_callback'  => 'textosemaforoslider',
        'mode'        => 'preview',
        'icon'        => 'slides',
        'keywords'      => ['custom', 'textosemaforoslider', 'semaforo', 'slider', 'pasos'],
    ]);
}

add_action('acf/init', 'textosemaforoslider_acf');

function textosemaforoslider_scripts()
{
    // ✅ SOLO CARGAR SCRIPTS SI NO ES AJAX Y NO ES ADMIN
    if (!is_admin() && !wp_doing_ajax()) {
        wp_enqueue_style('textosemaforoslider', get_stylesheet_directory_uri() . '/assets/functions/blocks/textosemaforoslider/textosemaforoslider.min.css');
        
        // ✅ REGISTRAR EL SCRIPT JAVASCRIPT DE FORMA CORRECTA
        wp_enqueue_script('textosemaforoslider-js', get_stylesheet_directory_uri() . '/assets/js/textosemaforoslider.js', array('jquery'), '1.0.0', true);
        
        // ✅ SI NO TIENES ARCHIVO SEPARADO, USAR INLINE SCRIPT
        $semaforo_js = "
        window.semaforoSliders = window.semaforoSliders || {};

        function initSemaforoSlider(blockId) {
            const container = document.getElementById(blockId);
            if (!container) return;

            const slides = container.querySelectorAll('.textosemaforoslider-slide');
            if (!slides.length) return;

            window.semaforoSliders[blockId] = {
                currentSlideIndex: 0,
                totalSlides: slides.length,
                semaforoIntervals: []
            };

            startSemaforoAnimation(blockId, 0);
            updateSemaforoSlider(blockId);
        }

        function changeSemaforoSlide(blockId, direction) {
            const sliderData = window.semaforoSliders[blockId];
            if (!sliderData) return;

            clearAllSemaforoIntervals(blockId);
            
            sliderData.currentSlideIndex += direction;
            
            if (sliderData.currentSlideIndex >= sliderData.totalSlides) {
                sliderData.currentSlideIndex = 0;
            } else if (sliderData.currentSlideIndex < 0) {
                sliderData.currentSlideIndex = sliderData.totalSlides - 1;
            }
            
            updateSemaforoSlider(blockId);
            startSemaforoAnimation(blockId, sliderData.currentSlideIndex);
        }

        function updateSemaforoSlider(blockId) {
            const container = document.getElementById(blockId);
            const sliderData = window.semaforoSliders[blockId];
            if (!container || !sliderData) return;

            const track = container.querySelector('.textosemaforoslider-track');
            if (!track) return;
            
            track.style.transform = 'translateX(-' + (sliderData.currentSlideIndex * 100) + '%)';
        }

        function clearAllSemaforoIntervals(blockId) {
            const sliderData = window.semaforoSliders[blockId];
            if (!sliderData) return;

            sliderData.semaforoIntervals.forEach(interval => clearInterval(interval));
            sliderData.semaforoIntervals = [];
            
            const container = document.getElementById(blockId);
            if (container) {
                container.querySelectorAll('.semaforo-item').forEach(item => {
                    item.classList.remove('active');
                });
            }
        }

        function startSemaforoAnimation(blockId, slideIndex) {
            const container = document.getElementById(blockId);
            const sliderData = window.semaforoSliders[blockId];
            if (!container || !sliderData) return;

            const currentSlide = container.querySelector('[data-slide=\"' + slideIndex + '\"]');
            if (!currentSlide) return;

            const items = currentSlide.querySelectorAll('.semaforo-item');
            let index = 0;

            function resetItems() {
                items.forEach((item, i) => {
                    item.classList.remove('active');
                });
            }

            function activateNext() {
                if (index < items.length) {
                    items[index].classList.add('active');
                    index++;
                    const timeoutId = setTimeout(activateNext, 1500);
                    sliderData.semaforoIntervals.push(timeoutId);
                } else {
                    const timeoutId = setTimeout(() => {
                        resetItems();
                        const timeoutId2 = setTimeout(() => {
                            index = 0;
                            activateNext();
                        }, 1500);
                        sliderData.semaforoIntervals.push(timeoutId2);
                    }, 1500);
                    sliderData.semaforoIntervals.push(timeoutId);
                }
            }

            if (items.length > 0) {
                activateNext();
            }
        }";
        
        wp_add_inline_script('jquery', $semaforo_js);
    }
}
add_action('wp_enqueue_scripts', 'textosemaforoslider_scripts');

function textosemaforoslider($block)
{
    // ✅ NO RENDERIZAR SI ES AJAX
    if (wp_doing_ajax()) {
        return;
    }

    $titulo = get_field("titulo_textosemaforoslider");
    $parrafo_inicial = get_field("parrafo_inicial");
    $slides = get_field("slider_semaforo");
    $parrafo_final = get_field("parrafo_final");
    $upload_dir = wp_upload_dir();
    
    // Generar un ID único para este bloque
    $semaforo_block_id = 'textosemaforoslider-' . uniqid();

    if (empty($slides)) return;
?>
    <div class="container textosemaforoslider" id="<?= $semaforo_block_id ?>">
        <div class="textosemaforoslider-content">
            <?php if ($titulo): ?>
                <h2 class="textosemaforoslider-titulo"><?= $titulo ?></h2>
            <?php endif; ?>
            
            <?php if ($parrafo_inicial): ?>
                <div class="textosemaforoslider-parrafo-inicial"><?= wpautop($parrafo_inicial) ?></div>
            <?php endif; ?>
        </div>

        <div class="textosemaforoslider-slider-container">
            <div class="textosemaforoslider-slider">
                <div class="textosemaforoslider-track">
                    <?php foreach ($slides as $slide_index => $slide) : ?>
                        <div class="textosemaforoslider-slide" data-slide="<?= $slide_index ?>">
                            <?php if ($slide['titulo_slide']): ?>
                                <p class="slide-titulo"><?= $slide['titulo_slide'] ?></p>
                            <?php endif; ?>

                            <?php if ($slide['pasos_semaforo']) : ?>
                                <div class="slide-semaforo">
                                    <div class="semaforo-contenedor">
                                        <?php foreach ($slide['pasos_semaforo'] as $paso_index => $paso) : ?>
                                            <div class="semaforo-item" data-paso="<?= $paso_index ?>">
                                                <div class="semaforo-textos">
                                                    <?php if ($paso['titulo_paso']) : ?>
                                                        <div class="semaforo-titulo-paso"><?= $paso['titulo_paso'] ?></div>
                                                    <?php endif; ?>
                                                    <div class="divrayas">
                                                        <?php if ($paso['parrafo_paso']) : ?>
                                                            <p class="semaforo-parrafo-paso"><?= $paso['parrafo_paso'] ?></p>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Navegación del slider -->
            <div class="textosemaforoslider-navigation">
                <button class="nav-btn prev-btn" onclick="changeSemaforoSlide('<?= $semaforo_block_id ?>', -1)">
                    <img src="https://autobusmedinaazahara.com/wp-content/uploads/2025/07/Vector-12.svg" alt="Anterior">
                </button>
                <button class="nav-btn next-btn" onclick="changeSemaforoSlide('<?= $semaforo_block_id ?>', 1)">
                    <img src="https://autobusmedinaazahara.com/wp-content/uploads/2025/07/Vector-13.svg" alt="Siguiente">
                </button>
            </div>
        </div>

        <?php if ($parrafo_final): ?>
            <div class="textosemaforoslider-content">
                <div class="textosemaforoslider-parrafo-final"><?= wpautop($parrafo_final) ?></div>
            </div>
        <?php endif; ?>
    </div>

    <style>
        .textosemaforoslider {
            box-shadow: 0px 0px 15px 0px #2E2D2C33;
            backdrop-filter: blur(3px);
            border-radius: 20px;
            padding: 50px !important;
            margin-top: 50px;
        }

        .textosemaforoslider-content {
            text-align: center;
        }

        .textosemaforoslider .divrayas{
            height: 200px;
        }

        .textosemaforoslider-titulo {
            color: #871727;
            font-size: 2.5rem;
            margin-bottom: 30px;
            line-height: 1.2;
            font-family: 'manhaj' !important;
        }

        .textosemaforoslider-parrafo-inicial,
        .textosemaforoslider-parrafo-final {
            margin: 0 auto;
            font-size: 1.1rem;
            line-height: 1.6;
            color: #2E2D2C;
        }

        .textosemaforoslider-parrafo-inicial p,
        .textosemaforoslider-parrafo-final p {
            margin-bottom: 20px;
        }

        .textosemaforoslider-slider-container {
            position: relative;
            overflow: hidden;
        }

        .textosemaforoslider-slider {
            position: relative;
            width: 100%;
            overflow: hidden;
        }

        .textosemaforoslider-track {
            display: flex;
            transition: transform 0.8s ease-in-out;
            width: 100%;
        }

        .textosemaforoslider-slide {
            flex: 0 0 100%;
            padding: 0 20px;
            box-sizing: border-box;
        }

        .slide-titulo {
            color: #DB7461 !important;
            font-size: 30px !important;
            margin-bottom: 50px;
            margin-top: 30px;
            text-align: center;
            font-family: 'Duran-Medium' !important;
            letter-spacing: 2px;
        }

        .slide-semaforo {
            margin-bottom: 30px;
        }

        .semaforo-contenedor {
            display: flex;
            gap: 20px;
            justify-content: space-between;
            flex-wrap: wrap;
        }

        .semaforo-item {
            display: flex;
            align-items: center;
            border-radius: 10px;
            transition: all 0.5s ease;
            flex: 1;
            min-width: 200px;
            margin-bottom: 20px;
        }

        .semaforo-textos {
            flex: 1;
        }

        .semaforo-titulo-paso {
            background-color: #B7B7B7;
            color: white;
            padding: 10px 20px;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
            text-align: center;
            font-weight: bold;
            font-size: 16px;
            transition: all 0.5s ease;
        }

        .divrayas {
            border: 4px dashed #B7B7B7;
            border-bottom-left-radius: 10px;
            border-bottom-right-radius: 10px;
            border-top: 0px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.5s ease;
        }

        .semaforo-parrafo-paso {
            font-family: 'Duran-Medium' !important;
            line-height: 1.4;
            color: #000000 !important;
            margin: 0;
            transition: all 0.5s ease;
            font-size: 14px !important;
            text-align: center !important;
            letter-spacing: 1px;
        }

        /* Estados activos del semáforo */
        .semaforo-item.active {
            transform: scale(1.02);
        }

        .semaforo-item.active .semaforo-titulo-paso {
            background-color: #DB7461 !important;
            color: #2E2D2C !important;
        }

        .semaforo-item.active .semaforo-parrafo-paso {
            color: white !important;
        }

        .semaforo-item.active .divrayas {
            background-color: #871727 !important;
            border: none !important;
        }

        /* Navegación del slider */
        .textosemaforoslider-navigation {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            pointer-events: none;
            z-index: 10;
            height: 80px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .textosemaforoslider {
                padding: 40px 15px !important;
            }

            .textosemaforoslider-titulo {
                font-size: 2rem;
                margin-bottom: 20px;
            }

            .slide-titulo {
                font-size: 25px;
                margin-bottom: 20px;
            }

            .semaforo-contenedor {
                flex-direction: column;
                gap: 15px;
            }

            .semaforo-item {
                min-width: 100%;
            }

            .semaforo-titulo-paso {
                font-size: 14px;
                height: 35px;
            }

            .divrayas {
                padding: 15px;
            }

            .semaforo-parrafo-paso {
                font-size: 12px !important;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof initSemaforoSlider === 'function') {
                initSemaforoSlider('<?= $semaforo_block_id ?>');
            }
        });
    </script>
<?php
}
?>