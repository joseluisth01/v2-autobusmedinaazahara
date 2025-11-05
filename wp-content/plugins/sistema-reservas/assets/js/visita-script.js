/**
 * JavaScript para gestión de reservas de visitas guiadas
 * Archivo: wp-content/plugins/sistema-reservas/assets/js/visita-script.js
 */

let serviceData = null;

jQuery(document).ready(function ($) {
    console.log('=== VISITA SCRIPT INICIALIZADO ===');

    // Verificar si estamos en la página de detalles
    if ($('#service-hero').length > 0) {
        loadServiceData();
    }

    // Verificar si estamos en la página de confirmación
    if ($('.confirmacion-visita-container').length > 0) {
        loadConfirmationData();
    }

    // Event listeners para cálculo de precio
    $('#adultos-visita, #ninos-visita, #ninos-menores-visita').on('input change', function () { // ✅ AÑADIDO #ninos-menores-visita
        calculateTotalPrice();
    });
});

function loadServiceData() {
    console.log('=== CARGANDO DATOS DEL SERVICIO ===');

    // ✅ GUARDAR LOCALIZADOR EN sessionStorage SI VIENE DE confirmacion-reserva
    const urlParams = new URLSearchParams(window.location.search);
    const localizadorFromReferrer = urlParams.get('localizador');

    if (localizadorFromReferrer) {
        sessionStorage.setItem('current_localizador', localizadorFromReferrer);
        console.log('✅ Localizador guardado:', localizadorFromReferrer);
    } else {
        // Intentar obtener de document.referrer si viene de confirmacion-reserva
        const referrer = document.referrer;
        if (referrer.includes('confirmacion-reserva')) {
            const referrerUrl = new URL(referrer);
            const locFromReferrer = referrerUrl.searchParams.get('localizador');
            if (locFromReferrer) {
                sessionStorage.setItem('current_localizador', locFromReferrer);
                console.log('✅ Localizador guardado desde referrer:', locFromReferrer);
            }
        }
    }

    try {
        const dataString = sessionStorage.getItem('selectedServiceData');

        if (!dataString) {
            console.error('No hay datos del servicio en sessionStorage');
            alert('Error: No se encontraron datos del servicio. Por favor, vuelve a seleccionar el servicio.');
            window.history.back();
            return;
        }

        serviceData = JSON.parse(dataString);
        console.log('Datos del servicio cargados:', serviceData);

        // ✅ CRÍTICO: Verificar y parsear idiomas_disponibles si es string
        if (serviceData.idiomas_disponibles) {
            console.log('🔍 idiomas_disponibles (tipo):', typeof serviceData.idiomas_disponibles);
            console.log('🔍 idiomas_disponibles (valor):', serviceData.idiomas_disponibles);

            // Si es string, parsearlo
            if (typeof serviceData.idiomas_disponibles === 'string') {
                try {
                    serviceData.idiomas_disponibles = JSON.parse(serviceData.idiomas_disponibles);
                    console.log('✅ Idiomas parseados correctamente:', serviceData.idiomas_disponibles);
                } catch (e) {
                    console.error('❌ Error parseando idiomas:', e);
                    serviceData.idiomas_disponibles = {};
                }
            }
        } else {
            console.log('⚠️ No hay idiomas_disponibles en serviceData');
            serviceData.idiomas_disponibles = {};
        }

        // Rellenar la página con los datos
        populateServicePage();

        // Calcular precio inicial
        calculateTotalPrice();

    } catch (error) {
        console.error('Error cargando datos del servicio:', error);
        alert('Error cargando los datos del servicio');
        window.history.back();
    }
}

