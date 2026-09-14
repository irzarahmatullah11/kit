<?php

namespace App\Http\Controllers;

use App\Models\Employ;
use App\Models\Project;
use App\Models\ProjectBilling;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

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
        ]);

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

        DB::transaction(function () use ($data): void {
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
        ]);

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
        $charge->update(collect($data)->except(['project_name', 'user', 'cost_center', 'no_kontrak', 'nilai_kontrak', 'tgl_kontrak'])->all());

        $destination = $data['kategori_layanan'] === 'OTM' ? 'charges.one-time' : 'charges.monthly';

        return to_route($destination)->with('success', 'Pembayaran berhasil diperbarui.');
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
            'oneTimeTotal' => $oneTime->sum('nilai_bulan'),
            'oneTimeCount' => $oneTime->count(),
            'dashboardTotals' => [
                'projectTotal' => Project::count(),
                'contractValueTotal' => Project::sum('nilai_kontrak'),
                'doneCount' => ProjectBilling::where('status', 'Done')->count(),
                'progressCount' => ProjectBilling::where('status', 'In Progress')->count(),
            ],
        ]);
    }
}
