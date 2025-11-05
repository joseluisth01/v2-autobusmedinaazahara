<?php

/**
 * Storefront automatically loads the core CSS even if using a child theme as it is more efficient
 * than @importing it in the child theme style.css file.
 *
 * Uncomment the line below if you'd like to disable the Storefront Core CSS.
 *
 * If you don't plan to dequeue the Storefront Core CSS you can remove the subsequent line and as well
 * as the sf_child_theme_dequeue_style() function declaration.
 */
//add_action( 'wp_enqueue_scripts', 'sf_child_theme_dequeue_style', 999 );

/**
 * Dequeue the Storefront Parent theme core CSS
 */


/**
 * Note: DO NOT! alter or remove the code above this text and only add your custom PHP functions below this text.
 */
// Simular funciones de WooCommerce si no está activo
if (!function_exists('wc_get_page_id')) {
    function wc_get_page_id($page) {
        // Devuelve un ID de página por defecto o null
        return null;
    }
}

if (!function_exists('is_woocommerce')) {
    function is_woocommerce() {
        return false;
    }
}

if (!function_exists('is_shop')) {
    function is_shop() {
        return false;
    }
}

if (!function_exists('is_product_category')) {
    function is_product_category() {
        return false;
    }
}

if (!function_exists('is_product')) {
    function is_product() {
        return false;
    }
}

if (!function_exists('is_cart')) {
    function is_cart() {
        return false;
    }
}

if (!function_exists('is_checkout')) {
    function is_checkout() {
        return false;
    }
}

require_once(get_stylesheet_directory() . '/assets/functions/acf_gutenberg.php');
add_theme_support('custom-logo');
add_image_size('foto', 650, 650, true);


// ✅ CAMBIAR ESTA FUNCIÓN PARA EVITAR EL WARNING
function load_textdomains_properly() {
    if (did_action('init')) {
        load_plugin_textdomain('acf');
        load_plugin_textdomain('complianz-terms-conditions');
        load_plugin_textdomain('complianz-gdpr');
    }
}
add_action('init', 'load_textdomains_properly', 20); // ✅ PRIORIDAD ALTA

function load_scripts()
{
  if (!is_admin()) {
    wp_enqueue_style('font', get_stylesheet_directory_uri() . '/assets/style/font.min.css');
    wp_enqueue_style('header', get_stylesheet_directory_uri() . '/assets/style/header.min.css');
    wp_enqueue_style('footer', get_stylesheet_directory_uri() . '/assets/style/footer.min.css');
    wp_enqueue_style('custom', get_stylesheet_directory_uri() . '/assets/style/custom.min.css');
    wp_enqueue_style('splidecss', get_stylesheet_directory_uri() . '/assets/splide/dist/css/splide.min.css');
    wp_enqueue_style('bootstrap', get_stylesheet_directory_uri() . '/assets/bootstrap/css/bootstrap.min.css');

    // Cargar jQuery primero
    wp_enqueue_script('jquery');

    // Eliminar los espacios en los handles de script
    wp_enqueue_script('splidejs', get_stylesheet_directory_uri() . '/assets/splide/dist/js/splide.min.js', array('jquery'), null, true);
    wp_enqueue_script('splidejs-grid', get_stylesheet_directory_uri() . '/assets/splide/dist/js/splide-extension-grid.min.js', array('jquery', 'splidejs'), null, true);
    wp_enqueue_script('bootstrap-js', get_stylesheet_directory_uri() . '/assets/bootstrap/js/bootstrap.min.js', array('jquery'), null, true);
    wp_enqueue_script('custom-js', get_stylesheet_directory_uri() . '/assets/js/custom.js', array('jquery', 'bootstrap-js'), null, true);

    // Localizar el script custom.js con ajax_object
    wp_localize_script('custom-js', 'ajax_object', array(
      'ajax_url' => admin_url('admin-ajax.php')
    ));
  }
}
add_action('wp_enqueue_scripts', 'load_scripts');

