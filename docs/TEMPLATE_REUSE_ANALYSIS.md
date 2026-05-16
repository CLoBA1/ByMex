# Análisis de Reutilización como Plantilla

Fecha de análisis: Mayo 2026  
Proyecto base: Viajes By Mex (Laravel)  
Objetivo: Evaluar qué tan reutilizable es el sistema actual como plantilla lógica para otros proyectos de reservaciones con cupos, pagos y administración.

---

## 1. Resumen del Potencial como Plantilla

El sistema actual implementa un **motor de reservaciones completo** que cubre desde la selección de cupos/recursos hasta la conciliación financiera y generación de documentos. Su arquitectura (Controladores → Servicios → Modelos → Vistas Blade) es lo suficientemente modular para extraer una plantilla reutilizable con esfuerzo moderado.

**Nivel de reutilización estimado: 75-80% del código lógico.**

El 20-25% restante son textos, copy de marca, colores y reglas de negocio específicas de una agencia de viajes terrestres (tipos de pasajero, descuentos por duración de tour, puntos de abordaje geográficos).

---

## 2. Módulos 100% Reutilizables (Núcleo Lógico)

Estos módulos funcionan como motor genérico sin importar el giro del negocio:

| Módulo | Archivos Clave | Notas |
|--------|----------------|-------|
| **Reservaciones** | `ReservationService`, `Reservation` model, `ReservationController` (Web + Admin) | Motor central. Crear reserva, calcular totales, expiración automática. |
| **Pagos y Abonos** | `PaymentService`, `Payment` model, `PaymentController` | Soporta pagos parciales, acumulación, conciliación de saldos. |
| **Clientes** | `Client` model, `ClientController` (Admin) | CRUD básico, historial por cliente. |
| **Recursos/Cupos** | `ReservationSeat` model, `SeatController` (API) | Inventario en tiempo real, liberación automática por expiración. |
| **Participantes** | `ReservationPassenger` model | Detalle nominal por reserva con precios individuales. |
| **Ajustes Financieros** | `ReservationAdjustment` model | Penalizaciones, retenciones, refunds trazables. |
| **Cancelación controlada** | `AdminReservationController@cancelPassenger` | Cancelar sin borrar datos ni dinero. |
| **Documentos** | `PassengerDocument` model | Subida y descarga de archivos por participante. |
| **PDFs (Ticket y Voucher)** | `resources/views/pdf/ticket.blade.php`, voucher | Comprobantes generados con DomPDF. |
| **Configuración bancaria** | `PaymentSetting`, `BankAccount` models | Dinámico desde admin, no requiere código. |
| **Políticas** | Campos en `PaymentSetting` | Textos editables de reservación, cancelación, reembolso. |
| **WhatsApp** | Botones en vistas | Integración directa `wa.me` sin API de pago. |
| **Notificaciones admin** | `AdminNotification` model | Alertas en tiempo real al panel. |
| **Portal de cliente** | `Client/AuthController`, `Client/DashboardController` | Login separado, historial, documentos. |
| **Bonificaciones** | `BonusRequest` model, `BonusRequestController` | Programa de lealtad con solicitud y aprobación. |
| **Expiración automática** | `ReservationService@cancelExpiredReservations` | Libera cupos vencidos (cron + lazy cleanup). |
| **Mercado Pago** | `MercadoPagoWebhookController`, `PaymentService` | Webhook con HMAC, idempotencia por `mp_payment_id`. |
| **Banners** | `Banner` model, `BannerController` | Administración dinámica de contenido visual. |

---

## 3. Partes Acopladas a Viajes By Mex

Estas partes requieren adaptación al cambiar de giro:

### 3.1 Terminología Hardcodeada

| Ubicación | Texto Actual | Concepto Genérico |
|-----------|--------------|-------------------|
| Modelos, controladores, rutas | `Tour` | Servicio / Evento / Producto Reservable |
| Modelos, vistas | `Pasajero` / `Passenger` | Participante / Asistente / Persona |
| Modelos, vistas | `Asiento` / `Seat` | Recurso / Cupo / Lugar / Horario |
| Vistas, rutas | `Viaje` / `Tour` | Servicio / Experiencia / Sesión |
| Vistas | `Punto de Abordaje` | Punto de Encuentro / Sucursal / Ubicación |
| Modelos, vistas | `Flota` / `Bus` | Recurso Físico / Equipo / Instalación |
| PDF ticket | `Viajero` | Cliente / Titular |
| PDF ticket | `Detalles del Viaje` | Detalles del Servicio |

