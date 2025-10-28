<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleBasedAccessTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that admin users can access admin routes
     */
    public function test_admin_can_access_admin_routes(): void
    {
        $admin = User::factory()->create(['role' => 'Admin']);

        $response = $this->actingAs($admin)
            ->get('/dashboard');

        $response->assertStatus(200);
    }

    /**
     * Test that admin users can access user routes (hierarchical access)
     */
    public function test_admin_can_access_user_routes(): void
    {
        $admin = User::factory()->create(['role' => 'Admin']);

        $response = $this->actingAs($admin)
            ->get('/user/dashboard');

        $response->assertStatus(200);
    }

    /**
     * Test that alumni users cannot access admin routes
     */
    public function test_alumni_cannot_access_admin_routes(): void
    {
        $alumni = User::factory()->create(['role' => 'Alumni']);

        $response = $this->actingAs($alumni)
            ->get('/dashboard');

        $response->assertStatus(403);
    }

    /**
     * Test that alumni users can access user routes
     */
    public function test_alumni_can_access_user_routes(): void
    {
        $alumni = User::factory()->create(['role' => 'Alumni']);

        $response = $this->actingAs($alumni)
            ->get('/user/dashboard');

        $response->assertStatus(200);
    }

    /**
     * Test that alumni users cannot access account management
     */
    public function test_alumni_cannot_access_account_management(): void
    {
        $alumni = User::factory()->create(['role' => 'Alumni']);

        $response = $this->actingAs($alumni)
            ->get('/AccountManagement');

        $response->assertStatus(403);
    }

    /**
     * Test that admin users can access account management
     */
    public function test_admin_can_access_account_management(): void
    {
        $admin = User::factory()->create(['role' => 'Admin']);

        $response = $this->actingAs($admin)
            ->get('/AccountManagement');

        $response->assertStatus(200);
    }

    /**
     * Test that unauthenticated users are redirected to login
     */
    public function test_unauthenticated_users_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect();

        $response = $this->get('/user/dashboard');
        $response->assertRedirect();
    }
}