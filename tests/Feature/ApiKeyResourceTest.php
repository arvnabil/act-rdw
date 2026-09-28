<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Modules\Settings\Filament\Resources\ApiKeyResource\Pages\CreateApiKey;
use Modules\Settings\Filament\Resources\ApiKeyResource\Pages\EditApiKey;
use Modules\Settings\Filament\Resources\ApiKeyResource\Pages\ListApiKeys;
use Modules\Settings\Models\ApiKey;
use Tests\TestCase;

class ApiKeyResourceTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate');

        $moduleDirs = glob(base_path('Modules/*/Database/Migrations'));
        foreach ($moduleDirs as $dir) {
            $relativePath = str_replace(base_path() . '/', '', $dir);
            $this->artisan('migrate', ['--path' => $relativePath]);
        }

        $this->user = User::factory()->create();
    }

    public function test_can_render_list_api_keys_page(): void
    {
        ApiKey::create([
            'name' => 'Existing Key',
            'key' => 'act_test_key_12345678901234567890',
            'is_active' => true,
            'capabilities' => ['product.read'],
        ]);

        Livewire::actingAs($this->user)
            ->test(ListApiKeys::class)
            ->assertSuccessful()
            ->assertSee('Existing Key');
    }

    public function test_can_create_api_key_via_filament(): void
    {
        Livewire::actingAs($this->user)
            ->test(CreateApiKey::class)
            ->fillForm([
                'name' => 'Agent Key',
                'capabilities' => ['product.read', 'product.write'],
                'is_active' => true,
                'debug_mode' => false,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('api_keys', [
            'name' => 'Agent Key',
            'is_active' => true,
        ]);

        $created = ApiKey::where('name', 'Agent Key')->first();
        $this->assertNotNull($created);
        $this->assertNotEmpty($created->key);
        $this->assertContains('product.read', $created->capabilities);
        $this->assertContains('product.write', $created->capabilities);
    }

    public function test_can_render_edit_api_key_page(): void
    {
        $key = ApiKey::create([
            'name' => 'Key To Edit',
            'key' => 'act_test_edit_12345678901234567890',
            'is_active' => true,
            'capabilities' => ['news.read'],
        ]);

        Livewire::actingAs($this->user)
            ->test(EditApiKey::class, ['record' => $key->id])
            ->assertSuccessful()
            ->assertFormSet([
                'name' => 'Key To Edit',
            ]);
    }
}