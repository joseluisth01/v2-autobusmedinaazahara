<?php
add_action( 'acf/include_fields', function() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group( array(
	'key' => 'group_660d469ec0d9d',
	'title' => 'Link',
	'fields' => array(
		array(
			'key' => 'field_660d469f03588',
			'label' => 'link',
			'name' => 'link',
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
				'value' => 'acf/link',
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



acf_register_block_type([
    'name'        => 'link',
    'title'        => __('link', 'tictac'),
    'description'    => __('link', 'tictac'),
    'render_callback'  => 'link_precio',
    'mode'        => 'preview',
    'icon'        => 'image-filter',
    'keywords'      => ['bloque', 'link'],
]);


function link_precio($block)
{
    $link = get_field("link");
    ?>
    <div class="btn_coleccion">
        <div class="text">
            <?= $link; ?>
        </div>
        <div class="icon">
			<img class="arrow_btn" src="<?= get_stylesheet_directory_uri(); ?>/assets/images/arrow.svg" alt="">
        </div>
    </div>
    <?php
}
