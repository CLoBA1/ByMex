# Desglose Completo del Sistema: Viajes By Mex

Este documento detalla exhaustivamente la arquitectura, módulos, flujos y lógicas de negocio implementadas en la plataforma "Viajes By Mex". Está redactado de forma clara para que tanto administradores como futuros desarrolladores puedan entender el funcionamiento interno y externo de todo el ecosistema.

---

## 1. Introducción general del sistema

**¿Qué es Viajes By Mex?**
Es una plataforma web integral (sitio público + panel administrativo + portal de clientes) diseñada a la medida para gestionar la venta, reservación y control financiero de tours terrestres.

**¿Qué problema resuelve?**
Digitaliza y automatiza el proceso de reservación de asientos en autobuses, cálculo de precios dinámicos (descuentos por niños), procesamiento de pagos, emisión de tickets y comprobantes, liberando a la empresa de procesos manuales y otorgando una experiencia moderna a sus clientes.

**Usuarios del sistema:**
- **Público general:** Visitantes que buscan tours y realizan reservas.
- **Clientes registrados:** Usuarios que han reservado al menos una vez, con acceso a un portal para ver sus viajes, historial, subir documentos y solicitar bonificaciones.
- **Administrador:** Personal interno que crea tours, aprueba pagos manuales, cancela reservas/pasajeros, atiende WhatsApp y gestiona las finanzas.

**Módulos Principales:**
- **Frontend / Sitio Web:** Catálogo interactivo de viajes.
- **Motor de Reservaciones:** Sistema interactivo (mapa de asientos interactivo).
- **Control Financiero:** Gestión de anticipos, saldos pendientes y pagos a plazos (abonos).
- **Módulo de Bonificaciones (Lealtad):** Sistema para premiar a clientes frecuentes.
- **Portal de Administración:** Panel protegido para gestionar el negocio.

---

## 2. Arquitectura general

**Tecnología Principal:** El backend y el frontend acoplado están construidos con **Laravel** (PHP).

**Cómo fluyen los datos (Arquitectura Mvc):**

*   **Petición Pública:** `Usuario` → `Blade (Vista)` → `Controlador Web` → `ReservationService` → `Modelos (Base de datos)`.
*   **Gestión Administrativa:** `Admin` → `Panel Blade` → `Controlador Admin` → `Modelos` → `Base de datos`.
*   **Gestión de Clientes:** `Cliente` → `Portal Cliente (Auth)` → `Controlador Cliente` → `Modelos`.
*   **Webhooks (Pagos):** `Mercado Pago` → `Controlador API` → `PaymentService` → `Actualización de Reservación`.

**Componentes Clave:**
- **Blade:** Sistema de plantillas de Laravel que renderiza el HTML dinámicamente con estilos personalizados (CSS puro / Bootstrap en el panel).
- **Rutas (Routes):** Archivos donde se registran las URLs que apuntan a sus respectivos Controladores.
- **Controladores (Controllers):** Intermediarios que reciben las acciones del usuario, llaman a los servicios y retornan las vistas.
- **Servicios (Services):** Clases (`PaymentService`, `ReservationService`) que contienen las reglas de negocio pesadas para mantener los controladores limpios.
- **Modelos (Models):** Clases que representan y comunican con las tablas de la base de datos (Eloquent ORM).

---

## 3. Estructura de carpetas importantes

Para encontrar rápidamente cualquier archivo, estas son las ubicaciones principales:

- `routes/web.php`: Contiene casi todas las URLs del sistema (públicas, del admin y portal de cliente).
- `app/Http/Controllers/`:
  - `Web/`: Controladores del sitio público (mostrar tours, iniciar checkout).
  - `Admin/`: Controladores protegidos para gestionar el negocio (Dashboard, Tours, Reservaciones).
  - `Client/`: Controladores para el portal de clientes.
  - `Api/`: Rutas consumidas por Javascript (mapa de asientos y webhooks).