### 3.2 Branding y Contenido Visual

| Archivo | Elemento Hardcodeado |
|---------|---------------------|
| `layouts/public.blade.php` | Logo `logobymex.jpeg`, título "Viajes By Mex", meta description, teléfonos, email, dirección |
| `welcome.blade.php` | Textos "Descubre México", "Salidas desde: Acapulco, Chilpancingo e Iguala" |
| `pdf/ticket.blade.php` | Logo path, texto "Viajes By Mex - Hecho en México", "Agencia de Viajes y Excursiones Premium" |
| `about.blade.php` | Texto "Nosotros" específico de la agencia |
| `services.blade.php` | Servicios específicos de agencia de viajes |
| Footer (layout público) | Teléfonos `744 129 5026`, `733 136 2024`, email `info@viajesbymex.com` |
| Botón flotante WhatsApp | Número hardcodeado `527441295026` |
| `PaymentService` | `'statement_descriptor' => 'ByMex Viajes'` |
| `PaymentService` | Email fallback `no-reply@bymex.com` |
| `DatabaseSeeder` | Email `admin@bymex.com` |

### 3.3 Lógica de Negocio Específica

| Lógica | Detalle | Cómo Generalizar |
|--------|---------|-------------------|
| Descuento por tipo de pasajero | 50% niños (tour ≤ 2 días), 75% niños (tour > 2 días). Adultos/Adultos Mayores sin descuento. | Hacer configurable por servicio o tabla de reglas. |
| Tipos de pasajero | `Adulto`, `Niño`, `Adulto Mayor` | Configurar tipos y descuentos desde admin. |
| Documentos requeridos | "INE o INAPAM" | Configurar tipos de documento por servicio. |
| Puntos de abordaje | Modelo geográfico con ciudades y subpuntos | Renombrar a "Puntos de encuentro" o "Ubicaciones". |
| Duración del tour | Campo `duration_days` afecta descuentos | Puede no aplicar en otros giros. |

---

## 4. Riesgos del Refactor

| Riesgo | Nivel | Mitigación |
|--------|-------|------------|
| Renombrar modelos/tablas (`Tour` → `Service`) | **ALTO** | No hacerlo ahora. Documentar equivalencias. Las migraciones existentes dependen de los nombres actuales. |
| Cambiar rutas públicas | **MEDIO** | Las URLs `/tours/{id}` están indexadas en Google y referenciadas en producción. Para la plantilla usar alias. |
| Tocar `PaymentService` o `MercadoPagoWebhookController` | **ALTO** | La integración está probada en producción. Solo agregar configurabilidad sin modificar lógica. |
| Crear sistema de configuración complejo | **MEDIO** | Empezar solo con `config/template.php` para labels. Conectar gradualmente. |
| Romper PDFs al cambiar textos | **BAJO** | Los PDFs ya usan `$paymentSettings` dinámicamente para casi todo. Solo el header/footer tiene texto hardcodeado. |

---

## 5. Recomendaciones

### Hacer Ahora (Fase Actual)
1. **Crear `config/template.php`** con labels configurables (no conectar a vistas aún).
2. **Documentar el mapa de equivalencias** ByMex → Genérico.
3. **Crear guía de adaptación** paso a paso.
4. **Crear README de plantilla** con requisitos y flujo de arranque.

### Hacer en Versión 2 (Próximo Sprint)
1. Reemplazar textos hardcodeados del layout público y PDFs por `config('template.labels')` o campos de `PaymentSetting`.
2. Hacer configurables los tipos de participante y reglas de descuento.
3. Agregar campo `business_logo` a `PaymentSetting` para logo dinámico.

### Dejar para Versión SaaS / Multitenant
1. Tabla `companies` con `company_id` en todos los módulos principales.
2. Subdominios o dominios personalizados por empresa.
3. Planes y suscripciones.
4. Superadmin global.
5. Aislamiento total de datos.

---

## 6. Conclusión

El proyecto **es altamente reutilizable** como plantilla lógica. El motor de reservaciones, pagos parciales, expiración, cancelaciones con trazabilidad financiera, PDFs y portal de cliente son genéricos por naturaleza. Los textos de marca y reglas específicas de la agencia de viajes representan una capa delgada que se puede abstraer sin reescribir el sistema.
