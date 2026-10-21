<?php

namespace Modules\Employee\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'designation' => $this->designation->value,
            'monthly_salary_package' => (float) $this->monthly_salary_package,
            'monthly_tax_value' => (float) $this->monthly_tax_value,
            'yearly_increasing_bonus' => (float) $this->yearly_increasing_bonus,
            'monthly_net_salary' => (float) $this->monthly_net_salary,
            'yearly_net_salary' => (float) $this->yearly_net_salary,
        ];
    }
}
