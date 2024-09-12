<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\AcademicYear;
use App\Models\Course;
use App\Models\TuitionFee;

class TuitionFeeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TuitionFee::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'academic_year_id' => AcademicYear::factory(),
            'course_id' => Course::factory(),
            'payment_partition_count' => $this->faker->numberBetween(-10000, 10000),
            'payment_partition' => '{}',
        ];
    }
}
