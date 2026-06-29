# Prompts para delegar un CRUD a otro agente

Este archivo junta prompts listos para copiar y pegar en otro agente. Estan pensados para seguir la arquitectura actual del proyecto: controlador delgado, servicio con reglas de negocio, repositorio PDO, DTOs, validadores y respuestas JSON con `JsonResponse`.

## Prompt principal

```text
Trabaja dentro del proyecto Flyto y respeta la arquitectura ya existente.

Objetivo: implementar el CRUD completo de la entidad [NOMBRE_ENTIDAD] siguiendo el mismo patron que usa el modulo `novedades`.

Contexto tecnico:
- PHP 8 sin framework.
- Punto de entrada en `public/index.php`.
- Rutas API por modulo en `app/<modulo>/routes.php`.
- Controladores en `controllers/`.
- Logica de negocio en `services/`.
- Acceso a datos en `repositories/` con PDO.
- Validacion en `validators/`.
- Transferencia de datos con `dtos/`.
- Respuestas JSON con `app/shared/http/json-response.php`.
- Errores de negocio con `HttpException`.

Lo que quiero que hagas:
1. Analiza el modulo existente mas cercano y replica su estilo de nombres y separacion por capas.
2. Crea o completa el CRUD de [NOMBRE_ENTIDAD] end-to-end.
3. Si faltan archivos, agrega `model`, `dto`, `validator`, `repository`, `service`, `controller` y `routes`.
4. Si el CRUD tiene panel admin, protege las rutas sensibles con el middleware adecuado.
5. Mantiene el controlador sin logica de negocio y sin SQL.
6. Mantiene el repositorio solo con consultas y mapeo de filas.
7. Devuelve JSON consistente con `success`, `message` y `error` segun corresponda.

Reglas de implementacion:
- No rompas el patron existente.
- No uses un framework nuevo.
- No cambies la arquitectura global.
- Si detectas un vacio en el container, agregalo en `app/container.php`.
- Si agregas rutas nuevas, registra el modulo donde corresponda.
- Si necesitas vistas, respetar `app/<modulo>/views`.

Antes de editar, resume en 5 o 6 lineas el plan tecnico concreto.
Despues de editar, valida que el flujo compile conceptualmente y que no queden dependencias rotas.
```

## Prompt para capa de dominio

```text
Quiero que implementes la capa de dominio de [NOMBRE_ENTIDAD] en Flyto.

Debes crear y/o ajustar:
- `models/[nombre-entidad].model.php`
- `dtos/crear-[nombre-entidad].dto.php`
- `dtos/editar-[nombre-entidad].dto.php`
- `validators/[nombre-entidad].validator.php`
- `services/[nombre-entidad].service.php`

Sigue estas reglas:
- El modelo debe exponer `toArray()` si la entidad va a serializarse a JSON.
- El DTO debe transportar solo datos ya validados.
- El validador debe transformar input crudo en DTO y arrojar `HttpException` ante errores de negocio o formato.
- El servicio debe contener la logica de negocio y delegar persistencia al repositorio.

Usa como referencia el modulo `novedades` para mantener nombres, estilo y flujo.
```

## Prompt para API y controladores

```text
Quiero que implementes la capa HTTP/API de [NOMBRE_ENTIDAD] en Flyto.

Debes revisar o crear:
- `controllers/[nombre-entidad].controller.php`
- `routes.php`
- registro de dependencias en `app/container.php`

Requisitos:
- Cada action del controlador debe hacer solo orquestacion.
- El controlador debe invocar middleware si la ruta es de administracion.
- El controlador debe usar el validador antes de llamar al servicio.
- La respuesta debe salir por `JsonResponse`.
- Los endpoints deben seguir el estilo del modulo `novedades`.

Entregame al final la lista de endpoints creados o modificados y una breve explicacion de cada uno.
```

## Prompt de verificacion

```text
Revisa el CRUD de [NOMBRE_ENTIDAD] en Flyto como auditor tecnico.

Chequea especificamente:
- consistencia de nombres entre model, dto, service, repository, controller y routes
- validaciones faltantes
- errores potenciales de PDO o de fechas
- rutas protegidas sin middleware
- respuestas JSON inconsistentes
- dependencias no registradas en el container

Devuelve solo hallazgos concretos, ordenados por severidad, y al final agrega una sugerencia breve de prueba manual para cada hallazgo.
```
