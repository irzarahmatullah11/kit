<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\Export;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class DashboardExport implements Export, WithMultipleSheets
{
    public function __construct(private Builder $query) {}

    /**
     * @return array<int, ChargeSheetExport>
     */
    public function sheets(): array
    {
        return [
            new ManagedServiceExport($this->query),
            new OneTimeChargeExport($this->query),
        ];
    }
}
