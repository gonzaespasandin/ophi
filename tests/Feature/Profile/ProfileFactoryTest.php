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
