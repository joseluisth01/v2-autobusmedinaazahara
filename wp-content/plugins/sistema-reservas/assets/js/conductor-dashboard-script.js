/**
 * JavaScript completo para dashboard de conductores
 * Archivo: wp-content/plugins/sistema-reservas/assets/js/conductor-dashboard-script.js
 */

// Variables globales
let conductorCurrentDate = new Date();
let conductorServicesData = {};
let selectedServiceData = null;

/**
 * Función principal: Cargar sección de calendario para conductores
 */
function loadConductorCalendarSection() {
    console.log('=== INICIANDO CALENDARIO CONDUCTOR ===');
    
    // Ejecutar debug si estamos en modo desarrollo
    if (window.location.hostname === 'localhost' || window.location.hostname.includes('dev')) {
        debugConductorSession();
    }
    
    // Ocultar dashboard principal y mostrar calendario
    document.querySelector('.dashboard-content').style.display = 'none';
    
    const dynamicContent = document.getElementById('conductor-dynamic-content');
    dynamicContent.style.display = 'block';
    
    dynamicContent.innerHTML = `
        <div class="conductor-calendar-management">
            <div class="conductor-header">
                <h1>📅 Servicios y Reservas</h1>
                <div class="conductor-actions">
                    <button class="btn-info" onclick="showConductorSummary()">📊 Resumen</button>
                    <button class="btn-info" onclick="debugConductorSession()" style="background: #ffc107; display:none !important">🔧 Debug</button>
                    <button class="btn-secondary" onclick="goBackToConductorDashboard()">← Volver al Dashboard</button>
                </div>
            </div>
            
            <div class="conductor-calendar-controls">
                <button onclick="changeConductorMonth(-1)">← Mes Anterior</button>
                <span id="conductor-current-month"></span>
                <button onclick="changeConductorMonth(1)">Siguiente Mes →</button>
            </div>
            
            <div id="conductor-calendar-container">
                <div class="loading">Cargando calendario...</div>
            </div>
            
            <!-- Información de debug -->
            <div id="conductor-debug-info" style="margin-top: 20px; padding: 15px; background: #f0f0f0; border-radius: 8px; font-family: monospace; font-size: 12px; display: none;">
                <strong>Información de Debug:</strong><br>
                <span id="debug-content"></span>
            </div>
        </div>
        
        <!-- Resto de modales sin cambios -->
        <div id="serviceReservationsModal" class="modal" style="display: none;">
            <div class="modal-content" style="max-width: 1200px;">
                <span class="close" onclick="closeServiceReservationsModal()">&times;</span>
                <h3 id="serviceReservationsTitle">Reservas del Servicio</h3>
                <div id="service-reservations-content">
                    <!-- Contenido se cargará aquí -->
                </div>
            </div>
        </div>
        
        <div id="conductorSummaryModal" class="modal" style="display: none;">
            <div class="modal-content" style="max-width: 800px;">
                <span class="close" onclick="closeConductorSummaryModal()">&times;</span>
                <h3>📊 Resumen de Servicios</h3>
                <div id="conductor-summary-content">
                    <div class="loading">Cargando resumen...</div>
                </div>
            </div>
        </div>
        
        <style>
        .conductor-calendar-management {
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .conductor-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #28a745;
        }
        
        .conductor-header h1 {
            color: #23282d;
            margin: 0;
        }
        
        .conductor-actions {
            display: flex;
            gap: 15px;
        }
        
        .conductor-calendar-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
        }
        
        .conductor-calendar-controls button {
            padding: 10px 20px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
        }
        
        .conductor-calendar-controls button:hover {
            background: #218838;
        }
        
        .conductor-calendar-controls span {
            font-size: 18px;
            font-weight: 600;
            color: #23282d;
        }
        
        .conductor-calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 2px;
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
        }
        
        .conductor-calendar-day-header {
            background: #28a745;
            color: white;
            padding: 15px 10px;
            text-align: center;
            font-weight: bold;
            font-size: 14px;
        }
        
        .conductor-calendar-day {
            background: white;
            min-height: 120px;
            padding: 10px;
            border: 1px solid #ddd;
            position: relative;
        }
        
        .conductor-calendar-day.other-month {
            background: #f8f9fa;
            color: #999;
        }
        
        .conductor-calendar-day.has-services {
            background: #e8f5e8;
            border-color: #28a745;
        }
        
        .conductor-day-number {
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 8px;
            color: #23282d;
        }
        
        .conductor-service-item {
            background: #007bff;
            color: white;
            padding: 6px 8px;
            margin: 3px 0;
            border-radius: 4px;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.3s;
            display: block;
            text-align: center;
            line-height: 1.2;
        }
        
        .conductor-service-item:hover {
            background: #0056b3;
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        
        .conductor-service-item.has-reservations {
            background: #28a745;
            font-weight: 600;
        }
        
        .conductor-service-item.has-reservations:hover {
            background: #218838;
        }
        
        .service-time {
            font-weight: bold;
            font-size: 13px;
        }
        
        .service-count {
            font-size: 10px;
            opacity: 0.9;
            display: block;
            margin-top: 2px;
        }
        
        .reservations-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .reservations-table th,
        .reservations-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }
        
        .reservations-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #23282d;
            position: sticky;
            top: 0;
        }
        
        .reservations-table tr:hover {
            background: #f8f9fa;
        }
        
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-confirmada {
            background: #d4edda;
            color: #155724;
        }
        
        .status-cancelada {
            background: #f8d7da;
            color: #721c24;
        }
        
        .status-pendiente {
            background: #fff3cd;
            color: #856404;
        }
        
        .service-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        
        .stat-item {
            text-align: center;
        }
        
        .stat-number {
            font-size: 24px;
            font-weight: bold;
            color: #28a745;
        }
        
        .stat-label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
        }
        
        .service-info {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        
        .service-info h4 {
            margin: 0 0 10px 0;
            color: #1976d2;
        }
        
        .origin-badge {
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .origin-directa {
            background: #e3f2fd;
            color: #1976d2;
        }
        
        .origin-agencia {
            background: #f3e5f5;
            color: #7b1fa2;
        }
        
        .verify-btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .verify-btn.verified {
            background: #28a745;
            color: white;
        }
        
        .verify-btn.not-verified {
            background: #6c757d;
            color: white;
        }
        
        .verify-btn:hover {
            transform: translateY(-1px);
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        
        .modal-content {
            background-color: #fefefe;
            margin: 2% auto;
            padding: 20px;
            border: none;
            border-radius: 8px;
            width: 90%;
            max-width: 1000px;
            max-height: 90vh;
            overflow-y: auto;
        }
        
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .close:hover {
            color: #000;
        }
        
        .loading {
            text-align: center;
            padding: 40px;
            color: #666;
            font-style: italic;
        }
        
        .error {
            background: #fbeaea;
            border-left: 4px solid #d63638;
            padding: 12px;
            margin: 15px 0;
            border-radius: 4px;
            color: #d63638;
        }
        
        @media (max-width: 768px) {
            .conductor-calendar-grid {
                grid-template-columns: 1fr;
                gap: 10px;
            }
            
            .conductor-header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .conductor-actions {
                justify-content: center;
            }
            
            .service-stats {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .modal-content {
                width: 95%;
                margin: 5% auto;
                padding: 15px;
            }
            
            .reservations-table {
                font-size: 12px;
            }
            
            .reservations-table th,
            .reservations-table td {
                padding: 8px 4px;
            }
        }
        </style>
    `;
    
    setTimeout(() => {
        loadConductorCalendar();
    }, 100);
}

