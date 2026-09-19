# Pull Request #1: Docker Compose, persistencia y esquema MySQL con datos semilla

- **Rama origen:** `feature/docker-mysql-schema`
- **Rama destino:** `main`
- **Estado:** Listo para revisión y aprobación
- **Criterios / Backlog cubiertos:**
  - `[RQNF-01]`: La base de datos MySQL debe ejecutarse en un contenedor Docker con persistencia mediante volumen.
  - `[RQNF-02]`: El entorno de base de datos debe poder levantarse con un solo comando (`docker compose up`) de forma reproducible.

---

## 📝 Descripción del Cambio

Este Pull Request establece la infraestructura de base de datos completa del sistema requerida para el Segundo Parcial:

1. **Orquestación con Docker Compose (`docker-compose.yml`)**:
   - Define el servicio `mysql` utilizando la imagen oficial `mysql:8.0`.
   - Asigna el nombre de contenedor `clinica_mysql`.
   - Configura el mapeo de puertos dinámico `${DB_PORT:-3306}:3306`.
   - Implementa persistencia estricta mediante el volumen nombrado `clinica_mysql_data` montado en `/var/lib/mysql`.
   - Añade sondeo de salud (*healthcheck*) mediante `mysqladmin ping`.

2. **Esquema Relacional DDL (`init.sql`)**:
   - Base de datos: `clinica_db` (UTF8MB4).
   - Tabla `pacientes`: identificador, nombre completo, teléfono, correo electrónico, fecha de nacimiento y auditoría temporal.
   - Tabla `doctores`: identificador, nombre completo, especialidad médica, teléfono, correo electrónico y auditoría temporal.
   - Tabla `citas`: identificador, llaves foráneas a pacientes y doctores con integridad referencial (`ON DELETE RESTRICT ON UPDATE CASCADE`), fecha, hora de inicio, hora de fin, motivo textual, y estado enumerado (`'pendiente'`, `'confirmada'`, `'cancelada'`, `'atendida'`).
   - Índices de alto rendimiento en `(doctor_id, fecha)` para optimizar las consultas de solapamiento y validación de disponibilidad.

3. **Datos Semilla DML (`init.sql`)**:
   - 5 doctores con especialidades médicas variadas (Medicina General, Cardiología, Pediatría, Dermatología, Odontología).
   - 6 pacientes con datos de contacto realistas.
   - 8 citas iniciales distribuidas entre los estados `pendiente`, `confirmada`, `atendida` y `cancelada`, utilizando fechas dinámicas (`CURDATE()`, `DATE_ADD()`, `DATE_SUB()`) para visualización inmediata en el calendario.

4. **Variables de Entorno (`.env.example`)**:
   - Parámetros de conexión desacoplados y listos para ejecución local y en contenedores.

---

## 🔍 Evidencias de Ejecución

### 1. Levantamiento del Contenedor con un Solo Comando
```powershell
PS> docker compose up -d
[+] Running 3/3
 ✔ Network segundoparcial_default  Created
 ✔ Volume clinica_mysql_data       Created
 ✔ Container clinica_mysql         Started
```

### 2. Estado del Contenedor (`docker ps`)
```powershell
PS> docker ps
CONTAINER ID   IMAGE       COMMAND                  STATUS                    PORTS                                         NAMES
e9ad605be284   mysql:8.0   "docker-entrypoint.s…"   Up 43 seconds (healthy)   0.0.0.0:3306->3306/tcp, [::]:3306->3306/tcp   clinica_mysql
```

### 3. Verificación de Tablas y Datos Semilla
```powershell
PS> docker exec clinica_mysql mysql -uclinica_user -pclinica_pass123 -e "USE clinica_db; SHOW TABLES; SELECT count(*) AS doctores FROM doctores; SELECT count(*) AS pacientes FROM pacientes; SELECT count(*) AS citas FROM citas;"
Tables_in_clinica_db:
- citas
- doctores
- pacientes

Registros sembrados:
- Doctores: 5
- Pacientes: 6
- Citas: 8
```

### 4. Persistencia de Datos
El volumen nombrado `clinica_mysql_data` garantiza que todos los registros creados o modificados persistan en el host ante cualquier reinicio del contenedor.

---

## ✅ Checklist de Verificación
- [x] Dockerfile / Docker Compose funcional y reproducible con `docker compose up`.
- [x] Contenedor de MySQL 8.0 levantado y operativo en estado `healthy`.
- [x] Volumen de persistencia creado y verificado.
- [x] Tablas `pacientes`, `doctores` y `citas` creadas con integridad referencial e índices.
- [x] Datos semilla insertados correctamente.
- [x] Commits descriptivos con trazabilidad a `[RQNF-01]` y `[RQNF-02]`.
