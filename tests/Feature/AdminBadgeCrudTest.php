<?php

namespace Tests\Feature;

use App\Models\Badge;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AdminBadgeCrudTest extends TestCase
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

    public function test_admins_can_generate_quiz_with_ai(): void
    {
        // Create a test badge
        $badge = Badge::create([
            'name' => 'Canva Expert Test',
            'slug' => 'canva-expert-test',
            'description' => 'Test description',
            'icon' => '🎨',
            'color' => '#7D2AE8',
            'category' => 'tool_mastery',
            'difficulty' => 'bronze',
            'criteria_type' => 'quiz',
            'sort_order' => 1,
        ]);

        // Mock Gemini Response
        $mockQuestions = [
            [
                'question' => '¿Cuál es la herramienta de diseño de Canva?',
                'options' => [
                    'a' => 'Opción A',
                    'b' => 'Opción B',
                    'c' => 'Opción C',
                    'd' => 'Opción D',
                ],
                'correct' => 'a',
                'explanation' => 'Test explanation',
            ]
        ];

        Http::fake([
            'https://generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => json_encode($mockQuestions)]
                            ]
                        ]
                    ]
                ]
            ], 200)
        ]);

        // Post request to generate quiz
        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.badges.generateQuiz', $badge));

        $response->assertRedirect(route('admin.badges.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('quizzes', [
            'badge_id' => $badge->id,
            'title' => 'Quiz: Canva Expert Test',
        ]);

        $quiz = Quiz::where('badge_id', $badge->id)->first();
        $this->assertCount(1, $quiz->questions);
        $this->assertEquals('¿Cuál es la herramienta de diseño de Canva?', $quiz->questions[0]['question']);
    }
}
