<?php
if (function_exists('acf_add_local_field_group')) :

    acf_add_local_field_group(array(
        'key' => 'group_galeriamenu',
        'title' => 'Galería Menú',
        'fields' => array(
            array(
                'key' => 'field_titulo_galeriamenu',
                'label' => 'Título del Bloque',
                'name' => 'titulo_galeriamenu',
                'aria-label' => '',
                'type' => 'text',
                'instructions' => 'Título general del bloque (opcional)',
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
                'key' => 'field_menu_principal',
                'label' => 'Menú Principal',
                'name' => 'menu_principal',
                'aria-label' => '',
                'type' => 'group',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'layout' => 'block',
                'sub_fields' => array(
                    array(
                        'key' => 'field_imagen_principal',
                        'label' => 'Imagen',
                        'name' => 'imagen',
                        'aria-label' => '',
                        'type' => 'image',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'return_format' => 'array',
                        'preview_size' => 'medium',
                        'library' => 'all',
                    ),
                    array(
                        'key' => 'field_titulo_principal',
                        'label' => 'Título',
                        'name' => 'titulo',
                        'aria-label' => '',
                        'type' => 'text',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => 'MENÚ PRINCIPAL',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'maxlength' => '',
                    ),
                    array(
                        'key' => 'field_texto_boton_principal',
                        'label' => 'Texto del Botón',
                        'name' => 'texto_boton',
                        'aria-label' => '',
                        'type' => 'text',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => 'CARTA DE COMIDA',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'maxlength' => '',
                    ),
                    array(
                        'key' => 'field_pdf_principal',
                        'label' => 'Documento PDF',
                        'name' => 'pdf',
                        'aria-label' => '',
                        'type' => 'file',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'return_format' => 'array',
                        'library' => 'all',
                        'min_size' => '',
                        'max_size' => '',
                        'mime_types' => 'pdf',
                    ),
                ),
            ),
            array(
                'key' => 'field_menus_secundarios',
                'label' => 'Menús Secundarios',
                'name' => 'menus_secundarios',
                'aria-label' => '',
                'type' => 'repeater',
                'instructions' => 'Añadir menús secundarios (máximo 3)',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'collapsed' => '',
                'min' => 0,
                'max' => 3,
                'layout' => 'block',
                'button_label' => 'Añadir Menú',
                'sub_fields' => array(
                    array(
                        'key' => 'field_imagen_secundario',
                        'label' => 'Imagen',
                        'name' => 'imagen',
                        'aria-label' => '',
                        'type' => 'image',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'return_format' => 'array',
                        'preview_size' => 'medium',
                        'library' => 'all',
                    ),
                    array(
                        'key' => 'field_titulo_secundario',
                        'label' => 'Título',
                        'name' => 'titulo',
                        'aria-label' => '',
                        'type' => 'text',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => 'MENÚ SUSHI',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'maxlength' => '',
                    ),
                    array(
                        'key' => 'field_texto_boton_secundario',
                        'label' => 'Texto del Botón',
                        'name' => 'texto_boton',
                        'aria-label' => '',
                        'type' => 'text',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => 'CARTA SUSHI',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'maxlength' => '',
                    ),
                    array(
                        'key' => 'field_pdf_secundario',
                        'label' => 'Documento PDF',
                        'name' => 'pdf',
                        'aria-label' => '',
                        'type' => 'file',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'return_format' => 'array',
                        'library' => 'all',
                        'min_size' => '',
                        'max_size' => '',
                        'mime_types' => 'pdf',
                    ),
                ),
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/galeriamenu',
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

acf_register_block_type([
    'name'        => 'galeriamenu',
    'title'        => __('Galería Menú', 'tictac'),
    'description'    => __('Bloque para mostrar galería de menús con enlaces a PDFs', 'tictac'),
    'render_callback'  => 'galeriamenu_block',
    'mode'        => 'preview',
    'icon'        => 'food',
    'keywords'      => ['menu', 'carta', 'galeria', 'pdf'],
]);

function galeriamenu_block($block)
{
    $titulo = get_field('titulo_galeriamenu');
    $menu_principal = get_field('menu_principal');
    $menus_secundarios = get_field('menus_secundarios');

    // Clase adicional si viene del bloque
    $clase_adicional = '';
    if (isset($block['className'])) {
        $clase_adicional = $block['className'];
    }
?>
    <link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/functions/blocks/galeriamenu/galeriamenu.min.css">

    <div class="<?php echo $clase_adicional; ?> galeriamenu-block">
        <div class="container">
            <?php if ($titulo) : ?>
                <h2 class="titulo-bloque"><?php echo $titulo; ?></h2>
            <?php endif; ?>

            <div class="galeria-contenedor">
                <!-- Menú Principal (imagen grande) -->
                <?php if ($menu_principal && $menu_principal['imagen']) : ?>
                    <div class="menu-item menu-principal">
                        <div class="imagen-contenedor">
                            <img src="<?php echo esc_url($menu_principal['imagen']['url']); ?>" alt="<?php echo esc_attr($menu_principal['imagen']['alt'] ? $menu_principal['imagen']['alt'] : $menu_principal['titulo']); ?>" />
                            
                            <?php if ($menu_principal['titulo']) : ?>
                                <div class="overlay-titulo">
                                    <h3><?php echo $menu_principal['titulo']; ?></h3>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($menu_principal['pdf'] && $menu_principal['texto_boton']) : ?>
                                <div class="overlay-boton">
                                    <a href="<?php echo esc_url($menu_principal['pdf']['url']); ?>" target="_blank" class="btn-menu">
                                        <?php echo $menu_principal['texto_boton']; ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Menús Secundarios (imágenes pequeñas) -->
                <?php if ($menus_secundarios && count($menus_secundarios) > 0) : ?>
                    <div class="menus-secundarios">
                        <?php foreach ($menus_secundarios as $menu) : ?>
                            <?php if ($menu['imagen']) : ?>
                                <div class="menu-item menu-secundario">
                                    <div class="imagen-contenedor">
                                        <img src="<?php echo esc_url($menu['imagen']['url']); ?>" alt="<?php echo esc_attr($menu['imagen']['alt'] ? $menu['imagen']['alt'] : $menu['titulo']); ?>" />
                                        
                                        <?php if ($menu['titulo']) : ?>
                                            <div class="overlay-titulo">
                                                <h3><?php echo $menu['titulo']; ?></h3>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if ($menu['pdf'] && $menu['texto_boton']) : ?>
                                            <div class="overlay-boton">
                                                <a href="<?php echo esc_url($menu['pdf']['url']); ?>" target="_blank" class="btn-menu">
                                                    <?php echo $menu['texto_boton']; ?>
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php
}
?>