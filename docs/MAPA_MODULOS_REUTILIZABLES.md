# Mapa de Módulos Reutilizables

Equivalencias entre los términos específicos de Viajes By Mex y los conceptos genéricos que esta plantilla puede representar en cualquier proyecto de reservaciones.

---

## Entidades Principales

| ByMex Actual | Concepto Genérico | Ejemplos en Otros Giros |
|-------------|-------------------|------------------------|
| **Tour** | Servicio Reservable / Evento / Producto | Curso, Taller, Cita, Función, Sesión, Excursión, Paquete |
| **Pasajero** | Participante / Persona Asociada | Alumno, Asistente, Inscrito, Paciente, Invitado |
| **Asiento** | Recurso / Cupo / Lugar | Plaza, Mesa, Estación, Horario, Espacio, Butaca |
| **Reservación** | Reserva / Inscripción / Contratación | Registro, Apartado, Booking, Orden |
| **Cliente** | Titular / Contratante / Responsable | Cliente, Padre de familia, Organizador, Contacto |
| **Pago** | Abono / Transacción | Depósito, Transferencia, Cobro, Cuota |
| **Voucher** | Comprobante de Pago | Recibo, Factura simplificada, Nota de venta |
| **Ticket** | Comprobante de Reserva | Boleto, Constancia, Pase, Confirmación |
| **Punto de Abordaje** | Punto de Encuentro / Ubicación | Campus, Sede, Sucursal, Dirección, Sala |
| **Sub-punto** | Ubicación Específica | Edificio, Parada exacta, Puerta, Módulo |
| **Flota / Autobús** | Recurso Físico / Equipo | Salón, Vehículo, Instalación, Cancha |
| **Banner** | Contenido Promocional | Slide, Anuncio, Destacado |
| **Bonificación** | Beneficio de Lealtad | Puntos, Descuento acumulado, Cortesía |

---

## Módulos Funcionales

| Módulo ByMex | Módulo Genérico | Archivos Clave |
|-------------|-----------------|----------------|
| Catálogo de Tours | Catálogo de Servicios | `Web/TourController`, `tours/show.blade.php` |
| Mapa de Asientos | Selector de Recursos/Cupos | `Api/SeatController`, `_bus_map.blade.php` |
| Motor de Reservas | Motor de Reservas | `ReservationService`, `Web/ReservationController` |
| Pasajeros por Reserva | Participantes por Reserva | `ReservationPassenger` model |
| Pagos y Abonos | Pagos y Abonos | `PaymentService`, `Payment` model |
| Mercado Pago | Pasarela de Pagos | `MercadoPagoWebhookController` |
| Cancelación con Penalización | Cancelación con Retención | `AdminReservationController@cancelPassenger` |
| Ajustes Financieros | Ajustes Financieros | `ReservationAdjustment` model |
| Documentos de Pasajero | Documentos de Participante | `PassengerDocument` model |
| Ticket PDF | Comprobante de Reserva PDF | `pdf/ticket.blade.php` |
| Voucher PDF | Comprobante de Pago PDF | `pdf/voucher.blade.php` (si existe) |
| Portal del Cliente | Portal del Usuario | `Client/DashboardController` |
| Panel Admin | Panel Admin | `Admin/*Controller` |
| Configuración de Pagos | Configuración del Negocio | `PaymentSetting` model |
| Cuentas Bancarias | Métodos de Pago Manual | `BankAccount` model |
| Políticas | Términos y Condiciones | Campos en `PaymentSetting` |
| WhatsApp | Comunicación Directa | Botones `wa.me` en vistas |
| Notificaciones Admin | Alertas del Sistema | `AdminNotification` model |
| Bonificaciones | Programa de Lealtad | `BonusRequest` model |
| Banners | Contenido Dinámico | `Banner` model |

---

## Estados del Sistema

### Estados de Reserva
| Estado | Significado Genérico |
|--------|---------------------|
| `pending` | Reservada, esperando pago/confirmación |
| `partial` | Pago parcial recibido |
| `paid` | Totalmente pagada |
| `expired` | Venció el plazo sin pago |
| `cancelled` | Cancelada por admin o sistema |

### Estados de Cupo/Recurso
| Estado | Significado Genérico |
|--------|---------------------|
| `available` | Disponible para reservar |
| `pending` | Apartado temporalmente |
| `paid` | Confirmado y pagado |

### Estados de Participante
| Estado | Significado Genérico |
|--------|---------------------|
| `active` | Vigente |
| `cancelled` | Cancelado (con campos de retención) |
| `validated` | Documentos verificados |

---

## Flujos Adaptables por Giro

### Agencia de Viajes (Implementación actual)
Tour → Asientos en autobús → Pasajeros con descuento por edad → Pago con anticipo → Ticket con itinerario

### Academia / Cursos
Curso → Cupos por grupo → Alumnos con precio por nivel → Inscripción con enganche → Constancia de inscripción

### Eventos con Boletos
Evento → Localidades/Zonas → Asistentes → Pago completo o parcial → Boleto de acceso

### Consultorio / Citas
Servicio médico → Horarios disponibles → Paciente → Anticipo de consulta → Confirmación de cita

### Salón de Eventos
Fecha/Horario → Espacio → Contratante + Invitados → Anticipo + liquidación → Contrato PDF

---

## Archivo de Configuración: `config/template.php`

Este archivo centraliza las etiquetas del sistema para facilitar la adaptación sin tocar lógica:

```php
'labels' => [
    'service'              => 'Tour',
    'services'             => 'Tours',
    'participant'          => 'Pasajero',
    'participants'         => 'Pasajeros',
    'resource'             => 'Asiento',
    'resources'            => 'Asientos',
    'reservation'          => 'Reservación',
    'reservations'         => 'Reservaciones',
    'payment_receipt'      => 'Voucher',
    'reservation_document' => 'Ticket',
    'meeting_point'        => 'Punto de Abordaje',
    'meeting_points'       => 'Puntos de Abordaje',
    'fleet'                => 'Flota',
    'business_tagline'     => 'Agencia de Viajes y Excursiones Premium',
],
```

Para un sistema de cursos, cambiarías a:

```php
'labels' => [
    'service'              => 'Curso',
    'services'             => 'Cursos',
    'participant'          => 'Alumno',
    'participants'         => 'Alumnos',
    'resource'             => 'Cupo',
    'resources'            => 'Cupos',
    'reservation'          => 'Inscripción',
    'reservations'         => 'Inscripciones',
    'payment_receipt'      => 'Recibo',
    'reservation_document' => 'Constancia',
    'meeting_point'        => 'Campus',
    'meeting_points'       => 'Campus',
    'fleet'                => 'Instalaciones',
    'business_tagline'     => 'Centro de Capacitación Profesional',
],
```