/**
 * Volver al dashboard principal del conductor
 */
function goBackToConductorDashboard() {
    document.querySelector('.dashboard-content').style.display = 'block';
    document.getElementById('conductor-dynamic-content').style.display = 'none';
}

/**
 * Cargar datos del calendario para conductores
 */
function loadConductorCalendar() {
    updateConductorCalendarHeader();
    
    console.log('=== INICIANDO CARGA DE CALENDARIO CONDUCTOR ===');
    console.log('reservasAjax disponible:', typeof reservasAjax !== 'undefined');
    console.log('AJAX URL:', reservasAjax?.ajax_url);
    console.log('Nonce:', reservasAjax?.nonce);
    
    // ✅ VERIFICACIÓN MEJORADA DE VARIABLES
    if (typeof reservasAjax === 'undefined') {
        console.error('❌ reservasAjax no está definido');
        document.getElementById('conductor-calendar-container').innerHTML = 
            '<div class="error">Error: Variables de configuración no disponibles. Recarga la página.</div>';
        return;
    }
    
    if (!reservasAjax.ajax_url || !reservasAjax.nonce) {
        console.error('❌ Faltan ajax_url o nonce');
        document.getElementById('conductor-calendar-container').innerHTML = 
            '<div class="error">Error: Configuración incompleta. Recarga la página.</div>';
        return;
    }
    
    const formData = new FormData();
    formData.append('action', 'get_conductor_calendar_data');
    formData.append('month', conductorCurrentDate.getMonth() + 1);
    formData.append('year', conductorCurrentDate.getFullYear());
    formData.append('nonce', reservasAjax.nonce);
    
    console.log('Enviando datos:', {
        action: 'get_conductor_calendar_data',
        month: conductorCurrentDate.getMonth() + 1,
        year: conductorCurrentDate.getFullYear(),
        nonce: reservasAjax.nonce
    });
    
    fetch(reservasAjax.ajax_url, {
        method: 'POST',
        body: formData,
        credentials: 'same-origin'
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        
        if (!response.ok) {
            console.error('❌ Error HTTP:', response.status, response.statusText);
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        return response.text(); // ✅ OBTENER COMO TEXTO PRIMERO
    })
    .then(text => {
        console.log('Raw response:', text.substring(0, 500) + (text.length > 500 ? '...' : ''));
        
        try {
            const data = JSON.parse(text);
            console.log('Parsed response:', data);
            
            if (data.success) {
                conductorServicesData = data.data;
                renderConductorCalendar();
                console.log('✅ Calendario renderizado correctamente');
            } else {
                console.error('❌ Error del servidor:', data.data);
                document.getElementById('conductor-calendar-container').innerHTML = 
                    '<div class="error">Error del servidor: ' + (data.data || 'Error desconocido') + '</div>';
            }
        } catch (parseError) {
            console.error('❌ Error parsing JSON:', parseError);
            console.error('Raw response was:', text);
            document.getElementById('conductor-calendar-container').innerHTML = 
                '<div class="error">Error de comunicación con el servidor. Raw response: ' + text.substring(0, 200) + '</div>';
        }
    })
    .catch(error => {
        console.error('❌ Fetch error:', error);
        document.getElementById('conductor-calendar-container').innerHTML = 
            '<div class="error">Error de conexión: ' + error.message + '</div>';
    });
}

// ✅ NUEVA FUNCIÓN: Debug para conductor
function debugConductorSession() {
    console.log('=== DEBUG SESIÓN CONDUCTOR ===');
    
    fetch(reservasAjax.ajax_url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            action: 'debug_conductor_session',
            nonce: reservasAjax.nonce
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log('Debug session response:', data);
    })
    .catch(error => {
        console.error('Debug session error:', error);
    });
}

