# Gestión del perfil principal — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Permitir que un usuario gestione su perfil principal —nombre, color de avatar, email con verificación y suscripción al mailing— y cerrar el defecto de autorización que permite editar perfiles ajenos.

**Architecture:** Cuenta y perfil quedan separados. El email y el mailing pertenecen a `User` y se exponen bajo `/api/account/*`; el nombre y el color pertenecen a `Profile` y viajan por el `PUT /api/profiles/{id}` existente. El cambio de email nunca se aplica de forma directa: se registra una solicitud con token hasheado y solo se escribe en `users.email` al confirmarse desde el enlace enviado al nuevo domicilio.

**Tech Stack:** Laravel 12 (PHP 8.2+), Sanctum, PHPUnit 11.5, MariaDB 10.4, Vue 3 con `<script setup>`, Vue Router 4, Axios, Tailwind 4, Vite 7.

**Spec:** `docs/superpowers/specs/2026-08-05-gestion-perfil-principal-design.md`

## Global Constraints

- Rama de trabajo: `nueva-db-ocr-local` en ambos repositorios.
- Backend en `ophi-back/`, frontend en `ophi-frontend/`. Son repositorios git independientes: cada uno lleva sus propios commits.
- Controllers finos: validan y delegan. Toda la lógica vive en un Service.
- Frontend: ningún componente invoca `axios` directamente; todas las llamadas HTTP pasan por un archivo de `src/services/`.
- Componentes Vue con Composition API y `<script setup>`.
- Los mensajes dirigidos al usuario van en español. Los identificadores, nombres de archivo y comentarios de código, en inglés.
- Suite de tests: `composer test` (ejecuta `php artisan config:clear` y `php artisan test`).
- Un test individual: `php artisan test --filter=NombreDelTest`.
- Colores de avatar: contraste mínimo 4.5:1 contra `#FFFFFF` (WCAG 2.2 AA).
- El token de cambio de email se guarda **hasheado**, nunca en claro.

---

### Task 1: Infraestructura de tests

Ningún test puede tocar la base hoy: `phpunit.xml` usa SQLite `:memory:` y la migración `2026_07_05_000001_convert_core_schema_to_er_v2` usa SQL exclusivo de MySQL (`ALTER TABLE ... MODIFY`, `ENUM`). Esta tarea es prerrequisito de todas las demás.

**Files:**
- Modify: `ophi-back/phpunit.xml:25-26`
- Create: `ophi-back/database/factories/ProfileFactory.php`
- Test: `ophi-back/tests/Feature/Profile/ProfileFactoryTest.php`

**Interfaces:**
- Consumes: nada.
- Produces: `ProfileFactory` con estados `main()` y `forOwner(User $user)`. Base de tests `ophi_testing`.

- [ ] **Step 1: Crear la base de datos de tests**

```bash
"/c/xampp/mysql/bin/mysql.exe" -h 127.0.0.1 -u root -e "CREATE DATABASE IF NOT EXISTS ophi_testing CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

- [ ] **Step 2: Apuntar phpunit.xml a esa base**

Reemplazar las líneas 25-26 de `phpunit.xml`:

```xml
        <env name="DB_CONNECTION" value="mariadb"/>
        <env name="DB_DATABASE" value="ophi_testing"/>
```

- [ ] **Step 3: Escribir el test que falla**

Crear `tests/Feature/Profile/ProfileFactoryTest.php`:

```php
<?php

namespace Tests\Feature\Profile;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileFactoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_crea_un_perfil_principal_para_un_usuario(): void
    {
        $user = User::factory()->create();

        $profile = Profile::factory()->main()->forOwner($user)->create();

        $this->assertTrue($profile->is_main);
        $this->assertSame($user->id, $profile->owner_id);
    }
}
```

- [ ] **Step 4: Correr el test y verificar que falla**

Run: `php artisan test --filter=ProfileFactoryTest`
Expected: FAIL con `Call to undefined method App\Models\Profile::factory()`

- [ ] **Step 5: Crear el factory**

Crear `database/factories/ProfileFactory.php`:

```php
<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Profile>
 */
class ProfileFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->firstName(),
            'avatar' => null,
            'avatar_color' => null,
            'is_main' => false,
        ];
    }

    public function main(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_main' => true,
        ]);
    }

    public function forOwner(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'owner_id' => $user->id,
            'user_id' => $user->id,
        ]);
    }
}
```

- [ ] **Step 6: Habilitar HasFactory en el modelo**

En `app/Models/Profile.php`, agregar el import y el trait:

```php
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Profile extends Model
{
    use HasFactory;
```

Agregar también `avatar_color` al array `$fillable` (la columna se crea en la Task 4; el factory ya la referencia, por eso entra ahora):

```php
    protected $fillable = [
        'name',
        'avatar',
        'avatar_color',
        'user_id',
        'owner_id',
        'share_token',
        'is_main',
    ];
```

- [ ] **Step 7: Crear la migración de `avatar_color`**

Sin esta columna el factory falla. Crear `database/migrations/2026_08_05_000001_add_avatar_color_to_profiles_table.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->string('avatar_color', 7)->nullable()->after('avatar');
        });
    }

    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn('avatar_color');
        });
    }
};
```

- [ ] **Step 8: Correr el test y verificar que pasa**

Run: `php artisan test --filter=ProfileFactoryTest`
Expected: PASS

- [ ] **Step 9: Aplicar la migración en la base de desarrollo**

Run: `php artisan migrate`
Expected: `2026_08_05_000001_add_avatar_color_to_profiles_table ... DONE`

- [ ] **Step 10: Commit**

```bash
cd ophi-back
git add phpunit.xml database/factories/ProfileFactory.php app/Models/Profile.php database/migrations/2026_08_05_000001_add_avatar_color_to_profiles_table.php tests/Feature/Profile/ProfileFactoryTest.php
git commit -m "test: configurar base de tests MySQL y agregar ProfileFactory"
```

---

### Task 2: Cerrar el acceso a perfiles ajenos

Defecto de seguridad activo: `ProfileService::update()` y `destroy()` resuelven el perfil con `findOrFail($id)` sin verificar pertenencia. Va primero porque es el único cambio que arregla algo roto en producción.

En la misma tarea, `ProfileService` pasa de métodos estáticos a instancia inyectable: es su único consumidor y los métodos estáticos no son sustituibles en un test.

**Files:**
- Modify: `ophi-back/app/Services/ProfileService.php`
- Modify: `ophi-back/app/Http/Controllers/Api/ProfileController.php`
- Test: `ophi-back/tests/Feature/Profile/ProfileAuthorizationTest.php`

**Interfaces:**
- Consumes: `ProfileFactory` de la Task 1.
- Produces: `ProfileService` como instancia con los métodos públicos `getAuthUserProfiles(): Collection`, `store(array $data): Profile`, `update(int $id, array $data): Profile`, `destroy(int $id): void`. El método privado `findOwned(int $id): Profile` resuelve todo perfil restringido al usuario autenticado.

- [ ] **Step 1: Escribir los tests que fallan**

Crear `tests/Feature/Profile/ProfileAuthorizationTest.php`:

```php
<?php

namespace Tests\Feature\Profile;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_usuario_no_puede_editar_el_perfil_de_otro(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $profile = Profile::factory()->forOwner($owner)->create(['name' => 'Original']);

        $response = $this->actingAs($intruder)
            ->putJson("/api/profiles/{$profile->id}", ['name' => 'Hackeado']);

        $response->assertNotFound();
        $this->assertSame('Original', $profile->fresh()->name);
    }

    public function test_un_usuario_no_puede_eliminar_el_perfil_de_otro(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $profile = Profile::factory()->forOwner($owner)->create();

        $response = $this->actingAs($intruder)
            ->deleteJson("/api/profiles/{$profile->id}");

        $response->assertNotFound();
        $this->assertDatabaseHas('profiles', ['id' => $profile->id]);
    }

    public function test_el_dueno_si_puede_editar_su_perfil(): void
    {
        $owner = User::factory()->create();
        $profile = Profile::factory()->forOwner($owner)->create(['name' => 'Original']);

        $response = $this->actingAs($owner)
            ->putJson("/api/profiles/{$profile->id}", ['name' => 'Nuevo nombre']);

        $response->assertOk();
        $this->assertSame('Nuevo nombre', $profile->fresh()->name);
    }

    public function test_persiste_el_color_del_avatar(): void
    {
        $owner = User::factory()->create();
        $profile = Profile::factory()->forOwner($owner)->create(['avatar_color' => null]);

        $response = $this->actingAs($owner)
            ->putJson("/api/profiles/{$profile->id}", ['avatar_color' => '#005B8E']);

        $response->assertOk();
        $this->assertSame('#005B8E', $profile->fresh()->avatar_color);
    }

    public function test_rechaza_un_color_con_formato_invalido(): void
    {
        $owner = User::factory()->create();
        $profile = Profile::factory()->forOwner($owner)->create(['avatar_color' => null]);

        $response = $this->actingAs($owner)
            ->putJson("/api/profiles/{$profile->id}", ['avatar_color' => 'rojo']);

        $response->assertStatus(422);
        $this->assertNull($profile->fresh()->avatar_color);
    }
}
```

- [ ] **Step 2: Correr los tests y verificar que fallan**

Run: `php artisan test --filter=ProfileAuthorizationTest`
Expected: FAIL — los dos primeros devuelven 200 en lugar de 404 (ese es el bug); el tercero falla porque `update()` todavía ignora el nombre.

- [ ] **Step 3: Reescribir ProfileService como instancia**

Reemplazar el contenido completo de `app/Services/ProfileService.php`:

```php
<?php

