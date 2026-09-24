<?php

namespace App\Http\Controllers;

use App\Exports\DashboardExport;
use App\Models\Employ;
use App\Models\Project;
use App\Models\ProjectBilling;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

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
            $request->query('filters', []),
            $request->query('ms_period')
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
        $managedServicePeriod = trim((string) $request->query('ms_period', ''));

        $rowsQuery = ProjectBilling::with('project.pm', 'project.pmo')->latest('billing_id');

        if (preg_match('/^\d{4}-\d{2}$/', $managedServicePeriod)) {
            $periodStart = Carbon::createFromFormat('Y-m', $managedServicePeriod)->startOfMonth();
            $periodEnd = $periodStart->copy()->endOfMonth();
            $rowsQuery->whereHas('project', fn ($projectQuery) => $projectQuery->whereBetween('tgl_kontrak', [$periodStart, $periodEnd]));
        }

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

        $filename = 'matrix-dashboard-'.now()->format('Ymd_His').'.xlsx';

        return Excel::download(new DashboardExport($rowsQuery), $filename);
    }

    public function edit(ProjectBilling $charge): View
    {
        $charge->load('project.pm', 'project.pmo');
        $pmOptions = Employ::where('role', 1)->orderBy('employ_name')->get(['employ_id', 'employ_name']);
        $pmoOptions = Employ::where('role', 2)->orderBy('employ_name')->get(['employ_id', 'employ_name']);

        return view('charges.edit', compact('charge', 'pmOptions', 'pmoOptions'));
    }

    public function deleteSelected(Request $request)
    {
        $billing_id = $request->input('billing_id');
        try {
            DB::transaction(function () use ($billing_id) {
                $billing = ProjectBilling::findOrFail($billing_id);
                $project_id = $billing->project_id;

                $billing->delete();
                if ($project_id) {
                    Project::where('project_id', $project_id)->delete();
                }
            });

            return redirect()->back()->with('success', 'Data pembayaran berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus data: '.$e->getMessage());
        }
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'kategori_layanan' => ['required', 'in:MS,OTM'],
            'project_name' => ['required', 'string', 'max:120'],
            'user' => ['required', 'string', 'max:120'],
            'pm_id' => ['required', 'integer', 'exists:employ,employ_id'],
            'pmo_id' => ['nullable', 'integer', 'exists:employ,employ_id', 'required_if:kategori_layanan,MS'],
            'ms_no' => ['nullable', 'string', 'max:80', 'required_if:kategori_layanan,MS'],
            'cost_center' => ['required', 'string', 'max:80'],
            'no_kontrak' => ['required', 'string', 'max:120'],
            'nilai_kontrak' => ['required', 'numeric', 'min:0'],
            'nilai_bulan' => ['nullable', 'numeric', 'min:0', 'required_if:kategori_layanan,MS'],
            'tgl_kontrak' => ['required', 'date'],
            'tipe_pengadaan' => ['nullable', 'string', 'max:80', 'required_if:kategori_layanan,OTM'],
            'priode' => ['required', 'string', 'max:30'],
            'due_date_kontrak' => ['nullable', 'date', 'required_if:kategori_layanan,OTM'],
            'tgl_pembuatan_ba' => ['nullable', 'date'],
            'tgl_paraf_pm' => ['nullable', 'date'],
            'tgl_ttd_manager' => ['nullable', 'date'],
            'tgl_submit_dokumen' => ['nullable', 'date'],
            'tgl_permintaan_invoice' => ['nullable', 'date'],
            'note_1' => ['nullable', 'string', 'max:1000'],
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
                'pmo_id' => $data['pmo_id'] ?? null,
            ]);

            ProjectBilling::create([
                'project_id' => $project->project_id,
                'ms_no' => $data['ms_no'] ?? null,
                'kategori_layanan' => $data['kategori_layanan'],
                'tipe_pengadaan' => $data['tipe_pengadaan'],
                'priode' => $data['priode'],
                'nilai_bulan' => $data['nilai_bulan'] ?? null,
                'due_date_kontrak' => $data['due_date_kontrak'],
                'tgl_pembuatan_ba' => $data['tgl_pembuatan_ba'],
                'tgl_paraf_pm' => $data['tgl_paraf_pm'],
                'tgl_ttd_manager' => $data['tgl_ttd_manager'],
                'tgl_submit_dokumen' => $data['tgl_submit_dokumen'],
                'tgl_permintaan_invoice' => $data['tgl_permintaan_invoice'],
                'status' => $data['status'],
                'note' => $data['note_1'] ?? null,
                'note_1' => $data['note_1'] ?? null,
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
            'kategori_layanan' => ['required', 'in:MS,OTM'],
            'project_name' => ['required', 'string', 'max:120'],
            'user' => ['required', 'string', 'max:120'],
            'pm_id' => ['required', 'integer', 'exists:employ,employ_id'],
            'pmo_id' => ['nullable', 'integer', 'exists:employ,employ_id', 'required_if:kategori_layanan,MS'],
            'ms_no' => ['nullable', 'string', 'max:80', 'required_if:kategori_layanan,MS'],
            'cost_center' => ['required', 'string', 'max:80'],
            'no_kontrak' => ['required', 'string', 'max:120'],
            'nilai_kontrak' => ['required', 'numeric', 'min:0'],
            'tgl_kontrak' => ['required', 'date'],
            'nilai_bulan' => ['nullable', 'numeric', 'min:0', 'required_if:kategori_layanan,MS'],
            'tipe_pengadaan' => ['nullable', 'string', 'max:80', 'required_if:kategori_layanan,OTM'],
            'priode' => ['required', 'string', 'max:30'],
            'due_date_kontrak' => ['nullable', 'date', 'required_if:kategori_layanan,OTM'],
            'tgl_pembuatan_ba' => ['nullable', 'date'],
            'tgl_paraf_pm' => ['nullable', 'date'],
            'tgl_ttd_manager' => ['nullable', 'date'],
            'tgl_submit_dokumen' => ['nullable', 'date'],
            'tgl_permintaan_invoice' => ['nullable', 'date'],
            'note_1' => ['nullable', 'string', 'max:1000'],
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
            'pm_id' => $data['pm_id'],
            'pmo_id' => $data['pmo_id'] ?? null,
        ]);
        $previousDocumentPaths = [
            'file_kontrak' => $charge->file_kontrak,
            'file_ba' => $charge->file_ba,
        ];
        $billingData = collect($data)
            ->except(['project_name', 'user', 'cost_center', 'no_kontrak', 'nilai_kontrak', 'tgl_kontrak', 'pm_id', 'pmo_id', 'file_kontrak', 'file_ba'])
            ->all();

        $billingData['note'] = $data['note_1'] ?? null;

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

    private function page(string $page, $query, ?string $status = null, ?string $service = null, ?string $search = null, array $filters = [], ?string $managedServicePeriodInput = null): View
    {
        $chargesQuery = $query->with('project.pm')->latest('billing_id');

        $managedServicePeriod = trim((string) $managedServicePeriodInput);
        if (! preg_match('/^\d{4}-\d{2}$/', $managedServicePeriod)) {
            $managedServicePeriod = '';
        }

        $periodStart = $managedServicePeriod !== ''
            ? Carbon::createFromFormat('Y-m', $managedServicePeriod)->startOfMonth()
            : null;
        $periodEnd = $periodStart?->copy()->endOfMonth();

        $applyProjectPeriod = function ($builder) use ($periodStart, $periodEnd): void {
            if ($periodStart && $periodEnd) {
                $builder->whereHas('project', fn ($projectQuery) => $projectQuery->whereBetween('tgl_kontrak', [$periodStart, $periodEnd]));
            }
        };

        $applyJoinedProjectPeriod = function ($builder) use ($periodStart, $periodEnd): void {
            if ($periodStart && $periodEnd) {
                $builder->whereBetween('p.tgl_kontrak', [$periodStart, $periodEnd]);
            }
        };

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
        $applyProjectPeriod($monthly);
        $oneTime = ProjectBilling::where('kategori_layanan', 'OTM');
        $applyProjectPeriod($oneTime);

        $dashboardRowsQuery = ProjectBilling::with('project.pm')->latest('billing_id');
        $applyProjectPeriod($dashboardRowsQuery);

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

        $pmoOptions = Employ::query()
            ->where('role', 2)
            ->orderBy('employ_name')
            ->get(['employ_id', 'employ_name']);

        $trendQuery = DB::table('project')
            ->selectRaw('DATE_FORMAT(tgl_kontrak, "%Y-%m") as month, SUM(nilai_kontrak) as total_biaya')
            ->whereNotNull('tgl_kontrak')
            ->groupByRaw('DATE_FORMAT(tgl_kontrak, "%Y-%m")')
            ->orderBy('month');

        if ($periodStart && $periodEnd) {
            $trendQuery->whereBetween('tgl_kontrak', [$periodStart, $periodEnd]);
        }

        $trendData = $trendQuery->get();

        $chartLabels = $trendData->map(fn ($data) => Carbon::parse($data->month.'-01')->locale('id')->translatedFormat('F Y'))->values();
        $chartData = $trendData->pluck('total_biaya')->map(fn ($total) => (float) $total)->values();

        $managedServicePeriodOptions = DB::table('project as p')
            ->whereNotNull('p.tgl_kontrak')
            ->selectRaw('DATE_FORMAT(p.tgl_kontrak, "%Y-%m") as period')
            ->distinct()
            ->orderByDesc('period')
            ->pluck('period')
            ->values();

        $managedServiceRowsQuery = DB::table('project_billing as pb')
            ->join('project as p', 'p.project_id', '=', 'pb.project_id')
            ->leftJoin('employ as e', 'e.employ_id', '=', 'p.pm_id')
            ->where('pb.kategori_layanan', 'MS')
            ->select([
                'pb.project_id',
                'pb.status',
                'pb.note',
                'pb.tgl_pembuatan_ba',
                'pb.tgl_paraf_pm',
                'pb.tgl_ttd_manager',
                'pb.tgl_submit_dokumen',
                'pb.tgl_permintaan_invoice',
                'p.project_name',
                'p.tgl_kontrak',
                'e.employ_name as pm_name',
            ]);

        $applyJoinedProjectPeriod($managedServiceRowsQuery);

        $managedServiceRows = $managedServiceRowsQuery
            ->orderBy('e.employ_name')
            ->orderBy('p.project_name')
            ->get();

        $managedServiceStatusCounts = $managedServiceRows
            ->groupBy('project_id')
            ->map(fn ($projectRows) => $projectRows->contains(fn ($row) => strtolower((string) $row->status) === 'done') ? 'Done' : 'On Progress')
            ->countBy()
            ->sortKeys();

        $managedServicePmSummary = $managedServiceRows
            ->groupBy(fn ($row) => $row->pm_name ?: 'Belum ditentukan')
            ->map(function ($pmRows, $pmName) {
                $projectGroups = $pmRows->groupBy('project_id');
                $projects = $projectGroups->map(function ($projectRows) {
                    $row = $projectRows->first();
                    $completedMilestones = collect([
                        $row->tgl_pembuatan_ba,
                        $row->tgl_paraf_pm,
                        $row->tgl_ttd_manager,
                        $row->tgl_submit_dokumen,
                        $row->tgl_permintaan_invoice,
                    ])->filter()->count();

                    return [
                        'name' => $row->project_name,
                        'status' => strtolower((string) $row->status) === 'done' ? 'Done' : 'On Progress',
                        'progress' => $completedMilestones * 20,
                        'note' => trim((string) ($row->note ?? '')),
                    ];
                });

                return [
                    'pm' => $pmName,
                    'total_projects' => $projects->count(),
                    'progress_ba' => round($projects->avg('progress') ?? 0, 1),
                    'on_progress_projects' => $projects->where('status', 'On Progress')->pluck('name')->filter()->values()->all(),
                    'information' => $projects->pluck('note')->filter()->unique()->values()->all(),
                ];
            })
            ->sortBy('pm')
            ->values();

        $dashboardProjectsQuery = Project::query();
        if ($periodStart && $periodEnd) {
            $dashboardProjectsQuery->whereBetween('tgl_kontrak', [$periodStart, $periodEnd]);
        }

        $dashboardBillingsQuery = ProjectBilling::query();
        $applyProjectPeriod($dashboardBillingsQuery);

        $oneTimeTotalQuery = ProjectBilling::query()
            ->join('project as p', 'project_billing.project_id', '=', 'p.project_id')
            ->where('project_billing.kategori_layanan', 'OTM');
        $applyJoinedProjectPeriod($oneTimeTotalQuery);

        return view('welcome', [
            'charges' => $charges,
            'dashboardRows' => $dashboardRows,
            'dashboardTotalCount' => $dashboardBillingsQuery->count(),
            'page' => $page,
            'activeStatus' => $status,
            'activeService' => $service,
            'activeSearch' => $search,
            'activeFilters' => $normalizedFilters ?? [],
            'filterOptions' => $filterOptions,
            'pmOptions' => $pmOptions,
            'pmoOptions' => $pmoOptions,
            'monthlyTotal' => $monthly->sum('nilai_bulan'),
            'monthlyCount' => $monthly->count(),

            'oneTimeTotal' => $oneTimeTotalQuery->sum('p.nilai_kontrak'),
            'oneTimeCount' => $oneTime->count(),

            'dashboardTotals' => [
                'projectTotal' => $dashboardProjectsQuery->count(),
                'contractValueTotal' => $dashboardProjectsQuery->sum('nilai_kontrak'),
                'doneCount' => (clone $dashboardBillingsQuery)->where('status', 'Done')->distinct('project_id')->count('project_id'),
                'progressCount' => (clone $dashboardBillingsQuery)->where('status', 'In Progress')->distinct('project_id')->count('project_id'),
            ],

            'chartLabels' => $chartLabels,
            'chartData' => $chartData,
            'managedServicePeriod' => $managedServicePeriod,
            'managedServicePeriodOptions' => $managedServicePeriodOptions,
            'managedServiceStatusCounts' => $managedServiceStatusCounts,
            'managedServicePmSummary' => $managedServicePmSummary,
        ]);
    }
}