function populateServicePage() {
    console.log('=== RELLENANDO PÁGINA ===');

    // Imagen de portada y título
    if (serviceData.portada_url) {
        jQuery('#hero-image').attr('src', serviceData.portada_url);
    } else {
        jQuery('#hero-image').attr('src', 'https://via.placeholder.com/1200x400?text=Visita+Guiada');
    }

    jQuery('#service-title').text(serviceData.titulo || serviceData.agency_name || 'VISITA GUIADA');

    // Fecha y hora de la reserva del autobús
    const fechaObj = new Date(serviceData.fecha + 'T00:00:00');
    const fechaFormateada = fechaObj.toLocaleDateString('es-ES', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });

    jQuery('#fecha-visita').text(fechaFormateada.charAt(0).toUpperCase() + fechaFormateada.slice(1));

    // Quitar segundos de la hora
    const horaInicio = serviceData.hora.substring(0, 5);
    jQuery('#hora-inicio').text(horaInicio);

    // Calcular hora de fin (sumar 3.5 horas)
    const horaInicioArr = serviceData.hora.split(':');
    const fechaFin = new Date(fechaObj);
    fechaFin.setHours(parseInt(horaInicioArr[0]) + 3);
    fechaFin.setMinutes(parseInt(horaInicioArr[1]) + 30);

    const horaFin = fechaFin.toLocaleTimeString('es-ES', {
        hour: '2-digit',
        minute: '2-digit'
    });

    jQuery('#fecha-fin').text(fechaFormateada.charAt(0).toUpperCase() + fechaFormateada.slice(1));
    jQuery('#hora-fin').text(horaFin);

    // ✅ NUEVA LÓGICA PARA IDIOMAS (CORREGIDA)
    let idiomasHTML = '';
    let idiomasDisponibles = []; // ✅ CAMBIO: Empezar vacío, NO con español por defecto

    if (serviceData.idiomas_disponibles) {
        let idiomas = {};
        try {
            idiomas = typeof serviceData.idiomas_disponibles === 'string'
                ? JSON.parse(serviceData.idiomas_disponibles)
                : serviceData.idiomas_disponibles;

            console.log('✅ Idiomas parseados:', idiomas);
        } catch (e) {
            console.error('❌ Error parseando idiomas:', e);
            idiomas = {};
        }

        // Obtener día de la semana de la fecha de reserva
        const diasSemana = ['domingo', 'lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];
        const diaNombre = diasSemana[fechaObj.getDay()];

        console.log('📅 Día de la semana:', diaNombre);
        console.log('🌍 Idiomas configurados:', idiomas);

        // ✅ OBTENER IDIOMAS SOLO DEL DÍA ESPECÍFICO
        if (idiomas && typeof idiomas === 'object' && idiomas[diaNombre]) {
            if (Array.isArray(idiomas[diaNombre]) && idiomas[diaNombre].length > 0) {
                idiomasDisponibles = idiomas[diaNombre];
                console.log('✅ Idiomas encontrados para', diaNombre, ':', idiomasDisponibles);
            }
        }
    }

    // ✅ SI NO HAY IDIOMAS CONFIGURADOS, USAR ESPAÑOL POR DEFECTO
    if (idiomasDisponibles.length === 0) {
        idiomasDisponibles = ['espanol'];
        console.log('⚠️ No hay idiomas configurados, usando español por defecto');
    }

    console.log('🎯 Idiomas finales a mostrar:', idiomasDisponibles);

    const idiomasConfig = {
        'espanol': {
            label: 'Español',
            flag: 'https://flagcdn.com/h20/es.png'
        },
        'ingles': {
            label: 'Inglés',
            flag: 'https://flagcdn.com/h20/gb.png'
        },
        'frances': {
            label: 'Francés',
            flag: 'https://flagcdn.com/h20/fr.png'
        }
    };

    // ✅ GENERAR HTML DEL SELECTOR CON BANDERAS
    if (idiomasDisponibles.length === 1) {
        // Solo hay un idioma: mostrar solo la bandera (sin selector)
        const idioma = idiomasDisponibles[0];
        const config = idiomasConfig[idioma] || { label: idioma, flag: '🏳️' };

        idiomasHTML = `
        <div class="person-selector" style="margin-top: 15px;">
        <label style="font-weight: 600;">IDIOMA DE LA VISITA</label>
        <div class="idiomas-selector-visual">
            ${idiomasDisponibles.map((idioma, index) => {
            const config = idiomasConfig[idioma] || { label: idioma, flag: '' };
            return `
                    <label class="idioma-option ${index === 0 ? 'selected' : ''}" data-idioma="${idioma}">
                        <input type="radio" name="idioma-visita" value="${idioma}" ${index === 0 ? 'checked' : ''} required style="display:none;">
                        <img src="${config.flag}" alt="${config.label}" style="width: 32px; height: 20px; border-radius: 3px; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                        <span style="font-size: 14px; font-weight: 600; margin-left: 8px;">${config.label}</span>
                    </label>
                `;
        }).join('')}
        </div>
        <input type="hidden" id="idioma-visita" value="${idiomasDisponibles[0]}">
    </div>
    `;
        console.log('✅ Idioma único mostrado:', idioma);
    } else {
        // Múltiples idiomas: selector con banderas
        idiomasHTML = `
        <div class="person-selector" style="margin-top: 15px;">
        <label style="font-weight: 600;">IDIOMA DE LA VISITA</label>
        <div class="idiomas-selector-visual">
            ${idiomasDisponibles.map((idioma, index) => {
            const config = idiomasConfig[idioma] || { label: idioma, flag: '' };
            return `
                    <label class="idioma-option ${index === 0 ? 'selected' : ''}" data-idioma="${idioma}">
                        <input type="radio" name="idioma-visita" value="${idioma}" ${index === 0 ? 'checked' : ''} required style="display:none;">
                        <img src="${config.flag}" alt="${config.label}" style="width: 32px; height: 20px; border-radius: 3px; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                        <span style="font-size: 14px; font-weight: 600; margin-left: 8px;">${config.label}</span>
                    </label>
                `;
        }).join('')}
        </div>
        <input type="hidden" id="idioma-visita" value="${idiomasDisponibles[0]}">
    </div>
    `;
        console.log('✅ Selector de idiomas generado con', idiomasDisponibles.length, 'opciones');
    }

    jQuery('#idioma-selector-container').html(idiomasHTML);

    // Event listener para el selector visual
    jQuery('.idioma-option').on('click', function () {
        jQuery('.idioma-option').removeClass('selected');
        jQuery(this).addClass('selected');
        jQuery(this).find('input[type="radio"]').prop('checked', true);
        jQuery('#idioma-visita').val(jQuery(this).data('idioma'));
    });
    console.log('✅ HTML de idiomas insertado en el DOM');

    // Precios dinámicos desde el servicio
    const precioAdulto = parseFloat(serviceData.precio_adulto) || 0;
    const precioNino = parseFloat(serviceData.precio_nino) || 0;
    const precioNinoMenor = parseFloat(serviceData.precio_nino_menor) || 0;

    jQuery('#precio-adulto-info').text(precioAdulto.toFixed(0) + '€');
    jQuery('#precio-nino-info').text(precioNino.toFixed(0) + '€');
    jQuery('#precio-nino-menor-info').text(precioNinoMenor.toFixed(0) + '€');

    console.log('✅ Página rellenada correctamente');

    // ✅ LLAMAR AL AUTORELLENO
    autoFillPersonasFromBusReservation();
}

