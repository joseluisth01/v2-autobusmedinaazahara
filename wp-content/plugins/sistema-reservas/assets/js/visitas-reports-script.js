/**
 * Script para gestión de informes de visitas guiadas
 * Archivo: wp-content/plugins/sistema-reservas/assets/js/visitas-reports-script.js
 */

let currentVisitasPage = 1;
let currentVisitasFilters = {
    fecha_inicio: new Date().toISOString().split('T')[0],
    fecha_fin: new Date().toISOString().split('T')[0],
    tipo_fecha: 'servicio',
    estado_filtro: 'confirmadas',
    agency_filter: 'todas'
};

function loadVisitasReportsSection() {
    console.log('=== CARGANDO SECCIÓN DE INFORMES DE VISITAS GUIADAS ===');

    // ✅ VERIFICAR SI ES AGENCIA
    const isAgency = window.reservasUser && window.reservasUser.role === 'agencia';
    const agencySelectorDisplay = isAgency ? 'style="display: none;"' : '';

    // ✅ NUEVO: Mostrar botón PDF solo para super_admin
    const isSuperAdmin = window.reservasUser && window.reservasUser.role === 'super_admin';
    const pdfButtonHtml = isSuperAdmin ? `
        <button class="btn-apply-filters" onclick="getAvailableSchedulesForVisitasPDF()" style="background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%);">
            📄 Descargar PDF
        </button>
    ` : '';

    document.body.innerHTML = `
        <style>
            /* ===== ESTILOS PARA FILTROS DE VISITAS ===== */
            #visitaDetailsModal {
            display: none;
            position: fixed;
            z-index: 10000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(5px);
            animation: fadeIn 0.3s ease;
        }

        #visitaDetailsModal .modal-content {
            background: white;
            margin: 3% auto;
            padding: 0;
            border-radius: 20px;
            max-width: 900px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: slideDown 0.3s ease;
        }

        .modal-header-visita {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 30px;
            position: relative;
            border-radius: 20px 20px 0 0;
        }

        .modal-header-visita h3 {
            color: white;
            font-size: 28px;
            font-weight: 700;
            margin: 0;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .modal-header-visita .close {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: white;
            font-size: 32px;
            font-weight: 300;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
        }

        .modal-header-visita .close:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-50%) rotate(90deg);
        }

        .modal-body-visita {
            padding: 40px;
        }

        .visita-details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .detail-card {
            background: #f7fafc;
            padding: 20px;
            border-radius: 12px;
            border-left: 4px solid #667eea;
            transition: all 0.3s ease;
        }

        .detail-card:hover {
            transform: translateX(5px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .detail-card-label {
            font-size: 12px;
            color: #718096;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .detail-card-value {
            font-size: 18px;
            color: #2d3748;
            font-weight: 700;
        }

        .detail-card-highlight {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-left: none;
        }

        .detail-card-highlight .detail-card-label {
            color: rgba(255, 255, 255, 0.9);
        }

        .detail-card-highlight .detail-card-value {
            color: white;
            font-size: 24px;
        }

        .status-badge-modal {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-confirmada {
            background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
            color: white;
        }

        .status-cancelada {
            background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%);
            color: white;
        }

        /* ===== ESTILOS PARA MODAL DE EDICIÓN ===== */
        #editVisitaModal {
            display: none;
            position: fixed;
            z-index: 10001;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(5px);
            animation: fadeIn 0.3s ease;
        }

        #editVisitaModal .modal-content {
            background: white;
            margin: 3% auto;
            padding: 0;
            border-radius: 20px;
            max-width: 650px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: slideDown 0.3s ease;
        }

        #editVisitaModal .modal-header-visita {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="tel"],
        .form-group input[type="number"],
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 15px;
            color: #2d3748;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 80px;
            font-family: inherit;
        }

        .form-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 2px solid #f1f5f9;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: #f1f5f9;
            color: #475569;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-secondary:hover {
            background: #e2e8f0;
            transform: translateY(-2px);
        }

        /* ===== ANIMACIONES ===== */
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        @keyframes slideDown {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            #visitaDetailsModal .modal-content,
            #editVisitaModal .modal-content {
                margin: 5% 10px;
                max-width: calc(100% - 20px);
            }

            .modal-header-visita {
                padding: 20px;
            }

            .modal-header-visita h3 {
                font-size: 20px;
                padding-right: 40px;
            }

            .modal-body-visita {
                padding: 20px;
            }

            .visita-details-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .detail-card-value {
                font-size: 16px;
            }

            .detail-card-highlight .detail-card-value {
                font-size: 20px;
            }

            .form-actions {
                flex-direction: column;
            }

            .btn-primary,
            .btn-secondary {
                width: 100%;
            }
        }

        /* ===== SCROLLBAR PERSONALIZADO PARA MODALES ===== */
        #visitaDetailsModal .modal-content::-webkit-scrollbar,
        #editVisitaModal .modal-content::-webkit-scrollbar {
            width: 8px;
        }

        #visitaDetailsModal .modal-content::-webkit-scrollbar-track,
        #editVisitaModal .modal-content::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        #visitaDetailsModal .modal-content::-webkit-scrollbar-thumb,
        #editVisitaModal .modal-content::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        #visitaDetailsModal .modal-content::-webkit-scrollbar-thumb:hover,
        #editVisitaModal .modal-content::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
            .visitas-filters-container {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                padding: 30px;
                border-radius: 15px;
                box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
                margin-bottom: 30px;
            }

            .visitas-filters-header {
                text-align: center;
                margin-bottom: 25px;
            }

            .visitas-filters-header h3 {
                color: white;
                font-size: 24px;
                font-weight: 700;
                margin: 0 0 10px 0;
                text-transform: uppercase;
                letter-spacing: 1px;
            }

            .visitas-filters-header p {
                color: rgba(255, 255, 255, 0.9);
                font-size: 14px;
                margin: 0;
            }

            .filters-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 20px;
                margin-bottom: 20px;
            }

            .filter-item {
                display: flex;
                flex-direction: column;
            }

            .filter-item label {
                color: white;
                font-weight: 600;
                font-size: 13px;
                margin-bottom: 8px;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            .filter-item input[type="date"],
            .filter-item select {
                padding: 12px 15px;
                border: none;
                border-radius: 8px;
                background: white;
                font-size: 14px;
                color: #2d3748;
                transition: all 0.3s ease;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            }

            .filter-item input[type="date"]:focus,
            .filter-item select:focus {
                outline: none;
                box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
                transform: translateY(-2px);
            }

            .filters-actions {
                display: flex;
                gap: 15px;
                justify-content: center;
                margin-top: 25px;
            }

            .filters-actions button {
                padding: 14px 35px;
                border: none;
                border-radius: 8px;
                font-size: 15px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s ease;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            .btn-apply-filters {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            }

            .btn-apply-filters:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
            }

            .btn-reset-filters {
                background: white;
                color: #667eea;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            }

            .btn-reset-filters:hover {
                background: #f7fafc;
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
            }

            /* ===== BÚSQUEDA RÁPIDA ===== */
            .quick-search-container {
                background: white;
                padding: 25px;
                border-radius: 15px;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
                margin-bottom: 30px;
            }

            .quick-search-header {
                display: flex;
                align-items: center;
                gap: 10px;
                margin-bottom: 20px;
            }

            .quick-search-header h3 {
                font-size: 18px;
                color: #2d3748;
                margin: 0;
                font-weight: 700;
            }

            .search-input-group {
                display: flex;
                gap: 15px;
                align-items: center;
            }

            .search-input-group select,
            .search-input-group input {
                padding: 12px 15px;
                border: 2px solid #e2e8f0;
                border-radius: 8px;
                font-size: 14px;
                transition: all 0.3s ease;
            }

            .search-input-group select {
                min-width: 150px;
            }

            .search-input-group input {
                flex: 1;
            }

            .search-input-group select:focus,
            .search-input-group input:focus {
                outline: none;
                border-color: #667eea;
                box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            }

            .btn-search {
                padding: 12px 30px;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                border: none;
                border-radius: 8px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s ease;
            }

            .btn-search:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            }

            /* ===== MODAL DE DETALLES MEJORADO ===== */
            #visitaDetailsModal {
                display: none;
                position: fixed;
                z-index: 10000;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.6);
                backdrop-filter: blur(5px);
                animation: fadeIn 0.3s ease;
            }

            @keyframes fadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }

            #visitaDetailsModal .modal-content {
                background: white;
                margin: 3% auto;
                padding: 0;
                border-radius: 20px;
                max-width: 900px;
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
                animation: slideDown 0.3s ease;
                overflow: hidden;
            }

            @keyframes slideDown {
                from {
                    transform: translateY(-50px);
                    opacity: 0;
                }
                to {
                    transform: translateY(0);
                    opacity: 1;
                }
            }

            .modal-header-visita {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                padding: 30px;
                position: relative;
            }

            .modal-header-visita h3 {
                color: white;
                font-size: 28px;
                font-weight: 700;
                margin: 0;
                text-align: center;
                text-transform: uppercase;
                letter-spacing: 1px;
            }

            .modal-header-visita .close {
                position: absolute;
                right: 20px;
                top: 50%;
                transform: translateY(-50%);
                color: white;
                font-size: 32px;
                font-weight: 300;
                cursor: pointer;
                transition: all 0.3s ease;
                width: 40px;
                height: 40px;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.1);
            }

            .modal-header-visita .close:hover {
                background: rgba(255, 255, 255, 0.2);
                transform: translateY(-50%) rotate(90deg);
            }

            .modal-body-visita {
                padding: 40px;
            }

            .visita-details-grid {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 25px;
            }

            .detail-card {
                background: #f7fafc;
                padding: 20px;
                border-radius: 12px;
                border-left: 4px solid #667eea;
                transition: all 0.3s ease;
            }

            .detail-card:hover {
                transform: translateX(5px);
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            }

            .detail-card-label {
                font-size: 12px;
                color: #718096;
                text-transform: uppercase;
                font-weight: 600;
                letter-spacing: 0.5px;
                margin-bottom: 8px;
            }

            .detail-card-value {
                font-size: 18px;
                color: #2d3748;
                font-weight: 700;
            }

            .detail-card-highlight {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                border-left: none;
            }

            .detail-card-highlight .detail-card-label {
                color: rgba(255, 255, 255, 0.9);
            }

            .detail-card-highlight .detail-card-value {
                color: white;
                font-size: 24px;
            }

            .detail-card-full {
                grid-column: 1 / -1;
            }

            .status-badge-modal {
                display: inline-block;
                padding: 8px 20px;
                border-radius: 20px;
                font-size: 14px;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            .status-confirmada {
                background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
                color: white;
            }

            .status-cancelada {
                background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%);
                color: white;
            }

            /* ===== RESPONSIVE ===== */
            @media (max-width: 768px) {
                .filters-grid {
                    grid-template-columns: 1fr;
                }

                .visita-details-grid {
                    grid-template-columns: 1fr;
                }

                .filters-actions {
                    flex-direction: column;
                }

                .filters-actions button {
                    width: 100%;
                }

                .search-input-group {
                    flex-direction: column;
                }

                .search-input-group select,
                .search-input-group input {
                    width: 100%;
                }
            }
        </style>

        <div class="reports-management">
            <div class="reports-header">
                <h1>📊 Informes de Visitas Guiadas${isAgency ? ' - ' + (window.reservasUser.agency_name || 'Mi Agencia') : ''}</h1>
                <div class="reports-actions">
                    <button class="btn-secondary" onclick="goBackToDashboard()">← Volver al Dashboard</button>
                </div>
            </div>
            
            <!-- Filtros -->
            <div class="visitas-filters-container">
                <div class="visitas-filters-header">
                    <h3>🔍 Filtros de Búsqueda</h3>
                    <p>Selecciona los criterios para generar tu informe</p>
                </div>
                
                <div class="filters-grid">
                    <div class="filter-item">
                        <label for="visitas-fecha-inicio">Fecha Inicio:</label>
                        <input type="date" id="visitas-fecha-inicio" value="${new Date().toISOString().split('T')[0]}">
                    </div>
                    <div class="filter-item">
                        <label for="visitas-fecha-fin">Fecha Fin:</label>
                        <input type="date" id="visitas-fecha-fin" value="${new Date().toISOString().split('T')[0]}">
                    </div>
                    <div class="filter-item">
                        <label for="visitas-tipo-fecha">Tipo de Fecha:</label>
                        <select id="visitas-tipo-fecha">
                            <option value="servicio">Fecha de Servicio</option>
                            <option value="compra">Fecha de Compra</option>
                        </select>
                    </div>
                    <div class="filter-item">
                        <label for="visitas-estado-filtro">Estado:</label>
                        <select id="visitas-estado-filtro">
                            <option value="confirmadas">Confirmadas</option>
                            <option value="canceladas">Canceladas</option>
                            <option value="todas">Todas</option>
                        </select>
                    </div>
                    <div class="filter-item" ${agencySelectorDisplay}>
                        <label for="visitas-agency-filter">Agencia:</label>
                        <select id="visitas-agency-filter">
                            <option value="todas">🔄 Cargando agencias...</option>
                        </select>
                    </div>
                </div>

                <div class="filters-actions">
                    <button class="btn-apply-filters" onclick="loadVisitasReportData()">
                        🔍 Aplicar Filtros
                    </button>
                    <button class="btn-reset-filters" onclick="resetVisitasFilters()">
                        ↺ Restablecer
                    </button>
                    ${pdfButtonHtml}
                </div>
            </div>

            <!-- Búsqueda Rápida Mejorada -->
            <div class="quick-search-container">
                <div class="quick-search-header">
                    <h3>🔎 Búsqueda Rápida</h3>
                </div>
                <div class="search-input-group">
                    <select id="visitas-search-type">
                        <option value="localizador">Localizador</option>
                        <option value="email">Email</option>
                        <option value="telefono">Teléfono</option>
                        <option value="nombre">Nombre</option>
                        <option value="fecha_servicio">Fecha Servicio</option>
                    </select>
                    <input type="text" id="visitas-search-value" placeholder="Introduce el valor a buscar...">
                    <button class="btn-search" onclick="searchVisitasData()">Buscar</button>
                </div>
            </div>

            <!-- Estadísticas -->
            <div id="visitas-stats-container"></div>

            <!-- Lista de visitas -->
            <div id="visitas-list-container"></div>

            <!-- Paginación -->
            <div id="visitas-pagination-container"></div>
        </div>

        <!-- Modal Mejorado para detalles de visita -->
        <div id="visitaDetailsModal" class="modal">
            <div class="modal-content">
                <div class="modal-header-visita">
                    <h3>Detalles de la Visita</h3>
                    <span class="close" onclick="closeVisitaDetailsModal()">&times;</span>
                </div>
                <div class="modal-body-visita">
                    <div id="visita-details-content"></div>
                </div>
            </div>
        </div>
    `;

    if (!isAgency) {
        loadAgenciesForVisitasFilter().then(() => {
            loadVisitasReportData();
        });
    } else {
        // Para agencias, cargar directamente los datos
        loadVisitasReportData();
    }

    initVisitasReportsEvents();
}