namespace App\Services;

use App\Models\Profile;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProfileService
{
    public function getAuthUserProfiles(): Collection
    {
        if (! Auth::check()) {
            return collect();
        }

        $profiles = Profile::with(['ingredients' => function ($query) {
            $query->select('ingredients.id', 'ingredients.name', 'ingredients.icon', 'ingredients.is_group', 'ingredients.aliases');
        }])
            ->where(function ($query) {
                $query->where('owner_id', Auth::id())
                    ->orWhere('user_id', Auth::id());
            })
            ->get(['id', 'name', 'avatar', 'avatar_color', 'user_id', 'owner_id', 'is_main', 'created_at', 'updated_at']);

        return $profiles->map(function (Profile $profile) {
            $ingredientIds = $profile->ingredients
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values()
                ->toArray();

            return [
                'id' => $profile->id,
                'name' => $profile->name,
                'avatar' => $profile->avatar,
                'avatar_color' => $profile->avatar_color,
                'user_id' => $profile->owner_id ?? $profile->user_id,
                'owner_id' => $profile->owner_id ?? $profile->user_id,
                'is_main' => (bool) $profile->is_main,
                'created_at' => $profile->created_at,
                'updated_at' => $profile->updated_at,
                'ingredients' => $profile->ingredients->values(),
                'ingredient_ids' => $ingredientIds,
            ];
        })->values();
    }

    public function store(array $data): Profile
    {
        $repeatedName = Profile::where(function ($query) {
            $query->where('owner_id', Auth::id())
                ->orWhere('user_id', Auth::id());
        })->where('name', $data['name'])->exists();

        if ($repeatedName) {
            throw new Exception('Ya tenés un perfil con ese nombre');
        }

        return DB::transaction(function () use ($data) {
            $profile = new Profile();
            $profile->name = $data['name'];
            $profile->avatar = $data['avatar'] ?? null;
            $profile->avatar_color = $data['avatar_color'] ?? null;
            $profile->user_id = Auth::id();
            $profile->owner_id = Auth::id();
            $profile->save();

            $profile->ingredients()->attach($data['ingredients'] ?? []);

            return $profile;
        });
    }

    public function update(int $id, array $data): Profile
    {
        $profile = $this->findOwned($id);

        return DB::transaction(function () use ($profile, $data) {
            if (array_key_exists('name', $data)) {
                $profile->name = $data['name'];
            }

            if (array_key_exists('avatar_color', $data)) {
                $profile->avatar_color = $data['avatar_color'];
            }

            $profile->save();

            if (array_key_exists('ingredients', $data)) {
                $profile->ingredients()->sync($data['ingredients'] ?? []);
            }

            return $profile->load('ingredients');
        });
    }

    public function destroy(int $id): void
    {
        $profile = $this->findOwned($id);

        DB::transaction(function () use ($profile) {
            $profile->ingredients()->detach();
            $profile->delete();
        });
    }

    /**
     * Resuelve un perfil restringido al usuario autenticado.
     * Devuelve 404 en lugar de 403 para no revelar la existencia de perfiles ajenos.
     */
    private function findOwned(int $id): Profile
    {
        return Profile::with('ingredients')
            ->where('owner_id', Auth::id())
            ->findOrFail($id);
    }
}
```

- [ ] **Step 4: Adaptar el controller a la inyección**

Reemplazar el contenido completo de `app/Http/Controllers/Api/ProfileController.php`:

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function __construct(private ProfileService $profileService)
    {
    }

    public function get_auth_user_profiles(): JsonResponse
    {
        return response()->json($this->profileService->getAuthUserProfiles());
    }

    public function store(Request $request): JsonResponse
    {
        $user = User::with(['profiles', 'subscription'])->find(Auth::id());
        $userProfiles = $user->profiles;

        if (! $user->isPremium() && count($userProfiles) >= 1) {
            return response()->json(['message' => 'Usuario no premium'], 403);
        }

        if ($user->isPremium() && count($userProfiles) >= 10) {
            return response()->json(['message' => 'Máximo de 10 perfiles por usuario'], 403);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'ingredients' => 'nullable|array',
        ], [
            'name.required' => 'El nombre es obligatorio',
        ]);

        try {
            $profile = $this->profileService->store($data);

            return response()->json([
                'message' => 'Perfil creado correctamente',
                'profile' => $profile,
            ]);
        } catch (\Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 422);
        }
    }

    public function update(int $id, Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'avatar_color' => 'sometimes|nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'ingredients' => 'sometimes|nullable|array',
        ], [
            'name.required' => 'El nombre es obligatorio',
            'avatar_color.regex' => 'El color no tiene un formato válido',
        ]);

        $profile = $this->profileService->update($id, $data);

        return response()->json([
            'message' => 'Perfil guardado',
            'profile' => $profile,
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->profileService->destroy($id);

        return response()->json(['message' => 'Perfil eliminado']);
    }
}
```

- [ ] **Step 5: Correr los tests y verificar que pasan**

Run: `php artisan test --filter=ProfileAuthorizationTest`
Expected: PASS (5 tests)

- [ ] **Step 6: Correr la suite completa**

Run: `composer test`
Expected: PASS — confirma que la conversión a instancia no rompió nada.

- [ ] **Step 7: Commit**

```bash
cd ophi-back
git add app/Services/ProfileService.php app/Http/Controllers/Api/ProfileController.php tests/Feature/Profile/ProfileAuthorizationTest.php
git commit -m "fix: restringir edición y borrado de perfiles al dueño autenticado"
```

---

### Task 3: Proteger el perfil principal del borrado

El invariante es que todo usuario tiene exactamente un perfil con `is_main = 1`. Sin esta restricción, el primer borrado lo rompe.

**Files:**
- Modify: `ophi-back/app/Services/ProfileService.php`
- Modify: `ophi-back/app/Http/Controllers/Api/ProfileController.php`
- Create: `ophi-back/app/Exceptions/MainProfileDeletionException.php`
- Test: `ophi-back/tests/Feature/Profile/MainProfileProtectionTest.php`

**Interfaces:**
- Consumes: `ProfileService::destroy()` de la Task 2.
- Produces: `MainProfileDeletionException`, lanzada por `destroy()` cuando el perfil tiene `is_main = true`.

- [ ] **Step 1: Escribir el test que falla**

Crear `tests/Feature/Profile/MainProfileProtectionTest.php`:

```php
<?php

namespace Tests\Feature\Profile;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MainProfileProtectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_no_se_puede_eliminar_el_perfil_principal(): void
    {
        $user = User::factory()->create();
        $profile = Profile::factory()->main()->forOwner($user)->create();

        $response = $this->actingAs($user)->deleteJson("/api/profiles/{$profile->id}");

        $response->assertStatus(422);
        $this->assertDatabaseHas('profiles', ['id' => $profile->id]);
    }

    public function test_si_se_puede_eliminar_un_perfil_familiar(): void
    {
        $user = User::factory()->create();
        Profile::factory()->main()->forOwner($user)->create();
        $familiar = Profile::factory()->forOwner($user)->create();

        $response = $this->actingAs($user)->deleteJson("/api/profiles/{$familiar->id}");

        $response->assertOk();
        $this->assertDatabaseMissing('profiles', ['id' => $familiar->id]);
    }
}
```

- [ ] **Step 2: Correr el test y verificar que falla**

Run: `php artisan test --filter=MainProfileProtectionTest`
Expected: FAIL — el primer test recibe 200 y el perfil se elimina.

- [ ] **Step 3: Crear la excepción**

Crear `app/Exceptions/MainProfileDeletionException.php`:

```php
<?php

namespace App\Exceptions;

use Exception;

class MainProfileDeletionException extends Exception
{
    public function __construct(string $message = 'No podés eliminar tu perfil principal')
    {
        parent::__construct($message);
    }
}
```

- [ ] **Step 4: Lanzarla desde el service**

En `app/Services/ProfileService.php`, agregar el import y modificar `destroy()`:

```php
use App\Exceptions\MainProfileDeletionException;
```

```php
    public function destroy(int $id): void
    {
        $profile = $this->findOwned($id);

        if ($profile->is_main) {
            throw new MainProfileDeletionException();
        }

        DB::transaction(function () use ($profile) {
            $profile->ingredients()->detach();
            $profile->delete();
        });
    }
```

- [ ] **Step 5: Traducirla a 422 en el controller**

En `app/Http/Controllers/Api/ProfileController.php`, agregar el import y modificar `destroy()`:

```php
use App\Exceptions\MainProfileDeletionException;
```

```php
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->profileService->destroy($id);
        } catch (MainProfileDeletionException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json(['message' => 'Perfil eliminado']);
    }
```

