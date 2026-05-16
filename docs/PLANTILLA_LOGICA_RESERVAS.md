# Plantilla Lógica de Reservaciones

Sistema base reutilizable para proyectos que requieran gestión de reservas con cupos limitados, pagos parciales, documentos y administración financiera.

---

## 1. Concepto General

Esta plantilla implementa un **ciclo de vida completo de reservación**:

```
Producto/Servicio reservable
  → Disponibilidad de cupos/recursos
    → Reservación (con expiración configurable)
      → Participantes con precios individuales
        → Pagos y abonos (en línea o manuales)
          → Saldo pendiente, conciliación
            → Comprobantes PDF (Ticket + Voucher)
              → Cancelaciones con retención financiera
                → Portal del cliente + Panel admin
```

Aplica para: agencias de viajes, eventos, cursos, citas, academias, salones reservables, servicios con anticipo.

---

## 2. Arquitectura Lógica

### Capas del Sistema

```
┌─────────────────────────────────────────┐
│          CAPA DE PRESENTACIÓN           │
│  Blade Views (Público + Admin + PDF)    │
├─────────────────────────────────────────┤
│          CAPA DE CONTROL                │
│  Controllers (Web / Admin / Api)        │
├─────────────────────────────────────────┤
│         CAPA DE NEGOCIO                 │
│  Services (Reservation, Payment)        │
├─────────────────────────────────────────┤
│          CAPA DE DATOS                  │
│  Models + Eloquent ORM                  │
├─────────────────────────────────────────┤
│        BASE DE DATOS (MySQL)            │
└─────────────────────────────────────────┘
```

### Entidades Principales

| Entidad BD | Rol en el Sistema |
|-----------|-------------------|
| `tours` | El servicio/producto que se reserva (tiene precio, cupos, fecha). |
| `clients` | El titular de la reserva (quien paga). |
| `reservations` | El expediente financiero que agrupa participantes y pagos. |
| `reservation_passengers` | Los individuos asociados a la reserva, con precio propio. |
| `reservation_seats` | Los cupos/recursos asignados y su estado. |
| `payments` | Los abonos recibidos (manuales o automáticos). |
| `reservation_adjustments` | Penalizaciones, retenciones y devoluciones. |
| `passenger_documents` | Archivos subidos por participante. |
| `payment_settings` | Configuración del negocio (políticas, WhatsApp, instrucciones). |
| `bank_accounts` | Cuentas bancarias dinámicas. |

---

## 3. Flujo de Reserva

### Paso a paso:

1. **Cliente navega** el catálogo de servicios disponibles.
2. **Selecciona un servicio** y entra al detalle.
3. **Acepta políticas** (mostradas desde `payment_settings`).
4. **Selecciona cupo(s)/recurso(s)** en el mapa interactivo.
5. **Llena datos del titular** (nombre, teléfono, email opcional).
6. **Registra participantes** por cada cupo (nombre, tipo, teléfono).
7. **Se crea la reserva** en BD con `status = pending`, token público y fecha de expiración.
8. **Se muestran opciones de pago**: Mercado Pago (en línea) o Transferencia (manual).
9. **El cliente recibe** un enlace seguro con su resumen.

### Qué sucede en el backend:
- `ReservationService@processNewReservation()` ejecuta todo en una transacción DB.
- Calcula precios individuales con descuentos configurados.
- Ejecuta limpieza "lazy" de reservas expiradas antes de insertar.
- Genera `public_token` criptográfico para URLs seguras.
- Dispara evento `ReservationCreated` para notificaciones.

---

## 4. Flujo de Pago

### Pago en Línea (Mercado Pago):
1. Cliente hace clic en "Pagar" → se genera una Preferencia de Pago.
2. Mercado Pago redirige al cliente para pagar.
3. Tras el pago, MP envía un `POST` al webhook del sistema.
4. El webhook verifica firma HMAC, consulta la API de MP, y si está aprobado:
   - Crea registro en `payments` con `mp_payment_id` (idempotencia).
   - Recalcula `balance_due`.
   - Si saldo = 0, marca reserva como `paid` y cupos como `paid`.

