<?php

namespace Modules\Employee\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Employee\Enums\Designation;
use Modules\Employee\Models\Employee;
use Modules\Employee\Services\PayrollCalculator;

class EmployeeDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $examples = [
            ['Olivia Rhye', 'olivia@example.test', '+15550100101', Designation::Manager, '200000.00'],
            ['Phoenix Baker', 'phoenix@example.test', '+15550100102', Designation::Senior, '150000.00'],
            ['Lana Steiner', 'lana@example.test', '+15550100103', Designation::Associate, '95000.00'],
            ['Demi Wilkinson', 'demi@example.test', '+15550100104', Designation::Intern, '60000.00'],
        ];

        $calculator = app(PayrollCalculator::class);

        foreach ($examples as [$name, $email, $phone, $designation, $salary]) {
            Employee::query()->updateOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'phone' => $phone,
                    'designation' => $designation,
                    'monthly_salary_package' => $salary,
                    ...$calculator->calculate($salary, $designation),
                ],
            );
        }
    }
}