- [ ] **Step 6: Correr el test y verificar que pasa**

Run: `php artisan test --filter=MainProfileProtectionTest`
Expected: PASS (2 tests)

- [ ] **Step 7: Commit**

```bash
cd ophi-back
git add app/Exceptions/MainProfileDeletionException.php app/Services/ProfileService.php app/Http/Controllers/Api/ProfileController.php tests/Feature/Profile/MainProfileProtectionTest.php
git commit -m "feat: impedir la eliminación del perfil principal"
```

---

### Task 4: Crear el perfil principal al registrarse

`AuthService::register()` no crea ningún perfil hoy. Por eso ningún usuario tiene `is_main`.

**Files:**
- Modify: `ophi-back/app/Services/AuthService.php:60-95`
- Test: `ophi-back/tests/Feature/Auth/RegisterCreatesMainProfileTest.php`

**Interfaces:**
- Consumes: modelo `Profile`.
- Produces: todo usuario registrado tiene un `Profile` con `is_main = true` y `name` igual al del usuario.

- [ ] **Step 1: Escribir el test que falla**

Crear `tests/Feature/Auth/RegisterCreatesMainProfileTest.php`:

```php
<?php

namespace Tests\Feature\Auth;

use App\Models\Profile;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterCreatesMainProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_el_registro_crea_el_perfil_principal(): void
    {
        $user = AuthService::register([
            'email' => 'nuevo@ophi.test',
            'password' => 'password',
        ], 'Gonzalo');

        $profile = Profile::where('owner_id', $user->id)->where('is_main', true)->first();

        $this->assertNotNull($profile);
        $this->assertSame('Gonzalo', $profile->name);
    }

    public function test_el_usuario_registrado_tiene_exactamente_un_perfil_principal(): void
    {
        $user = AuthService::register([
            'email' => 'otro@ophi.test',
            'password' => 'password',
        ], 'Ana');

        $this->assertSame(1, Profile::where('owner_id', $user->id)->where('is_main', true)->count());
    }
}
```

- [ ] **Step 2: Correr el test y verificar que falla**

Run: `php artisan test --filter=RegisterCreatesMainProfileTest`
Expected: FAIL con `Failed asserting that null is not null`

- [ ] **Step 3: Crear el perfil dentro de la transacción existente**

En `app/Services/AuthService.php`, agregar el import:

```php
use App\Models\Profile;
```

Dentro del `DB::transaction` de `register()`, justo antes de `return $user;`, agregar:

```php
            Profile::create([
                'name' => $name,
                'owner_id' => $user->id,
                'user_id' => $user->id,
                'is_main' => true,
            ]);
```

- [ ] **Step 4: Correr el test y verificar que pasa**

Run: `php artisan test --filter=RegisterCreatesMainProfileTest`
Expected: PASS (2 tests)

- [ ] **Step 5: Commit**

```bash
cd ophi-back
git add app/Services/AuthService.php tests/Feature/Auth/RegisterCreatesMainProfileTest.php
git commit -m "feat: crear el perfil principal al registrar un usuario"
```

---

### Task 5: Backfill del perfil principal

Los 16 usuarios existentes no tienen perfil principal. Esta migración de datos lo resuelve.

**Files:**
- Create: `ophi-back/database/migrations/2026_08_05_000002_backfill_main_profiles.php`
- Test: `ophi-back/tests/Feature/Profile/BackfillMainProfilesTest.php`

**Interfaces:**
- Consumes: nada.
- Produces: invariante garantizado sobre los datos existentes.

- [ ] **Step 1: Escribir el test que falla**

Crear `tests/Feature/Profile/BackfillMainProfilesTest.php`:

```php
<?php

namespace Tests\Feature\Profile;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class BackfillMainProfilesTest extends TestCase
{
    use RefreshDatabase;

    public function test_marca_el_perfil_mas_antiguo_como_principal(): void
    {
        $user = User::factory()->create();
        $viejo = Profile::factory()->forOwner($user)->create(['created_at' => now()->subDays(5)]);
        Profile::factory()->forOwner($user)->create(['created_at' => now()]);

        Profile::query()->update(['is_main' => false]);

        Artisan::call('migrate', ['--path' => 'database/migrations/2026_08_05_000002_backfill_main_profiles.php', '--force' => true]);

        $this->assertTrue($viejo->fresh()->is_main);
        $this->assertSame(1, Profile::where('owner_id', $user->id)->where('is_main', true)->count());
    }

    public function test_crea_un_perfil_para_el_usuario_que_no_tiene_ninguno(): void
    {
        $user = User::factory()->create(['name' => 'Sin Perfiles']);

        Artisan::call('migrate', ['--path' => 'database/migrations/2026_08_05_000002_backfill_main_profiles.php', '--force' => true]);

        $profile = Profile::where('owner_id', $user->id)->where('is_main', true)->first();

        $this->assertNotNull($profile);
        $this->assertSame('Sin Perfiles', $profile->name);
    }
}
```

- [ ] **Step 2: Correr el test y verificar que falla**

Run: `php artisan test --filter=BackfillMainProfilesTest`
Expected: FAIL — la migración no existe.

- [ ] **Step 3: Escribir la migración**

Crear `database/migrations/2026_08_05_000002_backfill_main_profiles.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Garantiza que todo usuario tenga exactamente un perfil con is_main = 1.
     * Marca el perfil más antiguo; si el usuario no tiene ninguno, lo crea.
     */
    public function up(): void
    {
        $users = DB::table('users')->select('id', 'name')->get();

        foreach ($users as $user) {
            $alreadyHasMain = DB::table('profiles')
                ->where('owner_id', $user->id)
                ->where('is_main', true)
                ->exists();

            if ($alreadyHasMain) {
                continue;
            }

            $oldest = DB::table('profiles')
                ->where('owner_id', $user->id)
                ->orderBy('created_at')
                ->orderBy('id')
                ->first();

            if ($oldest) {
                DB::table('profiles')->where('id', $oldest->id)->update(['is_main' => true]);

                continue;
            }

            DB::table('profiles')->insert([
                'name' => $user->name,
                'owner_id' => $user->id,
                'user_id' => $user->id,
                'is_main' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        // Los datos generados no se revierten: desmarcar is_main volvería a dejar
        // usuarios sin perfil principal, que es el estado defectuoso que corrige.
    }
};
```

- [ ] **Step 4: Correr el test y verificar que pasa**

Run: `php artisan test --filter=BackfillMainProfilesTest`
Expected: PASS (2 tests)

- [ ] **Step 5: Aplicar en desarrollo y verificar los datos reales**

```bash
php artisan migrate
php artisan tinker --execute="echo 'usuarios sin perfil principal: ' . \App\Models\User::whereDoesntHave('profiles', fn(\$q) => \$q->where('is_main',1))->count() . PHP_EOL;"
```

Expected: `usuarios sin perfil principal: 0`

- [ ] **Step 6: Commit**

```bash
cd ophi-back
git add database/migrations/2026_08_05_000002_backfill_main_profiles.php tests/Feature/Profile/BackfillMainProfilesTest.php
git commit -m "feat: backfill del perfil principal para usuarios existentes"
```

---

### Task 6: Solicitud de cambio de email

**Files:**
- Create: `ophi-back/database/migrations/2026_08_05_000003_create_email_change_requests_table.php`
- Create: `ophi-back/app/Models/EmailChangeRequest.php`
- Create: `ophi-back/app/Services/EmailChangeService.php`
- Create: `ophi-back/app/Notifications/EmailChangeVerificationNotification.php`
- Create: `ophi-back/app/Notifications/EmailChangeRequestedNotification.php`
- Create: `ophi-back/app/Http/Controllers/Api/AccountController.php`
- Modify: `ophi-back/routes/api.php`
- Test: `ophi-back/tests/Feature/Account/RequestEmailChangeTest.php`

**Interfaces:**
- Consumes: modelo `User`.
- Produces: `EmailChangeService::requestChange(User $user, string $newEmail, string $currentPassword): EmailChangeRequest` — lanza `ValidationException` si la contraseña es incorrecta, si el email está en uso o si coincide con el actual. `EmailChangeRequest` con campos `user_id`, `new_email`, `token`, `expires_at`.

- [ ] **Step 1: Escribir los tests que fallan**

Crear `tests/Feature/Account/RequestEmailChangeTest.php`:

