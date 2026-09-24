<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\Export;

abstract class ChargeSheetExport implements Export
{
    public function __construct(protected Builder $baseQuery) {}

    protected function queryForService(string $service): Builder
    {
        return (clone $this->baseQuery)
            ->with('project.pm', 'project.pmo')
            ->where('kategori_layanan', $service);
    }

    protected function value(mixed $value): mixed
    {
        return $value ?? '-';
    }

    protected function date(mixed $value): string
    {
        return $value?->format('Y-m-d') ?? '-';
    }
}
