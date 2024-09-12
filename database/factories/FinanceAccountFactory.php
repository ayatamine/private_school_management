<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\FinanceAccount;

class FinanceAccountFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = FinanceAccount::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'type' => $this->faker->randomElement(["bank","cash"]),
            'opening_balance' => $this->faker->randomFloat(0, 0, 9999999999.),
            'is_active' => $this->faker->boolean(),
            'bank_name' => $this->faker->word(),
            'account_number' => $this->faker->numberBetween(-100000, 100000),
            'link_with_employee_payments' => $this->faker->word(),
        ];
    }
}
