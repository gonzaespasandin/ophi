<?php

namespace Tests\Feature\Profile;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BackfillMainProfilesTest extends TestCase
{
    use RefreshDatabase;

    private function runBackfillMigration(): void
    {
        $migration = require database_path('migrations/2026_08_05_000002_backfill_main_profiles.php');
        $migration->up();
    }

    public function test_marca_el_perfil_mas_antiguo_como_principal(): void
    {
        $user = User::factory()->create();
        $viejo = Profile::factory()->forOwner($user)->create(['created_at' => now()->subDays(5)]);
        Profile::factory()->forOwner($user)->create(['created_at' => now()]);

        Profile::query()->update(['is_main' => false]);

        $this->runBackfillMigration();

        $this->assertTrue($viejo->fresh()->is_main);
        $this->assertSame(1, Profile::where('owner_id', $user->id)->where('is_main', true)->count());
    }

    public function test_crea_un_perfil_para_el_usuario_que_no_tiene_ninguno(): void
    {
        $user = User::factory()->create(['name' => 'Sin Perfiles']);

        $this->runBackfillMigration();

        $profile = Profile::where('owner_id', $user->id)->where('is_main', true)->first();

        $this->assertNotNull($profile);
        $this->assertSame('Sin Perfiles', $profile->name);
    }

    public function test_no_duplica_el_perfil_principal_si_el_usuario_ya_tiene_uno(): void
    {
        $user = User::factory()->create();
        $principal = Profile::factory()->forOwner($user)->main()->create();

        $this->runBackfillMigration();
        $this->runBackfillMigration();

        $this->assertSame(1, Profile::where('owner_id', $user->id)->where('is_main', true)->count());
        $this->assertTrue($principal->fresh()->is($principal));
        $this->assertTrue($principal->fresh()->is_main);
    }
}
