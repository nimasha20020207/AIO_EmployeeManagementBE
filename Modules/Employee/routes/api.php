<?php

use Illuminate\Support\Facades\Route;
use Modules\Employee\Http\Controllers\Api\EmployeeController;

Route::get('employees/search', [EmployeeController::class, 'search'])->name('employees.search');
Route::get('employees/report', [EmployeeController::class, 'report'])->name('employees.report');
Route::apiResource('employees', EmployeeController::class)->names('employees');
