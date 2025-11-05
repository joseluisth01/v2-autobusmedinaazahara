<?php	get_header(); ?>
<?php get_sidebar(); ?>

	<section class="wrapper-blog">
		<section class="blog">
		<?php
		if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
				<?php $img = imagen_destacada();$cat = get_the_category(); ?>
    	<a href="<?php the_permalink(); ?>" <?php post_class() ?> id="post-<?php the_ID(); ?>">
        <div class="image"> <img src="<?php echo $img['url']; ?>" alt="<?php echo $img['alt']; ?>" title="<?php echo $img['title']; ?>"> </div>
				<div class="text">
					<div class="type"><?php echo $cat[0]->name; ?></div>
					<div class="title"><?php echo the_title(); ?></div>
					<div class="date"><?php echo get_the_date(); ?></div>
				</div>
			</a>
		<?php endwhile;?>
		</section>
			<aside class="sidebar-blog-wrapper">
			<?php  if(is_active_sidebar('sidebar-blog')){
					dynamic_sidebar('sidebar-blog');
				}
			?>
			</aside>
	  <?php else: ?>
	  	<h2>No existen entradas</h2>
	  <?php endif; ?>
	</section>
	<?php
	// Pagination
	if ( function_exists('base_pagination') ) { base_pagination(); } else if ( is_paged() ) { ?>
	<div class="navigation clearfix">
			<div class="alignleft"><?php next_posts_link('&laquo; Previous Entries') ?></div>
			<div class="alignright"><?php previous_posts_link('Next Entries &raquo;') ?></div>
	</div>
	<?php } ?>
<?php get_footer(); ?>