```php
<?php

namespace Tests\Feature\Account;

use App\Models\User;
use App\Notifications\EmailChangeRequestedNotification;
use App\Notifications\EmailChangeVerificationNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class RequestEmailChangeTest extends TestCase
{
    use RefreshDatabase;

    public function test_crea_la_solicitud_sin_cambiar_el_email_actual(): void
    {
        Notification::fake();
        $user = User::factory()->create(['email' => 'viejo@ophi.test']);

        $response = $this->actingAs($user)->putJson('/api/account/email', [
            'new_email' => 'nuevo@ophi.test',
            'current_password' => 'password',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('email_change_requests', [
            'user_id' => $user->id,
            'new_email' => 'nuevo@ophi.test',
        ]);
        $this->assertSame('viejo@ophi.test', $user->fresh()->email);
    }

    public function test_envia_el_link_al_nuevo_y_el_aviso_al_anterior(): void
    {
        Notification::fake();
        $user = User::factory()->create(['email' => 'viejo@ophi.test']);

        $this->actingAs($user)->putJson('/api/account/email', [
            'new_email' => 'nuevo@ophi.test',
            'current_password' => 'password',
        ]);

        Notification::assertSentOnDemand(EmailChangeVerificationNotification::class);
        Notification::assertSentTo($user, EmailChangeRequestedNotification::class);
    }

    public function test_rechaza_una_contrasena_incorrecta(): void
    {
        Notification::fake();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->putJson('/api/account/email', [
            'new_email' => 'nuevo@ophi.test',
            'current_password' => 'incorrecta',
        ]);

        $response->assertStatus(422);
        $this->assertDatabaseCount('email_change_requests', 0);
    }

    public function test_rechaza_un_email_ya_registrado(): void
    {
        Notification::fake();
        $user = User::factory()->create();
        User::factory()->create(['email' => 'ocupado@ophi.test']);

        $response = $this->actingAs($user)->putJson('/api/account/email', [
            'new_email' => 'ocupado@ophi.test',
            'current_password' => 'password',
        ]);

        $response->assertStatus(422);
        $this->assertDatabaseCount('email_change_requests', 0);
    }

    public function test_una_solicitud_nueva_invalida_la_anterior(): void
    {
        Notification::fake();
        $user = User::factory()->create();

        $this->actingAs($user)->putJson('/api/account/email', [
            'new_email' => 'primero@ophi.test',
            'current_password' => 'password',
        ]);
        $this->actingAs($user)->putJson('/api/account/email', [
            'new_email' => 'segundo@ophi.test',
            'current_password' => 'password',
        ]);

        $this->assertDatabaseCount('email_change_requests', 1);
        $this->assertDatabaseHas('email_change_requests', ['new_email' => 'segundo@ophi.test']);
    }
}
```

- [ ] **Step 2: Correr los tests y verificar que fallan**

Run: `php artisan test --filter=RequestEmailChangeTest`
Expected: FAIL con 404 — la ruta no existe.

- [ ] **Step 3: Crear la migración**

Crear `database/migrations/2026_08_05_000003_create_email_change_requests_table.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_change_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('new_email');
            $table->string('token');
            $table->timestamp('expires_at');
            $table->timestamps();

            $table->index('token');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_change_requests');
    }
};
```

- [ ] **Step 4: Crear el modelo**

Crear `app/Models/EmailChangeRequest.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailChangeRequest extends Model
{
    protected $fillable = [
        'user_id',
        'new_email',
        'token',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }
}
```

- [ ] **Step 5: Crear las notificaciones**

Crear `app/Notifications/EmailChangeVerificationNotification.php`:

```php
<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailChangeVerificationNotification extends Notification
{
    public function __construct(public string $token)
    {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $url = rtrim(config('app.spa_url'), '/') . '/confirmar-email/' . $this->token;

        return (new MailMessage)
            ->subject('Confirmá tu nuevo email | Ophi')
            ->greeting('Hola 👋')
            ->line('Recibimos una solicitud para cambiar el email de tu cuenta de Ophi. Para confirmarlo, ingresá al siguiente enlace:')
            ->action('Confirmar mi email', $url)
            ->line('Este enlace expirará en 60 minutos.')
            ->line('Hasta que lo confirmes, seguís ingresando con tu email anterior.')
            ->salutation('¡Te saluda el equipo de Ophi!');
    }
}
```

Crear `app/Notifications/EmailChangeRequestedNotification.php`:

```php
<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailChangeRequestedNotification extends Notification
{
    public function __construct(public string $newEmail)
    {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Se solicitó un cambio de email | Ophi')
            ->greeting('Hola 👋')
            ->line('Alguien solicitó cambiar el email de tu cuenta de Ophi a ' . $this->newEmail . '.')
            ->line('Si fuiste vos, revisá esa casilla para confirmar el cambio.')
            ->line('Si no fuiste vos, cambiá tu contraseña cuanto antes: alguien podría tener acceso a tu cuenta.')
            ->salutation('¡Te saluda el equipo de Ophi!');
    }
}
```

- [ ] **Step 6: Crear el service**

Crear `app/Services/EmailChangeService.php`:

```php
<?php

namespace App\Services;

use App\Models\EmailChangeRequest;
use App\Models\User;
use App\Notifications\EmailChangeRequestedNotification;
use App\Notifications\EmailChangeVerificationNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class EmailChangeService
{
    private const EXPIRATION_MINUTES = 60;

    public function requestChange(User $user, string $newEmail, string $currentPassword): EmailChangeRequest
    {
        if (! Hash::check($currentPassword, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'La contraseña no es correcta',
            ]);
        }

        if (strcasecmp($newEmail, $user->email) === 0) {
            throw ValidationException::withMessages([
                'new_email' => 'Ese ya es tu email actual',
            ]);
        }

        if (User::where('email', $newEmail)->exists()) {
            throw ValidationException::withMessages([
                'new_email' => 'Ese email ya está registrado',
            ]);
        }

        $plainToken = Str::random(64);

        $request = DB::transaction(function () use ($user, $newEmail, $plainToken) {
            EmailChangeRequest::where('user_id', $user->id)->delete();

            return EmailChangeRequest::create([
                'user_id' => $user->id,
                'new_email' => $newEmail,
                'token' => hash('sha256', $plainToken),
                'expires_at' => now()->addMinutes(self::EXPIRATION_MINUTES),
            ]);
        });

        Notification::route('mail', $newEmail)
            ->notify(new EmailChangeVerificationNotification($plainToken));

        $user->notify(new EmailChangeRequestedNotification($newEmail));

        return $request;
    }
}
```

- [ ] **Step 7: Crear el controller**

Crear `app/Http/Controllers/Api/AccountController.php`:

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\EmailChangeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function __construct(private EmailChangeService $emailChangeService)
    {
    }

    public function updateEmail(Request $request): JsonResponse
    {
        $data = $request->validate([
            'new_email' => 'required|email|max:255',
            'current_password' => 'required|string',
        ], [
            'new_email.required' => 'El email es obligatorio',
            'new_email.email' => 'El email no tiene un formato válido',
            'current_password.required' => 'Necesitamos tu contraseña actual para confirmar el cambio',
        ]);

        $this->emailChangeService->requestChange(
            $request->user(),
            $data['new_email'],
            $data['current_password']
        );

        return response()->json([
            'message' => 'Te enviamos un mail a ' . $data['new_email'] . ' para confirmar el cambio',
        ]);
    }
}
```

- [ ] **Step 8: Registrar la ruta**

En `routes/api.php`, agregar el import junto a los demás controllers:

```php
use App\Http\Controllers\Api\AccountController;
```

Y agregar el bloque después de `/** PROFILES */` (líneas 36-40):

```php
/** ACCOUNT */
Route::middleware(['auth:sanctum'])
    ->prefix('account')
    ->group(function () {
        Route::put('/email', [AccountController::class, 'updateEmail'])->middleware('throttle:6,1');
    });
```

- [ ] **Step 9: Correr los tests y verificar que pasan**

Run: `php artisan test --filter=RequestEmailChangeTest`
Expected: PASS (5 tests)

- [ ] **Step 10: Commit**

```bash
cd ophi-back
git add database/migrations/2026_08_05_000003_create_email_change_requests_table.php app/Models/EmailChangeRequest.php app/Services/EmailChangeService.php app/Notifications/EmailChangeVerificationNotification.php app/Notifications/EmailChangeRequestedNotification.php app/Http/Controllers/Api/AccountController.php routes/api.php tests/Feature/Account/RequestEmailChangeTest.php
git commit -m "feat: solicitud de cambio de email con verificación por token"
```

---

### Task 7: Confirmación del cambio de email

**Files:**
- Modify: `ophi-back/app/Services/EmailChangeService.php`
- Modify: `ophi-back/app/Http/Controllers/Api/AccountController.php`
- Modify: `ophi-back/routes/api.php`
- Test: `ophi-back/tests/Feature/Account/ConfirmEmailChangeTest.php`

**Interfaces:**
- Consumes: `EmailChangeRequest` y `EmailChangeService` de la Task 6.
- Produces: `EmailChangeService::confirm(string $plainToken): User` — lanza `InvalidEmailChangeTokenException` si el token no existe o venció.

- [ ] **Step 1: Escribir los tests que fallan**

Crear `tests/Feature/Account/ConfirmEmailChangeTest.php`:

```php
<?php

namespace Tests\Feature\Account;

