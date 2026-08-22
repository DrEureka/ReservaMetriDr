# ReservaMetriDr

Sistema de reservas de mesas para restaurante. Permite a clientes registrar
reservas online y al admin gestionarlas, con notificaciones por mail y
consulta de disponibilidad con cache Redis.

**Stack:** Laravel 12 · PHP 8.3 · MySQL · Redis · Blade + Tailwind · Breeze

## Funcionalidades

- **ABM de mesas** con ubicación (A, B, C o D), número correlativo y capacidad.
- **Roles** admin / cliente con middleware que protege las rutas sensibles.
- **Solicitud de reserva** con disponibilidad en tiempo real:
  - Asignación automática de ubicación (primera que tenga mesas libres).
  - Mesas consecutivas correlativas (A-1 + A-2 + A-3) cubriendo la cantidad de
    personas, hasta un máximo de 3 mesas por reserva.
  - Cache en Redis con TTL hasta fin del día, invalidación al crear/cancelar.
  - Lock atómico con `SET NX EX` para evitar doble booking en concurrencia.
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
- **i18n español + inglés** con cambio de idioma via `?lang=es|en` o cookie.
- Formato argentino para fechas (`dd/MM/yy`) y moneda (`$ 1.234,56`).

## Requisitos

- PHP 8.3 o superior con extensiones `pdo_mysql`, `mbstring`, `openssl`, `intl`.
- Composer 2.x.
- Node 18+ y npm (para assets de frontend).
- MySQL 8 / MariaDB 10.4+ (probado con MariaDB 10).
- Redis 6+ o compatible (probado con Upstash vía TLS).
- Servidor de mail SMTP (probado con Heliohost via `smtp_relaja` driver).

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

### Redis (Upstash con TLS como ejemplo)

```env
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
estricta del cert sin perder la del propio cert.

Si tu cert es válido, podés usar `MAIL_MAILER=smtp` (driver estándar de Laravel).

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
│   │   │   ├── ListadoController.php       (consigna 4: listado por fecha)
│   │   │   └── MesaController.php          (consigna 1: ABM)
│   │   ├── Api/HorariosController.php      (slots AJAX para form)
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
└── Services/
    ├── AsignacionUbicacionService.php       (greedy A→B→C→D con corridas consecutivas)
    ├── DisponibilidadService.php            (cache Redis + cálculo de overlap)
    └── HorarioService.php                   (reglas de horario por día)
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
```

## Tests

Cada parte del plan fue validada con scripts PHP independientes durante el
desarrollo. Para correr la suite formal (si se agregan tests PHPUnit/Pest):

```bash
php artisan test
```

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