/**
 * Actualizar header del calendario
 */
function updateConductorCalendarHeader() {
    const monthNames = [
        'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
        'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
    ];
    
    const monthYear = monthNames[conductorCurrentDate.getMonth()] + ' ' + conductorCurrentDate.getFullYear();
    const currentMonthElement = document.getElementById('conductor-current-month');
    if (currentMonthElement) {
        currentMonthElement.textContent = monthYear;
    }
}

/**
 * Cambiar mes del calendario
 */
function changeConductorMonth(direction) {
    conductorCurrentDate.setMonth(conductorCurrentDate.getMonth() + direction);
    loadConductorCalendar();
}

/**
 * Renderizar calendario para conductores
 */
function renderConductorCalendar() {
    const year = conductorCurrentDate.getFullYear();
    const month = conductorCurrentDate.getMonth();
    
    const firstDay = new Date(year, month, 1);
    const lastDay = new Date(year, month + 1, 0);
    let firstDayOfWeek = firstDay.getDay();
    firstDayOfWeek = (firstDayOfWeek + 6) % 7; // Lunes = 0
    
    const daysInMonth = lastDay.getDate();
    const dayNames = ['L', 'M', 'X', 'J', 'V', 'S', 'D'];
    
    let calendarHTML = '<div class="conductor-calendar-grid">';
    
    // Encabezados de días
    dayNames.forEach(day => {
        calendarHTML += `<div class="conductor-calendar-day-header">${day}</div>`;
    });
    
    // Días del mes anterior (si es necesario)
    const prevMonth = new Date(year, month - 1, 0);
    const prevMonthDays = prevMonth.getDate();
    
    for (let i = firstDayOfWeek - 1; i >= 0; i--) {
        const dayNum = prevMonthDays - i;
        calendarHTML += `<div class="conductor-calendar-day other-month">
            <div class="conductor-day-number">${dayNum}</div>
        </div>`;
    }
    
    // Días del mes actual
    for (let day = 1; day <= daysInMonth; day++) {
        const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        const hasServices = conductorServicesData[dateStr] && conductorServicesData[dateStr].length > 0;
        
        calendarHTML += `<div class="conductor-calendar-day ${hasServices ? 'has-services' : ''}">
            <div class="conductor-day-number">${day}</div>`;
        
        if (hasServices) {
            conductorServicesData[dateStr].forEach(service => {
                const reservationCount = service.reservas_confirmadas || 0;
                const totalPersonas = service.personas_confirmadas || 0;
                
                calendarHTML += `<div class="conductor-service-item ${reservationCount > 0 ? 'has-reservations' : ''}" 
                    onclick="showServiceReservations(${service.id}, '${dateStr}', '${service.hora}')">
                    <span class="service-time">${service.hora}</span>
                    <span class="service-count">${reservationCount} reservas</span>
                    <span class="service-count">${totalPersonas} personas</span>
                </div>`;
            });
        }
        
        calendarHTML += '</div>';
    }
    
    // Días del siguiente mes (para completar la grilla)
    const totalCells = Math.ceil((firstDayOfWeek + daysInMonth) / 7) * 7;
    const remainingCells = totalCells - (firstDayOfWeek + daysInMonth);
    
    for (let day = 1; day <= remainingCells; day++) {
        calendarHTML += `<div class="conductor-calendar-day other-month">
            <div class="conductor-day-number">${day}</div>
        </div>`;
    }
    
    calendarHTML += '</div>';
    
    document.getElementById('conductor-calendar-container').innerHTML = calendarHTML;
}

