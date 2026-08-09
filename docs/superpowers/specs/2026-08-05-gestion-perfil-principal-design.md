# Gestión del perfil principal

**Fecha:** 2026-08-05
**Rama:** `nueva-db-ocr-local`
**Alcance:** ophi-back (Laravel 12) + ophi-frontend (Vue 3)

## Objetivo

Permitir que un usuario gestione su perfil principal: nombre, color de avatar, dirección de
email (con verificación) y suscripción al mailing.

## Estado actual

Relevamiento previo al diseño:

1. **No existe el perfil principal en los datos.** La columna `profiles.is_main` está en `0` para
   los 11 perfiles existentes y ninguno de los 16 usuarios tiene uno. La migración
   `2026_04_21_153649_add_column_is_main_to_profiles_table` agregó la columna con default `0` y
   nunca se hizo el backfill. `AuthService::register()` no crea perfiles.
2. **La pantalla es inconsistente.** `ProfileView.vue` resuelve el perfil principal filtrando por
   `is_main`, pero calcula los perfiles familiares comparando `profile.name === user.name`. Son dos
   criterios distintos para la misma división. Con `is_main` en `0`, la pestaña "MI PERFIL" queda
   vacía para todos los usuarios.
3. **Falta control de autorización.** `ProfileService::update()` y `destroy()` resuelven el perfil
   con `findOrFail($id)` sin verificar pertenencia. Cualquier usuario autenticado puede editar o
   eliminar el perfil de otro alterando el id de la petición.
4. **La edición no está implementada.** `ProfileService::update()` solo sincroniza ingredientes;
   descarta nombre y avatar aunque el cliente los envíe.
5. **El avatar nunca se usó.** `profiles.avatar` es `NULL` en todos los registros. La UI dibuja un
   círculo con la inicial del nombre en `SomeUserInfo.vue`. No hay manejo de archivos en el backend.
6. **El mailing está desacoplado del usuario.** La tabla `newsletter` guarda `email` y `status`, sin
   relación con `users`.
7. **Los tests no pueden tocar la base.** `phpunit.xml` configura SQLite `:memory:`, pero la
   migración `2026_07_05_000001_convert_core_schema_to_er_v2` usa SQL específico de MySQL
   (`ALTER TABLE ... MODIFY`, `ENUM`, `after()`). Cualquier test con `RefreshDatabase` falla. Los
   únicos tests presentes son los `ExampleTest` de Laravel.

## Decisiones tomadas

| Tema | Decisión |
| --- | --- |
| Email | Editable, con verificación por link antes de aplicarse |
| Foto | Iniciales sobre color elegible, sin subida de archivos |
| Mailing | Un switch de suscripción |
| Perfil principal | Se crea automáticamente al registrarse; backfill para los usuarios existentes |
| Arquitectura | Cuenta y perfil separados: email y mailing pertenecen a `User`; nombre y color a `Profile` |

La separación entre cuenta y perfil responde al modelo de datos: el email vive en `users` y es la
credencial de acceso, y el mailing se resuelve por dirección de correo. Además deja preparado el
terreno para la edición del perfil familiar, donde el email debe desaparecer.

## Modelo de datos

Cuatro migraciones nuevas:

**1. `profiles.avatar_color`** — `varchar(7)` nullable. Guarda un color hexadecimal.

No se reutiliza `avatar`, que queda reservada para una ruta de imagen si en el futuro se
implementan fotos reales.

**2. Backfill de `is_main`** — migración de datos. Para cada usuario sin perfil principal, marca el
perfil más antiguo con `is_main = 1`; si no tiene ninguno, crea uno con el nombre del usuario.

Invariante: todo usuario tiene exactamente un perfil con `is_main = 1`. `destroy()` rechaza la
eliminación del perfil principal con 422; sin esa restricción el invariante se rompe y la pestaña
"MI PERFIL" vuelve a quedar vacía.

**3. `newsletter.user_id`** — `bigint` nullable con FK a `users`.

Sin esta relación, un cambio de email deja la suscripción huérfana. Es nullable porque
`/subscribe-email` sigue aceptando suscriptores sin cuenta.

**4. Tabla `email_change_requests`** — `user_id`, `new_email`, `token` (hasheado), `expires_at`.

Laravel provee verificación de email inicial, pero no cambio de email verificado. Se sigue el patrón
de `password_reset_tokens`.

## Backend

### Endpoints

```
GET    /api/account                          → email actual, email pendiente, estado del mailing
PUT    /api/account/email                    → { new_email, current_password }
POST   /api/account/email/confirm/{token}    → público, sin autenticación
PUT    /api/account/newsletter               → { subscribed: bool }

PUT    /api/profiles/{id}                    → acepta name y avatar_color además de ingredients
```

Todos bajo `auth:sanctum` excepto la confirmación, que se invoca desde el link del correo.

### Servicios

- **`EmailChangeService`** (nuevo) — solicitar y confirmar el cambio de dirección.
- **`NewsletterSubscriberService`** — se agrega `unsubscribe()` y el vínculo con el usuario.
- **`ProfileService`** — `update()` pasa a aceptar nombre y color.
- **`AuthService::register()`** — crea el perfil principal dentro de la transacción existente.

`ProfileService` se convierte de métodos estáticos a instancia con inyección por constructor,
siguiendo el patrón de `ImportNormalizedProductsCommand`. Los métodos estáticos no son sustituibles
en un test y obligan a impactar la base para cualquier verificación. El cambio es acotado:
`ProfileController` es su único consumidor.

