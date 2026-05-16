# Reservation System — Reusable Template

Plantilla base reutilizable para sistemas de reservaciones con cupos limitados, pagos parciales, documentos y administración financiera.

Construido con **Laravel**, **Blade**, **MySQL** y **Mercado Pago**.

---

## ¿Para Qué Sirve?

Esta plantilla resuelve el problema de gestionar reservaciones con:
- Cupos/recursos limitados (asientos, plazas, horarios).
- Precios por participante con descuentos configurables.
- Pagos parciales (abonos) y conciliación automática.
- Expiración automática de reservas no pagadas.
- Cancelaciones con retención financiera trazable.
- Generación de comprobantes PDF (ticket y voucher).
- Portal de cliente y panel administrativo.

---

## Tipos de Proyectos Compatibles

- Agencias de viajes y excursiones
- Eventos con boletos y cupo limitado
- Cursos y talleres presenciales
- Academias con inscripciones
- Citas médicas o profesionales con anticipo
- Salones y espacios reservables
- Cualquier servicio que requiera pago inicial, abonos y saldo pendiente

---

## Módulos Incluidos

| Módulo | Estado |
|--------|--------|
| Catálogo de servicios/eventos | ✅ Listo |
| Selector de cupos/recursos (mapa interactivo) | ✅ Listo |
| Motor de reservaciones | ✅ Listo |
| Participantes con precios individuales | ✅ Listo |
| Pagos en línea (Mercado Pago) | ✅ Listo |
| Pagos manuales (transferencia) | ✅ Listo |
| Abonos parciales y saldo pendiente | ✅ Listo |
| Expiración automática de reservas | ✅ Listo |
| Cancelación con retención financiera | ✅ Listo |
| Ajustes financieros trazables | ✅ Listo |
| Comprobante de reserva (Ticket PDF) | ✅ Listo |
| Comprobante de pago (Voucher PDF) | ✅ Listo |
| Portal del cliente (login, historial, documentos) | ✅ Listo |
| Panel administrativo completo | ✅ Listo |
| Configuración de bancos y políticas | ✅ Listo |
| Integración WhatsApp (sin API de pago) | ✅ Listo |
| Notificaciones admin en tiempo real | ✅ Listo |
| Programa de lealtad / bonificaciones | ✅ Listo |
| Banners dinámicos | ✅ Listo |
| Documentos por participante (subida/descarga) | ✅ Listo |
| Comando de limpieza de datos demo | ✅ Listo |

---

## Configuración al Crear Nuevo Proyecto

### 1. Variables de Entorno (`.env`)

```env
APP_NAME="Nombre del Negocio"
APP_URL=https://tu-dominio.com

DB_DATABASE=nombre_bd
DB_USERNAME=usuario
DB_PASSWORD=contraseña

MERCADOPAGO_PUBLIC_KEY=tu_public_key
MERCADOPAGO_ACCESS_TOKEN=tu_access_token
MERCADOPAGO_WEBHOOK_SECRET=tu_webhook_secret

OWNER_NOTIFICATION_EMAIL=admin@tunegocio.com
```

### 2. Identidad Visual
- Reemplazar `public/img/logobymex.jpeg` con el logo del nuevo negocio.
- Editar colores en `public/css/style.css`.
- Editar textos del layout en `resources/views/layouts/public.blade.php`.

### 3. Labels del Sistema
Editar `config/template.php` para cambiar la terminología (Tour→Curso, Pasajero→Alumno, etc.).

### 4. Desde el Panel Admin
- Configurar bancos y cuentas.
- Escribir políticas de reservación y cancelación.
- Configurar WhatsApp de la empresa.
- Crear el primer servicio/evento.

---

## Comandos Útiles

```bash
# Instalar dependencias
composer install

# Configurar
cp .env.example .env
php artisan key:generate

# Base de datos
php artisan migrate
php artisan db:seed

# Storage (solo local)
php artisan storage:link

# Servidor local
php artisan serve

# Limpiar datos demo
php artisan vbymex:clean-test-data --dry-run   # Ver qué se borraría
php artisan vbymex:clean-test-data --force     # Borrar (con backup previo)

# Limpiar cache
php artisan optimize:clear
```

---

## Advertencias Importantes

- **NO** reutilizar credenciales de Mercado Pago de otro proyecto.
- **NO** copiar datos de clientes o pagos reales entre proyectos.
- **NO** reutilizar logo o imágenes sin autorización del dueño original.
- **NO** usar `php artisan migrate:fresh` en producción.
- **NO** usar `php artisan key:generate` en producción con datos existentes.
- Revisar y adaptar las políticas legales para cada empresa.
- Generar nuevas claves de cifrado por proyecto.

---

## Partes que Requieren Revisión por Giro

| Parte | Por Qué |
|-------|---------|
| Tipos de participante | Adulto/Niño/Adulto Mayor son específicos de viajes |
| Reglas de descuento | La lógica 50%/75% por duración es específica |
| Mapa de asientos | Puede no aplicar si solo se necesita un contador de cupos |
| Documentos requeridos | "INE o INAPAM" es específico de México/viajes |
| Puntos de abordaje | Concepto geográfico, puede no aplicar |
| Flota/Autobuses | Módulo informativo específico de transporte |
| Textos hardcodeados | Footer, hero, about, services tienen copy de ByMex |

---

## Documentación Completa

Consulta la carpeta `docs/` para documentación detallada:

- `TEMPLATE_REUSE_ANALYSIS.md` — Análisis de reutilización.
- `PLANTILLA_LOGICA_RESERVAS.md` — Arquitectura lógica y flujos.
- `GUIA_ADAPTACION_NUEVO_PROYECTO.md` — Guía paso a paso.
- `MAPA_MODULOS_REUTILIZABLES.md` — Equivalencias ByMex ↔ Genérico.
- `DESGLOSE_COMPLETO_SISTEMA_VIAJES_BY_MEX.md` — Desglose técnico exhaustivo.

---

## Camino Futuro hacia SaaS

Para convertir esta plantilla en una plataforma SaaS multiempresa, se necesitaría:

1. **Tabla `companies`**: Cada empresa registrada con su configuración.
2. **`company_id` en módulos principales**: tours, reservations, clients, payments, settings.
3. **Usuarios por empresa**: Admins asociados a su empresa.
4. **Configuración aislada**: Cada empresa con sus propios bancos, políticas, logo, WhatsApp.
5. **Subdominios o dominios personalizados**: `empresa1.plataforma.com`.
6. **Planes y suscripciones**: Limitar features por plan (básico, pro, enterprise).
7. **Superadmin global**: Para gestionar empresas y planes.
8. **Aislamiento total de datos**: Middleware que filtre por `company_id` automáticamente.

**Nota:** Esto NO está implementado actualmente. Esta plantilla funciona como sistema single-tenant (una empresa por instalación).
