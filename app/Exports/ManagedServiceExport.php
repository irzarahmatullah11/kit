<?php

namespace App\Exports;

use App\Models\ProjectBilling;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ManagedServiceExport extends ChargeSheetExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping, WithStyles, WithTitle
{
    public function query(): Builder
    {
        return $this->queryForService('MS');
    }

    public function headings(): array
    {
        return [
            'Project', 'User', 'PM', 'PMO', 'Periode Tagihan', 'Cost Center',
            'Kontrak/PO/JO', 'Nilai Kontrak', 'Nilai Bulanan/BA', 'Tanggal Kontrak',
            'Pembuatan BA, LHP', 'Paraf PM', 'TTD Manager',
            'Tanggal Dokumen BA/LHP Dikirim ke User', 'Permintaan Invoice Keuangan KIT',
            'Status', 'Note',
        ];
    }

    public function map($row): array
    {
        /** @var ProjectBilling $row */
        return [
            $this->value($row->project?->project_name),
            $this->value($row->project?->user),
            $this->value($row->project?->pm?->employ_name),
            $this->value($row->project?->pmo?->employ_name),
            $this->value($row->priode),
            $this->value($row->project?->cost_center),
            $this->value($row->project?->no_kontrak),
            (float) ($row->project?->nilai_kontrak ?? 0),
            (float) ($row->nilai_bulan ?? 0),
            $this->date($row->project?->tgl_kontrak),
            $this->date($row->tgl_pembuatan_ba),
            $this->date($row->tgl_paraf_pm),
            $this->date($row->tgl_ttd_manager),
            $this->date($row->tgl_submit_dokumen),
            $this->date($row->tgl_permintaan_invoice),
            $this->value($row->status),
            $this->value($row->note),
        ];
    }

    public function title(): string
    {
        return 'MS';
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '1F4E78']],
            ],
        ];
    }
}
