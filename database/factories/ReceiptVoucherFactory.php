<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\PaymentMehtod;
use App\Models\ReceiptVoucher;
use App\Models\Student;
use App\Models\User;

class ReceiptVoucherFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ReceiptVoucher::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'value' => $this->faker->randomFloat(0, 0, 9999999999.),
            'value_in_alphabetic' => $this->faker->word(),
            'document' => $this->faker->word(),
            'is_approved' => $this->faker->boolean(),
            'payment_method_id' => PaymentMehtod::factory(),
            'payment_date' => $this->faker->date(),
            'registered_by' => User::factory(),
            'user_id' => User::factory(),
        ];
    }
}
