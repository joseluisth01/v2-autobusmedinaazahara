<?php
add_action( 'acf/include_fields', function() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group( array(
		'key' => 'group_64917f963c352',
		'title' => 'Iconos horizontal',
		'fields' => array(
			array(
				'key' => 'field_64917fb234dd8',
				'label' => 'Iconos',
				'name' => 'repeater_iconos_horizontal',
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
						'key' => 'field_64917f9634dd6',
						'label' => 'Icono',
						'name' => 'icono_horizontal',
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
						'parent_repeater' => 'field_64917fb234dd8',
					),
					array(
						'key' => 'field_64917fa434dd7',
						'label' => 'Titulo',
						'name' => 'titulo_horizontal',
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
						'parent_repeater' => 'field_64917fb234dd8',
					),
				),
			),
		),
		'location' => array(
			array(
				array(
					'param' => 'block',
					'operator' => '==',
					'value' => 'acf/iconos-horizontal',
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



function iconos_horizontal_acf()
{
    acf_register_block_type([
        'name'        => 'iconos_horizontal',
        'title'        => __('Iconos horizontal', 'tictac'),
        'description'    => __('Iconos horizontal', 'tictac'),
        'render_callback'  => 'iconos_horizontal',
        'mode'        => 'preview',
        'icon'        => 'star-filled',
        'keywords'      => ['custom', 'iconos', 'bloque'],
    ]);
}

add_action('acf/init', 'iconos_horizontal_acf');

function iconos_horizontal_scripts()
{
    if (!is_admin()) {
        wp_enqueue_style('iconos_horizontal', get_stylesheet_directory_uri() . '/assets/functions/blocks/fila_iconos_horizontal/fila_iconos_horizontal.min.css');
    }
}
add_action('wp_enqueue_scripts', 'iconos_horizontal_scripts');

function iconos_horizontal()
{
?>
    <div class="fila_iconos_horizontal">
        <div class="container">
            <div class="row justify-content-between align-content-center align-items-center">
                <?php
                if (have_rows('repeater_iconos_horizontal')) {
                    while (have_rows('repeater_iconos_horizontal')) {
                        the_row();
                        $icono = get_sub_field('icono_horizontal');
                        $titulo = get_sub_field('titulo_horizontal');
                ?>
                <div class="row-icono col-12 col-md-2 mb-5 mb-md-0 d-flex align-content-center align-items-center">
                    <div class="icono text-start d-flex justify-content-center"><img src="<?= $icono["url"]; ?>" alt="<?= $icono["alt"]; ?>"></div>
                    <div class="titulo_icono text-center"><?= $titulo; ?></div>
                </div>
                <?php
                    }
                }
                ?>
            </div>
        </div>
    </div>
<?php
}
