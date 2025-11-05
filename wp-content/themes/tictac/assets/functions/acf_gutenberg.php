<?php
/*include("blocks/slider/slider.php");
EqaaaEEinclude("blocks/titulo_subtitulo/titulo_subtitulo.php");
include("blocks/paginas/paginas.php");
include("blocks/empresa_colaboradora/empresa_colaboradora.php");
include("blocks/texto/texto.php");
//include("blocks/galeria/galeria.php");
include("blocks/banner_texto/banner_texto.php");

include("blocks/faqs/faqs.php");
include("blocks/subvencion/subvencion.php");
include("blocks/contacto/contacto.php");
include("blocks/banner/banner.php");
include("blocks/equipo2/equipo2.php");
include("blocks/texto_imagen/texto_imagen.php");
include("blocks/texto_texto/texto_texto.php");
include("blocks/portfolio_home/portfolio_home.php");
include("blocks/link/link.php");
include("blocks/links_personalizados/links_personalizados.php");
include("blocks/btn_custom/btn_custom.php");
include("blocks/formulario/formulario.php");
include("blocks/mapas/mapas.php");*/

/*include("blocks/slider_social/slider_social.php");
include("blocks/texto/texto.php");
include("blocks/linea_proceso/linea_proceso.php");
include("blocks/opiniones/opiniones.php");
include("blocks/cta/cta.php");
include("blocks/banner_normal/banner_normal.php");
include("blocks/portfolio/portfolio.php");
include("blocks/banner_iconos/banner_iconos.php");
include("blocks/galeria/galeria.php");

include("blocks/equipo/equipo.php");
include("blocks/equipo2/equipo2.php");
*/

//include("blocks/footer.php");

include("blocks/formulario_map/formulario_map.php");
include("blocks/formulario_imagen/formulario_imagen.php");
include("blocks/ultimas_entradas/ultimas_entradas.php");
include("blocks/fila_iconos/fila_iconos.php");
include("blocks/logotipos/logotipos.php");
include("blocks/fila_iconos_horizontal/fila_iconos_horizontal.php");
include("blocks/bloqueprueba/bloqueprueba.php");
include("blocks/banner_normal/banner_normal.php");
include("blocks/galeria/galeria.php");
include("blocks/BloqueTextoNosotros/BloqueTextoNosotros.php");
include("blocks/bloqueinicio/bloqueinicio.php");
include("blocks/texto_texto/texto_texto.php");
include("blocks/texto/texto.php");
include("blocks/bloquecategorias/bloquecategorias.php");
include("blocks/bloquenewsletter/bloquenewsletter.php");
include("blocks/sliderproductos/sliderproductos.php");
include("blocks/masarticulos/masarticulos.php");
include("blocks/favoritos/favoritos.php");
include("blocks/texto_imagen2/texto_imagen2.php");
include("blocks/texto_imagen/texto_imagen.php");
include("blocks/texto_imagen3/texto_imagen3.php");
include("blocks/texto_imagen4/texto_imagen4.php");
include("blocks/slidergaleria/slidergaleria.php");
include("blocks/galeriamenu/galeriamenu.php");
include("blocks/textoslider/textoslider.php");
include("blocks/textosemaforo/textosemaforo.php");
include("blocks/horarios/horarios.php");
include("blocks/slidergrande/slidergrande.php");
include("blocks/textosemaforoslider/textosemaforoslider.php");
include("blocks/textoiconos/textoiconos.php");
include("blocks/textoaudio/textoaudio.php");
include("blocks/textoalternado/textoalternado.php");
include("blocks/textoalternado2/textoalternado2.php");



