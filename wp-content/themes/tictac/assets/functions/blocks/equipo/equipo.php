<?php
if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
        'key' => 'group_equipo',
        'title' => 'Información del equipo',
        'fields' => array(
            array(
                'key' => 'equipo_1',
                'label' => 'Título',
                'name' => 'titulo',
                'type' => 'text',
            ),
            array(
                'key' => 'equipo_2',
                'label' => 'Descripción',
                'name' => 'descripcion',
                'type' => 'textarea',
            ),
            array(
                'key' => 'equipo_3',
                'label' => 'Equipo',
                'name' => 'equipo',
                'type' => 'repeater',
                'sub_fields' => array(
                    array(
                        'key' => 'equipo_4',
                        'label' => 'Imagen',
                        'name' => 'imagen',
                        'type' => 'image',
                        'return_format' => 'url',
                    ),
                    array(
                        'key' => 'equipo_5',
                        'label' => 'Nombre',
                        'name' => 'nombre',
                        'type' => 'text',
                    ),
                    array(
                        'key' => 'equipo_6',
                        'label' => 'Puesto de trabajo',
                        'name' => 'puesto_trabajo',
                        'type' => 'text',
                    ),
                ),
            ),
            array(
                'key' => 'equipo_7',
                'label' => 'Enlace',
                'name' => 'enlace',
                'type' => 'link',
            ),
            array(
                'key' => 'equipo_8',
                'label' => 'Fondo de fila',
                'name' => 'fondo_fila',
                'type' => 'image',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/equipo',
                ),
            ),
        ),
    ));
}

function equipo_acf()
{
    acf_register_block_type(array(
        'name' => 'equipo',
        'title' => 'equipo',
        'description' => 'equipo',
        'category' => 'formatting',
        'icon' => 'star-filled',
        'keywords' => array('equipo', 'acf'),
        'render_callback' => 'equipo',
    ));
}
add_action('acf/init', 'equipo_acf');

function equipo(){
    $titulo = get_field('titulo');
    $descripcion = get_field('descripcion');
    $equipo = get_field('equipo');
    $enlace = get_field('enlace');
    $fondo_fila = get_field('fondo_fila');
    ?>
    <link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/functions/blocks/equipo/equipo.min.css">
    <div class="equipo py-5">
        <img class="fondo_fila" src="<?= $fondo_fila['url']; ?>" alt="<?= $fondo_fila['alt']; ?>">
        <div class="container">
            <div class="row d-flex justify-content-center">
                <div class="titulo-destacado"><?= $titulo; ?></div>
                <div class="descripcion"><?= $descripcion; ?></div>
                <div class="personal row d-flex justify-content-between">
                <?php
                    foreach ($equipo as $fila) {
                        $imagen = $fila['imagen'];
                        $nombre = $fila['nombre'];
                        $puesto = $fila['puesto_trabajo'];
                        
                        ?>
                        <div class="col-12 col-md-3 mb-4 text-center text-md-start">
                            <div class="persona">
                                <div class="img">
                                    <img src="<?= $imagen; ?>" alt="<?= $nombre; ?>">
                                </div>
                                <div class="nombre"><?= $nombre; ?></div>
                                <div class="puesto"><?= $puesto; ?></div>
                            </div>
                        </div>
                        <?php
                    }
                ?>
                </div>
                <div class="row justify-content-center"><a class="btn custom" href="<?= $enlace['url']; ?>"><?= $enlace['title']; ?></a></div>
            </div>
        </div>
    </div>
    <?php

}