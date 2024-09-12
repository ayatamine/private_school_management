<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Transport;
use App\Models\TransportFee;
use App\Models\User;
use App\Models\Vehicle;

class TransportFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Transport::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'vehicle_id' => Vehicle::factory(),
            'transport_fees_id' => TransportFee::factory(),
            'registration_date' => $this->faker->date(),
            'registred_by' => User::factory(),
            'transport_fee_id' => TransportFee::factory(),
        ];
    }
}