- `app/Models/`: Definiciones de tablas, relaciones (hasMany, belongsTo) y atributos (`fillable`).
- `app/Services/`: Lógica centralizada. Aquí está la "inteligencia" financiera y operativa.
- `database/migrations/`: Archivos PHP que estructuran y construyen la base de datos de manera controlada.
- `resources/views/`:
  - `tours/`: Vistas públicas de los viajes.
  - `checkout/`: Formulario de reservación pública.
  - `admin/`: Todo el panel administrativo (tablas, formularios).
  - `client/`: Panel del portal de clientes.
  - `pdf/`: Plantillas HTML que se transforman en archivos PDF (Tickets y Vouchers).
- `public_html/`: Carpeta pública en producción (CSS, JS e `index.php` punto de entrada).
- `storage/app/public/`: Almacenamiento real de imágenes de tours y comprobantes privados (se enlazan a lo público o se protegen por código).
- `config/services.php`: Dónde se guardan las credenciales de integraciones como Stripe.
- `.env`: Variables de entorno, contraseñas, credenciales de BD y claves de Mercado Pago (NO se versiona en Git).

---

## 4. Sitio público

El sitio público es la "vitrina" de la empresa.
- **Home:** Muestra banners promocionales, "Por qué elegirnos", y un bloque con los viajes próximos a salir.
- **Listado de Tours:** Catálogo de viajes activos mostrados en "Cards" atractivas.
- **Detalle de Tour:** Muestra información profunda de un viaje:
  - Título y galería de imágenes.
  - Fecha y Origen (Puntos de abordaje).
  - Precio por persona y opción de **"Reserva con $X anticipo mínimo"**.
  - Acordeones (Qué incluye, Itinerario, Notas).
  - Políticas de reservación (cargadas dinámicamente desde el Admin).
  - **Botón de Reservar:** Si hay lugares disponibles, redirige al flujo de reservación.

**Vistas Blade:** `resources/views/welcome.blade.php`, `resources/views/tours/show.blade.php`.

---

## 5. Módulo de tours

Es el corazón del inventario. Los tours se crean en el admin (`Admin/TourController`).

**Campos principales:**
- `title`, `destination`: Destino principal.
- `departure_date`: Fecha y hora de salida.
- `price`: Costo base adulto.
- `minimum_deposit`: Monto mínimo requerido para apartar lugar (útil para parcializar el pago).
- `total_seats`: Capacidad del autobús (ej. 40 asientos).
- `requires_passenger_documents`: Booleano para forzar a los pasajeros a subir foto de su INE/INAPAM.
- `status`: Activo, Inactivo, o Completado.
- `expiration_hours`: Horas de gracia que se da a los clientes que eligen "Pago Manual" antes de cancelarles el asiento automáticamente.

**Relaciones:** Un tour `hasMany` (tiene muchas) Reservaciones y Asientos.

---

## 6. Selección de asientos

La asignación de lugares en el autobús.

**El mapa:** Se dibuja dinámicamente con JavaScript en la vista de checkout, calculando la distribución en base a un autobús estándar.
- **Disponible (Blanco/Gris):** Se puede seleccionar.
- **Apartado/Pendiente (Naranja):** Alguien seleccionó el asiento y está llenando sus datos, o eligió pago manual y aún no deposita.
- **Ocupado/Pagado (Verde oscuro):** Pagado y confirmado.

**API:** `GET /api/seats/{tourId}`. El JS hace llamadas asíncronas para refrescar el mapa en tiempo real (`Api\SeatController`).

**Lazy Cleanup:** Para evitar que un usuario "abandone" la pantalla y deje bloqueados asientos por siempre, el sistema corre una validación que libera asientos vencidos silenciosamente.

---

## 7. Flujo completo de reservación pública