function add_file_types_to_uploads($file_types)
{
  $new_filetypes = array();
  $new_filetypes['svg'] = 'image/svg+xml';
  $file_types = array_merge($file_types, $new_filetypes);
  return $file_types;
}
add_filter('upload_mimes', 'add_file_types_to_uploads');


if (function_exists('acf_add_options_page')) {
  acf_add_options_page(array(
    'page_title'   => 'Opciones Generales',
    'menu_title'  => 'Opciones de Plantilla',
    'menu_slug'   => 'theme-general-settings',
    'capability'  => 'edit_posts',
    'redirect'    => false
  ));
  acf_add_options_sub_page(array(
    'page_title' => 'Bloques footer',
    'menu_title' => 'Bloques footer',
    'menu_slug'   => 'bloques-footer',
    'parent_slug' => 'theme-general-settings',
  ));
}

function wpb_custom_new_menu()
{
  register_nav_menus(
    array(
      'menu-header' => __('Menu header'),
      'menu-footer' => __('Menu footer')
    )
  );
}
add_action('init', 'wpb_custom_new_menu');

add_filter('body_class', 'my_body_classes');
function my_body_classes($classes)
{
  if (wp_is_mobile()) {
    $classes[] = 'responsive';
  }
  return $classes;
}

add_theme_support('post-thumbnails');

add_post_type_support('page', 'excerpt');

function custom_excerpt_length($length)
{
  return 20;
}
add_filter('excerpt_length', 'custom_excerpt_length', 999);



function add_custom_title_to_latest_posts_list($title, $block)
{
  if (!is_array($block) || !isset($block['blockName'])) {
    error_log('Invalid $block structure: ' . print_r($block, true));
    return $title;
  }

  if ($block['blockName'] === 'core/latest-posts') {
    // Agrega el título personalizado antes de la lista de publicaciones
    $title = '<h2>Mis últimas publicaciones</h2>' . $title;
  }
  return $title;
}
add_filter('the_title', 'add_custom_title_to_latest_posts_list', 10, 2);



function agregar_boton_scroll_to_top()
{
?>
  <a href="#" id="topbutton" title="Volver al inicio">
    <div class="icono">
      <svg width="40px" height="40px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
        <title>90-Arrow Up</title>
        <g id="_90-Arrow_Up" data-name="90-Arrow Up">
          <path d="M16,0A16,16,0,1,0,32,16,16,16,0,0,0,16,0Zm0,30A14,14,0,1,1,30,16,14,14,0,0,1,16,30Z"></path>
          <path d="M15.29,10.29l-8,8,1.41,1.41L16,12.41l7.29,7.29,1.41-1.41-8-8A1,1,0,0,0,15.29,10.29Z"></path>
        </g>
      </svg>
    </div>
  </a>

  <script>
    jQuery(document).ready(function($) {
      var offset = 100,
        speed = 250,
        duration = 500,
        scrollButton = $('#topbutton');
      $(window).scroll(function() {
        if ($(this).scrollTop() < offset) {
          scrollButton.fadeOut(duration);
        } else {
          scrollButton.fadeIn(duration);
        }
      });
      scrollButton.on('click', function(e) {
        e.preventDefault();
        $('html, body').animate({
          scrollTop: 0
        }, speed);
      });
    });
  </script>

<?php
}
//add_action('wp_footer', 'agregar_boton_scroll_to_top');

function obtener_bloque_reutilizable($bloque_id)
{
  $block = get_post($bloque_id);
  if ($block && $block->post_type === 'wp_block') {
    $content = apply_filters('the_content', $block->post_content);
    return $content;
  }
}

//add_action('woocommerce_shop_loop_item_title','custom_subtitle',20);

