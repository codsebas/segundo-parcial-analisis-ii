# Sistema de Gestión de Citas Médicas - Segundo Parcial

**Universidad Mariano Gálvez de Guatemala**  
**Facultad de Ingeniería en Sistemas de Información y Ciencias de la Computación**  
**Curso:** Análisis de Sistemas II  
**Repositorio:** [https://github.com/codsebas/segundo-parcial-analisis-ii.git](https://github.com/codsebas/segundo-parcial-analisis-ii.git)

---

## 📋 Descripción del Proyecto

El sistema es una aplicación integral para la planificación y gestión de citas médicas clínicas, diseñada con arquitectura por capas, persistencia en base de datos MySQL orquestada mediante Docker, API REST desacoplada y una interfaz rica e interactiva basada en FullCalendar v6.

El proyecto sigue un estricto flujo Git colaborativo compuesto por al menos 4 ramas de feature, commits semánticos con trazabilidad al backlog, Pull Requests documentados y merges a la rama principal `main`.

---

## 🏗️ Arquitectura del Sistema

El sistema implementa una arquitectura desacoplada y por capas (RQNF-04):

```
├── docker-compose.yml              # Orquestación del servicio MySQL 8.0 y volúmenes
├── init.sql                        # Esquema de base de datos DDL y datos semilla
├── package.json                    # Manifiesto de dependencias y scripts Node.js
├── .env.example                    # Plantilla de variables de entorno
├── .gitignore                      # Reglas de exclusión de Git
├── docs/
│   └── pull-requests/              # Evidencias y documentación de cada Pull Request
├── src/
│   ├── config/                     # Configuración y conexión de base de datos (Pool mysql2)
│   ├── repositories/               # Capa de Acceso a Datos (consultas SQL)
│   ├── services/                   # Capa de Lógica de Negocio (validaciones y solapamientos)
│   ├── controllers/                # Capa de Controladores HTTP
│   ├── routes/                     # Definición de rutas REST
│   ├── app.js                      # Aplicación Express y middlewares
│   └── server.js                   # Arranque del servidor HTTP
├── public/                         # Capa de Presentación (Frontend)
│   ├── index.html                  # Interfaz de usuario responsiva
│   ├── css/                        # Estilos y esquema visual por estados
│   └── js/                         # Lógica de FullCalendar y consumo de API
├── test/                           # Suite de pruebas automatizadas
└── EVIDENCIA.md                    # Reporte completo de evidencias y trazabilidad
```

---

## 📌 Matriz de Trazabilidad del Backlog

### Requisitos Funcionales (RQF)
- **[RQF-01]**: Crear cita médica indicando paciente, doctor, fecha, horas inicio/fin y motivo.
- **[RQF-02]**: Mostrar citas en FullCalendar (vistas mes y semana).
- **[RQF-03]**: Impedir doble reserva (solapamiento de horario para el mismo doctor en servidor).
- **[RQF-04]**: Reprogramar cita arrastrándola en el calendario (drag & drop) vía API.
- **[RQF-05]**: Cancelar cita cambiando estado sin eliminar el registro histórico.
- **[RQF-06]**: Filtrar/listar citas por doctor y rango de fechas.
- **[RQF-07]**: API REST con operaciones CRUD de citas y lectura de doctores y pacientes.
- **[RQF-08]**: Validar datos de entrada (requeridos, formatos, existencia en BD).
- **[RQF-09]**: Mostrar detalle de cita al hacer clic sobre evento en calendario.
- **[RQF-10]**: Representar visualmente estado mediante colores (pendiente, confirmada, cancelada, atendida).

### Requisitos No Funcionales (RQNF)
- **[RQNF-01]**: Base de datos MySQL en contenedor Docker con persistencia mediante volumen.
- **[RQNF-02]**: Entorno reproducible con un solo comando (`docker compose up`).
- **[RQNF-03]**: API con respuestas JSON y códigos HTTP correctos (200, 201, 400, 404, 409).
- **[RQNF-04]**: Organización en 4 capas estrictas (Presentación, Controladores, Negocio, Datos).
- **[RQNF-05]**: Trazabilidad Git: 4 ramas de feature, commits descriptivos, PRs y merges a main.
- **[RQNF-06]**: Interfaz web responsive para escritorio y tablet.
- **[RQNF-07]**: Validación de conflicto de horario ejecutada en el servidor.
- **[RQNF-08]**: Evidencias completas documentadas en `EVIDENCIA.md`.

---

## 🚀 Flujo Git de Ramas Requeridas

1. `feature/docker-mysql-schema` ➔ Pull Request #1 ➔ `main`
2. `feature/api-rest-citas` ➔ Pull Request #2 ➔ `main`
3. `feature/validacion-conflictos-estados` ➔ Pull Request #3 ➔ `main`
4. `feature/fullcalendar-ui` ➔ Pull Request #4 ➔ `main`
