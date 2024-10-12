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
        Schema::create('employment_durations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->references('id')->on('employees')->constrained();
            $table->foreignId('department_id')->references('id')->on('departments')->nullable()->constrained();
            $table->foreignId('designation_id')->references('id')->on('designations')->constrained();
            $table->date('contract_start_date');
            $table->date('contract_end_date')->nullable();
            $table->string('contract_image');
            $table->mediumText('note')->nullable();
            $table->string('attachment')->nullable();
            $table->string('contract_end_reason')->nullable();
            $table->timestamps();
        });
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign('employees_department_id_foreign');
            $table->dropColumn('department_id');
            $table->dropForeign('employees_designation_id_foreign');
            $table->dropColumn('designation_id');       
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employment_durations');
        Schema::table('employees', function (Blueprint $table) {
            $table->unsignedBigInteger('department_id')->nullable();
            $table->foreign('department_id')->references('id')->on('departments');
            $table->unsignedBigInteger('designation_id')->nullable();
            $table->foreign('designation_id')->references('id')->on('designations');  
        });
    }
};
