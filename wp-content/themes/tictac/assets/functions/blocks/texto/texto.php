<?php
if (function_exists('acf_add_local_field_group')) :

  acf_add_local_field_group(array(
    'key' => 'group_62b213bf0c8b8',
    'title' => 'Texto',
    'fields' => array(
      array(
        'key' => 'field_62b213bf13bdd',
        'label' => 'Titulo',
        'name' => 'titulo_bloque',
        'aria-label' => '',
        'type' => 'wysiwyg',
        'instructions' => '',
        'required' => 0,
        'conditional_logic' => 0,
        'wrapper' => array(
          'width' => '',
          'class' => '',
          'id' => '',
        ),
        'default_value' => '',
        'tabs' => 'all',
        'toolbar' => 'full',
        'media_upload' => 1,
        'delay' => 0,
      ),
      array(
        'key' => 'field_62b213bf1b336',
        'label' => 'Subtitulo',
        'name' => 'subtitulo_bloque',
        'aria-label' => '',
        'type' => 'wysiwyg',
        'instructions' => '',
        'required' => 0,
        'conditional_logic' => 0,
        'wrapper' => array(
          'width' => '',
          'class' => '',
          'id' => '',
        ),
        'default_value' => '',
        'placeholder' => '',
        'prepend' => '',
        'append' => '',
        'maxlength' => '',
      ),
      array(
        'key' => 'field_62b213bf1b335',
        'label' => 'Texto',
        'name' => 'texto_bloque',
        'aria-label' => '',
        'type' => 'wysiwyg',
        'instructions' => '',
        'required' => 0,
        'conditional_logic' => 0,
        'wrapper' => array(
          'width' => '',
          'class' => '',
          'id' => '',
        ),
        'default_value' => '',
        'tabs' => 'all',
        'toolbar' => 'full',
        'media_upload' => 1,
        'delay' => 0,
      ),
      array(
        'key' => 'field_62b213bf1b337',
        'label' => 'Texto ampliable',
        'name' => 'texto_ampliable',
        'type' => 'wysiwyg',
        'instructions' => '',
        'required' => 0,
        'conditional_logic' => 0,
        'wrapper' => array(
          'width' => '',
          'class' => '',
          'id' => '',
        ),
        'default_value' => '',
        'tabs' => 'all',
        'toolbar' => 'full',
        'media_upload' => 1,
        'delay' => 0,
      ),
      array(
        'key' => 'ampliable_fondo',
        'label' => 'fondo',
        'name' => 'fondo',
        'type' => 'color_picker',
        'required' => 0,
        'conditional_logic' => 0,
        'library' => 'all',
        'layout' => 'block',
      ),
    ),
    'location' => array(
      array(
        array(
          'param' => 'block',
          'operator' => '==',
          'value' => 'acf/texto',
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

function texto_acf()
{
  acf_register_block_type([
    'name'        => 'Texto',
    'title'        => __('Texto', 'tictac'),
    'description'    => __('Texto', 'tictac'),
    'render_callback'  => 'texto',
    'mode'        => 'preview',
    'icon'        => 'star-filled',
    'keywords'      => ['custom', 'seo', 'bloque'],
  ]);
}

add_action('acf/init', 'texto_acf');

function texto_scripts()
{
  if (!is_admin()) {
    wp_enqueue_style('texto', get_stylesheet_directory_uri() . '/assets/functions/blocks/texto/texto.min.css');
  }
}
add_action('wp_enqueue_scripts', 'texto_scripts');

function texto()
{
  $titulo = get_field("titulo_bloque");
  $subtitulo = get_field("subtitulo_bloque");
  $texto = get_field("texto_bloque");
  $texto_ampliable = get_field("texto_ampliable");
  $fondo = get_field("fondo");
?>
  <div class="bloque_seo <?php if (!$fondo) {
                            echo "white";
                          } ?>" style="<?php if ($fondo) {
                                          echo "background-color:" . $fondo;
                                        } ?>">
    <div class="content container">
      <?php if ($titulo) { ?>
        <div class="titulo">
          <?= $titulo ?>
        </div>
      <?php } ?>
      <?php if ($subtitulo) { ?>
        <div class="subtitulo">
          <?= $subtitulo ?>
        </div>
      <?php } ?>
      <?php if ($texto) { ?>
        <div class="texto">
          <?= $texto ?>
        </div>
      <?php } ?>
      <?php if ($texto_ampliable) { ?>
        <div class="open">
        <img src="<?= get_stylesheet_directory_uri(); ?>/assets/images/arrow.svg" alt="">
        </div>
        <div class="border_seo"></div>
      <?php } ?>
      <div class="seoclosed">
        <?php if ($texto_ampliable) { ?>
          <div class="texto">
            <?= $texto_ampliable ?>
          </div>
        <?php } ?>
      </div>
    </div>
  </div>
<?php
}
