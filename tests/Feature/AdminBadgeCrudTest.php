<?php

namespace Tests\Feature;

use App\Models\Badge;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AdminBadgeCrudTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin user
        $this->adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'testadmin@example.com',
            'role' => 'admin',
            'password' => bcrypt('password'),
        ]);

        // Create regular teacher user
        $this->regularUser = User::create([
            'name' => 'Regular User',
            'email' => 'testteacher@example.com',
            'role' => 'teacher',
            'password' => bcrypt('password'),
        ]);
    }

    public function test_guests_and_non_admins_cannot_access_admin_badges(): void
    {
        // Guest access
        $this->get(route('admin.badges.index'))->assertRedirect();

        // Non-admin user access
        $this->actingAs($this->regularUser)
            ->get(route('admin.badges.index'))
            ->assertRedirect('/')
            ->assertSessionHas('error');
    }

    public function test_admins_can_manage_badges(): void
    {
        // 1. View Index
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.badges.index'));
        $response->assertStatus(200);

        // 2. View Create
        $this->actingAs($this->adminUser)
            ->get(route('admin.badges.create'))
            ->assertStatus(200);

        // 3. Store new badge
        $badgeData = [
            'name' => 'Test AI Badge',
            'description' => 'Verify AI engineering skills.',
            'icon' => '🤖',
            'color' => '#1a2b3c',
            'category' => 'tool_mastery',
            'difficulty' => 'silver',
            'criteria_type' => 'quiz',
            'sort_order' => 15,
        ];

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.badges.store'), $badgeData);

        $response->assertRedirect(route('admin.badges.index'));
        $this->assertDatabaseHas('badges', [
            'slug' => 'test-ai-badge',
            'difficulty' => 'silver',
        ]);

        $badge = Badge::where('slug', 'test-ai-badge')->first();

        // 4. View Edit
        $this->actingAs($this->adminUser)
            ->get(route('admin.badges.edit', $badge))
            ->assertStatus(200);

        // 5. Update badge
        $updatedData = array_merge($badgeData, [
            'name' => 'Updated Test AI Badge',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->put(route('admin.badges.update', $badge), $updatedData);

        $response->assertRedirect(route('admin.badges.index'));
        $this->assertDatabaseHas('badges', [
            'id' => $badge->id,
            'slug' => 'updated-test-ai-badge',
        ]);

        // 6. Delete badge
        $response = $this->actingAs($this->adminUser)
            ->delete(route('admin.badges.destroy', $badge));

        $response->assertRedirect(route('admin.badges.index'));
        $this->assertDatabaseMissing('badges', [
            'id' => $badge->id,
        ]);
    }

}
