<?php 
add_action( 'acf/include_fields', function() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group( array(
	'key' => 'group_67c8234d88f3b',
	'title' => 'bloquecategorias',
	'fields' => array(
		array(
			'key' => 'field_67c8236310e5f',
			'label' => 'repcategorias',
			'name' => 'repcategorias',
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
			'max' => 5, // Límite explícito de 5 elementos
			'collapsed' => '',
			'button_label' => 'Agregar Fila',
			'rows_per_page' => 20,
			'sub_fields' => array(
				array(
					'key' => 'field_67c823a510e60',
					'label' => 'titulo_repcategorias',
					'name' => 'titulo_repcategorias',
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
					'parent_repeater' => 'field_67c8236310e5f',
				),
				array(
					'key' => 'field_67c823b410e61',
					'label' => 'img_repcategorias',
					'name' => 'img_repcategorias',
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
					'parent_repeater' => 'field_67c8236310e5f',
				),
				array(
					'key' => 'field_67c823c610e62',
					'label' => 'boton_repcategoria',
					'name' => 'boton_repcategoria',
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
					'parent_repeater' => 'field_67c8236310e5f',
				),
			),
		),
	),
	'location' => array(
		array(
			array(
				'param' => 'block',
				'operator' => '==',
				'value' => 'acf/bloquecategorias',
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
    'name'        => 'bloquecategorias',
    'title'        => __('bloquecategorias', 'tictac'),
    'description'    => __('bloquecategorias', 'tictac'),
    'render_callback'  => 'bloquecategorias',
    'mode'        => 'preview',
    'icon'        => 'star-filled',
    'keywords'      => ['bloque', 'bloquecategorias'],
]);

function bloquecategorias_scripts()
{
    if (!is_admin()) {
        wp_enqueue_style('bloquecategorias', get_stylesheet_directory_uri() . '/assets/functions/blocks/bloquecategorias/bloquecategorias.min.css');
    }
}
add_action('wp_enqueue_scripts', 'bloquecategorias_scripts');

function bloquecategorias()
{
    $repcategorias = get_field('repcategorias');
    // Limitar a máximo 5 elementos
    if ($repcategorias && count($repcategorias) > 5) {
        $repcategorias = array_slice($repcategorias, 0, 5);
    }
    
    if ($repcategorias) :
?>
    <div class="bloquecategorias">
        <div class="bloquecategorias__container">
            <?php 
            $contador = 0;
            foreach ($repcategorias as $categoria) :
                if ($contador >= 5) break; // Límite de 5 elementos

                $titulo = $categoria['titulo_repcategorias'];
                $imagen = $categoria['img_repcategorias'];
                $boton = $categoria['boton_repcategoria'];
                
                if (!empty($imagen)) :
            ?>
                <div class="bloquecategorias__item">
                    <div class="bloquecategorias__image" style="background-image: url('<?php echo esc_url($imagen['url']); ?>')">
                        <div style="display:flex; align-items:center; flex-direction: column;">
                            <?php if (!empty($titulo)) : ?>
                                <h3 class="bloquecategorias__title"><?php echo esc_html($titulo); ?></h3>
                            <?php endif; ?>
                            
                            <?php if (!empty($boton)) : ?>
                                <a href="<?php echo esc_url($boton['url']); ?>" class="bloquecategorias__button" <?php echo !empty($boton['target']) ? 'target="' . esc_attr($boton['target']) . '"' : ''; ?>>
                                    <?php echo !empty($boton['title']) ? esc_html($boton['title']) : 'VER TODO'; ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php 
                endif;
                $contador++;
            endforeach; 
            ?>
        </div>
    </div>
<?php
    endif;
}