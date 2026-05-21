<?php

namespace Tests\Feature;

use App\Models\PromptTip;
use App\Models\TaskForceMember;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AdminCrudTest extends TestCase
{
    use DatabaseTransactions;

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

    public function test_guests_and_non_admins_cannot_access_admin_panels(): void
    {
        // Guest access
        $this->get(route('admin.task-force.index'))->assertRedirect();
        $this->get(route('admin.prompt-tips.index'))->assertRedirect();

        // Non-admin user access
        $this->actingAs($this->regularUser)
            ->get(route('admin.task-force.index'))
            ->assertRedirect('/')
            ->assertSessionHas('error');

        $this->actingAs($this->regularUser)
            ->get(route('admin.prompt-tips.index'))
            ->assertRedirect('/')
            ->assertSessionHas('error');
    }

    public function test_admins_can_manage_task_force_members(): void
    {
        // 1. View Index
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.task-force.index'));
        $response->assertStatus(200);

        // 2. View Create
        $this->actingAs($this->adminUser)
            ->get(route('admin.task-force.create'))
            ->assertStatus(200);

        // 3. Store new member
        $memberData = [
            'name' => 'Test Member',
            'role' => 'Pediatric AI Specialist',
            'email' => 'test.member@ans.edu.ni',
            'description' => 'Helpful testing description for checking CRUD flow.',
            'initials' => 'TM',
            'avatar_color' => '#FF5733',
            'image_url' => 'https://example.com/avatar.png',
            'sort_order' => 5,
        ];

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.task-force.store'), $memberData);

        $response->assertRedirect(route('admin.task-force.index'));
        $this->assertDatabaseHas('task_force_members', [
            'email' => 'test.member@ans.edu.ni',
        ]);

        $member = TaskForceMember::where('email', 'test.member@ans.edu.ni')->first();

        // 4. View Edit
        $this->actingAs($this->adminUser)
            ->get(route('admin.task-force.edit', $member))
            ->assertStatus(200);

        // 5. Update member
        $updatedData = array_merge($memberData, [
            'name' => 'Updated Test Member',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->put(route('admin.task-force.update', $member), $updatedData);

        $response->assertRedirect(route('admin.task-force.index'));
        $this->assertDatabaseHas('task_force_members', [
            'id' => $member->id,
            'name' => 'Updated Test Member',
        ]);

        // 6. Delete member
        $response = $this->actingAs($this->adminUser)
            ->delete(route('admin.task-force.destroy', $member));

        $response->assertRedirect(route('admin.task-force.index'));
        $this->assertDatabaseMissing('task_force_members', [
            'id' => $member->id,
        ]);
    }

    public function test_admins_can_manage_prompt_tips(): void
    {
        // 1. View Index
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.prompt-tips.index'));
        $response->assertStatus(200);

        // 2. View Create
        $this->actingAs($this->adminUser)
            ->get(route('admin.prompt-tips.create'))
            ->assertStatus(200);

        // 3. Store new prompt tip
        $promptData = [
            'title' => 'Test Prompt Tip',
            'target_role' => 'docentes',
            'category' => 'Planificación',
            'complexity' => 'Alta',
            'description' => 'Test description details.',
            'prompt_text' => 'Actúia como tutor IA en [Tema].',
            'sort_order' => 12,
        ];

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.prompt-tips.store'), $promptData);

        $response->assertRedirect(route('admin.prompt-tips.index'));
        $this->assertDatabaseHas('prompt_tips', [
            'title' => 'Test Prompt Tip',
        ]);

        $prompt = PromptTip::where('title', 'Test Prompt Tip')->first();

        // 4. View Edit
        $this->actingAs($this->adminUser)
            ->get(route('admin.prompt-tips.edit', $prompt))
            ->assertStatus(200);

        // 5. Update prompt tip
        $updatedData = array_merge($promptData, [
            'title' => 'Updated Test Prompt Tip',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->put(route('admin.prompt-tips.update', $prompt), $updatedData);

        $response->assertRedirect(route('admin.prompt-tips.index'));
        $this->assertDatabaseHas('prompt_tips', [
            'id' => $prompt->id,
            'title' => 'Updated Test Prompt Tip',
        ]);

        // 6. Delete prompt tip
        $response = $this->actingAs($this->adminUser)
            ->delete(route('admin.prompt-tips.destroy', $prompt));

        $response->assertRedirect(route('admin.prompt-tips.index'));
        $this->assertDatabaseMissing('prompt_tips', [
            'id' => $prompt->id,
        ]);
    }
}
