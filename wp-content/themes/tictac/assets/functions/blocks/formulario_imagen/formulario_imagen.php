<?php

add_action('acf/include_fields', function () {
	if (!function_exists('acf_add_local_field_group')) {
		return;
	}

	acf_add_local_field_group(array(
		'key' => 'group_64e5cb3d3ae29',
		'title' => 'Formulario e imagen',
		'fields' => array(
			array(
				'key' => 'field_64e5cb3d17d37',
				'label' => 'Formulario',
				'name' => 'formulario_imagen',
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
				'key' => 'field_64e5cb5d17d38',
				'label' => 'Titulo',
				'name' => 'titulo_formulario_imagen',
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
				'key' => 'field_64e5cb6717d39',
				'label' => 'Subtitulo',
				'name' => 'subtitulo_formulario_imagen',
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
				'key' => 'field_64e5cb7217d3a',
				'label' => 'Imagen',
				'name' => 'fondo_formulario_imagen',
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
			array(
				'key' => 'video_form333',
				'label' => 'Texto',
				'name' => 'video_formulario',
				'type' => 'text',
				'required' => 1,
			),
		),
		'location' => array(
			array(
				array(
					'param' => 'block',
					'operator' => '==',
					'value' => 'acf/formulario-imagen',
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



acf_register_block_type([
	'name'        => 'formulario_imagen',
	'title'        => __('Formulario e Imagen', 'tictac'),
	'description'    => __('Formulario e Imagen', 'tictac'),
	'render_callback'  => 'formulario_imagen',
	'mode'        => 'edit',
	'icon'        => 'star-filled',
	'keywords'      => ['bloque', 'formulario', 'imagen', 'custom'],
]);

function formulario_imagen_scripts()
{
	if (!is_admin()) {
		wp_enqueue_style('formulario_imagen', get_stylesheet_directory_uri() . '/assets/functions/blocks/formulario_imagen/formulario_imagen.min.css');
	}
}
add_action('wp_enqueue_scripts', 'formulario_imagen_scripts');

function formulario_imagen()
{
	$titulo_formulario_imagen = get_field("titulo_formulario_imagen");
	$subtitulo_formulario_imagen = get_field("subtitulo_formulario_imagen");
	$formulario_imagen = get_field("formulario_imagen");
	$fondo_formulario_imagen = get_field("fondo_formulario_imagen");
	$video_formulario = get_field("video_formulario");
?>
	<div class="fila_formulario_imagen position-relative">
		<div class="row m-0 align-items-center container m-auto">
			<div class="image-box">
				<?php if ($video_formulario) : ?>
					<?= $video_formulario; ?>
				<?php else : ?>
					<img src="<?= $fondo_formulario_imagen["url"]; ?>" alt="<?= $fondo_formulario_imagen["alt"]; ?>">
				<?php endif; ?>
			</div>
				<div class="formulario">
					<?php if ($titulo_formulario_imagen) { ?>
						<div class="col-12 mb-3">
							<div class="titulo text-center text-md-end">
								<?php echo $titulo_formulario_imagen; ?>
							</div>
							<div class="subtitulo text-center text-md-end">
								<?php if ($subtitulo_formulario_imagen) {
									echo $subtitulo_formulario_imagen;
								} ?>
							</div>
						</div>

					<?php } ?>
					<?php
					echo do_shortcode($formulario_imagen);
					?>
				</div>
		</div>
	</div>
<?php
}
