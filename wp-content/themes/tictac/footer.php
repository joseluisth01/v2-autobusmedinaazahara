<?php
if (!is_front_page()) {
    // $trabajamosconlosmejores = obtener_bloque_reutilizable(236);


    if ($enquepodemosayudarte) {
        echo '<div class="mt-5"></div>';
        echo $enquepodemosayudarte;
        echo '<div class="mb-5"></div>';
    }
    if ($loquedicendenosotros) {
        echo $loquedicendenosotros;
        echo '<div class="mb-5"></div>';
    }
    if ($trabajamosconlosmejores) {
        echo $trabajamosconlosmejores;
        echo '<div class="mb-5"></div>';
    }
}
?>
<footer id="footer" class="">
    <?php if (get_field("imagen_footer", "options")) {
        $bgfooter = get_field("imagen_footer", "options");
    ?>
        <img class="bgfooter" src="<?= $bgfooter['url']; ?>" alt="<?= $bgfooter['alt']; ?>">
    <?php } ?>
    <?php
    $phone = get_field("footer_telefono_1", "options");
    $whatsapp = get_field("footer_telefono_2", "options");
    $fax = get_field("fax", "options");
    $email = get_field("footer_email", "options");
    $info = get_field("field_footer_informacion", "options");
    $instagram = get_field("instagram", "options");
    $facebook = get_field("facebook", "options");
    $twitter = get_field("twitter", "options");
    $linkedin = get_field("ln", "options");
    $direccion = get_field("direccion", "options");
    $upload_dir = wp_upload_dir();
    ?>
    <div class="container infofooter">

        <div class="logo">
            <img src="<?php echo $upload_dir['baseurl']; ?>/2025/07/Vector-10.svg" alt="">
        </div>

        <div class="columna2divinformacion">
            <img src="<?php echo $upload_dir['baseurl']; ?>/2025/07/A.-Bravo.svg" class="imglogo2footer" alt="">
            <div style="width:50%; display:flex; align-items:center; gap:6px; justify-content: center;">
                <img src="https://autobusmedinaazahara.com/wp-content/uploads/2025/07/Vector-16.svg" alt="">
                <a style="font-family: 'Duran-Medium' !important;" href="tel:957429034">957429034</a>
				
            </div>
			<div style="width:50%; display:flex; align-items:center; gap:6px; justify-content: center;">
                <img src="https://autobusmedinaazahara.com/wp-content/uploads/2025/07/Vector-18.svg" alt="">
                <a style="font-family: 'Duran-Medium' !important;" href="mailto:fbravo@autocaresbravo.com">fbravo@autocaresbravo.com</a>
            </div>
        </div>
    </div>
    <div class="container">
        <div>
        <?php
        wp_nav_menu(array(
          'theme_location' => 'menu-footer',
          'menu_class'    => 'flex items-center space-x-8',
          'container'     => false,
          'items_wrap'    => '<ul class="%2$s menufooter">%3$s</ul>',
          'fallback_cb'   => false
        ));
        ?>
        </div>
    </div>
    <div class="container subf">
        <div class="copyright">
            <a class="" href="https://tictac-comunicacion.es" target="_blank" rel="nofollow noopener">Copyright <?php echo date("Y"); ?> | Diseñado y Desarrollado por <img src="<?php echo $upload_dir['baseurl']; ?>/2025/07/Vector-11.svg" alt=""></a>
        </div>
        <div class="politicas">
            <?php
            $featured_posts = get_field('legal', 'options');
            if ($featured_posts) : ?>
                <ul class="d-flex flex-wrap justify-content-center align-content-center">
                    <?php foreach ($featured_posts as $featured_post) :
                        $permalink = get_permalink($featured_post->ID);
                        $title = get_the_title($featured_post->ID);
                    ?>
                        <li style="display:flex" class="">
                            <a class="justify-content-center" href="<?php echo esc_url($permalink); ?>" rel="noindex nofollow"><?php echo esc_html($title); ?></a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>

</html>