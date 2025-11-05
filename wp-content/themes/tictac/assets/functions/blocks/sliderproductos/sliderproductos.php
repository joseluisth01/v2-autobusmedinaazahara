<?php
add_action('acf/include_fields', function () {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    acf_add_local_field_group(array(
        'key' => 'group_sliderproductos',
        'title' => 'SliderProductos',
        'fields' => array(
            array(
                'key' => 'field_sliderproductos_titulo',
                'label' => 'Título',
                'name' => 'titulo_sliderproductos',
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
                'default_value' => 'Vuestros Favoritos y Novedades',
                'maxlength' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
            ),
            array(
                'key' => 'field_sliderproductos_subtitulo',
                'label' => 'Subtítulo',
                'name' => 'subtitulo_sliderproductos',
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
                'default_value' => 'Explora los productos destacados más buscados: calidad, diseños únicos y tendencias irresistibles en nuestra tienda online. ¡No te los pierdas! Encuentra lo que necesitas hoy. ¡Compra ahora y disfrútalos!',
                'maxlength' => '',
                'placeholder' => '',
                'rows' => 3,
                'new_lines' => '',
            ),
            array(
                'key' => 'field_sliderproductos_cantidad',
                'label' => 'Cantidad de productos a mostrar',
                'name' => 'cantidad_sliderproductos',
                'aria-label' => '',
                'type' => 'number',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => 3,
                'min' => 1,
                'max' => 12,
                'step' => 1,
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
            ),
            array(
                'key' => 'field_sliderproductos_texto_boton',
                'label' => 'Texto del botón',
                'name' => 'texto_boton_sliderproductos',
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
                'default_value' => 'VER TODO',
                'maxlength' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
            ),
            array(
                'key' => 'field_sliderproductos_url_tienda',
                'label' => 'URL de la tienda',
                'name' => 'url_tienda_sliderproductos',
                'aria-label' => '',
                'type' => 'url',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '/tienda',
                'placeholder' => '',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/sliderproductos',
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

function sliderproductos_acf()
{
    acf_register_block_type([
        'name' => 'sliderproductos',
        'title' => __('SliderProductos', 'tictac'),
        'description' => __('Bloque para mostrar un slider de productos exactamente igual al diseño original', 'tictac'),
        'render_callback' => 'sliderproductos',
        'mode' => 'preview',
        'icon' => 'cart',
        'keywords' => ['custom', 'sliderproductos', 'productos', 'tienda'],
    ]);
}

add_action('acf/init', 'sliderproductos_acf');

function sliderproductos_scripts()
{
    if (!is_admin()) {
        wp_enqueue_style('sliderproductos', get_stylesheet_directory_uri() . '/assets/functions/blocks/sliderproductos/sliderproductos.min.css');

        // Asegurarse de que jQuery esté cargado
        wp_enqueue_script('jquery');

        // Cargar Splide (si no está ya cargado)
        if (!wp_script_is('splide', 'enqueued')) {
            wp_enqueue_script('splide', 'https://cdn.jsdelivr.net/npm/@splidejs/splide@3/dist/js/splide.min.js', array('jquery'), null, true);
        }
        
        // Añadir script inline propio para este bloque con un nombre único
        wp_add_inline_script(
            'splide',
            'document.addEventListener("DOMContentLoaded", function () {
                // Solo inicializar si el elemento existe en la página
                if (document.querySelector(".sliderproductos-carousel")) {
                    // Usar un nombre único para la instancia del Splide
                    const productosSplide = new Splide(".sliderproductos-carousel", {
                        type: "loop",
                        perPage: 3,
                        perMove: 1,
                        gap: "10px",
                        padding: { left: 0, right: 0 },
                        trimSpace: false,
                        pagination: false,
                        arrows: false,
                        autoplay: true,
                        interval: 4000,
                        pauseOnHover: true,
                        pauseOnFocus: true,
                        speed: 800,
                        easing: "cubic-bezier(0.25, 1, 0.5, 1)",
                        rewind: true,
                        drag: true,
                        snap: true,
                        cloneStatus: true,
                        breakpoints: {
                            1024: {
                                perPage: 3,
                            },
                            768: {
                                perPage: 2,
                            },
                            480: {
                                perPage: 1,
                            }
                        }
                    });

                    productosSplide.mount();

                    // Manejar la navegación manual
                    document.querySelectorAll(".sliderproductos .nav-arrow").forEach(function(arrow) {
                        arrow.addEventListener("click", function(e) {
                            e.preventDefault();
                            if (this.classList.contains("prev")) {
                                productosSplide.go("<");
                            } else {
                                productosSplide.go(">");
                            }
                        });
                    });

                    // Actualizar los puntos de paginación
                    function updatePaginationDots() {
                        const totalSlides = productosSplide.length;
                        const perPage = productosSplide.options.perPage;
                        const totalPages = Math.ceil(totalSlides / perPage);
                        const currentIndex = Math.floor(productosSplide.index / perPage) % totalPages;
                        
                        const dotsContainer = document.querySelector(".sliderproductos .pagination-dots");
                        if (!dotsContainer) return;
                        
                        dotsContainer.innerHTML = "";
                        
                        for (let i = 0; i < totalPages; i++) {
                            const dot = document.createElement("span");
                            dot.className = "dot";
                            if (i === currentIndex) {
                                dot.classList.add("active");
                            }
                            dot.dataset.index = i * perPage;
                            dot.addEventListener("click", function() {
                                productosSplide.go(parseInt(this.dataset.index));
                            });
                            dotsContainer.appendChild(dot);
                        }
                    }

                    // Inicializar puntos
                    updatePaginationDots();

                    // Actualizar puntos cuando cambia la diapositiva
                    productosSplide.on("moved", function() {
                        const totalSlides = productosSplide.length;
                        const perPage = productosSplide.options.perPage;
                        const totalPages = Math.ceil(totalSlides / perPage);
                        const currentIndex = Math.floor(productosSplide.index / perPage) % totalPages;
                        
                        document.querySelectorAll(".sliderproductos .dot").forEach(function(dot, index) {
                            if (index === currentIndex) {
                                dot.classList.add("active");
                            } else {
                                dot.classList.remove("active");
                            }
                        });
                    });
                }
            });',
            'after'
        );
    }
}
add_action('wp_enqueue_scripts', 'sliderproductos_scripts');

function sliderproductos($block)
{
    // Obtener campos ACF
    $titulo = get_field("titulo_sliderproductos") ?: 'Vuestros Favoritos y Novedades';
    $subtitulo = get_field("subtitulo_sliderproductos") ?: 'Explora los productos destacados más buscados: calidad, diseños únicos y tendencias irresistibles en nuestra tienda online. ¡No te los pierdas! Encuentra lo que necesitas hoy. ¡Compra ahora y disfrútalos!';
    $cantidad = get_field("cantidad_sliderproductos") ?: 3;
    $texto_boton = get_field("texto_boton_sliderproductos") ?: 'VER TODO';
    $url_tienda = get_field("url_tienda_sliderproductos") ?: '/tienda';
    $upload_dir = wp_upload_dir();

    // Obtener productos usando WooCommerce
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => $cantidad,
        'post_status' => 'publish',
    );

    $productos = wc_get_products($args);

    if (empty($productos))
        return;
    ?>
    <div class="sliderproductos container">
        <div class="sliderproductos-header">
            <h2 class=""><?php echo esc_html($titulo); ?></h2>
            <div class="slider-nav">
                <button class="nav-arrow prev">
                <img src="<?php echo $upload_dir['baseurl']; ?>/2025/03/Vector-1.svg" alt="">

                </button>
                <button class="nav-arrow next">
                <img src="<?php echo $upload_dir['baseurl']; ?>/2025/03/Vector.svg" alt="">
                </button>
            </div>
        </div>
        <br>

        <div class="sliderproductos-subtitulo">
            <p><?php echo $subtitulo; ?></p>
        </div>
    <br>
        <div class="sliderproductos-carousel splide">
            <div class="splide__track">
                <ul class="splide__list">
                    <?php foreach ($productos as $producto):
                        $precio = $producto->get_price();
                        $precio_formateado = number_format($precio, 0, ',', '.');

                        // Obtener categoría para mostrar en el subtítulo del producto
                        $categorias = wp_get_post_terms($producto->get_id(), 'product_cat');
                        $categoria_texto = !empty($categorias) ? $categorias[0]->name : '';
                        ?>
                        <li class="splide__slide">
                            <div class="producto-card">
                                <div class="producto-imagen">
                                    <?php echo $producto->get_image('medium'); ?>

                                    <div class="producto-acciones">
                                        <a href="<?php echo esc_url(get_permalink($producto->get_id())); ?>"
                                            class="carrito-icon">
                                            <img class="default-icon"
                                                src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/Comprar.svg"
                                                alt="Comprar"
                                                onerror="this.src='<?php echo get_site_url(); ?>/wp-content/uploads/2025/03/Comprar.svg'">
                                            <img class="hover-icon"
                                                src="<?php echo get_site_url(); ?>/wp-content/uploads/2025/03/Comprar-1.svg"
                                                alt="Comprar hover">
                                        </a>
                                        <a href="#" class="favorito-icon"
                                            data-product-id="<?php echo esc_attr($producto->get_id()); ?>">
                                            <img class="default-icon"
                                                src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/Anadir-a-Favoritos.svg"
                                                alt="Favorito"
                                                onerror="this.src='<?php echo get_site_url(); ?>/wp-content/uploads/2025/03/Anadir-a-Favoritos.svg'">
                                            <img class="hover-icon"
                                                src="<?php echo get_site_url(); ?>/wp-content/uploads/2025/03/Anadir-a-Favoritos-1.svg"
                                                alt="Favorito hover">
                                        </a>
                                    </div>
                                </div>

                                <div class="producto-info">
                                    <div>
                                        <p class="producto-nombre"><?php echo esc_html($producto->get_name()); ?></p>
                                        <div class="producto-descripcion">
                                            <?php echo wp_trim_words($producto->get_short_description(), 10, '...'); ?>
                                        </div>
                                    </div>

                                    <div class="producto-precio"><?php echo $precio_formateado; ?> €</div>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <div style="display:flex; justify-content: space-between; align-items: center;">
            <div class="pagination-dots">
                <!-- Los puntos se generarán dinámicamente con JavaScript -->
            </div>

            <div class="sliderproductos-footer">
                <a href="<?php echo esc_url($url_tienda); ?>" class="ver-todo-btn">
                    <?php echo esc_html($texto_boton); ?>
                </a>
            </div>
        </div>
    </div>
    <?php
}