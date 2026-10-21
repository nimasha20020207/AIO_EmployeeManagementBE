<?php

namespace Modules\Employee\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Employee\Enums\Designation;

class Employee extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'designation',
        'monthly_salary_package',
        'monthly_tax_value',
        'yearly_increasing_bonus',
        'monthly_net_salary',
        'yearly_net_salary',
    ];

    protected function casts(): array
    {
        return [
            'designation' => Designation::class,
            'monthly_salary_package' => 'decimal:2',
            'monthly_tax_value' => 'decimal:2',
            'yearly_increasing_bonus' => 'decimal:2',
            'monthly_net_salary' => 'decimal:2',
            'yearly_net_salary' => 'decimal:2',
        ];
    }
}
