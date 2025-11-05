<?php

add_action('acf/include_fields', function () {
	if (!function_exists('acf_add_local_field_group')) {
		return;
	}

	acf_add_local_field_group(array(
		'key' => 'group_64eef02f928b9',
		'title' => 'acordeon',
		'fields' => array(
			array(
				'key' => 'field_64eef032f8a93',
				'label' => 'faqs',
				'name' => 'faqs',
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
						'key' => 'field_64eef049f8a94',
						'label' => 'Titulo',
						'name' => 'titulo_faq',
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
						'parent_repeater' => 'field_64eef032f8a93',
					),
					array(
						'key' => 'field_64eef051f8a95',
						'label' => 'Respuesta faq',
						'name' => 'respuesta_faq',
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
						'parent_repeater' => 'field_64eef032f8a93',
					),
				),
			),
		),
		'location' => array(
			array(
				array(
					'param' => 'block',
					'operator' => '==',
					'value' => 'acf/faqs',
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
	'name'        => 'Faqs',
	'title'        => __('Faqs', 'tictac'),
	'description'    => __('Faqs', 'tictac'),
	'render_callback'  => 'faqs',
	'mode'        => 'preview',
	'icon'        => 'image-filter',
	'keywords'      => ['bloque', 'faqs', 'paginas'],
]);

function faqs_scripts()
{
	if (!is_admin()) {
		wp_enqueue_style('faqs', get_stylesheet_directory_uri() . '/assets/functions/blocks/faqs/faqs.min.css');
		//wp_enqueue_script('faqs', get_stylesheet_directory_uri() . '/assets/functions/blocks/faqs/faqs.js');
	}
}
add_action('wp_enqueue_scripts', 'faqs_scripts');

function faqs($block)
{
	if (have_rows('faqs')) {
		$c = 0;
?>
		<div class="<?php if(isset($block['className'])){ echo $block['className']; } ?> faqs">
			<div class="container">
				<?php
				while (have_rows('faqs')) {
					the_row();
					$titulo_faq = get_sub_field('titulo_faq');
					$respuesta_faq = get_sub_field('respuesta_faq');
				?>
					<div class="row pb-2 mb-3">
						<div class="col-12 titulo_faq <?php if($c == 0){echo 'activo';} ?>">
							<div class="icono">
								<img class="white" src="<?= get_stylesheet_directory_uri(); ?>/assets/images/arrow.svg" alt="">
							</div>
							<?= $titulo_faq; ?>
						</div>
						<div class="col-12 respuesta_faq" <?php if($c == 0){echo 'style="display: block;"';} ?>>
							<div class="content"><?= $respuesta_faq; ?></div>
						</div>
					</div>
				<?php
				$c++;
				}
				?>
			</div>
		</div>
		<script>
			jQuery(document).ready(function(){

if(jQuery(".faqs").length > 0){
  jQuery(".titulo_faq").on( "click", function() {
    jQuery(this).toggleClass("activo");
    jQuery(this).next().slideToggle();
  });
}

});

		</script>
<?php
	}
}
