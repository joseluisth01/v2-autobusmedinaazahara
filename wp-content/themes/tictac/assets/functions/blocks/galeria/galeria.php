<?php
if (function_exists('acf_add_local_field_group')) :

	acf_add_local_field_group(array(
		'key' => 'group_galeria',
		'title' => 'Galería de Imágenes',
		'fields' => array(
			array(
				'key' => 'field_titulo_galeria',
				'label' => 'Título',
				'name' => 'titulo_galeria',
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
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'maxlength' => '',
			),
			array(
				'key' => 'field_fila_1',
				'label' => 'Fila 1 (1 grande + 4 pequeñas)',
				'name' => 'fila_1',
				'aria-label' => '',
				'type' => 'repeater',
				'instructions' => 'Primera fila con 1 imagen grande a la izquierda y 4 pequeñas a la derecha (máximo 5 imágenes)',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'layout' => 'block',
				'min' => 0,
				'max' => 5,
				'button_label' => 'Añadir Imagen',
				'sub_fields' => array(
					array(
						'key' => 'field_imagen_fila_1',
						'label' => 'Imagen',
						'name' => 'imagen',
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
			),
			array(
				'key' => 'field_fila_2',
				'label' => 'Fila 2 (2 pequeñas + 1 grande + 2 pequeñas)',
				'name' => 'fila_2',
				'aria-label' => '',
				'type' => 'repeater',
				'instructions' => 'Segunda fila con 2 imágenes pequeñas a la izquierda, 1 grande en el centro y 2 pequeñas a la derecha (máximo 5 imágenes)',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'layout' => 'block',
				'min' => 0,
				'max' => 5,
				'button_label' => 'Añadir Imagen',
				'sub_fields' => array(
					array(
						'key' => 'field_imagen_fila_2',
						'label' => 'Imagen',
						'name' => 'imagen',
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
			),
			array(
				'key' => 'field_fila_3',
				'label' => 'Fila 3 (2 arriba + 3 abajo)',
				'name' => 'fila_3',
				'aria-label' => '',
				'type' => 'repeater',
				'instructions' => 'Tercera fila con 2 imágenes arriba y 3 abajo (máximo 5 imágenes)',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'layout' => 'block',
				'min' => 0,
				'max' => 5,
				'button_label' => 'Añadir Imagen',
				'sub_fields' => array(
					array(
						'key' => 'field_imagen_fila_3',
						'label' => 'Imagen',
						'name' => 'imagen',
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
			),
			array(
				'key' => 'field_fila_4',
				'label' => 'Fila 4',
				'name' => 'fila_4',
				'aria-label' => '',
				'type' => 'repeater',
				'instructions' => 'Cuarta fila (máximo 5 imágenes)',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'layout' => 'block',
				'min' => 0,
				'max' => 5,
				'button_label' => 'Añadir Imagen',
				'sub_fields' => array(
					array(
						'key' => 'field_imagen_fila_4',
						'label' => 'Imagen',
						'name' => 'imagen',
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
			),
			array(
				'key' => 'field_fila_5',
				'label' => 'Fila 5',
				'name' => 'fila_5',
				'aria-label' => '',
				'type' => 'repeater',
				'instructions' => 'Quinta fila (máximo 5 imágenes)',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'layout' => 'block',
				'min' => 0,
				'max' => 5,
				'button_label' => 'Añadir Imagen',
				'sub_fields' => array(
					array(
						'key' => 'field_imagen_fila_5',
						'label' => 'Imagen',
						'name' => 'imagen',
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
			),
		),
		'location' => array(
			array(
				array(
					'param' => 'block',
					'operator' => '==',
					'value' => 'acf/galeria',
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

endif;

acf_register_block_type([
	'name'        => 'galeria',
	'title'        => __('Galería', 'tictac'),
	'description'    => __('Bloque de galería con diferentes distribuciones', 'tictac'),
	'render_callback'  => 'galeria_block',
	'mode'        => 'preview',
	'icon'        => 'images-alt2',
	'keywords'      => ['galeria', 'imagenes', 'fotos'],
]);

function galeria_block($block)
{
	$titulo = get_field('titulo_galeria');
	$fila_1 = get_field('fila_1');
	$fila_2 = get_field('fila_2');
	$fila_3 = get_field('fila_3');
	$fila_4 = get_field('fila_4');
	$fila_5 = get_field('fila_5');

	// Clase adicional si viene del bloque
	$clase_adicional = '';
	if (isset($block['className'])) {
		$clase_adicional = $block['className'];
	}
?>
	<link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/functions/blocks/galeria/galeria.min.css">

	<div class="<?php echo $clase_adicional; ?> galeria-block">
		<div class="container">
			<?php if ($titulo) : ?>
				<h2><?php echo $titulo; ?></h2>
			<?php endif; ?>

			<?php if ($fila_1 && count($fila_1) > 0) : ?>
				<div class="galeria-fila fila-1">
					<?php
					// Obtener las imágenes
					$imagenes = array();
					foreach ($fila_1 as $imagen) {
						if ($imagen['imagen']) {
							$imagenes[] = $imagen['imagen'];
						}
					}

					// Imagen grande (primera)
					if (count($imagenes) > 0) : ?>
						<div class="galeria-item grande">
							<div class="imagen-contenedor">
								<img src="<?php echo esc_url($imagenes[0]['url']); ?>" alt="<?php echo esc_attr($imagenes[0]['alt']); ?>" />
							</div>
						</div>
					<?php endif; ?>

					<?php if (count($imagenes) > 1) : ?>
						<div class="galeria-item-grupo">
							<?php
							// Imágenes pequeñas (resto)
							for ($i = 1; $i < count($imagenes) && $i < 5; $i++) : ?>
								<div class="galeria-item pequeno">
									<div class="imagen-contenedor">
										<img src="<?php echo esc_url($imagenes[$i]['url']); ?>" alt="<?php echo esc_attr($imagenes[$i]['alt']); ?>" />
									</div>
								</div>
							<?php endfor; ?>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if ($fila_2 && count($fila_2) > 0) : ?>
				<div class="galeria-fila fila-2">
					<?php
					// Obtener las imágenes
					$imagenes = array();
					foreach ($fila_2 as $imagen) {
						if ($imagen['imagen']) {
							$imagenes[] = $imagen['imagen'];
						}
					}

					// Comprobamos que haya al menos una imagen
					if (count($imagenes) > 0) :
						// Definimos cuántas imágenes hay para cada sección
						$total_imagenes = count($imagenes);
						$izq_count = min(2, $total_imagenes);
						$centro_count = ($total_imagenes > 2) ? 1 : 0;
						$der_count = max(0, min(2, $total_imagenes - $izq_count - $centro_count));
					?>

						<!-- Imágenes pequeñas a la izquierda -->
						<?php if ($izq_count > 0) : ?>
							<div class="galeria-item-grupo izquierda">
								<?php for ($i = 0; $i < $izq_count; $i++) : ?>
									<div class="galeria-item pequeno">
										<div class="imagen-contenedor">
											<img src="<?php echo esc_url($imagenes[$i]['url']); ?>" alt="<?php echo esc_attr($imagenes[$i]['alt']); ?>" />
										</div>
									</div>
								<?php endfor; ?>
							</div>
						<?php endif; ?>

						<!-- Imagen grande del centro -->
						<?php if ($centro_count > 0) : ?>
							<div class="galeria-item centro">
								<div class="imagen-contenedor">
									<img src="<?php echo esc_url($imagenes[$izq_count]['url']); ?>" alt="<?php echo esc_attr($imagenes[$izq_count]['alt']); ?>" />
								</div>
							</div>
						<?php endif; ?>

						<!-- Imágenes pequeñas a la derecha -->
						<?php if ($der_count > 0) : ?>
							<div class="galeria-item-grupo derecha">
								<?php for ($i = 0; $i < $der_count; $i++) : ?>
									<div class="galeria-item pequeno">
										<div class="imagen-contenedor">
											<img src="<?php echo esc_url($imagenes[$izq_count + $centro_count + $i]['url']); ?>" alt="<?php echo esc_attr($imagenes[$izq_count + $centro_count + $i]['alt']); ?>" />
										</div>
									</div>
								<?php endfor; ?>
							</div>
						<?php endif; ?>

					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if ($fila_3 && count($fila_3) > 0) : ?>
				<div class="galeria-fila fila-3">
					<?php
					// Obtener las imágenes
					$imagenes = array();
					foreach ($fila_3 as $imagen) {
						if ($imagen['imagen']) {
							$imagenes[] = $imagen['imagen'];
						}
					}

					// Comprobamos que haya al menos una imagen
					if (count($imagenes) > 0) :
						$total_imagenes = count($imagenes);
						$pequeñas_count = min(4, $total_imagenes - 1);
						$grande_count = ($total_imagenes > 4) ? 1 : 0;

						// Si hay menos de 5 imágenes, ajustamos la distribución
						if ($total_imagenes < 5) {
							if ($total_imagenes == 1) {
								$pequeñas_count = 0;
								$grande_count = 1;
							} else {
								$pequeñas_count = $total_imagenes;
								$grande_count = 0;
							}
						}
					?>

						<!-- Imágenes pequeñas a la izquierda (grid 2x2) -->
						<?php if ($pequeñas_count > 0) : ?>
							<div class="galeria-item-grupo izquierda">
								<?php for ($i = 0; $i < $pequeñas_count; $i++) : ?>
									<div class="galeria-item pequeno">
										<div class="imagen-contenedor">
											<img src="<?php echo esc_url($imagenes[$i]['url']); ?>" alt="<?php echo esc_attr($imagenes[$i]['alt']); ?>" />
										</div>
									</div>
								<?php endfor; ?>
							</div>
						<?php endif; ?>

						<!-- Imagen grande a la derecha -->
						<?php if ($grande_count > 0) : ?>
							<div class="galeria-item grande">
								<div class="imagen-contenedor">
									<img src="<?php echo esc_url($imagenes[$pequeñas_count]['url']); ?>" alt="<?php echo esc_attr($imagenes[$pequeñas_count]['alt']); ?>" />
								</div>
							</div>
						<?php endif; ?>

					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if ($fila_4 && count($fila_4) > 0) : ?>
				<div class="galeria-fila fila-4">
					<?php
					// Obtener las imágenes
					$imagenes = array();
					foreach ($fila_4 as $imagen) {
						if ($imagen['imagen']) {
							$imagenes[] = $imagen['imagen'];
						}
					}

					// Comprobamos que haya al menos una imagen
					if (count($imagenes) > 0) :
						$total_imagenes = count($imagenes);
						$izq_count = min(2, $total_imagenes);
						$centro_count = ($total_imagenes > 2) ? 1 : 0;
						$der_count = max(0, min(2, $total_imagenes - $izq_count - $centro_count));
					?>

						<!-- Imágenes pequeñas a la izquierda -->
						<?php if ($izq_count > 0) : ?>
							<div class="galeria-item-grupo izquierda">
								<?php for ($i = 0; $i < $izq_count; $i++) : ?>
									<div class="galeria-item pequeno">
										<div class="imagen-contenedor">
											<img src="<?php echo esc_url($imagenes[$i]['url']); ?>" alt="<?php echo esc_attr($imagenes[$i]['alt']); ?>" />
										</div>
									</div>
								<?php endfor; ?>
							</div>
						<?php endif; ?>

						<!-- Imagen grande del centro -->
						<?php if ($centro_count > 0) : ?>
							<div class="galeria-item centro">
								<div class="imagen-contenedor">
									<img src="<?php echo esc_url($imagenes[$izq_count]['url']); ?>" alt="<?php echo esc_attr($imagenes[$izq_count]['alt']); ?>" />
								</div>
							</div>
						<?php endif; ?>

						<!-- Imágenes pequeñas a la derecha -->
						<?php if ($der_count > 0) : ?>
							<div class="galeria-item-grupo derecha">
								<?php for ($i = 0; $i < $der_count; $i++) : ?>
									<div class="galeria-item pequeno">
										<div class="imagen-contenedor">
											<img src="<?php echo esc_url($imagenes[$izq_count + $centro_count + $i]['url']); ?>" alt="<?php echo esc_attr($imagenes[$izq_count + $centro_count + $i]['alt']); ?>" />
										</div>
									</div>
								<?php endfor; ?>
							</div>
						<?php endif; ?>

					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if ($fila_5 && count($fila_5) > 0) : ?>
    <div class="galeria-fila fila-5">
        <?php 
        // Obtener las imágenes
        $imagenes = array();
        foreach ($fila_5 as $imagen) {
            if ($imagen['imagen']) {
                $imagenes[] = $imagen['imagen'];
            }
        }
        
        // Comprobamos que haya al menos una imagen
        if (count($imagenes) > 0) :
            $total_imagenes = count($imagenes);
            $grande_count = ($total_imagenes > 0) ? 1 : 0;
            $pequeñas_count = min(4, $total_imagenes - $grande_count);
            
            // Si hay menos de 5 imágenes, ajustamos la distribución
            if ($total_imagenes < 5) {
                if ($total_imagenes == 1) {
                    $grande_count = 1;
                    $pequeñas_count = 0;
                } else {
                    // Primero la grande, luego las pequeñas
                    $grande_count = 1;
                    $pequeñas_count = $total_imagenes - 1;
                }
            }
        ?>
            
            <!-- Imagen grande a la izquierda -->
            <?php if ($grande_count > 0) : ?>
                <div class="galeria-item grande">
                    <div class="imagen-contenedor">
                        <img src="<?php echo esc_url($imagenes[0]['url']); ?>" alt="<?php echo esc_attr($imagenes[0]['alt']); ?>" />
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Imágenes pequeñas a la derecha (grid 2x2) -->
            <?php if ($pequeñas_count > 0) : ?>
                <div class="galeria-item-grupo derecha">
                    <?php for ($i = 0; $i < $pequeñas_count; $i++) : ?>
                        <div class="galeria-item pequeno">
                            <div class="imagen-contenedor">
                                <img src="<?php echo esc_url($imagenes[$grande_count + $i]['url']); ?>" alt="<?php echo esc_attr($imagenes[$grande_count + $i]['alt']); ?>" />
                            </div>
                        </div>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        
        <?php endif; ?>
    </div>
<?php endif; ?>
		</div>
	</div>
<?php
}
?>