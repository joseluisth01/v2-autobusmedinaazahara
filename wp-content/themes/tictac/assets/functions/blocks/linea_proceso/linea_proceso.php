<?php

add_action( 'acf/include_fields', function() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group( array(
		'key' => 'group_648ffdb7f2164',
		'title' => 'Linea proceso',
		'fields' => array(
			array(
				'key' => 'field_648ffdb8b332e',
				'label' => 'Titulo proceso',
				'name' => 'titulo_proceso',
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
				'key' => 'field_648ffdc7b332f',
				'label' => 'Linea de pasos',
				'name' => 'linea_de_pasos',
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
				'pagination' => 1,
				'rows_per_page' => 1,
				'min' => 0,
				'max' => 0,
				'collapsed' => '',
				'button_label' => 'Agregar Fila',
				'sub_fields' => array(
					array(
						'key' => 'field_648ffdd4b3330',
						'label' => 'Icono del paso',
						'name' => 'icono_del_paso',
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
						'parent_repeater' => 'field_648ffdc7b332f',
					),
					array(
						'key' => 'field_648ffde4b3331',
						'label' => 'Titulo del paso',
						'name' => 'titulo_del_paso',
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
						'parent_repeater' => 'field_648ffdc7b332f',
					),
				),
			),
		),
		'location' => array(
			array(
				array(
					'param' => 'block',
					'operator' => '==',
					'value' => 'acf/lineaprocesopasos',
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



function lineaproceso_acf()
{
    acf_register_block_type([
        'name'        => 'lineaprocesopasos',
        'title'        => __('Linea Proceso', 'tictac'),
        'description'    => __('Linea Proceso', 'tictac'),
        'render_callback'  => 'lineaproceso',
        'mode'        => 'preview',
        'icon'        => 'star-filled',
        'keywords'      => ['custom', 'linea', 'proceso', 'bloque'],
    ]);
}

add_action('acf/init', 'lineaproceso_acf');

function lineaproceso_scripts()
{
    if (!is_admin()) {
        wp_enqueue_style('lineaproceso', get_stylesheet_directory_uri() . '/assets/functions/blocks/linea_proceso/linea_proceso.min.css');
    }
}
add_action('wp_enqueue_scripts', 'lineaproceso_scripts');

function lineaproceso()
{
    $titulo_proceso = get_field("titulo_proceso");
?>
    <div class="lineaproceso my-5 px-lg-5">
        <div class="container">
            <div class="titulo text-center mb-5"><?= $titulo_proceso; ?></div>
            <div class="row d-flex flex-nowrap pe-5">
                <?php
                if (have_rows('linea_de_pasos')) {
                    $counter = 1;
                    while (have_rows('linea_de_pasos')) {
                        the_row();
                        $icono_paso = get_sub_field('icono_del_paso');
                        $titulo_paso = get_sub_field('titulo_del_paso');
                ?>
                        <div class="icono-box order-<?= $counter; ?> col col-lg d-flex flex-wrap mb-3 px-0">
                            <div class="icono_paso col-12 text-center d-flex justify-content-center align-items-end">
                                <img src="<?= $icono_paso['url']; ?>" alt="<?= $icono_paso['alt']; ?>">
                                <div class="step"><?= $counter; ?></div>
                            </div>
                            <div class="titulo-icono h3 col-12 text-center"><?= $titulo_paso; ?></div>
                        </div>
                <?php
                        $counter++;
                    }
                }
                ?>
            </div>
        </div>
    </div>
<?php
}
