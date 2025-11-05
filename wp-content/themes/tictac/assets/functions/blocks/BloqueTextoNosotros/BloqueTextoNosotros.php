<?php

if (function_exists('acf_add_local_field_group')) :

    acf_add_local_field_group(array(
        'key' => 'group_628e9448qrwvwervvewrvjhb399d8',
        'title' => 'Nosotros',
        'fields' => array(
            array(
                'key' => 'field_64e5cb3sqwrveqWr3vrEQFRVbewaewrvrbwertbrdad17d37',
                'label' => 'TituloNosotros',
                'name' => 'TituloNosotros',
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
                'key' => 'field_64e5cb3sqqrwfgwrveqWr3vrEQFRVbewaewrvrbwertbrdad17d37',
                'label' => 'parrafo1',
                'name' => 'parrafo1',
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
                'key' => 'field_64e5cb3sqwrveqWr3vqw4grEQFRVbewaewrvrbwertbrdad17d37',
                'label' => 'parrafo2',
                'name' => 'parrafo2',
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
                'key' => 'field_64e5cb3seqwrevqWEQFRVbewaewrvrbwertbrdad17d37',
                'label' => 'imgNosotros',
                'name' => 'imgNosotros',
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
                'default_value' => '',
                'maxlength' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/nosotros',
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

function Nosotros_acf()
{
    acf_register_block_type([
        'name'        => 'Nosotros',
        'title'        => __('Nosotros ', 'tictac'),
        'description'    => __('Usado en la home normalmente', 'tictac'),
        'render_callback'  => 'Nosotros',
        'mode'        => 'preview',
        'icon'        => 'star-filled',
        'keywords'      => ['custom', 'Nosotros', 'bloque', 'home'],
    ]);
}

add_action('acf/init', 'Nosotros_acf');

function Nosotros_scripts()
{
    if (!is_admin()) {
        wp_enqueue_style('BloqueNosotros', get_stylesheet_directory_uri() . '/assets/functions/blocks/BloqueTextoNosotros/BloqueTextoNosotros.min.css');
    }
}
add_action('wp_enqueue_scripts', 'Nosotros_scripts');

function Nosotros()
{

    $TituloNosotros = get_field('TituloNosotros');
    $parrafo1 = get_field('parrafo1');
    $parrafo2 = get_field('parrafo2');
    $imgNosotros = get_field('imgNosotros');
?>

    <div class="Nosotros container">
        <h2 class="">
            <?php echo esc_html($TituloNosotros); ?>
        </h2>


        <div class="d-flex justify-content-between columna" style="align-items: center; height:600px">
            <?php if ($parrafo1 || $TituloNosotros) { ?>
                <div class="parrafo">
                    <p>

                        <?= $parrafo1 ?>
                    </p>
                </div>
            <?php } ?>

            <?php if ($parrafo2) { ?>
                <div class="parrafo">
                <img style="border-radius:10px; height: 100%; object-fit:cover" src="<?php echo esc_url($imgNosotros['url']); ?>" alt="<?php echo esc_attr($imgNosotros['alt']); ?>" />
                </div>
            <?php } ?>
        </div>




    </div>


<?php
}
