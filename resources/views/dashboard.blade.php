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

    <!-- Area Filter & Pencarian & Export -->
    <div class="d-flex justify-content-between mb-3">
        <!-- Form Filter dan Pencarian -->
        <form action="{{ route('dashboard') }}" method="GET" class="d-flex gap-2">
            <!-- Dropdown Kategori (Otomatis submit saat dipilih) -->
            <select name="kategori" class="form-select" style="width: 200px;" onchange="this.form.submit()">
                <option value="">Semua Kategori</option>
                <option value="MS" {{ request('kategori') == 'MS' ? 'selected' : '' }}>Manage Service (MS)</option>
                <option value="OTC" {{ request('kategori') == 'OTC' ? 'selected' : '' }}>One-Time Charge (OTC)</option>
            </select>
            
            <!-- Input Cari Nama Proyek -->
            <input type="text" name="search" class="form-control" placeholder="Cari nama proyek..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-outline-secondary">Cari</button>
            
            <!-- Tombol Reset (Muncul kalau lagi nyari sesuatu) -->
            @if(request('kategori') || request('search'))
                <a href="{{ route('dashboard') }}" class="btn btn-danger">Reset</a>
            @endif
        </form>

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
                            <td class="text-center gap-2 d-flex justify-content-center">
                                <!-- Tombol Hover Tanggal -->
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

                                <!-- Tombol Pemicu Modal Upload -->
                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
                                    Upload PDF
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

<!-- Modal Form Upload PDF & Note -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="uploadModalLabel">Upload Dokumen & Catatan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="#" method="POST" enctype="multipart/form-data">
                    @csrf 
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">File Surat Kontrak (PDF)</label>
                        <input class="form-control" type="file" name="file_kontrak" accept=".pdf" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Berita Acara / LHP (PDF)</label>
                        <input class="form-control" type="file" name="file_ba" accept=".pdf" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Catatan (Note)</label>
                        <textarea class="form-control" name="note" rows="3" placeholder="Masukkan keterangan tambahan jika ada..."></textarea>
                    </div>

                    <div class="modal-footer px-0 pb-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Simpan Dokumen</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Script Bootstrap & Inisialisasi Tooltip -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });

<!-- Area Grafik Analitik -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title fw-bold text-muted mb-3">Rasio Status Proyek</h5>
                    <canvas id="statusChart" height="250"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title fw-bold text-muted mb-3">Tren Kategori Layanan</h5>
                    <canvas id="kategoriChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    

</script>
</body>
</html>