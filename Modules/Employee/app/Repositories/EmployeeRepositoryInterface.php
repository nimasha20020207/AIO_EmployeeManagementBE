<?php

namespace Modules\Employee\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Modules\Employee\Models\Employee;

interface EmployeeRepositoryInterface
{
    public function all(?string $phone = null): Collection;

    public function findOrFail(int $id): Employee;

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(array $attributes): Employee;

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function update(Employee $employee, array $attributes): Employee;

    public function delete(Employee $employee): void;
}
