<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('employees')) {
            return;
        }

        Schema::table('employees', function (Blueprint $table): void {
            if (! Schema::hasColumn('employees', 'phone')) {
                $table->string('phone', 32)->nullable()->unique();
            }

            if (! Schema::hasColumn('employees', 'designation')) {
                $table->string('designation', 20)->default('Intern')->index();
            }

            if (! Schema::hasColumn('employees', 'monthly_salary_package')) {
                $table->decimal('monthly_salary_package', 12, 2)->default(0);
            }

            if (! Schema::hasColumn('employees', 'monthly_tax_value')) {
                $table->decimal('monthly_tax_value', 12, 2)->default(0);
            }

            if (! Schema::hasColumn('employees', 'yearly_increasing_bonus')) {
                $table->decimal('yearly_increasing_bonus', 12, 2)->default(0);
            }

            if (! Schema::hasColumn('employees', 'monthly_net_salary')) {
                $table->decimal('monthly_net_salary', 12, 2)->default(0);
            }

            if (! Schema::hasColumn('employees', 'yearly_net_salary')) {
                $table->decimal('yearly_net_salary', 14, 2)->default(0);
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('employees')) {
            return;
        }

        Schema::table('employees', function (Blueprint $table): void {
            foreach ([
                'phone',
                'designation',
                'monthly_salary_package',
                'monthly_tax_value',
                'yearly_increasing_bonus',
                'monthly_net_salary',
                'yearly_net_salary',
            ] as $column) {
                if (Schema::hasColumn('employees', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