/**
 * Mostrar reservas de un servicio específico
 */
function showServiceReservations(serviceId, fecha, hora) {
    console.log(`Cargando reservas para servicio ${serviceId} del ${fecha} a las ${hora}`);
    
    const modal = document.getElementById('serviceReservationsModal');
    const title = document.getElementById('serviceReservationsTitle');
    const content = document.getElementById('service-reservations-content');
    
    // Formatear fecha para mostrar
    const fechaFormateada = new Date(fecha + 'T00:00:00').toLocaleDateString('es-ES', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
    
    title.textContent = `Reservas del ${fechaFormateada} a las ${hora}`;
    content.innerHTML = '<div class="loading">Cargando reservas...</div>';
    modal.style.display = 'block';
    
    // Cargar datos del servicio
    const formData = new FormData();
    formData.append('action', 'get_service_reservations');
    formData.append('service_id', serviceId);
    formData.append('nonce', reservasAjax.nonce);
    
    fetch(reservasAjax.ajax_url, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            renderServiceReservations(data.data);
        } else {
            content.innerHTML = '<div class="error">Error: ' + data.data + '</div>';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        content.innerHTML = '<div class="error">Error de conexión</div>';
    });
}

/**
 * Renderizar las reservas de un servicio
 */
function renderServiceReservations(data) {
    const { servicio, reservas, stats } = data;
    const content = document.getElementById('service-reservations-content');
    
    let html = `
        <div class="service-info">
            <h4>📍 Información del Servicio</h4>
            <p><strong>Fecha:</strong> ${servicio.fecha_formateada}</p>
            <p><strong>Hora de salida:</strong> ${servicio.hora}</p>
            ${servicio.hora_vuelta ? `<p><strong>Hora de regreso:</strong> ${servicio.hora_vuelta}</p>` : ''}
            <p><strong>Plazas totales:</strong> ${servicio.plazas_totales}</p>
        </div>
        
        <div class="service-stats">
            <div class="stat-item">
                <div class="stat-number">${stats.confirmadas}</div>
                <div class="stat-label">Confirmadas</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">${stats.total_personas}</div>
                <div class="stat-label">Personas</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">${stats.plazas_libres}</div>
                <div class="stat-label">Plazas Libres</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">${stats.ocupacion_porcentaje}%</div>
                <div class="stat-label">Ocupación</div>
            </div>
        </div>
    `;
    
    if (reservas.length === 0) {
        html += '<div class="error">No hay reservas para este servicio.</div>';
    } else {
        html += `
            <table class="reservations-table">
                <thead>
                    <tr>
                        <th>Localizador</th>
                        <th>Cliente</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                        <th>Personas</th>
                        <th>Desglose</th>
                        <th>Origen</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
        `;
        
        reservas.forEach(reserva => {
            const origen = reserva.origen_reserva || 'Reserva Directa';
            const originClass = reserva.agency_id ? 'origin-agencia' : 'origin-directa';
            
            // Construir desglose de personas
            let desglose = [];
            if (reserva.adultos > 0) desglose.push(`${reserva.adultos} adultos`);
            if (reserva.residentes > 0) desglose.push(`${reserva.residentes} residentes`);
            if (reserva.ninos_5_12 > 0) desglose.push(`${reserva.ninos_5_12} niños`);
            if (reserva.ninos_menores > 0) desglose.push(`${reserva.ninos_menores} bebés`);
            
            html += `
                <tr>
                    <td><strong>${reserva.localizador}</strong></td>
                    <td>${reserva.nombre} ${reserva.apellidos}</td>
                    <td>${reserva.email}</td>
                    <td>${reserva.telefono}</td>
                    <td><strong>${reserva.total_personas}</strong></td>
                    <td>${desglose.join(', ')}</td>
                    <td><span class="origin-badge ${originClass}">${origen}</span></td>
                    <td><span class="status-badge status-${reserva.estado}">${reserva.estado}</span></td>
                </tr>
            `;
        });
        
        html += `
                </tbody>
            </table>
        `;
    }
    
    content.innerHTML = html;
}

