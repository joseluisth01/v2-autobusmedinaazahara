<?php
    global $post;
    $post_slug = $post->post_name;
?>
<?php get_header(); ?>

<div id="primary" class="interna content-area <?php echo $post_slug; ?>">
    <main id="main" class="site-main" role="main">
        <div class="content_page">
          <?php the_content(); ?>
        </div>
    </main><!-- .site-main -->

</div><!-- .content-area -->

<?php get_footer(); ?>
