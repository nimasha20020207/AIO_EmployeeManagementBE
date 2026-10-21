<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Employee\Models\Employee;
use Tests\TestCase;

class EmployeeApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_creation_calculates_tax_bonus_and_monthly_and_yearly_net_salary(): void
    {
        $response = $this->postJson('/api/employees', [
            'name' => 'Jamie Smith',
            'email' => 'jamie@example.test',
            'phone' => '+15550100201',
            'designation' => 'Manager',
            'monthly_salary_package' => '200000.00',
            'monthly_tax_value' => 1,
            'yearly_increasing_bonus' => 1,
            'monthly_net_salary' => 1,
            'yearly_net_salary' => 1,
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.designation', 'Manager')
            ->assertJsonPath('data.monthly_salary_package', 200000)
            ->assertJsonPath('data.monthly_tax_value', 7500)
            ->assertJsonPath('data.yearly_increasing_bonus', 10000)
            ->assertJsonPath('data.monthly_net_salary', 192500)
            ->assertJsonPath('data.yearly_net_salary', 2320000);

        $this->assertDatabaseHas('employees', [
            'email' => 'jamie@example.test',
            'monthly_tax_value' => '7500.00',
            'yearly_increasing_bonus' => '10000.00',
            'monthly_net_salary' => '192500.00',
            'yearly_net_salary' => '2320000.00',
        ]);
    }

    public function test_it_applies_each_tax_band_and_designation_bonus(): void
    {
        $cases = [
            ['100000.00', 'Intern', 4500, 0],
            ['99999.99', 'Associate', 0, 1000],
            ['150000.00', 'Senior', 7500, 4500],
            ['200000.00', 'Manager', 7500, 10000],
        ];

        foreach ($cases as $index => [$salary, $designation, $tax, $bonus]) {
            $this->postJson('/api/employees', [
                'name' => "Payroll Test {$index}",
                'email' => "payroll{$index}@example.test",
                'phone' => "+1555010030{$index}",
                'designation' => $designation,
                'monthly_salary_package' => $salary,
            ])
                ->assertCreated()
                ->assertJsonPath('data.monthly_tax_value', $tax)
                ->assertJsonPath('data.yearly_increasing_bonus', $bonus);
        }
    }

    public function test_employee_update_changes_phone_and_salary_and_recalculates_payroll(): void
    {
        $employee = $this->createEmployee();

        $this->patchJson("/api/employees/{$employee->id}", [
            'phone' => '+15550100401',
            'monthly_salary_package' => '150000.00',
        ])
            ->assertOk()
            ->assertJsonPath('data.phone', '+15550100401')
            ->assertJsonPath('data.monthly_tax_value', 7500)
            ->assertJsonPath('data.yearly_increasing_bonus', 7500)
            ->assertJsonPath('data.monthly_net_salary', 142500)
            ->assertJsonPath('data.yearly_net_salary', 1717500);
    }

    public function test_it_searches_employees_by_phone(): void
    {
        $this->createEmployee();

        $this->getJson('/api/employees/search?phone=0201')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.phone', '+15550100201');
    }

    public function test_it_shows_an_employee_with_all_payroll_fields(): void
    {
        $employee = $this->createEmployee();

        $this->getJson("/api/employees/{$employee->id}")
            ->assertOk()
            ->assertJsonPath('data.name', 'Jamie Smith')
            ->assertJsonPath('data.email', 'jamie@example.test')
            ->assertJsonPath('data.phone', '+15550100201')
            ->assertJsonPath('data.designation', 'Manager')
            ->assertJsonPath('data.monthly_salary_package', 200000)
            ->assertJsonPath('data.monthly_tax_value', 7500)
            ->assertJsonPath('data.yearly_increasing_bonus', 10000)
            ->assertJsonPath('data.monthly_net_salary', 192500)
            ->assertJsonPath('data.yearly_net_salary', 2320000);
    }

    public function test_it_returns_a_payroll_report_with_totals(): void
    {
        $this->createEmployee();

        $this->getJson('/api/employees/report')
            ->assertOk()
            ->assertJsonPath('summary.employee_count', 1)
            ->assertJsonPath('summary.monthly_salary_package_total', 200000)
            ->assertJsonPath('summary.monthly_tax_value_total', 7500)
            ->assertJsonPath('summary.monthly_net_salary_total', 192500)
            ->assertJsonPath('summary.yearly_increasing_bonus_total', 10000)
            ->assertJsonPath('summary.yearly_net_salary_total', 2320000)
            ->assertJsonPath('data.0.email', 'jamie@example.test');
    }

    public function test_it_deletes_an_employee(): void
    {
        $employee = $this->createEmployee();

        $this->deleteJson("/api/employees/{$employee->id}")->assertNoContent();
        $this->assertDatabaseMissing('employees', ['id' => $employee->id]);
    }

    public function test_employee_creation_validates_fields_and_unique_values(): void
    {
        $this->createEmployee();

        $this->postJson('/api/employees', [
            'name' => '',
            'email' => 'jamie@example.test',
            'phone' => '+15550100201',
            'designation' => 'Director',
            'monthly_salary_package' => '-10',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'email', 'phone', 'designation', 'monthly_salary_package']);
    }

    private function createEmployee(): Employee
    {
        $this->postJson('/api/employees', [
            'name' => 'Jamie Smith',
            'email' => 'jamie@example.test',
            'phone' => '+15550100201',
            'designation' => 'Manager',
            'monthly_salary_package' => '200000.00',
        ])->assertCreated();

        return Employee::query()->where('email', 'jamie@example.test')->firstOrFail();
    }
}
