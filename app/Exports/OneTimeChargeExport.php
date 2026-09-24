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

class OneTimeChargeExport extends ChargeSheetExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping, WithStyles, WithTitle
{
    public function query(): Builder
    {
        return $this->queryForService('OTM');
    }

    public function headings(): array
    {
        return [
            'PM', 'PROJECT', 'USER', 'TYPE PENGADAAN', 'COST CENTER', 'KONTRAK/PO/JO',
            'NILAI KONTRAK', 'PERIODE PENGADAAN', 'TANGGAL KONTRAK/PO/JO',
            'MASA KONTRAK DUE DATE', 'PEMBUATAN BA, LHP', 'PARAF PM', 'TTD MANAGER',
            'Dokumen BA/LHP dikirim ke user', 'Permintaan invoice keuangan KIT', 'Status', 'Note',
        ];
    }

    public function map($row): array
    {
        /** @var ProjectBilling $row */
        return [
            $this->value($row->project?->pm?->employ_name),
            $this->value($row->project?->project_name),
            $this->value($row->project?->user),
            $this->value($row->tipe_pengadaan),
            $this->value($row->project?->cost_center),
            $this->value($row->project?->no_kontrak),
            (float) ($row->project?->nilai_kontrak ?? 0),
            $this->value($row->priode),
            $this->date($row->project?->tgl_kontrak),
            $this->date($row->due_date_kontrak),
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
        return 'OTC';
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
