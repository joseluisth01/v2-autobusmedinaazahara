<?php
global $post;
$post_slug = $post->post_name;
?>
<?php get_header(); ?>

<?php
if (have_posts()) :
    while (have_posts()) : the_post(); ?>

        <div class="custom-postblog-container container">
            <br><br>
            <!-- Mostrar el título de la entrada -->
            <h2 class="custom-postblog-title"><?php the_title(); ?></h2>

            <!-- Mostrar el contenido de la entrada -->
            <div class="custom-postblog-content">
                <?php the_content(); ?>
            </div>
            <br>
            <!-- Mostrar la imagen destacada -->
            <?php if (has_post_thumbnail()) : ?>
                <div class="custom-postblog-thumbnail">
                    <?php the_post_thumbnail(); ?>
                </div>
            <?php endif; ?>
        </div>

<?php endwhile;
endif;
?>

<?php get_footer(); ?>
