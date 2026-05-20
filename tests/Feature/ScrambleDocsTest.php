<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;

class ScrambleDocsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test documentation access in local environment.
     */
    public function test_scramble_docs_accessible_to_everyone_in_local_environment()
    {
        $this->app->detectEnvironment(function () {
            return 'local';
        });

        $response = $this->get('/docs/api');

        $response->assertStatus(200);
    }

    /**
     * Test documentation access in production/testing without authentication.
     */
    public function test_scramble_docs_forbidden_for_guests_in_production_environment()
    {
        $this->app->detectEnvironment(function () {
            return 'production';
        });

        $response = $this->get('/docs/api');

        $response->assertStatus(403);
    }

    /**
     * Test documentation access in production/testing with authenticated user.
     */
    public function test_scramble_docs_accessible_for_authenticated_users_in_production_environment()
    {
        $this->app->detectEnvironment(function () {
            return 'production';
        });

        $user = User::create([
            'name' => 'Admin Developer',
            'email' => 'admin-dev@active.co.id',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->actingAs($user)->get('/docs/api');

        $response->assertStatus(200);
    }
}
