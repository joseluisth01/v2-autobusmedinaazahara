<?php get_header(); ?>

<div id="content" class="blogpage">

	<?php if (have_posts()) : ?>
		<div class="blog">
			<div class="postList">
				<?php while (have_posts()) : the_post(); ?>
					<?php
					$img = get_the_post_thumbnail();
					if (!$img) {
						$img = '<img src="' . get_template_directory_uri() . '/assets/images/post_blog.webp" alt="' . get_the_title() . '">';
					}
					?>
					<div class="post">
						<?= $img; ?>
						<div class="contenido">
							<div class="desc">
								<div class="h2">
									<?php the_title(); ?>
								</div>
								<div class="excerpt">
									<?= get_the_excerpt(); ?>
								</div>
								<a class="btn custom" href="<?php the_permalink(); ?>" class="post">Leer más</a>
							</div>
						</div>
					</div>
				<?php endwhile; ?>
			</div><!-- /#post-<?php get_the_ID(); ?> -->

		</div>
		<?php wp_reset_query(); ?>
		<?php
		// Pagination
		the_posts_pagination(array(
			'prev_text' => __('<span class="icon icon-left"></span>', 'textdomain'),
			'next_text' => __('<span class="icon icon-right"></span>', 'textdomain'),
		  )); ?>
</div>


<?php else : ?>

	<div id="post-404" class="noposts">

		<p><?php _e('None found.', 'example'); ?></p>

	</div><!-- /#post-404 -->

<?php endif;
	wp_reset_query(); ?>

</div><!-- /#content -->

<?php get_footer(); ?>