<?php
if (function_exists('acf_add_local_field_group')) :

    acf_add_local_field_group(array(
        'key' => 'group_629509fb88sj8',
        'title' => 'Texto y texto',
        'fields' => array(
            array(
                'key' => 'field_62b2344185e4e',
                'label' => 'Titulo 1',
                'name' => 'titulo_texto_y_texto',
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
            array(
                'key' => 'field_62460a019e243',
                'label' => 'Texto',
                'name' => 'texto_texto',
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
                'key' => 'field_6254294185e4e',
                'label' => 'Titulo 1',
                'name' => 'titulo_texto_y_texto_2',
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
            array(
                'key' => 'field_62852a019e243',
                'label' => 'Texto',
                'name' => 'texto_texto_2',
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
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/texto-y-texto',
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
    'name'        => 'Texto y texto',
    'title'        => __('Texto y texto', 'tictac'),
    'description'    => __('Texto y texto', 'tictac'),
    'render_callback'  => 'texto_texto',
    'mode'        => 'preview',
    'icon'        => 'image-filter',
    'keywords'      => ['texto', 'bloque', 'texto'],
]);

function texto_texto($block)
{
    $titulo_texto_y_texto = get_field("titulo_texto_y_texto");
    $texto_texto = get_field("texto_texto");
    $titulo_texto_y_texto_2 = get_field("titulo_texto_y_texto_2");
    $texto_texto_2 = get_field("texto_texto_2");
?>
    <link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/functions/blocks/texto_texto/texto_texto.min.css">
    <div class="<?php if (isset($block['className'])) {
                    echo $block['className'];
                } ?> texto_texto row">
        <div class="col-12 col-md-6 box_texto">
            <div class="content">
                <div class="titulo">
                    <?= $titulo_texto_y_texto; ?>
                </div>
                <div class="texto">
                    <?= $texto_texto; ?>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 box_texto">
        <div class="content">
                <div class="titulo">
                    <?= $titulo_texto_y_texto_2; ?>
                </div>
                <div class="texto">
                    <?= $texto_texto_2; ?>
                </div>
            </div>
        </div>
    </div>

<?php
}
