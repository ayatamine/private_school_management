<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\AcademicYear;
use App\Models\Course;
use App\Models\Semester;

class SemesterFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Semester::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'academic_year_id' => AcademicYear::factory(),
            'course_id' => Course::factory(),
            'name' => $this->faker->name(),
            'max_students_number' => $this->faker->randomNumber(),
            'is_registration_active' => $this->faker->boolean(),
            'is_promotion_active' => $this->faker->boolean(),
        ];
    }
}
