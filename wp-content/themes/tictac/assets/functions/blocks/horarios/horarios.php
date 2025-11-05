<?php
add_action('acf/include_fields', function () {
    if (! function_exists('acf_add_local_field_group')) {
        return;
    }

    acf_add_local_field_group(array(
        'key' => 'group_horarios_001',
        'title' => 'horarios',
        'fields' => array(
            array(
                'key' => 'field_horarios_titulo',
                'label' => 'titulo_horarios',
                'name' => 'titulo_horarios',
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
                'default_value' => '',
                'maxlength' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
            ),
            array(
                'key' => 'field_horarios_parrafo',
                'label' => 'parrafo_horarios',
                'name' => 'parrafo_horarios',
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
                'maxlength' => '',
                'rows' => 4,
                'new_lines' => 'wpautop',
            ),
            array(
                'key' => 'field_horarios_repetidor',
                'label' => 'repetidor_horarios',
                'name' => 'repetidor_horarios',
                'aria-label' => '',
                'type' => 'repeater',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'layout' => 'table',
                'pagination' => 0,
                'min' => 0,
                'max' => 0,
                'collapsed' => '',
                'button_label' => 'Agregar Horario',
                'rows_per_page' => 20,
                'sub_fields' => array(
                    array(
                        'key' => 'field_horarios_hora_salida',
                        'label' => 'hora_salida',
                        'name' => 'hora_salida',
                        'aria-label' => '',
                        'type' => 'time_picker',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'display_format' => 'H:i',
                        'return_format' => 'H:i',
                        'parent_repeater' => 'field_horarios_repetidor',
                    ),
                    array(
                        'key' => 'field_horarios_hora_regreso',
                        'label' => 'hora_regreso',
                        'name' => 'hora_regreso',
                        'aria-label' => '',
                        'type' => 'time_picker',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'display_format' => 'H:i',
                        'return_format' => 'H:i',
                        'parent_repeater' => 'field_horarios_repetidor',
                    ),
                    array(
                        'key' => 'field_horarios_es_exclusivo',
                        'label' => 'horario_exclusivo_temporada',
                        'name' => 'horario_exclusivo_temporada',
                        'aria-label' => '',
                        'type' => 'true_false',
                        'instructions' => 'Marcar si es horario exclusivo de temporada',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'message' => '',
                        'default_value' => 0,
                        'ui_on_text' => 'Sí',
                        'ui_off_text' => 'No',
                        'ui' => 1,
                        'parent_repeater' => 'field_horarios_repetidor',
                    )
                ),
            ),
            array(
                'key' => 'field_horarios_texto_boton',
                'label' => 'texto_boton',
                'name' => 'texto_boton',
                'aria-label' => '',
                'type' => 'text',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '50',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => 'RESERVA YA TU BILLETE DE BUS',
                'maxlength' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
            ),
            array(
                'key' => 'field_horarios_enlace_boton',
                'label' => 'enlace_boton',
                'name' => 'enlace_boton',
                'aria-label' => '',
                'type' => 'url',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '50',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'placeholder' => 'https://ejemplo.com',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/horarios',
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
});

function horarios_acf()
{
    acf_register_block_type([
        'name'        => 'horarios',
        'title'        => __('horarios', 'tictac'),
        'description'    => __('Bloque con título, párrafo, tabla de horarios y botón', 'tictac'),
        'render_callback'  => 'horarios',
        'mode'        => 'preview',
        'icon'        => 'clock',
        'keywords'      => ['custom', 'horarios', 'tabla', 'autobus'],
    ]);
}

add_action('acf/init', 'horarios_acf');

function horarios_scripts()
{
    if (!is_admin()) {
        wp_enqueue_style('horarios', get_stylesheet_directory_uri() . '/assets/functions/blocks/horarios/horarios.min.css');
    }
}
add_action('wp_enqueue_scripts', 'horarios_scripts');

function horarios($block)
{
    $titulo = get_field("titulo_horarios");
    $parrafo = get_field("parrafo_horarios");
    $horarios = get_field("repetidor_horarios");
    $texto_boton = get_field("texto_boton");
    $enlace_boton = get_field("enlace_boton");
?>
    <div class="container horarios">
        <div class="horarios-content">
            <?php if ($titulo): ?>
                <h2 class="horarios-titulo"><?= $titulo ?></h2>
            <?php endif; ?>
            
            <?php if ($parrafo): ?>
                <div class="horarios-parrafo"><?= wpautop($parrafo) ?></div>
            <?php endif; ?>
        </div>

        <?php if ($horarios) : ?>
            <div class="horarios-tabla-container">
                <table class="horarios-tabla">
                    <thead>
                        <tr>
                            <th class="hora-salida-header">HORA SALIDA</th>
                            <th class="hora-regreso-header">HORA REGRESO</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($horarios as $horario) : ?>
                            <tr class="horario-fila <?= $horario['horario_exclusivo_temporada'] ? 'horario-exclusivo' : '' ?>">
                                <td class="hora-salida">
                                    <?= $horario['hora_salida'] ? $horario['hora_salida'] : '' ?>
                                </td>
                                <td class="hora-regreso">
                                    <?= $horario['hora_regreso'] ? $horario['hora_regreso'] : '' ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <div class="horarios-leyenda">
                    <div class="leyenda-item">
                        <span class="leyenda-color exclusivo"></span>
                        <span class="leyenda-texto">Horario Exclusivo de Temporada</span>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($texto_boton && $enlace_boton): ?>
            <div class="horarios-boton-container">
                <a href="<?= esc_url($enlace_boton) ?>" class="horarios-boton" target="_blank">
                    <?= esc_html($texto_boton) ?>
                </a>
            </div>
        <?php endif; ?>
    </div>

    <style>
        
    </style>
<?php
}