/**
 * Calcular precio total de la visita CON FIRMA DIGITAL
 */
function calculateTotalPrice() {
    if (!serviceData) {
        console.log('No hay datos del servicio para calcular precio');
        return;
    }

    const adultos = parseInt(jQuery('#adultos-visita').val()) || 0;
    const ninos = parseInt(jQuery('#ninos-visita').val()) || 0;
    const ninosMenores = parseInt(jQuery('#ninos-menores-visita').val()) || 0;

    if (adultos + ninos + ninosMenores === 0) {
        jQuery('#total-visita').text('0,00€');
        window.precioCalculado = null;
        return;
    }

    // ✅ LLAMAR AL BACKEND PARA CALCULAR CON FIRMA
    jQuery.ajax({
        url: reservasVisitaAjax.ajax_url,
        type: 'POST',
        data: {
            action: 'calculate_visita_price_secure',
            nonce: reservasVisitaAjax.nonce,
            service_id: serviceData.id,
            adultos: adultos,
            ninos: ninos,
            ninos_menores: ninosMenores
        },
        success: function(response) {
            console.log('📊 Respuesta del servidor para precio:', response);
            
            if (response.success) {
                const precio = response.data;
                jQuery('#total-visita').text(formatPrice(precio.precio_final));
                
                // ✅ GUARDAR PRECIO VALIDADO CON FIRMA GLOBALMENTE
                window.precioCalculado = {
                    precio_final: precio.precio_final,
                    firma: precio.firma,
                    firma_data: precio.firma_data,
                    timestamp: precio.timestamp
                };
                
                console.log('✅ Precio calculado con firma guardado:', window.precioCalculado);
            } else {
                console.error('❌ Error calculando precio:', response);
                jQuery('#total-visita').text('0,00€');
                window.precioCalculado = null;
            }
        },
        error: function(error) {
            console.error('❌ Error AJAX calculando precio:', error);
            jQuery('#total-visita').text('0,00€');
            window.precioCalculado = null;
        }
    });
}


