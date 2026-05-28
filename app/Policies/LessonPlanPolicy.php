<?php

namespace App\Policies;

use App\Models\LessonPlan;
use App\Models\User;

class LessonPlanPolicy
{
    /**
     * Admins can do anything. Teachers/students only own resources.
     */
    public function before(User $user): ?bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return null;
    }

    public function view(User $user, LessonPlan $lessonPlan): bool
    {
        return $lessonPlan->user_id === $user->id;
    }

    public function update(User $user, LessonPlan $lessonPlan): bool
    {
        return $lessonPlan->user_id === $user->id;
    }

    public function delete(User $user, LessonPlan $lessonPlan): bool
    {
        return $lessonPlan->user_id === $user->id;
    }
}
