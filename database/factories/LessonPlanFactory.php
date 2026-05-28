<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LessonPlan>
 */
class LessonPlanFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'     => User::factory(),
            'title'       => $this->faker->sentence(4),
            'subject'     => $this->faker->randomElement(['Math', 'Science', 'English', 'History', 'Art']),
            'grade_level' => 'Grade ' . $this->faker->numberBetween(1, 12),
            'objectives'  => $this->faker->paragraph(),
            'duration'    => $this->faker->randomElement(['30 minutes', '45 minutes', '60 minutes', '90 minutes']),
            'content'     => "## Warm-up\n\n" . $this->faker->paragraph() . "\n\n## Activities\n\n" . $this->faker->paragraph(),
            'selected_tools' => [],
        ];
    }
}
