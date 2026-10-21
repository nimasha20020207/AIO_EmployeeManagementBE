<?php

namespace Modules\Employee\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Employee\Enums\Designation;

class StoreEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', Rule::unique('employees', 'email')],
            'phone' => ['required', 'string', 'max:32', Rule::unique('employees', 'phone')],
            'designation' => ['required', Rule::enum(Designation::class)],
            'monthly_salary_package' => ['required', 'numeric', 'min:0', 'max:999999999.99', 'decimal:0,2'],
        ];
    }
}
