<x-app-layout>
            <main class="main-content">
                <header class="topbar">
                    <div><span class="eyebrow">{{ $page === 'dashboard' ? 'MATRIX DASHBOARD' : ($page === 'one_time' ? 'ONETIME CHARGE' : 'Manage Service') }}</span><h1>{{ $page === 'dashboard' ? 'Matrix Dashboard' : ($page === 'one_time' ? 'One time Charge' : 'Manage Service') }}</h1></div>
                    <div class="topbar-actions">
                        @if ($page === 'dashboard')
                            <button class="secondary-button" type="button" data-open-export-modal aria-label="Export XLSX" title="Export XLSX">
                                Export XLSX
                            </button>
                        @endif
                        <div class="date-chip">{{ now()->translatedFormat('l, d F Y') }}</div>
                        @if ($page !== 'dashboard')
                            <button class="icon-button" type="button" data-open-charge-modal aria-label="Tambah pengeluaran" title="Tambah pengeluaran">+</button>
                        @endif
                    </div>
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
                    <section class="export-panel" data-export-modal aria-hidden="true">
                        <div class="modal-backdrop" data-close-export-modal></div>
                        <div class="modal-card export-card">
                            <div class="section-heading">
                                <div>
                                    <span class="eyebrow">EXPORT XLSX</span>
                                    <h2>Pratinjau ekspor data dashboard</h2>
                                </div>
                                <button class="modal-close" type="button" data-close-export-modal aria-label="Tutup popup ekspor">&times;</button>
                            </div>

                            <div class="export-preview">
                                <div class="export-summary">
                                    <span class="export-summary-label">Jumlah data siap diekspor</span>
                                    <strong>{{ $dashboardTotalCount }}</strong>
                                    <small>baris matrix</small>
                                </div>

                                <div class="export-meta">
                                    <div class="export-meta-item">
                                        <span class="export-meta-label">Status</span>
                                        <strong>{{ $activeStatus ?: 'Semua status' }}</strong>
                                    </div>
                                    <div class="export-meta-item">
                                        <span class="export-meta-label">Layanan</span>
                                        <strong>{{ $activeService && $activeService !== 'all' ? strtoupper($activeService) : 'Semua layanan' }}</strong>
                                    </div>
                                </div>

                                @if ($activeSearch || ! empty($activeFilters))
                                    <div class="export-filter-list">
                                        @if ($activeSearch)
                                            <span class="export-filter-tag">Search: {{ $activeSearch }}</span>
                                        @endif
                                        @foreach ($activeFilters as $filterKey => $filterValue)
                                            @php
                                                $filterLabel = collect([
                                                    'pm' => 'PM',
                                                    'project' => 'Project',
                                                    'user' => 'User',
                                                    'type' => 'Type',
                                                    'cost_center' => 'Cost Center',
                                                    'contract_reference' => 'Referensi Kontrak',
                                                    'nilai_kontrak' => 'Nilai Kontrak',
                                                    'periode' => 'Periode',
                                                    'contract_date' => 'Tanggal Kontrak',
                                                    'due_date' => 'Due Date',
                                                    'status' => 'Status',
                                                ])->get($filterKey, ucfirst(str_replace('_', ' ', $filterKey)));
                                            @endphp
                                            <span class="export-filter-tag">{{ $filterLabel }}: {{ is_array($filterValue) ? implode(', ', $filterValue) : $filterValue }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <div class="export-actions">
                                <button class="secondary-button" type="button" data-close-export-modal>Batalkan</button>
                                <a class="submit-button export-button" href="{{ route('dashboard.export-csv') }}">Download XLSX</a>
                            </div>
                        </div>
                    </section>
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
                                <span class="summary-label">Manage Service</span>
                                <strong>Rp {{ number_format($monthlyTotal, 0, ',', '.') }}</strong>
                            </div>
                            <span class="summary-caption">{{ $monthlyCount }} tagihan aktif</span>
                        </article>
                        <article class="summary-card summary-card-note">
                            <span class="summary-label">One time charge</span>
                            <strong>Rp {{ number_format($oneTimeTotal, 0, ',', '.') }}</strong>
                            <span class="summary-caption">{{ $oneTimeCount }} transaksi</span>
                        </article>
                    </section>

                    <section class="summary-grid" aria-label="Status dashboard">
                        <div class="summary-card status-filter-card {{ $activeStatus === 'Done' ? 'is-active' : '' }}" aria-disabled="true">
                            <div class="summary-icon summary-icon-gold">✓</div>
                            <div>
                                <span class="summary-label">Status Done</span>
                                <strong>{{ $dashboardTotals['doneCount'] ?? 0 }}</strong>
                            </div>
                            <span class="summary-caption">penyelesaian selesai</span>
                        </div>
                        <div class="summary-card status-filter-card {{ $activeStatus === 'In Progress' ? 'is-active' : '' }}" aria-disabled="true">
                            <div class="summary-icon">⏳</div>
                            <div>
                                <span class="summary-label">In Progress</span>
                                <strong>{{ $dashboardTotals['progressCount'] ?? 0 }}</strong>
                            </div>
                            <span class="summary-caption">masih berjalan</span>
                        </div>
                        <a href="{{ route('dashboard') }}" class="summary-card summary-card-note status-filter-card {{ !$activeStatus ? 'is-active' : '' }}">
                            <span class="summary-label">Total catatan</span>
                            <strong>{{ $dashboardTotalCount }}</strong>
                            <span class="summary-caption">{{ $activeStatus ? 'filter aktif: ' . $activeStatus : 'semua baris matrix' }}</span>
                        </a>
                    </section>

                   
                @else
                    <div class="content-grid">
                        <section class="form-panel charge-form-panel" aria-labelledby="form-title" data-charge-modal aria-hidden="true">
                            <div class="modal-backdrop" data-close-charge-modal></div><div class="modal-card"><div class="section-heading"><div><span class="eyebrow">CATAT BIAYA</span><h2 id="form-title">Tambah pengeluaran</h2></div><button class="modal-close" type="button" data-close-charge-modal aria-label="Tutup form">&times;</button></div>
                            <form method="POST" action="{{ route('charges.store') }}" class="charge-form" enctype="multipart/form-data">
                                @csrf
                                <div class="form-row">
                                    <div class="field-group"><label for="project_name">Project <span>*</span></label><input id="project_name" name="project_name" value="{{ old('project_name') }}" maxlength="120" required></div>
                                    <div class="field-group"><label for="user">USER <span>*</span></label><input id="user" name="user" value="{{ old('user') }}" maxlength="120" required></div>
                                </div>
                                <div class="form-row">
                                    <div class="field-group"><label for="pm_id">PM <span>*</span></label><select id="pm_id" name="pm_id" required><option value="">Pilih PM</option>@foreach ($pmOptions as $pm)<option value="{{ $pm->employ_id }}" @selected(old('pm_id') == $pm->employ_id)>{{ $pm->employ_name }}</option>@endforeach</select></div>
                                    <div class="field-group"><label for="cost_center">Cost center <span>*</span></label><input id="cost_center" name="cost_center" value="{{ old('cost_center') }}" maxlength="80" required></div>
                                </div>
                                <div class="form-row">
                                    <div class="field-group"><label for="no_kontrak">Kontrak / PO / JO <span>*</span></label><input id="no_kontrak" name="no_kontrak" value="{{ old('no_kontrak') }}" maxlength="120" required></div>
                                    <div class="field-group"><label for="nilai_kontrak">Nilai kontrak <span>*</span></label><input id="nilai_kontrak" name="nilai_kontrak" type="number" value="{{ old('nilai_kontrak') }}" min="0" required></div>
                                </div>
                                <div class="form-row">
                                    <div class="field-group"><label for="tgl_kontrak">Tanggal kontrak <span>*</span></label><input id="tgl_kontrak" name="tgl_kontrak" type="date" value="{{ old('tgl_kontrak') }}" required></div>
                                    <div class="field-group"><label for="kategori_layanan">Kategori layanan <span>*</span></label><select id="kategori_layanan" name="kategori_layanan" required><option value="MS" @selected(old('kategori_layanan', $page === 'one_time' ? 'OTM' : 'MS') === 'MS')>MS</option><option value="OTM" @selected(old('kategori_layanan', $page === 'one_time' ? 'OTM' : 'MS') === 'OTM')>OTM</option></select></div>
                                </div>
                                <div class="form-row">
                                    <div class="field-group"><label for="tipe_pengadaan">Type pengadaan</label><input id="tipe_pengadaan" name="tipe_pengadaan" value="{{ old('tipe_pengadaan') }}" maxlength="80"></div>
                                    <div class="field-group"><label for="priode">Periode pengadaan (bulan) <span>*</span></label><input id="priode" name="priode" value="{{ old('priode') }}" maxlength="30" required></div>
                                </div>
                                <div class="form-row">
                                    <div class="field-group"><label for="due_date_kontrak">Due Date</label><input id="due_date_kontrak" name="due_date_kontrak" type="date" value="{{ old('due_date_kontrak') }}"></div>
                                    <div class="field-group"><label for="tgl_pembuatan_ba">Tanggal pembuatan BA</label><input id="tgl_pembuatan_ba" name="tgl_pembuatan_ba" type="date" value="{{ old('tgl_pembuatan_ba') }}"></div>
                                </div>
                                <div class="form-row">
                                    <div class="field-group"><label for="tgl_paraf_pm">Tanggal paraf PM</label><input id="tgl_paraf_pm" name="tgl_paraf_pm" type="date" value="{{ old('tgl_paraf_pm') }}"></div>
                                    <div class="field-group"><label for="tgl_ttd_manager">Tanggal Tanda Tangan Manager</label><input id="tgl_ttd_manager" name="tgl_ttd_manager" type="date" value="{{ old('tgl_ttd_manager') }}"></div>
                                </div>
                                <div class="form-row">
                                    <div class="field-group"><label for="tgl_submit_dokumen">Tanggal submit dokumen</label><input id="tgl_submit_dokumen" name="tgl_submit_dokumen" type="date" value="{{ old('tgl_submit_dokumen') }}"></div>
                                    <div class="field-group"><label for="tgl_permintaan_invoice">Tanggal permintaan invoice</label><input id="tgl_permintaan_invoice" name="tgl_permintaan_invoice" type="date" value="{{ old('tgl_permintaan_invoice') }}"></div>
                                </div>
                                <div class="field-group"><label for="note">Note / Catatan</label><textarea id="note" name="note" rows="3" maxlength="500">{{ old('note') }}</textarea></div>
                                <div class="form-row">
                                    <div class="field-group"><label for="file_kontrak">File kontrak (PDF)</label><input id="file_kontrak" name="file_kontrak" type="file" accept="application/pdf,.pdf"><x-input-error :messages="$errors->get('file_kontrak')" /></div>
                                    <div class="field-group"><label for="file_ba">File BA (PDF)</label><input id="file_ba" name="file_ba" type="file" accept="application/pdf,.pdf"><x-input-error :messages="$errors->get('file_ba')" /></div>
                                </div>
                                <button class="submit-button" type="submit"><span>Simpan biaya</span><span aria-hidden="true">&#8594;</span></button>
                            </form></div>
                        </section>

                        <section class="history-panel" aria-labelledby="history-title">
                            <div class="section-heading history-heading"><div><span class="eyebrow">DAFTAR PEMBAYARAN</span><h2 id="history-title">{{ $page === 'one_time' ? 'LIST one time charge' : 'LIST Manage Service' }}</h2></div><span class="record-count">{{ $charges->total() }} total</span></div>

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
                                <div class="history-table-wrap"><table class="history-table"><thead><tr><th>No.</th><th>PM</th><th>Project</th><th>USER</th><th>Type pengadaan</th><th>Cost center</th><th>Kontrak / PO / JO</th><th>Nilai kontrak<br>(nominal uang)</th><th>Periode pengadaan<br>(bulan)</th><th>Tanggal kontrak / PO / JO</th><th>Masa kontrak<br>Due Date</th><th>Status pembayaran</th><th>Aksi</th><th>Cetak PDF</th></tr></thead><tbody>@foreach ($charges as $index => $row)<tr data-hover-detail="{{ route('charges.show', $row) }}"><td>{{ $charges->firstItem() + $index }}</td><td>{{ $row->pm ?: '-' }}</td><td><strong>{{ $row->name }}</strong><span class="table-note">{{ $row->note ?: 'Tanpa catatan' }}</span></td><td>{{ $row->user_name ?: '-' }}</td><td>{{ $row->procurement_type }}</td><td>{{ $row->cost_center ?: '-' }}</td><td>{{ $row->contract_reference ?: '-' }}</td><td class="money-cell">Rp {{ number_format($row->amount, 0, ',', '.') }}</td><td>{{ $row->procurement_period }}</td><td>{{ $row->contract_date?->translatedFormat('d M Y') ?: '-' }}</td><td>{{ $row->due_date?->translatedFormat('d M Y') ?: '-' }}</td><td><button type="button" class="payment-status-button {{ $row->status === 'Done' ? 'status-done' : 'status-progress' }}">{{ $row->status === 'Done' ? 'Done' : 'On progress' }}</button></td><td><a class="edit-link" href="{{ route('charges.edit', $row) }}">Edit</a></td><td><a class="edit-link" href="{{ route('charges.print', $row) }}" target="_blank" rel="noopener">Cetak</a></td></tr>@endforeach</tbody></table></div>
                            @else
                                <div class="empty-state"><div class="empty-icon">+</div><h3>Belum ada pembayaran</h3><p>Belum ada data pada kategori ini.</p></div>
                            @endif
                            @if ($charges->hasPages())<div class="pagination">{{ $charges->links() }}</div>@endif
                        </section>
                    </div>
                @endif
            </main>
</x-app-layout>