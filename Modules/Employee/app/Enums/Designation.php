<?php

namespace Modules\Employee\Enums;

enum Designation: string
{
    case Intern = 'Intern';
    case Associate = 'Associate';
    case Senior = 'Senior';
    case Manager = 'Manager';
}
