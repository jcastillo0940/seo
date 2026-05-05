# SEO Tool MVP

MVP en Laravel 13 para conectar Google Search Console, GA4 y Magento, sincronizar keywords de los ultimos 30 dias y disparar auditorias tecnicas con una interfaz Blade zero-NPM.

## Deploy con `public_html`

Este proyecto esta preparado para servidores compartidos donde el document root debe ser `public_html`.

El frontend debe compilarse localmente y luego subirse ya generado:

```bash
npm install
npm run build
```

Eso genera los assets en `public_html/build`.

Sube al servidor:

- `app/`
- `bootstrap/`
- `config/`
- `database/`
- `resources/`
- `routes/`
- `storage/`
- `vendor/`
- `public_html/`
- `artisan`
- `composer.json`
- `composer.lock`

No subas:

- `.env`
- `node_modules/`
- `database/database.sqlite`
- caches temporales de `storage/`

En hosting tipo cPanel:

1. El dominio debe apuntar a `public_html/`.
2. Ejecuta `composer install --no-dev --optimize-autoloader` localmente o en un entorno con suficiente recursos.
3. Ejecuta `php artisan config:clear`
4. Ejecuta `php artisan migrate --force`
5. Ejecuta `php artisan queue:work` o configura un worker/cron equivalente

## Incluye

- Login con Google preparado para `Laravel Socialite`.
- Servicio `GoogleConsoleService` para propiedades y metricas.
- Servicio `GoogleAnalyticsService` para landings organicas en GA4.
- Servicio `PageSpeedService` para score tecnico.
- Servicio `MagentoService` para productos, categorias y CMS pages.
- Servicio `SeoCrawlerService` para hallazgos on-page sobre catalogo y landings.
- Servicio `SerpTrackingService` para snapshots de competencia y posiciones.
- Servicio `SeoOpportunityService` para priorizacion de paginas, keywords y gaps.
- Jobs en cola para ingesta y auditoria.
- Dashboard Blade con Tailwind CDN, Alpine.js y Chart.js.
- Modo demo para recorrer el flujo sin credenciales reales.

## Flujo MVP

1. El usuario entra en `/` y conecta Google.
2. Selecciona una propiedad de Search Console.
3. Se encola la ingesta de metricas de 30 dias en `keyword_metrics`.
4. Se configura Magento para sincronizar catalogo SEO y GA4 para landings organicas.
5. El dashboard muestra Top 10 por clicks, Quick Wins, catalogo y paginas organicas.
6. El boton de auditoria encola una corrida de PageSpeed y guarda el resultado en `technical_audits`.
7. El boton `Run Crawl` recorre las URLs sincronizadas desde Magento y guarda issues en `crawl_runs` y `crawl_pages`.
8. El boton `Run SERP` crea snapshots de ranking para las `tracked_keywords` y estima gaps de competencia.

## Desarrollo local

```bash
composer install
php artisan key:generate
php artisan migrate
php artisan queue:work
php artisan serve
```

## Variables de entorno

Configura estas claves para activar integracion real con Google:

```env
SEO_DEMO_MODE=false
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"
GOOGLE_PAGESPEED_API_KEY=
GOOGLE_ANALYTICS_PROPERTY_ID=
MAGENTO_BASE_URL=
MAGENTO_STORE_CODE=default
MAGENTO_API_TOKEN=
SERP_PROVIDER=demo
SERP_API_KEY=
```

## Nota importante

La instalacion de `laravel/socialite` y `google/apiclient` sigue bloqueada por un problema SSL del entorno Composer de Windows. El proyecto queda funcional en modo demo y el codigo ya esta preparado para usar esos paquetes en cuanto Composer pueda instalarlos.
