<!DOCTYPE html>

<html <?php language_attributes(); ?>>

<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <title><?php wp_title(); ?></title>
  <?php wp_head(); ?>
  <!-- Definir viewporrrrrrrrrrt para dispositivos web móviles -->
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet preload" as="style" media="all" href="<?php bloginfo('stylesheet_url'); ?>" />
  <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
  <link rel="icon" type="image/x-icon" href="<?= site_url('/favicon.ico'); ?>">

  <!-- Google tag (gtag.js) -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=G-ZP27ZGNFQ5"></script>
  <script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
      dataLayer.push(arguments);
    }
    gtag('js', new Date());

    gtag('config', 'G-ZP27ZGNFQ5');
  </script>

  <script type="text/javascript" src="https://cache.consentframework.com/js/pa/48113/c/zLPp2/stub"></script>
  <script type="text/javascript" src="https://choices.consentframework.com/js/pa/48113/c/zLPp2/cmp" async></script>

  <meta name="google-site-verification" content="nilG5lz23BQyp_AYsIMygG_7h98-WMr0HHQFUPOdki8" />
</head>
<?php $post_slug = get_post_field('post_name', get_post()); ?>

<body <?php body_class(); ?> id="<?php echo $post_slug; ?>">



  <?php
  $phone = get_field("footer_telefono_1", "options");
  $whatsapp = get_field("footer_telefono_2", "options");
  $fax = get_field("fax", "options");
  $email = get_field("footer_email", "options");
  $info = get_field("field_footer_informacion", "options");
  $instagram = get_field("instagram", "options");
  $facebook = get_field("facebook", "options");
  $linkedin = get_field("ln", "options");
  $direccion = get_field("direccion", "options");
  $catalogo = get_field("catalogo", "options");
  $upload_dir = wp_upload_dir();
  ?>

  <a class="buttonreservar buttonreservarresponsive" href="https://autobusmedinaazahara.com/#procesocompra">RESERVAR<img style="width:20px" src="<?php echo $upload_dir['baseurl']; ?>/2025/07/87070a4063df51d50fd4bae645befbb94df703e2.gif"></a>
  <header id="header" class="navBar">

    <div class="divheader flex items-center justify-between">

      <?php echo do_shortcode('[gtranslate]'); ?>

      <!-- Desktop Navigation - Cambiado de md:flex a xl:flex -->
      <nav class="hidden xl:flex flex-1 justify-center">
        <?php
        wp_nav_menu(array(
          'theme_location' => 'menu-header',
          'menu_class'    => 'flex items-center space-x-8',
          'container'     => false,
          'items_wrap'    => '<ul class="%2$s">%3$s</ul>',
          'fallback_cb'   => false
        ));
        ?>
        <a class="buttonreservar" href="https://autobusmedinaazahara.com/#procesocompra">RESERVAR<img style="width:20px" src="<?php echo $upload_dir['baseurl']; ?>/2025/07/87070a4063df51d50fd4bae645befbb94df703e2.gif"></a>
      </nav>

      <!-- Mobile Menu Button - Cambiado de md:hidden a xl:hidden -->
      <div class="desplegablemenuopen">
        <div class="menuOpen xl:hidden flex flex-col justify-center items-center cursor-pointer">
          <span></span>
          <span></span>
          <span></span>
        </div>

      </div>

    </div>

    <!-- Mobile Navigation -->
    <nav id="menu" class="menu-mobile w-full bg-white">
      <?php
      wp_nav_menu(array(
        'theme_location' => 'menu-header',
        'menu_class'    => 'flex flex-col items-center space-y-4',
        'container'     => false,
        'items_wrap'    => '<ul class="%2$s">%3$s</ul>',
        'fallback_cb'   => false
      ));
      ?>
    </nav>


  </header>

  <style>
    /* Estilos para el buscador según la imagen */
    .search-container {
      margin-left: 20px;
      position: relative;
    }

    /* Estilos para pantallas grandes */
    .desktop-search .search-input-wrapper {
      position: relative;
      display: flex;
      align-items: center;
      border: 1px solid #333;
      border-radius: 8px;
      overflow: hidden;
      background-color: white;
      width: 240px;
    }

    .desktop-search .search-field {
      flex: 1;
      border: none;
      padding: 8px 8px 8px 0;
      font-family: 'MontserratAlternates-Medium', sans-serif;
      font-size: 16px;
      outline: none;
      background: transparent;
      color: #777;
    }

    .desktop-search .search-field::placeholder {
      color: #999;
    }

    .buttonreservarresponsive {
      display: none;
    }

    .search-submit {
      background: transparent;
      border: none;
      cursor: pointer;
      padding: 8px 12px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .search-submit img {
      width: 20px;
      height: 20px;
    }

    /* Estilos para móviles */
    .mobile-search-icon {
      display: none;
      /* Oculto por defecto, se muestra solo en pantallas pequeñas */
    }

    .search-icon-toggle {
      cursor: pointer;
      padding: 8px;
    }

    .search-icon-toggle img {
      width: 20px;
      height: 20px;
    }

    /* Panel de búsqueda a pantalla completa */
    .search-panel {
      top: 0;
      left: 0;
      width: 100%;
      background-color: white;
      z-index: 999999;
      display: none;
      flex-direction: column;
      overflow-y: auto;
    }

    .search-panel.active {
      display: flex;
    }

    .search-form-container {
      padding: 20px !important;
    }

    .search-panel .search-input-wrapper {
      position: relative;
      display: flex;
      align-items: center;
      border: 1px solid #333;
      border-radius: 8px;
      overflow: hidden;
      background-color: white;
      width: 100%;
    }

    .search-panel .search-field {
      flex: 1;
      border: none;
      padding: 12px 12px 12px 0;
      font-family: 'MontserratAlternates-Medium', sans-serif;
      font-size: 16px;
      outline: none;
      background: transparent;
      color: #777;
    }

    #woocommerce-product-search-field {
      background-color: #ffffff !important;
    }

    /* Media queries para comportamiento responsivo - Cambiado a 1100px */
    @media (max-width: 1100px) {
      .desktop-search {
        display: none;
        /* Ocultar buscador normal en pantallas pequeñas */
      }

      .mobile-search-icon {
        display: block;
        /* Mostrar icono de lupa en pantallas pequeñas */
      }
    }
  </style>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const searchToggle = document.querySelector('.search-icon-toggle');
      const searchPanel = document.getElementById('search-panel');

      if (searchToggle && searchPanel) {
        searchToggle.addEventListener('click', function(e) {
          e.preventDefault();
          e.stopPropagation();

          // Alternar clase active
          searchPanel.classList.toggle('active');

          // Si el panel está activo, enfocar el campo de búsqueda
          if (searchPanel.classList.contains('active')) {
            const searchField = searchPanel.querySelector('.search-field');
            if (searchField) {
              setTimeout(function() {
                searchField.focus();
              }, 100);
            }
          }
        });

        // También cerrar el panel al hacer clic en el mismo icono o fuera del panel
        document.addEventListener('click', function(e) {
          if (searchPanel.classList.contains('active') &&
            !searchPanel.contains(e.target) &&
            !searchToggle.contains(e.target)) {
            searchPanel.classList.remove('active');
          }
        });

        // Prevenir cierre al hacer clic dentro del panel
        searchPanel.addEventListener('click', function(e) {
          e.stopPropagation();
        });
      }
    });
  </script>

  <script>
    // JavaScript para el menú móvil - Agregar al final del header.php o en un archivo JS separado

    document.addEventListener('DOMContentLoaded', function() {
      const menuButton = document.querySelector('.menuOpen');
      const mobileMenu = document.querySelector('#menu.menu-mobile');

      if (menuButton && mobileMenu) {
        menuButton.addEventListener('click', function(e) {
          e.preventDefault();
          e.stopPropagation();

          // Toggle de las clases
          menuButton.classList.toggle('opened');
          mobileMenu.classList.toggle('opened');

          // Debug para verificar que funciona
          console.log('Menu button clicked');
          console.log('Menu opened:', mobileMenu.classList.contains('opened'));
        });

        // Cerrar menú al hacer clic en un enlace (opcional)
        const menuLinks = mobileMenu.querySelectorAll('a');
        menuLinks.forEach(function(link) {
          link.addEventListener('click', function() {
            menuButton.classList.remove('opened');
            mobileMenu.classList.remove('opened');
          });
        });

        // Cerrar menú al hacer clic fuera (opcional)
        document.addEventListener('click', function(e) {
          if (!menuButton.contains(e.target) && !mobileMenu.contains(e.target)) {
            menuButton.classList.remove('opened');
            mobileMenu.classList.remove('opened');
          }
        });
      }
    });
  </script>