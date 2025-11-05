<?php
add_action('acf/include_fields', function () {
    if (! function_exists('acf_add_local_field_group')) {
        return;
    }

    acf_add_local_field_group(array(
        'key' => 'group_textoslider_001',
        'title' => 'textoslider',
        'fields' => array(
            array(
                'key' => 'field_textoslider_titulo',
                'label' => 'titulo_textoslider',
                'name' => 'titulo_textoslider',
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
                'key' => 'field_textoslider_contenido',
                'label' => 'contenido_textoslider',
                'name' => 'contenido_textoslider',
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
                'key' => 'field_textoslider_imagenes',
                'label' => 'imagenes_textoslider',
                'name' => 'imagenes_textoslider',
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
                'button_label' => 'Agregar Imagen',
                'rows_per_page' => 20,
                'sub_fields' => array(
                    array(
                        'key' => 'field_textoslider_imagen',
                        'label' => 'imagen',
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
                        'preview_size' => 'medium',
                        'parent_repeater' => 'field_textoslider_imagenes',
                    )
                ),
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/textoslider',
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

function textoslider_acf()
{
    acf_register_block_type([
        'name'        => 'textoslider',
        'title'        => __('textoslider', 'tictac'),
        'description'    => __('Bloque con título, contenido y slider de imágenes', 'tictac'),
        'render_callback'  => 'textoslider',
        'mode'        => 'preview',
        'icon'        => 'images-alt2',
        'keywords'      => ['custom', 'textoslider', 'slider', 'imagenes'],
    ]);
}

add_action('acf/init', 'textoslider_acf');

function textoslider_scripts()
{
    if (!is_admin()) {
        wp_enqueue_style('textoslider', get_stylesheet_directory_uri() . '/assets/functions/blocks/textoslider/textoslider.min.css');
    }
}
add_action('wp_enqueue_scripts', 'textoslider_scripts');

function textoslider($block)
{
    $titulo = get_field("titulo_textoslider");
    $contenido = get_field("contenido_textoslider");
    $imagenes = get_field("imagenes_textoslider");

    if (empty($imagenes)) return;
?>
    <div class="container textoslider">
        <div class="textoslider-content">
            <?php if ($titulo): ?>
                <h1 class="textoslider-titulo"><?= $titulo ?></h1>
            <?php endif; ?>

            <?php if ($contenido): ?>
                <div class="textoslider-texto"><?= $contenido ?></div>
            <?php endif; ?>
        </div>

        <div class="textoslider-slider-container">
            <div class="textoslider-slider">
                <div class="textoslider-track">
                    <?php foreach ($imagenes as $index => $imagen) : ?>
                        <div class="textoslider-slide">
                            <img src="<?php echo esc_url($imagen['imagen']['url']); ?>"
                                alt="<?php echo esc_attr($imagen['imagen']['alt']); ?>">
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="textoslider-pagination">
                <?php
                $total_slides = count($imagenes);
                $slides_per_view = 3;
                // Crear un dot por cada posición posible del slider
                $max_slide_index = max(0, $total_slides - $slides_per_view);

                for ($i = 0; $i <= $max_slide_index; $i++) : ?>
                    <span class="textoslider-dot <?php echo $i === 0 ? 'active' : ''; ?>"
                        data-slide="<?php echo $i; ?>"></span>
                <?php endfor; ?>
            </div>
        </div>
    </div>

    <script>
        jQuery(document).ready(function($) {
            const track = document.querySelector('.textoslider-track');
            const slides = document.querySelectorAll('.textoslider-slide');
            const dots = document.querySelectorAll('.textoslider-dot');
            const slidesPerView = window.innerWidth <= 800 ? 1 : 3;

            const totalSlides = slides.length;
            const maxSlideIndex = totalSlides - slidesPerView; // Máximo índice para mostrar 3 slides
            let currentSlide = 0;

            function showSlide(slideIndex) {
                // Calcular el desplazamiento: cada slide ocupa 33.333% del ancho
                const translateX = -(slideIndex * (100 / slidesPerView));
                track.style.transform = `translateX(${translateX}%)`;

                // Actualizar dots - calcular qué dot corresponde al slide actual
                const activeDot = Math.floor(slideIndex / 1); // Un dot por cada slide
                dots.forEach((dot, index) => {
                    dot.classList.remove('active');
                    if (index === slideIndex) {
                        dot.classList.add('active');
                    }
                });
            }

            function nextSlide() {
                if (currentSlide < maxSlideIndex) {
                    currentSlide++;
                } else {
                    currentSlide = 0; // Volver al inicio
                }
                showSlide(currentSlide);
            }

            // Click en paginación - cada dot representa un slide
            dots.forEach((dot, index) => {
                dot.addEventListener('click', () => {
                    if (index <= maxSlideIndex) {
                        currentSlide = index;
                        showSlide(currentSlide);
                    }
                });
            });

            // Auto-play cada 5 segundos
            if (totalSlides > slidesPerView) {
                setInterval(nextSlide, 5000);
            }
        });
    </script>
<?php
}