1. **Usuario entra al detalle del tour** y da clic en Reservar.
2. **Aceptación de Políticas:** Antes de acceder, debe marcar una casilla indicando que leyó los términos.
3. **Selección de Asientos:** Elige `n` asientos en el mapa.
4. **Datos del Titular:** Rellena su Nombre, Email, Teléfono.
5. **Datos de Pasajeros:** Por cada asiento, asigna el nombre del pasajero y su tipo (Adulto, Niño, Adulto Mayor).
6. **Selección de Punto de Abordaje:** Si el tour lo permite, elige en qué ciudad y lugar se subirá.
7. **Creación de Reserva:** El controlador (`Web\ReservationController`) crea los registros en BD, y genera un `public_token` seguro para acceder al resumen.
8. **Pantalla de Confirmación:** Se muestra el total a pagar, descuentos aplicados y las opciones de pago (Mercado Pago o Manual/Transferencia).
9. **Pago:** Si paga en línea, la reserva pasa a completada; si sube comprobante, pasa a revisión por el admin.
10. **Botón WhatsApp:** Puede hacer clic en el botón para avisar por WhatsApp a la agencia.

---

## 8. Pasajeros

La tabla `reservation_passengers` guarda la vida de cada individuo que viaja.
- **Datos:** Nombre, Teléfono, Punto de Abordaje.
- **Categorías y Descuentos:** El sistema aplica automáticamente un descuento configurado (ej. 10%) si el tipo es `Niño`. Adultos y Adultos Mayores no reciben descuento automático por código, para mantener márgenes operativos.
- **Documentos:** Si el tour lo requiere, el portal solicitará "INE o INAPAM, si cuenta con alguno". Se suben y se ligan a esta tabla.
- **Cancelación:** Si el administrador cancela a un pasajero, no lo elimina físicamente. Modifica su estado, libera el asiento asociado en `reservation_seats` y llena los campos: `cancelled_at`, `cancellation_reason`, y `cancellation_retained_amount` (Penalidad financiera).

---

## 9. Resumen financiero de reservación

El dinero se calcula con exactitud matemática y trazabilidad histórica. Nunca se pierde rastro del dinero pagado.

- `subtotal`: Costo original sin modificaciones.
- `discount_amount`: Descuento global (ej. por niños).
- `total_amount`: Total real esperado por la empresa.
- **Pagado Acumulado:** Suma de todos los registros válidos en la tabla `payments`.
- **Balance Pendiente (`balance_due`):** El saldo que resta por liquidar.

**Nueva Lógica de Ajustes Financieros:**
Cuando se cancela un pasajero, **NO se borra el pago ni el asiento pagado**.
Se aplica la fórmula:
`total_ajustado = suma (precio final de pasajeros activos) + retenciones (penalidades) - devoluciones`.
`balance_due = max(0, total_ajustado - pagado_acumulado)`.

Esto evita que si un cliente pagó $1,000 y se cancela a un pasajero, la reservación quede debiendo dinero fantasma. El administrador puede decir: "Te cancelo el pasajero, pero retengo $200 como penalidad". La reserva queda clara: El cliente ya pagó $1000, su nuevo total es $800 (pasajeros restantes) + $200 (penalidad) = $1000. Balance pendiente: $0.

---

## 10. Cancelación de pasajeros

Acción disponible solo en el Admin.
1. Abre un **Modal** en el detalle de la reservación.
2. Ingresa el **Motivo de cancelación** y el **Monto de retención/penalización**.
3. El sistema:
   - Marca al pasajero como `cancelled`.
   - Crea un registro de ajuste (`ReservationAdjustment`) para justificar financieramente el dinero retenido.
   - Libera instantáneamente el asiento para que otra persona lo compre.
4. El pasajero cancelado sigue viéndose en el historial tachado y sin asiento.

---

## 11. Pagos

La tabla `payments` gestiona los abonos. Cada pago es una transacción que tiene un "status".
- **Pagos Manuales:** El cliente indica que hizo transferencia y sube una foto (comprobante). El admin debe revisarlo y cambiar el status a `approved`.
- **Abonos (Pago en Plazos):** El cliente puede hacer abonos parciales usando su token público, acercándose progresivamente al pago total.
- **Vouchers:** Por cada pago `approved`, el sistema permite descargar un "Voucher" o recibo de caja individual en formato PDF.

