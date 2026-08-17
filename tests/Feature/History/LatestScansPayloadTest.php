<?php

namespace Tests\Feature\History;

use App\Models\Brand;
use App\Models\Category;
use App\Models\History;
use App\Models\HistoryResult;
use App\Models\Plan;
use App\Models\Product;
use App\Models\Profile;
use App\Models\User;
use App\Services\HistoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LatestScansPayloadTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Product $product;
    private Profile $profile;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->profile = Profile::factory()->main()->forOwner($this->user)->create(['name' => 'Lucía']);

        $this->product = Product::create([
            'name' => 'Galletitas de avena y chía',
            'brand_id' => Brand::create(['name' => 'Granix'])->id,
            'category_id' => Category::create(['name' => 'Galletitas'])->id,
        ]);
    }

    private function recordScan(): History
    {
        $scan = History::create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'scanned_at' => now(),
        ]);

        HistoryResult::create([
            'scan_history_id' => $scan->id,
            'profile_id' => $this->profile->id,
            'profile_name' => $this->profile->name,
            'result' => HistoryResult::RESULT_SAFE,
            'unsafe_ingredients' => [],
        ]);

        return $scan;
    }

    public function test_incluye_la_marca_del_producto(): void
    {
        $this->recordScan();
        $this->actingAs($this->user);

        $scan = HistoryService::getLatestScans()->first()->toArray();

        // The home links each scan to /product/{name}/{brand}.
        $this->assertSame('Galletitas de avena y chía', $scan['product']['name']);
        $this->assertSame('Granix', $scan['product']['brand']['name']);
    }

    public function test_el_historial_completo_tambien_incluye_la_marca(): void
    {
        $this->recordScan();
        $this->actingAs($this->user);

        // The history screen reuses the same row component as the home.
        $scan = collect(HistoryService::index())->first()->toArray();

        $this->assertSame('Granix', $scan['product']['brand']['name']);
    }

    public function test_la_busqueda_por_nombre_tambien_incluye_la_marca(): void
    {
        $this->recordScan();

        // Searching the history is a premium-only feature.
        Plan::create(['id' => 1, 'name' => 'free']);
        $premium = Plan::create(['id' => 2, 'name' => 'premium']);
        $this->user->subscription()->create(['plan_id' => $premium->id]);

        $this->actingAs($this->user);

        $scan = collect(HistoryService::searchByName('galletitas'))->first()->toArray();

        $this->assertSame('Granix', $scan['product']['brand']['name']);
    }

    public function test_incluye_el_perfil_de_cada_resultado(): void
    {
        $this->recordScan();
        $this->actingAs($this->user);

        $scan = HistoryService::getLatestScans()->first()->toArray();

        $this->assertCount(1, $scan['results']);
        $this->assertTrue($scan['results'][0]['is_safe']);
        $this->assertSame('Lucía', $scan['results'][0]['profile']['name']);
    }

    public function test_devuelve_los_tres_escaneos_mas_recientes_primero(): void
    {
        foreach ([5, 1, 3, 2] as $daysAgo) {
            History::create([
                'user_id' => $this->user->id,
                'product_id' => $this->product->id,
                'scanned_at' => now()->subDays($daysAgo),
            ]);
        }

        $this->actingAs($this->user);

        $scans = HistoryService::getLatestScans();

        $this->assertCount(3, $scans);
        $this->assertTrue($scans->first()->scanned_at->isSameDay(now()->subDay()));
    }
}
