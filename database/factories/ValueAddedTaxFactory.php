<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\ValueAddedTax;

class ValueAddedTaxFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ValueAddedTax::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'percentage' => $this->faker->randomFloat(0, 0, 9999999999.),
            'applies_at' => $this->faker->date(),
            'is_saudi_student_exepmted' => $this->faker->boolean(),
        ];
    }
}
