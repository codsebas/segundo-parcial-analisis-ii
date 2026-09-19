# Pull Request #4: Motor de Validación de Conflictos en Servidor (HTTP 409) y Máquina de Estados de Citas

- **Rama origen:** `feature/validacion-conflictos-estados`
- **Rama destino:** `main`
- **Estado:** Listo para revisión y aprobación
- **Criterios / Backlog cubiertos:**
  - `[RQF-03]`: Impedir la doble reserva: no puede existir más de una cita activa para el mismo doctor en horarios que se solapan.
  - `[RQF-05]`: Cancelar una cita cambiando su estado, sin eliminar el registro histórico en base de datos.
  - `[RQNF-03]`: Códigos HTTP correctos: 200/201 éxito, 400 datos inválidos, 404 no encontrado, 409 conflicto de horario.
  - `[RQNF-07]`: La validación de conflicto de horario debe ejecutarse en el servidor; una validación exclusivamente en el cliente no es suficiente.

---

## 📝 Descripción del Cambio

Este Pull Request implementa el **núcleo de reglas de negocio del dominio médico**, garantizando que ninguna doble reserva ocurra en el servidor y formalizando el ciclo de vida de las citas médicas:

### 1. Motor de Detección de Conflictos (`AppointmentConflictValidator.php`)
Implementa la condición matemática estricta de solapamiento de intervalos continuos:
$$[inicio_A, fin_A) \cap [inicio_B, fin_B) \neq \emptyset \iff (inicio_A < fin_B) \land (fin_A > inicio_B)$$

Cubre y clasifica formalmente los 5 escenarios de solapamiento:
1. **Solapamiento Exacto**: Coincidencia idéntica de hora inicio y fin.
2. **Solapamiento Parcial Inicial**: La nueva cita inicia antes y termina dentro de la existente.
3. **Solapamiento Parcial Final**: La nueva cita inicia dentro y finaliza después de la existente.
4. **Solapamiento Envolvente**: La nueva cita engloba completamente a una cita corta existente.
5. **Solapamiento Contenido**: La nueva cita queda totalmente dentro de una cita larga existente.

**Casos permitidos sin conflicto:**
- **Citas Contiguas en Frontera**: Si la cita A termina a las `09:00:00` y la cita B inicia a las `09:00:00`, NO existe solapamiento (`inicioB >= finA`).
- **Doctores Distintos**: Médicos diferentes pueden atender en horarios paralelos.
- **Citas Canceladas**: Una cita en estado `cancelada` libera la agenda del doctor de inmediato.

### 2. Máquina de Estados de Citas (`CitaStateMachine.php`)
Controla las transiciones permitidas y la inmutabilidad:
- `pendiente` ➔ `confirmada`, `cancelada`
- `confirmada` ➔ `atendida`, `cancelada`
- `atendida` ➔ **Estado terminal** (no puede ser reabierta)
- `cancelada` ➔ **Estado terminal** (no puede ser reabierta)
- **Preservación Histórica (RQF-05)**: La cancelación actualiza el campo `estado = 'cancelada'`, manteniendo intacto el registro en la base de datos para auditoría y reportería.

### 3. Endpoint de Verificación Preventiva
- `POST /api/citas/validar-disponibilidad`: Permite evaluar la agenda de un doctor en tiempo real sin intentar persistir, devolviendo HTTP 200 (`disponible: true`) o HTTP 409 (`disponible: false, tipo_conflicto, conflicto`).

---

## 🧪 Evidencias de Ejecución Automatizada

Ejecución de la suite dedicada `Tests\Feature\ConflictosEstadosTest`:

```
   PASS  Tests\Feature\ConflictosEstadosTest
  ✓ detecta solapamiento exacto retorna 409                                      0.23s  
  ✓ detecta solapamiento parcial inicial retorna 409                             0.09s  
  ✓ detecta solapamiento parcial final retorna 409                               0.07s  
  ✓ detecta solapamiento envolvente retorna 409                                  0.05s  
  ✓ detecta solapamiento contenido retorna 409                                   0.09s  
  ✓ permite citas contiguas en frontera exacta sin conflicto                     0.11s  
  ✓ cita cancelada libera agenda del doctor                                      0.06s  
  ✓ maquina estados transicion valida pendiente a confirmada                     0.05s  
  ✓ maquina estados transicion valida confirmada a atendida                      0.04s  
  ✓ cancelacion preserva registro historico en bd                                0.05s  
  ✓ maquina estados rechaza reactivar cita cancelada                             0.07s  
  ✓ endpoint validar disponibilidad retorna 200 cuando esta libre                0.05s  
  ✓ endpoint validar disponibilidad retorna 409 cuando hay conflicto             0.04s  

  Tests:    13 passed (29 assertions)
  Duration: 1.20s
```

---

## ✅ Checklist de Verificación
- [x] Motor matemático de solapamiento implementado y validado en servidor (RQNF-07).
- [x] Respuestas formales con código HTTP 409 Conflict ante cualquier solapamiento de horario (RQF-03).
- [x] Máquina de estados con transiciones estrictas y control de estados terminales.
- [x] Cancelación de cita comprobada con persistencia en MySQL sin borrado físico (RQF-05).
- [x] Citas canceladas comprobadas liberando la agenda del doctor.
- [x] 13 de 13 pruebas de la suite de conflictos aprobadas al 100%.
