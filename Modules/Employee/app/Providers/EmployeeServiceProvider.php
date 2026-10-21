<?php

namespace Modules\Employee\Providers;

use Modules\Employee\Repositories\EloquentEmployeeRepository;
use Modules\Employee\Repositories\EmployeeRepositoryInterface;
use Nwidart\Modules\Support\ModuleServiceProvider;

class EmployeeServiceProvider extends ModuleServiceProvider
{
    /**
     * The name of the module.
     */
    protected string $name = 'Employee';

    /**
     * The lowercase version of the module name.
     */
    protected string $nameLower = 'employee';

    public function register(): void
    {
        parent::register();
        $this->app->bind(EmployeeRepositoryInterface::class, EloquentEmployeeRepository::class);
    }

    protected array $providers = [RouteServiceProvider::class];
}