/**
 * Formatear precio
 */
function formatPrice(price) {
    const numPrice = parseFloat(price) || 0;
    return numPrice.toFixed(2).replace('.', ',') + '€';
}

/**
 * Autorellenar campos de personas desde la reserva del autobús
 */
function autoFillPersonasFromBusReservation() {
    console.log('=== INTENTANDO AUTORELLENAR PERSONAS DESDE RESERVA DE AUTOBÚS ===');

    try {
        // Intentar obtener datos de la reserva del autobús
        const busReservationString = sessionStorage.getItem('reservationData');

        if (!busReservationString) {
            console.log('No hay datos de reserva de autobús en sessionStorage');
            return;
        }

        const busReservation = JSON.parse(busReservationString);
        console.log('Datos de reserva de autobús encontrados:', busReservation);

        // Extraer cantidades de personas
        const adultos = parseInt(busReservation.adultos) || 0;
        const residentes = parseInt(busReservation.residentes) || 0;
        const ninos_5_12 = parseInt(busReservation.ninos_5_12) || 0;
        const ninos_menores = parseInt(busReservation.ninos_menores) || 0;

        // Calcular totales
        const totalAdultos = adultos + residentes; // Adultos + Residentes = Adultos para la visita
        const totalNinos = ninos_5_12;
        const totalNinosMenores = ninos_menores;

        console.log('Cantidades calculadas:');
        console.log('- Adultos (incluye residentes):', totalAdultos);
        console.log('- Niños (5-12 años):', totalNinos);
        console.log('- Niños menores (-5 años):', totalNinosMenores);

        // Establecer valor mínimo de 1 adulto
        const adultosValue = totalAdultos > 0 ? totalAdultos : 1;

        // Autorrellenar los campos
        jQuery('#adultos-visita').val(adultosValue);
        jQuery('#ninos-visita').val(totalNinos);
        jQuery('#ninos-menores-visita').val(totalNinosMenores);

        // Recalcular el precio total
        calculateTotalPrice();

        console.log('✅ Campos autorrellenados correctamente');

    } catch (error) {
        console.error('Error al autorellenar personas:', error);
    }
}

