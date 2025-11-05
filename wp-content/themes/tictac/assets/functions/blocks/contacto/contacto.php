<?php
acf_register_block_type([
    'name'        => 'contacto',
    'title'        => __('Contacto', 'tictac'),
    'description'    => __('contacto', 'tictac'),
    'render_callback'  => 'contacto',
    'mode'        => 'preview',
    'icon'        => 'image-filter',
    'keywords'      => ['bloque', 'contacto'],
]);

add_action('acf/include_fields', function () {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    acf_add_local_field_group(array(
        'key' => 'group_65f564249d955',
        'title' => 'contacto',
        'fields' => array(
            array(
                'key' => 'field_65f564246ec69',
                'label' => 'Sedes',
                'name' => 'sedes',
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
                'button_label' => 'Agregar Fila',
                'rows_per_page' => 20,
                'sub_fields' => array(
                    array(
                        'key' => 'field_65f5643f6ec6a',
                        'label' => 'titulo',
                        'name' => 'titulo',
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
                        'parent_repeater' => 'field_65f564246ec69',
                    ),
                    array(
                        'key' => 'field_65f5643f6ec6z',
                        'label' => 'formulario',
                        'name' => 'formulario',
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
                        'parent_repeater' => 'field_65f564246ec69',
                    ),
                    array(
                        'key' => 'field_65f5643f6ec6z',
                        'label' => 'formulario',
                        'name' => 'formulario',
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
                        'parent_repeater' => 'field_65f564246ec69',
                    ),
                    array(
                        'key' => 'field_62950a3e9e24d',
                        'label' => 'Imagen',
                        'name' => 'imagen_bloque',
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
                        'parent_repeater' => 'field_65f564246ec69',
                    ),
                ),
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/contacto',
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


function contacto_scripts()
{
    if (!is_admin()) {
        wp_enqueue_style('contacto', get_stylesheet_directory_uri() . '/assets/functions/blocks/contacto/contacto.min.css');
    }
}
add_action('wp_enqueue_scripts', 'contacto_scripts');



function contacto($block)
{

?>
    <div class="contacto_block">
        <div class="container">
            <div class="tab-buttons row d-flex flex-wrap justify-content-between">
                <?php
                if (have_rows('sedes')) {
                    $c = 0;
                    while (have_rows('sedes')) {
                        the_row();
                        $titulo = get_sub_field('titulo');
                        $direccion = get_sub_field('direccion');
                        $email = get_sub_field('email');
                        $telefono = get_sub_field('telefono');
                        if ($c == 0) {
                ?>
                            <div class="w-auto tab-btn active" content-id="tab-<?= $c; ?>">
                                <div class="titulo"><?= $titulo; ?></div>
                            </div>
                        <?php
                        } else {
                        ?>
                            <div class="w-auto tab-btn" content-id="tab-<?= $c; ?>">
                                <div class="titulo"><?= $titulo; ?></div>
                            </div>
                <?php
                        }
                        $c++;
                    }
                }
                ?>
            </div>
        </div>
        <div class="tab-contents">
            <?php
            if (have_rows('sedes')) {
                $c = 0;
                while (have_rows('sedes')) {
                    the_row();
                    $formulario = get_sub_field('formulario');
                    $imagen_bloque = get_sub_field('imagen_bloque');

                    if ($c == 0) {
            ?>
                        <div class="content show" id="tab-<?= $c; ?>">
                            <?php if ($imagen_bloque) {
                            ?>
                                <img class="bg-contacto" src="<?= $imagen_bloque["url"]; ?>" alt="">
                            <?php
                            } ?>
                            <div class="content-info">
                                <?php if ($formulario) {
                                    echo do_shortcode($formulario);
                                } ?>
                            </div>
                        </div>
                    <?php
                    } else {
                    ?>
                        <div class="content" id="tab-<?= $c; ?>">
                        <?php if ($imagen_bloque) {
                            ?>
                                <img class="bg-contacto" src="<?= $imagen_bloque["url"]; ?>" alt="">
                            <?php
                            } ?>
                            <div class="content-info">
                                <?php if ($formulario) {
                                    echo do_shortcode($formulario);
                                } ?>
                            </div>
                        </div>
            <?php
                    }
                    $c++;
                }
            }
            ?>
        </div>
        <script>
            const tabButtons = document.querySelectorAll('.tab-btn')

            tabButtons.forEach((tab) => {
                tab.addEventListener('click', () => tabClicked(tab))
            })

            function tabClicked(tab) {

                tabButtons.forEach(tab => {
                    tab.classList.remove('active')
                })
                tab.classList.add('active')

                const contents = document.querySelectorAll('.content')

                contents.forEach((content) => {
                    content.classList.remove('show')
                })

                const contentId = tab.getAttribute('content-id')
                const contentSelected = document.getElementById(contentId)

                contentSelected.classList.add('show')
                //console.log(contentId)
            }
        </script>
    </div>
<?php

}
