<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\User;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'natinoal_id' => $this->faker->word(),
            'password' => $this->faker->password(),
            'is_admin' => $this->faker->boolean(),
            'phone_number' => $this->faker->phoneNumber(),
            'gender' => $this->faker->randomElement(["male","female"]),
        ];
    }
}
