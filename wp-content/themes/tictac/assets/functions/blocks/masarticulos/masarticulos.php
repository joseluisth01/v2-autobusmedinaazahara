<?php
add_action('acf/include_fields', function () {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    acf_add_local_field_group(array(
        'key' => 'group_masarticulos',
        'title' => 'MasArticulos',
        'fields' => array(
            array(
                'key' => 'field_masarticulos_titulo',
                'label' => 'Título',
                'name' => 'titulo_masarticulos',
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
                'key' => 'field_masarticulos_subtitulo',
                'label' => 'Subtítulo',
                'name' => 'subtitulo_masarticulos',
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
                'key' => 'field_masarticulos_texto_boton',
                'label' => 'Texto del botón',
                'name' => 'texto_boton_masarticulos',
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
                'key' => 'field_masarticulos_url_tienda',
                'label' => 'URL de la tienda',
                'name' => 'url_tienda_masarticulos',
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
                    'value' => 'acf/masarticulos',
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

function masarticulos_acf()
{
    acf_register_block_type([
        'name' => 'masarticulos',
        'title' => __('MasArticulos', 'tictac'),
        'description' => __('Bloque para mostrar productos en filas exactamente igual al diseño original', 'tictac'),
        'render_callback' => 'masarticulos',
        'mode' => 'preview',
        'icon' => 'cart',
        'keywords' => ['custom', 'masarticulos', 'productos', 'tienda'],
    ]);
}

add_action('acf/init', 'masarticulos_acf');

function masarticulos_scripts()
{
    if (!is_admin()) {
        wp_enqueue_style('masarticulos', get_stylesheet_directory_uri() . '/assets/functions/blocks/masarticulos/masarticulos.min.css');
        wp_enqueue_script('jquery');
    }
}
add_action('wp_enqueue_scripts', 'masarticulos_scripts');

function masarticulos($block)
{
    // Obtener campos ACF
    $titulo = get_field("titulo_masarticulos") ?: 'Vuestros Favoritos y Novedades';
    $subtitulo = get_field("subtitulo_masarticulos") ?: 'Explora los productos destacados más buscados: calidad, diseños únicos y tendencias irresistibles en nuestra tienda online. ¡No te los pierdas! Encuentra lo que necesitas hoy. ¡Compra ahora y disfrútalos!';
    $texto_boton = get_field("texto_boton_masarticulos") ?: 'VER TODO';
    $url_tienda = get_field("url_tienda_masarticulos") ?: '/tienda';

    // Obtener productos usando WooCommerce - 6 productos en total (2 filas de 3)
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => 6,
        'post_status' => 'publish',
    );

    $productos = wc_get_products($args);

    if (empty($productos))
        return;
    ?>
    <div class="masarticulos container">
        <div class="masarticulos-header">
            <h2 class=""><?php echo esc_html($titulo); ?></h2>
        </div>

        <div class="masarticulos-subtitulo">
            <p><?php echo $subtitulo; ?></p>
        </div>

        <div class="masarticulos-grid">
            <?php 
            // Dividir los productos en dos filas
            $productos_por_fila = array_chunk($productos, 3);
            
            foreach ($productos_por_fila as $fila):
            ?>
            <div class="masarticulos-row">
                <?php foreach ($fila as $producto):
                    $precio = $producto->get_price();
                    $precio_formateado = number_format($precio, 0, ',', '.');

                    // Obtener categoría para mostrar en el subtítulo del producto
                    $categorias = wp_get_post_terms($producto->get_id(), 'product_cat');
                    $categoria_texto = !empty($categorias) ? $categorias[0]->name : '';
                    ?>
                    <div class="producto-item">
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
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="masarticulos-footer">
            <a href="<?php echo esc_url($url_tienda); ?>" class="ver-todo-btn">
                <?php echo esc_html($texto_boton); ?>
            </a>
        </div>
    </div>
    <?php
}