use App\Models\EmailChangeRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConfirmEmailChangeTest extends TestCase
{
    use RefreshDatabase;

    private function crearSolicitud(User $user, string $newEmail, string $plainToken, ?\DateTimeInterface $expiresAt = null): void
    {
        EmailChangeRequest::create([
            'user_id' => $user->id,
            'new_email' => $newEmail,
            'token' => hash('sha256', $plainToken),
            'expires_at' => $expiresAt ?? now()->addHour(),
        ]);
    }

    public function test_confirma_y_cambia_el_email(): void
    {
        $user = User::factory()->create(['email' => 'viejo@ophi.test']);
        $this->crearSolicitud($user, 'nuevo@ophi.test', 'token-valido');

        $response = $this->postJson('/api/account/email/confirm/token-valido');

        $response->assertOk();
        $this->assertSame('nuevo@ophi.test', $user->fresh()->email);
        $this->assertDatabaseCount('email_change_requests', 0);
    }

    public function test_rechaza_un_token_vencido(): void
    {
        $user = User::factory()->create(['email' => 'viejo@ophi.test']);
        $this->crearSolicitud($user, 'nuevo@ophi.test', 'token-vencido', now()->subMinute());

        $response = $this->postJson('/api/account/email/confirm/token-vencido');

        $response->assertStatus(410);
        $this->assertSame('viejo@ophi.test', $user->fresh()->email);
    }

    public function test_rechaza_un_token_inexistente(): void
    {
        $user = User::factory()->create(['email' => 'viejo@ophi.test']);

        $response = $this->postJson('/api/account/email/confirm/token-inventado');

        $response->assertStatus(410);
        $this->assertSame('viejo@ophi.test', $user->fresh()->email);
    }

    public function test_rechaza_el_cambio_si_el_email_fue_tomado_mientras_tanto(): void
    {
        $user = User::factory()->create(['email' => 'viejo@ophi.test']);
        $this->crearSolicitud($user, 'ocupado@ophi.test', 'token-valido');
        User::factory()->create(['email' => 'ocupado@ophi.test']);

        $response = $this->postJson('/api/account/email/confirm/token-valido');

        $response->assertStatus(410);
        $this->assertSame('viejo@ophi.test', $user->fresh()->email);
    }
}
```

- [ ] **Step 2: Correr los tests y verificar que fallan**

Run: `php artisan test --filter=ConfirmEmailChangeTest`
Expected: FAIL con 404 — la ruta no existe.

- [ ] **Step 3: Crear la excepción**

Crear `app/Exceptions/InvalidEmailChangeTokenException.php`:

```php
<?php

namespace App\Exceptions;

use Exception;

class InvalidEmailChangeTokenException extends Exception
{
    public function __construct(string $message = 'El enlace no es válido o expiró. Solicitá el cambio nuevamente.')
    {
        parent::__construct($message);
    }
}
```

- [ ] **Step 4: Agregar confirm() al service**

En `app/Services/EmailChangeService.php`, agregar el import:

```php
use App\Exceptions\InvalidEmailChangeTokenException;
```

Y el método:

```php
    public function confirm(string $plainToken): User
    {
        $request = EmailChangeRequest::where('token', hash('sha256', $plainToken))->first();

        if (! $request || $request->isExpired()) {
            throw new InvalidEmailChangeTokenException();
        }

        if (User::where('email', $request->new_email)->exists()) {
            throw new InvalidEmailChangeTokenException('Ese email ya está registrado por otra cuenta.');
        }

        return DB::transaction(function () use ($request) {
            $user = $request->user;
            $user->email = $request->new_email;
            $user->save();

            $request->delete();

            return $user;
        });
    }
```

- [ ] **Step 5: Agregar el endpoint al controller**

En `app/Http/Controllers/Api/AccountController.php`, agregar el import y el método:

```php
use App\Exceptions\InvalidEmailChangeTokenException;
```

```php
    public function confirmEmail(string $token): JsonResponse
    {
        try {
            $user = $this->emailChangeService->confirm($token);
        } catch (InvalidEmailChangeTokenException $e) {
            return response()->json(['message' => $e->getMessage()], 410);
        }

        return response()->json([
            'message' => 'Tu email fue actualizado',
            'email' => $user->email,
        ]);
    }
```

- [ ] **Step 6: Registrar la ruta pública**

En `routes/api.php`, **fuera** del grupo con `auth:sanctum`, justo después del bloque ACCOUNT:

```php
Route::post('/account/email/confirm/{token}', [AccountController::class, 'confirmEmail'])
    ->middleware('throttle:10,1');
```

Es pública porque el usuario puede abrir el enlace desde un dispositivo sin sesión iniciada.

- [ ] **Step 7: Correr los tests y verificar que pasan**

Run: `php artisan test --filter=ConfirmEmailChangeTest`
Expected: PASS (4 tests)

- [ ] **Step 8: Commit**

```bash
cd ophi-back
git add app/Exceptions/InvalidEmailChangeTokenException.php app/Services/EmailChangeService.php app/Http/Controllers/Api/AccountController.php routes/api.php tests/Feature/Account/ConfirmEmailChangeTest.php
git commit -m "feat: confirmación del cambio de email por token"
```

---

### Task 8: Switch de mailing

**Files:**
- Create: `ophi-back/database/migrations/2026_08_05_000004_add_user_id_to_newsletter_table.php`
- Modify: `ophi-back/app/Models/NewsletterSubscriber.php`
- Modify: `ophi-back/app/Services/NewsletterSubscriberService.php`
- Modify: `ophi-back/app/Services/EmailChangeService.php`
- Modify: `ophi-back/app/Http/Controllers/Api/AccountController.php`
- Modify: `ophi-back/routes/api.php`
- Test: `ophi-back/tests/Feature/Account/NewsletterToggleTest.php`

**Interfaces:**
- Consumes: `EmailChangeService::confirm()` de la Task 7.
- Produces: `NewsletterSubscriberService::subscribe(string $email, ?User $user = null): NewsletterSubscriber`, `unsubscribe(string $email, ?User $user = null): ?NewsletterSubscriber`, `isSubscribed(User $user): bool`.

- [ ] **Step 1: Escribir los tests que fallan**

Crear `tests/Feature/Account/NewsletterToggleTest.php`:

```php
<?php

namespace Tests\Feature\Account;

