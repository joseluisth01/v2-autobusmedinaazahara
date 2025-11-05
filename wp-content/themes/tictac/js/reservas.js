/* Script para el sistema de reservas */
jQuery(document).ready(function($) {
    // Variables globales
    let pasoActual = 1;
    let servicioSeleccionado = null;
    let fechaSeleccionada = null;
    let horaSeleccionada = null;
    let infoServicio = {};
    
    // Elementos DOM
    const contenedor = $('.reserva-contenedor');
    const btnAnterior = $('.reserva-boton-anterior');
    const btnSiguiente = $('.reserva-boton-siguiente');
    
    // Inicialización
    inicializarFormulario();
    
    // Función de inicialización
    function inicializarFormulario() {
        // Cargar servicios
        cargarServicios();
        
        // Eventos de navegación
        btnAnterior.on('click', irPasoAnterior);
        btnSiguiente.on('click', irPasoSiguiente);
        
        // Inicializar paso 1
        mostrarPaso(1);
    }
    
    // Función para cargar servicios desde el servidor
    function cargarServicios() {
        contenedor.find('.reserva-servicios').html('<p>' + customReservas.textos.cargando + '</p>');
        
        $.ajax({
            url: customReservas.ajax_url,
            type: 'POST',
            data: {
                action: 'obtener_servicios',
                nonce: customReservas.nonce
            },
            success: function(respuesta) {
                if (respuesta.success) {
                    mostrarServicios(respuesta.data);
                } else {
                    contenedor.find('.reserva-servicios').html('<p>Error al cargar servicios</p>');
                }
            },
            error: function() {
                contenedor.find('.reserva-servicios').html('<p>Error de conexión</p>');
            }
        });
    }
    
    // Función para mostrar servicios
    function mostrarServicios(categorias) {
        const serviciosContainer = contenedor.find('.reserva-servicios');
        serviciosContainer.empty();
        
        // Crear elementos para cada categoría y servicio
        for (const categoria in categorias) {
            const categoriaDiv = $('<div class="reserva-categoria"></div>');
            categoriaDiv.append('<h3>' + categoria + '</h3>');
            
            const serviciosDiv = $('<div class="reserva-servicios-lista"></div>');
            
            categorias[categoria].forEach(servicio => {
                const servicioDiv = $('<div class="reserva-servicio" data-id="' + servicio.id + '">' + servicio.nombre + '</div>');
                servicioDiv.on('click', function() {
                    seleccionarServicio(servicio.id);
                });
                serviciosDiv.append(servicioDiv);
            });
            
            categoriaDiv.append(serviciosDiv);
            serviciosContainer.append(categoriaDiv);
        }
    }
    
    // Función para seleccionar un servicio
    function seleccionarServicio(id) {
        servicioSeleccionado = id;
        contenedor.find('.reserva-servicio').removeClass('seleccionado');
        contenedor.find('.reserva-servicio[data-id="' + id + '"]').addClass('seleccionado');
        
        // Habilitar botón siguiente
        btnSiguiente.prop('disabled', false);
    }
    
    // Función para cargar calendario
    function cargarCalendario() {
        if (!servicioSeleccionado) {
            return;
        }
        
        contenedor.find('.reserva-calendario-dias').html('<p>' + customReservas.textos.cargando + '</p>');
        
        $.ajax({
            url: customReservas.ajax_url,
            type: 'POST',
            data: {
                action: 'obtener_dias_disponibles',
                servicio_id: servicioSeleccionado,
                nonce: customReservas.nonce
            },
            success: function(respuesta) {
                if (respuesta.success) {
                    infoServicio = respuesta.data.info_servicio;
                    mostrarCalendario(respuesta.data);
                } else {
                    contenedor.find('.reserva-calendario-dias').html('<p>Error al cargar calendario</p>');
                }
            },
            error: function() {
                contenedor.find('.reserva-calendario-dias').html('<p>Error de conexión</p>');
            }
        });
    }
    
    // Función para mostrar calendario
    function mostrarCalendario(data) {
        const diasContainer = contenedor.find('.reserva-calendario-dias');
        diasContainer.empty();
        
        // Mostrar información del servicio
        const infoDiv = $('<div class="reserva-info-servicio"></div>');
        infoDiv.append('<h3>' + data.info_servicio.categoria + ' - ' + data.info_servicio.nombre + '</h3>');
        infoDiv.append('<p>Horario: ' + data.horario_inicio + ' - ' + data.horario_fin + '</p>');
        diasContainer.append(infoDiv);
        
        // Crear calendario
        const hoy = new Date();
        const fechaMinima = new Date(data.fecha_minima);
        const fechaMaxima = new Date(data.fecha_maxima);
        
        let mesActual = fechaMinima.getMonth();
        let anioActual = fechaMinima.getFullYear();
        
        // Crear navegación de mes
        const navegacionMes = $('<div class="reserva-calendario-navegacion"></div>');
        const btnMesAnterior = $('<button class="reserva-calendario-mes-anterior">◄</button>');
        const btnMesSiguiente = $('<button class="reserva-calendario-mes-siguiente">►</button>');
        const tituloMes = $('<div class="reserva-calendario-mes"></div>');
        
        navegacionMes.append(btnMesAnterior);
        navegacionMes.append(tituloMes);
        navegacionMes.append(btnMesSiguiente);
        diasContainer.append(navegacionMes);
        
        // Crear días de la semana
        const diasSemana = $('<div class="reserva-calendario-semana"></div>');
        ['L', 'M', 'X', 'J', 'V', 'S', 'D'].forEach(dia => {
            diasSemana.append('<div class="reserva-calendario-dia-nombre">' + dia + '</div>');
        });
        diasContainer.append(diasSemana);
        
        // Crear rejilla de días
        const diasGrid = $('<div class="reserva-calendario-dias-grid"></div>');
        diasContainer.append(diasGrid);
        
        // Función para renderizar un mes
        function renderizarMes(mes, anio) {
            tituloMes.text(obtenerNombreMes(mes) + ' ' + anio);
            diasGrid.empty();
            
            const primerDia = new Date(anio, mes, 1);
            const ultimoDia = new Date(anio, mes + 1, 0);
            
            // Ajustar para que la semana comience en lunes (1) en lugar de domingo (0)
            let diaSemanaInicio = primerDia.getDay();
            diaSemanaInicio = diaSemanaInicio === 0 ? 6 : diaSemanaInicio - 1;
            
            // Añadir espacios vacíos para los días anteriores al primer día del mes
            for (let i = 0; i < diaSemanaInicio; i++) {
                diasGrid.append('<div class="reserva-calendario-dia vacio"></div>');
            }
            
            // Añadir los días del mes
            for (let dia = 1; dia <= ultimoDia.getDate(); dia++) {
                const fecha = new Date(anio, mes, dia);
                const fechaStr = formatearFecha(fecha);
                
                // Determinar si el día está disponible
                let disponible = true;
                let razon = '';
                
                // Verificar si es una fecha pasada
                if (fecha < fechaMinima) {
                    disponible = false;
                    razon = 'Fecha pasada';
                }
                
                // Verificar si es una fecha futura muy lejana
                if (fecha > fechaMaxima) {
                    disponible = false;
                    razon = 'Fecha fuera de rango';
                }
                
                // Verificar si el día está bloqueado
                if (data.dias_bloqueados.includes(fechaStr)) {
                    disponible = false;
                    razon = 'Día bloqueado';
                }
                
                // Verificar si el día de la semana está disponible
                const diaSemana = fecha.getDay();
                const diasCodigoMap = [6, 0, 1, 2, 3, 4, 5]; // Mapeo de JS (0=domingo) a nuestro código (0=lunes)
                const diaCodigo = diasCodigoMap[diaSemana];
                const diasDisponiblesMap = {
                    'lun': 0, 'mar': 1, 'mie': 2, 'jue': 3, 'vie': 4, 'sab': 5, 'dom': 6
                };
                
                let diaPermitido = false;
                data.dias_semana_disponibles.forEach(dia => {
                    if (diasDisponiblesMap[dia] === diaCodigo) {
                        diaPermitido = true;
                    }
                });
                
                if (!diaPermitido) {
                    disponible = false;
                    razon = 'Día no disponible';
                }
                
                // Crear el elemento del día
                const diaElem = $('<div class="reserva-calendario-dia' + (disponible ? '' : ' no-disponible') + '" data-fecha="' + fechaStr + '" title="' + (disponible ? 'Disponible' : razon) + '">' + dia + '</div>');
                
                if (disponible) {
                    diaElem.on('click', function() {
                        seleccionarFecha(fechaStr);
                    });
                }
                
                diasGrid.append(diaElem);
            }
        }
        
        // Renderizar mes inicial
        renderizarMes(mesActual, anioActual);
        
        // Eventos para navegar entre meses
        btnMesAnterior.on('click', function() {
            mesActual--;
            if (mesActual < 0) {
                mesActual = 11;
                anioActual--;
            }
            renderizarMes(mesActual, anioActual);
        });
        
        btnMesSiguiente.on('click', function() {
            mesActual++;
            if (mesActual > 11) {
                mesActual = 0;
                anioActual++;
            }
            renderizarMes(mesActual, anioActual);
        });
    }
    
    // Función para seleccionar una fecha
    function seleccionarFecha(fecha) {
        fechaSeleccionada = fecha;
        contenedor.find('.reserva-calendario-dia').removeClass('seleccionado');
        contenedor.find('.reserva-calendario-dia[data-fecha="' + fecha + '"]').addClass('seleccionado');
        
        // Cargar horas disponibles
        cargarHorasDisponibles();
    }
    
    // Función para cargar horas disponibles
    function cargarHorasDisponibles() {
        if (!servicioSeleccionado || !fechaSeleccionada) {
            return;
        }
        
        contenedor.find('.reserva-calendario-horas').html('<p>' + customReservas.textos.cargando + '</p>');
        
        $.ajax({
            url: customReservas.ajax_url,
            type: 'POST',
            data: {
                action: 'obtener_horas_disponibles',
                servicio_id: servicioSeleccionado,
                fecha: fechaSeleccionada,
                nonce: customReservas.nonce
            },
            success: function(respuesta) {
                if (respuesta.success) {
                    mostrarHorasDisponibles(respuesta.data);
                } else {
                    contenedor.find('.reserva-calendario-horas').html('<p>Error al cargar horas</p>');
                }
            },
            error: function() {
                contenedor.find('.reserva-calendario-horas').html('<p>Error de conexión</p>');
            }
        });
    }
    
    // Función para mostrar horas disponibles
    function mostrarHorasDisponibles(data) {
        const horasContainer = contenedor.find('.reserva-calendario-horas');
        horasContainer.empty();
        
        // Título
        horasContainer.append('<h3>Horas disponibles</h3>');
        
        // Lista de horas
        const horasLista = $('<div class="reserva-horas-lista"></div>');
        
        data.horas.forEach(hora => {
            const horaElem = $('<div class="reserva-hora' + (hora.disponible ? '' : ' no-disponible') + '" data-hora="' + hora.hora + '" title="' + (hora.disponible ? 'Disponible' : hora.mensaje) + '">' + hora.hora + '</div>');
            
            if (hora.disponible) {
                horaElem.on('click', function() {
                    seleccionarHora(hora.hora);
                });
            }
            
            horasLista.append(horaElem);
        });
        
        horasContainer.append(horasLista);
        
        // Habilitar botón siguiente si ya hay una hora seleccionada
        if (horaSeleccionada) {
            btnSiguiente.prop('disabled', false);
        }
    }
    
    // Función para seleccionar una hora
    function seleccionarHora(hora) {
        horaSeleccionada = hora;
        contenedor.find('.reserva-hora').removeClass('seleccionada');
        contenedor.find('.reserva-hora[data-hora="' + hora + '"]').addClass('seleccionada');
        
        // Habilitar botón siguiente
        btnSiguiente.prop('disabled', false);
    }
    
    // Función para ir al paso anterior
    function irPasoAnterior() {
        if (pasoActual > 1) {
            mostrarPaso(pasoActual - 1);
        }
    }
    
    // Función para ir al paso siguiente
    function irPasoSiguiente() {
        if (pasoActual < 3) {
            // Validar paso actual
            if (validarPasoActual()) {
                mostrarPaso(pasoActual + 1);
            }
        } else {
            // Enviar formulario
            enviarFormulario();
        }
    }
    
    // Función para mostrar un paso
    function mostrarPaso(paso) {
        pasoActual = paso;
        
        // Ocultar todos los pasos
        contenedor.find('.reserva-paso-contenido').hide();
        
        // Mostrar paso actual
        contenedor.find('.reserva-paso-' + paso).show();
        
        // Actualizar navegación de pasos
        contenedor.find('.reserva-paso').removeClass('activo completado');
        
        for (let i = 1; i <= 3; i++) {
            if (i < paso) {
                contenedor.find('.reserva-paso[data-paso="' + i + '"]').addClass('completado');
            } else if (i === paso) {
                contenedor.find('.reserva-paso[data-paso="' + i + '"]').addClass('activo');
            }
        }
        
        // Actualizar botones
        btnAnterior.prop('disabled', paso === 1);
        
        if (paso === 1) {
            btnSiguiente.prop('disabled', !servicioSeleccionado);
        } else if (paso === 2) {
            btnSiguiente.prop('disabled', !horaSeleccionada);
            cargarCalendario();
        } else if (paso === 3) {
            btnSiguiente.text(customReservas.textos.completar_reserva);
        }
    }
        // Función para validar el paso actual
    function validarPasoActual() {
        if (pasoActual === 1) {
            if (!servicioSeleccionado) {
                alert(customReservas.textos.selecciona_servicio);
                return false;
            }
        } else if (pasoActual === 2) {
            if (!fechaSeleccionada) {
                alert(customReservas.textos.selecciona_fecha);
                return false;
            }
            if (!horaSeleccionada) {
                alert(customReservas.textos.selecciona_hora);
                return false;
            }
        } else if (pasoActual === 3) {
            // Validar formulario
            const nombre = $('#reserva-nombre').val();
            const email = $('#reserva-email').val();
            const telefono = $('#reserva-telefono').val();
            const numAdultos = $('#reserva-num-adultos').val();
            
            if (!nombre || !email || !telefono || !numAdultos) {
                alert('Por favor, completa todos los campos obligatorios');
                return false;
            }
            
            // Validar email
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                alert('Por favor, introduce un email válido');
                return false;
            }
            
            // Validar teléfono (formato básico)
            const telefonoRegex = /^\d{9,}$/;
            if (!telefonoRegex.test(telefono.replace(/\s/g, ''))) {
                alert('Por favor, introduce un número de teléfono válido');
                return false;
            }
        }
        
        return true;
    }
    
    // Función para enviar el formulario
    function enviarFormulario() {
        const nombre = $('#reserva-nombre').val();
        const email = $('#reserva-email').val();
        const telefono = $('#reserva-telefono').val();
        const numAdultos = $('#reserva-num-adultos').val();
        const numNinos = $('#reserva-num-ninos').val();
        const comentarios = $('#reserva-comentarios').val();
        
        // Deshabilitar botón para evitar doble envío
        btnSiguiente.prop('disabled', true).text('Enviando...');
        
        $.ajax({
            url: customReservas.ajax_url,
            type: 'POST',
            data: {
                action: 'guardar_reserva',
                servicio_id: servicioSeleccionado,
                fecha: fechaSeleccionada,
                hora: horaSeleccionada,
                nombre: nombre,
                email: email,
                telefono: telefono,
                num_adultos: numAdultos,
                num_ninos: numNinos,
                comentarios: comentarios,
                nonce: customReservas.nonce
            },
            success: function(respuesta) {
                if (respuesta.success) {
                    // Redirigir a página de éxito
                    window.location.href = window.location.href + (window.location.href.indexOf('?') !== -1 ? '&' : '?') + 'reserva_completada=1';
                } else {
                    alert('Error: ' + respuesta.data);
                    btnSiguiente.prop('disabled', false).text(customReservas.textos.completar_reserva);
                }
            },
            error: function() {
                alert('Error de conexión');
                btnSiguiente.prop('disabled', false).text(customReservas.textos.completar_reserva);
            }
        });
    }
    
    // Funciones auxiliares
    function obtenerNombreMes(mes) {
        const meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        return meses[mes];
    }
    
    function formatearFecha(fecha) {
        const anio = fecha.getFullYear();
        const mes = (fecha.getMonth() + 1).toString().padStart(2, '0');
        const dia = fecha.getDate().toString().padStart(2, '0');
        return anio + '-' + mes + '-' + dia;
    }
});
