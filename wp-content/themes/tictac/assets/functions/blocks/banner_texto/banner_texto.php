<?php
if (function_exists('acf_add_local_field_group')) :

    acf_add_local_field_group(array(
        'key' => 'group_bannertexto',
        'title' => 'bannertexto',
        'fields' => array(
            array(
                'key' => 'fondo_bannertexto',
                'label' => 'Fondo',
                'name' => 'fondo_bloque',
                'type' => 'image',
                'required' => 1,
            ),
            array(
                'key' => 'titulo_bannertexto',
                'label' => 'Titulo',
                'name' => 'titulo_bloque',
                'type' => 'text',
                'required' => 1,
            ),
            array(
                'key' => 'subtitulo_bannertexto',
                'label' => 'subtitulo',
                'name' => 'subtitulo_bloque',
                'type' => 'text',
                'required' => 1,
            ),
            array(
                'key' => 'texto_bannertexto',
                'label' => 'Texto',
                'name' => 'texto_bloque',
                'type' => 'wysiwyg',
                'required' => 1,
            ),
            array(
                'key' => 'link1_bannertexto',
                'label' => 'Link 1',
                'name' => 'link1_bloque',
                'type' => 'link',
                'required' => 1,
                'return_format' => 'array',
            ),
            array(
                'key' => 'link2_bannertexto',
                'label' => 'Link 2',
                'name' => 'link2_bloque',
                'type' => 'link',
                'required' => 1,
                'return_format' => 'array',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/bannertexto',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => '',
        'show_in_rest' => 0,
    ));

endif;

function bannertexto_acf()
{
    acf_register_block_type([
        'name'        => 'bannertexto',
        'title'        => __('Banner texto', 'tictac'),
        'description'    => __('Banner texto', 'tictac'),
        'render_callback'  => 'bannertexto',
        'mode'        => 'preview',
        'icon'        => 'star-filled',
        'keywords'      => ['custom', 'banner', 'bloque'],
    ]);
}

add_action('acf/init', 'bannertexto_acf');

function bannertexto_scripts()
{
    if (!is_admin()) {
        wp_enqueue_style('bannertexto', get_stylesheet_directory_uri() . '/assets/functions/blocks/banner_texto/banner_texto.min.css');
    }
}
add_action('wp_enqueue_scripts', 'bannertexto_scripts');

function bannertexto($block)
{
    $fondo_bloque = get_field("fondo_bloque");
    $titulo_bloque = get_field("titulo_bloque");
    $subtitulo_bloque = get_field("subtitulo_bloque");
    $texto_bloque = get_field("texto_bloque");
    $link1_bloque = get_field("link1_bloque");
    $link2_bloque = get_field("link2_bloque");
?>
    <div class="bannertexto <?php if(isset($block['className'])){ echo $block['className']; } ?>">
        <img class="bg" src="<?= $fondo_bloque["url"]; ?>" alt="<?= $fondo_bloque["alt"]; ?>">
        <div class="container">
            <div class="row">
                <div class="titulo"><?= $titulo_bloque ?></div>
                <?php if($subtitulo_bloque){ ?>
                    <div class="subtitulo"><?= $subtitulo_bloque; ?></div>
                <?php } ?>
                <div class="texto"><?= $texto_bloque ?></div>
            </div>
            <?php if($link1_bloque){ ?>
            <div class="row d-flex justify-content-center links">
                <?php if($link1_bloque){ ?>
                <a class="btn custom yellow mx-2" href="<?= $link1_bloque["url"]; ?>"><?= $link1_bloque["title"]; ?></a>
                <?php } ?>
                <?php if($link2_bloque){ ?>
                <a class="btn custom yellow mx-2" href="<?= $link2_bloque["url"]; ?>"><?= $link2_bloque["title"]; ?></a>
                <?php } ?>
            </div>
            <?php } ?>
        </div>
    </div>
<?php
}
