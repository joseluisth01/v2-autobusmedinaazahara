<?php

add_action( 'acf/include_fields', function() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group( array(
	'key' => 'group_6613a0bd5c44c',
	'title' => 'Links personalizados',
	'fields' => array(
		array(
			'key' => 'field_6613a0bd3d2c7',
			'label' => 'Links personalizados',
			'name' => 'links_personalizados',
			'aria-label' => '',
			'type' => 'repeater',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'layout' => 'table',
			'pagination' => 0,
			'min' => 0,
			'max' => 0,
			'collapsed' => '',
			'button_label' => 'Agregar Fila',
			'rows_per_page' => 20,
			'sub_fields' => array(
				array(
					'key' => 'field_6613a0cf3d2c8',
					'label' => 'Imagen 1',
					'name' => 'imagen_1',
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
					'parent_repeater' => 'field_6613a0bd3d2c7',
				),
				array(
					'key' => 'field_6613a0d73d2c9',
					'label' => 'Imagen 2',
					'name' => 'imagen_2',
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
					'parent_repeater' => 'field_6613a0bd3d2c7',
				),
				array(
					'key' => 'field_6613a0da3d2ca',
					'label' => 'Link',
					'name' => 'link',
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
					'parent_repeater' => 'field_6613a0bd3d2c7',
				),
				array(
					'key' => 'field_6613a0ee3d2cb',
					'label' => 'Título',
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
					'parent_repeater' => 'field_6613a0bd3d2c7',
				),
			),
		),
	),
	'location' => array(
		array(
			array(
				'param' => 'block',
				'operator' => '==',
				'value' => 'acf/links-personalizados',
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
    'name'        => 'links_personalizados',
    'title'        => __('links personalizados', 'tictac'),
    'description'    => __('links personalizados', 'tictac'),
    'render_callback'  => 'links_personalizados',
    'mode'        => 'preview',
    'icon'        => 'image-filter',
    'keywords'      => ['bloque', 'links', 'personalizados'],
]);

function links_personalizados_scripts()
{
	if (!is_admin()) {
		wp_enqueue_style('links_personalizados', get_stylesheet_directory_uri() . '/assets/functions/blocks/links_personalizados/links_personalizados.min.css');
	}
}
add_action('wp_enqueue_scripts', 'links_personalizados_scripts');


function links_personalizados($block)
{
	$rows = get_field('links_personalizados');
	
	if( $rows ) {
		?>
		<div class="links_personalizados <?php if (isset($block['className'])) {
                    echo $block['className'];
                } ?>">
		<?php
		echo '<div class="container">';
		echo '<div class="row">';
		foreach( $rows as $row ) {
			$imagen_1 = $row['imagen_1'];
			$imagen_2 = $row['imagen_2'];
			$link = $row['link'];
			$titulo = $row['titulo'];
			echo '<div class="link_personalizado col-12 col-md-3 mb-3">';
			?>
			<div class="content">
				<img class="imagen1" src="<?= $imagen_1["url"]; ?>" alt="<?= $imagen_1["alt"]; ?>">
				<img class="imagen2" src="<?= $imagen_2["url"]; ?>" alt="<?= $imagen_2["alt"]; ?>">
				<div class="title"><?= $titulo; ?></div>
			</div>
			<a class="btn custom purple mt-3" href="<?= $link["url"]; ?>" target="<?= $link["target"]; ?>"><?= $link["title"]; ?></a>
			<?php
			echo '</div>';
		}
		echo '</div>';
		echo '</div>';
		echo '</div>';
	}

}
