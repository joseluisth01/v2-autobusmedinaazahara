<?php
add_action( 'acf/include_fields', function() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group( array(
	'key' => 'group_64e5f47acb878',
	'title' => 'Banner iconos',
	'fields' => array(
		array(
			'key' => 'field_64e5f47b2dfa3',
			'label' => 'Imagen',
			'name' => 'imagen_banner_iconos',
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
			'key' => 'field_64e5f48d2dfa4',
			'label' => 'Iconos',
			'name' => 'iconos',
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
					'key' => 'field_64e5f4a42dfa5',
					'label' => 'Imagen',
					'name' => 'imagen_icono',
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
					'parent_repeater' => 'field_64e5f48d2dfa4',
				),
				array(
					'key' => 'field_64e5f4b42dfa6',
					'label' => 'Titulo',
					'name' => 'titulo_icono',
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
					'parent_repeater' => 'field_64e5f48d2dfa4',
				),
				array(
					'key' => 'field_64e5f4bc2dfa7',
					'label' => 'Descripcion',
					'name' => 'descripcion_icono',
					'aria-label' => '',
					'type' => 'textarea',
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
					'rows' => '',
					'placeholder' => '',
					'new_lines' => '',
					'parent_repeater' => 'field_64e5f48d2dfa4',
				),
			),
		),
	),
	'location' => array(
		array(
			array(
				'param' => 'block',
				'operator' => '==',
				'value' => 'acf/banner-iconos',
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
    'name'        => 'Banner iconos',
    'title'        => __('Banner iconos', 'tictac'),
    'description'    => __('Banner iconos', 'tictac'),
    'render_callback'  => 'banner_iconos',
    'mode'        => 'preview',
    'icon'        => 'image-filter',
    'keywords'      => ['bloque', 'banner', 'iconos'],
]);

function banner_iconos_scripts()
{
  if (!is_admin()) {
    wp_enqueue_style('banner_iconos', get_stylesheet_directory_uri().'/assets/functions/blocks/banner_iconos/banner_iconos.min.css');
  }
}
add_action('wp_enqueue_scripts', 'banner_iconos_scripts');

function banner_iconos()
{
    $imagen_banner_iconos = get_field("imagen_banner_iconos");
?>
    <div class="banner_iconos py-5">
        <img class="fondo" src="<?= $imagen_banner_iconos['url']; ?>" alt="<?= $imagen_banner_iconos['alt']; ?>">
        <div class="container row">
            <?php
            if( have_rows('iconos') ) {
                while( have_rows('iconos') ) {
                    the_row();
                    $imagen_icono = get_sub_field('imagen_icono');
                    $titulo_icono = get_sub_field('titulo_icono');
                    $descripcion_icono = get_sub_field('descripcion_icono');
                    ?>
                    <div class="icono_box mb-3 mb-md-0 col-12 col-md-4 text-center">
                        <div class="icono mb-3"><?php if($imagen_icono){echo "<img alt='".$titulo_icono."' src='".$imagen_icono['url']."'>";} ?></div>
                        <div class="titulo mb-3"><?php if($titulo_icono){ echo $titulo_icono; }  ?></div>
                        <div class="descripcion"><?php if($descripcion_icono){ echo $descripcion_icono; }  ?></div>
                    </div>
                    <?php
                }
            }
            ?>
        </div>
    </div>
<?php
}
