<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Course;
use App\Models\Parent;
use App\Models\Student;
use App\Models\User;

class StudentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Student::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName(),
            'middle_name' => $this->faker->word(),
            'third_name' => $this->faker->word(),
            'last_name' => $this->faker->lastName(),
            'birth_date' => $this->faker->date(),
            'nationality' => $this->faker->word(),
            'email' => $this->faker->safeEmail(),
            'course_id' => Course::factory(),
            'parent_id' => Parent::factory(),
            'is_approved' => $this->faker->boolean(),
            'approved_at' => $this->faker->dateTime(),
            'registered_by' => User::factory(),
            'registration_number' => $this->faker->word(),
            'user_id' => User::factory(),
            'gender' => $this->faker->randomElement(["male","female"]),
            'opening_balance' => $this->faker->randomFloat(0, 0, 9999999999.),
            'finance_document' => $this->faker->word(),
            'note' => $this->faker->word(),
        ];
    }
}
