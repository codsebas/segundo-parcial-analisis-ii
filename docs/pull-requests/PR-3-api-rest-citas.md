# Pull Request #3: API REST por Capas (Laravel 12), Validación de Contención y Suite Automatizada

- **Rama origen:** `feature/api-rest-citas`
- **Rama destino:** `main`
- **Estado:** Listo para revisión y aprobación
- **Criterios / Backlog cubiertos:**
  - `[RQF-01]`: Creación de citas médicas indicando paciente, doctor, fecha, horarios y motivo.
  - `[RQF-03]`: Validación y bloqueo de doble reserva en servidor (HTTP 409 Conflict).
  - `[RQF-05]`: Cancelación de citas preservando el registro histórico en base de datos.
  - `[RQF-06]`: Filtrado y listado de citas por doctor y rango de fechas.
  - `[RQF-07]`: Endpoints REST con operaciones CRUD sobre citas y lectura de doctores y pacientes.
  - `[RQF-08]`: Validación exhaustiva de datos de entrada y pruebas de contención (HTTP 400).
  - `[RQNF-03]`: Respuestas en formato JSON y códigos HTTP estrictos (200, 201, 400, 404, 409).
  - `[RQNF-04]`: Arquitectura limpia por capas (Controladores, Servicios, Repositorios, Modelos).
  - `[RQNF-07]`: Validación de conflicto de horario ejecutada en el servidor.
  - `[RQNF-08]`: Evidencia y trazabilidad técnica formal.

---

## 🏛️ Arquitectura Senior por Capas Implementada

El sistema se organizó bajo el patrón arquitectónico por capas desacoplado:

```
┌─────────────────────────────────────────────────────────────┐
│               CAPA DE RUTAS Y HTTP REQUESTS                 │
│  - routes/api.php                                           │
│  - StoreCitaRequest, UpdateCitaHorarioRequest               │
│  - UpdateCitaEstadoRequest, FilterCitasRequest              │
│  (Contención de entrada y retorno HTTP 400 garantizado)    │
└──────────────────────────────┬──────────────────────────────┘
                               │
┌──────────────────────────────▼──────────────────────────────┐
│                    CAPA DE CONTROLADORES                    │
│  - CitaController, DoctorController, PacienteController     │
│  (Transformación con API Resources y códigos HTTP correctos)│
└──────────────────────────────┬──────────────────────────────┘
                               │
┌──────────────────────────────▼──────────────────────────────┐
│                CAPA DE LÓGICA DE NEGOCIO                    │
│  - CitaService, DoctorService, PacienteService              │
│  - HorarioConflictException (409 Conflict)                  │
│  (Reglas de negocio, algoritmos de solapamiento y estados)  │
└──────────────────────────────┬──────────────────────────────┘
                               │
┌──────────────────────────────▼──────────────────────────────┐
│                  CAPA DE ACCESO A DATOS                     │
│  - DoctorRepositoryInterface / DoctorRepository             │
│  - PacienteRepositoryInterface / PacienteRepository         │
│  - CitaRepositoryInterface / CitaRepository                 │
│  - RepositoryServiceProvider (Inyección de Dependencias)    │
└──────────────────────────────┬──────────────────────────────┘
                               │
┌──────────────────────────────▼──────────────────────────────┐
│                   CAPA DE MODELOS / DDL                     │
│  - Models: Cita, Doctor, Paciente (Eloquent)                │
│  - Migrations: 2026_09_19_000001_create_clinica_tables.php  │
│  - Database: MySQL 8.0 en Docker (clinica_db)               │
└─────────────────────────────────────────────────────────────┘
```

---

## 📡 Matriz de Endpoints REST Implementados

| Método | Endpoint | Propósito | Códigos HTTP |
|---|---|---|---|
| `GET` | `/api/doctores` | Listar catálogo de doctores con especialidad | 200 |
| `GET` | `/api/doctores/{id}` | Detalle de un doctor específico | 200, 404 |
| `GET` | `/api/pacientes` | Listar catálogo de pacientes | 200 |
| `GET` | `/api/pacientes/{id}` | Detalle de un paciente específico | 200, 404 |
| `GET` | `/api/citas` | Listar citas con filtros (`doctor_id`, `desde`, `hasta`) | 200 |
| `POST` | `/api/citas` | Crear cita con validación de horario | 201, 400, 409 |
| `GET` | `/api/citas/{id}` | Detalle completo de una cita médica | 200, 404 |
| `PUT` | `/api/citas/{id}` | Reprogramar fecha/horario (Drag & Drop) | 200, 400, 404, 409 |
| `PATCH` | `/api/citas/{id}/estado` | Cambiar estado (confirmar, atender, cancelar) | 200, 400, 404 |

---

## 🛡️ Pruebas de Contención (Defensive Programming)

Se realizaron pruebas específicas para asegurar que la API no falle ante datos maliciosos o mal estructurados:
1. **Campos incompletos o vacíos**: Responde `400 Bad Request` con arreglo detallado de errores.
2. **Entidades inexistentes**: Enviar `doctor_id: 99999` o `paciente_id: 99999` responde `400 Bad Request`.
3. **Inversión horaria**: Enviar `hora_inicio: 11:00` y `hora_fin: 10:00` es contenido con `400 Bad Request`.
4. **Fechas o formatos inválidos**: Contenido con `400 Bad Request`.
5. **Recurso no encontrado**: Buscar cita inexistente responde `404 Not Found`.

---

## 🧪 Evidencia de Ejecución de la Suite Automatizada

```
======================================================================
 SUITE AUTOMATIZADA DE PRUEBAS DE API REST Y CONTENCIÓN (LARAVEL 12)  
======================================================================

   PASS  Tests\Feature\CitasApiTest
  ✓ puede listar doctores y pacientes con http 200                               0.18s  
  ✓ crear cita exitosa retorna http 201 y estructura json                        0.05s  
  ✓ contencion campos obligatorios retorna http 400                              0.05s  
  ✓ contencion llaves foraneas inexistentes retorna http 400                     0.05s  
  ✓ contencion rango horario invertido retorna http 400                          0.08s  
  ✓ rechaza doble reserva con http 409 conflict                                  0.16s  
  ✓ permite mismo horario para doctor diferente                                  0.05s  
  ✓ permite horario si la cita previa fue cancelada                              0.06s  
  ✓ reprogramar cita actualiza exitosamente o detecta conflicto                  0.05s  
  ✓ cancelar cita conserva el registro historico en bd                           0.07s  
  ✓ consulta cita inexistente retorna http 404                                   0.03s  

  Tests:    11 passed (52 assertions)
  Duration: 1.00s

======================================================================
>> RESULTADO: TODAS LAS PRUEBAS DE API Y CONTENCIÓN PASARON SATISFACTORIAMENTE (100%) <<
```

---

## ✅ Checklist de Verificación
- [x] Framework Laravel 12 configurado y conectado al contenedor MySQL de Docker.
- [x] Arquitectura en 4 capas respetada (Modelos, Repositorios, Servicios, Controladores).
- [x] Endpoints CRUD completos y funcionales según la tabla de referencia del enunciado.
- [x] Validación de doble reserva en servidor retornando formalmente HTTP 409.
- [x] Pruebas de contención (boundary testing) retornando HTTP 400 y 404.
- [x] Suite automatizada con 11 de 11 pruebas superadas (52 aserciones).
- [x] Commits descriptivos con trazabilidad a los IDs del backlog.
