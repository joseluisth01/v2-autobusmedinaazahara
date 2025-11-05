<?php 
add_action( 'acf/include_fields', function() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group( array(
	'key' => 'group_67cf14c8e8cb8',
	'title' => 'bloquenewsletter',
	'fields' => array(
		array(
			'key' => 'field_67cf14c92845c',
			'label' => 'titulo_bloquenewsletter',
			'name' => 'titulo_bloquenewsletter',
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
			'key' => 'field_67cf14d92845d',
			'label' => 'parrafo_bloquenewsletter',
			'name' => 'parrafo_bloquenewsletter',
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
			'key' => 'field_67cf15002845e',
			'label' => 'shortcode_formulariocontactform7',
			'name' => 'shortcode_formulariocontactform7',
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
	),
	'location' => array(
		array(
			array(
				'param' => 'block',
				'operator' => '==',
				'value' => 'acf/bloquenewsletter',
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

function bloquenewsletter_acf()
{
  acf_register_block_type([
    'name'        => 'bloquenewsletter',
    'title'        => __('bloquenewsletter', 'tictac'),
    'description'    => __('bloquenewsletter', 'tictac'),
    'render_callback'  => 'bloquenewsletter',
    'mode'        => 'preview',
    'icon'        => 'star-filled',
    'keywords'      => ['custom', 'seo', 'bloque'],
  ]);
}

add_action('acf/init', 'bloquenewsletter_acf');

function bloquenewsletter_scripts()
{
  if (!is_admin()) {
    wp_enqueue_style('bloquenewsletter', get_stylesheet_directory_uri() . '/assets/functions/blocks/bloquenewsletter/bloquenewsletter.min.css');
  }
}
add_action('wp_enqueue_scripts', 'bloquenewsletter_scripts');

function bloquenewsletter()
{
  $titulo = get_field('titulo_bloquenewsletter');
  $parrafo = get_field('parrafo_bloquenewsletter');
  $shortcode = get_field('shortcode_formulariocontactform7');
  
  ?>
  <div class="indomita-newsletter-container">
    <div class="indomita-newsletter-content container">
    <div style="width:50%">
        <?php if ($titulo) : ?>
        <h2 class="indomita-newsletter-title" style="text-align:left !important;"><?php echo $titulo; ?></h2>
        <?php endif; ?>
        
        <?php if ($parrafo) : ?>
        <p class="indomita-newsletter-text"><?php echo $parrafo; ?></p>
        <?php endif; ?>
    </div>
    <div style="width:50%">
    <?php echo do_shortcode($shortcode); ?>
    </div>

      
    
    </div>
  </div>

  <?php
}