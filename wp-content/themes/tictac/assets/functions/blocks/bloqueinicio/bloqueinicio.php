<?php
add_action('acf/include_fields', function () {
    if (! function_exists('acf_add_local_field_group')) {
        return;
    }

    acf_add_local_field_group(array(
        'key' => 'group_6772776f39799',
        'title' => 'bloqueinicio',
        'fields' => array(
            array(
                'key' => 'field_677277703a180',
                'label' => 'titulo_bloqueinicio',
                'name' => 'titulo_bloqueinicio',
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
                'key' => 'field_677277703a181',
                'label' => 'subtitulo_bloqueinicio',
                'name' => 'subtitulo_bloqueinicio',
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
                'key' => 'field_677277703a182',
                'label' => 'descripcion_bloqueinicio',
                'name' => 'descripcion_bloqueinicio',
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
                'key' => 'field_677277a23a183',
                'label' => 'fondos_bloqueinicio',
                'name' => 'fondos_bloqueinicio',
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
                'button_label' => 'Agregar Fondo',
                'rows_per_page' => 20,
                'sub_fields' => array(
                    array(
                        'key' => 'field_677277b73a184',
                        'label' => 'imagen_fondo',
                        'name' => 'imagen_fondo',
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
                        'preview_size' => 'medium',
                        'parent_repeater' => 'field_677277a23a183',
                    )
                ),
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/bloqueinicio',
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

function bloqueinicio_acf()
{
    acf_register_block_type([
        'name'        => 'bloqueinicio',
        'title'        => __('bloqueinicio', 'tictac'),
        'description'    => __('Bloque de inicio con slider de fondos', 'tictac'),
        'render_callback'  => 'bloqueinicio',
        'mode'        => 'preview',
        'icon'        => 'star-filled',
        'keywords'      => ['custom', 'bloqueinicio', 'bloque', 'slider'],
    ]);
}

add_action('acf/init', 'bloqueinicio_acf');

function bloqueinicio_scripts()
{
    if (!is_admin()) {
        wp_enqueue_style('bloqueinicio', get_stylesheet_directory_uri() . '/assets/functions/blocks/bloqueinicio/bloqueinicio.min.css');
    }
}
add_action('wp_enqueue_scripts', 'bloqueinicio_scripts');

function bloqueinicio($block)
{
    $titulo = get_field("titulo_bloqueinicio");
    $subtitulo = get_field("subtitulo_bloqueinicio");
    $descripcion = get_field("descripcion_bloqueinicio");
    $fondos = get_field("fondos_bloqueinicio");
    $upload_dir = wp_upload_dir();

    if (empty($fondos)) return;
?>
    <div class="bloqueinicio">
        <!-- Slides de fondo -->
        <div class="bloqueinicio-slides">
            <?php foreach ($fondos as $index => $fondo) : ?>
                <div class="bloqueinicio-slide <?php echo $index === 0 ? 'active' : ''; ?>"
                    style="background-image: url('<?php echo esc_url($fondo['imagen_fondo']['url']); ?>');">
                    <div class="bloqueinicio-overlay">
                        <div class="bloqueinicio-contenido">
                            <?php if ($titulo): ?>
                                <div class="h1"><?= $titulo ?></div>
                            <?php endif; ?>
                            <?php if ($subtitulo): ?>
                                <div class="h2"><?= $subtitulo ?></div>
                            <?php endif; ?>
                            <?php if ($descripcion): ?>
                                <div class="descripcion"><?= $descripcion ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    </div>

    <script>
        jQuery(document).ready(function($) {
            // Inicialización del slider de fondos
            const slides = document.querySelectorAll('.bloqueinicio-slide');

            if (slides.length > 1) {
                let currentSlide = 0;

                function showSlide(index) {
                    slides.forEach(slide => slide.classList.remove('active'));
                    slides[index].classList.add('active');
                }

                function nextSlide() {
                    currentSlide = (currentSlide + 1) % slides.length;
                    showSlide(currentSlide);
                }

                // Cambio de fondo cada 5 segundos
                setInterval(nextSlide, 5000);
            }
        });
    </script>
<?php
}