/*  Banner contacto lateral y pie  */
function banner_contacto_lateral()
{

  $phone = get_field("footer_telefono_1", "options");
  $whatsapp = get_field("footer_telefono_2", "options");
  $fax = get_field("fax", "options");
  $email = get_field("footer_email", "options");
  $info = get_field("field_footer_informacion", "options");
  $instagram = get_field("instagram", "options");
  $facebook = get_field("facebook", "options");
  $linkedin = get_field("ln", "options");
  $direccion = get_field("direccion", "options");
?>
  <div class="banner_lateral">
    <div class="sticky-header">
      <section>
        <a href="https://api.whatsapp.com/send/?phone=34650413632" target="_blank">
          <div class="borde_ext verde">
            <div class="borde_int">
              <img class="image_contact" src="https://www.thepointmarbella.com/wp-content/uploads/2025/06/Boton-1.0.png" alt="">
            </div>
          </div>
        </a>
      </section>
      <section>
        <a href="tel:650413632" target="_blank">
          <div class="borde_ext verde">
            <div class="borde_int">
              <img class="image_contact" src="https://www.thepointmarbella.com/wp-content/uploads/2025/06/Boton-2.0.png" alt="">
            </div>
          </div>
        </a>
      </section>
      <section>
        <a href="https://www.thepointmarbella.com/reservations/" target="_blank">
          <div class="borde_ext verde">
            <div class="borde_int">
              <img class="image_contact" src="https://www.thepointmarbella.com/wp-content/uploads/2025/06/Boton-3.0.png" alt="">
            </div>
          </div>
        </a>
      </section>
    </div>
  </div>
  <style>
    /* Estilo común para todos los botones */
    .banner_lateral .sticky-header section .borde_int {
      position: relative;
    }

    /* Primer botón */
    .banner_lateral .sticky-header section:first-child .borde_int::after {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-size: contain;
      background-repeat: no-repeat;
      background-position: center;
      opacity: 0;
      transition: opacity 0.3s ease;
    }

    /* Segundo botón */
    .banner_lateral .sticky-header section:nth-child(2) .borde_int::after {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-size: contain;
      background-repeat: no-repeat;
      background-position: center;
      opacity: 0;
      transition: opacity 0.3s ease;
    }

    /* Tercer botón */
    .banner_lateral .sticky-header section:nth-child(3) .borde_int::after {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-size: contain;
      background-repeat: no-repeat;
      background-position: center;
      opacity: 0;
      transition: opacity 0.3s ease;
    }

    /* Hover para todos los botones */
    .banner_lateral .sticky-header section:hover .borde_int::after {
      opacity: 1;
    }
  </style>
<?php

};
// add_action('wp_head', 'banner_contacto_lateral');







if (!function_exists('slider_scripts')) {
  function slider_scripts()
  {
    if (!is_admin()) {
      // Encolar el archivo CSS de Splide
      wp_enqueue_style('splide-css', get_stylesheet_directory_uri() . '/assets/splide/splide.min.css');

      // Encolar el archivo JS de Splide
      wp_enqueue_script('splide-js', get_stylesheet_directory_uri() . '/assets/splide/splide.min.js', array(), null, true);

      // Encolar el archivo custom.js
      wp_enqueue_script('custom-slider', get_stylesheet_directory_uri() . '/assets/js/custom.js', array('jquery', 'splide-js'), null, true);
    }
  }
  add_action('wp_enqueue_scripts', 'slider_scripts');
}




// Añade esto al final de functions.php de tu tema o en el plugin
add_action('wp_mail_failed', function($wp_error) {
    error_log('❌ WP_MAIL FAILED: ' . $wp_error->get_error_message());
});

add_action('phpmailer_init', function($phpmailer) {
    error_log('📧 PHPMailer configurado - SMTP: ' . ($phpmailer->isSMTP() ? 'SÍ' : 'NO'));
    error_log('📧 Host: ' . $phpmailer->Host);
    error_log('📧 Puerto: ' . $phpmailer->Port);
    error_log('📧 From: ' . $phpmailer->From);
});




