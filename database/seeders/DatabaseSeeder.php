<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Employee\Database\Seeders\EmployeeDatabaseSeeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(EmployeeDatabaseSeeder::class);
    }
}