function processVisitaReservation() {
    console.log('=== PROCESANDO RESERVA DE VISITA ===');

    // Validar política de privacidad
    const privacyCheckbox = document.getElementById('privacy-policy-visita');
    if (!privacyCheckbox || !privacyCheckbox.checked) {
        alert('Debes aceptar la política de privacidad para continuar.');
        if (privacyCheckbox) privacyCheckbox.focus();
        return;
    }

    // Validar datos personales
    const nombre = jQuery('[name="nombre"]').val().trim();
    const apellidos = jQuery('[name="apellidos"]').val().trim();
    const email = jQuery('[name="email"]').val().trim();
    const telefono = jQuery('[name="telefono"]').val().trim();

    if (!nombre || nombre.length < 2) {
        alert('Por favor, introduce un nombre válido (mínimo 2 caracteres).');
        jQuery('[name="nombre"]').focus();
        return;
    }

    if (!apellidos || apellidos.length < 2) {
        alert('Por favor, introduce apellidos válidos (mínimo 2 caracteres).');
        jQuery('[name="apellidos"]').focus();
        return;
    }

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!email || !emailRegex.test(email)) {
        alert('Por favor, introduce un email válido.');
        jQuery('[name="email"]').focus();
        return;
    }

    if (!telefono || telefono.length < 9) {
        alert('Por favor, introduce un teléfono válido (mínimo 9 dígitos).');
        jQuery('[name="telefono"]').focus();
        return;
    }

    // Validar personas
    const adultos = parseInt(jQuery('#adultos-visita').val()) || 0;
    const ninos = parseInt(jQuery('#ninos-visita').val()) || 0;
    const ninosMenores = parseInt(jQuery('#ninos-menores-visita').val()) || 0;
    const idiomaSeleccionado = jQuery('#idioma-visita').val() || 'español';

    if (adultos < 1) {
        alert('Debe haber al menos un adulto en la reserva.');
        jQuery('#adultos-visita').focus();
        return;
    }

    console.log('✅ Todas las validaciones pasadas, preparando datos...');

    // Preparar datos para Redsys
    const reservationData = {
        action: 'process_visita_reservation',
        nonce: reservasVisitaAjax.nonce,
        service_id: serviceData.id,
        agency_id: serviceData.agency_id,
        fecha: serviceData.fecha,
        hora: serviceData.hora,
        adultos: adultos,
        ninos: ninos,
        ninos_menores: ninosMenores,
        nombre: nombre,
        apellidos: apellidos,
        email: email,
        telefono: telefono,
        idioma: idiomaSeleccionado
    };

    console.log('📤 Datos a enviar:', reservationData);

    // Deshabilitar botón
    const processBtn = jQuery('.complete-btn');
    const originalText = processBtn.text();
    processBtn.prop('disabled', true).text('Procesando...');

    // ✅ ENVIAR SOLICITUD AJAX
    jQuery.ajax({
        url: reservasVisitaAjax.ajax_url,
        type: 'POST',
        data: reservationData,
        success: function(response) {
            console.log('📡 Respuesta del servidor:', response);

            if (response.success) {
                console.log('✅ Formulario Redsys generado');
                
                // ✅ INSERTAR FORMULARIO CORRECTAMENTE EN EL DOM
                document.body.insertAdjacentHTML('beforeend', response.data);

                console.log('🏦 Formulario insertado en DOM');

                // ✅ VERIFICAR QUE SE INSERTÓ CORRECTAMENTE
                const insertedForm = document.getElementById('formulario_redsys');
                const insertedOverlay = document.getElementById('redsys-overlay');

                if (insertedForm && insertedOverlay) {
                    console.log('✅ Elementos encontrados en el DOM');
                    
                    // ✅ VERIFICAR QUE EL SCRIPT SE EJECUTÓ
                    setTimeout(() => {
                        if (document.getElementById('redsys-overlay')) {
                            console.log('⚠️ El formulario sigue visible, forzando envío manual...');
                            insertedForm.submit();
                        } else {
                            console.log('✅ El formulario se envió automáticamente');
                        }
                    }, 2000);
                } else {
                    console.error('❌ No se encontraron elementos del formulario después de insertar');
                    alert('Error procesando el pago. Por favor, inténtalo de nuevo.');
                    processBtn.prop('disabled', false).text(originalText);
                }

            } else {
                console.error('❌ Error en la respuesta:', response.data);
                alert('Error: ' + (response.data || 'Error desconocido'));
                processBtn.prop('disabled', false).text(originalText);
            }
        },
        error: function(xhr, status, error) {
            console.error('❌ Error AJAX:', error);
            console.error('Status:', status);
            console.error('Response:', xhr.responseText);
            
            let errorMessage = 'Error de conexión al generar el formulario de pago.';
            if (xhr.status === 403) {
                errorMessage += ' (Error 403: Acceso denegado)';
            } else if (xhr.status === 404) {
                errorMessage += ' (Error 404: URL no encontrada)';
            } else if (xhr.status === 500) {
                errorMessage += ' (Error 500: Error interno del servidor)';
            }
            
            errorMessage += '\n\nPor favor, inténtalo de nuevo.';
            alert(errorMessage);
            
            processBtn.prop('disabled', false).text(originalText);
        }
    });
}

