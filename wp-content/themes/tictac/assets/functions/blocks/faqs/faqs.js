jQuery(document).ready(function(){

if(jQuery(".faqs").length > 0){
  jQuery(".titulo_faq").on( "click", function() {
    jQuery(this).toggleClass("activo");
    jQuery(this).next().slideToggle();
  });
}

});
