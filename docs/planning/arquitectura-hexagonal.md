# Plan de Mejora: Arquitectura Hexagonal Incompleta

## Problemas detectados
- La regla de dependencias no se mantiene de forma consistente: el dominio conoce detalles de Application, Infrastructure, Doctrine, AWS y Symfony.
- Ejemplos especificos: `NotificationFactoryInterface` dependia de `NotificationRequestDTO` (DTO de aplicacion), `ReportProxy` dependia de `AccessChecker` (infraestructura) y `AwsS3ClientInterface` dependia de `Aws\Result` (SDK externo).
- La aplicacion instanciaba adaptadores concretos y leia variables de entorno directamente, violando la inversion de dependencias.

## Mejora propuesta
Implementar una arquitectura hexagonal estricta enfocada en mostrar patrones de diseno. El dominio debe definir puertos con tipos propios del nucleo, mientras que la infraestructura proporciona adaptadores inyectados. Esto mejora la documentacion al demostrar separacion clara de responsabilidades y facilita ejemplos didacticos.

## Lista de tareas a realizar
- [ ] Refactorizar interfaces de dominio para usar tipos propios (value objects) en lugar de DTOs de aplicacion.
- [x] Crear adaptadores en Infrastructure que implementen puertos del dominio.
- [x] Configurar inyeccion de dependencias en `services.yaml` para adaptadores.
- [x] Eliminar lecturas directas de `$_ENV` en aplicacion.
- [x] Actualizar handlers de archivos para depender de interfaces.
- [x] Verificar dependencias del dominio.

## Avance actual
- `ReportProxy` ya no depende de `AccessChecker` de Infrastructure; ahora consume `ReportAccessCheckerInterface`.
- `NotificationFactoryInterface` y `UserRegistrationFacadeInterface` dejaron de vivir en Domain porque dependian de DTOs de Application.
- `FileStorageFactory` fue sustituido por `FileStorageResolverInterface` y `FileStorageResolver`.
- El wrapper de AWS S3 fue reubicado a `Infrastructure\Client\AwsS3ClientInterface`, eliminando `Aws\Result` del dominio.
- `services.yaml` ya resuelve los adaptadores necesarios por DI.
- `make deptrac` reporta 0 violaciones con la configuracion actual.
- Sigue pendiente decidir cuanto ORM vive realmente en Domain.

## Bundles entregables
### Bundle 1: Refactorizacion de puertos de dominio
- Refactorizar interfaces para usar tipos propios.
- Crear value objects basicos.
- Documentar con PHPDoc ejemplos de uso.

### Bundle 2: Implementacion de adaptadores
- Crear adaptadores en Infrastructure.
- Configurar inyeccion en `services.yaml`.
- Agregar ejemplos de inyeccion en README.

### Bundle 3: Limpieza de dependencias externas
- Eliminar lecturas de `$_ENV`.
- Actualizar handlers.
- Verificar y documentar la arquitectura resultante.
