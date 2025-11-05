<div class="reserva-rapida-container">
    <style>
        .reserva-rapida-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
            background: #FFFFFF;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .section-header {
            background: linear-gradient(135deg, #0073aa 0%, #005177 100%);
            color: white;
            padding: 25px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 30px;
        }

        .section-header h2 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .section-header p {
            margin: 10px 0 0 0;
            font-size: 16px;
            opacity: 0.9;
        }

        .agency-info {
            background: #E8F4F8;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            border-left: 4px solid #0073aa;
        }

        .agency-info h3 {
            margin: 0 0 15px 0;
            color: #0073aa;
            font-size: 18px;
            font-weight: 600;
        }

        .form-section {
            background: #F8F9FA;
            padding: 25px;
            border-radius: 8px;
            margin-bottom: 25px;
            border-left: 4px solid #0073aa;
        }

        .form-section h3 {
            margin: 0 0 20px 0;
            color: #0073aa;
            font-size: 18px;
            font-weight: 600;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            font-weight: 600;
            color: #2D2D2D;
            margin-bottom: 5px;
            font-size: 14px;
        }

        .form-group input,
        .form-group select {
            padding: 12px;
            border: 2px solid #E0E0E0;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #0073aa;
            box-shadow: 0 0 0 3px rgba(0, 115, 170, 0.1);
        }

        .personas-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
        }

        .persona-input {
            text-align: center;
            font-weight: 600;
        }

        .price-summary {
            background: #FFFFFF;
            padding: 20px;
            border-radius: 8px;
            border: 2px solid #EFCF4B;
            margin-top: 20px;
        }

        .price-row {
            display: flex;
            justify-content: space-between;
            margin: 10px 0;
            font-size: 16px;
        }

        .price-row.total {
            border-top: 2px solid #0073aa;
            padding-top: 15px;
            margin-top: 15px;
            font-weight: 700;
            font-size: 20px;
            color: #0073aa;
        }

        .discount-row {
            color: #E74C3C;
            font-weight: 600;
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
        }

        .btn {
            padding: 15px 30px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            min-width: 180px;
        }

        .btn-primary {
            background: #0073aa;
            color: white;
        }

        .btn-primary:hover {
            background: #005a87;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        .loading-state {
            text-align: center;
            padding: 20px;
            color: #666;
            font-style: italic;
        }

        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 6px;
            margin: 15px 0;
            border-left: 4px solid #dc3545;
        }

        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 6px;
            margin: 15px 0;
            border-left: 4px solid #28a745;
        }

        .service-info {
            background: #E3F2FD;
            padding: 15px;
            border-radius: 6px;
            margin-top: 10px;
            border-left: 4px solid #2196F3;
        }

        .service-info strong {
            color: #1976D2;
        }

        @media (max-width: 768px) {
            .reserva-rapida-container {
                margin: 10px;
                padding: 15px;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .personas-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .action-buttons {
                flex-direction: column;
            }
        }
    </style>

    <div class="section-header">
        <h2>⚡ RESERVA RÁPIDA</h2>
        <p>Sistema de reserva inmediata para agencias</p>
    </div>

    <!-- Información de la Agencia -->
    <div class="agency-info">
        <h3>🏢 Tu Agencia</h3>
        <div class="form-grid">
            <div>
                <strong>Agencia:</strong> <?php echo esc_html($_SESSION['reservas_user']['agency_name'] ?? 'No disponible'); ?>
            </div>
            <div>
                <strong>Usuario:</strong> <?php echo esc_html($_SESSION['reservas_user']['username'] ?? 'No disponible'); ?>
            </div>
            <div>
                <strong>Email:</strong> <?php echo esc_html($_SESSION['reservas_user']['email'] ?? 'No disponible'); ?>
            </div>
            <div>
                <strong>Comisión:</strong> <?php echo number_format($_SESSION['reservas_user']['commission_percentage'] ?? 0, 1); ?>%
            </div>
        </div>
    </div>

    <form id="agency-reserva-rapida-form">
        <!-- Sección de Cliente -->
        <div class="form-section">
            <h3>👤 Datos del Cliente</h3>
            <div class="form-grid">
                <div class="form-group">
                    <label for="nombre">Nombre *</label>
                    <input type="text" id="nombre" name="nombre" required>
                </div>
                <div class="form-group">
                    <label for="apellidos">Apellidos *</label>
                    <input type="text" id="apellidos" name="apellidos" required>
                </div>
                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="telefono">Teléfono *</label>
                    <input type="tel" id="telefono" name="telefono" required>
                </div>
            </div>
        </div>

        <!-- Sección de Servicio -->
        <div class="form-section">
            <h3>📅 Selección de Servicio</h3>
            <div class="form-group">
                <label for="service_id">Servicio disponible *</label>
                <select id="service_id" name="service_id" required>
                    <option value="">Cargando servicios disponibles...</option>
                </select>
            </div>
            <div id="service-info" class="service-info" style="display: none;">
                <strong>Información del servicio:</strong>
                <div id="service-details"></div>
            </div>
        </div>

        <!-- Sección de Personas -->
        <div class="form-section">
            <h3>👥 Distribución de Personas</h3>
            <div class="personas-grid">
                <div class="form-group">
                    <label for="adultos">Adultos</label>
                    <input type="number" id="adultos" name="adultos" min="0" max="50" value="0" class="persona-input">
                </div>
                <div class="form-group">
                    <label for="residentes">Residentes</label>
                    <input type="number" id="residentes" name="residentes" min="0" max="50" value="0" class="persona-input">
                </div>
                <div class="form-group">
                    <label for="ninos_5_12">Niños (5-12 años)</label>
                    <input type="number" id="ninos_5_12" name="ninos_5_12" min="0" max="50" value="0" class="persona-input">
                </div>
                <div class="form-group">
                    <label for="ninos_menores">Niños (-5 años)</label>
                    <input type="number" id="ninos_menores" name="ninos_menores" min="0" max="50" value="0" class="persona-input">
                </div>
            </div>

            <!-- Resumen de Precios -->
            <div id="price-summary" class="price-summary" style="display: none;">
                <h4 style="margin: 0 0 15px 0; color: #0073aa;">💰 Resumen de Precios</h4>
                <div class="price-row">
                    <span>Precio base:</span>
                    <span id="precio-base">0.00€</span>
                </div>
                <div class="price-row discount-row" id="discount-row" style="display: none;">
                    <span>Descuentos:</span>
                    <span id="total-descuentos">-0.00€</span>
                </div>
                <div class="price-row total">
                    <span>TOTAL:</span>
                    <span id="precio-total">0.00€</span>
                </div>
                <div id="discount-details" style="margin-top: 15px; font-size: 14px; color: #666;"></div>
            </div>
        </div>

        <!-- Botones de Acción -->
        <div class="action-buttons">
            <button type="button" class="btn btn-secondary" onclick="cancelAgencyReservaRapida()">
                ❌ Cancelar
            </button>
            <button type="submit" class="btn btn-primary" id="submit-btn" disabled>
                ⚡ Procesar Reserva Rápida
            </button>
        </div>

        <!-- Mensajes -->
        <div id="form-messages"></div>
    </form>
</div>

<script>
    const reservasAjax = {
    ajax_url: '<?php echo admin_url('admin-ajax.php'); ?>',
    nonce: '<?php echo wp_create_nonce('reservas_nonce'); ?>'
};

// ✅ DATOS DE SERVICIOS DESDE PHP
const serviciosDisponibles = <?php 
    $servicios_organizados = array();
    if (!empty($servicios_disponibles)) {
        foreach ($servicios_disponibles as $servicio) {
            if (!isset($servicios_organizados[$servicio->fecha])) {
                $servicios_organizados[$servicio->fecha] = array();
            }
            $servicios_organizados[$servicio->fecha][] = array(
                'id' => $servicio->id,
                'hora' => substr($servicio->hora, 0, 5),
                'plazas_disponibles' => $servicio->plazas_disponibles,
                'precio_adulto' => $servicio->precio_adulto,
                'precio_nino' => $servicio->precio_nino,
                'precio_residente' => $servicio->precio_residente
            );
        }
    }
    echo json_encode($servicios_organizados);
?>;

console.log('=== VARIABLES INICIALIZADAS ===');
console.log('AJAX URL:', reservasAjax.ajax_url);
console.log('Nonce:', reservasAjax.nonce);
console.log('Servicios disponibles:', serviciosDisponibles);

document.addEventListener('DOMContentLoaded', function() {
    initializeAgencyReservaRapida();
});


function initializeAgencyReservaRapida() {
    console.log('=== INICIALIZANDO RESERVA RÁPIDA AGENCIA ===');
    
    // Debug completo del entorno
    console.log('Window location:', window.location.href);
    console.log('reservasAjax disponible:', typeof reservasAjax !== 'undefined');
    
    if (typeof reservasAjax !== 'undefined') {
        console.log('reservasAjax.ajax_url:', reservasAjax.ajax_url);
        console.log('reservasAjax.nonce:', reservasAjax.nonce);
    } else {
        console.error('❌ reservasAjax NO ESTÁ DEFINIDO - Este es el problema principal');
        showError('Error de configuración: Variables AJAX no disponibles. Recarga la página.');
        return;
    }
    
    console.log('jQuery disponible:', typeof jQuery !== 'undefined');
    console.log('Service select encontrado:', document.getElementById('service_id') !== null);
    
    // Test de conectividad inmediato
    testAjaxConnection();
    
    // Cargar servicios disponibles
    setTimeout(() => {
        loadAvailableServices();
    }, 500);
    
    // Event listeners
    document.getElementById('service_id').addEventListener('change', handleServiceChange);
    
    // Event listeners para cálculo de precios
    const personInputs = ['adultos', 'residentes', 'ninos_5_12', 'ninos_menores'];
    personInputs.forEach(inputId => {
        document.getElementById(inputId).addEventListener('input', calculatePrice);
    });
    
    // Event listener para el formulario
    document.getElementById('agency-reserva-rapida-form').addEventListener('submit', handleFormSubmit);
}

function testAjaxConnection() {
    console.log('=== AJAX CONNECTION TEST ===');
    console.log('✅ Variables disponibles');
    console.log('AJAX URL:', reservasAjax.ajax_url);
    console.log('Nonce válido:', reservasAjax.nonce ? 'SÍ' : 'NO');
    console.log('Servicios cargados:', Object.keys(serviciosDisponibles).length, 'fechas');
}

function loadAvailableServices() {
    console.log('=== CARGANDO SERVICIOS DISPONIBLES (DESDE PHP) ===');
    
    const serviceSelect = document.getElementById('service_id');
    serviceSelect.innerHTML = '<option value="">Selecciona un servicio</option>';
    
    if (!serviciosDisponibles || typeof serviciosDisponibles !== 'object') {
        console.error('❌ No hay servicios disponibles');
        serviceSelect.innerHTML = '<option value="">No hay servicios disponibles</option>';
        showError('No hay servicios disponibles. Contacta con el administrador.');
        return;
    }
    
    populateServiceSelect(serviciosDisponibles);
}

function populateServiceSelect(serviciosData) {
    console.log('=== POBLANDO SELECT DE SERVICIOS ===');
    console.log('Datos recibidos:', serviciosData);
    
    const serviceSelect = document.getElementById('service_id');
    serviceSelect.innerHTML = '<option value="">Selecciona un servicio</option>';
    
    if (!serviciosData || typeof serviciosData !== 'object') {
        console.error('❌ Datos de servicios inválidos:', serviciosData);
        serviceSelect.innerHTML = '<option value="">Error: Datos inválidos</option>';
        return;
    }
    
    const fechas = Object.keys(serviciosData);
    console.log('Fechas disponibles:', fechas);
    
    if (fechas.length === 0) {
        serviceSelect.innerHTML = '<option value="">No hay servicios disponibles en los próximos días</option>';
        showError('No hay servicios disponibles. Contacta con el administrador.');
        return;
    }
    
    // Ordenar fechas
    fechas.sort();
    
    fechas.forEach(fecha => {
        const serviciosDia = serviciosData[fecha];
        
        if (!Array.isArray(serviciosDia) || serviciosDia.length === 0) {
            console.warn('⚠️ No hay servicios para la fecha:', fecha);
            return;
        }
        
        try {
            const fechaObj = new Date(fecha + 'T00:00:00');
            const fechaFormateada = fechaObj.toLocaleDateString('es-ES', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            
            // Capitalizar primera letra
            const fechaCapitalizada = fechaFormateada.charAt(0).toUpperCase() + fechaFormateada.slice(1);
            
            serviciosDia.forEach(servicio => {
                const option = document.createElement('option');
                option.value = servicio.id;
                option.textContent = `${fechaCapitalizada} - ${servicio.hora} (${servicio.plazas_disponibles} plazas disponibles)`;
                
                // Añadir datos como atributos
                option.dataset.fecha = fecha;
                option.dataset.hora = servicio.hora;
                option.dataset.plazas = servicio.plazas_disponibles;
                option.dataset.precioAdulto = servicio.precio_adulto;
                option.dataset.precioNino = servicio.precio_nino;
                option.dataset.precioResidente = servicio.precio_residente;
                
                serviceSelect.appendChild(option);
                
                console.log(`✅ Añadido servicio: ${fechaCapitalizada} - ${servicio.hora}`);
            });
            
        } catch (error) {
            console.error('❌ Error procesando fecha:', fecha, error);
        }
    });
    
    console.log(`✅ Total opciones añadidas: ${serviceSelect.options.length - 1}`);
    
    if (serviceSelect.options.length === 1) {
        serviceSelect.innerHTML = '<option value="">No se pudieron cargar los servicios</option>';
        showError('No se encontraron servicios disponibles.');
    }
}

function handleServiceChange() {
    const serviceSelect = document.getElementById('service_id');
    const serviceInfo = document.getElementById('service-info');
    const serviceDetails = document.getElementById('service-details');
    
    if (serviceSelect.value) {
        const option = serviceSelect.selectedOptions[0];
        const fecha = option.dataset.fecha;
        const hora = option.dataset.hora;
        const plazas = option.dataset.plazas;
        const precioAdulto = option.dataset.precioAdulto;
        const precioNino = option.dataset.precioNino;
        const precioResidente = option.dataset.precioResidente;
        
        serviceDetails.innerHTML = `
            <strong>Fecha:</strong> ${fecha}<br>
            <strong>Hora:</strong> ${hora}<br>
            <strong>Plazas disponibles:</strong> ${plazas}<br>
            <strong>Precios:</strong> Adulto: ${precioAdulto}€ | Niño: ${precioNino}€ | Residente: ${precioResidente}€
        `;
        serviceInfo.style.display = 'block';
        
        // Calcular precio si hay personas seleccionadas
        calculatePrice();
    } else {
        serviceInfo.style.display = 'none';
        document.getElementById('price-summary').style.display = 'none';
    }
    
    updateSubmitButton();
}

function calculatePrice() {
    const serviceSelect = document.getElementById('service_id');
    if (!serviceSelect.value) return;
    
    const adultos = parseInt(document.getElementById('adultos').value) || 0;
    const residentes = parseInt(document.getElementById('residentes').value) || 0;
    const ninos_5_12 = parseInt(document.getElementById('ninos_5_12').value) || 0;
    const ninos_menores = parseInt(document.getElementById('ninos_menores').value) || 0;
    
    const totalPersonas = adultos + residentes + ninos_5_12;
    
    if (totalPersonas === 0) {
        document.getElementById('price-summary').style.display = 'none';
        updateSubmitButton();
        return;
    }
    
    // Validar que hay al menos un adulto si hay niños
    if (ninos_5_12 > 0 && (adultos + residentes) === 0) {
        showError('Debe haber al menos un adulto si hay niños');
        document.getElementById('price-summary').style.display = 'none';
        updateSubmitButton();
        return;
    }
    
    // Verificar disponibilidad
    const option = serviceSelect.selectedOptions[0];
    const plazasDisponibles = parseInt(option.dataset.plazas);
    
    if (totalPersonas > plazasDisponibles) {
        showError(`Solo quedan ${plazasDisponibles} plazas disponibles`);
        document.getElementById('price-summary').style.display = 'none';
        updateSubmitButton();
        return;
    }
    
    clearMessages();
    
    // Calcular precio vía AJAX
    jQuery.ajax({
        url: reservasAjax.ajax_url,
        type: 'POST',
        data: {
            action: 'calculate_price_rapida',
            service_id: serviceSelect.value,
            adultos: adultos,
            residentes: residentes,
            ninos_5_12: ninos_5_12,
            ninos_menores: ninos_menores,
            nonce: reservasAjax.nonce
        },
        success: function(response) {
            if (response.success) {
                updatePriceSummary(response.data);
            } else {
                showError('Error calculando precio: ' + response.data);
            }
        },
        error: function() {
            showError('Error de conexión calculando precio');
        }
    });
}

function updatePriceSummary(priceData) {
    document.getElementById('precio-base').textContent = priceData.precio_base + '€';
    document.getElementById('precio-total').textContent = priceData.precio_final + '€';
    
    // Mostrar descuentos si los hay
    if (priceData.descuento_total > 0) {
        document.getElementById('total-descuentos').textContent = '-' + priceData.descuento_total + '€';
        document.getElementById('discount-row').style.display = 'flex';
        
        // Detalles de descuentos
        let discountDetails = [];
        if (priceData.descuento_residentes > 0) {
            discountDetails.push(`Descuento residentes: -${priceData.descuento_residentes}€`);
        }
        if (priceData.descuento_ninos > 0) {
            discountDetails.push(`Descuento niños: -${priceData.descuento_ninos}€`);
        }
        if (priceData.descuento_grupo > 0) {
            discountDetails.push(`Descuento por grupo: -${priceData.descuento_grupo}€`);
        }
        
        document.getElementById('discount-details').innerHTML = discountDetails.join('<br>');
    } else {
        document.getElementById('discount-row').style.display = 'none';
        document.getElementById('discount-details').innerHTML = '';
    }
    
    document.getElementById('price-summary').style.display = 'block';
    updateSubmitButton();
}

function updateSubmitButton() {
    const serviceSelect = document.getElementById('service_id');
    const adultos = parseInt(document.getElementById('adultos').value) || 0;
    const residentes = parseInt(document.getElementById('residentes').value) || 0;
    const ninos_5_12 = parseInt(document.getElementById('ninos_5_12').value) || 0;
    const nombre = document.getElementById('nombre').value.trim();
    const apellidos = document.getElementById('apellidos').value.trim();
    const email = document.getElementById('email').value.trim();
    const telefono = document.getElementById('telefono').value.trim();
    
    const totalPersonas = adultos + residentes + ninos_5_12;
    const submitBtn = document.getElementById('submit-btn');
    
    const isValid = serviceSelect.value && 
                   totalPersonas > 0 && 
                   nombre.length >= 2 && 
                   apellidos.length >= 2 && 
                   email && 
                   telefono.length >= 9 &&
                   (ninos_5_12 === 0 || (adultos + residentes) > 0);
    
    submitBtn.disabled = !isValid;
    submitBtn.style.opacity = isValid ? '1' : '0.6';
}

function handleFormSubmit(e) {
    e.preventDefault();
    
    if (document.getElementById('submit-btn').disabled) {
        return;
    }
    
    // Deshabilitar botón y mostrar loading
    const submitBtn = document.getElementById('submit-btn');
    const originalText = submitBtn.textContent;
    submitBtn.disabled = true;
    submitBtn.textContent = '⏳ Procesando...';
    
    clearMessages();
    
    // Procesar reserva usando función específica para agencias
    processAgencyReservaRapida(function() {
        // Restaurar botón en caso de error
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
    });
}

function processAgencyReservaRapida(callbackOnError) {
    console.log('=== INICIANDO PROCESS AGENCIA RESERVA RÁPIDA ===');
    
    try {
        // Recopilar datos del formulario
        const formData = {
            action: 'process_agency_reserva_rapida',
            nonce: reservasAjax.nonce,
            // Datos del cliente
            nombre: document.getElementById('nombre').value.trim(),
            apellidos: document.getElementById('apellidos').value.trim(),
            email: document.getElementById('email').value.trim(),
            telefono: document.getElementById('telefono').value.trim(),
            // Datos del servicio
            service_id: document.getElementById('service_id').value,
            // Datos de personas
            adultos: parseInt(document.getElementById('adultos').value) || 0,
            residentes: parseInt(document.getElementById('residentes').value) || 0,
            ninos_5_12: parseInt(document.getElementById('ninos_5_12').value) || 0,
            ninos_menores: parseInt(document.getElementById('ninos_menores').value) || 0
        };
        
        console.log('Datos a enviar:', formData);
        
        // Validaciones del lado cliente
        const validation = validateAgencyReservaRapidaData(formData);
        if (!validation.valid) {
            showError(validation.error);
            if (callbackOnError) callbackOnError();
            return;
        }
        
        // Enviar solicitud AJAX
        jQuery.ajax({
            url: reservasAjax.ajax_url,
            type: 'POST',
            data: formData,
            timeout: 30000,
            success: function(response) {
                console.log('Respuesta del servidor:', response);
                
                if (response.success) {
                    handleAgencyReservaRapidaSuccess(response.data);
                } else {
                    showError('Error procesando reserva: ' + response.data);
                    if (callbackOnError) callbackOnError();
                }
            },
            error: function(xhr, status, error) {
                console.error('Error AJAX:', status, error);
                
                let errorMessage = 'Error de conexión';
                if (status === 'timeout') {
                    errorMessage = 'La solicitud tardó demasiado tiempo. Por favor, inténtalo de nuevo.';
                } else if (xhr.responseJSON && xhr.responseJSON.data) {
                    errorMessage = xhr.responseJSON.data;
                }
                
                showError(errorMessage);
                if (callbackOnError) callbackOnError();
            }
        });
        
    } catch (error) {
        console.error('Error en processAgencyReservaRapida:', error);
        showError('Error interno: ' + error.message);
        if (callbackOnError) callbackOnError();
    }
}

function validateAgencyReservaRapidaData(data) {
    // Validar datos del cliente
    if (!data.nombre || data.nombre.length < 2) {
        return { valid: false, error: 'El nombre debe tener al menos 2 caracteres' };
    }
    
    if (!data.apellidos || data.apellidos.length < 2) {
        return { valid: false, error: 'Los apellidos deben tener al menos 2 caracteres' };
    }
    
    if (!data.email || !isValidEmail(data.email)) {
        return { valid: false, error: 'Email no válido' };
    }
    
    if (!data.telefono || data.telefono.length < 9) {
        return { valid: false, error: 'Teléfono debe tener al menos 9 dígitos' };
    }
    
    // Validar servicio
    if (!data.service_id) {
        return { valid: false, error: 'Debe seleccionar un servicio' };
    }
    
    // Validar personas
    const totalPersonas = data.adultos + data.residentes + data.ninos_5_12;
    
    if (totalPersonas === 0) {
        return { valid: false, error: 'Debe haber al menos una persona que ocupe plaza' };
    }
    
    if (data.ninos_5_12 > 0 && (data.adultos + data.residentes) === 0) {
        return { valid: false, error: 'Debe haber al menos un adulto si hay niños' };
    }
    
    return { valid: true };
}

function handleAgencyReservaRapidaSuccess(data) {
    console.log('=== RESERVA RÁPIDA AGENCIA EXITOSA ===');
    console.log('Datos de respuesta:', data);
    
    // Mostrar mensaje de éxito con detalles
    const successMessage = `
        <div style="text-align: center; padding: 30px; background: #d4edda; border: 2px solid #28a745; border-radius: 12px; margin: 20px 0;">
            <h3 style="color: #155724; margin: 0 0 15px 0; font-size: 24px;">
                ✅ ¡RESERVA RÁPIDA PROCESADA EXITOSAMENTE!
            </h3>
            
            <div style="background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #28a745;">
                <h4 style="color: #28a745; margin: 0 0 15px 0;">Detalles de la Reserva:</h4>
                <div style="font-size: 16px; line-height: 1.6; color: #2d2d2d;">
                    <strong>Localizador:</strong> <span style="font-family: monospace; background: #f8f9fa; padding: 4px 8px; border-radius: 4px; font-size: 18px; color: #28a745; font-weight: bold;">${data.localizador}</span><br>
                    <strong>Cliente:</strong> ${document.getElementById('nombre').value} ${document.getElementById('apellidos').value}<br>
                    <strong>Email:</strong> ${document.getElementById('email').value}<br>
                    <strong>Fecha:</strong> ${formatDateForDisplay(data.detalles.fecha)}<br>
                    <strong>Hora:</strong> ${data.detalles.hora}<br>
                    <strong>Personas:</strong> ${data.detalles.personas}<br>
                    <strong>Total:</strong> <span style="color: #28a745; font-weight: bold; font-size: 18px;">${data.detalles.precio_final}€</span><br>
                    <strong>Agencia:</strong> ${data.admin_user}
                </div>
            </div>
            
            <div style="background: #e3f2fd; padding: 15px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #2196f3;">
                <p style="margin: 0; color: #1976d2; font-weight: 600;">
                    📧 Emails enviados automáticamente:
                </p>
                <ul style="margin: 10px 0 0 0; color: #1976d2; text-align: left; display: inline-block;">
                    <li>Confirmación al cliente (con PDF adjunto)</li>
                    <li>Notificación al administrador del sistema</li>
                    <li>Confirmación a tu agencia</li>
                </ul>
            </div>
            
            <div style="margin-top: 25px;">
                <button onclick="createNewAgencyReservaRapida()" style="background: #0073aa; color: white; border: none; padding: 12px 25px; border-radius: 6px; margin-right: 10px; cursor: pointer; font-weight: 600;">
                    ➕ Nueva Reserva Rápida
                </button>
                <button onclick="goBackToDashboard()" style="background: #6c757d; color: white; border: none; padding: 12px 25px; border-radius: 6px; cursor: pointer; font-weight: 600;">
                    🏠 Volver al Dashboard
                </button>
            </div>
        </div>
    `;
    
    // Mostrar el mensaje de éxito
    document.getElementById('form-messages').innerHTML = successMessage;
    
    // Hacer scroll hacia el mensaje
    document.getElementById('form-messages').scrollIntoView({ behavior: 'smooth' });
    
    // Log para debugging
    console.log('✅ Reserva rápida de agencia completada exitosamente');
    console.log('Localizador:', data.localizador);
    console.log('Agencia:', data.admin_user);
}

function createNewAgencyReservaRapida() {
    // Recargar el formulario
    location.reload();
}

function cancelAgencyReservaRapida() {
    if (confirm('¿Estás seguro de que quieres cancelar? Se perderán todos los datos introducidos.')) {
        // Volver al dashboard
        goBackToDashboard();
    }
}

function goBackToDashboard() {
    // Recargar la página para volver al dashboard
    location.reload();
}

function showError(message) {
    const messagesDiv = document.getElementById('form-messages');
    messagesDiv.innerHTML = `<div class="error-message">${message}</div>`;
    messagesDiv.scrollIntoView({ behavior: 'smooth' });
}

function showSuccess(message) {
    const messagesDiv = document.getElementById('form-messages');
    messagesDiv.innerHTML = `<div class="success-message">${message}</div>`;
    messagesDiv.scrollIntoView({ behavior: 'smooth' });
}

function clearMessages() {
    document.getElementById('form-messages').innerHTML = '';
}

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function formatDateForDisplay(dateString) {
    try {
        const date = new Date(dateString + 'T00:00:00');
        return date.toLocaleDateString('es-ES', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    } catch (error) {
        return dateString;
    }
}

// Event listeners para validación en tiempo real
document.addEventListener('DOMContentLoaded', function() {
    const inputs = ['nombre', 'apellidos', 'email', 'telefono', 'adultos', 'residentes', 'ninos_5_12', 'ninos_menores'];
    inputs.forEach(inputId => {
        const input = document.getElementById(inputId);
        if (input) {
            input.addEventListener('input', updateSubmitButton);
            input.addEventListener('blur', updateSubmitButton);
        }
    });
});
</script>