---

## 12. Mercado Pago

Es la pasarela de pagos primaria oficial y activa.

- **Checkout Directo:** Muestra el widget nativo (Preference) en el formulario de la reserva.
- **Webhook:** Mercado Pago informa asíncronamente cuando el banco aprobó un pago en la ruta secreta `POST /mercadopago/webhook`.
- **Idempotencia:** Cada notificación se filtra revisando si el `mp_payment_id` ya fue procesado para evitar duplicar el dinero del cliente.
- **Seguridad:** Utiliza verificación de firma HMAC-SHA256 comparando contra `MERCADOPAGO_WEBHOOK_SECRET`. Notificaciones con firma falsa son rechazadas (Código 403).
- **Proceso:** Si es exitoso, el controlador llama al `PaymentService` que genera el registro de `payments` tipo 'mercadopago' con status `approved` y actualiza la reserva.

---

## 13. Stripe legacy

**Estado actual:** Inactivo / Legado.
El código contiene infraestructura y configuraciones (SDK, Webhooks, claves) para procesar pagos en dólares o tarjetas internacionales con Stripe. Sin embargo, no está habilitado en las vistas actuales para evitar confusión y centrar el negocio en moneda local a través de Mercado Pago.
**Recomendación:** No tocar ni modificar estas rutas/archivos, ya que sirven como respaldo (fallback) inmediato en caso de que la agencia desee expandirse.

---

## 14. PDFs

El sistema genera documentos listos para impresión usando la librería DomPDF (renderiza Blade HTML a PDF).
- **Ticket PDF:** El "Pase de abordar" del cliente. Contiene un código QR (simbólico/estético), el origen, destino, y lista de pasajeros con sus asientos confirmados.
- **Voucher PDF:** Un recibo fiscal/interno que demuestra un pago aislado ("Abono de $500 MXN recibido").
- **Rutas seguras:** Los PDFs públicos se protegen usando el `public_token` de la reservación (ej. `/reservations/public/{token}/ticket-pdf`).

---

## 15. WhatsApp

El sistema tiene dos integraciones directas con WhatsApp (sin usar APIs de paga de Facebook).
- **A) Cliente → Empresa:** En la confirmación pública hay un botón "Notificar a la empresa". Usa la URL `wa.me/{numero_empresa}` enviando un texto automático con el ID de su reserva.
- **B) Admin → Cliente:** En el panel de control, existe un botón "Enviar ticket por WhatsApp". Al hacer clic, abre WhatsApp Web/Desktop hacia el número del cliente con un mensaje de cortesía pre-cargado y un enlace seguro al PDF del ticket. **Nota:** No se pueden adjuntar PDFs automáticamente mediante la URL gratuita de `wa.me`, por ello el link es la solución perfecta.

---

## 16. Portal cliente

Usuarios regulares obtienen un portal privado (Dashboard) después de crear su cuenta.
- **Viajes Próximos/Pasados:** Listados organizados de sus reservaciones.
- **Sección de Reservación:** Pueden ver su estatus de pago, subir documentos (INE), descargar Tickets o Vouchers.
- **Membresías y Lealtad:** Muestra cuántos viajes válidos han realizado y botones para solicitar bonos si alcanzan los requisitos.

---

## 17. Admin clientes

En el panel de control, los clientes reales (`clients` table) no son simples filas de Excel.
- Tienen un historial y un contador de viajes acumulados.
- Se pueden editar sus detalles.
- El admin puede aprobar "Bonificaciones" manualmente e inclusive forzar su nivel de membresía o viajes válidos acumulados como atención especial.

---

## 18. Bonificaciones

El programa de lealtad de la empresa.
- **Regla:** Un viaje se considera "Válido" solo si la reservación de la que forma parte está pagada y finalizada.
- **Flujo:** Cuando el cliente llega a la meta, da clic en "Solicitar Bonificación" en su portal.
- Se crea una `bonus_request` en estado `pending`.
- El administrador recibe la alerta, valida la petición, y puede cambiar el estado a `approved`.
- El cliente tiene un saldo virtual que podrá aplicar como descuento especial.