### Autorización

El perfil se resuelve siempre restringido al usuario autenticado:

```php
Profile::where('owner_id', auth()->id())->findOrFail($id);
```

Devuelve 404 en lugar de 403, de modo que no se revela la existencia de perfiles ajenos. Se aplica
en `update()` y `destroy()`.

### Flujo de cambio de email

1. El usuario envía la nueva dirección junto con su contraseña actual. La contraseña evita que
   alguien con acceso físico al dispositivo desbloqueado se apropie de la cuenta.
2. Se valida que la dirección no esté en uso y sea distinta de la actual.
3. Se registra la solicitud con el token hasheado y vencimiento a 60 minutos, invalidando
   solicitudes previas del mismo usuario.
4. Se envía el link a la **nueva** dirección y un aviso a la **anterior** informando que se solicitó
   el cambio.
5. Al confirmar se actualiza `users.email`, se arrastra el email en `newsletter` y se elimina la
   solicitud.

El arrastre en `newsletter` busca por `user_id`. Si no hay fila vinculada pero existe una con la
dirección anterior —caso de quien se suscribió antes de registrarse— se vincula a ese usuario y se
actualiza su email en la misma operación.

Hasta el paso 5 la credencial de acceso sigue siendo la dirección original. Un error de tipeo no
deja al usuario fuera de su cuenta.

## Frontend

### Service

`src/services/account.js` — `getAccount()`, `requestEmailChange()`, `setNewsletter()`. Ningún
componente invoca axios directamente.

### Componentes

```
components/profile/AvatarColorPicker.vue    → selección de color
components/profile/EmailChangeForm.vue      → email, contraseña y estado pendiente
components/profile/NewsletterToggle.vue     → switch de suscripción
composables/useAccount.js                   → estado de la cuenta y sus llamadas
```

La pestaña "MI PERFIL" de `ProfileView.vue` pasa a ser la pantalla de gestión. Los componentes se
extraen porque la vista ya tiene 240 líneas y duplicaría su tamaño; cada pieza queda comprensible y
verificable de forma aislada.

`SomeUserInfo.vue` recibe el color por prop, con el comportamiento actual como valor por defecto. Se
usa en cuatro lugares distintos, por lo que el cambio debe ser retrocompatible.

### Paleta

Conjunto cerrado de seis colores derivados de la identidad visual de Ophi, en lugar de un selector
libre. Cada color debe alcanzar un contraste mínimo de 4.5:1 contra el blanco de las iniciales
(WCAG 2.2 AA); la lista definitiva se valida contra ese umbral durante la implementación. Un
selector libre permite combinaciones ilegibles que el proyecto no puede controlar.

### Confirmación

Ruta nueva `/confirmar-email/:token`. El link del correo apunta al frontend, que dispara la
confirmación con un **POST**.

Los clientes de correo y los antivirus corporativos suelen abrir los enlaces automáticamente para
analizarlos. Si el enlace fuera un GET directo contra la API, ese análisis consumiría el token y
aplicaría el cambio sin intervención del usuario.

## Manejo de errores

| Caso | Respuesta |
| --- | --- |
| Email ya en uso | 422 |
| Contraseña incorrecta | 422 — no 401, que se confunde con sesión vencida |
| Token inválido o vencido | 410, indicando que debe solicitarse el cambio nuevamente |
| Perfil de otro usuario | 404 |

`PUT /api/account/email` se protege con `throttle:6,1`. Sin límite, el endpoint permite enviar
correos masivos a direcciones arbitrarias firmados por Ophi.

El frontend reutiliza `Feedback.vue` y `Error.vue`, ya presentes en la pantalla. Cuando existe una
solicitud pendiente, la interfaz lo informa explícitamente.

## Testing

### Trabajo previo

`phpunit.xml` pasa a apuntar a una base MySQL dedicada (`ophi_testing`) con `RefreshDatabase`. La
alternativa —reescribir la migración v2 para que sea compatible con ambos motores— implica más
trabajo y resulta frágil, sin beneficio para el proyecto.

Se agrega `ProfileFactory`; hoy solo existe `UserFactory`.

### Casos

En orden de implementación, escribiendo primero la prueba que falla:

1. Un usuario no puede editar el perfil de otro → 404
2. Un usuario no puede eliminar el perfil de otro → 404
3. `update()` persiste nombre y color
4. El registro crea el perfil principal con `is_main`
5. Solicitar el cambio de email crea la solicitud, no modifica `users.email` y envía ambos correos
6. Contraseña incorrecta → 422 y ninguna solicitud creada
7. Email en uso → 422 y ninguna solicitud creada
8. Confirmar con token válido cambia el email, arrastra `newsletter` y elimina la solicitud
9. Token vencido, ajeno o inválido → falla y el email permanece intacto
10. El switch de newsletter suscribe y desuscribe
11. Eliminar el perfil principal → 422 y el perfil permanece
12. La migración de backfill deja perfil principal a todos los usuarios existentes

Los casos 1 y 2 van primero por tratarse del defecto de seguridad.

### Fuera de alcance

No se monta infraestructura de tests en el frontend: no hay vitest ni equivalente instalado, y
armarla excede esta tarea. Queda como candidato de backlog.

## Notas de entorno

`MAIL_MAILER=log` en el entorno local: los correos de verificación se escriben en `storage/logs` y
no se envían. Para probar el flujo completo hace falta Mailpit, Mailtrap o un SMTP real.
