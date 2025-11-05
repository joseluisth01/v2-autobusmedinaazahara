<?php
add_action('acf/include_fields', function () {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    acf_add_local_field_group(array(
        'key' => 'group_64e5c18d2cf52',
        'title' => 'Logotipos',
        'fields' => array(
            array(
                'key' => 'field_62b33dwfvcs14',
                'label' => 'Titulo logos',
                'name' => 'titulo_logotipos',
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
                'key' => 'field_64e5c18d10b2d',
                'label' => 'Logotipos',
                'name' => 'logotipos',
                'aria-label' => '',
                'type' => 'gallery',
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
                'min' => '',
                'max' => '',
                'min_width' => '',
                'min_height' => '',
                'min_size' => '',
                'max_width' => '',
                'max_height' => '',
                'max_size' => '',
                'mime_types' => '',
                'insert' => 'append',
                'preview_size' => 'medium',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/logotipos',
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


function logotipos_acf()
{
    acf_register_block_type([
        'name'        => 'Logotipos',
        'title'        => __('Logotipos', 'tictac'),
        'description'    => __('Logotipos', 'tictac'),
        'render_callback'  => 'logotipos',
        'mode'        => 'edit',
        'icon'        => 'star-filled',
        'keywords'      => ['logotipos', 'slider', 'bloque', 'custom'],
    ]);
}

add_action('acf/init', 'logotipos_acf');

function logotipos_scripts()
{
    if (!is_admin()) {
        wp_enqueue_style('logotipos', get_stylesheet_directory_uri() . '/assets/functions/blocks/logotipos/logotipos.min.css');
    }
}
add_action('wp_enqueue_scripts', 'logotipos_scripts');

function logotipos()
{

    $images = get_field('logotipos');
    $titulo_logotipos = get_field('titulo_logotipos');
    if ($images) : ?>
        <section id="logotipos" class="logotipos splide container" aria-label="Slider logotipos">
            <h2 class="titulogeneral"><?= $titulo_logotipos?></h2>
            <div class="splide__arrows">
                <button class="splide__arrow splide__arrow--prev">
                    <img class="arrow-custom" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/left.svg" alt="">
                </button>
                <button class="splide__arrow splide__arrow--next">
                    <img class="arrow-custom" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/right.svg" alt="">
                </button>
            </div>
            <div class="splide__track">
                <ul class="splide__list">

                    <?php foreach ($images as $image) : ?>
                        <li class="splide__slide">
                            <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>" />
                            <p><?php echo esc_html($image['caption']); ?></p>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </section>
<?php endif;
}
