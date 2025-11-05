<?php
add_action('acf/include_fields', function () {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    acf_add_local_field_group(array(
        'key' => 'group_favoritos',
        'title' => 'Favoritos',
        'fields' => array(
            array(
                'key' => 'field_favoritos_titulo',
                'label' => 'Título',
                'name' => 'titulo_favoritos',
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
                'default_value' => 'Mis Productos Favoritos',
                'maxlength' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
            ),
            array(
                'key' => 'field_favoritos_subtitulo',
                'label' => 'Subtítulo',
                'name' => 'subtitulo_favoritos',
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
                'default_value' => 'Aquí tienes tu selección personal de productos guardados como favoritos. Estos son los diseños que más te han gustado, listos para añadir a tu carrito cuando desees.',
                'maxlength' => '',
                'placeholder' => '',
                'rows' => 3,
                'new_lines' => '',
            ),
            array(
                'key' => 'field_favoritos_mensaje_vacio',
                'label' => 'Mensaje cuando no hay favoritos',
                'name' => 'mensaje_vacio_favoritos',
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
                'default_value' => 'Aún no has añadido productos a tus favoritos. Explora nuestra tienda y haz clic en el icono de corazón para guardar tus productos preferidos.',
                'maxlength' => '',
                'placeholder' => '',
                'rows' => 3,
                'new_lines' => '',
            ),
            array(
                'key' => 'field_favoritos_texto_boton',
                'label' => 'Texto del botón',
                'name' => 'texto_boton_favoritos',
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
                'default_value' => 'IR A LA TIENDA',
                'maxlength' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
            ),
            array(
                'key' => 'field_favoritos_url_tienda',
                'label' => 'URL de la tienda',
                'name' => 'url_tienda_favoritos',
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
                    'value' => 'acf/favoritos',
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

function favoritos_acf()
{
    acf_register_block_type([
        'name' => 'favoritos',
        'title' => __('Favoritos', 'tictac'),
        'description' => __('Bloque para mostrar productos favoritos guardados por el usuario', 'tictac'),
        'render_callback' => 'favoritos',
        'mode' => 'preview',
        'icon' => 'heart',
        'keywords' => ['custom', 'favoritos', 'productos', 'tienda'],
    ]);
}

add_action('acf/init', 'favoritos_acf');

function favoritos_scripts()
{
    if (!is_admin()) {
        wp_enqueue_style('favoritos', get_stylesheet_directory_uri() . '/assets/functions/blocks/favoritos/favoritos.min.css');
        wp_enqueue_script('jquery');
    }
}
add_action('wp_enqueue_scripts', 'favoritos_scripts');

function favoritos($block)
{
    // Obtener campos ACF
    $titulo = get_field("titulo_favoritos") ?: 'Mis Productos Favoritos';
    $subtitulo = get_field("subtitulo_favoritos") ?: 'Aquí tienes tu selección personal de productos guardados como favoritos. Estos son los diseños que más te han gustado, listos para añadir a tu carrito cuando desees.';
    $mensaje_vacio = get_field("mensaje_vacio_favoritos") ?: 'Aún no has añadido productos a tus favoritos. Explora nuestra tienda y haz clic en el icono de corazón para guardar tus productos preferidos.';
    $texto_boton = get_field("texto_boton_favoritos") ?: 'IR A LA TIENDA';
    $url_tienda = get_field("url_tienda_favoritos") ?: '/tienda';

    ?>
    <div class="favoritos container">
        <div class="favoritos-header">
            <h2 class=""><?php echo esc_html($titulo); ?></h2>
        </div>

        <div class="favoritos-subtitulo">
            <p><?php echo $subtitulo; ?></p>
        </div>

        <div id="favoritos-container">
            <!-- Contenido de favoritos se cargará con JavaScript -->
        </div>

        <div class="favoritos-footer">
            <a href="<?php echo esc_url($url_tienda); ?>" class="ver-todo-btn">
                <?php echo esc_html($texto_boton); ?>
            </a>
        </div>
    </div>

    <script>
    jQuery(document).ready(function($) {
        // Función para obtener los favoritos del localStorage
        function getFavorites() {
            var favorites = localStorage.getItem('favorite_products');
            return favorites ? favorites.split(',').map(Number).filter(Boolean) : [];
        }
        
        // Obtener los IDs de productos favoritos
        var favoritos_ids = getFavorites();
        
        // Verificar si hay favoritos
        if (favoritos_ids.length === 0) {
            // Mostrar mensaje de que no hay favoritos
            $('#favoritos-container').html('<div class="favoritos-vacio"><p><?php echo esc_js($mensaje_vacio); ?></p></div>');
        } else {
            // Hacer una solicitud AJAX para obtener los productos
            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'POST',
                data: {
                    action: 'get_favorite_products',
                    product_ids: favoritos_ids.join(',')
                },
                success: function(response) {
                    if (response.success) {
                        // Mostrar los productos en el contenedor
                        $('#favoritos-container').html(response.data.html);
                        
                        // Configurar el evento para eliminar de favoritos
                        $('.remove-favorite').on('click', function(e) {
                            e.preventDefault();
                            var productId = parseInt($(this).data('product-id'));
                            removeFromFavorites(productId);
                        });
                    } else {
                        // Mostrar mensaje de error
                        $('#favoritos-container').html('<div class="favoritos-vacio"><p>Ocurrió un error al cargar tus productos favoritos.</p></div>');
                    }
                },
                error: function() {
                    // Mostrar mensaje de error
                    $('#favoritos-container').html('<div class="favoritos-vacio"><p>Ocurrió un error al cargar tus productos favoritos.</p></div>');
                }
            });
        }
        
        // Función para eliminar un producto de favoritos
        function removeFromFavorites(productId) {
            var favorites = getFavorites();
            var index = favorites.indexOf(productId);
            
            if (index !== -1) {
                // Eliminar ID del array
                favorites.splice(index, 1);
                
                // Guardar en localStorage
                localStorage.setItem('favorite_products', favorites.join(','));
                
                // Eliminar el elemento visualmente
                var $productCard = $('.producto-item').filter(function() {
                    return $(this).find('.favorito-icon').data('product-id') == productId;
                });
                
                $productCard.fadeOut(300, function() {
                    $(this).remove();
                    
                    // Si no quedan productos en la fila, eliminar la fila
                    $('.favoritos-row').each(function() {
                        if ($(this).find('.producto-item:visible').length === 0) {
                            $(this).remove();
                        }
                    });
                    
                    // Si no quedan filas, mostrar mensaje de favoritos vacío
                    if ($('.favoritos-row').length === 0) {
                        $('#favoritos-container').html('<div class="favoritos-vacio"><p><?php echo esc_js($mensaje_vacio); ?></p></div>');
                    }
                });
            }
        }
    });
    </script>
    <?php
}

// Función AJAX para obtener los productos favoritos
function get_favorite_products() {
    if (!isset($_POST['product_ids']) || empty($_POST['product_ids'])) {
        wp_send_json_error(['message' => 'No se proporcionaron IDs de productos']);
        return;
    }
    
    $product_ids = explode(',', $_POST['product_ids']);
    $product_ids = array_map('intval', $product_ids);
    
    // Filtrar IDs no válidos o vacíos
    $product_ids = array_filter($product_ids);
    
    if (empty($product_ids)) {
        wp_send_json_error(['message' => 'IDs de productos no válidos']);
        return;
    }
    
    // Obtener productos usando WooCommerce
    $args = array(
        'post_type' => 'product',
        'post__in' => $product_ids,
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'orderby' => 'post__in' // Mantener el orden de los favoritos
    );
    
    $query = new WP_Query($args);
    $productos_favoritos = [];
    
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $producto_id = get_the_ID();
            $producto = wc_get_product($producto_id);
            if ($producto && $producto->is_visible()) {
                $productos_favoritos[] = $producto;
            }
        }
        wp_reset_postdata();
    }
    
    if (empty($productos_favoritos)) {
        wp_send_json_error(['message' => 'No se encontraron productos con los IDs proporcionados']);
        return;
    }
    
    // Iniciar buffer de salida para capturar el HTML
    ob_start();
    
    // Dividir los productos en filas de 3
    $productos_por_fila = array_chunk($productos_favoritos, 3);
    
    echo '<div class="favoritos-grid">';
    
    foreach ($productos_por_fila as $fila): ?>
        <div class="favoritos-row">
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
                                        src="<?php echo get_site_url(); ?>/wp-content/uploads/2025/03/Comprar.svg"
                                        alt="Comprar">
                                    <img class="hover-icon"
                                        src="<?php echo get_site_url(); ?>/wp-content/uploads/2025/03/Comprar-1.svg"
                                        alt="Comprar hover">
                                </a>
                                <a href="#" class="favorito-icon remove-favorite"
    data-product-id="<?php echo esc_attr($producto->get_id()); ?>">
    <img class="default-icon"
        src="<?php echo get_site_url(); ?>/wp-content/uploads/2025/03/Anadir-a-Favoritos-2.svg"
        alt="Quitar de favoritos">
    <img class="hover-icon"
        src="<?php echo get_site_url(); ?>/wp-content/uploads/2025/03/Anadir-a-Favoritos.svg"
        alt="Quitar de favoritos hover">
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
    <?php endforeach;
    
    echo '</div>';
    
    $html = ob_get_clean();
    
    wp_send_json_success(['html' => $html]);
}
add_action('wp_ajax_get_favorite_products', 'get_favorite_products');
add_action('wp_ajax_nopriv_get_favorite_products', 'get_favorite_products');