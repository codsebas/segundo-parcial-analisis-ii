# Pull Request #2: Suite Automatizada de Pruebas para Entorno Docker y Persistencia MySQL

- **Rama origen:** `feature/docker-test-suite`
- **Rama destino:** `main`
- **Estado:** Listo para revisión y aprobación
- **Criterios / Backlog cubiertos:**
  - `[RQNF-01]`: La base de datos MySQL debe ejecutarse en un contenedor Docker con persistencia mediante volumen.
  - `[RQNF-02]`: El entorno de base de datos debe poder levantarse con un solo comando (`docker compose up`) de forma reproducible.
  - `[RQNF-08]`: Toda evidencia (comandos, respuestas y verificaciones) debe quedar documentada formalmente.

---

## 📝 Descripción del Cambio

Este Pull Request implementa una **Suite Automatizada de Pruebas de Integración** (`tests/verify-docker.php` y `tests/verify-docker.ps1`) dedicada a validar de manera rigurosa, reproducible y cuantitativa el correcto funcionamiento del entorno de base de datos en Docker:

1. **TEST-01 (Sintaxis y Reproducibilidad [RQNF-02])**:
   - Valida el manifiesto `docker-compose.yml` mediante `docker compose config` asegurando la correcta definición del servicio `clinica_mysql`.
2. **TEST-02 (Estado del Contenedor y Healthcheck [RQNF-01] [RQNF-02])**:
   - Inspecciona el ciclo de vida del contenedor en Docker verificando estado `running` y estado de salud `healthy`.
3. **TEST-03 (Socket TCP y Exposición de Puerto [RQNF-01])**:
   - Comprueba la accesibilidad del puerto `3306` en el host mediante socket de red.
4. **TEST-04 (Autenticación y Acceso a Base de Datos [RQNF-01])**:
   - Establece una conexión autenticada vía PDO con el usuario `clinica_user` a la base de datos `clinica_db`.
5. **TEST-05 (Integridad del Esquema DDL [RQNF-01])**:
   - Valida la existencia de las tablas `pacientes`, `doctores` y `citas`.
6. **TEST-06 (Validación de Datos Semilla y Estados [RQNF-01])**:
   - Verifica los conteos mínimos requeridos (≥5 doctores, ≥6 pacientes, ≥8 citas) y la presencia efectiva de los 4 estados del backlog (`pendiente`, `confirmada`, `atendida`, `cancelada`).
7. **TEST-07 (Comprobación Real de Persistencia con Volumen Docker tras Reinicio [RQNF-01])**:
   - Inserta un registro testigo dinámico en la base de datos.
   - Ejecuta un reinicio forzado del contenedor (`docker compose restart mysql`).
   - Espera la reestabilización del servicio y verifica que el registro persiste intacto en el volumen `clinica_mysql_data`.
   - Limpia el registro de prueba tras la aserción exitosa.

---

## 🔍 Evidencias de Ejecución Automatizada

```
======================================================================
 SUITE DE PRUEBAS DE INTEGRACIÓN: ENTORNO DOCKER Y PERSISTENCIA MYSQL 
======================================================================
Fecha y Hora: 2026-09-19 15:36:51

----------------------------------------------------------------------
[TEST-01] Validación de sintaxis y reproducibilidad de docker-compose.yml [RQNF-02]
  -> docker-compose.yml es válido y define el servicio 'clinica_mysql'.
[PASS] -> Prueba superada con éxito.
----------------------------------------------------------------------
[TEST-02] Verificación del contenedor clinica_mysql en ejecución y saludable [RQNF-01] [RQNF-02]
  -> Estado del contenedor: running | Salud: healthy
[PASS] -> Prueba superada con éxito.
----------------------------------------------------------------------
[TEST-03] Verificación de socket TCP en puerto 3306 expuesto al host [RQNF-01]
  -> Puerto 3306 accesible correctamente vía TCP.
[PASS] -> Prueba superada con éxito.
----------------------------------------------------------------------
[TEST-04] Conexión autenticada vía PDO con usuario clinica_user a clinica_db [RQNF-01]
  -> Conectado a DB: clinica_db | MySQL Versión: 8.0.46
[PASS] -> Prueba superada con éxito.
----------------------------------------------------------------------
[TEST-05] Integridad del esquema DDL (tablas pacientes, doctores, citas) [RQNF-01]
  -> Tablas detectadas: citas, doctores, pacientes
[PASS] -> Prueba superada con éxito.
----------------------------------------------------------------------
[TEST-06] Validación de datos semilla mínimos y los 4 estados de citas [RQNF-01]
  -> Conteo registrado: Doctores=5 (esperado >=5), Pacientes=6 (esperado >=6), Citas=8 (esperado >=8)
  -> Estados de citas presentes: pendiente, confirmada, cancelada, atendida
[PASS] -> Prueba superada con éxito.
----------------------------------------------------------------------
[TEST-07] Persistencia comprobada: inserción de registro testigo, reinicio del contenedor y verificación [RQNF-01]
  -> 1. Insertando registro testigo en base de datos: 'PACIENTE_TEST_VOLUMEN_2980'
  -> 2. Reiniciando el contenedor con 'docker compose restart mysql'...
  -> 3. Esperando 10 segundos a que el motor MySQL reinicie y vuelva a estar listo...
  -> 4. Verificando que el registro testigo persiste intacto en el volumen clinica_mysql_data...
  -> 5. Limpiando registro testigo...
  -> ¡Persistencia confirmada al 100%! El registro sobrevivió al ciclo de reinicio del contenedor.
[PASS] -> Prueba superada con éxito.
======================================================================
 RESUMEN FINAL DE LA SUITE DE PRUEBAS DE DOCKER 
======================================================================
Total de pruebas ejecutadas: 7
Pruebas superadas (PASS):    7
Pruebas fallidas  (FAIL):    0

>> RESULTADO: TODAS LAS PRUEBAS DE DOCKER Y PERSISTENCIA PASARON SATISFACTORIAMENTE (100%) <<
```

---

## ✅ Checklist de Verificación
- [x] Script de pruebas automatizadas funcional en PHP y ejecutable con wrapper PowerShell.
- [x] 7 de 7 pruebas unitarias/integradas aprobadas (100%).
- [x] Persistencia de volumen (`clinica_mysql_data`) demostrada formalmente mediante reinicio de contenedor.
- [x] Trazabilidad semántica en commits con referencias a `[RQNF-01]`, `[RQNF-02]` y `[RQNF-08]`.