/**
 * Alternar verificación de una reserva
 */
function toggleReservationVerification(reservaId, button) {
    const isVerified = button.classList.contains('verified');
    const newStatus = !isVerified;
    
    // Actualizar UI inmediatamente
    if (newStatus) {
        button.classList.remove('not-verified');
        button.classList.add('verified');
        button.textContent = 'Verificado ✓';
    } else {
        button.classList.remove('verified');
        button.classList.add('not-verified');
        button.textContent = 'Verificar';
    }
    
    // Enviar al servidor
    const formData = new FormData();
    formData.append('action', 'verify_reservation');
    formData.append('reserva_id', reservaId);
    formData.append('verified', newStatus ? '1' : '0');
    formData.append('nonce', reservasAjax.nonce);
    
    fetch(reservasAjax.ajax_url, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Verificación actualizada:', data.data.message);
        } else {
            console.error('Error en verificación:', data.data);
            // Revertir cambio en caso de error
            if (newStatus) {
                button.classList.remove('verified');
                button.classList.add('not-verified');
                button.textContent = 'Verificar';
            } else {
                button.classList.remove('not-verified');
                button.classList.add('verified');
                button.textContent = 'Verificado ✓';
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        // Revertir cambio en caso de error
        if (newStatus) {
            button.classList.remove('verified');
            button.classList.add('not-verified');
            button.textContent = 'Verificar';
        } else {
            button.classList.remove('not-verified');
            button.classList.add('verified');
            button.textContent = 'Verificado ✓';
        }
    });
}