if (function_exists('acf_add_local_field_group')) :
  acf_add_local_field_group(array(
    'key' => 'group_footer_settings',
    'title' => 'General',
    'fields' => array(
      array(
        'key' => 'catalogo_helix',
        'label' => 'catalogo',
        'name' => 'catalogo',
        'type' => 'file',
        'required' => false,
      ),
      array(
        'key' => 'field_footer_telefono_1',
        'label' => 'Teléfono 1',
        'name' => 'footer_telefono_1',
        'type' => 'link',
        'required' => false,
      ),
      array(
        'key' => 'field_footer_telefono_2',
        'label' => 'Teléfono 2',
        'name' => 'footer_telefono_2',
        'type' => 'link',
        'required' => false,
      ),
      array(
        'key' => 'field_fax',
        'label' => 'FAX',
        'name' => 'fax',
        'type' => 'link',
        'required' => false,
      ),
      array(
        'key' => 'field_footer_email',
        'label' => 'Email',
        'name' => 'footer_email',
        'type' => 'link',
        'required' => false,
      ),
      array(
        'key' => 'twitter',
        'label' => 'Twitter',
        'name' => 'twitter',
        'type' => 'link',
        'required' => false,
      ),
      array(
        'key' => 'instagram',
        'label' => 'Instagram',
        'name' => 'instagram',
        'type' => 'link',
        'required' => false,
      ),
      array(
        'key' => 'facebook',
        'label' => 'Facebook',
        'name' => 'facebook',
        'type' => 'link',
        'required' => false,
      ),
      array(
        'key' => 'ln',
        'label' => 'Linkedin',
        'name' => 'ln',
        'type' => 'link',
        'required' => false,
      ),
      array(
        'key' => 'yt',
        'label' => 'Youtube',
        'name' => 'yt',
        'type' => 'link',
        'required' => false,
      ),
      array(
        'key' => 'direccion',
        'label' => 'direccion',
        'name' => 'direccion',
        'type' => 'link',
        'required' => false,
      ),
      array(
        'key' => 'envio_gratuito',
        'label' => 'Envio gratuito',
        'name' => 'envio_gratuito',
        'type' => 'wysiwyg',
        'required' => false,
      ),
      array(
        'key' => 'field_footer_informacion',
        'label' => 'Información',
        'name' => 'informacion_texto',
        'type' => 'wysiwyg',
        'required' => false,
      ),
      array(
        'key' => 'legal',
        'label' => 'Legal',
        'name' => 'legal',
        'type' => 'post_object',
        'post_type' => array('page'),
        'multiple' => true,
        'return_format' => 'object',
        'ui' => 1,
      ),
      array(
        'key' => 'imagen_footer',
        'label' => 'Imagen Footer',
        'name' => 'Imagen Footer',
        'type' => 'image',
        'required' => false,
      ),
      array(
        'key' => 'banner_comun',
        'label' => 'Imagen banner',
        'name' => 'banner_comun',
        'type' => 'image',
        'required' => false,
      ),
    ),
    'location' => array(
      array(
        array(
          'param' => 'options_page',
          'operator' => '==',
          'value' => 'theme-general-settings',
        ),
      ),
    ),
  ));
/*
  acf_add_local_field_group(array(
    'key' => 'group_footer_blocks',
    'title' => 'Bloques footer',
    'fields' => array(
      array(
        'key' => 'test',
        'label' => 'Teléfono 1',
        'name' => 'test_1',
        'type' => 'link',
        'required' => false,
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
  ));
*/
endif;

add_action( 'acf/include_fields', function() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group( array(
	'key' => 'group_64f845ea765f0',
	'title' => 'Banner productos',
	'fields' => array(
		array(
			'key' => 'field_64f845ebe5648',
			'label' => 'Banner Productos',
			'name' => 'banner_productos',
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
	'location' => array(
		array(
			array(
				'param' => 'options_page',
				'operator' => '==',
				'value' => 'theme-general-settings',
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



function subvencion_footer()
{
    $logotipos = get_field('logotipos_subvencion','options');
    $size = 'large';
    $texto_subvencion = get_field("texto_subvencion",'options');
?>
    <div class="subvencion d-flex justify-content-center py-5">
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

add_action( 'acf/include_fields', function() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group( array(
	'key' => 'group_65f2d6c96aec1',
	'title' => 'Subtitulo producto',
	'fields' => array(
		array(
			'key' => 'field_65f2d6c91472b',
			'label' => 'Subtitulo producto',
			'name' => 'subtitulo_producto',
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
				'param' => 'post_type',
				'operator' => '==',
				'value' => 'product',
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