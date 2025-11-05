<?php

add_action('acf/include_fields', function () {
	if (!function_exists('acf_add_local_field_group')) {
		return;
	}

	acf_add_local_field_group(array(
		'key' => 'group_660c27d1cd15f',
		'title' => 'Portfolio home',
		'fields' => array(
			array(
				'key' => 'field_660c27d25cc94',
				'label' => 'Fotos bodas',
				'name' => 'fotos_bodas',
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
			array(
				'key' => 'field_660c27e55cc95',
				'label' => 'Fotos eventos',
				'name' => 'fotos_eventos',
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
			array(
				'key' => 'field_660c27ef5cc96',
				'label' => 'Fotos Corporativas',
				'name' => 'fotos_corporativas',
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
			array(
				'key' => 'field_660c28035cc97',
				'label' => 'Videos bodas',
				'name' => 'videos_bodas',
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
						'key' => 'field_660c28245cc98',
						'label' => 'video boda',
						'name' => 'video_boda',
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
						'parent_repeater' => 'field_660c28035cc97',
					),
				),
			),
			array(
				'key' => 'field_660c282e5cc99',
				'label' => 'Videos eventos',
				'name' => 'videos_eventos',
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
						'key' => 'field_660c282e5cc9a',
						'label' => 'video evento',
						'name' => 'video_evento',
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
						'parent_repeater' => 'field_660c282e5cc99',
					),
				),
			),
			array(
				'key' => 'field_660c28fd5cc9c',
				'label' => 'Videos corporativas',
				'name' => 'videos_corporativas',
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
						'key' => 'field_660c28fd5cc9d',
						'label' => 'video corporativa',
						'name' => 'video_corporativa',
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
						'parent_repeater' => 'field_660c28fd5cc9c',
					),
				),
			),
		),
		'location' => array(
			array(
				array(
					'param' => 'block',
					'operator' => '==',
					'value' => 'acf/portfolio-home',
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
	'name'        => 'Portfolio_home',
	'title'        => __('Portfolio Home', 'tictac'),
	'description'    => __('Portfolio home', 'tictac'),
	'render_callback'  => 'portfolio_home',
	'mode'        => 'preview',
	'icon'        => 'star-filled',
	'keywords'      => ['custom', 'bloque', "Portfolio", "home"],
]);

function portfolio_home_scripts()
{
	if (!is_admin()) {
		wp_enqueue_style('portfolio_home', get_stylesheet_directory_uri() . '/assets/functions/blocks/portfolio_home/portfolio_home.min.css');
		wp_enqueue_script('isotope', get_stylesheet_directory_uri() . '/assets/functions/blocks/portfolio_home/isotope.pkgd.min.js');
		wp_enqueue_style('fancybox', get_stylesheet_directory_uri() . '/assets/functions/blocks/portfolio_home/fancybox.css');
		wp_enqueue_script('fancybox', get_stylesheet_directory_uri() . '/assets/functions/blocks/portfolio_home/fancybox.umd.js');
		wp_enqueue_script('portfolio_home', get_stylesheet_directory_uri() . '/assets/functions/blocks/portfolio_home/portfolio_home.js', array('jquery'), '', true);
	}
}
add_action('wp_enqueue_scripts', 'portfolio_home_scripts');


function portfolio_home()
{
	$bodas = get_field("fotos_bodas");
	$eventos = get_field("fotos_eventos");
	$corporativas = get_field("fotos_corporativas");
	$videos_bodas = get_field('videos_bodas');
	$videos_eventos = get_field('videos_eventos');
	$videos_corporativas = get_field('videos_corporativas');
?>
	<div class="portfolio tabset container text-center">
	<?php if ($videos_bodas) { ?>
		<div class="selector_type row mb-3">
			<div class="fotos-tab col-6 d-flex justify-content-end">
				<span class="active_span"><?= __("Fotos")?></span>
			</div>
			<div class="videos-tab col-6 d-flex justify-content-start">
				<span><?= __("Videos")?></span>
			</div>
		</div>
	<?php }?>
		<div class="fotos">
			<div class="tabs_buttons_gallery">
				<!-- Tab 3 -->
				<div class="box-btn tab1 active">
					<img class="white" src="<?= get_stylesheet_directory_uri(); ?>/assets/images/wired-outline-2237-champagne-flutes-copia.gif" alt="">
					<img class="green" src="<?= get_stylesheet_directory_uri(); ?>/assets/images/wired-outline-2237-champagne-flutes.gif" alt="">
					<input type="radio" name="tabset" id="tab1" aria-controls="bodas" checked>
					<label class="" for="tab1">Bodas</label>
				</div>
				<!-- Tab 2 -->
				<div class="box-btn tab2">
					<img class="white" src="<?= get_stylesheet_directory_uri(); ?>/assets/images/wired-outline-1103-confetti-copia.gif" alt="">
					<img class="green" src="<?= get_stylesheet_directory_uri(); ?>/assets/images/wired-outline-1103-confetti.gif" alt="">
					<input type="radio" name="tabset" id="tab2" aria-controls="eventos">
					<label class="" for="tab2">Eventos</label>
				</div>
				<!-- Tab 1 -->
				<div class="box-btn tab3">
					<img class="white" src="<?= get_stylesheet_directory_uri(); ?>/assets/images/wired-outline-1459-old-shop-copia.gif" alt="">
					<img class="green" src="<?= get_stylesheet_directory_uri(); ?>/assets/images/wired-outline-1459-old-shop.gif" alt="">
					<input type="radio" name="tabset" id="tab3" aria-controls="corporativa">
					<label class="" for="tab3">Corporativa</label>
				</div>
			</div>
			<div class="tab-panels">

				<?php
				echo '<div id="bodas" class="bodas tab-panel text-start" style="display: block;">';
				if ($bodas) {
				?>
					<section class="galeria_fotos px-3" aria-label="Galería">
						<div class="row">
							<?php foreach ($bodas as $image) { ?>
								<div class="col-12 col-md-3">
									<a href="<?php echo esc_url($image['url']); ?>" data-fancybox="gallery">
										<img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>" />
									</a>
								</div>
							<?php } ?>
						</div>
					</section>
					<script>
						Fancybox.bind("[data-fancybox]");
					</script>
				<?php
				}
				echo '</div>';
				?>


				<?php
				echo '<div id="eventos" class="eventos tab-panel text-start">';
				if ($eventos) {
				?>
					<section class="galeria_fotos px-3" aria-label="Galería">
						<div class="row">
							<?php foreach ($eventos as $image) { ?>
								<div class="col-12 col-md-3">
									<a href="<?php echo esc_url($image['url']); ?>" data-fancybox="gallery">
										<img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>" />
									</a>
								</div>
							<?php } ?>
						</div>
					</section>
					<script>
						Fancybox.bind("[data-fancybox]");
					</script>
				<?php
				}
				echo '</div>';
				?>
				<?php
				echo '<div id="corporativa" class="corporativa tab-panel text-start">';
				if ($corporativas) {
				?>
					<section class="galeria_fotos px-3" aria-label="Galería">
						<div class="row">
							<?php foreach ($corporativas as $image) { ?>
								<div class="col-12 col-md-3">
									<a href="<?php echo esc_url($image['url']); ?>" data-fancybox="gallery">
										<img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>" />
									</a>
								</div>
							<?php } ?>
						</div>
					</section>
					<script>
						Fancybox.bind("[data-fancybox]");
					</script>
				<?php
				}
				echo '</div>';
				?>


			</div>
		</div>
		<?php if ($videos_bodas) { ?>
			<div class="videos" style="display:none;">
			<div class="tabs_buttons_gallery">
				<!-- Tab 3 -->
				<div class="box-btn tab4 active">
					<img class="white" src="<?= get_stylesheet_directory_uri(); ?>/assets/images/wired-outline-2237-champagne-flutes-copia.gif" alt="">
					<img class="green" src="<?= get_stylesheet_directory_uri(); ?>/assets/images/wired-outline-2237-champagne-flutes.gif" alt="">
					<input type="radio" name="tabset" id="tab4" aria-controls="bodas" checked>
					<label class="" for="tab1">Bodas</label>
				</div>
				<!-- Tab 2 -->
				<div class="box-btn tab5">
					<img class="white" src="<?= get_stylesheet_directory_uri(); ?>/assets/images/wired-outline-1103-confetti-copia.gif" alt="">
					<img class="green" src="<?= get_stylesheet_directory_uri(); ?>/assets/images/wired-outline-1103-confetti.gif" alt="">
					<input type="radio" name="tabset" id="tab5" aria-controls="eventos">
					<label class="" for="tab2">Eventos</label>
				</div>
				<!-- Tab 1 -->
				<div class="box-btn tab6">
					<img class="white" src="<?= get_stylesheet_directory_uri(); ?>/assets/images/wired-outline-1459-old-shop-copia.gif" alt="">
					<img class="green" src="<?= get_stylesheet_directory_uri(); ?>/assets/images/wired-outline-1459-old-shop.gif" alt="">
					<input type="radio" name="tabset" id="tab6" aria-controls="corporativa">
					<label class="" for="tab3">Corporativa</label>
				</div>
			</div>
			<div class="tab-panels">

				<?php
				echo '<div id="videos-bodas" class="videos-bodas tab-panel text-start" style="display: block;">';
				if ($videos_bodas) {
					echo '<section class="splide bodas">';
					echo '<div class="splide__track">';
					echo '<ul class="splide__list">';
					foreach( $videos_bodas as $row ) {
						$video = $row['video_boda'];
						echo '<li class="splide__slide">';
							echo $video;
						echo '</li>';
					}
					echo '</ul>';
					echo '</div>';
					echo '</section>';
				}
				echo '</div>';
				?>


				<?php
				echo '<div id="videos-eventos" class="videos-eventos tab-panel text-start">';
				if ($videos_eventos) {
					echo '<section class="splide eventos">';
					echo '<div class="splide__track">';
					echo '<ul class="splide__list">';
					foreach( $videos_eventos as $row ) {
						$video = $row['video_evento'];
						echo '<li class="splide__slide">';
							echo $video;
						echo '</li>';
					}
					echo '</ul>';
					echo '</div>';
					echo '</section>';
				}
				echo '</div>';
				?>
				<?php
				echo '<div id="videos-corporativa" class="videos-corporativa tab-panel text-start">';
				if ($videos_corporativas) {
					echo '<section class="splide corporativas">';
					echo '<div class="splide__track">';
					echo '<ul class="splide__list">';
					foreach( $videos_corporativas as $row ) {
						$video = $row['video_corporativa'];
						echo '<li class="splide__slide">';
							echo $video;
						echo '</li>';
					}
					echo '</ul>';
					echo '</div>';
					echo '</section>';
				}
				echo '</div>';
				?>


			</div>
		</div>
		<?php } ?>
	</div>
<?php
}
