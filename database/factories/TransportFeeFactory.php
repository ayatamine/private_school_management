<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\AcademicYear;
use App\Models\TransportFee;

class TransportFeeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TransportFee::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'academic_year_id' => AcademicYear::factory(),
            'name' => $this->faker->name(),
            'payment_partition_count' => $this->faker->numberBetween(-10000, 10000),
            'payment_partition' => '{}',
        ];
    }
}
