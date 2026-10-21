<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 120);
            $table->string('email')->unique();
            $table->string('phone', 32)->unique();
            $table->string('designation', 20)->index();
            $table->decimal('monthly_salary_package', 12, 2);
            $table->decimal('monthly_tax_value', 12, 2);
            $table->decimal('yearly_increasing_bonus', 12, 2);
            $table->decimal('monthly_net_salary', 12, 2);
            $table->decimal('yearly_net_salary', 14, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
