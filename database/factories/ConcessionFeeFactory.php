<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\AcademicYear;
use App\Models\ConcessionFee;

class ConcessionFeeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ConcessionFee::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'academic_year_id' => AcademicYear::factory(),
            'name' => $this->faker->name(),
            'type' => 'value',
            'value' => $this->faker->randomFloat(0, 0, 9999999999.),
            'is_active' => $this->faker->boolean(),
        ];
    }
}