---

## 19. Flota / Nosotros

Módulo informativo dinámico y administrable.
El administrador sube fotos e información de las unidades de transporte (autobuses/vans) que posee la empresa. Estas imágenes y textos se reflejan en el sitio público en la pestaña "Flota/Nosotros" dándole confianza y profesionalismo al público.

---

## 20. Configuración del sistema

En el menú "Configuración" del panel administrativo existen tablas vitales:
- **`payment_settings`**: Guarda el teléfono oficial de WhatsApp de la agencia, y bloques de texto largo (Políticas de reservación, instrucciones generales para depósitos y notas en el ticket).
- **`bank_accounts`**: Permite dar de alta dinámicamente bancos y números de cuenta CLABE que se muestran a los clientes que escogen método "Transferencia".

Esto evita que los desarrolladores tengan que intervenir en el código fuente cada vez que la empresa cambia de tarjeta para recibir pagos.

---

## 21. Puntos de abordaje

Tablas `boarding_points` (ciudades, ej. "Chihuahua") y `boarding_sub_points` (ubicaciones exactas, ej. "Alsuper Universidad").
Se utilizan durante el checkout. En lugar de campos de texto libre que arruinan la base de datos, el cliente debe escoger de listas desplegables. Facilita la logística de la agencia al saber exactamente por dónde pasar por la gente.

---

## 22. Seguridad

- **Tokens públicos (`public_token`):** Las reservaciones no usan IDs secuenciales (ej. `id=5`) en las URLs para evitar Enumeración (que un usuario cambie el 5 por el 6 y vea los datos de otro). Usan hashes de 32 a 40 caracteres criptográficamente seguros.
- **CSRF:** Protección nativa de Laravel contra envío de formularios maliciosos cruzados. Solo exceptuado deliberadamente en el webhook de Mercado Pago.
- **Protección de index.php:** Hostinger ejecuta el código a través de `public_html/index.php`. Esto esconde el framework, `.env` y el código fuente de los navegadores públicos, aislando el ecosistema en `domains/vbymex.com/laravel`.

---

## 23. Storage e imágenes

- **Almacenamiento Local Seguro:** Documentos confidenciales (INE, comprobantes de pago) se guardan en el disco interno de Laravel (`storage/app/private` o `public` pero no enlazado con symlinks).
- **El reto de Hostinger:** Como en Hostinger Shared Hosting no se pueden correr libremente comandos como `php artisan storage:link`, el proyecto usa una aproximación de crear una URL dinámica que carga y entrega la imagen bajo demanda o mueve los assets al `public_html/storage` de forma ruteada.
- **Regla:** Nunca se deben borrar o alterar estas rutas sin conocimiento previo.

---

## 24. Producción / Hostinger

**Estructura del servidor real:**
- Dominio público: `https://vbymex.com`
- Código de la app (Backend protegido): `/home/u707366501/domains/vbymex.com/laravel`
- Directorio expuesto a internet (Frontend): `/home/u707366501/domains/vbymex.com/public_html`

En `public_html/index.php` existe un enlace vital (`$app->usePublicPath(__DIR__);`) que acopla el código seguro con la cara expuesta.

**Comandos seguros en producción:**
- `php artisan optimize:clear` (Para limpiar la memoria caché si los cambios no se ven).
- **Prohibido:** `php artisan key:generate` (Desconectaría todas las cookies y datos encriptados), `php artisan tinker` (Riesgo de modificar datos accidentalmente).

---

## 25. GitHub y despliegue

El flujo de entrega y publicación continuo debe ser **estrictamente de una vía**.
1. El desarrollador o agente AI hace cambios en el entorno local (Windows/XAMPP).
2. Se hace un "commit" y se empuja a GitHub (`git push origin main`).
3. El administrador entra a Hostinger vía SSH y corre `git pull origin main`.

