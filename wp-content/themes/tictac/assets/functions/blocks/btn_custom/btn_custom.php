<?php
add_action( 'acf/include_fields', function() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group( array(
	'key' => 'group_660d4bfb0a480',
	'title' => 'link custom',
	'fields' => array(
		array(
			'key' => 'field_660d4bfbf50d4',
			'label' => 'btn_custom',
			'name' => 'btn_custom',
			'aria-label' => '',
			'type' => 'link',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'return_format' => 'array',
		),
	),
	'location' => array(
		array(
			array(
				'param' => 'block',
				'operator' => '==',
				'value' => 'acf/link-custom',
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
    'name'        => 'link_custom',
    'title'        => __('link_custom', 'tictac'),
    'description'    => __('link_custom', 'tictac'),
    'render_callback'  => 'link_custom',
    'mode'        => 'preview',
    'icon'        => 'image-filter',
    'keywords'      => ['bloque', 'link', 'custom'],
]);


function link_custom($block)
{
    $btn_custom = get_field("btn_custom");
    ?>
    <a href="<?= $btn_custom["url"]; ?>" class="btn custom  <?php if(isset($block['className'])){ echo $block['className']; } ?>">
		<?= $btn_custom["title"]; ?>
	</a>
    <?php
}
