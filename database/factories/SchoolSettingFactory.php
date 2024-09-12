<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\SchoolSetting;

class SchoolSettingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SchoolSetting::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'phone_number' => $this->faker->phoneNumber(),
            'website' => $this->faker->word(),
            'permit_number' => $this->faker->numberBetween(-10000, 10000),
            'commercial_register_number' => $this->faker->numberBetween(-10000, 10000),
            'added_value_tax_number' => $this->faker->numberBetween(-10000, 10000),
            'logo' => $this->faker->word(),
            'stamp' => $this->faker->word(),
            'new_registration_number_start' => $this->faker->word(),
        ];
    }
}
