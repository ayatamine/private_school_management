<?php

namespace Database\Seeders;

use App\Models\KnowledgeBaseCategory;
use Illuminate\Database\Seeder;

class KnowledgeBaseCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        KnowledgeBaseCategory::factory()->count(5)->create();
    }
}