### Pago Manual (Transferencia/Depósito):
1. Cliente deposita en una cuenta bancaria mostrada en su ticket.
2. Admin registra el pago manualmente desde el panel con monto y nota.
3. El sistema recalcula saldos automáticamente.

### Abonos Parciales:
- El sistema soporta N pagos parciales por reserva.
- `balance_due` se actualiza dinámicamente: `max(0, total_ajustado - sum(pagos aprobados))`.
- La reserva cambia de `pending` → `partial` → `paid` conforme llegan los abonos.

---

## 5. Flujo de Cancelación

1. Admin abre el detalle de la reserva.
2. Selecciona un participante y da clic en "Cancelar".
3. Ingresa motivo y monto de retención (penalización).
4. El sistema:
   - Marca al participante como `cancelled` (no lo borra).
   - Registra un `ReservationAdjustment` tipo `penalty`.
   - Libera el cupo/recurso asociado.
   - Recalcula: `total = sum(activos) + penalties - refunds`.
   - Actualiza `balance_due`.

**Principio:** Los pagos nunca se borran. Cancelar ≠ borrar dinero.

---

## 6. Flujo de Documentos

- Al crear un servicio, el admin puede activar "Requiere documentos".
- El participante/cliente puede subir archivos (imagen/PDF) desde su portal o la pantalla de confirmación.
- Los archivos se guardan en `storage/app/` con referencia en `passenger_documents`.
- El admin puede descargar, verificar y eliminar documentos individualmente.

---

## 7. Flujo de Portal de Cliente

1. El cliente accede con email + contraseña (generada al crear la primera reserva).
2. Ve su dashboard con viajes próximos y pasados.
3. Puede ver detalle de cada reserva, estado de pago, subir documentos.
4. Si califica, puede solicitar bonificación/canje por lealtad.

---

## 8. Flujo Admin

- **Dashboard:** Resumen con reservaciones pendientes, pagos recientes, alertas.
- **Gestión de Servicios:** CRUD completo de tours/servicios.
- **Gestión de Reservas:** Ver detalle, aprobar pagos, cancelar participantes, descargar PDFs.
- **Configuración:** Políticas, bancos, WhatsApp, instrucciones de pago.
- **Clientes:** Historial, edición, bonificaciones.
- **Notificaciones:** Badge con alertas en tiempo real.

---

## 9. Cómo Adaptar la Plantilla a Otro Giro

### Ejemplo: Sistema de Inscripción a Cursos

| Concepto ByMex | Nuevo Concepto | Cambios |
|----------------|---------------|---------|
| Tour | Curso / Taller | Renombrar en vistas y labels |
| Pasajero | Alumno / Inscrito | Renombrar en vistas |
| Asiento | Cupo / Plaza | El mapa de asientos se convierte en un contador de cupos |
| Punto de abordaje | Campus / Sede | Renombrar labels |
| Anticipo mínimo | Inscripción inicial | Misma lógica de `minimum_deposit` |
| Ticket PDF | Constancia de inscripción | Cambiar textos del PDF |
| INE / INAPAM | Acta de nacimiento / Credencial | Cambiar label de documento |

### Pasos para adaptar:
1. Copiar el proyecto base.
2. Configurar `config/template.php` con los nuevos labels.
3. Cambiar logo e identidad visual en `public/img/`.
4. Editar los textos del layout público.
5. Ajustar tipos de participante y reglas de descuento en `ReservationService`.
6. Configurar bancos, políticas y WhatsApp desde el panel admin.
7. Generar nuevas credenciales de Mercado Pago.
8. Ejecutar la limpieza de datos demo.
9. Validar flujo completo.
