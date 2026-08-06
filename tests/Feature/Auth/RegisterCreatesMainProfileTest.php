<?php

namespace Tests\Feature\Auth;

use App\Models\Profile;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class RegisterCreatesMainProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        DB::table('plans')->insert([
            'id' => 1,
            'name' => 'free',
            'price_per_month' => 0,
            'price_per_year' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

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
