<?php

namespace Database\Seeders;

use App\Models\AcademicStage;
use Illuminate\Database\Seeder;

class AcademicStageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AcademicStage::factory()->count(5)->create();
    }
}
