<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\User;

class EmployeeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Employee::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'code' => $this->faker->word(),
            'user_id' => User::factory(),
            'first_name' => $this->faker->firstName(),
            'middle_name' => $this->faker->word(),
            'third_name' => $this->faker->word(),
            'last_name' => $this->faker->lastName(),
            'department_id' => Department::factory(),
            'designation_id' => Designation::factory(),
            'gender' => $this->faker->randomElement(["male","female"]),
            'joining_date' => $this->faker->date(),
            'nationality' => $this->faker->word(),
            'identity_type' => $this->faker->word(),
            'identity_expire_date' => $this->faker->date(),
        ];
    }
}