/**
 * Cargar agencias para el filtro
 */
function loadAgenciesForVisitasFilter() {
    jQuery.ajax({
        url: reservasAjax.ajax_url,
        type: 'POST',
        data: {
            action: 'get_agencies_for_filter',
            nonce: reservasAjax.nonce
        },
        success: function (response) {
            if (response.success && response.data) {
                const select = document.getElementById('visitas-agency-filter');
                response.data.forEach(agency => {
                    const option = document.createElement('option');
                    option.value = agency.id;
                    option.textContent = agency.agency_name;
                    select.appendChild(option);
                });
            }
        }
    });
}

/**
 * Cargar informe de visitas
 */
function loadVisitasReport() {
    console.log('=== CARGANDO INFORME DE VISITAS ===');

    showVisitasLoading();

    jQuery.ajax({
        url: reservasAjax.ajax_url,
        type: 'POST',
        data: {
            action: 'get_visitas_report',
            nonce: reservasAjax.nonce,
            ...currentVisitasFilters,
            page: currentVisitasPage
        },
        success: function (response) {
            console.log('Respuesta del servidor:', response);

            if (response.success) {
                renderVisitasStats(response.data.stats, response.data.stats_por_agencias);
                renderVisitasList(response.data.visitas);
                renderVisitasPagination(response.data.pagination);
            } else {
                showError('Error cargando informe: ' + response.data);
            }
        },
        error: function (xhr, status, error) {
            console.error('Error AJAX:', error);
            showError('Error de conexión al cargar el informe');
        }
    });
}

