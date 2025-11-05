
  jQuery( document ).ready(function() {
    Fancybox.bind("[data-fancybox]", {
        // Your custom options
      });

      jQuery(".box-btn.tab1").on( "click", function() {
        jQuery(".box-btn").removeClass("active");
        jQuery(this).toggleClass("active");
        jQuery(".tab-panel").hide();
        jQuery(".bodas").show();
      });

      jQuery(".box-btn.tab2").on( "click", function() {
        jQuery(".box-btn").removeClass("active");
        jQuery(this).toggleClass("active");
        jQuery(".tab-panel").hide();
        jQuery(".eventos").show();
      });

      jQuery(".box-btn.tab3").on( "click", function() {
        jQuery(".box-btn").removeClass("active");
        jQuery(this).toggleClass("active");
        jQuery(".tab-panel").hide();
        jQuery(".corporativa").show();
      });

      jQuery(".box-btn.tab4").on( "click", function() {
        jQuery(".box-btn").removeClass("active");
        jQuery(this).toggleClass("active");
        jQuery(".tab-panel").hide();
        jQuery(".videos-bodas").show();
      });

      jQuery(".box-btn.tab5").on( "click", function() {
        jQuery(".box-btn").removeClass("active");
        jQuery(this).toggleClass("active");
        jQuery(".tab-panel").hide();
        jQuery(".videos-eventos").show();
      });

      jQuery(".box-btn.tab6").on( "click", function() {
        jQuery(".box-btn").removeClass("active");
        jQuery(this).toggleClass("active");
        jQuery(".tab-panel").hide();
        jQuery(".videos-corporativa").show();
      });
      if(jQuery(".splide.bodas").length > 0){
      var splide_bodas = new Splide( '.splide.bodas', {
        type   : 'loop',
        padding: '5rem',
      } );
      splide_bodas.mount();
    }

    if(jQuery(".splide.eventos").length > 0){
      var splide_eventos = new Splide( '.splide.eventos', {
        type   : 'loop',
        padding: '5rem',
      } );
      
      splide_eventos.mount();
    }

      if(jQuery(".splide.corporativas").length > 0){
      var splide_corporativas = new Splide( '.splide.corporativas', {
        type   : 'loop',
        padding: '5rem',
      } );
      
      splide_corporativas.mount();
    }
    if(jQuery(".selector_type").length > 0){
      jQuery(".selector_type .fotos-tab span").on( "click", function() {
        jQuery(".selector_type span").removeClass("active_span");
        jQuery(this).addClass("active_span");
        jQuery(".videos").hide();
        jQuery(".fotos").show();
      });
      jQuery(".selector_type .videos-tab span").on( "click", function() {
        jQuery(".selector_type span").removeClass("active_span");
        jQuery(this).addClass("active_span");
        jQuery(".fotos").hide();
        jQuery(".videos").show();
      });
    }
  });