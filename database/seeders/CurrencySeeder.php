<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Currency::factory()->count(5)->create();
        Currency::create([
            'name'=>'yuan',
            'symbol'=>'CNY',
            'is_active'=>true,
        ]);
        Currency::create([
            'name'=>'dollar',
            'symbol'=>'USD',
            'is_active'=>true,
        ]);
    }
}
