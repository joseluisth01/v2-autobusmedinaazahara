<?php
add_action('acf/include_fields', function () {
	if (!function_exists('acf_add_local_field_group')) {
		return;
	}

	acf_add_local_field_group(array(
		'key' => 'group_64941f20dbea4',
		'title' => 'Portfolio',
		'fields' => array(
			array(
				'key' => 'field_64941f446e42e',
				'label' => 'Proyectos Agrícolas',
				'name' => 'proyectos_agricolas',
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
				'layout' => 'block',
				'pagination' => 0,
				'min' => 0,
				'max' => 0,
				'collapsed' => '',
				'button_label' => 'Agregar Fila',
				'rows_per_page' => 20,
				'sub_fields' => array(
					array(
						'key' => 'field_6495487e74380',
						'label' => 'Galería',
						'name' => 'galeria_agricola',
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
						'insert' => 'append',
						'preview_size' => 'medium',
						'parent_repeater' => 'field_64941f446e42e',
					),
					array(
						'key' => 'field_6493127e74380',
						'label' => 'Título',
						'name' => 'titulo_agricola',
						'type' => 'text',
						'parent_repeater' => 'field_64941f446e42e',
					),
				),
			),
			array(
				'key' => 'field_649548d474381',
				'label' => 'Proyectos Industrial',
				'name' => 'proyectos_industrial',
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
				'layout' => 'block',
				'pagination' => 0,
				'min' => 0,
				'max' => 0,
				'collapsed' => '',
				'button_label' => 'Agregar Fila',
				'rows_per_page' => 20,
				'sub_fields' => array(
					array(
						'key' => 'field_649548d474382',
						'label' => 'Galería',
						'name' => 'galeria_industrial',
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
						'insert' => 'append',
						'preview_size' => 'medium',
						'parent_repeater' => 'field_649548d474381',
					),
					array(
						'key' => 'field_3563127e74380',
						'label' => 'Título',
						'name' => 'titulo_industrial',
						'type' => 'text',
						'parent_repeater' => 'field_649548d474381',
					),
				),
			),
			array(
				'key' => 'field_6495491874384',
				'label' => 'Proyectos residencial',
				'name' => 'proyectos_residencial',
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
				'layout' => 'block',
				'pagination' => 0,
				'min' => 0,
				'max' => 0,
				'collapsed' => '',
				'button_label' => 'Agregar Fila',
				'rows_per_page' => 20,
				'sub_fields' => array(
					array(
						'key' => 'field_6495495374385',
						'label' => 'Galeria',
						'name' => 'galeria_residencial',
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
						'parent_repeater' => 'field_6495491874384',
					),
					array(
						'key' => 'field_3563127e734580',
						'label' => 'Título',
						'name' => 'titulo_residencial',
						'type' => 'text',
						'parent_repeater' => 'field_6495491874384',
					),
				),
			),
		),
		'location' => array(
			array(
				array(
					'param' => 'block',
					'operator' => '==',
					'value' => 'acf/portfolio',
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
	'name'        => 'Portfolio',
	'title'        => __('Portfolio', 'tictac'),
	'description'    => __('Portfolio', 'tictac'),
	'render_callback'  => 'portfolio',
	'mode'        => 'preview',
	'icon'        => 'star-filled',
	'keywords'      => ['custom', 'bloque', "Portfolio",],
]);

function portfolio_scripts()
{
	if (!is_admin()) {
		wp_enqueue_style('portfolio', get_stylesheet_directory_uri() . '/assets/functions/blocks/portfolio/portfolio.min.css');
		wp_enqueue_script('isotope', get_stylesheet_directory_uri() . '/assets/functions/blocks/portfolio/isotope.pkgd.min.js');
		wp_enqueue_style('fancybox', get_stylesheet_directory_uri() . '/assets/functions/blocks/portfolio/fancybox.css');
		wp_enqueue_script('fancybox', get_stylesheet_directory_uri() . '/assets/functions/blocks/portfolio/fancybox.umd.js');
		wp_enqueue_script('portfolio', get_stylesheet_directory_uri() . '/assets/functions/blocks/portfolio/portfolio.js', array('jquery'), '', true);
	}
}
add_action('wp_enqueue_scripts', 'portfolio_scripts');


function portfolio()
{
	$proyectos_agricolas = get_field("proyectos_agricolas");
	$proyectos_industrial = get_field("proyectos_industrial");
	$proyectos_residencial = get_field("proyectos_residencial");
?>
	<div class="portfolio tabset container text-center">
		<!-- Tab 3 -->
		<input type="radio" name="tabset" id="tab1" aria-controls="residencial" checked>
		<label class="btn custom mb-4" for="tab1">Residencial</label>
		<!-- Tab 2 -->
		<input type="radio" name="tabset" id="tab2" aria-controls="industrial">
		<label class="btn custom mb-4" for="tab2">Industrial</label>
		<!-- Tab 1 -->
		<input type="radio" name="tabset" id="tab3" aria-controls="agricola">
		<label class="btn custom mb-4" for="tab3">Agricola</label>
		<div class="tab-panels">

			<?php
			echo '<div id="residencial" class="residencial tab-panel text-start">';
			$rows_residencial = get_field('proyectos_residencial');
			if ($rows_residencial) {
				foreach ($rows_residencial as $row) {
					$galeria = $row['galeria_residencial'];
					$titulo_galeria = $row['titulo_residencial'];
					$count_residencial = 0;
					if ($galeria) : ?>
						<?php foreach ($galeria as $image) : ?>
							<?php
							$display = "box_portfolio mb-3 me-3";
							if ($count_residencial !== 0) {
								$display = "d-none";
							}
							?>
							<a class="<?= $display; ?>" href="<?php echo esc_url($image['url']); ?>" data-fancybox="residencial" data-caption="<?php echo esc_attr($image['alt']); ?>">
								<img class="" src="<?php echo esc_url($image["sizes"]["large"]); ?>" />
								<div class="h2 px-2 py-2"> <?php if ($titulo_galeria) {
																echo $titulo_galeria;
															} ?> </div>
							</a>
						<?php $count_residencial++;
						endforeach; ?>
			<?php endif;
				}
			}
			echo '</div>';
			?>


			<?php
			$rows_industrial = get_field('proyectos_industrial');
			echo '<div id="industrial" class="industrial tab-panel text-start">';
			if ($rows_industrial) {
				foreach ($rows_industrial as $row) {
					$galeria = $row['galeria_industrial'];
					$titulo_galeria = $row['titulo_industrial'];
					$count_industrial = 0;
					if ($galeria) : ?>
						<?php foreach ($galeria as $image) : ?>
							<?php
							$display = "box_portfolio mb-3 me-3";
							if ($count_industrial !== 0) {
								$display = "d-none";
							}
							?>
							<a class="<?= $display; ?>" href="<?php echo esc_url($image['url']); ?>" data-fancybox="industrial" data-caption="<?php echo esc_attr($image['alt']); ?>">
								<img class="" src="<?php echo esc_url($image["sizes"]["large"]); ?>" />
								<div class="h2 px-2 py-2"> <?php if ($titulo_galeria) {
																echo $titulo_galeria;
															} ?> </div>
							</a>
						<?php $count_industrial++;
						endforeach; ?>
			<?php endif;
				}
			}
			echo '</div>';
			?>
			<?php
			$rows_agricola = get_field('proyectos_agricolas');
			echo '<div id="agricola" class="agricolas tab-panel text-start">';
			if ($rows_agricola) {
				foreach ($rows_agricola as $row) {
					$galeria = $row['galeria_agricola'];
					$titulo_galeria = $row['titulo_agricola'];
					$count_agricola = 0;
					if ($galeria) : ?>
						<?php foreach ($galeria as $image) : ?>
							<?php
							$display = "box_portfolio mb-3 me-3";
							if ($count_agricola !== 0) {
								$display = "d-none";
							}
							?>
							<a class="<?= $display; ?>" href="<?php echo esc_url($image['url']); ?>" data-fancybox="agricola" data-caption="<?php echo esc_attr($image['alt']); ?>">
								<img class="" src="<?php echo esc_url($image["sizes"]["large"]); ?>" />
								<div class="h2 px-2 py-2"> <?php if ($titulo_galeria) {
																echo $titulo_galeria;
															} ?> </div>
							</a>
						<?php $count_agricola++;
						endforeach; ?>
			<?php endif;
				}
			}
			echo '</div>';
			?>


		</div>
	</div>
<?php
}