**PRECAUCIONES:**
- NUNCA correr `git add .` en Hostinger o de lo contrario el servidor podría sobrescribirse y romper el historial.
- Hostinger es un ambiente pasivo de lectura de código.

---

## 26. Limpieza de base de datos

Se construyó una herramienta temporal para borrar todos los registros falsos antes del lanzamiento real, sin destruir la configuración.
- **Comando:** `php artisan vbymex:clean-test-data`
- **Tablas que borra:** Clientes, reservaciones, pasajeros, asientos, pagos, y notificaciones.
- **Tablas que conserva:** Tours reales, bancos, puntos de abordaje, admins.
- **Modos:**
  - `--dry-run`: No borra nada, solo hace simulacro de conteos.
  - `--force`: Trunca las tablas, borra la data permanentemente y resetea los IDs a 1.
- **Recomendación:** Eliminar la clase de este comando del código fuente una vez que el negocio inicie operaciones formales.

---

## 27. Tablas principales de base de datos

Resumen de la anatomía de los datos:

- **`users`**: Administradores del sistema.
- **`clients`**: Usuarios públicos/pasajeros recurrentes. (Relación `hasMany` reservations).
- **`tours`**: Catálogo de viajes.
- **`reservations`**: Agrupación financiera. Unifica al titular, el tour y el costo final.
- **`reservation_passengers`**: Detalle nominal de las personas que viajan. (Hijos directos de una reservation).
- **`reservation_seats`**: Registro de a qué pasajero y reservación le pertenece el número de butaca.
- **`payments`**: Recibos de dinero reales (transferencias aprobadas y transacciones Mercado Pago).
- **`reservation_adjustments`**: Penalizaciones y ajustes monetarios por cancelar a una persona.
- **`passenger_documents`**: Las fotos de la INE.
- **`payment_settings`**: Textos y teléfonos. Un solo registro maestro.
- **`bank_accounts`**: Cuentas CLABE para transferencia manual.
- **`boarding_points`** / **`boarding_sub_points`**: Orígenes estructurados.
- **`bonus_requests`**: Trámites del portal de lealtad.
- **`admin_notifications`**: Alertas que le avisan al admin cuando le llega dinero o solicitudes.
- **`buses` / `bus_images`**: Las camionetas mostradas en la sección flota.

---

## 28. Rutas importantes

- **Público:**
  - `GET /` (Inicio)
  - `GET /viajes/{id}` (Detalle del tour)
  - `GET /viajes/{id}/reservar` (Inicia el checkout)
  - `POST /viajes/{id}/reservar` (Procesa el formulario público)
  - `GET /reservations/public/{token}` (Pantalla de resumen y éxito)
- **Mercado Pago:**
  - `POST /mercadopago/webhook` (El banco avisa que hay un pago válido).
- **Admin:**
  - `GET /admin/dashboard` (Inicio)
  - `GET /admin/reservations/{id}` (Gestionar una reserva, cancelar pasajeros)
- **API (JS):**
  - `GET /api/seats/{tourId}` (Carga de colores para el mapa del autobús).
- **PDFs:**
  - `GET /reservations/public/{token}/ticket-pdf`

---

## 29. Controladores importantes

- `Web/ReservationController`: Crea la reserva a partir de lo que el cliente tecleó en la vista, aplica descuentos para niños, calcula totales.
- `Admin/ReservationController`: Donde la agencia aprueba pagos manuales y efectúa los procesos destructivos o de control (cancelar pasajeros).
- `Admin/TourController`: CRUD del inventario. Administra las imágenes y campos.
- `Api/MercadoPagoWebhookController`: Capa de seguridad y recepción automática para Mercado Pago.
- `Api/SeatController`: Regresa en JSON la estructura actual del autobús para pintar el mapa dinámico.

---

## 30. Servicios importantes

- **`ReservationService`**: Contiene la función que todo lo amarra: `recalculateReservation($reservation)`. Suma los costos, resta descuentos, suma penalizaciones y compara con lo abonado para actualizar el estado (`pending`, `paid`, o `partially_paid`).
- **`PaymentService`**: Abstracción teórica para centralizar pasarelas (Stripe, Transferencias). Llama al recalculador financiero tras aprobar un abono.

