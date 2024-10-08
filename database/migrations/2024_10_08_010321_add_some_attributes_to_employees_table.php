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
        Schema::table('employees', function (Blueprint $table) {
            $table->string('code')->nullable()->change();
            $table->date('birth_date')->nullable();
            $table->string('social_status')->nullable();
            $table->string('study_degree')->nullable();
            $table->string('study_speciality')->nullable();
            $table->string('national_address')->nullable();
            $table->string('iban')->nullable();
            $table->mediumText('documents')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->timestamp('approved_at')->nullable();
            $table->string(column: 'termination_reason')->nullable();
            $table->date(column: 'termination_date')->nullable();
            $table->date(column: 'termination_document')->nullable();
            $table->unsignedBigInteger('terminated_by')->nullable();
            $table->foreign('terminated_by')->references('id')->on('users');
            $table->string(column: 'full_name')->virtualAs('concat(first_name, \' \', middle_name, \' \', third_name, \' \', last_name)');
            $table->unsignedBigInteger('department_id')->nullable()->change();
            $table->foreign('department_id')->references('id')->on('departments')->nullable()->change();
            $table->unsignedBigInteger('designation_id')->nullable()->change();
            $table->foreign('designation_id')->references('id')->on('designations')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::enableForeignKeyConstraints();

        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('birth_date');
            $table->dropColumn('social_status');
            $table->dropColumn('study_degree');
            $table->dropColumn('study_speciality');
            $table->dropColumn('national_address');
            $table->dropColumn('iban');
            $table->dropColumn('documents');
            $table->dropColumn('termination_reason');
            $table->dropColumn('termination_date');
            $table->dropColumn('termination_document');
            $table->dropColumn('is_approved');
            $table->dropColumn('approved_at');            
            $table->dropForeign((['terminated_by']));
            $table->dropColumn('terminated_by');
            $table->string('code')->change();
            $table->dropColumn('full_name');
        });
        Schema::enableForeignKeyConstraints();
    }
};
