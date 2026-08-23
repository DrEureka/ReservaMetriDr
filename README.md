# ReservaMetriDr

Sistema de reservas de mesas para restaurante. Permite a clientes registrar
reservas online y al admin gestionarlas, con notificaciones por mail,
consulta de disponibilidad con cache Upstash REST, y protección anti-bot.

**Stack:** Laravel 12 · PHP 8.3 · MySQL · Upstash REST · Blade + Tailwind · Breeze · Alpine.js

## Funcionalidades

- **ABM de mesas** con ubicación (A, B, C o D), número correlativo y capacidad.
- **Roles** admin / cliente con middleware que protege las rutas sensibles.
- **Solicitud de reserva** con disponibilidad en tiempo real:
  - Asignación automática de ubicación (primera que tenga mesas libres).
  - Mesas consecutivas correlativas (A-1 + A-2 + A-3) cubriendo la cantidad de
    personas, hasta un máximo de 3 mesas por reserva.
  - Cache Upstash REST (HTTPS/443) con TTL hasta fin del día, invalidación al crear/cancelar.
  - Fallback a database si Upstash falla.
  - Lock atómico con `Cache::add` para evitar doble booking en concurrencia.
- **Horarios** por día de la semana (configurables):
  - Lunes a viernes: 10:00 a 24:00
  - Sábado: 22:00 a 02:00 (cruza medianoche)
  - Domingo: 12:00 a 16:00
  - Duración fija de 2 horas, anticipación mínima de 15 minutos.
- **Cancelación** por el cliente vía link firmado (válido 7 días, enviado por mail),
  o por el admin desde el listado.
- **Listado admin por fecha** con **una sola consulta SQL** que usa
  `GROUP_CONCAT` para traer las mesas concatenadas, agrupado por ubicación y
  turno (mañana / tarde / noche).
- **Cloudflare Turnstile** anti-bot en registro, login y reserva de invitados.
- **Dark / Light mode** con persistencia en localStorage y respeto de `prefers-color-scheme`.
- **SweetAlert2** para confirmaciones (eliminar mesa, cancelar reserva).
- **Emails HTML** con diseño responsive light (confirmación y cancelación).
- **i18n español + inglés** con cambio de idioma via `?lang=es|en` o cookie.
- Formato argentino para fechas (`dd/MM/yy`) y moneda (`$ 1.234,56`).

## Requisitos

- PHP 8.3 o superior con extensiones `pdo_mysql`, `mbstring`, `openssl`, `intl`.
- Composer 2.x.
- Node 18+ y npm (para assets de frontend).
- MySQL 8 / MariaDB 10.4+ (probado con MariaDB 10).
- Upstash Redis (REST API vía HTTPS, probado en Heliohost que bloquea TCP/6379).
- Servidor de mail SMTP (probado con Heliohost via `smtp_relaja` driver, límite 50/hora).
- Cloudflare Turnstile (site key + secret).

## Instalación

```bash
git clone https://github.com/DrEureka/ReservaMetriDr.git
cd ReservaMetriDr
composer install
npm install
npm run build
cp .env.example .env
php artisan key:generate
```

Configurar `.env` con credenciales reales (ver sección siguiente), luego:

```bash
php artisan migrate
php artisan db:seed --class=AdminSeeder
```

Para correr el servidor de desarrollo:

```bash
php artisan serve
npm run dev   # en otra terminal, para Vite + hot reload
```

> **Admin inicial** creado por `AdminSeeder`: email configurable via
> `AdminSeeder` (password inicial `Cambiar1234`, **cambiar antes de pasar a
> producción**).

## Configuración de `.env`

Copiá `.env.example` a `.env` y completá las variables. **No commitear `.env`**
— ya está cubierto por `.gitignore`. `.env.example` solo trae placeholders.

### MySQL

```env
DB_CONNECTION=mysql
DB_HOST=tu-host-mysql.example.com
DB_PORT=3306
DB_DATABASE=tu_base_de_datos
DB_USERNAME=tu_usuario
DB_PASSWORD="tu_password"   # comillas dobles si tiene caracteres especiales (#, ^, $)
```

### Cache — Upstash REST

En hosts que bloquean TCP/6379 (como Heliohost), se usa la REST API de Upstash
(vía HTTPS/443). Driver custom `upstash-rest` registrado en `AppServiceProvider`.

```env
CACHE_STORE=upstash-rest

UPSTASH_REDIS_REST_URL="https://tu-base.upstash.io"
UPSTASH_REDIS_REST_TOKEN="tu_token"
```

Si TCP funciona, usar Redis nativo:

```env
CACHE_STORE=redis
REDIS_CLIENT=predis
REDIS_HOST=tu-host.upstash.io
REDIS_PORT=6379
REDIS_PASSWORD=tu_password_redis
REDIS_SCHEME=tls
REDIS_URL="tls://default:tu_password_redis@tu-host.upstash.io:6379"
```

### Mail

Por defecto se usa el driver `log` (los mails van a `storage/logs/laravel.log`)
para desarrollo. En producción, configurar SMTP:

```env
MAIL_MAILER=smtp_relaja
MAIL_HOST=tu-mail-host.example.com
MAIL_PORT=465
MAIL_USERNAME="tu_usuario@tu-dominio.com"
MAIL_PASSWORD="tu_password_smtp"
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS="noreply@tu-dominio.com"
MAIL_FROM_NAME="${APP_NAME}"
```

