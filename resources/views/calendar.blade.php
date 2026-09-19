<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cirugía Digestiva & Clínica Médica · Agenda de Citas</title>

    <!-- Google Fonts: Raleway (Encabezados) y Open Sans (Cuerpo) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600;700&family=Raleway:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- FontAwesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Tailwind CSS (CDN con configuración de paleta hospitalaria personalizada) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        heading: ['Raleway', 'sans-serif'],
                        body: ['"Open Sans"', 'sans-serif'],
                    },
                    colors: {
                        hospital: {
                            deep: '#2B6B8A',     // Azul hospitalario profundo
                            pastel: '#4A89A7',   // Azul pastel clínico
                            ice: '#EBF4F8',      // Azul hielo suave
                            hover: '#1F536C',    // Hover azul
                            dark: '#163E52',     // Acento oscuro
                        },
                        clinica: {
                            mint: '#0DB26B',     // Verde esmeralda cirugía digestiva
                            soft: '#E6F8F0',     // Verde menta suave
                            hover: '#0A9659',    // Hover verde
                        },
                        estado: {
                            pendienteBg: '#FEF3C7',
                            pendienteBorder: '#F59E0B',
                            pendienteText: '#92400E',
                            confirmadaBg: '#E0F2FE',
                            confirmadaBorder: '#2B6B8A',
                            confirmadaText: '#0369A1',
                            atendidaBg: '#E6F8F0',
                            atendidaBorder: '#0DB26B',
                            atendidaText: '#065F46',
                            canceladaBg: '#FEE2E2',
                            canceladaBorder: '#EF4444',
                            canceladaText: '#991B1B',
                        }
                    }
                }
            }
        }
    </script>

    <!-- FullCalendar v6 Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>

    <!-- SweetAlert2 para Notificaciones y Alertas Interactivas -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            font-family: 'Open Sans', sans-serif;
            background-color: #F8FAFC;
        }
        h1, h2, h3, h4, .font-heading {
            font-family: 'Raleway', sans-serif;
        }
        /* Personalización sutil de FullCalendar para alinearlo a la paleta médica */
        .fc {
            --fc-border-color: #E2E8F0;
            --fc-button-bg-color: #2B6B8A;
            --fc-button-border-color: #2B6B8A;
            --fc-button-hover-bg-color: #1F536C;
            --fc-button-hover-border-color: #1F536C;
            --fc-button-active-bg-color: #163E52;
            --fc-button-active-border-color: #163E52;
            --fc-today-bg-color: #F0F7FA;
            font-family: 'Open Sans', sans-serif;
        }
        .fc .fc-toolbar-title {
            font-family: 'Raleway', sans-serif;
            font-size: 1.35rem;
            font-weight: 700;
            color: #1E293B;
        }
        .fc-col-header-cell-cushion {
            font-family: 'Raleway', sans-serif;
            font-weight: 600;
            color: #334155;
            padding: 8px 4px !important;
            text-transform: capitalize;
        }
        .fc-event {
            border-radius: 6px !important;
            padding: 3px 6px !important;
            font-size: 0.82rem !important;
            font-weight: 600 !important;
            cursor: pointer;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }
        .fc-event:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="text-slate-800 antialiased min-h-screen flex flex-col">

    <!-- BARRA SUPERIOR / HEADER CLÍNICO -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                
                <!-- Identidad Clínica (Inspirada en Cirugía Digestiva) -->
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-hospital-deep to-hospital-pastel flex items-center justify-center text-white shadow-sm">
                        <i class="fa-solid fa-hospital-user text-2xl"></i>
                    </div>
                    <div>
                        <div class="flex items-center space-x-2">
                            <span class="font-heading font-extrabold text-xl sm:text-2xl text-hospital-deep tracking-tight">Cirugía Digestiva</span>
                            <span class="px-2 py-0.5 text-xs font-bold rounded-full bg-clinica-soft text-clinica-mint border border-clinica-mint/30">Clínica Médica</span>
                        </div>
                        <p class="text-xs text-slate-500 font-medium">Sistema Integral de Citas Médicas · Segundo Parcial ASII</p>
                    </div>
                </div>

                <!-- Botón Acción Principal -->
                <div class="flex items-center space-x-3">
                    <button id="btnAbrirModalPaciente" class="inline-flex items-center space-x-2 px-3.5 py-2.5 bg-hospital-soft hover:bg-slate-100 text-hospital-deep border border-hospital-pastel/40 rounded-lg font-heading font-bold text-sm shadow-xs transition-all duration-200">
                        <i class="fa-solid fa-user-plus text-base text-hospital-pastel"></i>
                        <span class="hidden sm:inline">Nuevo Paciente</span>
                    </button>
                    <button id="btnAbrirModalCrear" class="inline-flex items-center space-x-2 px-4 py-2.5 bg-clinica-mint hover:bg-clinica-hover text-white rounded-lg font-heading font-bold text-sm shadow-sm transition-all duration-200 transform hover:scale-[1.02]">
                        <i class="fa-solid fa-calendar-plus text-base"></i>
                        <span>Nueva Cita</span>
                    </button>
                </div>


            </div>
        </div>
    </header>

    <!-- ÁREA PRINCIPAL DE CONTENIDO -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-5">
        
        <!-- BARRA DE FILTROS Y LEYENDA SEMÁFORO -->
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            
            <!-- Controles de Filtrado (RQF-06) -->
            <div class="flex flex-wrap items-center gap-3">
                
                <!-- Filtro por Doctor -->
                <div class="flex items-center space-x-2">
                    <label for="filtroDoctor" class="text-xs font-bold text-slate-600 uppercase tracking-wider flex items-center gap-1">
                        <i class="fa-solid fa-user-doctor text-hospital-pastel"></i> Doctor:
                    </label>
                    <select id="filtroDoctor" class="text-sm border border-slate-300 rounded-lg px-3 py-1.5 bg-slate-50 focus:bg-white focus:border-hospital-deep focus:ring-1 focus:ring-hospital-deep outline-hidden font-medium">
                        <option value="">Todos los doctores</option>
                    </select>
                </div>

                <!-- Filtro por Estado -->
                <div class="flex items-center space-x-2">
                    <label for="filtroEstado" class="text-xs font-bold text-slate-600 uppercase tracking-wider flex items-center gap-1">
                        <i class="fa-solid fa-filter text-hospital-pastel"></i> Estado:
                    </label>
                    <select id="filtroEstado" class="text-sm border border-slate-300 rounded-lg px-3 py-1.5 bg-slate-50 focus:bg-white focus:border-hospital-deep focus:ring-1 focus:ring-hospital-deep outline-hidden font-medium">
                        <option value="">Todos los estados</option>
                        <option value="pendiente">Pendiente</option>
                        <option value="confirmada">Confirmada</option>
                        <option value="atendida">Atendida</option>
                        <option value="cancelada">Cancelada</option>
                    </select>
                </div>

                <!-- Botón Refrescar -->
                <button id="btnRefrescar" class="px-2.5 py-1.5 text-slate-600 hover:text-hospital-deep hover:bg-slate-100 rounded-lg text-sm border border-slate-200 transition" title="Refrescar agenda">
                    <i class="fa-solid fa-rotate-right"></i>
                </button>
            </div>

            <!-- Leyenda de Estados por Color (RQF-10) -->
            <div class="flex flex-wrap items-center gap-3 text-xs font-medium">
                <span class="text-slate-400 font-semibold text-[11px] uppercase tracking-wider">Leyenda:</span>
                
                <span class="inline-flex items-center space-x-1.5 px-2.5 py-1 rounded-md bg-estado-pendienteBg border border-estado-pendienteBorder text-estado-pendienteText font-semibold">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    <span>Pendiente</span>
                </span>

                <span class="inline-flex items-center space-x-1.5 px-2.5 py-1 rounded-md bg-estado-confirmadaBg border border-estado-confirmadaBorder text-estado-confirmadaText font-semibold">
                    <span class="w-2 h-2 rounded-full bg-hospital-deep"></span>
                    <span>Confirmada</span>
                </span>

                <span class="inline-flex items-center space-x-1.5 px-2.5 py-1 rounded-md bg-estado-atendidaBg border border-estado-atendidaBorder text-estado-atendidaText font-semibold">
                    <span class="w-2 h-2 rounded-full bg-clinica-mint"></span>
                    <span>Atendida</span>
                </span>

                <span class="inline-flex items-center space-x-1.5 px-2.5 py-1 rounded-md bg-estado-canceladaBg border border-estado-canceladaBorder text-estado-canceladaText font-semibold">
                    <span class="w-2 h-2 rounded-full bg-red-500"></span>
                    <span>Cancelada</span>
                </span>
            </div>

        </div>

        <!-- CONTENEDOR DEL CALENDARIO FULLCALENDAR (RQF-02, RQNF-06) -->
        <div class="bg-white p-4 sm:p-6 rounded-xl border border-slate-200 shadow-xs">
            <div id="calendar" class="min-h-[680px]"></div>
        </div>

    </main>

    <!-- PIE DE PÁGINA INFORMATIVO -->
    <footer class="bg-white border-t border-slate-200 mt-auto py-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row justify-between items-center text-xs text-slate-500 gap-2">
            <div>
                <strong>Universidad Mariano Gálvez de Guatemala</strong> · Análisis de Sistemas II · Segundo Parcial
            </div>
            <div class="flex items-center space-x-4">
                <span><i class="fa-brands fa-docker text-hospital-deep mr-1"></i> MySQL 8.0 en Docker</span>
                <span><i class="fa-brands fa-laravel text-red-500 mr-1"></i> Laravel 12 REST API</span>
                <span><i class="fa-solid fa-calendar-days text-clinica-mint mr-1"></i> FullCalendar v6</span>
            </div>
        </div>
    </footer>

    <!-- ================================================================= -->
    <!-- MODAL 1: CREAR NUEVA CITA MÉDICA (RQF-01, RQF-08, RQNF-07) -->
    <!-- ================================================================= -->
    <div id="modalCrearCita" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center hidden p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 transform transition-all">
            
            <!-- Encabezado Modal -->
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-lg bg-clinica-soft text-clinica-mint flex items-center justify-center">
                        <i class="fa-solid fa-calendar-plus text-lg"></i>
                    </div>
                    <div>
                        <h3 class="font-heading font-bold text-lg text-slate-800">Nueva Cita Médica</h3>
                        <p class="text-xs text-slate-500">Completa los campos para agendar la cita en el servidor</p>
                    </div>
                </div>
                <button type="button" class="btnCerrarModal text-slate-400 hover:text-slate-600 p-1">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Formulario de Creación -->
            <form id="formCrearCita" class="mt-4 space-y-4">
                
                <!-- Mensaje de Error en Servidor -->
                <div id="bannerErrorCrear" class="hidden p-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-xs font-medium space-y-1"></div>

                <!-- Selección de Paciente (RQF-08, RQF-09) -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-bold text-slate-700" for="crearPacienteId">
                            Paciente *
                        </label>
                        <button type="button" id="btnCrearPacienteRapido" class="text-xs font-bold text-hospital-deep hover:text-hospital-hover hover:underline flex items-center gap-1 cursor-pointer">
                            <i class="fa-solid fa-user-plus text-[11px]"></i> + Registrar Paciente
                        </button>
                    </div>
                    <select id="crearPacienteId" name="paciente_id" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:border-hospital-deep focus:ring-1 focus:ring-hospital-deep outline-hidden bg-slate-50 focus:bg-white">
                        <option value="">Cargando lista de pacientes...</option>
                    </select>
                </div>


                <!-- Selección de Doctor (RQF-08) -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1" for="crearDoctorId">
                        Doctor / Especialidad *
                    </label>
                    <select id="crearDoctorId" name="doctor_id" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:border-hospital-deep focus:ring-1 focus:ring-hospital-deep outline-hidden bg-slate-50 focus:bg-white">
                        <option value="">Cargando lista de doctores...</option>
                    </select>
                </div>

                <!-- Fecha de la Cita -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1" for="crearFecha">
                        Fecha de la Consulta *
                    </label>
                    <input type="date" id="crearFecha" name="fecha" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:border-hospital-deep focus:ring-1 focus:ring-hospital-deep outline-hidden bg-slate-50 focus:bg-white">
                </div>

                <!-- Rango Horario (Inicio y Fin) -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1" for="crearHoraInicio">
                            Hora Inicio *
                        </label>
                        <input type="time" id="crearHoraInicio" name="hora_inicio" required step="60" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:border-hospital-deep focus:ring-1 focus:ring-hospital-deep outline-hidden bg-slate-50 focus:bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1" for="crearHoraFin">
                            Hora Fin *
                        </label>
                        <input type="time" id="crearHoraFin" name="hora_fin" required step="60" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:border-hospital-deep focus:ring-1 focus:ring-hospital-deep outline-hidden bg-slate-50 focus:bg-white">
                    </div>
                </div>

                <!-- Motivo de la Cita -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1" for="crearMotivo">
                        Motivo de la Cita *
                    </label>
                    <textarea id="crearMotivo" name="motivo" rows="2" required placeholder="Describe brevemente la razón médica de la consulta..." class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:border-hospital-deep focus:ring-1 focus:ring-hospital-deep outline-hidden bg-slate-50 focus:bg-white"></textarea>
                </div>

                <!-- Botones de Acción -->
                <div class="flex items-center justify-end space-x-3 pt-3 border-t border-slate-100">
                    <button type="button" class="btnCerrarModal px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-100 rounded-lg transition">
                        Cancelar
                    </button>
                    <button type="submit" id="btnGuardarCita" class="inline-flex items-center space-x-2 px-5 py-2 bg-hospital-deep hover:bg-hospital-hover text-white rounded-lg font-heading font-bold text-sm shadow-sm transition">
                        <i class="fa-solid fa-check"></i>
                        <span>Agendar Cita</span>
                    </button>
                </div>

            </form>
        </div>
    </div>

    <!-- ================================================================= -->
    <!-- MODAL 2: DETALLE DE CITA MÉDICA Y ESTADOS (RQF-05, RQF-09, RQF-10) -->
    <!-- ================================================================= -->
    <div id="modalDetalleCita" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center hidden p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 transform transition-all">
            
            <!-- Encabezado Modal -->
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-lg bg-hospital-ice text-hospital-deep flex items-center justify-center">
                        <i class="fa-solid fa-stethoscope text-lg"></i>
                    </div>
                    <div>
                        <h3 class="font-heading font-bold text-lg text-slate-800">Detalle de la Cita Médica</h3>
                        <p class="text-xs text-slate-500">ID de Cita: #<span id="detalleCitaId"></span></p>
                    </div>
                </div>
                <button type="button" class="btnCerrarModalDetalle text-slate-400 hover:text-slate-600 p-1">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Ficha Técnica de la Cita -->
            <div class="mt-4 space-y-4 text-sm">
                
                <!-- Badge de Estado Actual -->
                <div class="flex items-center justify-between bg-slate-50 p-3 rounded-xl border border-slate-200">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Estado Actual:</span>
                    <span id="detalleEstadoBadge" class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider"></span>
                </div>

                <!-- Doctor -->
                <div class="flex items-start space-x-3">
                    <i class="fa-solid fa-user-doctor text-hospital-pastel mt-1 text-base"></i>
                    <div>
                        <div class="font-bold text-slate-800" id="detalleDoctorNombre"></div>
                        <div class="text-xs text-slate-500" id="detalleDoctorEspecialidad"></div>
                    </div>
                </div>

                <!-- Paciente -->
                <div class="flex items-start space-x-3">
                    <i class="fa-solid fa-hospital-user text-clinica-mint mt-1 text-base"></i>
                    <div>
                        <div class="font-bold text-slate-800" id="detallePacienteNombre"></div>
                        <div class="text-xs text-slate-500">Tel: <span id="detallePacienteTelefono"></span></div>
                    </div>
                </div>

                <!-- Fecha y Hora -->
                <div class="flex items-start space-x-3">
                    <i class="fa-solid fa-clock text-amber-500 mt-1 text-base"></i>
                    <div>
                        <div class="font-bold text-slate-800" id="detalleFecha"></div>
                        <div class="text-xs text-slate-600 font-semibold" id="detalleHorario"></div>
                    </div>
                </div>

                <!-- Motivo -->
                <div class="bg-slate-50 p-3 rounded-xl border border-slate-200">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">Motivo de Consulta:</span>
                    <p class="text-slate-700 text-xs leading-relaxed" id="detalleMotivo"></p>
                </div>

                <!-- ACCIONES DE CAMBIO DE ESTADO (RQF-05) -->
                <div class="pt-2 border-t border-slate-100">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-2">Gestionar Estado de la Cita:</span>
                    <div class="flex flex-wrap gap-2" id="contenedorAccionesEstado">
                        
                        <!-- Confirmar -->
                        <button type="button" id="btnAccionConfirmar" class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-lg bg-hospital-deep hover:bg-hospital-hover text-white text-xs font-bold transition">
                            <i class="fa-solid fa-circle-check"></i>
                            <span>Confirmar Cita</span>
                        </button>

                        <!-- Atender -->
                        <button type="button" id="btnAccionAtender" class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-lg bg-clinica-mint hover:bg-clinica-hover text-white text-xs font-bold transition">
                            <i class="fa-solid fa-user-check"></i>
                            <span>Marcar Atendida</span>
                        </button>

                        <!-- Cancelar -->
                        <button type="button" id="btnAccionCancelar" class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-lg bg-red-600 hover:bg-red-700 text-white text-xs font-bold transition ml-auto">
                            <i class="fa-solid fa-ban"></i>
                            <span>Cancelar Cita</span>
                        </button>
                    </div>
                </div>

            </div>

            <!-- Footer Modal -->
            <div class="flex justify-end pt-4 mt-4 border-t border-slate-100">
                <button type="button" class="btnCerrarModalDetalle px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-100 rounded-lg transition">
                    Cerrar
                </button>
            </div>

        </div>
    </div>

    <!-- ================================================================= -->
    <!-- MODAL 3: REGISTRO DE NUEVO PACIENTE (RQF-09, RQNF-03)             -->
    <!-- ================================================================= -->
    <div id="modalNuevoPaciente" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center hidden p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-100 transform transition-all">
            
            <!-- Encabezado Modal -->
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-lg bg-hospital-ice text-hospital-deep flex items-center justify-center">
                        <i class="fa-solid fa-user-plus text-lg"></i>
                    </div>
                    <div>
                        <h3 class="font-heading font-bold text-lg text-slate-800">Registrar Paciente</h3>
                        <p class="text-xs text-slate-500">Expediente y datos personales para citas médicas</p>
                    </div>
                </div>
                <button type="button" class="btnCerrarModalPaciente text-slate-400 hover:text-slate-600 p-1 cursor-pointer">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Formulario Paciente -->
            <form id="formCrearPaciente" class="mt-4 space-y-4">
                
                <!-- Alerta de Errores 400 -->
                <div id="bannerErrorPaciente" class="hidden p-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-xs font-medium space-y-1"></div>

                <!-- Nombre Completo (Obligatorio) -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1" for="pacienteNombre">
                        Nombre Completo *
                    </label>
                    <input type="text" id="pacienteNombre" name="nombre" required maxlength="120" placeholder="Ej. Mariana Morales Gómez" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:border-hospital-deep focus:ring-1 focus:ring-hospital-deep outline-hidden bg-slate-50 focus:bg-white">
                </div>

                <!-- Teléfono (Opcional) -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1" for="pacienteTelefono">
                        Teléfono
                    </label>
                    <input type="tel" id="pacienteTelefono" name="telefono" maxlength="25" placeholder="Ej. +502 4567-8901" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:border-hospital-deep focus:ring-1 focus:ring-hospital-deep outline-hidden bg-slate-50 focus:bg-white">
                </div>

                <!-- Correo Electrónico (Opcional) -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1" for="pacienteEmail">
                        Correo Electrónico
                    </label>
                    <input type="email" id="pacienteEmail" name="email" maxlength="120" placeholder="Ej. paciente@clinica.com" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:border-hospital-deep focus:ring-1 focus:ring-hospital-deep outline-hidden bg-slate-50 focus:bg-white">
                </div>

                <!-- Fecha de Nacimiento (Opcional) -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1" for="pacienteFechaNacimiento">
                        Fecha de Nacimiento
                    </label>
                    <input type="date" id="pacienteFechaNacimiento" name="fecha_nacimiento" max="{{ date('Y-m-d') }}" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:border-hospital-deep focus:ring-1 focus:ring-hospital-deep outline-hidden bg-slate-50 focus:bg-white">
                </div>

                <!-- Botones de Acción -->
                <div class="flex items-center justify-end space-x-3 pt-3 border-t border-slate-100">
                    <button type="button" class="btnCerrarModalPaciente px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-100 rounded-lg transition cursor-pointer">
                        Cancelar
                    </button>
                    <button type="submit" id="btnGuardarPaciente" class="inline-flex items-center space-x-2 px-5 py-2 bg-hospital-deep hover:bg-hospital-hover text-white rounded-lg font-heading font-bold text-sm shadow-sm transition cursor-pointer">
                        <i class="fa-solid fa-save"></i>
                        <span>Guardar Paciente</span>
                    </button>
                </div>
            </form>
        </div>
    </div>


    <!-- ================================================================= -->
    <!-- LOGICA JAVASCRIPT: FULLCALENDAR + CONSUMO DE API REST             -->
    <!-- ================================================================= -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            
            let calendar = null;
            let citaSeleccionada = null;

            // Elementos del DOM
            const calendarEl = document.getElementById('calendar');
            const filtroDoctorEl = document.getElementById('filtroDoctor');
            const filtroEstadoEl = document.getElementById('filtroEstado');
            const btnRefrescarEl = document.getElementById('btnRefrescar');
            const btnAbrirModalCrearEl = document.getElementById('btnAbrirModalCrear');

            const modalCrearCitaEl = document.getElementById('modalCrearCita');
            const formCrearCitaEl = document.getElementById('formCrearCita');
            const bannerErrorCrearEl = document.getElementById('bannerErrorCrear');
            const crearPacienteIdEl = document.getElementById('crearPacienteId');
            const crearDoctorIdEl = document.getElementById('crearDoctorId');
            const crearFechaEl = document.getElementById('crearFecha');
            const crearHoraInicioEl = document.getElementById('crearHoraInicio');
            const crearHoraFinEl = document.getElementById('crearHoraFin');
            const crearMotivoEl = document.getElementById('crearMotivo');

            const modalDetalleCitaEl = document.getElementById('modalDetalleCita');
            const detalleCitaIdEl = document.getElementById('detalleCitaId');
            const detalleEstadoBadgeEl = document.getElementById('detalleEstadoBadge');
            const detalleDoctorNombreEl = document.getElementById('detalleDoctorNombre');
            const detalleDoctorEspecialidadEl = document.getElementById('detalleDoctorEspecialidad');
            const detallePacienteNombreEl = document.getElementById('detallePacienteNombre');
            const detallePacienteTelefonoEl = document.getElementById('detallePacienteTelefono');
            const detalleFechaEl = document.getElementById('detalleFecha');
            const detalleHorarioEl = document.getElementById('detalleHorario');
            const detalleMotivoEl = document.getElementById('detalleMotivo');
            
            const btnAccionConfirmarEl = document.getElementById('btnAccionConfirmar');
            const btnAccionAtenderEl = document.getElementById('btnAccionAtender');
            const btnAccionCancelarEl = document.getElementById('btnAccionCancelar');

            // Cargar Catálogos Iniciales
            cargarDoctores();
            cargarPacientes();

            // -------------------------------------------------------------
            // 1. INICIALIZACIÓN DE FULLCALENDAR v6 (RQF-02, RQF-04, RQF-10)
            // -------------------------------------------------------------
            calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'es',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
                },
                buttonText: {
                    today: 'Hoy',
                    month: 'Mes',
                    week: 'Semana',
                    day: 'Día',
                    list: 'Agenda'
                },
                firstDay: 1, // Lunes
                navLinks: true,
                editable: true,       // Habilita Drag & Drop
                droppable: false,
                selectable: true,
                selectMirror: true,
                dayMaxEvents: true,
                slotMinTime: '07:00:00',
                slotMaxTime: '20:00:00',
                slotDuration: '00:15:00',
                slotLabelInterval: '01:00',

                // Carga dinámica consumiendo el endpoint /api/citas con filtros (RQF-06)
                events: function(info, successCallback, failureCallback) {
                    const params = new URLSearchParams();
                    params.append('desde', info.startStr.substring(0, 10));
                    params.append('hasta', info.endStr.substring(0, 10));

                    if (filtroDoctorEl.value) {
                        params.append('doctor_id', filtroDoctorEl.value);
                    }
                    if (filtroEstadoEl.value) {
                        params.append('estado', filtroEstadoEl.value);
                    }

                    fetch(`/api/citas?${params.toString()}`)
                        .then(res => {
                            if (!res.ok) throw new Error('Error al cargar citas');
                            return res.json();
                        })
                        .then(response => {
                            successCallback(response.data || []);
                        })
                        .catch(err => {
                            console.error(err);
                            failureCallback(err);
                        });
                },

                // Clic en un día u hora del calendario: Abrir Modal de Creación (RQF-01)
                dateClick: function(info) {
                    abrirModalCrearConFecha(info.dateStr);
                },

                // Clic sobre un evento existente: Abrir Modal de Detalle (RQF-09)
                eventClick: function(info) {
                    abrirModalDetalle(info.event);
                },

                // Drag & Drop: Reprogramación con Validación de Conflicto en Servidor (RQF-04, RQNF-07)
                eventDrop: function(info) {
                    handleEventReschedule(info);
                },

                // Redimensión de Horario (Extender duración)
                eventResize: function(info) {
                    handleEventReschedule(info);
                }
            });

            calendar.render();

            // -------------------------------------------------------------
            // 2. MANEJO DE DRAG & DROP Y CONFLICTOS 409 (RQF-04, RQNF-07)
            // -------------------------------------------------------------
            function handleEventReschedule(info) {
                const event = info.event;
                const citaId = event.id;

                const start = event.start;
                // Si end es null, asumimos 45 minutos después del start
                let end = event.end;
                if (!end) {
                    end = new Date(start.getTime() + 45 * 60000);
                }

                const fecha = start.toISOString().substring(0, 10);
                const horaInicio = start.toTimeString().substring(0, 8);
                const horaFin = end.toTimeString().substring(0, 8);

                // Llamada a la API PUT /api/citas/{id}
                fetch(`/api/citas/${citaId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        fecha: fecha,
                        hora_inicio: horaInicio,
                        hora_fin: horaFin
                    })
                })
                .then(async res => {
                    const data = await res.json();
                    if (res.status === 409) {
                        // Conflicto de horario detectado en el servidor (RQF-03, RQNF-07)
                        info.revert(); // Revertir visualmente el evento a su posición original
                        Swal.fire({
                            icon: 'warning',
                            title: 'Conflicto de Horario (HTTP 409)',
                            html: `<p class="text-sm text-slate-600">${data.mensaje || 'El doctor ya tiene una cita activa en ese intervalo.'}</p>`,
                            confirmButtonColor: '#2B6B8A',
                            confirmButtonText: 'Entendido'
                        });
                        return;
                    }

                    if (!res.ok) {
                        info.revert();
                        Swal.fire({
                            icon: 'error',
                            title: 'No se pudo reprogramar',
                            text: data.mensaje || 'Ocurrió un error al procesar el cambio.',
                            confirmButtonColor: '#2B6B8A'
                        });
                        return;
                    }

                    // Éxito: Notificación Toast elegante
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Cita reprogramada exitosamente',
                        showConfirmButton: false,
                        timer: 2500
                    });

                    calendar.refetchEvents();
                })
                .catch(err => {
                    console.error(err);
                    info.revert();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de Red',
                        text: 'No fue posible comunicar con el servidor.',
                        confirmButtonColor: '#2B6B8A'
                    });
                });
            }

            // -------------------------------------------------------------
            // 3. CARGA DE CATÁLOGOS (DOCTORES Y PACIENTES) (RQF-07)
            // -------------------------------------------------------------
            function cargarDoctores() {
                fetch('/api/doctores')
                    .then(res => res.json())
                    .then(response => {
                        const docs = response.data || [];
                        
                        // Llenar select de filtro
                        filtroDoctorEl.innerHTML = '<option value="">Todos los doctores</option>';
                        docs.forEach(d => {
                            const opt = document.createElement('option');
                            opt.value = d.id;
                            opt.textContent = `${d.nombre} (${d.especialidad})`;
                            filtroDoctorEl.appendChild(opt);
                        });

                        // Llenar select del formulario crear
                        crearDoctorIdEl.innerHTML = '<option value="">Selecciona un doctor...</option>';
                        docs.forEach(d => {
                            const opt = document.createElement('option');
                            opt.value = d.id;
                            opt.textContent = `${d.nombre} · ${d.especialidad}`;
                            crearDoctorIdEl.appendChild(opt);
                        });
                    })
                    .catch(err => console.error('Error cargando doctores:', err));
            }

            function cargarPacientes(pacienteSeleccionarId = null) {
                fetch('/api/pacientes')
                    .then(res => res.json())
                    .then(response => {
                        const pacs = response.data || [];
                        crearPacienteIdEl.innerHTML = '<option value="">Selecciona un paciente...</option>';
                        pacs.forEach(p => {
                            const opt = document.createElement('option');
                            opt.value = p.id;
                            opt.textContent = `${p.nombre} (Tel: ${p.telefono || 'S/N'})`;
                            if (pacienteSeleccionarId && p.id === pacienteSeleccionarId) {
                                opt.selected = true;
                            }
                            crearPacienteIdEl.appendChild(opt);
                        });
                        if (pacienteSeleccionarId) {
                            crearPacienteIdEl.value = pacienteSeleccionarId;
                        }
                    })
                    .catch(err => console.error('Error cargando pacientes:', err));
            }


            // -------------------------------------------------------------
            // 4. CREACIÓN DE CITA MÉDICA (RQF-01, RQF-08)
            // -------------------------------------------------------------
            function abrirModalCrearConFecha(dateStr) {
                formCrearCitaEl.reset();
                bannerErrorCrearEl.classList.add('hidden');
                bannerErrorCrearEl.innerHTML = '';

                // Si viene fecha completa ISO (con hora)
                if (dateStr.includes('T')) {
                    const partes = dateStr.split('T');
                    crearFechaEl.value = partes[0];
                    crearHoraInicioEl.value = partes[1].substring(0, 5);
                    
                    // Hora fin por defecto: 45 minutos después
                    const [h, m] = partes[1].substring(0, 5).split(':').map(Number);
                    const d = new Date();
                    d.setHours(h, m + 45);
                    crearHoraFinEl.value = d.toTimeString().substring(0, 5);
                } else {
                    crearFechaEl.value = dateStr;
                    crearHoraInicioEl.value = '09:00';
                    crearHoraFinEl.value = '09:45';
                }

                modalCrearCitaEl.classList.remove('hidden');
            }

            btnAbrirModalCrearEl.addEventListener('click', () => {
                abrirModalCrearConFecha(new Date().toISOString().substring(0, 10));
            });

            formCrearCitaEl.addEventListener('submit', (e) => {
                e.preventDefault();
                bannerErrorCrearEl.classList.add('hidden');
                bannerErrorCrearEl.innerHTML = '';

                const payload = {
                    paciente_id: parseInt(crearPacienteIdEl.value),
                    doctor_id: parseInt(crearDoctorIdEl.value),
                    fecha: crearFechaEl.value,
                    hora_inicio: crearHoraInicioEl.value + ':00',
                    hora_fin: crearHoraFinEl.value + ':00',
                    motivo: crearMotivoEl.value,
                    estado: 'pendiente'
                };

                const btnGuardar = document.getElementById('btnGuardarCita');
                btnGuardar.disabled = true;
                btnGuardar.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Guardando...';

                fetch('/api/citas', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                })
                .then(async res => {
                    const data = await res.json();
                    
                    if (res.status === 409) {
                        // Conflicto de horario
                        bannerErrorCrearEl.classList.remove('hidden');
                        bannerErrorCrearEl.innerHTML = `<strong><i class="fa-solid fa-triangle-exclamation"></i> Conflicto de horario (409):</strong><br>${data.mensaje}`;
                        return;
                    }

                    if (res.status === 400) {
                        // Error de validación de entrada
                        bannerErrorCrearEl.classList.remove('hidden');
                        let msgs = '<strong><i class="fa-solid fa-circle-exclamation"></i> Error de validación:</strong><ul class="list-disc pl-4 mt-1">';
                        if (data.errores) {
                            Object.values(data.errores).forEach(errArray => {
                                errArray.forEach(msg => { msgs += `<li>${msg}</li>`; });
                            });
                        } else {
                            msgs += `<li>${data.mensaje}</li>`;
                        }
                        msgs += '</ul>';
                        bannerErrorCrearEl.innerHTML = msgs;
                        return;
                    }

                    if (!res.ok) {
                        bannerErrorCrearEl.classList.remove('hidden');
                        bannerErrorCrearEl.innerHTML = `Error del servidor: ${data.mensaje || 'No se pudo guardar la cita'}`;
                        return;
                    }

                    // Éxito
                    modalCrearCitaEl.classList.add('hidden');
                    Swal.fire({
                        icon: 'success',
                        title: '¡Cita Médica Creada!',
                        text: 'La cita ha sido registrada exitosamente en la base de datos.',
                        confirmButtonColor: '#2B6B8A'
                    });

                    calendar.refetchEvents();
                })
                .catch(err => {
                    console.error(err);
                    bannerErrorCrearEl.classList.remove('hidden');
                    bannerErrorCrearEl.innerHTML = 'Error de conexión con el servidor.';
                })
                .finally(() => {
                    btnGuardar.disabled = false;
                    btnGuardar.innerHTML = '<i class="fa-solid fa-check"></i> <span>Agendar Cita</span>';
                });
            });

            // -------------------------------------------------------------
            // 5. DETALLE Y GESTIÓN DE ESTADOS (RQF-05, RQF-09, RQF-10)
            // -------------------------------------------------------------
            function abrirModalDetalle(event) {
                const props = event.extendedProps || {};
                citaSeleccionada = {
                    id: event.id,
                    estado: props.estado || 'pendiente'
                };

                detalleCitaIdEl.textContent = event.id;
                detalleDoctorNombreEl.textContent = props.doctor_nombre || 'No disponible';
                detalleDoctorEspecialidadEl.textContent = props.doctor_especialidad || '';
                detallePacienteNombreEl.textContent = props.paciente_nombre || 'No disponible';
                detallePacienteTelefonoEl.textContent = props.paciente_telefono || 'No registrado';
                detalleFechaEl.textContent = event.start.toLocaleDateString('es-ES', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
                
                const horaIni = event.start.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
                const horaFin = event.end ? event.end.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' }) : '';
                detalleHorarioEl.textContent = `${horaIni} - ${horaFin}`;
                detalleMotivoEl.textContent = props.motivo || 'Sin motivo especificado';

                // Configurar Badge de Estado con los colores acordados
                actualizarBadgeEstado(props.estado);

                // Configurar visibilidad de botones según la máquina de estados
                ajustarBotonesEstado(props.estado);

                modalDetalleCitaEl.classList.remove('hidden');
            }

            function actualizarBadgeEstado(estado) {
                detalleEstadoBadgeEl.className = 'px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider border';
                
                switch(estado) {
                    case 'pendiente':
                        detalleEstadoBadgeEl.className += ' bg-estado-pendienteBg border-estado-pendienteBorder text-estado-pendienteText';
                        detalleEstadoBadgeEl.innerHTML = '<i class="fa-solid fa-clock mr-1"></i> Pendiente';
                        break;
                    case 'confirmada':
                        detalleEstadoBadgeEl.className += ' bg-estado-confirmadaBg border-estado-confirmadaBorder text-estado-confirmadaText';
                        detalleEstadoBadgeEl.innerHTML = '<i class="fa-solid fa-circle-check mr-1"></i> Confirmada';
                        break;
                    case 'atendida':
                        detalleEstadoBadgeEl.className += ' bg-estado-atendidaBg border-estado-atendidaBorder text-estado-atendidaText';
                        detalleEstadoBadgeEl.innerHTML = '<i class="fa-solid fa-user-check mr-1"></i> Atendida';
                        break;
                    case 'cancelada':
                        detalleEstadoBadgeEl.className += ' bg-estado-canceladaBg border-estado-canceladaBorder text-estado-canceladaText';
                        detalleEstadoBadgeEl.innerHTML = '<i class="fa-solid fa-ban mr-1"></i> Cancelada';
                        break;
                }
            }

            function ajustarBotonesEstado(estado) {
                // Si la cita ya está atendida o cancelada (estados terminales), se deshabilitan las transiciones
                if (estado === 'atendida' || estado === 'cancelada') {
                    btnAccionConfirmarEl.classList.add('hidden');
                    btnAccionAtenderEl.classList.add('hidden');
                    btnAccionCancelarEl.classList.add('hidden');
                } else if (estado === 'pendiente') {
                    btnAccionConfirmarEl.classList.remove('hidden');
                    btnAccionAtenderEl.classList.add('hidden');
                    btnAccionCancelarEl.classList.remove('hidden');
                } else if (estado === 'confirmada') {
                    btnAccionConfirmarEl.classList.add('hidden');
                    btnAccionAtenderEl.classList.remove('hidden');
                    btnAccionCancelarEl.classList.remove('hidden');
                }
            }

            function cambiarEstadoCita(nuevoEstado) {
                if (!citaSeleccionada) return;

                const citaId = citaSeleccionada.id;

                // Confirmación visual especial si es cancelación (RQF-05)
                if (nuevoEstado === 'cancelada') {
                    Swal.fire({
                        title: '¿Confirmas la cancelación?',
                        text: 'La cita cambiará su estado a Cancelada pero el registro histórico se conservará en la base de datos (RQF-05).',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#EF4444',
                        cancelButtonColor: '#64748B',
                        confirmButtonText: 'Sí, cancelar cita',
                        cancelButtonText: 'Volver'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            ejecutarPatchEstado(citaId, nuevoEstado);
                        }
                    });
                } else {
                    ejecutarPatchEstado(citaId, nuevoEstado);
                }
            }

            function ejecutarPatchEstado(citaId, nuevoEstado) {
                fetch(`/api/citas/${citaId}/estado`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ estado: nuevoEstado })
                })
                .then(async res => {
                    const data = await res.json();
                    if (!res.ok) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error al cambiar estado',
                            text: data.mensaje || 'No se pudo actualizar el estado de la cita.',
                            confirmButtonColor: '#2B6B8A'
                        });
                        return;
                    }

                    modalDetalleCitaEl.classList.add('hidden');
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: `Estado actualizado a '${nuevoEstado}'`,
                        showConfirmButton: false,
                        timer: 2000
                    });

                    calendar.refetchEvents();
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de Conexión',
                        text: 'No se pudo comunicar con la API.',
                        confirmButtonColor: '#2B6B8A'
                    });
                });
            }

            btnAccionConfirmarEl.addEventListener('click', () => cambiarEstadoCita('confirmada'));
            btnAccionAtenderEl.addEventListener('click', () => cambiarEstadoCita('atendida'));
            btnAccionCancelarEl.addEventListener('click', () => cambiarEstadoCita('cancelada'));

            // -------------------------------------------------------------
            // 6. EVENTOS DE CIERRE DE MODALES Y FILTROS
            // -------------------------------------------------------------
            document.querySelectorAll('.btnCerrarModal').forEach(btn => {
                btn.addEventListener('click', () => modalCrearCitaEl.classList.add('hidden'));
            });

            document.querySelectorAll('.btnCerrarModalDetalle').forEach(btn => {
                btn.addEventListener('click', () => modalDetalleCitaEl.classList.add('hidden'));
            });

            filtroDoctorEl.addEventListener('change', () => calendar.refetchEvents());
            filtroEstadoEl.addEventListener('change', () => calendar.refetchEvents());
            btnRefrescarEl.addEventListener('click', () => calendar.refetchEvents());

            // -------------------------------------------------------------
            // 7. REGISTRO DE NUEVOS PACIENTES (RQF-09, RQNF-03)
            // -------------------------------------------------------------
            const modalNuevoPacienteEl = document.getElementById('modalNuevoPaciente');
            const formCrearPacienteEl = document.getElementById('formCrearPaciente');
            const bannerErrorPacienteEl = document.getElementById('bannerErrorPaciente');
            const btnAbrirModalPacienteEl = document.getElementById('btnAbrirModalPaciente');
            const btnCrearPacienteRapidoEl = document.getElementById('btnCrearPacienteRapido');
            const pacienteNombreEl = document.getElementById('pacienteNombre');
            const pacienteTelefonoEl = document.getElementById('pacienteTelefono');
            const pacienteEmailEl = document.getElementById('pacienteEmail');
            const pacienteFechaNacimientoEl = document.getElementById('pacienteFechaNacimiento');
            const btnGuardarPacienteEl = document.getElementById('btnGuardarPaciente');

            function abrirModalNuevoPaciente() {
                formCrearPacienteEl.reset();
                bannerErrorPacienteEl.classList.add('hidden');
                bannerErrorPacienteEl.innerHTML = '';
                modalNuevoPacienteEl.classList.remove('hidden');
                setTimeout(() => pacienteNombreEl.focus(), 100);
            }

            if (btnAbrirModalPacienteEl) {
                btnAbrirModalPacienteEl.addEventListener('click', abrirModalNuevoPaciente);
            }

            if (btnCrearPacienteRapidoEl) {
                btnCrearPacienteRapidoEl.addEventListener('click', abrirModalNuevoPaciente);
            }

            document.querySelectorAll('.btnCerrarModalPaciente').forEach(btn => {
                btn.addEventListener('click', () => {
                    modalNuevoPacienteEl.classList.add('hidden');
                });
            });

            formCrearPacienteEl.addEventListener('submit', (e) => {
                e.preventDefault();
                bannerErrorPacienteEl.classList.add('hidden');
                bannerErrorPacienteEl.innerHTML = '';

                const payload = {
                    nombre: pacienteNombreEl.value.trim(),
                    telefono: pacienteTelefonoEl.value.trim() || null,
                    email: pacienteEmailEl.value.trim() || null,
                    fecha_nacimiento: pacienteFechaNacimientoEl.value || null
                };

                btnGuardarPacienteEl.disabled = true;
                btnGuardarPacienteEl.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Guardando...';

                fetch('/api/pacientes', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                })
                .then(async res => {
                    const data = await res.json();

                    if (res.status === 400) {
                        bannerErrorPacienteEl.classList.remove('hidden');
                        let errHtml = '<strong><i class="fa-solid fa-circle-exclamation"></i> Error en los datos ingresados:</strong><ul class="list-disc list-inside mt-1">';
                        if (data.errores) {
                            Object.values(data.errores).flat().forEach(err => {
                                errHtml += `<li>${err}</li>`;
                            });
                        } else {
                            errHtml += `<li>${data.mensaje || 'Datos inválidos'}</li>`;
                        }
                        errHtml += '</ul>';
                        bannerErrorPacienteEl.innerHTML = errHtml;
                        return;
                    }

                    if (!res.ok) {
                        throw new Error(data.mensaje || 'Error en el servidor');
                    }

                    // Éxito: Status 201 Created
                    modalNuevoPacienteEl.classList.add('hidden');
                    const nuevoPaciente = data.data;

                    // Actualizar el dropdown de pacientes y pre-seleccionar el nuevo paciente
                    cargarPacientes(nuevoPaciente.id);

                    Swal.fire({
                        icon: 'success',
                        title: '¡Paciente Registrado!',
                        text: `Se ha registrado a ${nuevoPaciente.nombre} exitosamente.`,
                        confirmButtonColor: '#2B6B8A',
                        timer: 2500
                    });

                    formCrearPacienteEl.reset();
                })
                .catch(err => {
                    console.error('Error al registrar paciente:', err);
                    bannerErrorPacienteEl.classList.remove('hidden');
                    bannerErrorPacienteEl.innerHTML = `<strong>Error inesperado:</strong> ${err.message}`;
                })
                .finally(() => {
                    btnGuardarPacienteEl.disabled = false;
                    btnGuardarPacienteEl.innerHTML = '<i class="fa-solid fa-save"></i> <span>Guardar Paciente</span>';
                });
            });


        });
    </script>

</body>
</html>