/**
 * Renderizar estadísticas
 */
function renderVisitasStats(stats, stats_agencias) {
    const container = document.getElementById('visitas-stats-container');

    let html = `
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Visitas</h3>
                <div class="stat-number">${stats.total_visitas || 0}</div>
            </div>
            <div class="stat-card">
                <h3>Total Personas</h3>
                <div class="stat-number">${stats.total_personas || 0}</div>
            </div>
            <div class="stat-card">
                <h3>Adultos</h3>
                <div class="stat-number">${stats.total_adultos || 0}</div>
            </div>
            <div class="stat-card">
                <h3>Niños</h3>
                <div class="stat-number">${stats.total_ninos || 0}</div>
            </div>
            <div class="stat-card">
                <h3>Niños Menores</h3>
                <div class="stat-number">${stats.total_ninos_menores || 0}</div>
            </div>
            <div class="stat-card">
                <h3>Ingresos Totales</h3>
                <div class="stat-number">${parseFloat(stats.ingresos_totales || 0).toFixed(2)}€</div>
            </div>
        </div>
    `;

    // Estadísticas por agencia
    if (stats_agencias && stats_agencias.length > 0) {
        html += `
            <div class="agencies-stats-section">
                <h3>📊 Estadísticas por Agencia</h3>
                <table class="agencies-stats-table">
                    <thead>
                        <tr>
                            <th>Agencia</th>
                            <th>Visitas</th>
                            <th>Personas</th>
                            <th>Ingresos</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

        stats_agencias.forEach(agency => {
            html += `
                <tr>
                    <td>${agency.agency_name}</td>
                    <td>${agency.total_visitas}</td>
                    <td>${agency.total_personas}</td>
                    <td>${parseFloat(agency.ingresos_total).toFixed(2)}€</td>
                </tr>
            `;
        });

        html += `
                    </tbody>
                </table>
            </div>
        `;
    }

    container.innerHTML = html;
}

/**
 * Renderizar lista de visitas
 */
/**
 * Renderizar lista de visitas
 */
function renderVisitasList(visitas) {
    const container = document.getElementById('visitas-list-container');

    if (!visitas || visitas.length === 0) {
        container.innerHTML = '<p class="no-results">No se encontraron visitas para los filtros seleccionados</p>';
        return;
    }

    // ✅ VERIFICAR ROL DEL USUARIO
    const isSuperAdmin = window.reservasUser && window.reservasUser.role === 'super_admin';
    
    console.log('🔍 Renderizando lista - Usuario:', window.reservasUser);
    console.log('🔍 Es super_admin:', isSuperAdmin);

    let html = `
        <h3>Listado de Visitas (${visitas.length})</h3>
        <table class="visitas-table">
            <thead>
                <tr>
                    <th>Localizador</th>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Cliente</th>
                    <th>Personas</th>
                    <th>Total</th>
                    <th>Agencia</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
    `;

    visitas.forEach(visita => {
        const fecha = new Date(visita.fecha + 'T00:00:00').toLocaleDateString('es-ES');
        const estadoClass = visita.estado === 'confirmada' ? 'status-confirmed' : 'status-cancelled';

        // ✅ BOTÓN DE CANCELAR SOLO PARA SUPER_ADMIN Y SOLO SI ESTÁ CONFIRMADA
        let cancelButton = '';
        if (visita.estado === 'confirmada' && isSuperAdmin) {
            cancelButton = `<button class="btn-small btn-danger" onclick="cancelVisita(${visita.id})">Cancelar</button>`;
        }

        html += `
            <tr>
                <td><strong>${visita.localizador}</strong></td>
                <td>${fecha}</td>
                <td>${visita.hora}</td>
                <td>${visita.nombre} ${visita.apellidos}</td>
                <td>${visita.total_personas}</td>
                <td>${parseFloat(visita.precio_total).toFixed(2)}€</td>
                <td>${visita.agency_name || 'Sin agencia'}</td>
                <td><span class="status-badge ${estadoClass}">${visita.estado}</span></td>
                <td>
                    <button class="btn-small" onclick="viewVisitaDetails(${visita.id})">Ver</button>
                    ${cancelButton}
                </td>
            </tr>
        `;
    });

    html += `
            </tbody>
        </table>
    `;

    container.innerHTML = html;
}

/**
 * Renderizar paginación
 */
function renderVisitasPagination(pagination) {
    const container = document.getElementById('visitas-pagination-container');

    if (pagination.total_pages <= 1) {
        container.innerHTML = '';
        return;
    }

    let html = '<div class="pagination">';

    // Botón anterior
    if (pagination.current_page > 1) {
        html += `<button onclick="changeVisitasPage(${pagination.current_page - 1})">« Anterior</button>`;
    }

    // Números de página
    for (let i = 1; i <= pagination.total_pages; i++) {
        const activeClass = i === pagination.current_page ? 'active' : '';
        html += `<button class="${activeClass}" onclick="changeVisitasPage(${i})">${i}</button>`;
    }

    // Botón siguiente
    if (pagination.current_page < pagination.total_pages) {
        html += `<button onclick="changeVisitasPage(${pagination.current_page + 1})">Siguiente »</button>`;
    }

    html += '</div>';
    container.innerHTML = html;
}

/**
 * Cambiar página
 */
function changeVisitasPage(page) {
    currentVisitasPage = page;
    loadVisitasReport();
}

/**
 * Aplicar filtros
 */
function applyVisitasFilters() {
    currentVisitasFilters = {
        fecha_inicio: document.getElementById('visitas-fecha-inicio').value,
        fecha_fin: document.getElementById('visitas-fecha-fin').value,
        tipo_fecha: document.getElementById('visitas-tipo-fecha').value,
        estado_filtro: document.getElementById('visitas-estado-filtro').value,
        agency_filter: document.getElementById('visitas-agency-filter').value
    };
    currentVisitasPage = 1;
    loadVisitasReport();
}

function resetVisitasFilters() {
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('visitas-fecha-inicio').value = today;
    document.getElementById('visitas-fecha-fin').value = today;
    document.getElementById('visitas-tipo-fecha').value = 'servicio';
    document.getElementById('visitas-estado-filtro').value = 'confirmadas';
    document.getElementById('visitas-agency-filter').value = 'todas';
    loadVisitasReportData();
}

/**
 * Buscar visitas
 */
function searchVisitas() {
    const searchType = document.getElementById('visitas-search-type').value;
    const searchValue = document.getElementById('visitas-search-value').value;

    if (!searchValue) {
        alert('Por favor, introduce un valor de búsqueda');
        return;
    }

    showVisitasLoading();

    jQuery.ajax({
        url: reservasAjax.ajax_url,
        type: 'POST',
        data: {
            action: 'search_visitas',
            nonce: reservasAjax.nonce,
            search_type: searchType,
            search_value: searchValue
        },
        success: function (response) {
            if (response.success) {
                document.getElementById('visitas-stats-container').innerHTML = `<p>Resultados de búsqueda: ${response.data.total_found} visitas encontradas</p>`;
                renderVisitasList(response.data.visitas);
                document.getElementById('visitas-pagination-container').innerHTML = '';
            } else {
                showError('Error en la búsqueda: ' + response.data);
            }
        }
    });
}

/**
 * Ver detalles de visita
 */
function viewVisitaDetails(visitaId) {
    jQuery.ajax({
        url: reservasAjax.ajax_url,
        type: 'POST',
        data: {
            action: 'get_visita_details',
            nonce: reservasAjax.nonce,
            visita_id: visitaId
        },
        success: function (response) {
            if (response.success) {
                showVisitaDetailsModal(response.data);
            }
        }
    });
}

function showVisitaDetails(visitaId) {
    console.log('=== MOSTRANDO DETALLES DE VISITA ===');
    console.log('ID:', visitaId);

    const formData = new FormData();
    formData.append('action', 'get_visita_details');
    formData.append('visita_id', visitaId);
    formData.append('nonce', reservasAjax.nonce);

    fetch(reservasAjax.ajax_url, {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            console.log('Respuesta del servidor:', data);

            if (data.success) {
                const visita = data.data;

                const fechaFormateada = new Date(visita.fecha + 'T00:00:00').toLocaleDateString('es-ES', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });

                const estadoClass = visita.estado === 'confirmada' ? 'status-confirmada' : 'status-cancelada';
                const estadoTexto = visita.estado === 'confirmada' ? '✓ Confirmada' : '✕ Cancelada';

                const detailsHtml = `
                    <div class="visita-details-grid">
                        <div class="detail-card detail-card-highlight">
                            <div class="detail-card-label">Localizador</div>
                            <div class="detail-card-value">${visita.localizador}</div>
                        </div>

                        <div class="detail-card detail-card-highlight">
                            <div class="detail-card-label">Estado</div>
                            <div class="detail-card-value">
                                <span class="status-badge-modal ${estadoClass}">${estadoTexto}</span>
                            </div>
                        </div>

                        <div class="detail-card">
                            <div class="detail-card-label">📅 Fecha del Servicio</div>
                            <div class="detail-card-value">${fechaFormateada}</div>
                        </div>

                        <div class="detail-card">
                            <div class="detail-card-label">🕐 Hora</div>
                            <div class="detail-card-value">${visita.hora}</div>
                        </div>

                        <div class="detail-card">
                            <div class="detail-card-label">👤 Cliente</div>
                            <div class="detail-card-value">${visita.nombre} ${visita.apellidos}</div>
                        </div>

                        <div class="detail-card">
                            <div class="detail-card-label">📧 Email</div>
                            <div class="detail-card-value" style="font-size: 14px;">${visita.email}</div>
                        </div>

                        <div class="detail-card">
                            <div class="detail-card-label">📱 Teléfono</div>
                            <div class="detail-card-value">${visita.telefono}</div>
                        </div>

                        <div class="detail-card">
                            <div class="detail-card-label">🏢 Agencia</div>
                            <div class="detail-card-value">${visita.agency_name || 'Sin agencia'}</div>
                        </div>

                        <div class="detail-card">
                            <div class="detail-card-label">👨‍👩‍👧‍👦 Adultos</div>
                            <div class="detail-card-value">${visita.adultos}</div>
                        </div>

                        <div class="detail-card">
                            <div class="detail-card-label">👶 Niños (5-12 años)</div>
                            <div class="detail-card-value">${visita.ninos}</div>
                        </div>

                        <div class="detail-card">
                            <div class="detail-card-label">🍼 Niños menores (-5 años)</div>
                            <div class="detail-card-value">${visita.ninos_menores}</div>
                        </div>

                        <div class="detail-card detail-card-highlight">
                            <div class="detail-card-label">💰 Total</div>
                            <div class="detail-card-value">${parseFloat(visita.precio_total).toFixed(2)}€</div>
                        </div>
                    </div>
                `;

                document.getElementById('visita-details-content').innerHTML = detailsHtml;
                document.getElementById('visitaDetailsModal').style.display = 'block';
            } else {
                console.error('❌ Error:', data);
                alert('❌ Error obteniendo datos de la visita: ' + (data.data || 'Error desconocido'));
            }
        })
        .catch(error => {
            console.error('❌ Error de conexión:', error);
            alert('❌ Error de conexión al obtener detalles');
        });
}

/**
 * Cerrar modal de detalles
 */
function closeVisitaDetailsModal() {
    const modal = document.querySelector('.modal-overlay');
    if (modal) {
        modal.remove();
    }
}

/**
 * Cancelar visita
 */
function cancelVisita(visitaId) {
    if (!confirm('¿Estás seguro de que deseas cancelar esta visita?')) {
        return;
    }

    const motivo = prompt('Motivo de cancelación (opcional):');

    jQuery.ajax({
        url: reservasAjax.ajax_url,
        type: 'POST',
        data: {
            action: 'cancel_visita',
            nonce: reservasAjax.nonce,
            visita_id: visitaId,
            motivo_cancelacion: motivo || 'Cancelación administrativa'
        },
        success: function (response) {
            if (response.success) {
                alert('Visita cancelada correctamente');
                loadVisitasReport();
            } else {
                alert('Error: ' + response.data);
            }
        }
    });
}

/**
 * Mostrar estado de carga
 */
function showVisitasLoading() {
    const container = document.getElementById('visitas-list-container');
    container.innerHTML = '<div class="loading">Cargando datos...</div>';
}

/**
 * Mostrar error
 */
function showError(message) {
    const container = document.getElementById('visitas-list-container');
    container.innerHTML = `<div class="error">${message}</div>`;
}


/**
 * Obtener horarios disponibles para el filtro de PDF de visitas
 */
function getAvailableSchedulesForVisitasPDF() {
    console.log('=== OBTENIENDO HORARIOS DISPONIBLES PARA VISITAS ===');

    const fecha_inicio = document.getElementById('visitas-fecha-inicio').value;
    const fecha_fin = document.getElementById('visitas-fecha-fin').value;
    const tipo_fecha = document.getElementById('visitas-tipo-fecha').value;
    const estado_filtro = document.getElementById('visitas-estado-filtro').value;
    const agency_filter = document.getElementById('visitas-agency-filter').value;

    jQuery.ajax({
        url: reservasAjax.ajax_url,
        type: 'POST',
        data: {
            action: 'get_available_schedules_for_visitas_pdf',
            nonce: reservasAjax.nonce,
            fecha_inicio: fecha_inicio,
            fecha_fin: fecha_fin,
            tipo_fecha: tipo_fecha,
            estado_filtro: estado_filtro,
            agency_filter: agency_filter
        },
        success: function (response) {
            console.log('Horarios disponibles:', response);

            if (response.success && response.data.schedules) {
                showSchedulesModalForVisitas(response.data.schedules, response.data);
            } else {
                alert('No se encontraron horarios disponibles para los filtros seleccionados');
            }
        },
        error: function (xhr, status, error) {
            console.error('Error obteniendo horarios:', error);
            alert('Error al obtener los horarios disponibles');
        }
    });
}

/**
 * Mostrar modal de selección de horarios para visitas
 */
function showSchedulesModalForVisitas(schedules, stats) {
    console.log('Mostrando modal con', schedules.length, 'horarios');

    const modalHtml = `
        <div class="modal-overlay" id="schedules-modal-visitas" style="
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
        ">
            <div style="
                background: white;
                padding: 30px;
                border-radius: 15px;
                max-width: 600px;
                width: 90%;
                max-height: 80vh;
                overflow-y: auto;
                box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            ">
                <h2 style="margin-top: 0; color: #667eea; text-align: center;">
                    📅 Seleccionar Horarios para el Informe
                </h2>
                
                <div style="background: #f7fafc; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                    <p style="margin: 5px 0;"><strong>Total de visitas:</strong> ${stats.total_services || 0}</p>
                    <p style="margin: 5px 0;"><strong>Días con servicio:</strong> ${stats.days_with_services || 0}</p>
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: flex; align-items: center; gap: 10px; padding: 10px; background: #e6f3ff; border-radius: 5px; cursor: pointer;">
                        <input type="checkbox" id="select-all-schedules-visitas" onchange="toggleAllSchedulesVisitas(this)">
                        <strong>Seleccionar todos los horarios</strong>
                    </label>
                </div>

                <div id="schedules-list-visitas" style="max-height: 300px; overflow-y: auto;">
                    ${schedules.map(schedule => `
                        <label style="
                            display: flex;
                            align-items: center;
                            gap: 10px;
                            padding: 12px;
                            margin-bottom: 8px;
                            background: #f9f9f9;
                            border-radius: 8px;
                            cursor: pointer;
                            transition: all 0.2s;
                        " onmouseover="this.style.background='#e6f3ff'" onmouseout="this.style.background='#f9f9f9'">
                            <input type="checkbox" class="schedule-checkbox-visitas" value='${JSON.stringify({ hora: schedule.hora })}'>
                            <span style="flex: 1;">
                                <strong>Hora:</strong> ${schedule.hora.substring(0, 5)}
                                <br>
                                <small style="color: #666;">
                                    ${schedule.count} visita(s) en ${schedule.days_count} día(s)
                                </small>
                            </span>
                        </label>
                    `).join('')}
                </div>

                <div style="margin-top: 20px; display: flex; gap: 10px; justify-content: flex-end;">
                    <button onclick="closeSchedulesModalVisitas()" style="
                        padding: 10px 20px;
                        background: #e2e8f0;
                        border: none;
                        border-radius: 5px;
                        cursor: pointer;
                    ">
                        Cancelar
                    </button>
                    <button onclick="generateVisitasPDFWithSchedules()" style="
                        padding: 10px 20px;
                        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                        color: white;
                        border: none;
                        border-radius: 5px;
                        cursor: pointer;
                        font-weight: 600;
                    ">
                        Generar PDF
                    </button>
                </div>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', modalHtml);
}

/**
 * Alternar selección de todos los horarios
 */
function toggleAllSchedulesVisitas(checkbox) {
    const checkboxes = document.querySelectorAll('.schedule-checkbox-visitas');
    checkboxes.forEach(cb => {
        cb.checked = checkbox.checked;
    });
}

/**
 * Cerrar modal de horarios
 */
function closeSchedulesModalVisitas() {
    const modal = document.getElementById('schedules-modal-visitas');
    if (modal) {
        modal.remove();
    }
}

/**
 * Generar PDF con horarios seleccionados
 */
function generateVisitasPDFWithSchedules() {
    const checkboxes = document.querySelectorAll('.schedule-checkbox-visitas:checked');

    if (checkboxes.length === 0) {
        alert('Por favor, selecciona al menos un horario');
        return;
    }

    const selectedSchedules = Array.from(checkboxes).map(cb => JSON.parse(cb.value));

    console.log('Horarios seleccionados:', selectedSchedules);

    closeSchedulesModalVisitas();

    // Generar PDF con los horarios seleccionados
    const fecha_inicio = document.getElementById('visitas-fecha-inicio').value;
    const fecha_fin = document.getElementById('visitas-fecha-fin').value;
    const tipo_fecha = document.getElementById('visitas-tipo-fecha').value;
    const estado_filtro = document.getElementById('visitas-estado-filtro').value;
    const agency_filter = document.getElementById('visitas-agency-filter').value;

    showVisitasLoading();

    jQuery.ajax({
        url: reservasAjax.ajax_url,
        type: 'POST',
        data: {
            action: 'generate_visitas_pdf_report',
            nonce: reservasAjax.nonce,
            fecha_inicio: fecha_inicio,
            fecha_fin: fecha_fin,
            tipo_fecha: tipo_fecha,
            estado_filtro: estado_filtro,
            agency_filter: agency_filter,
            selected_schedules: JSON.stringify(selectedSchedules)
        },
        success: function (response) {
            console.log('Respuesta PDF:', response);

            if (response.success && response.data.pdf_url) {
                // Abrir PDF en nueva ventana
                window.open(response.data.pdf_url, '_blank');

                // Mostrar mensaje de éxito
                showNotification('PDF generado correctamente', 'success');
            } else {
                alert('Error generando el PDF: ' + (response.data || 'Error desconocido'));
            }

            hideVisitasLoading();
        },
        error: function (xhr, status, error) {
            console.error('Error AJAX:', error);
            alert('Error de conexión al generar el PDF');
            hideVisitasLoading();
        }
    });
}

function showVisitasLoading() {
    const container = document.getElementById('visitas-list-container');
    if (container) {
        container.innerHTML = '<div class="loading">Generando PDF...</div>';
    }
}

function hideVisitasLoading() {
    // La lista se recargará automáticamente
}