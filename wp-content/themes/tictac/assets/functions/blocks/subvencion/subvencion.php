<?php
add_action('acf/include_fields', function () {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    acf_add_local_field_group(array(
        'key' => 'group_6492c212f1ab1',
        'title' => 'Subvencion',
        'fields' => array(
            array(
                'key' => 'field_6492c21359c16',
                'label' => 'logotipos',
                'name' => 'logotipos_subvencion',
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
                'key' => 'field_6492c23b177d9',
                'label' => 'Texto subvencion',
                'name' => 'texto_subvencion',
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
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/subvencion',
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

add_action( 'acf/include_fields', function() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group( array(
	'key' => 'group_64e5e71d3e276',
	'title' => 'Subvencion',
	'fields' => array(
		array(
			'key' => 'field_64e5e71d1a2de',
			'label' => 'logotipos',
			'name' => 'logotipos_subvencion',
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
			'key' => 'field_64e5e7301a2df',
			'label' => 'Texto',
			'name' => 'texto_subvencion',
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
		),
	),
	'location' => array(
		array(
			array(
				'param' => 'options_page',
				'operator' => '==',
				'value' => 'bloques-footer',
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




function subvencion_acf()
{
    acf_register_block_type([
        'name'        => 'subvencion',
        'title'        => __('Subvencion', 'tictac'),
        'description'    => __('Subvencion', 'tictac'),
        'render_callback'  => 'subvencion',
        'mode'        => 'preview',
        'icon'        => 'star-filled',
        'keywords'      => ['subvencion', 'bloque', 'custom'],
    ]);
}

add_action('acf/init', 'subvencion_acf');

function subvencion_scripts()
{
    if (!is_admin()) {
        wp_enqueue_style('subvencion', get_stylesheet_directory_uri() . '/assets/functions/blocks/subvencion/subvencion.min.css');
    }
}
add_action('wp_enqueue_scripts', 'subvencion_scripts');

function subvencion()
{
    $logotipos = get_field('logotipos_subvencion');
    $size = 'large';
    $texto_subvencion = get_field("texto_subvencion");
?>
    <div class="subvencion py-3 d-flex justify-content-center">
        <div class="container row">
            <div class="col-12 col-md-6 d-flex flex-wrap justify-content-center align-items-center align-content-center">
                <?php if ($logotipos) : ?>
                    <?php foreach ($logotipos as $logotipo) : ?>
                        <img class="mx-2 mb-2" src="<?= $logotipo["sizes"]["medium"]; ?>" alt="<?= $logotipo["alt"]; ?>">
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="col-12 col-md-6 text-center">
                <?= $texto_subvencion; ?>
            </div>
        </div>
    </div>
<?php
}
