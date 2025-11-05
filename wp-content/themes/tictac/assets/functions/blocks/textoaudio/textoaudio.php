<?php
add_action('acf/include_fields', function () {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    acf_add_local_field_group(array(
        'key' => 'group_textoaudio_001',
        'title' => 'textoaudio',
        'fields' => array(
            array(
                'key' => 'field_textoaudio_titulo',
                'label' => 'Título principal',
                'name' => 'titulo_textoaudio',
                'type' => 'text',
            ),
            array(
                'key' => 'field_textoaudio_parrafo_principal',
                'label' => 'Párrafo principal',
                'name' => 'parrafo_principal_textoaudio',
                'type' => 'wysiwyg',
            ),
            array(
                'key' => 'field_textoaudio_subtitulo',
                'label' => 'Subtítulo',
                'name' => 'subtitulo_textoaudio',
                'type' => 'text',
            ),
            array(
                'key' => 'field_textoaudio_parrafo',
                'label' => 'Párrafo informativo',
                'name' => 'parrafo_textoaudio',
                'type' => 'wysiwyg',
            ),
            array(
                'key' => 'field_textoaudio_audio_imagen',
                'label' => 'Imagen del reproductor',
                'name' => 'imagen_audio_textoaudio',
                'type' => 'image',
                'return_format' => 'array',
                'library' => 'all',
                'preview_size' => 'medium',
            ),
            array(
                'key' => 'field_textoaudio_audio',
                'label' => 'Archivo de audio',
                'name' => 'audio_textoaudio',
                'type' => 'file',
                'return_format' => 'url',
                'library' => 'all',
                'mime_types' => 'mp3,wav',
            ),
            array(
                'key' => 'field_textoaudio_boton',
                'label' => 'Texto del botón',
                'name' => 'boton_textoaudio',
                'type' => 'text',
            ),
            array(
                'key' => 'field_textoaudio_boton_url',
                'label' => 'URL del botón',
                'name' => 'boton_textoaudio_url',
                'type' => 'url',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/textoaudio',
                ),
            ),
        ),
    ));
});

function textoaudio_acf()
{
    acf_register_block_type([
        'name' => 'textoaudio',
        'title' => __('textoaudio', 'tictac'),
        'description' => __('Bloque con h1, párrafos, imagen, reproductor de audio y botón', 'tictac'),
        'render_callback' => 'textoaudio_render',
        'mode' => 'preview',
        'icon' => 'format-audio',
        'keywords' => ['audio', 'imagen', 'bloque', 'custom'],
    ]);
}
add_action('acf/init', 'textoaudio_acf');

function textoaudio_scripts()
{
    if (!is_admin()) {
        wp_enqueue_style('textoaudio', get_stylesheet_directory_uri() . '/assets/functions/blocks/textoaudio/textoaudio.min.css');
    }
}
add_action('wp_enqueue_scripts', 'textoaudio_scripts');

