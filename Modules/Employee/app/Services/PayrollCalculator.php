<?php

namespace Modules\Employee\Services;

use Modules\Employee\Enums\Designation;

class PayrollCalculator
{
    /**
     * Calculate derived payroll values using integer cents to avoid float drift.
     *
     * The tax brackets follow the specified fixed 150,000 calculation base:
     * 5% at or above 150,000 and 3% from 100,000 up to (but not including) 150,000.
     *
     * @return array<string, string>
     */
    public function calculate(float|string $monthlySalaryPackage, Designation $designation): array
    {
        $salaryCents = (int) round((float) $monthlySalaryPackage * 100, 0, PHP_ROUND_HALF_UP);
        $taxCents = match (true) {
            $salaryCents >= 15_000_000 => $this->percentageOf(15_000_000, 5),
            $salaryCents >= 10_000_000 => $this->percentageOf(15_000_000, 3),
            default => 0,
        };
        $bonusRate = match ($designation) {
            Designation::Manager => 5,
            Designation::Senior => 3,
            Designation::Associate => 1,
            Designation::Intern => 0,
        };
        $bonusCents = $this->percentageOf($salaryCents, $bonusRate);
        $monthlyNetCents = $salaryCents - $taxCents;
        $yearlyNetCents = ($monthlyNetCents * 12) + $bonusCents;

        return [
            'monthly_tax_value' => $this->asDecimal($taxCents),
            'yearly_increasing_bonus' => $this->asDecimal($bonusCents),
            'monthly_net_salary' => $this->asDecimal($monthlyNetCents),
            'yearly_net_salary' => $this->asDecimal($yearlyNetCents),
        ];
    }

    private function percentageOf(int $amountCents, int $percentage): int
    {
        return intdiv(($amountCents * $percentage) + 50, 100);
    }

    private function asDecimal(int $amountCents): string
    {
        return number_format($amountCents / 100, 2, '.', '');
    }
}