---

## 31. Procesos principales paso a paso

### A) Pagar con Mercado Pago
1. Cliente da clic en el botón nativo de Mercado Pago tras hacer una reserva.
2. Ingresa su tarjeta, aprueba el pago en el widget.
3. Mercado Pago redirige al cliente a la página de éxito.
4. En paralelo, en background, MP lanza un POST al Webhook de Laravel.
5. El Webhook verifica la firma HMAC, extrae el monto, verifica que no sea duplicado.
6. Si es válido, inserta en `payments`, estado `approved`.
7. Laravel lanza recalculador. Si pagado >= total_esperado, la reserva se marca `status = 'paid'`.
8. El admin recibe notificación.

### B) Cancelar pasajero (Flujo Financiero)
1. Admin abre detalle de reserva, ve pasajero "José". Da clic en Cancelar.
2. Escribe: "Motivo: Enfermedad. Retención: $300".
3. Laravel libera el asiento 15.
4. Laravel inactiva a "José".
5. Laravel inyecta $300 en `reservation_adjustments`.
6. Al recalcular: Se resta el pasaje completo de José, pero se suma la penalidad de $300.
7. El balance financiero cuadró correctamente, la empresa retuvo dinero real y el asiento quedó libre de nuevo.

---

## 32. Estado actual del sistema

- **Súper Estable y Funcional:** Todo el núcleo transaccional (Reservas, Selección interactiva, Base de Datos y Webhooks) fue finalizado y corregido de bugs lógicos severos y problemas de finanzas.
- **Cuidado con:** El ambiente compartido de Hostinger (`public_html` vs `laravel`). Su naturaleza de hosting compartido requiere ejecutar el comando artisan con rutas limpias y no sobrescribir el archivo `index.php` con flujos crudos de Git.
- **Pruebas finales recomendadas antes del lanzamiento masivo:** Recomiendo agendar dos reservas y cancelarlas desde diferentes correos en Producción, usando tarjetas de prueba o transacciones manuales.

---

## 33. Pendientes o recomendaciones

- **Opcional (Baja prioridad):** Remover/Limpiar código y vistas sobrantes de la integración Legacy de Stripe si se confirma 100% que la empresa jamás volverá a facturar fuera de Mercado Pago. (Para no contaminar el código).
- **Recomendado:** Borrar el archivo `CleanTestData.php` de la consola de comandos una vez que el sistema esté en el mercado y facturando reales, para evitar tragedias por error de dedo de futuros desarrolladores.

---

## 34. Glosario

- **Reservación:** El expediente financiero. Dueño del dinero y agrupa a la gente que viaja junta.
- **Pasajero:** El individuo físico con nombre y apellido.
- **Asiento:** La butaca física en el autobús. (Puede tener estado Disponible, Pendiente, Ocupado).
- **Abono:** Dinero recibido que no cubre el total de la reservación (pago en plazos).
- **Saldo pendiente (Balance Due):** Lo que falta por cobrarle al cliente para que pueda subirse al viaje.
- **Retención / Penalización:** Castigo financiero justificado por cancelar lugares de último momento.
- **Voucher:** Recibo que avala un pago individual / abono.
- **Ticket:** Boleto / Pase de abordar de toda la reservación, que contiene todos los nombres, los asientos confirmados y un código QR.
- **Token Público (`public_token`):** Cadena de texto larguísima e indescifrable que funciona como llave secreta para que el cliente pueda abrir su portal de ticket.
- **Webhook:** "Teléfono rojo". Ruta donde Mercado Pago nos avisa, de robot a robot, que el cliente ya depositó dinero.
- **Idempotencia:** Regla de oro en pagos. Asegurarse técnica y matemáticamente que un abono o evento de red repetido no duplique dinero mágicamente.

---
*Documento autogenerado en Análisis Forense y Mapeo de Arquitectura. Mayo 2026.*
