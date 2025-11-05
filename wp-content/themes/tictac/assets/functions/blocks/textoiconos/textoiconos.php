<?php
add_action('acf/include_fields', function () {
    if (! function_exists('acf_add_local_field_group')) {
        return;
    }

    acf_add_local_field_group(array(
        'key' => 'group_textoiconos_001',
        'title' => 'textoiconos',
        'fields' => array(
            array(
                'key' => 'field_textoiconos_titulo',
                'label' => 'titulo_textoiconos',
                'name' => 'titulo_textoiconos',
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
                'key' => 'field_textoiconos_parrafo',
                'label' => 'parrafo_textoiconos',
                'name' => 'parrafo_textoiconos',
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
                'key' => 'field_textoiconos_repetidor',
                'label' => 'repetidor_iconos',
                'name' => 'repetidor_iconos',
                'aria-label' => '',
                'type' => 'repeater',
                'instructions' => '',
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
                'button_label' => 'Agregar Icono',
                'rows_per_page' => 20,
                'sub_fields' => array(
                    array(
                        'key' => 'field_textoiconos_icono',
                        'label' => 'icono',
                        'name' => 'icono',
                        'aria-label' => '',
                        'type' => 'image',
                        'instructions' => 'Subir icono o imagen',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'return_format' => 'array',
                        'library' => 'all',
                        'preview_size' => 'thumbnail',
                        'parent_repeater' => 'field_textoiconos_repetidor',
                    ),
                    array(
                        'key' => 'field_textoiconos_titulo_icono',
                        'label' => 'titulo_icono',
                        'name' => 'titulo_icono',
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
                        'parent_repeater' => 'field_textoiconos_repetidor',
                    ),
                    array(
                        'key' => 'field_textoiconos_parrafo_icono',
                        'label' => 'parrafo_icono',
                        'name' => 'parrafo_icono',
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
                        'parent_repeater' => 'field_textoiconos_repetidor',
                    ),
                    array(
                        'key' => 'field_textoiconos_layout_alternativo',
                        'label' => 'Usar diseño alternativo',
                        'name' => 'layout_alternativo',
                        'type' => 'true_false',
                        'ui' => 1,
                        'instructions' => 'Si se selecciona, el título se mostrará primero, luego la imagen y después el párrafo.',
                        'default_value' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'parent_repeater' => 'field_textoiconos_repetidor',
                    ),

                ),

            ),
            array(
                'key' => 'field_horarios_texto_boton2',
                'label' => 'texto_boton',
                'name' => 'texto_boton2',
                'aria-label' => '',
                'type' => 'text',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '50',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => 'RESERVA YA TU BILLETE DE BUS',
                'maxlength' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
            ),
            array(
                'key' => 'field_horarios_enlace_boton2',
                'label' => 'enlace_boton',
                'name' => 'enlace_boton2',
                'aria-label' => '',
                'type' => 'url',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '50',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'placeholder' => 'https://ejemplo.com',
            ),

        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/textoiconos',
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

function textoiconos_acf()
{
    acf_register_block_type([
        'name'        => 'textoiconos',
        'title'        => __('textoiconos', 'tictac'),
        'description'    => __('Bloque con título, párrafo y repetidor de iconos con texto', 'tictac'),
        'render_callback'  => 'textoiconos',
        'mode'        => 'preview',
        'icon'        => 'grid-view',
        'keywords'      => ['custom', 'textoiconos', 'iconos', 'servicios'],
    ]);
}

add_action('acf/init', 'textoiconos_acf');

function textoiconos_scripts()
{
    if (!is_admin()) {
        wp_enqueue_style('textoiconos', get_stylesheet_directory_uri() . '/assets/functions/blocks/textoiconos/textoiconos.min.css');
    }
}
add_action('wp_enqueue_scripts', 'textoiconos_scripts');

function textoiconos($block)
{
    $titulo = get_field("titulo_textoiconos");
    $parrafo = get_field("parrafo_textoiconos");
    $iconos = get_field("repetidor_iconos");
    $texto_boton = get_field("texto_boton2");
    $enlace_boton = get_field("enlace_boton2");
    
    // Generar un ID único para este bloque
    $block_id = 'textoiconos-' . uniqid();
?>
    <div class="container textoiconos" id="<?= $block_id ?>">
        <div class="textoiconos-content">
            <?php if ($titulo): ?>
                <h2 class="textoiconos-titulo"><?= $titulo ?></h2>
            <?php endif; ?>

            <?php if ($parrafo): ?>
                <div class="textoiconos-parrafo"><?= wpautop($parrafo) ?></div>
            <?php endif; ?>
        </div>
                <!-- Controles del slider (solo visibles en móvil) -->
                <div class="slider-controls">
                    <button class="slider-btn prev-btn" onclick="moveSlide('<?= $block_id ?>', -1)"><img src="https://autobusmedinaazahara.com/wp-content/uploads/2025/07/Vector-12.svg" alt=""></button>
                    <button class="slider-btn next-btn" onclick="moveSlide('<?= $block_id ?>', 1)"><img src="https://autobusmedinaazahara.com/wp-content/uploads/2025/07/Vector-13.svg" alt=""></button>
                </div>
        <?php if ($iconos) : ?>
            <div class="textoiconos-grid-wrapper">
                <div class="textoiconos-grid">
                    <?php foreach ($iconos as $icono) : ?>
                        <?php $layout_alternativo = !empty($icono['layout_alternativo']); ?>
                        <div class="icono-item <?= $layout_alternativo ? 'layout-alternativo' : 'layout-normal'; ?>">

                            <?php if ($layout_alternativo) : ?>
                                <?php if ($icono['titulo_icono']): ?>
                                    <div class="icono-titulo"><?= $icono['titulo_icono'] ?></div>
                                <?php endif; ?>

                                <?php if ($icono['icono']): ?>
                                    <div class="icono-imagen">
                                        <img src="<?= esc_url($icono['icono']['url']); ?>" alt="<?= esc_attr($icono['icono']['alt']); ?>">
                                    </div>
                                <?php endif; ?>

                                <?php if ($icono['parrafo_icono']): ?>
                                    <div class="icono-parrafo"><?= wpautop($icono['parrafo_icono']) ?></div>
                                <?php endif; ?>
                            <?php else : ?>
                                <?php if ($icono['icono']): ?>
                                    <div class="icono-imagen">
                                        <img src="<?= esc_url($icono['icono']['url']); ?>" alt="<?= esc_attr($icono['icono']['alt']); ?>">
                                    </div>
                                <?php endif; ?>

                                <div class="icono-contenido">
                                    <?php if ($icono['titulo_icono']): ?>
                                        <div class="icono-titulo"><?= $icono['titulo_icono'] ?></div>
                                    <?php endif; ?>

                                    <?php if ($icono['parrafo_icono']): ?>
                                        <div class="icono-parrafo"><?= wpautop($icono['parrafo_icono']) ?></div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                

            </div>
        <?php endif; ?>
        

        
        <?php if (!empty($enlace_boton)): ?>
            <div class="horarios-boton-container">
                <a href="<?= esc_url($enlace_boton) ?>" class="horarios-boton" target="_blank">
                    <?= esc_html($texto_boton) ?>
                </a>
            </div>
        <?php endif; ?>
    </div>

    <style>
        .textoiconos {
            box-shadow: 0px 0px 15px 0px #2E2D2C33;
            backdrop-filter: blur(3px);
            border-radius: 20px;
            padding: 50px !important;
            margin-top: 50px;
        }

        .textoiconos-content {
            text-align: center;
            margin-bottom: 50px;
        }

        .textoiconos-titulo {
            color: #871727;
            font-size: 2.5rem;
            margin-bottom: 30px;
            line-height: 1.2;
            font-family: 'manhaj' !important;
        }

        .icono-item.layout-alternativo .icono-imagen {
            height: auto !important;
        }

        .icono-item.layout-alternativo .icono-imagen img {
            max-width: 100% !important;
            border-radius: 10px !important;
        }

        .textoiconos-parrafo {
            margin: 0 auto;
            font-size: 1.1rem;
            line-height: 1.6;
            color: #2E2D2C;
            max-width: 800px;
        }

        .textoiconos-parrafo p {
            margin-bottom: 20px;
        }

        .textoiconos-grid-wrapper {
            position: relative;
        }

        .textoiconos-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            transition: transform 0.3s ease;
        }

        .icono-item.layout-normal {
            flex: 0 1 33%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .icono-item.layout-alternativo {
            flex: 0 1 33%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .icono-item {
            text-align: center;
            padding: 30px;
            transition: all 0.3s ease;
        }

        .icono-imagen {
            margin-bottom: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 80px;
        }

        .icono-imagen img {
            max-width: 100px;
            width: auto;
            height: auto;
            object-fit: contain;
        }

        .icono-contenido {
            text-align: center;
        }

        .icono-titulo {
            color: #DB7461;
            font-size: 25px;
            margin-bottom: 15px;
            line-height: 1.3;
            font-family: 'Duran-Medium';
            text-transform: uppercase;
            letter-spacing: 4px;
        }

        .icono-parrafo {
            color: #2E2D2C;
            font-size: 1rem !important;
            line-height: 28px !important;
            margin: 0;
            letter-spacing: 0.02em !important;
        }

        .icono-parrafo p {
            margin-bottom: 15px;
        }

        .icono-parrafo p:last-child {
            margin-bottom: 0;
        }

        /* Controles del slider - ocultos por defecto */
        .slider-controls {
            display: none;
            align-items: center;
            justify-content: center;
            margin-top: 30px;
            gap: 20px;
        }

        .slider-btn {
            color: white;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            font-size: 20px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            background-color: inherit !important;
        }



        /* Responsive - Tablet */
        @media (max-width: 1024px) {
            .icono-item.layout-normal,
            .icono-item.layout-alternativo {
                flex: 0 1 50%;
            }
        }

        /* Responsive - Móvil (800px y menos) */
        @media (max-width: 800px) {
            .textoiconos-grid-wrapper {
                overflow: hidden;
                margin-top: -80px;
            }

            .textoiconos-grid {
                flex-wrap: nowrap;
                justify-content: flex-start;
                transition: transform 0.3s ease;
            }

            .icono-item.layout-normal,
            .icono-item.layout-alternativo {
                flex: 0 0 100%;
                min-width: 100%;
            }

            .slider-controls {
                display: flex;
                justify-content: space-between;
                z-index: 9999999999999999999999999999;
        position: relative;
            }

            .textoiconos {
                padding: 40px 20px !important;
            }

            .textoiconos-titulo {
                font-size: 2rem;
                margin-bottom: 20px;
            }

            .textoiconos-parrafo {
                font-size: 1rem;
            }

            .icono-item {
                padding: 25px 20px;
            }


            .icono-parrafo {
                font-size: 0.95rem;
            }
        }

        @media (max-width: 480px) {
            .textoiconos-titulo {
                font-size: 1.8rem;
            }

            .icono-item {
                padding: 20px 15px;
            }

        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            initSlider('<?= $block_id ?>');
        });

        function initSlider(blockId) {
            const container = document.getElementById(blockId);
            if (!container) return;

            const grid = container.querySelector('.textoiconos-grid');
            const items = container.querySelectorAll('.icono-item');
            const dotsContainer = container.querySelector('.slider-dots');
            
            if (!grid || !items.length) return;

            let currentSlide = 0;
            const totalSlides = items.length;

            // Crear dots
            dotsContainer.innerHTML = '';
            for (let i = 0; i < totalSlides; i++) {
                const dot = document.createElement('div');
                dot.className = 'slider-dot';
                dot.onclick = () => goToSlide(blockId, i);
                dotsContainer.appendChild(dot);
            }

            updateSlider(blockId);
        }

        function moveSlide(blockId, direction) {
            const container = document.getElementById(blockId);
            const items = container.querySelectorAll('.icono-item');
            const totalSlides = items.length;
            
            if (!container.currentSlide) container.currentSlide = 0;
            
            container.currentSlide += direction;
            
            if (container.currentSlide >= totalSlides) {
                container.currentSlide = 0;
            } else if (container.currentSlide < 0) {
                container.currentSlide = totalSlides - 1;
            }
            
            updateSlider(blockId);
        }

        function goToSlide(blockId, slideIndex) {
            const container = document.getElementById(blockId);
            container.currentSlide = slideIndex;
            updateSlider(blockId);
        }

        function updateSlider(blockId) {
            const container = document.getElementById(blockId);
            const grid = container.querySelector('.textoiconos-grid');
            const dots = container.querySelectorAll('.slider-dot');
            const prevBtn = container.querySelector('.prev-btn');
            const nextBtn = container.querySelector('.next-btn');
            
            if (!container.currentSlide) container.currentSlide = 0;
            
            // Solo aplicar transformación en pantallas móviles
            if (window.innerWidth <= 800) {
                const translateX = -container.currentSlide * 100;
                grid.style.transform = `translateX(${translateX}%)`;
                
                // Actualizar dots
                dots.forEach((dot, index) => {
                    dot.classList.toggle('active', index === container.currentSlide);
                });
            } else {
                // En pantallas grandes, resetear transformación
                grid.style.transform = 'translateX(0%)';
                dots.forEach(dot => dot.classList.remove('active'));
            }
        }

        // Reinicializar slider al cambiar tamaño de ventana
        window.addEventListener('resize', function() {
            document.querySelectorAll('[id^="textoiconos-"]').forEach(container => {
                updateSlider(container.id);
            });
        });
    </script>
<?php
}