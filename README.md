# PRCW Theme — Puerto Rico Cocktail Week

Theme FSE Gutenberg + ACF Blocks. Migrado de Ubuntu a **Windows nativo** — este README cubre setup cross-OS.

## Requisitos Windows

| Herramienta | Versión | Notas |
|-------------|---------|-------|
| Node.js | 20 LTS (v24 funciona, probado) | Usa `nvm-windows` |
| npm | 11.x | |
| PHP | 8.2 | Para `wp-env` viene en Docker; para nativo usa Laragon/XAMPP/LocalWP |
| Docker Desktop + WSL2 | Último | **Solo si usas `wp-env`** (ver abajo) |
| Git | Último | Con `core.autocrlf=true` OK — `.gitattributes` fuerza `LF` |

### Fix PowerShell ExecutionPolicy (obligatorio para `npm run build`)

`wp-scripts` falla con `no se reconoce como comando` en PowerShell 5.1 porque bloquea `.ps1`:

```powershell
Set-ExecutionPolicy -Scope CurrentUser RemoteSigned
# verificar
Get-ExecutionPolicy -Scope CurrentUser
```

Alternativa sin cambiar policy:

```powershell
cmd /c "npm run build"
# o directo
node node_modules/@wordpress/scripts/bin/wp-scripts.js build
```

## Instalación

```powershell
cd wp-content/themes/prcw-theme
npm install
node node_modules/@wordpress/scripts/bin/wp-scripts.js build
# o npm run build (si ExecutionPolicy ya está en RemoteSigned)
```

## Entornos: `wp-env` (Docker) vs Nativo

### Opción A — `wp-env` (recomendado, paridad con Ubuntu)

Requiere Docker Desktop + WSL2 backend habilitado. Levanta WordPress 6.8 + PHP 8.2 + MySQL en contenedores Linux aunque estés en Windows.

```powershell
# en la raíz del proyecto (donde está .wp-env.json)
npx wp-env start   # primera vez tarda 2-4 min (descarga core)
npx wp-env stop
```

URL: `http://localhost:8888` | Admin: `http://localhost:8888/wp-admin` (user `admin` / pass `password` por defecto `wp-env`)

> Puerto 8888 puede chocar con Hyper-V/IIS. Si falla, cambia `"port": 8889` en `.wp-env.json`.

**Secrets:** No pongas la key real en `.wp-env.json` (se commitea). Crea `.wp-env.override.json` (gitignored):

```json
{
  "config": {
    "PRCW_LOCATIONIQ_API_KEY": "pk.TU_KEY_REAL_AQUI"
  }
}
```

### Opción B — Nativo Windows (Laragon / LocalWP / XAMPP)

1. Instala WP manualmente en `C:\laragon\www\prcw` o similar
2. Clona/copia `wp-content/themes/prcw-theme` a `wp-content/themes/`
3. Define en `wp-config.php` (fuera del repo):

```php
define('PRCW_LOCATIONIQ_API_KEY', 'pk.TU_KEY_REAL_AQUI');
```

4. O deja que el admin configure en **WP Admin > PRCW Settings > LocationIQ API Key** (campo ACF registrado en `inc/acf-options.php:28`).

Ambas opciones inyectan la key a `inc/official-bars.php:16` vía `prcw_get_locationiq_api_key()`.

## Configuración LocationIQ

La key anterior `pk.d23c...` estaba hardcodeada y ya fue removida de `inc/official-bars.php:95`. **Rota la key** en https://my.locationiq.com/dashboard si fue expuesta en git.

Prioridad de resolución (`inc/official-bars.php:9`):
1. `PRCW_LOCATIONIQ_API_KEY` constant
2. `LOCATIONIQ_API_KEY` constant (fallback)
3. `get_option('prcw_locationiq_api_key')`
4. `get_field('locationiq_api_key','option')` (ACF en PRCW Settings)
5. `apply_filters('prcw_locationiq_api_key', '')`

## Scripts NPM

```powershell
npm run build   # producción (webpack + CopyWebpackPlugin copia src/blocks/**/render.php -> build/blocks/**)
npm run start   # watch mode (si no detecta cambios en Windows, es polling de NTFS — reinicia o usa --hot)
```

Fix cross-OS aplicado en `webpack.config.js:12`: `absoluteFilename.replace(/\\/g,'/')` para que `src\blocks\` en Windows normalice a `src/blocks/`.

## Estructura

```
prcw-theme/
├── .wp-env.json              # Docker wp-env (port 8888, PHP 8.2)
├── .wp-env.override.json     # local override gitignored (tu key real)
├── .gitattributes            # fuerza LF (evita CRLF Windows vs LF Ubuntu)
├── .gitignore
└── wp-content/themes/prcw-theme/
    ├── src/blocks/*/         # bloques ACF (render.php, block.json, style.scss)
    ├── build/blocks/*/       # generado por wp-scripts (no editar a mano)
    ├── inc/                  # setup.php, acf-blocks.php, official-bars.php, etc.
    ├── acf-json/             # field groups versionados
    └── assets/
```

## Troubleshooting Windows

| Síntoma | Causa | Fix |
|---------|-------|-----|
| `wp-scripts no se reconoce` | ExecutionPolicy | `Set-ExecutionPolicy RemoteSigned` o `cmd /c npm run build` |
| `npx wp-env start` → `docker not found` | Docker Desktop no instalado / WSL2 deshabilitado | Instalar Docker Desktop, habilitar WSL2 backend |
| `port 8888 already in use` | Hyper-V / IIS | Cambiar `port` en `.wp-env.json` |
| `LocationIQ 401` | Key no configurada | Ver `PRCW Settings` o `wp-config.php` |
| Diffs con `^M` | CRLF | `.gitattributes` ya fuerza LF — haz `git add --renormalize .` |

## Deploy a Ubuntu

```bash
git add --renormalize .
npm run build   # en CI o local Ubuntu
rsync -avz --exclude node_modules --exclude .wp-env.override.json ./ user@prod:/var/www/html/wp-content/themes/prcw-theme/
```
En prod, define `PRCW_LOCATIONIQ_API_KEY` en `wp-config.php` del servidor, no en repo.
