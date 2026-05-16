# Guía de Adaptación para Nuevo Proyecto

Guía paso a paso para usar esta base como plantilla en un nuevo negocio de reservaciones.

---

## Requisitos Previos

- PHP 8.2+
- Composer
- MySQL/MariaDB
- Node.js (opcional, para assets)
- Cuenta de Mercado Pago (o la pasarela que se desee)
- Servidor con SSH (Hostinger, DigitalOcean, etc.)

---

## Paso A — Cambiar Identidad del Negocio

### Archivos a modificar:

1. **`.env`**
   - `APP_NAME="Nombre del Nuevo Negocio"`
   - `APP_URL=https://nuevo-dominio.com`
   - Nuevas credenciales de BD.
   - Nuevas claves de Mercado Pago (ver Paso E).
   - `OWNER_NOTIFICATION_EMAIL=nuevo@email.com`

2. **`resources/views/layouts/public.blade.php`**
   - Cambiar el logo: reemplazar `public/img/logobymex.jpeg` con el nuevo logo.
   - Cambiar `<title>` del documento.
   - Cambiar meta description.
   - Cambiar textos del footer (teléfonos, email, dirección).
   - Cambiar número del botón flotante de WhatsApp.

3. **`resources/views/pdf/ticket.blade.php`**
   - Cambiar la ruta del logo en el header del PDF.
   - Cambiar el texto "Viajes By Mex - Hecho en México" por el nombre del nuevo negocio.
   - Cambiar "Agencia de Viajes y Excursiones Premium" por la descripción del nuevo giro.

4. **`config/template.php`**
   - Actualizar todos los labels para reflejar la terminología del nuevo negocio.

5. **`database/seeders/DatabaseSeeder.php`**
   - Cambiar email y nombre del admin inicial.

---

## Paso B — Configurar Bancos

Desde el panel de administración:

1. Ir a **Configuración → Pagos y Bancos**.
2. Agregar las cuentas bancarias reales del nuevo negocio.
3. Cada cuenta puede tener: Banco, Titular, Número de Cuenta, CLABE, Tarjeta, Etiqueta.

No se requiere tocar código para esto.

---

## Paso C — Configurar Políticas

Desde el panel de administración:

1. Ir a **Configuración → Pagos y Bancos**.
2. Editar los campos de texto:
   - **Políticas de Reservación**: Términos y condiciones de apartado.
   - **Políticas de Cancelación**: Reglas de penalización.
   - **Políticas de No Asistencia**: Qué pasa si el cliente no se presenta.
   - **Políticas de Reembolso**: Condiciones para devolución.
   - **Instrucciones Generales**: Cómo y dónde depositar.
   - **Nota Final**: Texto al final del ticket PDF.

---

## Paso D — Configurar WhatsApp

1. En **Configuración → Pagos**, llenar el campo **WhatsApp de la empresa**.
2. Este número se usa en:
   - Botón flotante del sitio público.
   - Enlace de notificación en la pantalla de confirmación.
   - Datos de contacto en el ticket PDF.

Para cambiar el botón flotante hardcodeado, editar `resources/views/layouts/public.blade.php` línea del `whatsapp-float`.

---

## Paso E — Configurar Pagos (Mercado Pago)

