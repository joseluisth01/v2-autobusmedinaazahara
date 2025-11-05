<?php
add_action('acf/include_fields', function () {
    if (! function_exists('acf_add_local_field_group')) {
        return;
    }

    acf_add_local_field_group(array(
        'key' => 'group_slidergrande_001',
        'title' => 'slidergrande',
        'fields' => array(
            array(
                'key' => 'field_slidergrande_imagenes',
                'label' => 'imagenes_slidergrande',
                'name' => 'imagenes_slidergrande',
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
                        'key' => 'field_slidergrande_imagen',
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
                        'parent_repeater' => 'field_slidergrande_imagenes',
                    )
                ),
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/slidergrande',
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

function slidergrande_acf()
{
    acf_register_block_type([
        'name'        => 'slidergrande',
        'title'        => __('slidergrande', 'tictac'),
        'description'    => __('Slider de imágenes a ancho completo', 'tictac'),
        'render_callback'  => 'slidergrande',
        'mode'        => 'preview',
        'icon'        => 'format-gallery',
        'keywords'      => ['custom', 'slidergrande', 'slider', 'imagenes', 'fullwidth'],
    ]);
}

add_action('acf/init', 'slidergrande_acf');

function slidergrande_scripts()
{
    if (!is_admin()) {
        wp_enqueue_style('slidergrande', get_stylesheet_directory_uri() . '/assets/functions/blocks/slidergrande/slidergrande.min.css');
    }
}
add_action('wp_enqueue_scripts', 'slidergrande_scripts');

function slidergrande($block)
{
    $imagenes = get_field("imagenes_slidergrande");

    if (empty($imagenes)) return;
?>
    <div class="slidergrande">
        <div class="slidergrande-slider-container">
            <div class="slidergrande-slider">
                <div class="slidergrande-track">
                    <?php foreach ($imagenes as $index => $imagen) : ?>
                        <div class="slidergrande-slide">
                            <img src="<?php echo esc_url($imagen['imagen']['url']); ?>" 
                                 alt="<?php echo esc_attr($imagen['imagen']['alt']); ?>">
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="slidergrande-pagination">
                <?php 
                $total_slides = count($imagenes);
                $slides_per_view = 3;
                // Crear un dot por cada posición posible del slider
                $max_slide_index = max(0, $total_slides - $slides_per_view);
                
                for ($i = 0; $i <= $max_slide_index; $i++) : ?>
                    <span class="slidergrande-dot <?php echo $i === 0 ? 'active' : ''; ?>" 
                          data-slide="<?php echo $i; ?>"></span>
                <?php endfor; ?>
            </div>
        </div>
    </div>

    <style>

    </style>

    <script>
        jQuery(document).ready(function($) {
            const track = document.querySelector('.slidergrande-track');
            const slides = document.querySelectorAll('.slidergrande-slide');
            const dots = document.querySelectorAll('.slidergrande-dot');
            let slidesPerView = window.innerWidth <= 800 ? 1 : 3;

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