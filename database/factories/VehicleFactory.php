<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Vehicle;

class VehicleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Vehicle::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'car_name' => $this->faker->word(),
            'plate_number' => $this->faker->word(),
            'form_number' => $this->faker->word(),
            'expire_date' => $this->faker->date(),
            'insurance_name' => $this->faker->numberBetween(-100000, 100000),
            'insurance_expire_at' => $this->faker->date(),
            'periodic_inspection_expire_at' => $this->faker->date(),
            'documents' => $this->faker->text(),
        ];
    }
}
