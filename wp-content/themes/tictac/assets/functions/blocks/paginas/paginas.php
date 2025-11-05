<?php
if (function_exists('acf_add_local_field_group')) :

    acf_add_local_field_group(array(
        'key' => 'paginas',
        'title' => 'Paginas',
        'fields' => array(
            array(
                'key' => 'paginas',
                'label' => 'Páginas',
                'name' => 'paginas',
                'type' => 'post_object',
                'post_type' => array('page'),
                'multiple' => true,
                'return_format' => 'object',
                'ui' => 1,
            ),
            array(
                'key' => 'field_129 50a4d9zz45',
                'label' => 'estilo 2',
                'name' => 'estilo2',
                'aria-label' => '',
                'type' => 'true_false',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'message' => '',
                'default_value' => 0,
                'ui' => 0,
                'ui_on_text' => '',
                'ui_off_text' => '',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/paginas',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
    ));

endif;

function paginas_acf()
{
    acf_register_block_type(array(
        'name' => 'paginas',
        'title' => 'Paginas',
        'description' => 'Paginas',
        'category' => 'formatting',
        'icon' => 'star-filled',
        'keywords' => array('paginas', 'acf'),
        'render_callback' => 'paginas',
    ));
}
add_action('acf/init', 'paginas_acf');



// Función para renderizar el bloque
function paginas($block, $content = '', $is_preview = false)
{
    // Obtener las páginas seleccionadas en el bloque
    $paginas = get_field('paginas', $block['id']);
    $estilo2 = get_field("estilo2");
    if ($paginas) {
?>
        <link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/functions/blocks/paginas/paginas.min.css">
        <div class="paginas <?php if($estilo2 == true){echo 'estilo2'; } ?>">
            <div class="container">
                <div class="row d-flex justify-content-center">
                    <?php
                    foreach ($paginas as $pagina) {
                        $imagen = get_the_post_thumbnail_url($pagina->ID);
                        $titulo = $pagina->post_title;
                        $enlace = get_permalink($pagina->ID);
                        $excerpt = get_the_excerpt($pagina->ID);
                    ?>
                        <div class="item col-12 <?php if($estilo2 == true){echo 'col-md-3'; }else{echo 'col-md-4';} ?> mb-4 px-1">
                            <div class="content">
                                <div class="hover">
                                <label class="titulo mb-5"><?= $titulo; ?> </label>
                                <?php if($excerpt){ ?><span><?= $excerpt; ?></span><?php } ?>
                                <a href="<?= $enlace; ?>" class="btn custom yellow">
                                    <?= __("CONSULTAR TARIFAS","custom"); ?>
                                </a>
                                </div>
                                <div class="img"><img src="<?= $imagen; ?>" alt="<?= $titulo; ?>"></div>
                            </div>
                        </div>
                    <?php
                    }
                    ?>
                </div>
            </div>
        </div>
<?php
    }
}
