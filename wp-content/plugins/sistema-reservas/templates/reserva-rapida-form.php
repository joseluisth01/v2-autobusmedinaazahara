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
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
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

        .form-section {
            background: #F8F9FA;
            padding: 25px;
            border-radius: 8px;
            margin-bottom: 25px;
            border-left: 4px solid #28a745;
        }

        .form-section h3 {
            margin: 0 0 20px 0;
            color: #28a745;
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
            border-color: #28a745;
            box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.1);
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
            border-top: 2px solid #28a745;
            padding-top: 15px;
            margin-top: 15px;
            font-weight: 700;
            font-size: 20px;
            color: #28a745;
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
            background: #28a745;
            color: white;
        }

        .btn-primary:hover {
            background: #218838;
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
        <p>Proceso de reserva inmediata para administradores</p>
    </div>

    <form id="reserva-rapida-form">
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
                <h4 style="margin: 0 0 15px 0; color: #28a745;">💰 Resumen de Precios</h4>
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
            <button type="button" class="btn btn-secondary" onclick="cancelReservaRapida()">
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
    document.addEventListener('DOMContentLoaded', function() {
        initializeReservaRapida();
    });

    function initializeReservaRapida() {
        console.log('=== INICIALIZANDO RESERVA RÁPIDA ===');

        // Cargar servicios disponibles
        loadAvailableServices();

        // Event listeners
        document.getElementById('service_id').addEventListener('change', handleServiceChange);

        // Event listeners para cálculo de precios
        const personInputs = ['adultos', 'residentes', 'ninos_5_12', 'ninos_menores'];
        personInputs.forEach(inputId => {
            document.getElementById(inputId).addEventListener('input', calculatePrice);
        });

        // Event listener para el formulario
        document.getElementById('reserva-rapida-form').addEventListener('submit', handleFormSubmit);
    }

    function loadAvailableServices() {
        const serviceSelect = document.getElementById('service_id');
        serviceSelect.innerHTML = '<option value="">Cargando servicios...</option>';

        jQuery.ajax({
            url: reservasAjax.ajax_url,
            type: 'POST',
            data: {
                action: 'get_available_services_rapida',
                nonce: reservasAjax.nonce
            },
            success: function(response) {
                if (response.success) {
                    populateServiceSelect(response.data);
                } else {
                    showError('Error cargando servicios: ' + response.data);
                }
            },
            error: function() {
                showError('Error de conexión cargando servicios');
            }
        });
    }

    function populateServiceSelect(serviciosData) {
        const serviceSelect = document.getElementById('service_id');
        serviceSelect.innerHTML = '<option value="">Selecciona un servicio</option>';

        if (Object.keys(serviciosData).length === 0) {
            serviceSelect.innerHTML = '<option value="">No hay servicios disponibles</option>';
            return;
        }

        Object.keys(serviciosData).forEach(fecha => {
            const fechaObj = new Date(fecha + 'T00:00:00');
            const fechaFormateada = fechaObj.toLocaleDateString('es-ES', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });

            serviciosData[fecha].forEach(servicio => {
                const option = document.createElement('option');
                option.value = servicio.id;
                option.textContent = `${fechaFormateada} - ${servicio.hora} (${servicio.plazas_disponibles} plazas)`;
                option.dataset.fecha = fecha;
                option.dataset.hora = servicio.hora;
                option.dataset.plazas = servicio.plazas_disponibles;
                option.dataset.precioAdulto = servicio.precio_adulto;
                option.dataset.precioNino = servicio.precio_nino;
                option.dataset.precioResidente = servicio.precio_residente;

                serviceSelect.appendChild(option);
            });
        });
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

        // Procesar reserva
        processReservaRapida(function() {
            // Restaurar botón en caso de error
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        });
    }

    function cancelReservaRapida() {
        if (confirm('¿Estás seguro de que quieres cancelar? Se perderán todos los datos introducidos.')) {
            // Volver al dashboard
            loadDashboardSection('dashboard');
        }
    }

    function showError(message) {
        const messagesDiv = document.getElementById('form-messages');
        messagesDiv.innerHTML = `<div class="error-message">${message}</div>`;
    }

    function showSuccess(message) {
        const messagesDiv = document.getElementById('form-messages');
        messagesDiv.innerHTML = `<div class="success-message">${message}</div>`;
    }

    function clearMessages() {
        document.getElementById('form-messages').innerHTML = '';
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