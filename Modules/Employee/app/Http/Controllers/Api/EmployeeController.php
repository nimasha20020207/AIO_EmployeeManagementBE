<?php

namespace Modules\Employee\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Modules\Employee\Enums\Designation;
use Modules\Employee\Http\Requests\SearchEmployeesRequest;
use Modules\Employee\Http\Requests\StoreEmployeeRequest;
use Modules\Employee\Http\Requests\UpdateEmployeeRequest;
use Modules\Employee\Http\Resources\EmployeeResource;
use Modules\Employee\Models\Employee;
use Modules\Employee\Repositories\EmployeeRepositoryInterface;
use Modules\Employee\Services\PayrollCalculator;

class EmployeeController extends Controller
{
    public function __construct(
        private readonly EmployeeRepositoryInterface $employees,
        private readonly PayrollCalculator $payrollCalculator,
    ) {}

    public function index(): AnonymousResourceCollection
    {
        return EmployeeResource::collection($this->employees->all());
    }

    public function search(SearchEmployeesRequest $request): AnonymousResourceCollection
    {
        return EmployeeResource::collection(
            $this->employees->all($request->validated('phone')),
        );
    }

    public function report(): JsonResponse
    {
        $employees = $this->employees->all();

        return response()->json([
            'data' => EmployeeResource::collection($employees)->resolve(),
            'summary' => [
                'employee_count' => $employees->count(),
                'monthly_salary_package_total' => $this->sumMoney($employees, 'monthly_salary_package'),
                'monthly_tax_value_total' => $this->sumMoney($employees, 'monthly_tax_value'),
                'monthly_net_salary_total' => $this->sumMoney($employees, 'monthly_net_salary'),
                'yearly_increasing_bonus_total' => $this->sumMoney($employees, 'yearly_increasing_bonus'),
                'yearly_net_salary_total' => $this->sumMoney($employees, 'yearly_net_salary'),
            ],
        ]);
    }

    public function store(StoreEmployeeRequest $request): JsonResponse
    {
        $attributes = $request->validated();
        $attributes = [
            ...$attributes,
            ...$this->payrollCalculator->calculate(
                $attributes['monthly_salary_package'],
                Designation::from($attributes['designation']),
            ),
        ];

        return (new EmployeeResource($this->employees->create($attributes)))
            ->response()
            ->setStatusCode(201);
    }

    public function show(int $employee): EmployeeResource
    {
        return new EmployeeResource($this->employees->findOrFail($employee));
    }

    public function update(UpdateEmployeeRequest $request, int $employee): EmployeeResource
    {
        $employeeRecord = $this->employees->findOrFail($employee);
        $attributes = $request->validated();
        $payroll = $this->payrollCalculator->calculate(
            $attributes['monthly_salary_package'],
            $employeeRecord->designation,
        );

        return new EmployeeResource(
            $this->employees->update($employeeRecord, [...$attributes, ...$payroll]),
        );
    }

    public function destroy(int $employee): Response
    {
        $this->employees->delete($this->employees->findOrFail($employee));

        return response()->noContent();
    }

    private function sumMoney(Collection $employees, string $attribute): float
    {
        $totalCents = $employees->sum(
            fn (Employee $employee): int => (int) round((float) $employee->{$attribute} * 100),
        );

        return $totalCents / 100;
    }
}
