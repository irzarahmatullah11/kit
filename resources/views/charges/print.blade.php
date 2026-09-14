<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail {{ $charge->project?->project_name ?? 'Project' }}</title>
    <style>
        :root { color-scheme: light; font-family: Arial, sans-serif; color: #17202a; }
        body { margin: 0; background: #fff; }
        .document { max-width: 760px; margin: 0 auto; padding: 48px 56px; }
        .header { border-bottom: 2px solid #17202a; padding-bottom: 20px; margin-bottom: 28px; }
        .eyebrow { color: #607080; font-size: 11px; font-weight: 700; letter-spacing: 1.5px; }
        h1 { margin: 8px 0 0; font-size: 28px; }
        .summary { display: grid; grid-template-columns: 1fr 1fr; gap: 16px 32px; margin-bottom: 32px; }
        .summary-item, .detail-row { display: flex; justify-content: space-between; gap: 24px; padding: 11px 0; border-bottom: 1px solid #d9e0e5; }
        .summary-item span, .detail-row span { color: #607080; }
        .summary-item strong, .detail-row strong { text-align: right; }
        h2 { font-size: 17px; margin: 0 0 10px; }
        .detail-list { border-top: 1px solid #17202a; }
        .note { margin-top: 28px; padding: 14px; background: #f3f6f7; }
        .note strong { display: block; margin-bottom: 6px; }
        .print-button { margin-top: 32px; padding: 10px 16px; border: 1px solid #17202a; background: #fff; cursor: pointer; }
        @media print { .document { padding: 0; } .print-button { display: none; } }
        @media (max-width: 600px) { .document { padding: 28px 20px; } .summary { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <main class="document">
        <header class="header">
            <span class="eyebrow">DETAIL PROJECT BILLING</span>
            <h1>{{ $charge->project?->project_name ?? '-' }}</h1>
        </header>

        <section class="summary">
            <div class="summary-item"><span>PM</span><strong>{{ $charge->pm ?: '-' }}</strong></div>
            <div class="summary-item"><span>USER</span><strong>{{ $charge->user_name ?: '-' }}</strong></div>
            <div class="summary-item"><span>Cost center</span><strong>{{ $charge->cost_center ?: '-' }}</strong></div>
            <div class="summary-item"><span>Kontrak / PO / JO</span><strong>{{ $charge->contract_reference ?: '-' }}</strong></div>
            <div class="summary-item"><span>Nilai kontrak</span><strong>Rp {{ number_format($charge->amount, 0, ',', '.') }}</strong></div>
            <div class="summary-item"><span>Status</span><strong>{{ $charge->status ?: '-' }}</strong></div>
        </section>

        <section>
            <h2>Timeline dokumen</h2>
            <div class="detail-list">
                <div class="detail-row"><span>Tanggal kontrak</span><strong>{{ $charge->contract_date?->translatedFormat('d M Y') ?? '-' }}</strong></div>
                <div class="detail-row"><span>Tanggal pembuatan BA</span><strong>{{ $charge->tgl_pembuatan_ba?->translatedFormat('d M Y') ?? '-' }}</strong></div>
                <div class="detail-row"><span>Tanggal paraf PM</span><strong>{{ $charge->tgl_paraf_pm?->translatedFormat('d M Y') ?? '-' }}</strong></div>
                <div class="detail-row"><span>Tanda Tangan Manager</span><strong>{{ $charge->tgl_ttd_manager?->translatedFormat('d M Y') ?? '-' }}</strong></div>
                <div class="detail-row"><span>Tanggal submit dokumen</span><strong>{{ $charge->tgl_submit_dokumen?->translatedFormat('d M Y') ?? '-' }}</strong></div>
                <div class="detail-row"><span>Tanggal permintaan invoice</span><strong>{{ $charge->tgl_permintaan_invoice?->translatedFormat('d M Y') ?? '-' }}</strong></div>
            </div>
        </section>

        @if ($charge->note)
            <div class="note"><strong>Catatan</strong>{{ $charge->note }}</div>
        @endif

        <button class="print-button" type="button" onclick="window.print()">Cetak / Simpan sebagai PDF</button>
    </main>
    <script>window.addEventListener('load', () => window.print());</script>
</body>
</html>