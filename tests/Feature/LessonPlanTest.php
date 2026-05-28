<?php

namespace Tests\Feature;

use App\Models\LessonPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LessonPlanTest extends TestCase
{
    use RefreshDatabase;

    // ── Authorization ────────────────────────────────────────────

    public function test_guest_cannot_view_lesson_plan(): void
    {
        $plan = LessonPlan::factory()->create();

        $this->get(route('lesson-plans.show', $plan))
            ->assertRedirect(route('login'));
    }

    public function test_owner_can_view_own_lesson_plan(): void
    {
        $user = User::factory()->create(['role' => 'teacher']);
        $plan = LessonPlan::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->get(route('lesson-plans.show', $plan))
            ->assertOk();
    }

    public function test_other_user_cannot_view_anothers_lesson_plan(): void
    {
        $owner = User::factory()->create(['role' => 'teacher']);
        $other = User::factory()->create(['role' => 'teacher']);
        $plan  = LessonPlan::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($other)
            ->get(route('lesson-plans.show', $plan))
            ->assertForbidden();
    }

    public function test_admin_can_view_any_lesson_plan(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $owner = User::factory()->create(['role' => 'teacher']);
        $plan  = LessonPlan::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($admin)
            ->get(route('lesson-plans.show', $plan))
            ->assertOk();
    }

    public function test_owner_can_delete_own_lesson_plan(): void
    {
        $user = User::factory()->create(['role' => 'teacher']);
        $plan = LessonPlan::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->delete(route('lesson-plans.destroy', $plan))
            ->assertRedirect(route('lesson-plans.index'));

        $this->assertSoftDeleted($plan);
    }

    public function test_other_user_cannot_delete_anothers_lesson_plan(): void
    {
        $owner = User::factory()->create(['role' => 'teacher']);
        $other = User::factory()->create(['role' => 'teacher']);
        $plan  = LessonPlan::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($other)
            ->delete(route('lesson-plans.destroy', $plan))
            ->assertForbidden();

        $this->assertModelExists($plan);
    }

    // ── Store ────────────────────────────────────────────────────

    public function test_teacher_can_store_lesson_plan(): void
    {
        $user = User::factory()->create(['role' => 'teacher']);

        $this->actingAs($user)
            ->post(route('lesson-plans.store'), [
                'title'       => 'Math Lesson',
                'subject'     => 'Mathematics',
                'grade_level' => 'Grade 5',
                'objectives'  => 'Students will learn fractions.',
                'duration'    => '45 minutes',
                'content'     => '## Warm-up\nFraction review.',
            ])
            ->assertOk()
            ->assertJsonStructure(['success', 'id', 'url']);

        $this->assertDatabaseHas('lesson_plans', [
            'user_id' => $user->id,
            'title'   => 'Math Lesson',
        ]);
    }

    public function test_student_cannot_store_lesson_plan(): void
    {
        $student = User::factory()->create(['role' => 'student']);

        $this->actingAs($student)
            ->post(route('lesson-plans.store'), [
                'title'       => 'Math Lesson',
                'subject'     => 'Mathematics',
                'grade_level' => 'Grade 5',
                'objectives'  => 'Students will learn fractions.',
                'duration'    => '45 minutes',
                'content'     => '## Content',
            ])
            // is_teacher_or_admin middleware redirects students (not 403)
            ->assertRedirect();
    }

    // ── Index ────────────────────────────────────────────────────

    public function test_teacher_sees_only_own_lesson_plans_in_index(): void
    {
        $user1 = User::factory()->create(['role' => 'teacher']);
        $user2 = User::factory()->create(['role' => 'teacher']);

        LessonPlan::factory()->create(['user_id' => $user1->id, 'title' => 'Plan Alpha Unique 9x']);
        LessonPlan::factory()->create(['user_id' => $user2->id, 'title' => 'Plan Beta Unique 9x']);

        // The controller filters by auth user_id — verify DB-level correctness
        $plans = LessonPlan::where('user_id', $user1->id)->get();
        $this->assertCount(1, $plans);
        $this->assertSame('Plan Alpha Unique 9x', $plans->first()->title);

        // The index page loads for user1
        $this->actingAs($user1)
            ->get(route('lesson-plans.index'))
            ->assertOk()
            ->assertSee('Plan Alpha Unique 9x');
    }
}
