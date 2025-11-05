<?php
add_action('acf/include_fields', function () {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    acf_add_local_field_group(array(
        'key' => 'group_649009d21dd0a',
        'title' => 'Slider opiniones',
        'fields' => array(
            array(
                'key' => 'field_649009f6aad93',
                'label' => 'Opiniones',
                'name' => 'opiniones',
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
                'layout' => 'row',
                'pagination' => 1,
                'rows_per_page' => 1,
                'min' => 0,
                'max' => 0,
                'collapsed' => '',
                'button_label' => 'Agregar Fila',
                'sub_fields' => array(
                    array(
                        'key' => 'field_649009d2aad92',
                        'label' => 'Foto opinion',
                        'name' => 'foto_opinion',
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
                        'library' => 'all',
                        'min_width' => '',
                        'min_height' => '',
                        'min_size' => '',
                        'max_width' => '',
                        'max_height' => '',
                        'max_size' => '',
                        'mime_types' => '',
                        'preview_size' => 'medium',
                        'parent_repeater' => 'field_649009f6aad93',
                    ),
                    array(
                        'key' => 'field_64900a15aad94',
                        'label' => 'Fecha opinion',
                        'name' => 'fecha_opinion',
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
                        'parent_repeater' => 'field_649009f6aad93',
                    ),
                    array(
                        'key' => 'field_64900a23aad95',
                        'label' => 'Nombre opinion',
                        'name' => 'nombre_opinion',
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
                        'parent_repeater' => 'field_649009f6aad93',
                    ),
                    array(
                        'key' => 'field_64900a38aad96',
                        'label' => 'Texto opinion',
                        'name' => 'texto_opinion',
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
                        'maxlength' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'parent_repeater' => 'field_649009f6aad93',
                    ),
                ),
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/opiniones',
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



function opiniones_acf()
{
    acf_register_block_type([
        'name'        => 'opiniones',
        'title'        => __('Slider opiniones', 'tictac'),
        'description'    => __('Splide requerido', 'tictac'),
        'render_callback'  => 'opiniones_slider',
        'mode'        => 'preview',
        'icon'        => 'star-filled',
        'keywords'      => ['custom', 'opiniones', 'bloque', 'slider'],
    ]);
}

add_action('acf/init', 'opiniones_acf');

function opiniones_scripts()
{
    if (!is_admin()) {
        wp_enqueue_style('opiniones', get_stylesheet_directory_uri() . '/assets/functions/blocks/opiniones/opiniones.min.css');
    }
}
add_action('wp_enqueue_scripts', 'opiniones_scripts');

function opiniones_slider()
{
    if (true) {
        $nombreArchivo = plugin_dir_path(__FILE__) . 'reviews.bin';
        $option = array(
            'google_maps_review_cid' => '1050392466598184992',
            'show_only_if_with_text' => false,
            'show_only_if_greater_x' => 4,
            'show_rule_after_review' => false,
            'show_blank_star_till_5' => false,
            'your_language_for_tran' => 'es',
            'sort_by_reating_best_1' => true,
            'show_cname_as_headline' => false,
            'show_age_of_the_review' => true,
            'show_txt_of_the_review' => true,
            'show_author_of_reviews' => true,
            'show_author_avatar_img' => true,
        );

        // Comprobar si el archivo existe y si la fecha de modificación tiene antigüedad de 7 días
        if (!file_exists($nombreArchivo) || (file_exists($nombreArchivo) && (filemtime($nombreArchivo) < strtotime('-7 days')))) {
            $ch = curl_init('https://www.google.com/maps?cid=' . $option['google_maps_review_cid'] . '');
            if (isset($option['your_language_for_tran']) and !empty($option['your_language_for_tran'])) {
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept-Language: ' . $option['your_language_for_tran']));
            }
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.116 Safari/537.36');
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
            $result = curl_exec($ch);
            curl_close($ch);
            $pattern = '/window\.APP_INITIALIZATION_STATE(.*);window\.APP_FLAGS=/ms';
            if (preg_match($pattern, $result, $match)) {
                $match[1] = trim($match[1], ' =;');
                $reviews  = json_decode($match[1]);
                $reviews  = ltrim($reviews[3][6], ")]}'");
                $reviews  = json_decode($reviews);
                $customer = $reviews[6][11];
                $reviews  = $reviews[6][52][0];
            }
            file_put_contents($nombreArchivo, serialize($reviews));
        }
        if (file_exists($nombreArchivo)) {
            $reviews = unserialize(file_get_contents($nombreArchivo));
        }
        if (isset($reviews)) {
            if (isset($option['sort_by_reating_best_1']) and $option['your_language_for_tran'] == true)
                array_multisort(array_map(function ($element) {
                    return $element[4];
                }, $reviews), SORT_DESC, $reviews);
?>
            <section class="splide slider_opiniones google my-5" aria-label="Slider Opiniones">
                <!-- <div class="background"></div> -->
                <div class="splide__track container">
                    <ul class="splide__list">
                        <?php
                        $flag_count = 1;
                        foreach ($reviews as $review) {
                            $resena = $review[3];
                            $resena_short = strlen($resena) > 130 ? substr($resena,0,30000)."..." : $resena;
                            $starts = "";
                            for ($i = 1; $i <= $review[4]; ++$i) $starts .= '⭐';
                            $nombre = $review[0][1];
                            $tiempo = $review[1];
                            $foto = $review[0][2];
                            $flag = false;
                            if ($review[4] >= $option['show_only_if_greater_x']) {
                                $flag = true;
                            }
                            if ($flag && $flag_count < 4) {
                        ?>
                                <li class="splide__slide">
                                    <div class="content d-flex flex-wrap">
                                        <div class="col-12">
                                            <div class="row d-flex justify-content-center align-items-center align-content-center">
                                                <div class="foto my-2 mb-3 col-12 d-flex justify-content-center">
                                                    <img src="<?= $foto; ?>" alt="<?= $nombre; ?>">
                                                </div>
                                                <div class="stars col-12 mb-3 text-center d-flex justify-content-center">
                                                    <span class="icon icon-stars"></span>
                                                </div>
                                                <div class="datos col-12 text-center d-flex justify-content-center">
                                                    <!-- div class="fecha"><?= $tiempo; ?></div -->
                                                    <div class="nombre"><?= $nombre; ?></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 text-center py-2 px-5 texto-resena">
                                            <?= $resena_short; ?>
                                        </div>
                                    </div>
                                </li>
                        <?php
                            }
                            $flag_count++;
                        }
                        ?>
                    </ul>
                </div>
            </section>
<?php
        }
    }
}
