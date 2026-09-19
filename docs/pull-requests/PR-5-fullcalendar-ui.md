# Pull Request #5: Interfaz Web con FullCalendar v6, Drag & Drop, Filtros y Paleta Médica

- **Rama origen:** `feature/fullcalendar-ui`
- **Rama destino:** `main`
- **Estado:** Listo para revisión y aprobación
- **Criterios / Backlog cubiertos:**
  - `[RQF-02]`: Mostrar citas en un calendario interactivo (FullCalendar v6) con vistas de mes y semana.
  - `[RQF-04]`: Reprogramar citas arrastrándolas en el calendario (drag & drop) sincronizando con la base de datos vía API y revirtiendo ante colisión.
  - `[RQF-06]`: Filtrar citas por doctor y por estado de forma reactiva.
  - `[RQF-09]`: Mostrar el detalle de una cita al hacer clic sobre el evento en el calendario.
  - `[RQF-10]`: Representar visualmente el estado de cada cita mediante color (pendiente, confirmada, cancelada, atendida).
  - `[RQNF-06]`: Interfaz usable y responsiva en escritorio y tablet.

---

## 🎨 Sistema de Diseño y Paleta Clínica

Inspirado en la web de referencia clínica (**cirugiadigestivamini.com**) y el estándar hospitalario:

1. **Tipografías**:
   - **Títulos y Encabezados (`font-heading`)**: **`Raleway`** (Google Fonts).
   - **Cuerpo, Horarios y Tablas (`font-body`)**: **`Open Sans`** (Google Fonts).

2. **Paleta de Colores**:
   - **Azul Hospitalario Profundo**: `#2B6B8A` (Header, botones de acción primaria).
   - **Azul Pastel Clínico**: `#4A89A7` (Acentos secundarios, botones de toolbar).
   - **Azul Hielo Suave**: `#EBF4F8` / `#F0F7FA` (Fondo del día de hoy y contrastes).
   - **Verde Menta Cirugía Digestiva**: `#0DB26B` (Botón "Nueva Cita", estado Atendida).
   - **Fondo General**: `#F8FAFC` (Slate 50 clínico y limpio).

3. **Colores Semánticos de Citas (RQF-10)**:
   - 🟡 **`pendiente`**: Fondo `#FEF3C7` · Borde `#F59E0B` · Texto `#92400E`
   - 🔵 **`confirmada`**: Fondo `#E0F2FE` · Borde `#2B6B8A` · Texto `#0369A1`
   - 🟢 **`atendida`**: Fondo `#E6F8F0` · Borde `#0DB26B` · Texto `#065F46`
   - 🔴 **`cancelada`**: Fondo `#FEE2E2` · Borde `#EF4444` · Texto `#991B1B` (Rojo pastel según especificación del usuario).

---

## 🚀 Capacidades Funcionales Implementadas

1. **Vistas del Calendario (RQF-02)**:
   - Mes (`dayGridMonth`), Semana (`timeGridWeek`), Día (`timeGridDay`), y Agenda (`listWeek`), completamente localizadas al español (`es`).
2. **Creación Dinámica (RQF-01, RQF-08)**:
   - Al hacer clic en cualquier día u hora del calendario (`dateClick`), se abre el modal `#modalCrearCita` con la fecha y hora preseleccionadas.
   - Si el servidor devuelve `400` o `409` (conflicto), se muestra una alerta visual sin perder los datos del formulario.
3. **Detalle Completo de Cita (RQF-09)**:
   - Al hacer clic en un evento (`eventClick`), se abre el modal `#modalDetalleCita` mostrando doctor, especialidad, paciente, teléfono, fecha, rango horario y motivo.
4. **Gestión de Estados en Vivo (RQF-05)**:
   - Botones rápidos en el modal de detalle para:
     * *Confirmar Cita* (`confirmada`)
     * *Marcar Atendida* (`atendida`)
     * *Cancelar Cita* (`cancelada`, solicitando confirmación y recordando que el registro se conserva en la base de datos).
5. **Reprogramación Drag & Drop con Rollback (RQF-04, RQNF-07)**:
   - Al arrastrar un evento a un nuevo día u hora (`eventDrop`), se envía una petición `PUT /api/citas/{id}`.
   - Si la API detecta colisión de horario con otra cita del doctor, responde `HTTP 409 Conflict`, la interfaz lanza una alerta interactiva y ejecuta automáticamente `info.revert()`, restaurando el evento a su horario original.
6. **Filtros Reactivos (RQF-06)**:
   - Filtro desplegable por Doctor y por Estado que actualizan el calendario al instante sin recargar la página.

---

## 🧪 Pruebas Automatizadas de Interfaz y Recursos

Ejecución de `Tests\Feature\CalendarUiTest`:

```
   PASS  Tests\Feature\CalendarUiTest
  ✓ ruta raiz carga vista calendario con recursos                                0.17s  
  ✓ citas api retorna colores de estado especificados incluyendo rojo cancelada  0.27s  

  Tests:    2 passed (12 assertions)
  Duration: 0.60s
```

---

## ✅ Checklist de Verificación
- [x] FullCalendar v6 integrado con vistas de mes, semana, día y lista.
- [x] Tipografía `Raleway` y `Open Sans` aplicada en toda la interfaz.
- [x] Paleta azul pastel hospitalario y verde cirugía digestiva con rojo pastel para canceladas.
- [x] Creación de cita interactiva al hacer clic en el calendario.
- [x] Modal de detalle de cita con botones de transición de estado.
- [x] Reprogramación Drag & Drop con rollback en caso de conflicto 409.
- [x] Filtros por doctor y por estado funcionales.
- [x] Diseño responsivo probado en resoluciones de escritorio y tablet.
