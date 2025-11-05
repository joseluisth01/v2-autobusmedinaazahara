<?php

function ultimas_entradas_acf()
{
    acf_register_block_type([
        'name'        => 'ultimas_entradas',
        'title'        => __('Tendencias, Trucos y Consejos', 'tictac'),
        'description'    => __('Tendencias, Trucos y Consejos', 'tictac'),
        'render_callback'  => 'ultimas_entradas',
        'mode'        => 'preview',
        'icon'        => 'star-filled',
        'keywords'      => ['tendencias', 'trucos', 'consejos', 'bloque', 'custom', 'blog', 'moda'],
    ]);
}

add_action('acf/init', 'ultimas_entradas_acf');

function ultimas_entradas_scripts()
{
    if (!is_admin()) {
        wp_enqueue_style('entradas', get_stylesheet_directory_uri() . '/assets/functions/blocks/ultimas_entradas/ultimas_entradas.min.css');
    }
}
add_action('wp_enqueue_scripts', 'ultimas_entradas_scripts');

function ultimas_entradas()
{
    $args = array(
        'post_type' => 'post',
        'posts_per_page' => 2,
    );

    $query = new WP_Query($args);

    if ($query->have_posts()) {        
?>
        <div class="ultimas_entradas py-5">            
            
            <div class="container">
            <div class="col-12 encabezado text-left">                                                
                <h2>Tendencias, Trucos y Consejos</h2>
            </div>
                <?php
                $description = "Sumérgete en el mundo de la moda, descubre tendencias, consejos y trucos de estilo en nuestro blog. Inspírate con ideas frescas para realizar tu look y vive la moda a tu manera. ¡Inspírate y transforma tu look!";
                ?>
                <p class="blog-description text-justify mb-4"><?php echo $description; ?></p>
                <?php                
                while ($query->have_posts()) {
                    $query->the_post();
                    $fecha = get_the_date('d/m/y');                 
                ?>
                    <a class="entrada" href="<?php the_permalink(); ?>">
                        <div class="content">  
                            <?php if (has_post_thumbnail()) { ?>
                                <div class="wp-block-latest-posts__featured-image">
                                    <?php the_post_thumbnail('large'); ?>
                                </div>
                            <?php } ?>         
                            <div class="contenidoentrada">
                            <div class="wp-block-latest-posts__post-date"><?php echo $fecha; ?></div>
                            <div class="wp-block-latest-posts__post-title"><?php the_title(); ?></div>                           
                            <div class="wp-block-latest-posts__post-excerpt"><?php the_excerpt(); ?></div>  
                            </div> 
                                     
                                                                                  
                        </div>
                    </a>                    
                <?php
                }
                ?>
            </div>
            <div class="col-12 text-center mt-4">
                <a class="btn custom ver-todo" href="<?php echo get_permalink( get_option( 'page_for_posts' ) ); ?>">VER TODO</a>
            </div>
        </div>
<?php
    }

    wp_reset_postdata();
}