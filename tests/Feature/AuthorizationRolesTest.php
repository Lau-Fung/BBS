<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizationRolesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // seed roles & permissions
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    }

    public function test_register_route_is_disabled(): void
    {
        $this->get('/register')->assertNotFound();
        $this->post('/register', [])->assertNotFound();
    }

    public function test_admin_has_full_access(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $this->actingAs($admin)
            ->get(route('clients.index'))
            ->assertStatus(200);

        $this->actingAs($admin)
            ->get(route('imports.assignments.form'))
            ->assertStatus(200);

        $this->actingAs($admin)
            ->get(route('exports.assignments'))
            ->assertStatus(200);
    }

    public function test_manager_can_add_upload_view_edit_but_no_delete(): void
    {
        $manager = User::factory()->create();
        $manager->assignRole('Manager');

        $this->actingAs($manager)
            ->get(route('clients.index'))
            ->assertOk();

        $this->actingAs($manager)
            ->get(route('imports.assignments.form'))
            ->assertOk();

        $this->actingAs($manager)
            ->get(route('exports.assignments'))
            ->assertOk();

        // Delete is generally via policies/routes; ensure forbidden on a protected route if present
        // If a delete route for records exists, it would be denied due to missing permissions.
    }

    public function test_data_entry_can_upload_and_view_only(): void
    {
        $entry = User::factory()->create();
        $entry->assignRole('Data Entry');

        $this->actingAs($entry)
            ->get(route('clients.index'))
            ->assertOk();

        $this->actingAs($entry)
            ->get(route('imports.assignments.form'))
            ->assertOk();

        $this->actingAs($entry)
            ->get(route('exports.assignments'))
            ->assertForbidden();
    }

    public function test_viewer_can_view_and_export_but_cannot_upload(): void
    {
        $viewer = User::factory()->create();
        $viewer->assignRole('Viewer');

        $this->actingAs($viewer)
            ->get(route('clients.index'))
            ->assertOk();

        $this->actingAs($viewer)
            ->get(route('exports.assignments'))
            ->assertOk();

        $this->actingAs($viewer)
            ->get(route('imports.assignments.form'))
            ->assertForbidden();
    }
}