function textoaudio_render($block)
{
    $titulo = get_field("titulo_textoaudio");
    $parrafo_principal = get_field("parrafo_principal_textoaudio");
    $subtitulo = get_field("subtitulo_textoaudio");
    $parrafo = get_field("parrafo_textoaudio");
    $imagen_audio = get_field("imagen_audio_textoaudio");
    $audio = get_field("audio_textoaudio");
    $boton = get_field("boton_textoaudio");
    $boton_url = get_field("boton_textoaudio_url");
?>
    <div class="container textoaudio">
        <?php if ($titulo): ?>
            <h1 class="textoaudio-titulo"><?= esc_html($titulo) ?></h1>
        <?php endif; ?>

        <?php if ($parrafo_principal): ?>
            <div class="textoaudio-parrafo-principal"><?= wpautop($parrafo_principal) ?></div>
        <?php endif; ?>
        
        <div class="textoaudio-contenido">
            <div class="textoaudio-texto">
                <?php if ($subtitulo): ?>
                    <div class="textoaudio-subtitulo"><?= esc_html($subtitulo) ?></div>
                <?php endif; ?>
                <?php if ($parrafo): ?>
                    <div class="textoaudio-parrafo"><?= wpautop($parrafo) ?></div>
                <?php endif; ?>
            </div>
            <div class="custom-audio-wrapper">
                <div class="custom-audio-player" data-audio="<?= esc_url($audio) ?>">
                    <div class="audio-image">
                        <img src="<?= esc_url($imagen_audio['url']) ?>" alt="<?= esc_attr($imagen_audio['alt']) ?>">
                    </div>
                    <div class="audio-controls">
    <button class="play-btn">
        <img class="icon-play" src="https://autobusmedinaazahara.com/wp-content/uploads/2025/07/Play.png" alt="Play">
    </button>
    <span class="time current">00:00</span><span class="barra"> / </span><span class="time duration">00:00</span>
    <div class="volume-wrapper">
        <img class="volume-icon" src="https://autobusmedinaazahara.com/wp-content/uploads/2025/07/altavoz-removebg-preview.png" alt="Volumen">
        <input type="range" class="volume-slider" min="0" max="1" step="0.01" value="1">
    </div>
</div>

                    <div class="progress-bar-container">
                        <div class="progress-bar"></div>
                    </div>
                </div>
            </div>


        </div>
        
        <?php if ($boton && $boton_url): ?>
            <div class="horarios-boton-container">
                <a href="<?= esc_url($boton_url) ?>" class="horarios-boton"><?= esc_html($boton) ?></a>
            </div>
        <?php endif; ?>
    </div>

    <style>
        .textoaudio {
            box-shadow: 0px 0px 15px 0px #2E2D2C33;
            backdrop-filter: blur(3px);
            border-radius: 20px;
            padding: 50px;
            margin-top: 50px;
        }

        .textoaudio-titulo {
            text-align: center;
            color: #871727;
            font-size: 2.8rem;
            font-family: 'manhaj' !important;
            margin-bottom: 20px;
        }

        .custom-audio-wrapper {
    background-color: #c7c5c56b;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 8px #00000020;
    width: 45%;
    margin: auto;
    padding: 30px;
}

.custom-audio-player {
    display: flex;
    flex-direction: column;
    align-items: stretch;
}

.audio-image img {
    width: 100%;
    height: 100px;
    object-fit: cover;
    border-radius: 5px;
}

.audio-controls {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 15px;
    flex-wrap: wrap;
    gap: 10px;
}

.play-btn {
    background: none;
    border: none;
    padding: 0;
    width: 50px;
    height: 50px;
    cursor: pointer;
}
.play-btn:hover{
    background-color: inherit !important;
}

.play-btn img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

.volume-icon {
    width: 24px;
    height: 24px;
    object-fit: contain;
}

.barra{
    font-size: 24px;
    color: black;
    font-family: 'Duran-Regular';
}
.time {
    font-size: 24px;
    color: black;
    font-family: 'Duran-Regular';
}

.volume-wrapper {
    display: flex;
    align-items: center;
    gap: 5px;
    flex: 1;
    justify-content: flex-end;
}


.volume-slider {
    -webkit-appearance: none;
    width: 100px;
    height: 8px;
    background: transparent;
    border-radius: 5px;
    outline: none;
}

.volume-slider::-webkit-slider-runnable-track {
    height: 8px;
    background: #ccc;
    border-radius: 5px;
}


/* Botón (thumb) - Webkit */
.volume-slider::-webkit-slider-thumb {
    -webkit-appearance: none;
    height: 20px;
    width: 20px;
    border-radius: 50%;
    background: #2E2D2C;
    cursor: pointer;
    margin-top: -6px;
    box-shadow: 0 0 2px rgba(0,0,0,0.3);
}

/* Firefox */
.volume-slider::-moz-range-track {
    height: 8px;
    background: #ccc;
    border-radius: 5px;
}

.volume-slider::-moz-range-progress {
    background: #2E2D2C;
    height: 8px;
    border-radius: 5px;
}

.volume-slider::-moz-range-thumb {
    height: 20px;
    width: 20px;
    border-radius: 50%;
    background: #2E2D2C;
    cursor: pointer;
}

/* Edge / IE */
.volume-slider::-ms-track {
    height: 8px;
    background: transparent;
    border-color: transparent;
    color: transparent;
}

.volume-slider::-ms-fill-lower {
    background: #2E2D2C;
    border-radius: 5px;
}

.volume-slider::-ms-fill-upper {
    background: #ccc;
    border-radius: 5px;
}

.volume-slider::-ms-thumb {
    height: 20px;
    width: 20px;
    border-radius: 50%;
    background: #2E2D2C;
    cursor: pointer;
}


.progress-bar-container {
    margin-top: 10px;
    height: 6px;
    background-color: #ccc;
    border-radius: 3px;
    position: relative;
    overflow: hidden;
}

.progress-bar {
    height: 100%;
    background-color: #871727;
    width: 0%;
    transition: width 0.2s ease-in-out;
}



        .textoaudio-parrafo-principal {}

        .textoaudio-contenido {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 30px;
        }

        .textoaudio-texto {
            flex: 1 1 50%;
        }

        .textoaudio-subtitulo {
            color: #DB7461;
            font-size: 25px;
            margin-bottom: 15px;
            line-height: 1.3;
            font-family: 'Duran-Medium';
            text-transform: uppercase;
            letter-spacing: 4px;
            text-align: center !important;
        }

        .textoaudio-parrafo {
            font-size: 1rem;
            line-height: 1.6;
            color: #2E2D2C;
        }

        .textoaudio-audio {
            flex: 1 1 45%;
            text-align: center;
        }

        .textoaudio-imagen img {
            width: 100%;
            height: auto;
            border-radius: 10px;
            margin-bottom: 15px;
        }

        .textoaudio-audio audio {
            width: 100%;
            max-width: 100%;
        }

        .textoaudio-boton {
            margin-top: 40px;
            text-align: center;
        }

        .btn-bus {
            background-color: #f0c53d;
            color: #000;
            padding: 15px 30px;
            border-radius: 10px;
            font-weight: bold;
            text-decoration: none;
            font-size: 1rem;
            transition: background 0.3s;
        }

        .btn-bus:hover {
            background-color: #ddb62c;
        }

        @media (max-width: 768px) {
            .textoaudio-contenido {
                flex-direction: column;
                align-items: flex-start;
            }

            .textoaudio-audio,
            .textoaudio-texto {
                width: 100%;
            }
        }
    </style>
    <script>
document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll('.custom-audio-player').forEach(function (player) {
        const audio = new Audio(player.dataset.audio);
        const playBtn = player.querySelector('.play-btn');
        const playIcon = playBtn.querySelector('img');
        const progressBar = player.querySelector('.progress-bar');
        const currentTime = player.querySelector('.current');
        const durationTime = player.querySelector('.duration');
        const volumeSlider = player.querySelector('.volume-slider');

        let isPlaying = false;

        playBtn.addEventListener('click', function () {
            if (isPlaying) {
                audio.pause();
                playIcon.src = "https://autobusmedinaazahara.com/wp-content/uploads/2025/07/Play.png";
            } else {
                audio.play();
                playIcon.src = "https://autobusmedinaazahara.com/wp-content/uploads/2025/07/Vector-14.png";
            }
            isPlaying = !isPlaying;
        });

        audio.addEventListener('loadedmetadata', function () {
            durationTime.textContent = formatTime(audio.duration);
        });

        audio.addEventListener('timeupdate', function () {
            currentTime.textContent = formatTime(audio.currentTime);
            const percent = (audio.currentTime / audio.duration) * 100;
            progressBar.style.width = percent + '%';
        });

        volumeSlider.addEventListener('input', function () {
    audio.volume = this.value;

    // Color dinámico de la barra (solo WebKit compatible)
    const val = this.value * 100;
    this.style.background = `linear-gradient(to right, #2E2D2C ${val}%, #ccc ${val}%)`;
});


        function formatTime(seconds) {
            const minutes = Math.floor(seconds / 60);
            const secs = Math.floor(seconds % 60);
            return `${minutes}:${secs.toString().padStart(2, '0')}`;
        }
    });
});

    </script>


<?php
}
