<?php

namespace App\Http\Controllers;

use App\Models\Employ;
use App\Models\Project;
use App\Models\ProjectBilling;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Table;
use PhpOffice\PhpSpreadsheet\Worksheet\Table\TableStyle;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ChargeController extends Controller
{
    public function index(Request $request): View
    {
        return $this->page(
            'dashboard',
            ProjectBilling::query(),
            null,
            $request->query('service'),
            $request->query('search'),
            $request->query('filters', [])
        );
    }

    public function oneTime(Request $request): View
    {
        return $this->page('one_time', ProjectBilling::where('kategori_layanan', 'OTM'), null, null, $request->query('search'));
    }

    public function monthly(Request $request): View
    {
        return $this->page('monthly', ProjectBilling::where('kategori_layanan', 'MS'), null, null, $request->query('search'));
    }

    public function show(ProjectBilling $charge): View
    {
        $charge->load('project.pm');

        return view('charges.show', compact('charge'));
    }

    public function print(ProjectBilling $charge): View
    {
        $charge->load('project.pm');

        return view('charges.print', compact('charge'));
    }

    public function exportCsv(Request $request)
    {
        $status = $request->query('status');
        $service = $request->query('service');
        $search = trim((string) $request->query('search', ''));
        $filters = $request->query('filters', []);

        $rowsQuery = ProjectBilling::with('project.pm')->latest('billing_id');

        if ($status) {
            $rowsQuery->where('status', $status);
        }

        if ($service && $service !== 'all') {
            $rowsQuery->where('kategori_layanan', $service);
        }

        if ($search !== '') {
            $rowsQuery->where(function ($rowQuery) use ($search) {
                $rowQuery->where('kategori_layanan', 'like', "%{$search}%")
                    ->orWhere('tipe_pengadaan', 'like', "%{$search}%")
                    ->orWhere('priode', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhere('note', 'like', "%{$search}%")
                    ->orWhere('due_date_kontrak', 'like', "%{$search}%")
                    ->orWhereHas('project', function ($projectQuery) use ($search) {
                        $projectQuery->where('project_name', 'like', "%{$search}%")
                            ->orWhere('user', 'like', "%{$search}%")
                            ->orWhere('cost_center', 'like', "%{$search}%")
                            ->orWhere('no_kontrak', 'like', "%{$search}%")
                            ->orWhere('nilai_kontrak', 'like', "%{$search}%")
                            ->orWhere('tgl_kontrak', 'like', "%{$search}%");
                    })
                    ->orWhereHas('project.pm', function ($pmQuery) use ($search) {
                        $pmQuery->where('employ_name', 'like', "%{$search}%");
                    });
            });
        }

        $normalizedFilters = [];
        foreach ($filters as $key => $value) {
            if (is_array($value)) {
                $selectedValues = array_values(array_filter(array_map(fn ($item) => is_string($item) ? trim($item) : $item, $value), fn ($item) => $item !== '' && $item !== null));

                if (! empty($selectedValues)) {
                    $normalizedFilters[$key] = $selectedValues;
                }

                continue;
            }

            $trimmedValue = is_string($value) ? trim($value) : $value;

            if ($trimmedValue !== '' && $trimmedValue !== null) {
                $normalizedFilters[$key] = $trimmedValue;
            }
        }

        if (! empty($normalizedFilters)) {
            foreach ($normalizedFilters as $filterKey => $filterValue) {
                $values = is_array($filterValue) ? $filterValue : [$filterValue];

                switch ($filterKey) {
                    case 'pm':
                        $rowsQuery->whereHas('project.pm', fn ($q) => $q->whereIn('employ_name', $values));
                        break;
                    case 'project':
                        $rowsQuery->whereHas('project', fn ($q) => $q->whereIn('project_name', $values));
                        break;
                    case 'user':
                        $rowsQuery->whereHas('project', fn ($q) => $q->whereIn('user', $values));
                        break;
                    case 'type':
                        $rowsQuery->whereIn('kategori_layanan', $values);
                        break;
                    case 'cost_center':
                        $rowsQuery->whereHas('project', fn ($q) => $q->whereIn('cost_center', $values));
                        break;
                    case 'contract_reference':
                        $rowsQuery->whereHas('project', fn ($q) => $q->whereIn('no_kontrak', $values));
                        break;
                    case 'nilai_kontrak':
                        $rowsQuery->whereHas('project', fn ($q) => $q->whereIn('nilai_kontrak', $values));
                        break;
                    case 'periode':
                        $rowsQuery->whereIn('priode', $values);
                        break;
                    case 'contract_date':
                        $rowsQuery->whereHas('project', fn ($q) => $q->whereIn('tgl_kontrak', $values));
                        break;
                    case 'due_date':
                        $rowsQuery->whereIn('due_date_kontrak', $values);
                        break;
                    case 'status':
                        $rowsQuery->whereIn('status', $values);
                        break;
                }
            }
        }

        $rows = $rowsQuery->get();

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Matrix Dashboard');

        $headers = ['No', 'PM', 'Project', 'USER', 'Type Pengadaan', 'Cost Center', 'Kontrak / PO / JO', 'Nilai Kontrak', 'Periode Pengadaan', 'Tanggal Kontrak / PO / JO', 'Masa Kontrak Due Date', 'Tgl Pembuatan BA', 'Tgl Paraf PM', 'Tgl TTD Manager', 'Tgl Submit Dokumen', 'Tgl Permintaan Invoice', 'Status', 'Catatan'];
        $sheetData = [$headers];

        $rowNumber = 1;
        foreach ($rows as $row) {
            $sheetData[] = [
                $rowNumber,
                $row->pm ?? '-',
                $row->name ?? '-',
                $row->user_name ?? '-',
                $row->procurement_type ?? '-',
                $row->cost_center ?? '-',
                $row->contract_reference ?? '-',
                (float) ($row->amount ?? 0),
                $row->procurement_period ?? '-',
                $row->contract_date?->format('Y-m-d') ?? '-',
                $row->due_date?->format('Y-m-d') ?? '-',
                $row->tgl_pembuatan_ba?->format('Y-m-d') ?? '-',
                $row->tgl_paraf_pm?->format('Y-m-d') ?? '-',
                $row->tgl_ttd_manager?->format('Y-m-d') ?? '-',
                $row->tgl_submit_dokumen?->format('Y-m-d') ?? '-',
                $row->tgl_permintaan_invoice?->format('Y-m-d') ?? '-',
                $row->status ?? '-',
                $row->note ?? '-',
            ];

            $rowNumber++;
        }

        $sheet->fromArray($sheetData, null, 'A1');

        $lastRow = count($sheetData) + 1;

        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'name' => 'Calibri',
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1F4E78'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'D9E2F3'],
                ],
            ],
        ];

        $sheet->getStyle('A1:R1')->applyFromArray($headerStyle);
        $sheet->getStyle('A1:R'.$lastRow)->getAlignment()->setWrapText(true);

        $bodyStyle = [
            'font' => [
                'name' => 'Calibri',
                'size' => 10,
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'D9E2F3'],
                ],
            ],
        ];

        $sheet->getStyle('A2:R'.$lastRow)->applyFromArray($bodyStyle);

        for ($rowIndex = 2; $rowIndex <= $lastRow; $rowIndex++) {
            $sheet->getStyle('A'.$rowIndex.':L'.$rowIndex)->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $rowIndex % 2 === 0 ? 'F7F9FC' : 'FFFFFF'],
                ],
            ]);
        }

        $sheet->getStyle('G2:G'.$lastRow)->getNumberFormat()->setFormatCode('#,##0');

        foreach (range('A', 'R') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $tableRange = 'A1:R'.$lastRow;
        $table = new Table($tableRange, 'MatrixDashboardTable');
        $table->setStyle(new TableStyle(TableStyle::TABLE_STYLE_MEDIUM2));
        $sheet->addTable($table);

        $filename = 'matrix-dashboard-'.now()->format('Ymd_His').'.xlsx';

        $writer = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), 'xlsx_');

        if ($tempFile === false) {
            abort(500, 'Gagal membuat file spreadsheet sementara.');
        }

        $writer->save($tempFile);

        return response()->download($tempFile, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    public function edit(ProjectBilling $charge): View
    {
        $charge->load('project.pm');

        return view('charges.edit', compact('charge'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'project_name' => ['required', 'string', 'max:120'],
            'user' => ['required', 'string', 'max:120'],
            'pm_id' => ['required', 'integer', 'exists:employ,employ_id'],
            'cost_center' => ['required', 'string', 'max:80'],
            'no_kontrak' => ['required', 'string', 'max:120'],
            'nilai_kontrak' => ['required', 'numeric', 'min:0'],
            'tgl_kontrak' => ['required', 'date'],
            'kategori_layanan' => ['required', 'in:MS,OTM'],
            'tipe_pengadaan' => ['nullable', 'string', 'max:80'],
            'priode' => ['required', 'string', 'max:30'],
            'due_date_kontrak' => ['nullable', 'date'],
            'tgl_pembuatan_ba' => ['nullable', 'date'],
            'tgl_paraf_pm' => ['nullable', 'date'],
            'tgl_ttd_manager' => ['nullable', 'date'],
            'tgl_submit_dokumen' => ['nullable', 'date'],
            'tgl_permintaan_invoice' => ['nullable', 'date'],
            'note' => ['nullable', 'string'],
            'file_kontrak' => ['nullable', 'file', 'mimes:pdf', 'extensions:pdf', 'max:10240'],
            'file_ba' => ['nullable', 'file', 'mimes:pdf', 'extensions:pdf', 'max:10240'],
        ]);

        $documentPaths = $this->storeDocuments($request);
        $statusDates = [
            'tgl_pembuatan_ba',
            'tgl_paraf_pm',
            'tgl_ttd_manager',
            'tgl_submit_dokumen',
            'tgl_permintaan_invoice',
        ];
        $data['status'] = collect($statusDates)->every(fn (string $date) => filled($data[$date] ?? null))
            ? 'Done'
            : 'In Progress';

        DB::transaction(function () use ($data, $documentPaths): void {
            $project = Project::create([
                'project_name' => $data['project_name'],
                'user' => $data['user'],
                'cost_center' => $data['cost_center'],
                'no_kontrak' => $data['no_kontrak'],
                'nilai_kontrak' => $data['nilai_kontrak'],
                'tgl_kontrak' => $data['tgl_kontrak'],
                'pm_id' => $data['pm_id'],
            ]);

            ProjectBilling::create([
                'project_id' => $project->project_id,
                'kategori_layanan' => $data['kategori_layanan'],
                'tipe_pengadaan' => $data['tipe_pengadaan'],
                'priode' => $data['priode'],
                'due_date_kontrak' => $data['due_date_kontrak'],
                'tgl_pembuatan_ba' => $data['tgl_pembuatan_ba'],
                'tgl_paraf_pm' => $data['tgl_paraf_pm'],
                'tgl_ttd_manager' => $data['tgl_ttd_manager'],
                'tgl_submit_dokumen' => $data['tgl_submit_dokumen'],
                'tgl_permintaan_invoice' => $data['tgl_permintaan_invoice'],
                'status' => $data['status'],
                'note' => $data['note'],
                'file_kontrak' => $documentPaths['file_kontrak'] ?? null,
                'file_ba' => $documentPaths['file_ba'] ?? null,
            ]);
        });

        $destination = $data['kategori_layanan'] === 'OTM' ? 'charges.one-time' : 'charges.monthly';

        return to_route($destination)->with('success', 'Pembayaran berhasil ditambahkan.');
    }

    public function update(Request $request, ProjectBilling $charge): RedirectResponse
    {
        $data = $request->validate([
            'project_name' => ['required', 'string', 'max:120'],
            'user' => ['required', 'string', 'max:120'],
            'cost_center' => ['required', 'string', 'max:80'],
            'no_kontrak' => ['required', 'string', 'max:120'],
            'nilai_kontrak' => ['required', 'numeric', 'min:0'],
            'tgl_kontrak' => ['required', 'date'],
            'kategori_layanan' => ['required', 'in:MS,OTM'],
            'tipe_pengadaan' => ['nullable', 'string', 'max:80'],
            'priode' => ['required', 'string', 'max:30'],
            'due_date_kontrak' => ['nullable', 'date'],
            'tgl_pembuatan_ba' => ['nullable', 'date'],
            'tgl_paraf_pm' => ['nullable', 'date'],
            'tgl_ttd_manager' => ['nullable', 'date'],
            'tgl_submit_dokumen' => ['nullable', 'date'],
            'tgl_permintaan_invoice' => ['nullable', 'date'],
            'note' => ['nullable', 'string'],
            'file_kontrak' => ['nullable', 'file', 'mimes:pdf', 'extensions:pdf', 'max:10240'],
            'file_ba' => ['nullable', 'file', 'mimes:pdf', 'extensions:pdf', 'max:10240'],
        ]);

        $documentPaths = $this->storeDocuments($request);
        $statusDates = [
            'tgl_pembuatan_ba',
            'tgl_paraf_pm',
            'tgl_ttd_manager',
            'tgl_submit_dokumen',
            'tgl_permintaan_invoice',
        ];
        $data['status'] = collect($statusDates)->every(fn (string $date) => filled($data[$date] ?? null))
            ? 'Done'
            : 'In Progress';

        $charge->project->update([
            'project_name' => $data['project_name'],
            'user' => $data['user'],
            'cost_center' => $data['cost_center'],
            'no_kontrak' => $data['no_kontrak'],
            'nilai_kontrak' => $data['nilai_kontrak'],
            'tgl_kontrak' => $data['tgl_kontrak'],
        ]);
        $previousDocumentPaths = [
            'file_kontrak' => $charge->file_kontrak,
            'file_ba' => $charge->file_ba,
        ];
        $billingData = collect($data)
            ->except(['project_name', 'user', 'cost_center', 'no_kontrak', 'nilai_kontrak', 'tgl_kontrak', 'file_kontrak', 'file_ba'])
            ->all();

        foreach ($documentPaths as $field => $path) {
            $billingData[$field] = $path;
        }

        $charge->update($billingData);

        foreach ($documentPaths as $field => $path) {
            if ($previousDocumentPaths[$field] && $previousDocumentPaths[$field] !== $path) {
                Storage::disk('public')->delete($previousDocumentPaths[$field]);
            }
        }

        $destination = $data['kategori_layanan'] === 'OTM' ? 'charges.one-time' : 'charges.monthly';

        return to_route($destination)->with('success', 'Pembayaran berhasil diperbarui.');
    }

    /**
     * @return array<string, string>
     */
    private function storeDocuments(Request $request): array
    {
        $paths = [];

        foreach (['file_kontrak', 'file_ba'] as $field) {
            if ($request->hasFile($field)) {
                $paths[$field] = $request->file($field)->store('payment-documents', 'public');
            }
        }

        return $paths;
    }

   private function page(string $page, $query, ?string $status = null, ?string $service = null, ?string $search = null, array $filters = []): View
    {
        $chargesQuery = $query->with('project.pm')->latest('billing_id');

        if ($page !== 'dashboard' && $search) {
            $searchTerm = trim($search);

            if ($searchTerm !== '') {
                $chargesQuery->where(function ($rowQuery) use ($searchTerm) {
                    $rowQuery->where('kategori_layanan', 'like', "%{$searchTerm}%")
                        ->orWhere('tipe_pengadaan', 'like', "%{$searchTerm}%")
                        ->orWhere('priode', 'like', "%{$searchTerm}%")
                        ->orWhere('status', 'like', "%{$searchTerm}%")
                        ->orWhere('note', 'like', "%{$searchTerm}%")
                        ->orWhere('due_date_kontrak', 'like', "%{$searchTerm}%")
                        ->orWhereHas('project', function ($projectQuery) use ($searchTerm) {
                            $projectQuery->where('project_name', 'like', "%{$searchTerm}%")
                                ->orWhere('user', 'like', "%{$searchTerm}%")
                                ->orWhere('cost_center', 'like', "%{$searchTerm}%")
                                ->orWhere('no_kontrak', 'like', "%{$searchTerm}%")
                                ->orWhere('nilai_kontrak', 'like', "%{$searchTerm}%")
                                ->orWhere('tgl_kontrak', 'like', "%{$searchTerm}%");
                        })
                        ->orWhereHas('project.pm', function ($pmQuery) use ($searchTerm) {
                            $pmQuery->where('employ_name', 'like', "%{$searchTerm}%");
                        });
                });
            }
        }

        $charges = $chargesQuery->paginate(8);
        $monthly = ProjectBilling::where('kategori_layanan', 'MS');
        $oneTime = ProjectBilling::where('kategori_layanan', 'OTM');

        $dashboardRowsQuery = ProjectBilling::with('project.pm')->latest('billing_id');

        if ($page === 'dashboard') {
            if ($status) {
                $dashboardRowsQuery->where('status', $status);
            }

            if ($service && $service !== 'all') {
                $dashboardRowsQuery->where('kategori_layanan', $service);
            }

            $normalizedFilters = [];
            foreach ($filters as $key => $value) {
                if (is_array($value)) {
                    $selectedValues = array_values(array_filter(array_map(fn ($item) => is_string($item) ? trim($item) : $item, $value), fn ($item) => $item !== '' && $item !== null));

                    if (! empty($selectedValues)) {
                        $normalizedFilters[$key] = $selectedValues;
                    }

                    continue;
                }

                $trimmedValue = is_string($value) ? trim($value) : $value;

                if ($trimmedValue !== '' && $trimmedValue !== null) {
                    $normalizedFilters[$key] = $trimmedValue;
                }
            }

            if (! empty($normalizedFilters)) {
                foreach ($normalizedFilters as $filterKey => $filterValue) {
                    $values = is_array($filterValue) ? $filterValue : [$filterValue];

                    switch ($filterKey) {
                        case 'pm':
                            $dashboardRowsQuery->whereHas('project.pm', fn ($q) => $q->whereIn('employ_name', $values));
                            break;
                        case 'project':
                            $dashboardRowsQuery->whereHas('project', fn ($q) => $q->whereIn('project_name', $values));
                            break;
                        case 'user':
                            $dashboardRowsQuery->whereHas('project', fn ($q) => $q->whereIn('user', $values));
                            break;
                        case 'type':
                            $dashboardRowsQuery->whereIn('kategori_layanan', $values);
                            break;
                        case 'cost_center':
                            $dashboardRowsQuery->whereHas('project', fn ($q) => $q->whereIn('cost_center', $values));
                            break;
                        case 'contract_reference':
                            $dashboardRowsQuery->whereHas('project', fn ($q) => $q->whereIn('no_kontrak', $values));
                            break;
                        case 'nilai_kontrak':
                            $dashboardRowsQuery->whereHas('project', fn ($q) => $q->whereIn('nilai_kontrak', $values));
                            break;
                        case 'periode':
                            $dashboardRowsQuery->whereIn('priode', $values);
                            break;
                        case 'contract_date':
                            $dashboardRowsQuery->whereHas('project', fn ($q) => $q->whereIn('tgl_kontrak', $values));
                            break;
                        case 'due_date':
                            $dashboardRowsQuery->whereIn('due_date_kontrak', $values);
                            break;
                        case 'status':
                            $dashboardRowsQuery->whereIn('status', $values);
                            break;
                    }
                }
            }
        }

        $dashboardRows = $dashboardRowsQuery->get();

        $filterOptions = [
            'pm' => $dashboardRows->map(fn ($row) => $row->pm)->filter()->unique()->sort()->values()->all(),
            'project' => $dashboardRows->map(fn ($row) => $row->name)->filter()->unique()->sort()->values()->all(),
            'user' => $dashboardRows->map(fn ($row) => $row->user_name)->filter()->unique()->sort()->values()->all(),
            'type' => $dashboardRows->map(fn ($row) => $row->kategori_layanan)->filter()->unique()->sort()->values()->all(),
            'cost_center' => $dashboardRows->map(fn ($row) => $row->cost_center)->filter()->unique()->sort()->values()->all(),
            'contract_reference' => $dashboardRows->map(fn ($row) => $row->contract_reference)->filter()->unique()->sort()->values()->all(),
            'nilai_kontrak' => $dashboardRows->map(fn ($row) => $row->amount)->filter()->unique()->sort()->values()->all(),
            'periode' => $dashboardRows->map(fn ($row) => $row->procurement_period)->filter()->unique()->sort()->values()->all(),
            'contract_date' => $dashboardRows->map(fn ($row) => $row->contract_date?->format('Y-m-d'))->filter()->unique()->sort()->values()->all(),
            'due_date' => $dashboardRows->map(fn ($row) => $row->due_date?->format('Y-m-d'))->filter()->unique()->sort()->values()->all(),
            'status' => $dashboardRows->map(fn ($row) => $row->status)->filter()->unique()->sort()->values()->all(),
        ];

        $pmOptions = Employ::query()
            ->where('role', 1)
            ->orderBy('employ_name')
            ->get(['employ_id', 'employ_name']);

        // --- DATA GRAFIK 1: NILAI KONTRAK ---
        $chartProjects = Project::select('project_name', 'nilai_kontrak')
            ->orderBy('tgl_kontrak', 'asc')
            ->take(7)
            ->get();

        $chartLabels = $chartProjects->pluck('project_name');
        $chartData = $chartProjects->pluck('nilai_kontrak');

        // --- DATA GRAFIK 2: STATUS PROYEK ---
        $statusCounts = ProjectBilling::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $statusLabels = $statusCounts->keys();
        $statusData = $statusCounts->values();

        // --- RETURN VIEW UTAMA (HANYA SATU) ---
        return view('welcome', [
            'charges' => $charges,
            'dashboardRows' => $dashboardRows,
            'dashboardTotalCount' => ProjectBilling::count(),
            'page' => $page,
            'activeStatus' => $status,
            'activeService' => $service,
            'activeSearch' => $search,
            'activeFilters' => $normalizedFilters ?? [],
            'filterOptions' => $filterOptions,
            'pmOptions' => $pmOptions,
            'monthlyTotal' => $monthly->sum('nilai_bulan'),
            'monthlyCount' => $monthly->count(),

            'oneTimeTotal' => ProjectBilling::join('project', 'project_billing.project_id', '=', 'project.project_id')
                ->where('project_billing.kategori_layanan', 'OTM')
                ->sum('project.nilai_kontrak'),
            'oneTimeCount' => $oneTime->count(),

            'dashboardTotals' => [
                'projectTotal' => Project::count(),
                'contractValueTotal' => Project::sum('nilai_kontrak'),
                'doneCount' => ProjectBilling::where('status', 'Done')->count(),
                'progressCount' => ProjectBilling::where('status', 'In Progress')->count(),
            ],

            'chartLabels' => $chartLabels,
            'chartData' => $chartData,
            'statusLabels' => $statusLabels,
            'statusData' => $statusData,
        ]);
    }
}
