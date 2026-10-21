<?php

namespace Modules\Employee\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeeRequest extends FormRequest
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
            'phone' => [
                'required',
                'string',
                'max:32',
                Rule::unique('employees', 'phone')->ignore($this->route('employee')),
            ],
            'monthly_salary_package' => ['required', 'numeric', 'min:0', 'max:999999999.99', 'decimal:0,2'],
        ];
    }
}
