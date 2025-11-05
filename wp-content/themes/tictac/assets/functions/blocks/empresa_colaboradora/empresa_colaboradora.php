<?php

add_action( 'acf/include_fields', function() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group( array(
	'key' => 'group_65f42a8e6faa8',
	'title' => 'Empresa colaboradora',
	'fields' => array(
		array(
			'key' => 'field_65f42a8e2ce3b',
			'label' => 'Titulo',
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
		),
		array(
			'key' => 'field_65f42b512ce3c',
			'label' => 'Logo',
			'name' => 'logo',
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
				'value' => 'acf/empresa-colaboradora',
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




function colabora_acf()
{
    acf_register_block_type([
        'name'        => 'empresa_colaboradora',
        'title'        => __('Empresa colaboradora', 'tictac'),
        'description'    => __('Empresa colaboradora', 'tictac'),
        'render_callback'  => 'empresa_colaboradora',
        'mode'        => 'edit',
        'icon'        => 'star-filled',
        'keywords'      => ['custom','empresa', 'bloque'],
      ]);
}

add_action('acf/init', 'colabora_acf');

function empresa_colaboradora_scripts()
{
    if (!is_admin()) {
        wp_enqueue_style('empresa_colaboradora', get_stylesheet_directory_uri() . '/assets/functions/blocks/empresa_colaboradora/empresa_colaboradora.min.css');
    }
}
add_action('wp_enqueue_scripts', 'empresa_colaboradora_scripts');

function empresa_colaboradora($block){
    $titulo = get_field("titulo");
    $logo = get_field("logo");
    ?>
    <div class="empresa_colaboradora <?php if(isset($block['className'])){ echo $block['className']; } ?>">
        <div class="row d-flex align-items-center justify-content-center">
            <div class="col-8 titulo_colabora text-end pe-4">
                <?php if($titulo){echo $titulo;} ?>
            </div>
            <div class="col-4 logo">
                <?php if($logo){echo "<img src='".$logo["url"]."'>";} ?>
            </div>
        </div>
    </div>
    <?php
}