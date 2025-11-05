<?php
if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
        'key' => 'group_equipo_2',
        'title' => 'Información del equipo',
        'fields' => array(
            array(
                'key' => 'equipo2_3',
                'label' => 'Equipo',
                'name' => 'equipo',
                'type' => 'repeater',
                'sub_fields' => array(
                    array(
                        'key' => 'equipo2_4',
                        'label' => 'Imagen',
                        'name' => 'imagen',
                        'type' => 'image',
                        'return_format' => 'url',
                    ),
                    array(
                        'key' => 'equipo2_5',
                        'label' => 'Nombre',
                        'name' => 'nombre',
                        'type' => 'text',
                    ),
                    array(
                        'key' => 'equipo2_6',
                        'label' => 'Puesto de trabajo',
                        'name' => 'puesto_trabajo',
                        'type' => 'text',
                    ),
                    array(
                        'key' => 'equipo2_7',
                        'label' => 'Descripcion',
                        'name' => 'descripcion',
                        'type' => 'textarea',
                    ),
                ),
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/equipo2',
                ),
            ),
        ),
    ));
}

function equipo2_acf()
{
    acf_register_block_type(array(
        'name' => 'equipo2',
        'title' => 'Nuestro equipo internas',
        'description' => 'equipo',
        'category' => 'formatting',
        'icon' => 'star-filled',
        'keywords' => array('equipo', 'acf'),
        'render_callback' => 'equipo2',
    ));
}
add_action('acf/init', 'equipo2_acf');

function equipo2(){
    $equipo = get_field('equipo');
    ?>
    <link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/functions/blocks/equipo2/equipo2.min.css">
    <div class="equipo pt-5 pb-1">
        <div class="container">
            <div class="row d-flex justify-content-center">
                <div class="personal row d-flex justify-content-between">
                <?php
                $count = 1;
                    foreach ($equipo as $fila) {
                        $imagen = $fila['imagen'];
                        $nombre = $fila['nombre'];
                        $puesto = $fila['puesto_trabajo'];
                        $descripcion = $fila['descripcion'];
                        $classes = "col-12 col-md-6 col-lg-4";
                        ?>
                        <div class="<?= $classes; ?> mb-4 text-center text-md-start d-flex justify-content-center">
                            <div class="persona">
                                <div class="img">
                                    <img src="<?= $imagen; ?>" alt="<?= $nombre; ?>">
                                </div>
                                <div class="nombre"><?= $nombre; ?></div>
                                <div class="puesto"><?= $puesto; ?></div>
                                <div class="descripcion"><?= $descripcion; ?></div>
                            </div>
                        </div>
                        <?php
                        $count++;
                    }
                ?>
                </div>
            </div>
        </div>
    </div>
    <?php

}