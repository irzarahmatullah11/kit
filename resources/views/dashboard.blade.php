<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Overview</title>
    <!-- Memanggil Bootstrap CSS agar tampilan langsung rapi -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light pt-5">

<div class="container">
    <h2 class="mb-4 fw-bold">Dashboard Overview</h2>

    <!-- Tiga Kotak Ringkasan -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-primary border-start border-5">
                <div class="card-body">
                    <h5 class="text-muted">Total Proyek</h5>
                    <h2 class="fw-bold">{{ $totalProyek }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-success border-start border-5">
                <div class="card-body">
                    <h5 class="text-muted">Selesai (Done)</h5>
                    <h2 class="fw-bold">{{ $totalDone }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-warning border-start border-5">
                <div class="card-body">
                    <h5 class="text-muted">Sedang Berjalan</h5>
                    <h2 class="fw-bold">{{ $totalInProgress }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Area Filter & Tombol Export -->
    <div class="d-flex justify-content-between mb-3">
        <div class="d-flex gap-2">
            <select class="form-select" style="width: 200px;">
                <option value="Semua">Semua Kategori</option>
                <option value="MS">Manage Service (MS)</option>
                <option value="OTC">One-Time Charge (OTC)</option>
            </select>
            <button class="btn btn-outline-secondary">Cari Data</button>
        </div>
        <button class="btn btn-success">Export to Excel</button>
    </div>

    <!-- Tabel Data Proyek -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover table-bordered mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nama Proyek</th>
                        <th>Kategori Layanan</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($billings as $item)
                        <tr>
                            <td class="fw-semibold">{{ $item->project->project_name ?? 'Tanpa Nama' }}</td>
                            <td>{{ $item->kategori_layanan }}</td>
                            <td>
                                @if($item->status == 'Done')
                                    <span class="badge bg-success">Done</span>
                                @else
                                    <span class="badge bg-warning text-dark">In Progress</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-info text-white" 
        data-bs-toggle="tooltip" 
        data-bs-html="true" 
        data-bs-placement="left"
        title="
        <div class='text-start'>
            <b>Tgl BA:</b> {{ $item->tgl_pembuatan_ba ?? 'Belum' }} <br>
            <b>Paraf PM:</b> {{ $item->tgl_paraf_pm ?? 'Belum' }} <br>
            <b>TTD Manager:</b> {{ $item->tgl_tdd_manager ?? 'Belum' }} <br>
            <b>Submit Dokumen:</b> {{ $item->tgl_submit_dokumen ?? 'Belum' }} <br>
            <b>Req Invoice:</b> {{ $item->tgl_permintaan_invoice ?? 'Belum' }}
        </div>
        ">
    Sorot Progres
</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">Belum ada data proyek dari database.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
</body>
</html>