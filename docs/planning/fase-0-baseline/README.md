# FASE 0: Baseline verificable

**Objetivo**: convertir el diagnostico en hechos ejecutables antes de tocar arquitectura.  
**Duracion estimada**: 1-2 dias.  
**Criterio de exito**: existe una lista corta de fallos reales obtenida con comandos reproducibles.

## Por que existe esta fase

El planning anterior empezaba directamente por Docker y Makefile. Eso era razonable, pero dejaba un riesgo: refactorizar a partir de supuestos no verificados. La revision de Codex encontro varios hallazgos obsoletos, asi que esta fase fuerza una fotografia tecnica inicial.

## Checklist

- [x] Decidir si los comandos oficiales se ejecutan en local o dentro de Docker.
- [x] Documentar el comando unico para abrir shell PHP si Windows no tiene `php` en `PATH`.
- [x] Ejecutar `composer validate`.
- [x] Ejecutar `composer dump-autoload`.
- [x] Ejecutar `php bin/console lint:container` o `php bin/console debug:container`.
- [x] Ejecutar `vendor/bin/phpunit`.
- [x] Ejecutar `docker compose -f docker/docker-compose.yml config`.
- [x] Ejecutar `docker compose -f compose.yaml config` solo para confirmar que sera eliminado o ignorado.
- [x] Registrar fallos reales en el checklist ejecutivo.

## Fallos ya confirmados sin ejecutar PHP

- Doble compose: raiz con PostgreSQL y `docker/` con MySQL.
- `Makefile` usa `docker/`, por lo que el compose raiz no es la fuente de verdad actual.
- `.env` tiene `APP_SECRET` vacio.
- `.env` usa `FTP_PASS`, pero servicios y factory esperan `FTP_PASSWORD`.
- `.env` usa `AWS_*`, pero `FileStorageFactory` lee `S3_*`.
- `services.yaml` referencia `App\Application\BuilderUserProfile\BuildFormHandler`, pero la clase real es `App\Application\BuildForm\BuildFormHandler`.
- `src/Infrastructure/Client/Aws3Client.php` declara `AwsS3Client`.
- `src/Application/DTO/GenerateReportDTO.php` no respeta ruta/namespace.

## Entregable

Crear una seccion "Baseline actual" en [CHECKLIST-EJECUTIVO.md](../CHECKLIST-EJECUTIVO.md) con:

- comando ejecutado;
- resultado;
- fallo si aplica;
- enlace al plan que lo corrige.

No se debe pasar a Fase 1 hasta que haya una ruta clara para ejecutar PHP y Composer de forma reproducible.

## Estado actual

- Ruta oficial definida: Docker + `docker/docker-compose.yml`.
- Shell PHP documentada: `make shell`.
- Calidad basica verificada: `composer validate`, `composer dump-autoload`, `lint:container` y PHPUnit ejecutados en Docker.
- El checklist ejecutivo ya refleja el estado real y separa lo resuelto en Fase 1 de lo pendiente en Fase 2 y Fase 3.