/**
 * Cerrar modal de reservas del servicio
 */
function closeServiceReservationsModal() {
    document.getElementById('serviceReservationsModal').style.display = 'none';
}

/**
 * Mostrar resumen de servicios para conductor
 */
function showConductorSummary() {
    const modal = document.getElementById('conductorSummaryModal');
    const content = document.getElementById('conductor-summary-content');
    
    content.innerHTML = '<div class="loading">Cargando resumen...</div>';
    modal.style.display = 'block';
    
    const formData = new FormData();
    formData.append('action', 'get_reservations_summary');
    formData.append('nonce', reservasAjax.nonce);
    
    fetch(reservasAjax.ajax_url, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            renderConductorSummary(data.data);
        } else {
            content.innerHTML = '<div class="error">Error: ' + data.data + '</div>';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        content.innerHTML = '<div class="error">Error de conexión</div>';
    });
}

/**
 * Renderizar resumen para conductor
 */
function renderConductorSummary(data) {
    const { servicios_hoy, proximos_servicios, fecha_actual_formateada } = data;
    const content = document.getElementById('conductor-summary-content');
    
    let html = `
        <div class="summary-sections">
            <div class="summary-section">
                <h4>📅 Servicios de Hoy (${fecha_actual_formateada})</h4>
    `;
    
    if (servicios_hoy.length === 0) {
        html += '<p>No hay servicios programados para hoy.</p>';
    } else {
        servicios_hoy.forEach(servicio => {
            const ocupacion = servicio.plazas_totales > 0 ? 
                Math.round((servicio.personas_confirmadas / servicio.plazas_totales) * 100) : 0;
            
            html += `
                <div class="service-summary-item" onclick="showServiceReservations(${servicio.id}, '${servicio.fecha}', '${servicio.hora}')">
                    <div>
                        <span class="service-time">${servicio.hora}</span>
                        <span class="service-occupancy"> - ${servicio.personas_confirmadas}/${servicio.plazas_totales} personas (${ocupacion}%)</span>
                    </div>
                    <div>
                        <strong>${servicio.total_reservas} reservas</strong>
                    </div>
                </div>
            `;
        });
    }
    
    html += `
            </div>
            <div class="summary-section">
                <h4>📋 Próximos Servicios (7 días)</h4>
    `;
    
    if (proximos_servicios.length === 0) {
        html += '<p>No hay servicios programados en los próximos 7 días.</p>';
    } else {
        proximos_servicios.forEach(servicio => {
            const fechaFormateada = new Date(servicio.fecha + 'T00:00:00').toLocaleDateString('es-ES', {
                weekday: 'short',
                month: 'short',
                day: 'numeric'
            });
            
            const ocupacion = servicio.plazas_totales > 0 ? 
                Math.round((servicio.personas_confirmadas / servicio.plazas_totales) * 100) : 0;
            
            html += `
                <div class="service-summary-item" onclick="showServiceReservations(${servicio.id}, '${servicio.fecha}', '${servicio.hora}')">
                    <div>
                        <span class="service-time">${fechaFormateada} ${servicio.hora}</span>
                        <span class="service-occupancy"> - ${servicio.personas_confirmadas}/${servicio.plazas_totales} personas (${ocupacion}%)</span>
                    </div>
                    <div>
                        <strong>${servicio.total_reservas} reservas</strong>
                    </div>
                </div>
            `;
        });
    }
    
    html += `
            </div>
        </div>
    `;
    
    content.innerHTML = html;
}

/**
 * Cerrar modal de resumen
 */
function closeConductorSummaryModal() {
    document.getElementById('conductorSummaryModal').style.display = 'none';
}

// Cerrar modales al hacer clic fuera de ellos
window.onclick = function(event) {
    const serviceModal = document.getElementById('serviceReservationsModal');
    const summaryModal = document.getElementById('conductorSummaryModal');
    
    if (event.target === serviceModal) {
        serviceModal.style.display = 'none';
    }
    if (event.target === summaryModal) {
        summaryModal.style.display = 'none';
    }
}