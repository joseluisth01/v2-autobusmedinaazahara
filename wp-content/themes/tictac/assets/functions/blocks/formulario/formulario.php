<?php
if (function_exists('acf_add_local_field_group')) :

    acf_add_local_field_group(array(
        'key' => 'group_62b1e5205bbab',
        'title' => 'Formulario',
        'fields' => array(
            array(
                'key' => 'field_titulo_formulario',
                'label' => 'Titulo Formulario',
                'name' => 'titulo_formulario',
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
                'key' => 'field_texto_formulario',
                'label' => 'Texto Formulario',
                'name' => 'texto_formulario',
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
                'maxlength' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
            ),
            array(
                'key' => 'field_62b1e5247bd2c',
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
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'maxlength' => '',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/formulario',
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

endif;

acf_register_block_type([
    'name'        => 'Formulario',
    'title'        => __('Formulario', 'tictac'),
    'description'    => __('Formulario', 'tictac'),
    'render_callback'  => 'formulario',
    'mode'        => 'preview',
    'icon'        => 'star-filled',
    'keywords'      => ['bloque', 'formulario'],
]);

function formulario_scripts()
{
    if (!is_admin()) {
        wp_enqueue_style('formulario', get_stylesheet_directory_uri() . '/assets/functions/blocks/formulario/formulario.min.css');
    }
}
add_action('wp_enqueue_scripts', 'formulario_scripts');

function formulario()
{
    $titulo_formulario = get_field("titulo_formulario");
    $texto_formulario = get_field("texto_formulario");
    $formulario = get_field("formulario");
?>
    <div class="fila_formulario py-5 px-3">
        <div class="content container p-3 px-md-5 py-md-4">
            <div class="row">
                <div class="form-box col-12 col-lg-12">
                    <div class="titulo text-center justify-content-center h1 mb-3"><?= $titulo_formulario; ?></div>
                    <div class="texto"><?= $texto_formulario; ?></div>
                    <?php echo do_shortcode($formulario); ?>
                </div>
            </div>
        </div>
    </div>
<?php
}
