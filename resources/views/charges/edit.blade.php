<x-app-layout>
    <main class="main-content edit-page">
        <header class="topbar">
            <div><span class="eyebrow">EDIT PEMBAYARAN</span><h1>{{ $charge->project?->project_name }}</h1></div>
            <a class="back-link" href="{{ route('dashboard') }}">Kembali</a>
        </header>
        <section class="form-panel edit-panel">
            <form method="POST" action="{{ route('charges.update', $charge) }}" class="charge-form" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="field-group service-category-field">
                    <label for="edit_kategori_layanan">Kategori layanan <span>*</span></label>
                    <select id="edit_kategori_layanan" name="kategori_layanan" required data-service-category>
                        <option value="MS" @selected(old('kategori_layanan', $charge->kategori_layanan) === 'MS')>Managed Service (MS)</option>
                        <option value="OTM" @selected(old('kategori_layanan', $charge->kategori_layanan) === 'OTM')>One-Time Charge (OTM)</option>
                    </select>
                </div>
                <div class="service-fields service-fields-ms" data-service-fields="MS">
                    <div class="form-row">
                        <div class="field-group"><label>MS No / Nomor MS <span>*</span></label><input name="ms_no" value="{{ old('ms_no', $charge->ms_no) }}" data-required-ms></div>
                        <div class="field-group"><label>PMO <span>*</span></label><select name="pmo_id" data-required-ms><option value="">Pilih PMO</option>@foreach ($pmoOptions as $pmo)<option value="{{ $pmo->employ_id }}" @selected(old('pmo_id', $charge->project?->pmo_id) == $pmo->employ_id)>{{ $pmo->employ_name }}</option>@endforeach</select></div>
                    </div>
                    <div class="form-row">
                        <div class="field-group"><label>Nilai Bulanan / BA <span>*</span></label><input type="number" name="nilai_bulan" value="{{ old('nilai_bulan', $charge->nilai_bulan) }}" min="0" data-required-ms></div>
                        <div class="field-group"><label>Periode Tagihan (Bulan) <span>*</span></label><input name="priode" value="{{ old('priode', $charge->priode) }}" data-required-ms></div>
                    </div>
                </div>
                <div class="form-row"><div class="field-group"><label>Project</label><input name="project_name" value="{{ old('project_name', $charge->project?->project_name) }}" required></div><div class="field-group"><label>USER</label><input name="user" value="{{ old('user', $charge->project?->user) }}" required></div></div>
                <div class="form-row"><div class="field-group"><label>PM</label><select name="pm_id" required><option value="">Pilih PM</option>@foreach ($pmOptions as $pm)<option value="{{ $pm->employ_id }}" @selected(old('pm_id', $charge->project?->pm_id) == $pm->employ_id)>{{ $pm->employ_name }}</option>@endforeach</select></div><div class="field-group"><label>Cost center</label><input name="cost_center" value="{{ old('cost_center', $charge->project?->cost_center) }}" required></div></div>
                <div class="form-row"><div class="field-group"><label>Kontrak / PO / JO</label><input name="no_kontrak" value="{{ old('no_kontrak', $charge->project?->no_kontrak) }}" required></div><div class="field-group"><label>Nilai kontrak</label><input type="number" name="nilai_kontrak" value="{{ old('nilai_kontrak', $charge->project?->nilai_kontrak) }}" min="0" required></div></div>
                <div class="form-row"><div class="field-group"><label>Tanggal kontrak</label><input type="date" name="tgl_kontrak" value="{{ old('tgl_kontrak', $charge->project?->tgl_kontrak?->format('Y-m-d')) }}" required></div><div class="field-group service-fields service-fields-otm" data-service-fields="OTM"><label>Type Pengadaan <span>*</span></label><input name="tipe_pengadaan" value="{{ old('tipe_pengadaan', $charge->tipe_pengadaan) }}" data-required-otm></div></div>
                <div class="form-row service-fields service-fields-otm" data-service-fields="OTM"><div class="field-group"><label>Periode Pengadaan <span>*</span></label><input name="priode" value="{{ old('priode', $charge->priode) }}" data-required-otm></div><div class="field-group"><label>Masa Kontrak Due Date <span>*</span></label><input type="date" name="due_date_kontrak" value="{{ old('due_date_kontrak', $charge->due_date_kontrak?->format('Y-m-d')) }}" data-required-otm></div></div>
                <div class="field-group"><label>Pembuatan BA, LHP</label><input type="date" name="tgl_pembuatan_ba" value="{{ old('tgl_pembuatan_ba', $charge->tgl_pembuatan_ba?->format('Y-m-d')) }}"></div>
                <div class="form-row"><div class="field-group"><label>Paraf PM</label><input type="date" name="tgl_paraf_pm" value="{{ old('tgl_paraf_pm', $charge->tgl_paraf_pm?->format('Y-m-d')) }}"></div><div class="field-group"><label>TTD Manager</label><input type="date" name="tgl_ttd_manager" value="{{ old('tgl_ttd_manager', $charge->tgl_ttd_manager?->format('Y-m-d')) }}"></div></div>
                <div class="form-row"><div class="field-group"><label>Dokumen BA/LHP dikirim ke user</label><input type="date" name="tgl_submit_dokumen" value="{{ old('tgl_submit_dokumen', $charge->tgl_submit_dokumen?->format('Y-m-d')) }}"></div><div class="field-group"><label>Permintaan invoice keuangan KIT</label><input type="date" name="tgl_permintaan_invoice" value="{{ old('tgl_permintaan_invoice', $charge->tgl_permintaan_invoice?->format('Y-m-d')) }}"></div></div>
                <div class="field-group"><label>Note 1 (Catatan / Informasi)</label><textarea name="note_1" rows="3">{{ old('note_1', $charge->note_1 ?? $charge->note) }}</textarea></div>
                <div class="form-row"><div class="field-group"><label for="file_kontrak">File kontrak (PDF)</label><input id="file_kontrak" name="file_kontrak" type="file" accept="application/pdf,.pdf"><x-input-error :messages="$errors->get('file_kontrak')" /></div><div class="field-group"><label for="file_ba">File BA (PDF)</label><input id="file_ba" name="file_ba" type="file" accept="application/pdf,.pdf"><x-input-error :messages="$errors->get('file_ba')" /></div></div>
                <div class="edit-actions"><a class="cancel-link" href="{{ route('dashboard') }}">Batal</a><button class="submit-button" type="submit">Simpan perubahan</button></div>
            </form>
        </section>
    </main>
</x-app-layout>