// ✅ NUEVA FUNCIÓN AUXILIAR PARA PROCESAR LA RESERVA
function processVisitaReservationWithPrice() {
    console.log('=== PROCESANDO CON PRECIO VALIDADO ===');
    
    const adultos = parseInt(jQuery('#adultos-visita').val()) || 0;
    const ninos = parseInt(jQuery('#ninos-visita').val()) || 0;
    const ninosMenores = parseInt(jQuery('#ninos-menores-visita').val()) || 0;
    const idiomaSeleccionado = jQuery('#idioma-visita').val() || 'español';
    
    const nombre = jQuery('[name="nombre"]').val().trim();
    const apellidos = jQuery('[name="apellidos"]').val().trim();
    const email = jQuery('[name="email"]').val().trim();
    const telefono = jQuery('[name="telefono"]').val().trim();

    // ✅ PREPARAR DATOS PARA REDSYS
    const reservationData = {
        action: 'process_visita_reservation',
        nonce: reservasVisitaAjax.nonce,
        service_id: serviceData.id,
        agency_id: serviceData.agency_id,
        fecha: serviceData.fecha,
        hora: serviceData.hora,
        adultos: adultos,
        ninos: ninos,
        ninos_menores: ninosMenores,
        nombre: nombre,
        apellidos: apellidos,
        email: email,
        telefono: telefono,
        idioma: idiomaSeleccionado,
        precio_calculado: window.precioCalculado
    };

    console.log('📤 Datos a enviar:', reservationData);

    // Deshabilitar botón
    const processBtn = jQuery('.complete-btn');
    const originalText = processBtn.text();
    processBtn.prop('disabled', true).text('Procesando...');

    // ✅ ENVIAR SOLICITUD PARA GENERAR FORMULARIO REDSYS
    jQuery.ajax({
        url: reservasVisitaAjax.ajax_url,
        type: 'POST',
        data: reservationData,
        success: function(response) {
            console.log('📡 Respuesta del servidor:', response);

            if (response.success) {
                console.log('✅ Formulario Redsys generado');
                // Insertar y ejecutar formulario
                document.body.insertAdjacentHTML('beforeend', response.data);
            } else {
                console.error('❌ Error en la respuesta:', response.data);
                alert('Error: ' + (response.data || 'Error desconocido'));
                processBtn.prop('disabled', false).text(originalText);
            }
        },
        error: function(xhr, status, error) {
            console.error('❌ Error AJAX:', error);
            console.error('Status:', status);
            console.error('Response:', xhr.responseText);
            alert('Error de conexión. Por favor, inténtalo de nuevo.');
            processBtn.prop('disabled', false).text(originalText);
        }
    });
}

/**
 * Cargar datos de confirmación
 */
