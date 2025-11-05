<?php

add_action( 'acf/include_fields', function() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group( array(
	'key' => 'group_65d9efb3de105',
	'title' => 'Galeria social',
	'fields' => array(
		array(
			'key' => 'field_65d9efb438e90',
			'label' => 'Galeria social',
			'name' => 'galeria_social',
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
				'value' => 'acf/slider-social',
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
) );
} );



function slider_social_acf()
{
    acf_register_block_type([
        'name'        => 'Slider social',
        'title'        => __('Slider social', 'tictac'),
        'description'    => __('Slider social', 'tictac'),
        'render_callback'  => 'slider_social',
        'mode'        => 'preview',
        'icon'        => 'star-filled',
        'keywords'      => ['custom', 'slider', 'bloque', 'social'],
    ]);
}

add_action('acf/init', 'slider_social_acf');

function slider_social_scripts()
{
    if (!is_admin()) {
        wp_enqueue_style('slider_social', get_stylesheet_directory_uri() . '/assets/functions/blocks/slider_social/slider_social.min.css');
    }
}
add_action('wp_enqueue_scripts', 'slider_social_scripts');

function slider_social()
{
    $phone = get_field("footer_telefono_1", "options");
    $whatsapp = get_field("footer_telefono_2", "options");
    $fax = get_field("fax", "options");
    $email = get_field("footer_email", "options");
    $info = get_field("field_footer_informacion", "options");
    $instagram = get_field("instagram", "options");
    $facebook = get_field("facebook", "options");
    $linkedin = get_field("ln", "options");
    $direccion = get_field("direccion", "options");
?>
    <section class="splide slider_social" aria-label="Slider Principal">
        <div class="splide__track">
            <ul class="splide__list">
                <?php
                $images = get_field('galeria_social');
                if ($images) {
                    $counter = 0;
                    foreach( $images as $imagen_slider ) {
                        the_row();
                ?>
                        <li class="splide__slide">
                            <img class="<?php echo 'banner-'.$counter; ?>" src="<?= $imagen_slider['url']; ?>" alt="<?= $imagen_slider['alt']; ?>">
                        </li>
                <?php
                    $counter++;
                    }
                }
                ?>
            </ul>
        </div>
        <div class="social">
            <?php if ($instagram) { ?>
                <div class="link mx-2"><a href="<?= $instagram['url']; ?>" target="_blank"><span class="icon icon-instagram"></span></a></div>
            <?php } ?>
            <?php if ($facebook) { ?>
                <div class="link mx-2"><a href="<?= $facebook['url']; ?>" target="_blank"><span class="icon icon-facebook"></span></a></div>
            <?php } ?>
            <?php if ($linkedin) { ?>
                <div class="link mx-2"><a href="<?= $linkedin['url']; ?>" target="_blank"><span class="icon icon-linkedin"></span></a></div>
            <?php } ?>
        </div>
    </section>
<?php
}
