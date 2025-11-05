<?php

function titular_acf()
{
    acf_register_block_type([
        'name'        => 'Titular',
        'title'        => __('Titular', 'tictac'),
        'description'    => __('Titular', 'tictac'),
        'render_callback'  => 'titular',
        'mode'        => 'edit',
        'icon'        => 'star-filled',
        'keywords'      => ['custom','seo', 'bloque'],
      ]);
}

add_action( 'acf/include_fields', function() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group( array(
	'key' => 'group_64e5bbb59ad14',
	'title' => 'Titulo y Subtítulo',
	'fields' => array(
		array(
			'key' => 'field_64e5bbb5a3918',
			'label' => 'Título',
			'name' => 'titulo_titular',
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
			'key' => 'field_64e5bbd8a3919',
			'label' => 'Subtitulo titular',
			'name' => 'subtitulo_titular',
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
	),
	'location' => array(
		array(
			array(
				'param' => 'block',
				'operator' => '==',
				'value' => 'acf/titular',
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


add_action('acf/init', 'titular_acf');

function titular_scripts()
{
    if (!is_admin()) {
        wp_enqueue_style('titular', get_stylesheet_directory_uri() . '/assets/functions/blocks/titulo_subtitulo/titulo_subtitulo.min.css');
    }
}
add_action('wp_enqueue_scripts', 'titular_scripts');

function titular($block){
    $titulo_titular = get_field("titulo_titular");
    $subtitulo_titular = get_field("subtitulo_titular");
    ?>
    <div class="titular_block <?php if(isset($block['className'])){ echo $block['className']; } ?>">
        <div class="container">
            <div class="titular">
                <?php if($titulo_titular){echo $titulo_titular;} ?>
            </div>
            <div class="subtitular">
                <?php if($subtitulo_titular){echo $subtitulo_titular;} ?>
            </div>
        </div>
    </div>
    <?php
}