function loadConfirmationData() {
    console.log('=== CARGANDO DATOS DE CONFIRMACIÓN ===');

    try {
        const dataString = sessionStorage.getItem('visitaConfirmationData');

        if (!dataString) {
            console.error('No hay datos de confirmación en sessionStorage');
            return;
        }

        const data = JSON.parse(dataString);
        console.log('Datos de confirmación cargados:', data);

        // Rellenar datos
        jQuery('#conf-localizador').text(data.localizador || '-');

        // Formatear fecha
        const fechaObj = new Date(data.fecha + 'T00:00:00');
        const fechaFormateada = fechaObj.toLocaleDateString('es-ES', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        jQuery('#conf-fecha').text(fechaFormateada.charAt(0).toUpperCase() + fechaFormateada.slice(1));

        jQuery('#conf-hora').text(data.hora || '-');

        const totalPersonas = (data.adultos || 0) + (data.ninos || 0);
        jQuery('#conf-personas').text(totalPersonas + ' persona' + (totalPersonas !== 1 ? 's' : ''));

        jQuery('#conf-total').text((data.total || 0).toFixed(2) + '€');

        // Limpiar sessionStorage
        sessionStorage.removeItem('visitaConfirmationData');
        sessionStorage.removeItem('selectedServiceData');

        console.log('✅ Datos de confirmación cargados correctamente');

    } catch (error) {
        console.error('Error cargando datos de confirmación:', error);
    }
}

/**
 * Volver a servicios
 */
function goBackToServices() {
    console.log('=== VOLVIENDO A SERVICIOS ===');

    // ✅ OBTENER LOCALIZADOR DE MÚLTIPLES FUENTES
    let localizador = null;

    // Primero intentar desde URL
    const urlParams = new URLSearchParams(window.location.search);
    localizador = urlParams.get('localizador');

    if (!localizador) {
        // Intentar desde sessionStorage
        localizador = sessionStorage.getItem('current_localizador');
    }

    if (!localizador) {
        // Intentar desde datos del servicio
        try {
            const serviceDataString = sessionStorage.getItem('selectedServiceData');
            if (serviceDataString) {
                const serviceData = JSON.parse(serviceDataString);
                // Puede que esté guardado ahí
                localizador = serviceData.localizador;
            }
        } catch (error) {
            console.error('Error obteniendo localizador:', error);
        }
    }

    console.log('Localizador encontrado:', localizador);

    // ✅ CONSTRUIR URL CON LOCALIZADOR
    const currentPath = window.location.pathname;
    let targetUrl;

    if (currentPath.includes('/')) {
        const pathParts = currentPath.split('/').filter(part => part !== '');

        if (pathParts.length > 0 && pathParts[0] !== 'confirmacion-reserva') {
            targetUrl = window.location.origin + '/' + pathParts[0] + '/confirmacion-reserva/';
        } else {
            targetUrl = window.location.origin + '/confirmacion-reserva/';
        }
    } else {
        targetUrl = window.location.origin + '/confirmacion-reserva/';
    }

    // ✅ AÑADIR LOCALIZADOR A LA URL
    if (localizador) {
        targetUrl += '?localizador=' + localizador;
        console.log('✅ Redirigiendo con localizador:', targetUrl);
    } else {
        console.warn('⚠️ No se encontró localizador');
        alert('No se pudo encontrar los datos de tu reserva. Por favor, revisa tu email.');
        return;
    }

    // ✅ IMPORTANTE: Forzar recarga completa de la página
    window.location.href = targetUrl;
}

/**
 * Volver al inicio
 */
function goBackToInicio() {
    // ✅ CONSTRUIR URL DE INICIO CORRECTAMENTE
    const currentPath = window.location.pathname;
    let targetUrl;

    // Si estamos en un subdirectorio
    if (currentPath.includes('/')) {
        const pathParts = currentPath.split('/').filter(part => part !== '');

        // Si hay al menos una parte en la ruta (subdirectorio)
        if (pathParts.length > 0) {
            // Usar el primer segmento como base
            targetUrl = window.location.origin + '/' + pathParts[0] + '/';
        } else {
            // Estamos en la raíz
            targetUrl = window.location.origin + '/';
        }
    } else {
        // Estamos en la raíz
        targetUrl = window.location.origin + '/';
    }

    console.log('Volviendo al inicio:', targetUrl);
    window.location.href = targetUrl;
}

/**
 * Ver comprobante de visita (PENDIENTE - para próxima fase)
 */
function viewVisitaTicket() {
    alert('La función de visualización de comprobantes estará disponible próximamente.');
}

/**
 * Descargar comprobante de visita (PENDIENTE - para próxima fase)
 */
function downloadVisitaTicket() {
    alert('La función de descarga de comprobantes estará disponible próximamente.');
}


// Añadir al final del archivo, reemplazando las funciones placeholder

/**
 * ✅ VER COMPROBANTE DE VISITA
 */
function viewVisitaTicket() {
    console.log('🎫 Solicitando ver comprobante de visita');

    const localizador = window.visitaLocalizador;

    if (!localizador) {
        alert('No se encontró el localizador de la visita. Por favor, revisa tu email.');
        return;
    }

    showLoadingModal('Generando comprobante de visita...');
    generateAndViewVisitaPDF(localizador);
}

/**
 * ✅ DESCARGAR COMPROBANTE DE VISITA
 */
function downloadVisitaTicket() {
    console.log('⬇️ Solicitando descargar comprobante de visita');

    const localizador = window.visitaLocalizador;

    if (!localizador) {
        alert('No se encontró el localizador de la visita. Por favor, revisa tu email.');
        return;
    }

    showLoadingModal('Preparando descarga...');
    generateAndDownloadVisitaPDF(localizador);
}

/**
 * ✅ GENERAR Y VISUALIZAR PDF DE VISITA
 */
function generateAndViewVisitaPDF(localizador) {
    console.log('📋 Generando PDF de visita para visualización...');
    console.log('🔍 Localizador:', localizador);

    jQuery.ajax({
        url: reservasVisitaAjax.ajax_url,
        type: 'POST',
        data: {
            action: 'generate_visita_pdf_view',
            localizador: localizador,
            nonce: reservasVisitaAjax.nonce
        },
        success: function (response) {
            console.log('📡 Respuesta:', response);
            hideLoadingModal();

            if (response.success && response.data.pdf_url) {
                console.log('✅ PDF URL recibida:', response.data.pdf_url);
                console.log('📁 Archivo existe:', response.data.file_exists);
                console.log('📏 Tamaño:', response.data.file_size);

                // Abrir PDF en nueva ventana
                window.open(response.data.pdf_url, '_blank');
            } else {
                console.error('❌ Error en respuesta:', response);
                alert('Error generando el comprobante: ' + (response.data || 'Error desconocido'));
            }
        },
        error: function (xhr, status, error) {
            hideLoadingModal();
            console.error('❌ Error AJAX:', error);
            console.error('Response:', xhr.responseText);
            alert('Error de conexión al generar el comprobante');
        }
    });
}

/**
 * ✅ GENERAR Y DESCARGAR PDF DE VISITA
 */
function generateAndDownloadVisitaPDF(localizador) {
    console.log('⬇️ Generando PDF de visita para descarga...');
    console.log('🔍 Localizador:', localizador);

    jQuery.ajax({
        url: reservasVisitaAjax.ajax_url,
        type: 'POST',
        data: {
            action: 'generate_visita_pdf_download',
            localizador: localizador,
            nonce: reservasVisitaAjax.nonce
        },
        success: function (response) {
            console.log('📡 Respuesta:', response);
            hideLoadingModal();

            if (response.success && response.data.pdf_url) {
                console.log('✅ PDF URL recibida:', response.data.pdf_url);
                console.log('📁 Archivo existe:', response.data.file_exists);
                console.log('📏 Tamaño:', response.data.file_size);

                // Crear enlace de descarga
                const link = document.createElement('a');
                link.href = response.data.pdf_url;
                link.download = `billete_visita_${localizador}.pdf`;
                link.target = '_blank';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);

                console.log('✅ Descarga iniciada');
            } else {
                console.error('❌ Error en respuesta:', response);
                alert('Error preparando la descarga: ' + (response.data || 'Error desconocido'));
            }
        },
        error: function (xhr, status, error) {
            hideLoadingModal();
            console.error('❌ Error AJAX:', error);
            console.error('Response:', xhr.responseText);
            alert('Error de conexión al preparar la descarga');
        }
    });
}

