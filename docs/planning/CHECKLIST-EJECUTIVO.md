# Checklist Ejecutivo: fix y refactor

Este checklist sustituye el enfoque anterior de "todo listo para empezar" por seguimiento verificable. Marca una tarea como completa solo cuando el comando o cambio asociado exista.

## Baseline actual

- [x] `docker compose -f docker/docker-compose.yml config` valida la configuracion MySQL.
- [x] `docker compose -f compose.yaml config` valida una configuracion PostgreSQL divergente.
- [x] Se confirma que `vendor/` existe en este checkout.
- [x] Se confirma que `php` no esta en el `PATH` local de Windows.
- [x] Se confirma que `composer` no esta en el `PATH` local de Windows.
- [x] Se confirma que `git status` requiere `safe.directory` o `git -c safe.directory=...`.
- [x] Ejecutar PHP/Composer dentro de Docker o instalar toolchain local.
- [x] Ejecutar `composer validate`.
- [x] Ejecutar `composer dump-autoload`.
- [x] Ejecutar `php bin/console lint:container` o `debug:container`.
- [x] Ejecutar `vendor/bin/phpunit`.

## Fase 0 - Baseline verificable

- [x] Definir runner oficial: local o Docker.
- [x] Documentar comando para shell PHP.
- [x] Registrar errores reales con salida resumida.
- [x] Clasificar cada fallo por plan corrector.

**Salida esperada**: lista de fallos reproducibles, no suposiciones.

Estado verificado y clasificado:

- Runner oficial: Docker (`docker/docker-compose.yml`) con MySQL como base de desarrollo.
- Shell PHP documentada en `README.md` y `Makefile` via `make shell`.
- Fallos de bootstrap, entorno, namespaces y DI corregidos en Fase 1.
- Violaciones arquitectonicas corregidas en Fase 2 con `make deptrac` en 0 violaciones.
- Coverage mantenido como trabajo separado en `docs/planning/cobertura-tests-100porciento.md`.

## Fase 1 - Fundacional

### Setup, README y Makefile

- [x] `make build` usa el compose oficial.
- [x] `make start` arranca servicios necesarios.
- [x] `make test` existe y ejecuta PHPUnit por la ruta oficial.
- [x] `make console` o equivalente permite ejecutar comandos Symfony.
- [x] README explica requisitos reales: Docker, PHP/Composer local opcional, puertos y comandos.

### Docker y entorno

- [x] Elegida una unica BD para desarrollo.
- [x] Eliminado o alineado el compose no oficial.
- [x] `.env.example` existe y no contiene secretos reales.
- [x] `APP_SECRET` no queda vacio en entorno de ejemplo.
- [x] Variables FTP y AWS/S3 tienen nombres consistentes en `.env`, servicios y codigo.

### Servicios y autoload

- [x] `services.yaml` usa `App\Application\BuildForm\BuildFormHandler`.
- [x] `AwsS3Client.php`/`AwsS3Client` respeta PSR-4.
- [x] `GenerateReportDTO` respeta ruta y namespace.
- [x] Contenedor Symfony valida sin servicios inexistentes.

### Herramientas base

- [x] PHPStan configurado en nivel alcanzable.
- [x] Formateador configurado.
- [x] Deptrac configurado inicialmente como diagnostico.
- [x] PHPUnit genera reporte de coverage.

## Fase 2 - Arquitectura y dominio

- [x] Domain deja de depender de DTOs de Application.
- [ ] Domain deja de depender de SDKs externos (`Aws\Result`, Symfony, Infrastructure).
- [x] Handlers de Application dependen de puertos o servicios inyectados, no de factories estaticas de Infrastructure.
- [x] `NotificationFactory` convierte `string` a `NotificationChannel` o recibe enum tipado.
- [x] `FileStorageFactory` se elimina o se convierte en resolver inyectado.
- [ ] Entidades criticas validan invariantes en constructor/named constructors.
- [ ] `Document::getLastVersion()` maneja ausencia de versiones.
- [ ] `DocumentVersion` es realmente inmutable o deja de presentarse como tal.
- [ ] Value objects usados donde aportan invariantes, no como decoracion.

Estado actual de arquitectura:

- `make deptrac` reporta 0 violaciones con las reglas activas.
- `ReportProxy` ya depende de un puerto de dominio (`ReportAccessCheckerInterface`).
- Los handlers de archivos usan `FileStorageResolverInterface` y ya no construyen adaptadores de Infrastructure.
- Sigue pendiente eliminar acoplamientos de dominio a SDKs/ORM cuando aporten ruido arquitectonico real.

## Fase 3 - Testing y observabilidad

- [x] Suite PHPUnit verde.
- [ ] Tests con namespaces alineados con `App\Tests\`.
- [ ] Tests de comportamiento sustituyen mocks excesivos en casos clave.
- [ ] Coverage medible con umbral inicial documentado.
- [ ] Umbral minimo estable en CI o Makefile.
- [ ] Logs estructurados en casos de uso relevantes.
- [ ] Tests de logging solo donde verifican comportamiento util.

## Fase 4 - Portfolio y pulido

- [ ] README tiene quick start probado.
- [ ] Tabla de patrones indica ubicacion, intencion y estado.
- [ ] Code tour enlaza ejemplos ejecutables.
- [ ] Badges solo apuntan a checks existentes.
- [ ] Metricas generadas por comandos reales.
- [ ] Se documenta deuda pendiente sin ocultarla.

## Criterios finales

El proyecto estara listo para presentarse cuando:

- [x] se pueda arrancar desde cero con una ruta documentada;
- [x] los tests y quality gates se ejecuten de forma reproducible;
- [ ] la arquitectura no dependa de excepciones invisibles;
- [ ] los patrones tengan tests y una razon clara;
- [ ] la documentacion refleje el estado real del codigo.