1. Crear una **nueva aplicación** en [mercadopago.com/developers](https://www.mercadopago.com/developers).
2. Obtener:
   - `MERCADOPAGO_PUBLIC_KEY`
   - `MERCADOPAGO_ACCESS_TOKEN`
   - `MERCADOPAGO_WEBHOOK_SECRET`
3. Configurar el webhook en el panel de MP apuntando a:
   `https://nuevo-dominio.com/mercadopago/webhook`
4. Agregar las variables al `.env` del nuevo proyecto.

**IMPORTANTE:** Nunca reutilizar las claves de otro proyecto.

---

## Paso F — Crear Servicios/Productos/Eventos

Desde el panel de administración:

1. Ir a **Tours** (o el nombre que se haya configurado).
2. Crear un nuevo servicio con:
   - Título y destino/descripción.
   - Fecha y hora.
   - Precio por persona.
   - Anticipo mínimo (`minimum_deposit`).
   - Total de cupos/recursos disponibles.
   - Horas de expiración para reservas sin pagar.
   - Imagen representativa.
   - Itinerario o descripción detallada.

---

## Paso G — Configurar Cupos/Recursos

El sistema usa un mapa visual de asientos tipo autobús. Para adaptarlo:

- **Si el nuevo negocio usa cupos numerados** (asientos, mesas, estaciones): El mapa funciona tal cual.
- **Si solo se necesita un contador de cupos** (sin asignación visual): Simplificar la vista de checkout eliminando el mapa y dejando solo un selector numérico de cantidad.

Archivo clave: `resources/views/partials/_bus_map.blade.php`

---

## Paso H — Ajustar Documentos Requeridos

1. En la creación del servicio, activar/desactivar "Requiere documentos".
2. El texto actual dice "INE o INAPAM, si cuenta con alguno". Para cambiarlo:
   - Editar `resources/views/admin/tours/form.blade.php` (checkbox de documentos).
   - Editar `resources/views/checkout/success.blade.php` (instrucciones al cliente).

---

## Paso I — Ajustar Categorías de Participantes

Las categorías actuales son: `Adulto`, `Niño`, `Adulto Mayor`.

Para cambiarlas (ej. "Estudiante", "Profesional", "Cortesía"):
1. Editar la vista de checkout donde se muestran los selects de tipo.
2. Actualizar `ReservationService@calculatePassengerPricing()` con las nuevas reglas de descuento.

---

## Paso J — Ajustar Reglas de Descuento

Actualmente: Niños reciben 50% (tours cortos) o 75% (tours largos).

Para cambiar:
1. Editar `app/Services/ReservationService.php` → método `calculatePassengerPricing()`.
2. Implementar la lógica de precios del nuevo negocio.

---

## Paso K — Ajustar PDFs

Los PDFs son plantillas Blade que se renderizan con DomPDF.

1. **Ticket** (`resources/views/pdf/ticket.blade.php`): Comprobante de reserva completo.
2. **Voucher** (`resources/views/pdf/voucher.blade.php`): Recibo individual de pago.

Cambiar:
- Logo y colores (el color primario actual es `#d62828`).
- Textos del header/footer.
- Estructura de la tabla si los campos difieren.

---

## Paso L — Validar Flujo Completo

Antes de lanzar, verificar:

- [ ] Login admin funciona.
- [ ] Dashboard carga.
- [ ] Se puede crear un servicio/evento.
- [ ] El sitio público muestra el catálogo.
- [ ] Se puede hacer una reserva completa.
- [ ] El mapa de cupos/asientos funciona.
- [ ] Los precios se calculan correctamente.
- [ ] Mercado Pago procesa pagos.
- [ ] El webhook recibe y registra pagos.
- [ ] Se puede registrar un pago manual.
- [ ] El ticket PDF se descarga.
- [ ] El voucher PDF se descarga.
- [ ] El botón de WhatsApp funciona.
- [ ] La cancelación de participante funciona.
- [ ] El portal del cliente funciona.
- [ ] Las políticas se muestran correctamente.
- [ ] Los bancos aparecen en el ticket.

---

## Variables de Entorno Requeridas

```
APP_NAME=
APP_URL=
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=
MERCADOPAGO_PUBLIC_KEY=
MERCADOPAGO_ACCESS_TOKEN=
MERCADOPAGO_WEBHOOK_SECRET=
OWNER_NOTIFICATION_EMAIL=
```

**Opcionales (legado Stripe):**
```
STRIPE_KEY=
STRIPE_SECRET=
STRIPE_WEBHOOK_SECRET=
```

---

## Comandos de Arranque

```bash
# Instalar dependencias
composer install

# Copiar y configurar .env
cp .env.example .env
php artisan key:generate

# Crear base de datos y ejecutar migraciones
php artisan migrate

# Sembrar datos iniciales (admin demo)
php artisan db:seed

# Enlazar storage (solo en local, NO en Hostinger)
php artisan storage:link

# Iniciar servidor local
php artisan serve
```
