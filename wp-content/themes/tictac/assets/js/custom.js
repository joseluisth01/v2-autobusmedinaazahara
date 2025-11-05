jQuery(document).ready(function () {


  jQuery(document).ready(function () {
    function initSplide() {
      if (jQuery(".slider_full").length > 0) {
        var splideHome = new Splide('.slider_full', {
          type: 'loop',
          arrows: true,  // Asegúrate de que las flechas estén activadas
          pagination: false,
          lazyLoad: "nearby",
          autoplay: true,
          preloadPages: 1
        });
        splideHome.mount();
      }
    }

    // Inicializa el slider en la carga de la página
    initSplide();

    // Vuelve a inicializar el slider después de que WooCommerce actualiza los productos vía AJAX
    jQuery(document.body).on('updated_wc_div', function () {
      initSplide();
    });
  });


  if (jQuery(".slider_social").length > 0) {
    var splide_social = new Splide('.slider_social', {
      type: 'loop',
      arrows: false,
      pagination: false,
      perPage: 3,
      perMove: 3,
      height: "650px",
      autoplay: true,
      grid: {
        // You can define rows/cols instead of dimensions.
        dimensions: [[1, 1], [2, 1], [1, 1], [1, 1], [2, 1], [1, 1]],
        gap: {
          row: '6px',
          col: '6px',
        },
      },
      breakpoints: {
        640: {
          height: "250px",
          grid: {
            dimensions: [[1, 1], [2, 1], [1, 1], [1, 1], [2, 1], [1, 1]],
          },
        },
      },
    });
    splide_social.mount(window.splide.Extensions);
  }

  if (jQuery(".logotipos").length > 0) {
    var splideLogotipos = new Splide('.logotipos', {
      arrows: true,
      pagination: false,
      type: 'loop',
      perPage: 5,
      perMove: 1,
      breakpoints: {
        768: {
          perPage: 2,
        }
      }
    });
    splideLogotipos.mount();
  }

  if (jQuery(".slider_opiniones").length > 0) {
    var splideHome = new Splide('.slider_opiniones', {
      type: 'loop',
      arrows: false,
      pagination: true,
      autoplay: true,
      classes: {
        pagination: 'splide__pagination custom_pagination', // container
        page: 'splide__pagination__page custom_paginate', // each button
      },
    });
    splideHome.mount();
  }

  if (jQuery(".bloque_seo").length > 0) {
    jQuery(".bloque_seo .open").on("click", function () {
      jQuery(this).parent().find(".seoclosed").toggleClass("closed");
      jQuery(this).toggleClass("change");
    });
    jQuery(window).scroll(function () {
      if (jQuery(this).scrollTop() > 50) {
        if (!jQuery(".bloque_seo").hasClass("toggled")) {
          jQuery(".bloque_seo").addClass("toggled");
          jQuery(".bloque_seo .seoclosed").toggleClass("closed");
        }
      }
    });
  }

  if (jQuery(".filaTextoAmpliable").length > 0) {
    jQuery(".filaTextoAmpliable .titulo").on("click", function () {
      jQuery(this).toggleClass("rotar");
      jQuery(this).parent().find(".textoCerrado").slideToggle();
    });
  }

  if (jQuery(".filaAcordeon").length > 0) {
    jQuery(".filaAcordeon .titulo").on("click", function () {
      jQuery(this).parent().toggleClass("openFAQ");
      jQuery(this).parent().find(".contenido").slideToggle();
    });
  }

  if (jQuery(".menu-movil .menu-item-has-children").length > 0) {
    var openSubMenu = "<span class='openSubMenu'>+</span>";
    jQuery(".menu-movil .menu-item-has-children").append(openSubMenu);
    jQuery(".openSubMenu").on("click", function () {
      jQuery(this).siblings(".sub-menu").slideToggle();
    });
  }
  if (jQuery(".menuOpen").length > 0) {
    jQuery(".menuOpen").on("click", function () {
      jQuery(".menuOpen").toggleClass("opened");
      jQuery("#menu.menu-mobile").toggleClass("opened");
    });
  }

  const popup = jQuery('#popup');
  const imagenPopup = jQuery('#imagen-popup');

  if (popup.length) {
    jQuery('.foto img').click(function () {
      popup.addClass('mostrar');
      imagenPopup.attr('src', jQuery(this).attr('src'));
    });

    popup.click(function () {
      popup.removeClass('mostrar');
    });
  }

  if (jQuery("#portfolio").length > 0) {
    // Inicializar Isotope

    var $grid = jQuery('.grid_portfolio').isotope({
      itemSelector: '.grid-item',
      layoutMode: 'fitRows'
    });

    // Agregar los botones de filtro
    jQuery('.filter-button').on('click', function () {
      var filterValue = jQuery(this).attr('data-filter');
      $grid.isotope({ filter: filterValue });
      jQuery('.filter-button').removeClass('active');
      jQuery(this).addClass('active');
    });

    // Inicializar Fancybox
    Fancybox.bind("[data-fancybox]");

  }




});