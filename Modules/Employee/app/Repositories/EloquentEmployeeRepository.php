<?php

namespace Modules\Employee\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Modules\Employee\Models\Employee;

class EloquentEmployeeRepository implements EmployeeRepositoryInterface
{
    public function all(?string $phone = null): Collection
    {
        return Employee::query()
            ->when($phone !== null, fn ($query) => $query->where('phone', 'like', "%{$phone}%"))
            ->orderBy('name')
            ->get();
    }

    public function findOrFail(int $id): Employee
    {
        return Employee::query()->findOrFail($id);
    }

    public function create(array $attributes): Employee
    {
        return Employee::query()->create($attributes);
    }

    public function update(Employee $employee, array $attributes): Employee
    {
        $employee->update($attributes);

        return $employee->refresh();
    }

    public function delete(Employee $employee): void
    {
        $employee->delete();
    }
}
