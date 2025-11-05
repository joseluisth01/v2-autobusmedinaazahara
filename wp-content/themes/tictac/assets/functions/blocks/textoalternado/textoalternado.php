<?php
add_action('acf/include_fields', function () {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    acf_add_local_field_group(array(
        'key' => 'group_textoalternado_001',
        'title' => 'textoalternado',
        'fields' => array(
            array(
                'key' => 'field_textoalternado_titulo',
                'label' => 'Título principal',
                'name' => 'titulo_textoalternado',
                'type' => 'text',
            ),
            array(
                'key' => 'field_textoalternado_repetidor',
                'label' => 'Bloques alternados',
                'name' => 'bloques_alternados',
                'type' => 'repeater',
                'layout' => 'block',
                'button_label' => 'Agregar bloque',
                'sub_fields' => array(
                    array(
                        'key' => 'field_textoalternado_imagen',
                        'label' => 'Imagen',
                        'name' => 'imagen',
                        'type' => 'image',
                        'return_format' => 'array',
                        'library' => 'all',
                        'preview_size' => 'medium',
                    ),
                    array(
                        'key' => 'field_textoalternado_lado',
                        'label' => '¿Dónde aparece la imagen?',
                        'name' => 'lado_imagen',
                        'type' => 'select',
                        'choices' => array(
                            'izquierda' => 'Izquierda',
                            'derecha' => 'Derecha',
                        ),
                        'default_value' => 'izquierda',
                        'allow_null' => 0,
                        'multiple' => 0,
                    ),
                    array(
                        'key' => 'field_textoalternado_titulo_item',
                        'label' => 'Título del bloque',
                        'name' => 'titulo_item',
                        'type' => 'text',
                    ),
                    array(
                        'key' => 'field_textoalternado_parrafo_item',
                        'label' => 'Párrafo del bloque',
                        'name' => 'parrafo_item',
                        'type' => 'wysiwyg',
                    ),
                ),
            ),
            array(
                'key' => 'field_textoalternado_boton_texto',
                'label' => 'Texto del botón',
                'name' => 'boton_texto',
                'type' => 'text',
            ),
            array(
                'key' => 'field_textoalternado_boton_url',
                'label' => 'URL del botón',
                'name' => 'boton_url',
                'type' => 'url',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/textoalternado',
                ),
            ),
        ),
    ));
});

function textoalternado_acf()
{
    acf_register_block_type([
        'name' => 'textoalternado',
        'title' => __('textoalternado', 'tictac'),
        'description' => __('Bloques con imagen y texto izquierda/derecha personalizable', 'tictac'),
        'render_callback' => 'textoalternado_render',
        'mode' => 'preview',
        'icon' => 'align-left',
        'keywords' => ['bloques', 'alternados', 'imagen', 'texto'],
    ]);
}
add_action('acf/init', 'textoalternado_acf');

function textoalternado_render($block)
{
    $titulo = get_field("titulo_textoalternado");
    $bloques = get_field("bloques_alternados");
    $boton = get_field("boton_texto");
    $boton_url = get_field("boton_url");
?>
    <div class="container textoalternado">
        <?php if ($titulo): ?>
            <h2 class="textoalternado-titulo"><?= esc_html($titulo) ?></h2>
        <?php endif; ?>

        <?php if ($bloques): ?>
            <div class="textoalternado-wrapper">
                <?php foreach ($bloques as $item): 
                    $imagen = $item['imagen'];
                    $lado = $item['lado_imagen'];
                    $titulo_item = $item['titulo_item'];
                    $parrafo_item = $item['parrafo_item'];

                    $clase_lado = $imagen ? ($lado === 'derecha' ? 'invertido' : '') : '';
                ?>
                    <div class="bloque <?= $clase_lado ?>">
                        <?php if ($imagen): ?>
                            <div class="bloque-imagen">
                                <img src="<?= esc_url($imagen['url']) ?>" alt="<?= esc_attr($imagen['alt']) ?>">
                            </div>
                        <?php endif; ?>
                        
                        <div class="bloque-texto <?= !$imagen ? 'ancho-completo' : '' ?>">
                            <?php if ($titulo_item): ?>
                                <h3 class="bloque-titulo"><?= esc_html($titulo_item) ?></h3>
                            <?php endif; ?>
                            <?php if ($parrafo_item): ?>
                                <div class="bloque-parrafo"><?= wpautop($parrafo_item) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
                                
        <?php if ($boton && $boton_url): ?>
            <div class="horarios-boton-container">
                <a href="<?= esc_url($boton_url) ?>" class="horarios-boton"><?= esc_html($boton) ?></a>
            </div>
        <?php endif; ?>
    </div>

    <style>
        .textoalternado {
            box-shadow: 0px 0px 15px 0px #2E2D2C33;
    backdrop-filter: blur(3px);
    border-radius: 20px;
    padding: 50px;
    margin-top: 50px;
        }

        .textoalternado-titulo {
            text-align: center;
            font-size: 2.2rem;
            color: #871727;
            font-family: 'manhaj' !important;
            margin-bottom: 50px;
        }

        .textoalternado-wrapper {
            display: flex;
            flex-direction: column;
            gap: 60px;
        }

        .bloque {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 40px;
        }

        .bloque.invertido {
            flex-direction: row-reverse;
        }

        .bloque-imagen {
            flex: 1 1 45%;
        }

        .bloque-imagen img {
            width: 100%;
            border-radius: 15px;
        }

        .bloque-texto {
            flex: 1 1 50%;
        }

        .bloque-texto.ancho-completo {
            flex: 1 1 100%;
            text-align: center;
        }

        .bloque-titulo {
            font-size: 25px;
            color: #DB7461;
            font-family: 'Duran-Medium';
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 20px;
            text-align: center;
        }

        .bloque-parrafo {
            font-size: 1rem;
            color: #2E2D2C;
            line-height: 1.7;
        }

        .textoalternado-boton {
            text-align: center;
            margin-top: 50px;
        }

        .btn-alternado {
            background-color: #f0c53d;
            color: #000;
            padding: 15px 30px;
            border-radius: 10px;
            font-weight: bold;
            text-decoration: none;
            font-size: 1rem;
            transition: background 0.3s;
        }

        .btn-alternado:hover {
            background-color: #ddb62c;
        }

        @media (max-width: 768px) {
            .bloque {
                flex-direction: column !important;
                text-align: center;
            }

            .bloque-imagen, .bloque-texto {
                flex: 1 1 100%;
            }
        }
    </style>
<?php
}