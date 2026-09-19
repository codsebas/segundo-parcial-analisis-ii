# Sistema de Gestión de Citas Médicas · Segundo Parcial

**Universidad Mariano Gálvez de Guatemala (UMG)**  
**Facultad de Ingeniería en Sistemas de Información y Ciencias de la Computación**  
**Curso:** Análisis de Sistemas II (Octavo Ciclo)  
**Evaluación:** Segundo Parcial — Serie II (Módulo Funcional)  
**Estudiante:** Sebastián (`codsebas`)  
**Repositorio:** [https://github.com/codsebas/segundo-parcial-analisis-ii.git](https://github.com/codsebas/segundo-parcial-analisis-ii.git)  

---

## 📋 Descripción del Proyecto

Sistema clínico web para la planificación, agendamiento y gestión reactiva de citas médicas. El software implementa una arquitectura desacoplada y modular en **4 capas** sobre **Laravel 12**, base de datos relacional **MySQL 8.0** orquestada con **Docker** (volumen persistente y healthcheck), motor de contención de colisiones en el servidor (`HTTP 409 Conflict`), máquina de estados determinista con preservación histórica y una interfaz gráfica responsiva basada en **FullCalendar v6**, inspirada en la identidad visual de clínicas médicas especializadas (paleta médica pastel y tipografías Google Fonts `Raleway` y `Open Sans`).

---

## 💻 Requisitos Previos en el Host

Para ejecutar y validar este proyecto se requiere:
- **Docker Desktop** (en ejecución con motor Linux containers).
- **PHP 8.2 o superior** (con extensiones `pdo_mysql` e `intl` habilitadas en `php.ini`).
- **Composer** (gestor de paquetes de PHP).
- **Git** (cliente de control de versiones).

---

## 🚀 Guía de Inicio Rápido (Quickstart Paso a Paso)

Ejecuta los siguientes comandos en tu terminal (PowerShell o Bash) desde la carpeta donde deseas instalar el proyecto:

```bash
# 1. Clonar el repositorio
git clone https://github.com/codsebas/segundo-parcial-analisis-ii.git
cd segundo-parcial-analisis-ii

# 2. Instalar dependencias de PHP (Laravel 12)
composer install

# 3. Configurar entorno y generar llave de cifrado de la aplicación
cp .env.example .env
php artisan key:generate

# 4. Levantar la base de datos MySQL 8.0 en Docker (puerto 3306 y datos semilla automáticos)
docker compose up -d

# 5. Ejecutar la suite automatizada de pruebas (34 tests pasando al 100%)
php artisan test

# 6. Iniciar el servidor local de desarrollo
php artisan serve
```

---

## 🌐 Acceso al Sistema

- **Interfaz Web Interactiva (FullCalendar):** 👉 **[http://127.0.0.1:8000](http://127.0.0.1:8000)**  
  *(Las citas de prueba sembradas se encuentran ubicadas en **Septiembre de 2026**, del 18 al 22 de septiembre).*
- **API REST Endpoints:** Disponibles bajo el prefijo `http://127.0.0.1:8000/api/*` (`/citas`, `/doctores`, `/pacientes`).
- **Documento Maestro de Evidencias:** Consulta el archivo [EVIDENCIA.md](EVIDENCIA.md) para ver la auditoría completa, trazas cURL y capturas de pantalla.

---

## 🔑 Credenciales de Acceso a Base de Datos (Docker)

| Parámetro | Valor Configurado |
|---|---|
| **Contenedor** | `clinica_mysql` |
| **Imagen** | `mysql:8.0` |
| **Host** | `127.0.0.1` |
| **Puerto Host / Contenedor** | `3306` |
| **Base de Datos** | `clinica_db` |
| **Usuario** | `clinica_user` |
| **Contraseña** | `clinica_pass123` |
| **Usuario Root** | `root` / `root_password_segura` |
| **Volumen Persistente** | `clinica_mysql_data` |

---

## 🏗️ Arquitectura en 4 Capas (Laravel 12)

El proyecto cumple estrictamente con el patrón de diseño por capas (`[RQNF-04]`):

```
├── docker-compose.yml              # Orquestación de MySQL 8.0 y volumen persistente
├── init.sql                        # Script DDL de tablas y semillas iniciales
├── .env.example                    # Plantilla de variables de entorno
├── EVIDENCIA.md                    # Informe maestro de evidencias técnicas y capturas
├── docs/
│   └── pull-requests/              # Documentación técnica de cada Pull Request (PR-1 al PR-6)
├── app/
│   ├── Http/
│   │   ├── Controllers/Api/        # Capa de Controladores REST (Cita, Doctor, Paciente)
│   │   ├── Requests/               # Capa de Validación y Contención HTTP 400 (BaseApiRequest, StoreCita, StorePaciente)
│   │   └── Resources/              # Capa de Transformación JSON (CitaResource con colores, PacienteResource)
│   ├── Models/                     # Capa de Dominio (Modelos Eloquent: Cita, Doctor, Paciente)
│   ├── Repositories/
│   │   ├── Contracts/              # Interfaces y contratos del repositorio
│   │   └── Eloquent/               # Implementación de acceso a datos con Eloquent
│   └── Services/                   # Capa de Lógica de Negocio (CitaService, AppointmentConflictValidator, CitaStateMachine)
├── resources/views/
│   └── calendar.blade.php          # Frontend con FullCalendar v6, modales y consumo reactivo
├── routes/
│   ├── api.php                     # Definición de rutas del API REST
│   └── web.php                     # Ruta raíz web '/'
└── tests/
    ├── Feature/                    # Pruebas automatizadas (CitasApiTest, ConflictosEstadosTest, PacientesApiTest, CalendarUiTest)
    └── verify-docker.php           # Script autónomo de verificación de Docker y persistencia (7/7 PASS)
```

---

## 📌 Matriz de Requerimientos y Trazabilidad

### Requerimientos Funcionales (RQF)
- **`[RQF-01]`**: Agendamiento de citas médicas validando campos obligatorios, doctor, paciente, fecha y horas.
- **`[RQF-02]`**: Visualización interactiva en FullCalendar v6 (vistas de Mes, Semana, Día y Agenda).
- **`[RQF-03]`**: Detección server-side de solapamientos con retorno formal de `HTTP 409 Conflict`.
- **`[RQF-04]`**: Reprogramación interactiva por arrastre (Drag & Drop) con reversión automática ante conflicto.
- **`[RQF-05]`**: Cancelación y máquina de estados preservando el registro histórico en base de datos (sin `DELETE`).
- **`[RQF-06]`**: Filtrado dinámico de citas por doctor y estado.
- **`[RQF-07]`**: Catálogos maestros de doctores y pacientes vía API REST.
- **`[RQF-08]`**: Contención de datos inválidos en frontera respondiendo con `HTTP 400 Bad Request`.
- **`[RQF-09]`**: Registro ágil de pacientes en API y UI con pre-selección automática en el agendamiento.
- **`[RQF-10]`**: Código visual de estados (Pendiente en ámbar, Confirmada en azul pastel, Atendida en verde menta y Cancelada en rojo pastel `#FEE2E2`).

### Requerimientos No Funcionales (RQNF)
- **`[RQNF-01]`**: Contenedor MySQL 8.0 en Docker con healthcheck y datos iniciales de prueba.
- **`[RQNF-02]`**: Persistencia de datos comprobada mediante volumen dedicado `clinica_mysql_data`.
- **`[RQNF-03]`**: Estandarización de códigos de estado HTTP (`200 OK`, `201 Created`, `400 Bad Request`, `404 Not Found`, `409 Conflict`).
- **`[RQNF-04]`**: Arquitectura en 4 capas estrictas (FormRequests, Controllers, Services, Repositories).
- **`[RQNF-05]`**: Flujo Git estructurado con ramas feature, commits semánticos, Pull Requests y merges a `main`.
- **`[RQNF-06]`**: Interfaz responsiva, moderna y accesible en escritorio y tablets.
- **`[RQNF-07]`**: Lógica de negocio y cálculo de disponibilidad ejecutados estrictamente en el servidor.
- **`[RQNF-08]`**: Documentación técnica exhaustiva consolidada en `EVIDENCIA.md` y `docs/pull-requests/`.

---

## 🔀 Historial de Git y Pull Requests Merged

El repositorio cuenta con 6 ramas de feature integradas a la rama principal `main` mediante merges no fast-forward visibles en el grafo de Git:

| PR # | Rama Origen | Rama Destino | Título y Descripción | Estado |
|:---:|---|---|---|:---:|
| **[#1](https://github.com/codsebas/segundo-parcial-analisis-ii/pull/1)** | `feature/docker-mysql-schema` | `main` | Configuración de Docker Compose, MySQL 8.0, volumen persistente y DDL inicial con semillas | **MERGED** |
| **[#2](https://github.com/codsebas/segundo-parcial-analisis-ii/pull/2)** | `feature/docker-test-suite` | `main` | Suite de pruebas de integración de Docker y persistencia tras reinicio (7/7 PASS) | **MERGED** |
| **[#3](https://github.com/codsebas/segundo-parcial-analisis-ii/pull/3)** | `feature/api-rest-citas` | `main` | Estructura en 4 capas en Laravel 12, Repositorios, Servicios, API REST y contención 400 | **MERGED** |
| **[#4](https://github.com/codsebas/segundo-parcial-analisis-ii/pull/4)** | `feature/validacion-conflictos-estados` | `main` | Motor matemático de conflictos (409), Máquina de Estados y endpoint validar-disponibilidad | **MERGED** |
| **[#5](https://github.com/codsebas/segundo-parcial-analisis-ii/pull/5)** | `feature/fullcalendar-ui` | `main` | Frontend FullCalendar v6, Drag & Drop, modales, paleta médica y rojo pastel para canceladas | **MERGED** |
| **[#6](https://github.com/codsebas/segundo-parcial-analisis-ii/pull/6)** | `feature/creacion-pacientes` | `main` | Registro ágil de pacientes en API y UI con validación 400 y auto-selección en formulario | **MERGED** |

---

## 🧪 Pruebas Automatizadas

Para validar la integridad de todas las capas del sistema, ejecuta:

```bash
php artisan test
```

**Resultado:**
```text
   PASS  Tests\Unit\ExampleTest
   PASS  Tests\Feature\CalendarUiTest (2 tests)
   PASS  Tests\Feature\CitasApiTest (11 tests)
   PASS  Tests\Feature\ConflictosEstadosTest (13 tests)
   PASS  Tests\Feature\ExampleTest (1 test)
   PASS  Tests\Feature\PacientesApiTest (6 tests)

  Tests:    34 passed (123 assertions)
  Duration: ~3.5s
```