use App\Models\EmailChangeRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsletterToggleTest extends TestCase
{
    use RefreshDatabase;

    public function test_suscribe_al_usuario(): void
    {
        $user = User::factory()->create(['email' => 'yo@ophi.test']);

        $response = $this->actingAs($user)->putJson('/api/account/newsletter', ['subscribed' => true]);

        $response->assertOk();
        $this->assertDatabaseHas('newsletter', [
            'email' => 'yo@ophi.test',
            'user_id' => $user->id,
            'status' => 'subscribed',
        ]);
    }

    public function test_desuscribe_al_usuario(): void
    {
        $user = User::factory()->create(['email' => 'yo@ophi.test']);
        $this->actingAs($user)->putJson('/api/account/newsletter', ['subscribed' => true]);

        $response = $this->actingAs($user)->putJson('/api/account/newsletter', ['subscribed' => false]);

        $response->assertOk();
        $this->assertDatabaseHas('newsletter', [
            'email' => 'yo@ophi.test',
            'status' => 'unsubscribed',
        ]);
    }

    public function test_el_cambio_de_email_arrastra_la_suscripcion(): void
    {
        $user = User::factory()->create(['email' => 'viejo@ophi.test']);
        $this->actingAs($user)->putJson('/api/account/newsletter', ['subscribed' => true]);

        EmailChangeRequest::create([
            'user_id' => $user->id,
            'new_email' => 'nuevo@ophi.test',
            'token' => hash('sha256', 'token-valido'),
            'expires_at' => now()->addHour(),
        ]);

        $this->postJson('/api/account/email/confirm/token-valido');

        $this->assertDatabaseHas('newsletter', [
            'user_id' => $user->id,
            'email' => 'nuevo@ophi.test',
        ]);
        $this->assertDatabaseMissing('newsletter', ['email' => 'viejo@ophi.test']);
    }
}
```

- [ ] **Step 2: Correr los tests y verificar que fallan**

Run: `php artisan test --filter=NewsletterToggleTest`
Expected: FAIL con 404 — la ruta no existe.

- [ ] **Step 3: Crear la migración**

Crear `database/migrations/2026_08_05_000004_add_user_id_to_newsletter_table.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('newsletter', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('newsletter', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
```

- [ ] **Step 4: Actualizar el modelo**

En `app/Models/NewsletterSubscriber.php`, asegurar que `$fillable` incluya los campos usados:

```php
    protected $fillable = [
        'user_id',
        'email',
        'status',
        'subscribed_at',
        'unsubscribed_at',
        'last_sent_at',
    ];
```

- [ ] **Step 5: Ampliar el service de newsletter**

Reemplazar el contenido de `app/Services/NewsletterSubscriberService.php`:

```php
<?php

namespace App\Services;

use App\Models\NewsletterSubscriber;
use App\Models\User;

class NewsletterSubscriberService
{
    public function subscribe(string $email, ?User $user = null): NewsletterSubscriber
    {
        $subscriber = $this->findFor($email, $user) ?? new NewsletterSubscriber();

        $subscriber->email = $email;
        $subscriber->user_id = $user?->id ?? $subscriber->user_id;
        $subscriber->status = 'subscribed';
        $subscriber->subscribed_at = now();
        $subscriber->unsubscribed_at = null;
        $subscriber->save();

        return $subscriber;
    }

    public function unsubscribe(string $email, ?User $user = null): ?NewsletterSubscriber
    {
        $subscriber = $this->findFor($email, $user);

        if (! $subscriber) {
            return null;
        }

        $subscriber->status = 'unsubscribed';
        $subscriber->unsubscribed_at = now();
        $subscriber->save();

        return $subscriber;
    }

    public function isSubscribed(User $user): bool
    {
        $subscriber = $this->findFor($user->email, $user);

        return $subscriber?->status === 'subscribed';
    }

    /**
     * Prioriza el vínculo por usuario; cae al email para quienes se
     * suscribieron antes de tener cuenta.
     */
    private function findFor(string $email, ?User $user): ?NewsletterSubscriber
    {
        if ($user) {
            $byUser = NewsletterSubscriber::where('user_id', $user->id)->first();

            if ($byUser) {
                return $byUser;
            }
        }

        return NewsletterSubscriber::where('email', $email)->first();
    }
}
```

- [ ] **Step 6: Adaptar el controller de la landing**

`NewsletterSubscriberController` invoca `NewsletterSubscriberService::subscribe()` de forma estática. Cambiarlo a inyección:

```php
    public function __construct(private NewsletterSubscriberService $newsletterService)
    {
    }
```

Y reemplazar la llamada `NewsletterSubscriberService::subscribe($email)` por `$this->newsletterService->subscribe($email)`.

- [ ] **Step 7: Arrastrar la suscripción al confirmar el email**

En `app/Services/EmailChangeService.php`, agregar el import:

```php
use App\Models\NewsletterSubscriber;
```

Y reemplazar el método `confirm()` completo por esta versión, que captura el email anterior antes de sobrescribirlo:

```php
    public function confirm(string $plainToken): User
    {
        $request = EmailChangeRequest::where('token', hash('sha256', $plainToken))->first();

        if (! $request || $request->isExpired()) {
            throw new InvalidEmailChangeTokenException();
        }

        if (User::where('email', $request->new_email)->exists()) {
            throw new InvalidEmailChangeTokenException('Ese email ya está registrado por otra cuenta.');
        }

        return DB::transaction(function () use ($request) {
            $user = $request->user;
            $oldEmail = $user->email;

            $user->email = $request->new_email;
            $user->save();

            $subscriber = NewsletterSubscriber::where('user_id', $user->id)
                ->orWhere('email', $oldEmail)
                ->first();

            if ($subscriber) {
                $subscriber->user_id = $user->id;
                $subscriber->email = $user->email;
                $subscriber->save();
            }

            $request->delete();

            return $user;
        });
    }
```

`EmailChangeService` no necesita inyectar `NewsletterSubscriberService`: acá se actualiza una fila existente, no se cambia el estado de suscripción.

- [ ] **Step 8: Agregar el endpoint**

En `AccountController`, inyectar el service y agregar el método:

```php
    public function updateNewsletter(Request $request): JsonResponse
    {
        $data = $request->validate([
            'subscribed' => 'required|boolean',
        ], [
            'subscribed.required' => 'Falta indicar si querés recibir novedades',
        ]);

        $user = $request->user();

        $data['subscribed']
            ? $this->newsletterService->subscribe($user->email, $user)
            : $this->newsletterService->unsubscribe($user->email, $user);

        return response()->json([
            'message' => $data['subscribed']
                ? 'Vas a recibir nuestras novedades'
                : 'Ya no vas a recibir novedades',
        ]);
    }
```

En `routes/api.php`, dentro del grupo `account`:

```php
        Route::put('/newsletter', [AccountController::class, 'updateNewsletter']);
```

- [ ] **Step 9: Correr los tests y verificar que pasan**

Run: `php artisan test --filter=NewsletterToggleTest`
Expected: PASS (3 tests)

- [ ] **Step 10: Correr la suite completa**

Run: `composer test`
Expected: PASS — confirma que el cambio en `NewsletterSubscriberService` no rompió la landing.

- [ ] **Step 11: Commit**

```bash
cd ophi-back
git add database/migrations/2026_08_05_000004_add_user_id_to_newsletter_table.php app/Models/NewsletterSubscriber.php app/Services/NewsletterSubscriberService.php app/Services/EmailChangeService.php app/Http/Controllers/Api/AccountController.php app/Http/Controllers/Api/NewsletterSubscriberController.php routes/api.php tests/Feature/Account/NewsletterToggleTest.php
git commit -m "feat: switch de suscripción al mailing vinculado al usuario"
```

---

### Task 9: Endpoint de estado de la cuenta

El frontend necesita una sola llamada que le diga el email actual, si hay un cambio pendiente y el estado del mailing.

**Files:**
- Modify: `ophi-back/app/Http/Controllers/Api/AccountController.php`
- Modify: `ophi-back/routes/api.php`
- Test: `ophi-back/tests/Feature/Account/ShowAccountTest.php`

**Interfaces:**
- Consumes: `NewsletterSubscriberService::isSubscribed()` de la Task 8.
- Produces: `GET /api/account` devuelve `{ email, pending_email, newsletter_subscribed }`.

- [ ] **Step 1: Escribir los tests que fallan**

Crear `tests/Feature/Account/ShowAccountTest.php`:

```php
<?php

namespace Tests\Feature\Account;

use App\Models\EmailChangeRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowAccountTest extends TestCase
{
    use RefreshDatabase;

    public function test_devuelve_el_estado_de_la_cuenta(): void
    {
        $user = User::factory()->create(['email' => 'yo@ophi.test']);

        $response = $this->actingAs($user)->getJson('/api/account');

        $response->assertOk()->assertJson([
            'email' => 'yo@ophi.test',
            'pending_email' => null,
            'newsletter_subscribed' => false,
        ]);
    }

    public function test_informa_el_email_pendiente_de_confirmacion(): void
    {
        $user = User::factory()->create(['email' => 'yo@ophi.test']);
        EmailChangeRequest::create([
            'user_id' => $user->id,
            'new_email' => 'nuevo@ophi.test',
            'token' => hash('sha256', 'algun-token'),
            'expires_at' => now()->addHour(),
        ]);

        $response = $this->actingAs($user)->getJson('/api/account');

        $response->assertOk()->assertJson(['pending_email' => 'nuevo@ophi.test']);
    }

    public function test_una_solicitud_vencida_no_cuenta_como_pendiente(): void
    {
        $user = User::factory()->create();
        EmailChangeRequest::create([
            'user_id' => $user->id,
            'new_email' => 'nuevo@ophi.test',
            'token' => hash('sha256', 'algun-token'),
            'expires_at' => now()->subMinute(),
        ]);

        $response = $this->actingAs($user)->getJson('/api/account');

        $response->assertOk()->assertJson(['pending_email' => null]);
    }
}
```

- [ ] **Step 2: Correr los tests y verificar que fallan**

Run: `php artisan test --filter=ShowAccountTest`
Expected: FAIL con 404.

- [ ] **Step 3: Agregar el método al controller**

En `app/Http/Controllers/Api/AccountController.php`, agregar el import de `EmailChangeRequest` y el método:

```php
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();

        $pending = EmailChangeRequest::where('user_id', $user->id)
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        return response()->json([
            'email' => $user->email,
            'pending_email' => $pending?->new_email,
            'newsletter_subscribed' => $this->newsletterService->isSubscribed($user),
        ]);
    }
```

- [ ] **Step 4: Registrar la ruta**

En `routes/api.php`, dentro del grupo `account`:

```php
        Route::get('/', [AccountController::class, 'show']);
```

- [ ] **Step 5: Correr los tests y verificar que pasan**

Run: `php artisan test --filter=ShowAccountTest`
Expected: PASS (3 tests)

- [ ] **Step 6: Correr la suite completa del backend**

Run: `composer test`
Expected: PASS — cierra el backend completo.

- [ ] **Step 7: Commit**

```bash
cd ophi-back
git add app/Http/Controllers/Api/AccountController.php routes/api.php tests/Feature/Account/ShowAccountTest.php
git commit -m "feat: endpoint de estado de la cuenta"
```

---

### Task 10: Service y composable del frontend

A partir de acá el trabajo es en `ophi-frontend/`, que es otro repositorio.

**Files:**
- Create: `ophi-frontend/src/services/account.js`
- Create: `ophi-frontend/src/composables/useAccount.js`
- Modify: `ophi-frontend/src/services/profiles.js`

**Interfaces:**
- Consumes: los endpoints de las tasks 6 a 9.
- Produces: `useAccount()` devuelve `{ account, loading, error, loadAccount, changeEmail, toggleNewsletter }`. `account` tiene la forma `{ email, pending_email, newsletter_subscribed }`.

- [ ] **Step 1: Crear el service**

Crear `src/services/account.js`:

```js
import axiosInstance from "../config/axios.js"

export async function getAccount() {
    const result = await axiosInstance.get('/api/account')
    return result.data
}

export async function requestEmailChange({ newEmail, currentPassword }) {
    const result = await axiosInstance.put('/api/account/email', {
        new_email: newEmail,
        current_password: currentPassword,
    })
    return result.data
}

export async function confirmEmailChange(token) {
    const result = await axiosInstance.post(`/api/account/email/confirm/${token}`)
    return result.data
}

export async function setNewsletter(subscribed) {
    const result = await axiosInstance.put('/api/account/newsletter', { subscribed })
    return result.data
}
```

- [ ] **Step 2: Crear el composable**

Crear `src/composables/useAccount.js`:

```js
import { ref } from 'vue'
import { getAccount, requestEmailChange, setNewsletter } from '../services/account.js'

export function useAccount() {
    const account = ref({ email: null, pending_email: null, newsletter_subscribed: false })
    const loading = ref(false)
    const error = ref(null)

    async function loadAccount() {
        loading.value = true
        error.value = null

        try {
            account.value = await getAccount()
        } catch (e) {
            error.value = 'No pudimos cargar los datos de tu cuenta'
        } finally {
            loading.value = false
        }
    }

    async function changeEmail({ newEmail, currentPassword }) {
        loading.value = true
        error.value = null

        try {
            const result = await requestEmailChange({ newEmail, currentPassword })
            account.value.pending_email = newEmail
            return result.message
        } catch (e) {
            error.value = firstValidationMessage(e) ?? 'No pudimos procesar el cambio de email'
            throw e
        } finally {
            loading.value = false
        }
    }

    async function toggleNewsletter(subscribed) {
        const previous = account.value.newsletter_subscribed
        account.value.newsletter_subscribed = subscribed

        try {
            const result = await setNewsletter(subscribed)
            return result.message
        } catch (e) {
            account.value.newsletter_subscribed = previous
            error.value = 'No pudimos actualizar tu preferencia de novedades'
            throw e
        }
    }

    function firstValidationMessage(e) {
        const errors = e?.response?.data?.errors
        if (!errors) return null
        const first = Object.values(errors)[0]
        return Array.isArray(first) ? first[0] : first
    }

    return { account, loading, error, loadAccount, changeEmail, toggleNewsletter }
}
```

- [ ] **Step 3: Agregar avatar_color al update de perfiles**

En `src/services/profiles.js`, `updateProfile` ya envía el objeto completo, por lo que basta con que el llamador incluya `avatar_color`. Verificar que no filtre campos:

```js
export async function updateProfile(data) {
    const result = await axiosInstance.put(`/api/profiles/${data.id}`, data)
    return result.data
}
```

- [ ] **Step 4: Verificar que compila**

Run: `npm run build`
Expected: `✓ built in ...` sin errores

- [ ] **Step 5: Commit**

```bash
cd ophi-frontend
git add src/services/account.js src/composables/useAccount.js src/services/profiles.js
git commit -m "feat: service y composable de cuenta"
```

---

### Task 11: Selector de color de avatar

**Files:**
- Create: `ophi-frontend/src/components/profile/AvatarColorPicker.vue`
- Modify: `ophi-frontend/src/components/ui/SomeUserInfo.vue`

**Interfaces:**
- Consumes: nada.
- Produces: `AvatarColorPicker` con `v-model` (string hex o `null`). `SomeUserInfo` pinta el círculo con `user.avatar_color` cuando está presente, sin prop nueva.

Los seis colores fueron elegidos sobre la identidad de Ophi y verificados contra blanco (`#FFFFFF`) con un contraste mínimo de 4.5:1.

- [ ] **Step 1: Agregar la prop de color a SomeUserInfo**

En `src/components/ui/SomeUserInfo.vue`, agregar la prop manteniendo el comportamiento actual por defecto:

```js
const props = defineProps({
    user: Object,
    showPremium: {
      type: Boolean,
      default: false,
    },
    isPremium: {
      type: Boolean,
      default: false,
    }
});
```

Reemplazar el div del círculo por:

```html
        <div
            class="circle-user roboto-slab"
            v-if="user.name"
            :style="user.avatar_color ? { backgroundColor: user.avatar_color } : null"
        >{{ props.user.name.charAt(0) }}</div>
```

Se lee de `user.avatar_color` en lugar de una prop nueva: los cuatro llamadores actuales ya pasan el objeto de perfil o usuario completo, y así ninguno necesita cambiar.

- [ ] **Step 2: Crear el selector**

Crear `src/components/profile/AvatarColorPicker.vue`:

```vue
<script setup>
const model = defineModel({ type: String, default: null })

// Contraste verificado >= 4.5:1 contra texto blanco (WCAG 2.2 AA)
const colors = [
  { value: '#005B8E', name: 'Azul' },
  { value: '#009161', name: 'Verde' },
  { value: '#9A3412', name: 'Naranja' },
  { value: '#B91C1C', name: 'Rojo' },
  { value: '#6D28D9', name: 'Violeta' },
  { value: '#374151', name: 'Gris' },
]
</script>

<template>
  <fieldset>
    <legend class="text-[#005B8E] font-semibold mb-2">Color del perfil</legend>
    <div class="flex gap-3 flex-wrap">
      <label
        v-for="color of colors"
        :key="color.value"
        class="cursor-pointer"
      >
        <input
          type="radio"
          class="sr-only peer"
          name="avatar-color"
          :value="color.value"
          v-model="model"
        />
        <span
          class="block w-10 h-10 rounded-full border-2 peer-focus-visible:ring-2 peer-focus-visible:ring-offset-2 peer-focus-visible:ring-[#005B8E]"
          :class="model === color.value ? 'border-[#005B8E] scale-110' : 'border-transparent'"
          :style="{ backgroundColor: color.value }"
        ></span>
        <span class="sr-only">{{ color.name }}</span>
      </label>
    </div>
  </fieldset>
</template>
```

El input real es un radio oculto con `sr-only`, no un div con click: así el selector es navegable por teclado y anunciable por lector de pantalla sin trabajo extra.

- [ ] **Step 3: Verificar que compila**

Run: `npm run build`
Expected: `✓ built in ...` sin errores

- [ ] **Step 4: Commit**

```bash
cd ophi-frontend
git add src/components/profile/AvatarColorPicker.vue src/components/ui/SomeUserInfo.vue
git commit -m "feat: selector de color de avatar accesible"
```

---

### Task 12: Formulario de email y switch de mailing

**Files:**
- Create: `ophi-frontend/src/components/profile/EmailChangeForm.vue`
- Create: `ophi-frontend/src/components/profile/NewsletterToggle.vue`

**Interfaces:**
- Consumes: `useAccount()` de la Task 10.
- Produces: dos componentes que reciben `account` por prop y emiten `changed` al completar una acción.

- [ ] **Step 1: Crear el formulario de email**

Crear `src/components/profile/EmailChangeForm.vue`:

```vue
<script setup>
import { ref } from 'vue'

const props = defineProps({
  account: { type: Object, required: true },
  loading: { type: Boolean, default: false },
})

const emit = defineEmits(['submit'])

const newEmail = ref('')
const currentPassword = ref('')

function handleSubmit() {
  emit('submit', { newEmail: newEmail.value, currentPassword: currentPassword.value })
  currentPassword.value = ''
}
</script>

<template>
  <section class="bg-white shadow-md p-5 rounded-[11px]">
    <h2 class="text-[#005B8E] font-semibold text-xl mb-2">Email</h2>
    <p class="mb-4">{{ props.account.email }}</p>

    <p
      v-if="props.account.pending_email"
      class="mb-4 p-3 rounded-[11px] bg-blue-50 text-[#005B8E]"
    >
      Te enviamos un mail a <strong>{{ props.account.pending_email }}</strong> para confirmar el cambio.
      Hasta que lo confirmes, seguís ingresando con tu email actual.
    </p>

    <form @submit.prevent="handleSubmit" class="grid gap-3">
      <label class="grid gap-1">
        <span class="text-sm">Nuevo email</span>
        <input
          v-model="newEmail"
          type="email"
          required
          class="border rounded-[11px] p-2"
          autocomplete="email"
        />
      </label>

      <label class="grid gap-1">
        <span class="text-sm">Tu contraseña actual</span>
        <input
          v-model="currentPassword"
          type="password"
          required
          class="border rounded-[11px] p-2"
          autocomplete="current-password"
        />
      </label>

      <button class="action-btn" :disabled="props.loading">Cambiar email</button>
    </form>
  </section>
</template>
```

- [ ] **Step 2: Crear el switch**

Crear `src/components/profile/NewsletterToggle.vue`:

```vue
<script setup>
const props = defineProps({
  subscribed: { type: Boolean, default: false },
})

const emit = defineEmits(['change'])
</script>

<template>
  <section class="bg-white shadow-md p-5 rounded-[11px]">
    <label class="flex items-center justify-between gap-4 cursor-pointer">
      <span>
        <span class="text-[#005B8E] font-semibold text-xl block">Novedades</span>
        <span class="text-sm">Recibí noticias de Ophi por email</span>
      </span>
      <input
        type="checkbox"
        class="w-6 h-6 accent-[#005B8E]"
        :checked="props.subscribed"
        @change="emit('change', $event.target.checked)"
      />
    </label>
  </section>
</template>
```

- [ ] **Step 3: Verificar que compila**

Run: `npm run build`
Expected: `✓ built in ...` sin errores

- [ ] **Step 4: Commit**

```bash
cd ophi-frontend
git add src/components/profile/EmailChangeForm.vue src/components/profile/NewsletterToggle.vue
git commit -m "feat: formulario de cambio de email y switch de novedades"
```

---

### Task 13: Integrar la gestión en ProfileView

**Files:**
- Modify: `ophi-frontend/src/views/ProfileView.vue`

**Interfaces:**
- Consumes: `useAccount()`, `AvatarColorPicker`, `EmailChangeForm`, `NewsletterToggle`, `updateProfile()`.
- Produces: la pestaña "MI PERFIL" muestra y edita nombre, color, email y novedades.

- [ ] **Step 1: Importar y montar el composable**

En el `<script setup>` de `ProfileView.vue`, agregar:

```js
import AvatarColorPicker from "../components/profile/AvatarColorPicker.vue";
import EmailChangeForm from "../components/profile/EmailChangeForm.vue";
import NewsletterToggle from "../components/profile/NewsletterToggle.vue";
import { useAccount } from "../composables/useAccount.js";
import { updateProfile } from "../services/profiles.js";

const { account, loading: accountLoading, loadAccount, changeEmail, toggleNewsletter } = useAccount();

const avatarColor = ref(null);
const profileName = ref('');
```

Dentro de `onMounted`, después de resolver `myProfile`, agregar:

```js
  loadAccount();

  if (myProfile.value.length > 0) {
    avatarColor.value = myProfile.value[0].avatar_color ?? null;
    profileName.value = myProfile.value[0].name;
  }
```

- [ ] **Step 2: Agregar los handlers**

```js
async function handleSaveProfile() {
  try {
    await updateProfile({
      id: myProfile.value[0].id,
      name: profileName.value,
      avatar_color: avatarColor.value,
    });
    myProfile.value[0].avatar_color = avatarColor.value;
    myProfile.value[0].name = profileName.value;
    feedback.value = { message: 'Perfil guardado', type: 'success' };
  } catch (error) {
    serverError.value = 'No pudimos guardar los cambios';
  } finally {
    setTimeout(() => feedback.value = { message: null, type: null }, 2000);
  }
}

async function handleChangeEmail({ newEmail, currentPassword }) {
  try {
    const message = await changeEmail({ newEmail, currentPassword });
    feedback.value = { message, type: 'success' };
  } catch (error) {
    feedback.value = { message: null, type: null };
  }
}

async function handleToggleNewsletter(subscribed) {
  try {
    const message = await toggleNewsletter(subscribed);
    feedback.value = { message, type: 'success' };
  } catch (error) {
    feedback.value = { message: null, type: null };
  } finally {
    setTimeout(() => feedback.value = { message: null, type: null }, 2000);
  }
}
```

- [ ] **Step 3: Reemplazar el bloque de la pestaña MI PERFIL**

Sustituir el bloque `<div v-else-if="myProfile.length > 0">` completo (líneas 208-218 del archivo original) por:

```html
      <div v-else-if="myProfile.length > 0" class="grid gap-4">
        <section class="bg-white shadow-md p-5 rounded-[11px] grid gap-4">
          <h2 class="text-[#005B8E] font-semibold text-xl">Mi perfil</h2>

          <label class="grid gap-1">
            <span class="text-sm">Nombre</span>
            <input v-model="profileName" type="text" class="border rounded-[11px] p-2" required />
          </label>

          <AvatarColorPicker v-model="avatarColor" />

          <button class="action-btn" type="button" @click="handleSaveProfile">Guardar</button>
        </section>

        <div class="bg-white shadow-md p-5 flex justify-between rounded-[11px]">
          <div>
            <h2 class="text-[#005B8E] font-semibold text-xl mb-2">Restricción alimenticia</h2>
            <p>{{ myProfile[0].ingredients.slice(0, 2).map(i => i.name).join(', ') }}...</p>
          </div>
          <RouterLink :to="`/profile/${myProfile[0].id}/edit`">
            <i class="fa-solid fa-pen-to-square text-[#005B8E] text-2xl"></i>
          </RouterLink>
        </div>

        <EmailChangeForm
          :account="account"
          :loading="accountLoading"
          @submit="handleChangeEmail"
        />

        <NewsletterToggle
          :subscribed="account.newsletter_subscribed"
          @change="handleToggleNewsletter"
        />
      </div>
```

- [ ] **Step 4: Verificar que compila**

Run: `npm run build`
Expected: `✓ built in ...` sin errores

- [ ] **Step 5: Verificación manual**

Con el backend corriendo (`composer dev`) y el frontend (`npm run dev`):

1. Iniciar sesión y entrar a `/profile`, pestaña "MI PERFIL".
2. Cambiar el nombre y elegir un color. Guardar. Recargar y confirmar que persiste.
3. Activar el switch de novedades. Recargar y confirmar que sigue activo.
4. Pedir un cambio de email con la contraseña correcta. Confirmar que aparece el aviso de pendiente.
5. Buscar el mail en `ophi-back/storage/logs/laravel.log` y copiar el enlace.

- [ ] **Step 6: Commit**

```bash
cd ophi-frontend
git add src/views/ProfileView.vue
git commit -m "feat: gestión del perfil principal en la pestaña MI PERFIL"
```

---

### Task 14: Pantalla de confirmación de email

**Files:**
- Create: `ophi-frontend/src/views/ConfirmEmailView.vue`
- Modify: `ophi-frontend/src/router.js`

**Interfaces:**
- Consumes: `confirmEmailChange(token)` de la Task 10.
- Produces: ruta `/confirmar-email/:token`, pública.

- [ ] **Step 1: Crear la vista**

Crear `src/views/ConfirmEmailView.vue`:

```vue
<script setup>
import { onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'
import { confirmEmailChange } from '../services/account.js'
import AppLoading from '../components/loadings/AppLoading.vue'

const route = useRoute()
const status = ref('loading')
const message = ref('')

onMounted(async () => {
  try {
    const result = await confirmEmailChange(route.params.token)
    status.value = 'success'
    message.value = result.message
  } catch (error) {
    status.value = 'error'
    message.value = error?.response?.data?.message ?? 'No pudimos confirmar el cambio'
  }
})
</script>

<template>
  <main class="min-h-screen flex items-center justify-center p-6">
    <div class="bg-white shadow-md p-6 rounded-[11px] text-center max-w-md w-full">
      <template v-if="status === 'loading'">
        <AppLoading />
        <p class="mt-4">Confirmando tu email...</p>
      </template>

      <template v-else-if="status === 'success'">
        <h1 class="text-[#005B8E] font-semibold text-xl mb-2">¡Listo!</h1>
        <p class="mb-4">{{ message }}</p>
        <RouterLink to="/profile" class="action-btn">Ir a mi perfil</RouterLink>
      </template>

      <template v-else>
        <h1 class="text-red-700 font-semibold text-xl mb-2">No pudimos confirmarlo</h1>
        <p class="mb-4">{{ message }}</p>
        <RouterLink to="/profile" class="action-btn">Volver a mi perfil</RouterLink>
      </template>
    </div>
  </main>
</template>
```

La confirmación se dispara con POST desde `onMounted`, no con un GET al abrir el enlace: los escáneres de enlaces de los clientes de correo no ejecutan JavaScript, así que no consumen el token.

- [ ] **Step 2: Registrar la ruta**

En `src/router.js`, agregar el import:

```js
import ConfirmEmailView from "./views/ConfirmEmailView.vue";
```

Y la ruta, antes del catch-all `/:pathMatch(.*)*`:

```js
    {
        'path': '/confirmar-email/:token',
        'component': ConfirmEmailView,
        'meta': { 'auth': false }
    },
```

Va con `auth: false` porque el usuario puede abrir el enlace desde un dispositivo sin sesión.

- [ ] **Step 3: Verificar que compila**

Run: `npm run build`
Expected: `✓ built in ...` sin errores

- [ ] **Step 4: Verificación manual del flujo completo**

1. Pedir un cambio de email desde `/profile`.
2. Abrir `ophi-back/storage/logs/laravel.log` y buscar el enlace `confirmar-email`.
3. Pegarlo en el navegador.
4. Confirmar que aparece "¡Listo!" y que el email cambió en la base:

```bash
cd ophi-back
php artisan tinker --execute="echo \App\Models\User::find(ID)->email . PHP_EOL;"
```

5. Cerrar sesión e ingresar con el email nuevo.

- [ ] **Step 5: Commit**

```bash
cd ophi-frontend
git add src/views/ConfirmEmailView.vue src/router.js
git commit -m "feat: pantalla de confirmación de cambio de email"
```

---

## Verificación final

- [ ] `cd ophi-back && composer test` — toda la suite en verde
- [ ] `cd ophi-frontend && npm run build` — compila sin errores
- [ ] Flujo manual completo: editar nombre y color, activar novedades, cambiar email y confirmarlo desde el enlace
- [ ] `php artisan tinker --execute="echo \App\Models\User::whereDoesntHave('profiles', fn(\$q) => \$q->where('is_main',1))->count();"` devuelve `0`

## Notas de entorno

`MAIL_MAILER=log` en desarrollo: los correos se escriben en `ophi-back/storage/logs/laravel.log` y no se envían. Para probar el flujo con correos reales, configurar Mailpit o Mailtrap.
