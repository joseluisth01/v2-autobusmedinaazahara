<?php

add_action('acf/include_fields', function () {
    if (! function_exists('acf_add_local_field_group')) {
        return;
    }

    acf_add_local_field_group(array(
        'key' => 'group_66fa4f289e21c',
        'title' => 'bloquedeprueba',
        'fields' => array(
            array(
                'key' => 'field_66fa4f2ac4036',
                'label' => 'titulo_bloqueprueba',
                'name' => 'titulo_bloqueprueba',
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
                'key' => 'field_66fa4f5bc4037',
                'label' => 'subtitulo_bloqueprueba',
                'name' => 'subtitulo_bloqueprueba',
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
                'key' => 'field_62950a2365h47',
                'label' => 'Imagen',
                'name' => 'imagen_bloqueprueba',
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
                'min_width' => '',
                'min_height' => '',
                'min_size' => '',
                'max_width' => '',
                'max_height' => '',
                'max_size' => '',
                'mime_types' => '',
                'preview_size' => 'medium',
            ),
            array(
                'key' => 'field_62950a21245465',
                'label' => 'Imagen',
                'name' => 'imagen2_bloqueprueba',
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
                'min_width' => '',
                'min_height' => '',
                'min_size' => '',
                'max_width' => '',
                'max_height' => '',
                'max_size' => '',
                'mime_types' => '',
                'preview_size' => 'medium',
            ),
            array(
                'key' => 'field_62950a22543',
                'label' => 'Imagen',
                'name' => 'imagen3_bloqueprueba',
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
                'min_width' => '',
                'min_height' => '',
                'min_size' => '',
                'max_width' => '',
                'max_height' => '',
                'max_size' => '',
                'mime_types' => '',
                'preview_size' => 'medium',
            ),
            array(
                'key' => 'field_62950a243563',
                'label' => 'Imagen',
                'name' => 'imagen4_bloqueprueba',
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
                'min_width' => '',
                'min_height' => '',
                'min_size' => '',
                'max_width' => '',
                'max_height' => '',
                'max_size' => '',
                'mime_types' => '',
                'preview_size' => 'medium',
            ),
            array(
                'key' => 'field_62950a3245452',
                'label' => 'Imagen',
                'name' => 'imagen5_bloqueprueba',
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
                'min_width' => '',
                'min_height' => '',
                'min_size' => '',
                'max_width' => '',
                'max_height' => '',
                'max_size' => '',
                'mime_types' => '',
                'preview_size' => 'medium',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/bloqueprueba',
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

acf_register_block_type([
    'name'        => 'bloqueprueba',
    'title'        => __('bloqueprueba', 'tictac'),
    'description'    => __('bloqueprueba', 'tictac'),
    'render_callback'  => 'bloqueprueba',
    'mode'        => 'preview',
    'icon'        => 'star-filled',
    'keywords'      => ['bloque', 'banner'],
]);

function bloqueprueba_scripts()
{
    if (!is_admin()) {
        wp_enqueue_style('bloqueprueba', get_stylesheet_directory_uri() . '/assets/functions/blocks/bloqueprueba/bloqueprueba.min.css');
    }
}
add_action('wp_enqueue_scripts', 'bloqueprueba_scripts');

function bloqueprueba()
{
    $titulo = get_field("titulo_bloqueprueba");
    $subtitulo = get_field("subtitulo_bloqueprueba");
    $imagen1 = get_field("imagen_bloqueprueba");

    $imagen1 = get_field("imagen_bloqueprueba");
    $imagen2 = get_field("imagen2_bloqueprueba");
    $imagen3 = get_field("imagen3_bloqueprueba");
    $imagen4 = get_field("imagen4_bloqueprueba");
    $imagen5 = get_field("imagen5_bloqueprueba");



?>
    <div class="bannerprueba">
        <h1 class="titulo mb-3"><?= $titulo; ?></h1>
        <h2 class="texto"><?= $subtitulo; ?></h2>

        <div class="bloques_bannerprueba">
            <div class="bloque1_bannerprueba">
                <img src="<?php echo esc_url($imagen1['url']); ?>" alt="<?php echo esc_attr($imagen1['alt']); ?>" />
            </div>
            <div class="bloque2_bannerprueba">
                <img class="img2" src="<?php echo esc_url($imagen2['url']); ?>" alt="<?php echo esc_attr($imagen2['alt']); ?>" />
                <img class="img3" src="<?php echo esc_url($imagen3['url']); ?>" alt="<?php echo esc_attr($imagen3['alt']); ?>" />
                <div class="subbloque_bloque2_bannerprueba">
                    <img class="img4" src="<?php echo esc_url($imagen4['url']); ?>" alt="<?php echo esc_attr($imagen4['alt']); ?>" />
                    <img class="img5" style="margin-left:18px" src="<?php echo esc_url($imagen5['url']); ?>" alt="<?php echo esc_attr($imagen5['alt']); ?>" />
                </div>
            </div>
        </div>

        <div class="col-12 text-center" style="">
            <a class="btn_mas_articulos " href="/annica/tienda/"><?= __("VER EN TIENDA ONLINE", "custom"); ?></a>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Primera imagen
            const img1 = document.querySelector('.bloque1_bannerprueba img');

            img1.addEventListener('mouseover', function() {
                this.dataset.originalSrc = this.src;
                this.src = '/annica/wp-content/uploads/2024/10/Moda-Flamenca-1.png'; // Reemplaza con la URL de la imagen de hover
            });

            img1.addEventListener('mouseout', function() {
                this.src = this.dataset.originalSrc;
            });

            // Segunda imagen (img3)
            const img2 = document.querySelector('.img2');

            img2.addEventListener('mouseover', function() {
                this.dataset.originalSrc = this.src;
                this.src = '/annica/wp-content/uploads/2024/11/Ceremonia-3.png'; // Reemplaza con la URL de la imagen de hover para img3
            });

            img2.addEventListener('mouseout', function() {
                this.src = this.dataset.originalSrc;
            });

            // Segunda imagen (img3)
            const img3 = document.querySelector('.img3');

            img3.addEventListener('mouseover', function() {
                this.dataset.originalSrc = this.src;
                this.src = '/annica/wp-content/uploads/2024/11/Comunion-4.png'; // Reemplaza con la URL de la imagen de hover para img3
            });

            img3.addEventListener('mouseout', function() {
                this.src = this.dataset.originalSrc;
            });

            // Segunda imagen (img3)
            const img4 = document.querySelector('.img4');

            img4.addEventListener('mouseover', function() {
                this.dataset.originalSrc = this.src;
                this.src = '/annica/wp-content/uploads/2024/11/Infantil-3.png'; // Reemplaza con la URL de la imagen de hover para img3
            });

            img4.addEventListener('mouseout', function() {
                this.src = this.dataset.originalSrc;
            });
        });

        // Segunda imagen (img3)
        const img5 = document.querySelector('.img5');

        img5.addEventListener('mouseover', function() {
            this.dataset.originalSrc = this.src;
            this.src = '/annica/wp-content/uploads/2024/11/Juguetes-3.png'; // Reemplaza con la URL de la imagen de hover para img3
        });

        img5.addEventListener('mouseout', function() {
            this.src = this.dataset.originalSrc;
        });
    </script>
<?php
}
