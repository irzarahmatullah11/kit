<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Ruang Biaya | Catatan pengeluaran</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <div class="app-shell">
            <aside class="sidebar" id="sidebar">
                <div class="sidebar-top">
                    <a class="brand" href="{{ route('dashboard') }}" aria-label="Ruang Biaya">
                        <span class="brand-mark">RB</span><span class="brand-text">Ruang Biaya</span>
                    </a>
                    <button class="sidebar-toggle" type="button" data-sidebar-toggle aria-label="Sembunyikan sidebar">
                        <span class="sidebar-toggle-icon">⟨</span>
                    </button>
                </div>
                <nav class="sidebar-nav" aria-label="Navigasi utama">
                    <a class="sidebar-nav-link {{ $page === 'dashboard' ? 'is-active' : '' }}" href="{{ route('dashboard') }}"><span>◈</span><span class="nav-label">Dashboard</span></a>
                    <a class="sidebar-nav-link {{ $page === 'one_time' ? 'is-active' : '' }}" href="{{ route('charges.one-time') }}"><span>+</span><span class="nav-label">One-time / Charge</span></a>
                    <a class="sidebar-nav-link {{ $page === 'monthly' ? 'is-active' : '' }}" href="{{ route('charges.monthly') }}"><span>↻</span><span class="nav-label">Bulanan</span></a>
                </nav>
                <div class="sidebar-intro">
                    <span class="eyebrow">PERSONAL FINANCE</span>
                    <h2>Lebih tenang saat semua tercatat.</h2>
                    <p>Kelola tagihan bulanan dan pengeluaran sekali bayar di satu tempat.</p>
                </div>
                <div class="sidebar-footer"><span class="status-dot"></span><span>Penyimpanan aktif</span></div>
            </aside>

            <main class="main-content">
                <header class="topbar">
                    <div><span class="eyebrow">{{ $page === 'dashboard' ? 'MATRIX DASHBOARD' : ($page === 'one_time' ? 'ONE-TIME / CHARGE' : 'PEMBAYARAN BULANAN') }}</span><h1>{{ $page === 'dashboard' ? 'Matrix Dashboard' : ($page === 'one_time' ? 'One-time / Charge' : 'Pembayaran bulanan') }}</h1></div>
                    <div class="topbar-actions"><div class="date-chip">{{ now()->translatedFormat('l, d F Y') }}</div>@if ($page !== 'dashboard')<button class="icon-button" type="button" data-open-charge-modal aria-label="Tambah pengeluaran" title="Tambah pengeluaran">+</button>@endif</div>
                </header>

                @if (session('success'))
                    <div class="alert alert-success" role="status"><span class="alert-icon">&#10003;</span><span>{{ session('success') }}</span></div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-error" role="alert"><span class="alert-icon">!</span><span>{{ $errors->first() }}</span></div>
                @endif

                <div class="detail-hover-panel" data-detail-hover aria-hidden="true">
                    <div data-detail-hover-body></div>
                </div>

                @if ($page === 'dashboard')
                    <section class="summary-grid" aria-label="Ringkasan dashboard">
                        <article class="summary-card summary-card-primary">
                            <div class="summary-icon">Rp</div>
                            <div>
                                <span class="summary-label">Total nilai kontrak</span>
                                <strong>Rp {{ number_format($dashboardTotals['contractValueTotal'] ?? 0, 0, ',', '.') }}</strong>
                            </div>
                            <span class="summary-caption">{{ $dashboardTotals['projectTotal'] ?? 0 }} project terdaftar</span>
                        </article>
                        <article class="summary-card">
                            <div class="summary-icon summary-icon-gold">+</div>
                            <div>
                                <span class="summary-label">Pembayaran bulanan</span>
                                <strong>Rp {{ number_format($monthlyTotal, 0, ',', '.') }}</strong>
                            </div>
                            <span class="summary-caption">{{ $monthlyCount }} tagihan aktif</span>
                        </article>
                        <article class="summary-card summary-card-note">
                            <span class="summary-label">One-time / charge</span>
                            <strong>Rp {{ number_format($oneTimeTotal, 0, ',', '.') }}</strong>
                            <span class="summary-caption">{{ $oneTimeCount }} transaksi</span>
                        </article>
                    </section>

                    <section class="summary-grid" aria-label="Status dashboard">
                        <a href="{{ route('dashboard', ['status' => 'Done']) }}" class="summary-card status-filter-card {{ $activeStatus === 'Done' ? 'is-active' : '' }}">
                            <div class="summary-icon summary-icon-gold">✓</div>
                            <div>
                                <span class="summary-label">Status Done</span>
                                <strong>{{ $dashboardTotals['doneCount'] ?? 0 }}</strong>
                            </div>
                            <span class="summary-caption">penyelesaian selesai</span>
                        </a>
                        <a href="{{ route('dashboard', ['status' => 'In Progress']) }}" class="summary-card status-filter-card {{ $activeStatus === 'In Progress' ? 'is-active' : '' }}">
                            <div class="summary-icon">⏳</div>
                            <div>
                                <span class="summary-label">In Progress</span>
                                <strong>{{ $dashboardTotals['progressCount'] ?? 0 }}</strong>
                            </div>
                            <span class="summary-caption">masih berjalan</span>
                        </a>
                        <a href="{{ route('dashboard') }}" class="summary-card summary-card-note status-filter-card {{ !$activeStatus ? 'is-active' : '' }}">
                            <span class="summary-label">Total catatan</span>
                            <strong>{{ $dashboardRows->count() }}</strong>
                            <span class="summary-caption">{{ $activeStatus ? 'filter aktif: ' . $activeStatus : 'semua baris matrix' }}</span>
                        </a>
                    </section>

                    @if ($activeStatus)
                        <div class="status-detail-bar">
                            <span class="eyebrow">STATUS DETAIL</span>
                            <div class="status-detail-content">
                                <strong>{{ $activeStatus }}</strong>
                                <span>{{ $dashboardRows->count() }} item tampil</span>
                            </div>
                            <a href="{{ route('dashboard') }}" class="clear-filter-link">Tampilkan semua</a>
                        </div>
                    @endif

                    <section class="history-panel" aria-labelledby="dashboard-matrix-title">
                        <div class="section-heading history-heading">
                            <div>
                                <span class="eyebrow">MATRIX</span>
                                <h2 id="dashboard-matrix-title">Project billing matrix</h2>
                            </div>
                            <span class="record-count">{{ $dashboardRows->count() }} total</span>
                        </div>

                        @if ($dashboardRows->count())
                            <div class="history-table-wrap">
                                <table class="history-table">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>PM</th>
                                            <th>Project</th>
                                            <th>USER</th>
                                            <th>Type</th>
                                            <th>Cost center</th>
                                            <th>Kontrak / PO / JO</th>
                                            <th>Nilai kontrak</th>
                                            <th>Periode</th>
                                            <th>Tanggal kontrak</th>
                                            <th>Due date</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($dashboardRows as $index => $row)
                                            <tr data-hover-detail="{{ route('charges.show', $row) }}">
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $row->pm ?: '-' }}</td>
                                                <td><strong>{{ $row->name }}</strong><span class="table-note">{{ $row->note ?: 'Tanpa catatan' }}</span></td>
                                                <td>{{ $row->user_name ?: '-' }}</td>
                                                <td>{{ $row->type === 'monthly' ? 'Bulanan' : 'One-time' }}</td>
                                                <td>{{ $row->cost_center ?: '-' }}</td>
                                                <td>{{ $row->contract_reference ?: '-' }}</td>
                                                <td class="money-cell">Rp {{ number_format($row->amount, 0, ',', '.') }}</td>
                                                <td>{{ $row->procurement_period }}</td>
                                                <td>{{ $row->contract_date?->translatedFormat('d M Y') ?: '-' }}</td>
                                                <td>{{ $row->due_date?->translatedFormat('d M Y') ?: '-' }}</td>
                                                <td><button type="button" class="payment-status-button {{ $row->status === 'Done' ? 'status-done' : 'status-progress' }}">{{ $row->status === 'Done' ? 'Done' : 'On progress' }}</button></td>
                                                <td><a class="edit-link" href="{{ route('charges.edit', $row) }}">Edit</a></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="empty-state"><div class="empty-icon">+</div><h3>Belum ada data matrix</h3><p>Belum ada catatan project yang tersedia untuk dashboard ini.</p></div>
                        @endif
                    </section>
                @else
                    <div class="content-grid">
                        <section class="form-panel charge-form-panel" aria-labelledby="form-title" data-charge-modal aria-hidden="true">
                            <div class="modal-backdrop" data-close-charge-modal></div><div class="modal-card"><div class="section-heading"><div><span class="eyebrow">CATAT BIAYA</span><h2 id="form-title">Tambah pengeluaran</h2></div><button class="modal-close" type="button" data-close-charge-modal aria-label="Tutup form">&times;</button></div>
                            <form method="POST" action="{{ route('charges.store') }}" class="charge-form">
                                @csrf
                                <fieldset class="type-switcher">
                                    <legend>Jenis pengeluaran</legend>
                                    <label class="type-option"><input type="radio" name="type" value="monthly" @checked(old('type', 'monthly') === 'monthly')><span class="type-option-content"><strong>Bulanan</strong><small>Berulang setiap bulan</small></span><span class="radio-indicator"></span></label>
                                    <label class="type-option"><input type="radio" name="type" value="one_time" @checked(old('type') === 'one_time')><span class="type-option-content"><strong>One-time</strong><small>Sekali bayar</small></span><span class="radio-indicator"></span></label>
                                </fieldset>
                                <div class="field-group"><label for="name">Nama biaya <span>*</span></label><input id="name" name="name" type="text" value="{{ old('name') }}" placeholder="Contoh: Internet rumah" maxlength="120" required></div>
                                <div class="form-row">
                                    <div class="field-group"><label for="amount">Nominal <span>*</span></label><div class="input-prefix"><span>Rp</span><input id="amount" name="amount" type="number" value="{{ old('amount') }}" min="1" step="1" placeholder="0" required></div></div>
                                    <div class="field-group"><label for="occurred_on"><span data-date-label>Bulan</span> <span>*</span></label><input id="occurred_on" name="occurred_on" type="month" value="{{ old('occurred_on', now()->format('Y-m')) }}" required></div>
                                </div>
                                <div class="field-group"><label for="note">Note / Catatan <small>(opsional)</small></label><textarea id="note" name="note" rows="3" maxlength="500" placeholder="Tambahkan detail, pengingat, atau konteks biaya...">{{ old('note') }}</textarea></div>
                                <button class="submit-button" type="submit"><span>Simpan biaya</span><span aria-hidden="true">&#8594;</span></button>
                            </form></div>
                        </section>

                        <section class="history-panel" aria-labelledby="history-title">
                            <div class="section-heading history-heading"><div><span class="eyebrow">DAFTAR PEMBAYARAN</span><h2 id="history-title">{{ $page === 'one_time' ? 'LIST one-time / charge' : 'LIST pembayaran bulanan' }}</h2></div><span class="record-count">{{ $charges->total() }} total</span></div>

                            <form class="matrix-filter-form" method="GET" action="{{ $page === 'one_time' ? route('charges.one-time') : route('charges.monthly') }}">
                                <div class="matrix-tools">
                                    <div class="matrix-filter-group">
                                        <label for="listSearch">Cari list</label>
                                        <input id="listSearch" type="search" name="search" value="{{ request('search') }}" placeholder="Cari PM, project, USER, kontrak...">
                                    </div>
                                    <button class="matrix-filter-button" type="submit">Cari</button>
                                    @if (request('search'))
                                        <a class="matrix-clear-link" href="{{ $page === 'one_time' ? route('charges.one-time') : route('charges.monthly') }}">Reset</a>
                                    @endif
                                </div>
                            </form>

                            @if ($charges->count())
                                <div class="history-table-wrap"><table class="history-table"><thead><tr><th>No.</th><th>PM</th><th>Project</th><th>USER</th><th>Type pengadaan</th><th>Cost center</th><th>Kontrak / PO / JO</th><th>Nilai kontrak<br>(nominal uang)</th><th>Periode pengadaan<br>(bulan)</th><th>Tanggal kontrak / PO / JO</th><th>Masa kontrak<br>Due Date</th><th>Status pembayaran</th><th>Aksi</th></tr></thead><tbody>@foreach ($charges as $index => $row)<tr data-hover-detail="{{ route('charges.show', $row) }}"><td>{{ $charges->firstItem() + $index }}</td><td>{{ $row->pm ?: '-' }}</td><td><strong>{{ $row->name }}</strong><span class="table-note">{{ $row->note ?: 'Tanpa catatan' }}</span></td><td>{{ $row->user_name ?: '-' }}</td><td>{{ $row->procurement_type }}</td><td>{{ $row->cost_center ?: '-' }}</td><td>{{ $row->contract_reference ?: '-' }}</td><td class="money-cell">Rp {{ number_format($row->amount, 0, ',', '.') }}</td><td>{{ $row->procurement_period }}</td><td>{{ $row->contract_date?->translatedFormat('d M Y') ?: '-' }}</td><td>{{ $row->due_date?->translatedFormat('d M Y') ?: '-' }}</td><td><button type="button" class="payment-status-button {{ $row->status === 'Done' ? 'status-done' : 'status-progress' }}">{{ $row->status === 'Done' ? 'Done' : 'On progress' }}</button></td><td><a class="edit-link" href="{{ route('charges.edit', $row) }}">Edit</a></td></tr>@endforeach</tbody></table></div>
                            @else
                                <div class="empty-state"><div class="empty-icon">+</div><h3>Belum ada pembayaran</h3><p>Belum ada data pada kategori ini.</p></div>
                            @endif
                            @if ($charges->hasPages())<div class="pagination">{{ $charges->links() }}</div>@endif
                        </section>
                    </div>
                @endif
            </main>
        </div>
    </body>
</html>