/**
 * ✅ MOSTRAR MODAL DE CARGA
 */
function showLoadingModal(message) {
    let modal = document.getElementById('loading-modal-visita');
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'loading-modal-visita';
        modal.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
        `;

        const content = document.createElement('div');
        content.style.cssText = `
            background: white;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            max-width: 300px;
        `;

        content.innerHTML = `
            <div style="font-size: 24px; margin-bottom: 15px;">⏳</div>
            <div id="loading-message-visita" style="font-size: 16px; color: #333;">${message}</div>
        `;

        modal.appendChild(content);
        document.body.appendChild(modal);
    } else {
        document.getElementById('loading-message-visita').textContent = message;
        modal.style.display = 'flex';
    }
}

/**
 * ✅ OCULTAR MODAL DE CARGA
 */
function hideLoadingModal() {
    const modal = document.getElementById('loading-modal-visita');
    if (modal) {
        modal.style.display = 'none';
    }
}

// ✅ FUNCIÓN DE DEBUG - ELIMINAR DESPUÉS DE RESOLVER
window.debugServiceData = function () {
    console.log('=== DEBUG COMPLETO ===');
    console.log('serviceData completo:', JSON.stringify(serviceData, null, 2));
    console.log('idiomas_disponibles raw:', serviceData.idiomas_disponibles);
    console.log('Tipo:', typeof serviceData.idiomas_disponibles);
    console.log('Es NULL?:', serviceData.idiomas_disponibles === null);
    console.log('Es undefined?:', serviceData.idiomas_disponibles === undefined);
    console.log('Es string vacío?:', serviceData.idiomas_disponibles === '');

    const container = jQuery('#idioma-selector-container');
    console.log('Contenedor existe?:', container.length > 0);
    console.log('HTML del contenedor:', container.html());
};