Si tu servidor de mail tiene un cert SSL cuyo CN no matchea el dominio del mail
(por ejemplo, hosting compartido), usar el driver `smtp_relaja` que está
registrado en `AppServiceProvider`. Este driver desactiva la verificación
estRICTA del cert sin perder la del propio cert.

> **Nota:** Heliohost tiene un límite de ~50 emails/hora. Los envíos están
> envueltos en try-catch para que la reserva se registre aunque falle el SMTP.

### Cloudflare Turnstile

```env
TURNSTILE_SITE_KEY=tu_site_key
TURNSTILE_SECRET=tu_secret
```

### Locales

```env
APP_LOCALE=es
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=es_AR
APP_TIMEZONE=America/Argentina/Buenos_Aires
```

## Estructura del proyecto

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/
│   │   │   ├── ListadoController.php       (listado por fecha)
│   │   │   └── MesaController.php          (ABM de mesas)
│   │   ├── Api/HorariosController.php      (slots AJAX para form)
│   │   ├── Auth/
│   │   │   ├── AuthenticatedSessionController.php (login + Turnstile)
│   │   │   └── RegisteredUserController.php       (registro + Turnstile)
│   │   ├── ReservaController.php           (crear reserva + mis-reservas)
│   │   └── ReservaCancelacionController.php (cancelar via URL firmada o admin)
│   ├── Middleware/
│   │   ├── SetLocale.php                   (lee ?lang=, cookie, Accept-Language)
│   │   └── VerificarAdmin.php              (gate admin)
│   └── Requests/
│       ├── StoreMesaRequest.php
│       └── StoreReservaRequest.php
├── Mail/
│   ├── ReservaConfirmada.php
│   └── ReservaCancelada.php
├── Models/
│   ├── Mesa.php
│   ├── Reserva.php
│   └── User.php                             (con role admin/cliente)
├── Services/
│   ├── AsignacionUbicacionService.php       (greedy A→B→C→D con corridas consecutivas)
│   ├── DisponibilidadService.php            (cache Upstash + cálculo de overlap)
│   └── HorarioService.php                   (reglas de horario por día)
└── Support/
    ├── RedisStatus.php                     (helper de disponibilidad Redis)
    └── UpstashRestStore.php                (driver cache vía HTTPS)
```

### Base de datos

```
mesas           (id, ubicacion [A,B,C,D], numero, capacidad)
reservas        (id, user_id, fecha, hora_inicio, hora_fin, ubicacion,
                 cantidad_personas, estado, cancelada_at)
reserva_mesa    (id, reserva_id, mesa_id)  pivot
users           (id, name, email, role [admin|cliente], ...)
sessions, jobs, cache, password_resets   (Breeze)
```

## Comandos artisan útiles

```bash
# Ver la asignación propuesta para una fecha/hora/cantidad
php artisan reservas:disponibilidad 2026-08-25 20:00 4

# Ver mesas libres de una ubicación específica
php artisan reservas:disponibilidad 2026-08-25 20:00 4 --ubicacion=A

# Re-sembrar el admin (password inicial Cambiar1234)
php artisan db:seed --class=AdminSeeder

# Limpiar cache (si no hay consola en hosting)
php artisan optimize:clear
```

## Tests

Tests de autenticación y perfil provistos por Breeze:

```bash
php artisan test
```

Tests incluidos:
- `tests/Feature/Auth/AuthenticationTest.php` — login/logout
- `tests/Feature/Auth/RegistrationTest.php` — registro
- `tests/Feature/Auth/PasswordResetTest.php` — reset de contraseña
- `tests/Feature/Auth/PasswordUpdateTest.php` — cambio de contraseña
- `tests/Feature/Auth/PasswordConfirmationTest.php` — confirmar contraseña
- `tests/Feature/Auth/EmailVerificationTest.php` — verificación de email
- `tests/Feature/ProfileTest.php` — edición de perfil

## Horarios y reglas

Configurables en `config/reservas.php`:

```php
'horarios_por_dia' => [
    1 => ['inicio' => '10:00', 'fin' => '24:00', 'cruza_medianoche' => false], // lunes
    2 => ['inicio' => '10:00', 'fin' => '24:00', 'cruza_medianoche' => false],
    3 => ['inicio' => '10:00', 'fin' => '24:00', 'cruza_medianoche' => false],
    4 => ['inicio' => '10:00', 'fin' => '24:00', 'cruza_medianoche' => false],
    5 => ['inicio' => '10:00', 'fin' => '24:00', 'cruza_medianoche' => false], // viernes
    6 => ['inicio' => '22:00', 'fin' => '02:00', 'cruza_medianoche' => true],  // sábado
    0 => ['inicio' => '12:00', 'fin' => '16:00', 'cruza_medianoche' => false], // domingo
],
'duracion_minutos' => 120,
'anticipacion_minima_minutos' => 15,
'max_mesas_por_reserva' => 3,
```

## Git workflow

- `main` siempre estable.
- Cada feature se desarrolla en `feature/<nombre>` y se mergea con `--no-ff`
  (preserva el grafo de features).
- Commits atómicos, mensajes en español (conventional commits).

## Licencia

MIT.
