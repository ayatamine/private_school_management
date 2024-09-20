<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('car_name');
            $table->string('plate_number');
            $table->string('form_number');
            $table->date('expire_date');
            $table->string('insurance_name');
            $table->date('insurance_expire_at');
            $table->date('periodic_inspection_expire_at');
            $table->mediumText('documents')->nullable();
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
