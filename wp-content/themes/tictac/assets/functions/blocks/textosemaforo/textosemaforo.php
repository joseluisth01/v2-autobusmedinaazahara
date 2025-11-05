<?php
add_action('acf/include_fields', function () {
    if (! function_exists('acf_add_local_field_group')) {
        return;
    }

    acf_add_local_field_group(array(
        'key' => 'group_textosemaforo_001',
        'title' => 'textosemaforo',
        'fields' => array(
            array(
                'key' => 'field_textosemaforo_titulo',
                'label' => 'titulo_textosemaforo',
                'name' => 'titulo_textosemaforo',
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
                'key' => 'field_textosemaforo_parrafo',
                'label' => 'parrafo_textosemaforo',
                'name' => 'parrafo_textosemaforo',
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
                'key' => 'field_textosemaforo_repetidor',
                'label' => 'repetidor_semaforo',
                'name' => 'repetidor_semaforo',
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
                'layout' => 'table',
                'pagination' => 0,
                'min' => 0,
                'max' => 0,
                'collapsed' => '',
                'button_label' => 'Agregar Paso',
                'rows_per_page' => 20,
                'sub_fields' => array(
                    array(
                        'key' => 'field_textosemaforo_titulo_paso',
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
                        'parent_repeater' => 'field_textosemaforo_repetidor',
                    ),
                    array(
                        'key' => 'field_textosemaforo_subtitulo_paso',
                        'label' => 'subtitulo_paso',
                        'name' => 'subtitulo_paso',
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
                        'parent_repeater' => 'field_textosemaforo_repetidor',
                    )
                ),
            ),
            array(
                'key' => 'field_textosemaforo_imagen',
                'label' => 'imagen_textosemaforo',
                'name' => 'imagen_textosemaforo',
                'aria-label' => '',
                'type' => 'image',
                'instructions' => 'Imagen para desktop (mayor a 800px)',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'return_format' => 'array',
                'library' => 'all',
                'preview_size' => 'medium',
            ),
            array(
                'key' => 'field_textosemaforo_imagen_mobile',
                'label' => 'imagen_mobile_textosemaforo',
                'name' => 'imagen_mobile_textosemaforo',
                'aria-label' => '',
                'type' => 'image',
                'instructions' => 'Imagen para mobile (800px o menos)',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'return_format' => 'array',
                'library' => 'all',
                'preview_size' => 'medium',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/textosemaforo',
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

function textosemaforo_acf()
{
    acf_register_block_type([
        'name'        => 'textosemaforo',
        'title'        => __('textosemaforo', 'tictac'),
        'description'    => __('Bloque con título, párrafo, semáforo de pasos e imagen responsive', 'tictac'),
        'render_callback'  => 'textosemaforo',
        'mode'        => 'preview',
        'icon'        => 'lightbulb',
        'keywords'      => ['custom', 'textosemaforo', 'semaforo', 'pasos'],
    ]);
}

add_action('acf/init', 'textosemaforo_acf');

function textosemaforo_scripts()
{
    if (!is_admin()) {
        wp_enqueue_style('textosemaforo', get_stylesheet_directory_uri() . '/assets/functions/blocks/textosemaforo/textosemaforo.min.css');
    }
}
add_action('wp_enqueue_scripts', 'textosemaforo_scripts');

function textosemaforo($block)
{
    $titulo = get_field("titulo_textosemaforo");
    $parrafo = get_field("parrafo_textosemaforo");
    $pasos = get_field("repetidor_semaforo");
    $imagen = get_field("imagen_textosemaforo");
    $imagen_mobile = get_field("imagen_mobile_textosemaforo");
    $upload_dir = wp_upload_dir();
?>
    <div class="container textosemaforo">
        <div class="textosemaforo-content">
            <?php if ($titulo): ?>
                <h2 class="textosemaforo-titulo"><?= $titulo ?></h2>
            <?php endif; ?>
            
            <?php if ($parrafo): ?>
                <div class="textosemaforo-parrafo"><?= wpautop($parrafo) ?></div>
            <?php endif; ?>
        </div>

        <div class="textosemaforo-main-content">
            <?php if ($pasos) : ?>
                <div class="textosemaforo-semaforo">
                    <div class="semaforo-contenedor">
                        <?php 
                        $contador = 1;
                        foreach ($pasos as $paso) : 
                        ?>
                            <div class="semaforo-item">
                                <div class="semaforo-textos">
                                    <?php if ($paso['titulo_paso']) : ?>
                                        <p class="semaforo-titulo-paso"><?php echo $paso['titulo_paso']; ?></p>
                                    <?php endif; ?>
                                    <div class="divrayas">
                                        <img class="semaforo-imagen-gif" style="width: 70px;" src="<?php echo $upload_dir['baseurl']; ?>/2025/07/5fffe74ddf3130e7bf4894e67417eb025220228b.gif" alt="">
                                        <?php if ($paso['subtitulo_paso']) : ?>
                                        <p class="semaforo-subtitulo-paso"><?= $paso['subtitulo_paso']; ?></p>
                                    <?php endif; ?>
                                    </div>
                                    
                                </div>
                            </div>
                        <?php 
                        $contador++;
                        endforeach; 
                        ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($imagen || $imagen_mobile): ?>
                <div class="textosemaforo-imagen">
                    <?php if ($imagen): ?>
                        <img class="imagen-desktop" 
                             src="<?php echo esc_url($imagen['url']); ?>" 
                             alt="<?php echo esc_attr($imagen['alt']); ?>">
                    <?php endif; ?>
                    
                    <?php if ($imagen_mobile): ?>
                        <img class="imagen-mobile" 
                             src="<?php echo esc_url($imagen_mobile['url']); ?>" 
                             alt="<?php echo esc_attr($imagen_mobile['alt']); ?>">
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <style>
        .textosemaforo-imagen .imagen-desktop,
        .textosemaforo-imagen .imagen-mobile {
            width: 100%;
            height: auto;
        }
        
        /* Por defecto mostrar imagen desktop */
        .textosemaforo-imagen .imagen-mobile {
            display: none;
        }
        
        /* En móviles (800px o menos) mostrar imagen mobile y ocultar desktop */
        @media (max-width: 800px) {
            .textosemaforo-imagen .imagen-desktop {
                display: none;
            }
            
            .textosemaforo-imagen .imagen-mobile {
                display: block;
                object-fit: contain;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function animateTextosemaforo() {
                const items = document.querySelectorAll('.semaforo-item');
                const imagenes = document.querySelectorAll('.semaforo-imagen-gif');
                const imagenInactiva = '<?php echo $upload_dir['baseurl']; ?>/2025/07/5fffe74ddf3130e7bf4894e67417eb025220228b.gif';
                const imagenActiva = '<?php echo $upload_dir['baseurl']; ?>/2025/07/9a924490e1407d3fa231c192e30945080aee47cb.gif';
                let index = 0;

                function resetItems() {
                    items.forEach((item, i) => {
                        item.classList.remove('active');
                        // Restaurar imagen inactiva
                        if (imagenes[i]) {
                            imagenes[i].src = imagenInactiva;
                        }
                    });
                }

                function activateNext() {
                    if (index < items.length) {
                        // Activar el item actual
                        items[index].classList.add('active');
                        // Cambiar imagen a la activa
                        if (imagenes[index]) {
                            imagenes[index].src = imagenActiva;
                        }
                        index++;
                        setTimeout(activateNext, 2000); // 2 segundos de espera
                    } else {
                        // Esperar 2 segundos con todos activos
                        setTimeout(() => {
                            // Resetear todos los items
                            resetItems();
                            // Esperar otros 2 segundos antes de reiniciar
                            setTimeout(() => {
                                index = 0;
                                activateNext();
                            }, 2000);
                        }, 2000);
                    }
                }

                // Verificar que existan items antes de iniciar
                if (items.length > 0) {
                    activateNext();
                }
            }

            // Ejecutar la animación cuando la página esté lista
            animateTextosemaforo();
        });
    </script>
<?php
}