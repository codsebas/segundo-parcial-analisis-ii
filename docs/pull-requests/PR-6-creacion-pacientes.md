# Pull Request #6: Funcionalidad de Registro de Pacientes (API y UI)

- **Rama origen:** `feature/creacion-pacientes`
- **Rama destino:** `main`
- **Estado:** Listo para revisión y aprobación
- **Criterios / Backlog cubiertos:**
  - `[RQF-09]`: Registro de nuevos pacientes en el sistema clínico vía API REST y desde la interfaz gráfica.
  - `[RQNF-03]`: Respuestas y códigos de estado HTTP estandarizados (`201 Created`, `400 Bad Request`).
  - `[RQNF-04]`: Arquitectura modular estricta en 4 capas (FormRequests/Validación, Controladores/Recursos, Servicios de Dominio, Repositorios/Eloquent).
  - `[RQNF-06]`: Interfaz usable, intuitiva y accesible desde la vista principal del calendario.

---

## 🏗️ Arquitectura de 4 Capas Implementada

Siguiendo el estándar de arquitectura limpia del proyecto, la creación de pacientes se distribuye en:

1. **Capa de Dominio y Persistencia (Eloquent + Repositorio)**:
   - `app/Repositories/Contracts/PacienteRepositoryInterface.php`: Definición del contrato `create(array $data): Paciente`.
   - `app/Repositories/Eloquent/PacienteRepository.php`: Implementación concreta delegando en el modelo Eloquent `Paciente`.
2. **Capa de Negocio (Service)**:
   - `app/Services/PacienteService.php`: Método `crearPaciente(array $data): Paciente` como punto de entrada de la lógica de negocio.
3. **Capa de Validación y Contención (Form Request)**:
   - `app/Http/Requests/StorePacienteRequest.php`: Extiende `BaseApiRequest` para capturar cualquier violación de formato y responder automáticamente con `HTTP 400 Bad Request` en formato JSON estructurado:
     * `nombre`: Requerido, tipo string, longitud máxima de 120 caracteres.
     * `telefono`: Opcional, tipo string, longitud máxima de 25 caracteres.
     * `email`: Opcional, formato de correo válido, máximo 120 caracteres, unicidad garantizada en `pacientes`.
     * `fecha_nacimiento`: Opcional, fecha válida, anterior o igual al día actual (`before_or_equal:today`).
4. **Capa de Control y Transformación (Controller + Resource)**:
   - `app/Http/Controllers/Api/PacienteController.php`: Método `store(StorePacienteRequest $request)` que retorna `HTTP 201 Created` acompañado del recurso formateado `PacienteResource`.

---

## 🎨 Integración en la Interfaz Web (FullCalendar / Blade)

Para permitir un flujo de trabajo ágil al personal médico y de recepción:

1. **Acceso Dual al Registro**:
   - **Botón en Header Superior**: Botón `Nuevo Paciente` con diseño hospitalario junto al botón de agendamiento.
   - **Enlace Rápido en Modal de Cita**: Enlace interactivo `+ Registrar Paciente` ubicado directamente sobre el selector de pacientes en el formulario de creación de cita.
2. **Modal Responsivo `#modalNuevoPaciente`**:
   - Estilizado con la paleta de la clínica (`#2B6B8A`, `#0DB26B`, tipografías Raleway y Open Sans).
   - Incluye campos validados en cliente y banners para desplegar errores `HTTP 400` del servidor.
3. **Auto-Selección Reactiva**:
   - Al registrar exitosamente un paciente (`201 Created`), la interfaz recarga dinámicamente la lista de pacientes mediante `fetch('/api/pacientes')` y **pre-selecciona de forma automática** al paciente recién creado en el formulario de citas, sin recargar la página.

---

## 🧪 Suite de Pruebas Automatizadas

Se incorporó la suite `tests/Feature/PacientesApiTest.php` con 6 pruebas automatizadas:

| Test | Requerimiento | Descripción | Resultado |
|---|---|---|:---:|
| `test_crear_paciente_exitoso_retorna_http_201_y_datos` | `RQF-09`, `RQNF-03` | Registro con datos completos devuelve 201 y JSON | ✅ PASS |
| `test_crear_paciente_solo_con_nombre_requerido_exitoso` | `RQF-09`, `RQNF-03` | Registro solo con campo obligatorio nombre devuelve 201 | ✅ PASS |
| `test_contencion_nombre_requerido_retorna_http_400` | `RQNF-03` | Omisión de nombre devuelve 400 y error estructurado | ✅ PASS |
| `test_contencion_email_invalido_retorna_http_400` | `RQNF-03` | Email mal formado devuelve 400 | ✅ PASS |
| `test_contencion_email_duplicado_retorna_http_400` | `RQNF-03` | Email duplicado devuelve 400 | ✅ PASS |
| `test_contencion_fecha_nacimiento_futura_retorna_http_400` | `RQNF-03` | Fecha de nacimiento futura devuelve 400 | ✅ PASS |

**Resumen general del proyecto:** `34 passed (121 assertions)`.

---

## 📡 Evidencia de Consumo API (cURL)

### 1. Registro Exitoso (HTTP 201 Created)
```bash
curl -X POST http://127.0.0.1:8000/api/pacientes \
  -H "Content-Type: application/json" \
  -d '{"nombre": "Mariana Morales Gómez", "telefono": "+502 4567-8901", "email": "mariana.morales@gmail.com", "fecha_nacimiento": "1994-06-15"}'
```
**Respuesta:**
```json
{
  "status": "success",
  "codigo": 201,
  "mensaje": "Paciente registrado exitosamente.",
  "data": {
    "id": 10,
    "nombre": "Mariana Morales Gómez",
    "telefono": "+502 4567-8901",
    "email": "mariana.morales@gmail.com",
    "fecha_nacimiento": "1994-06-15"
  }
}
```

### 2. Rechazo por Validación de Entrada (HTTP 400 Bad Request)
```bash
curl -X POST http://127.0.0.1:8000/api/pacientes \
  -H "Content-Type: application/json" \
  -d '{"telefono": "12345"}'
```
**Respuesta:**
```json
{
  "status": "error",
  "codigo": 400,
  "mensaje": "Datos de entrada inválidos o incompletos.",
  "errores": {
    "nombre": [
      "El nombre completo del paciente es obligatorio."
    ]
  }
}
```
