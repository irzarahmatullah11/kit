<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail pembayaran</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="app-shell">
        <aside class="sidebar">
            <div class="sidebar-top">
                <a class="brand" href="{{ route('dashboard') }}" aria-label="Ruang Biaya">
                    <span class="brand-mark">RB</span><span class="brand-text">Ruang Biaya</span>
                </a>
            </div>
            <nav class="sidebar-nav" aria-label="Navigasi utama">
                <a class="sidebar-nav-link" href="{{ route('dashboard') }}"><span>◈</span><span class="nav-label">Dashboard</span></a>
                <a class="sidebar-nav-link" href="{{ route('charges.one-time') }}"><span>+</span><span class="nav-label">One-time / Charge</span></a>
                <a class="sidebar-nav-link" href="{{ route('charges.monthly') }}"><span>↻</span><span class="nav-label">Bulanan</span></a>
            </nav>
            <div class="sidebar-intro">
                <span class="eyebrow">PERSONAL FINANCE</span>
                <h2>Lebih tenang saat semua tercatat.</h2>
                <p>Kelola tagihan bulanan dan pengeluaran sekali bayar di satu tempat.</p>
            </div>
            <div class="sidebar-footer"><span class="status-dot"></span><span>Penyimpanan aktif</span></div>
        </aside>

        <main class="main-content edit-page">
            <div class="charge-form">
                <div class="detail-text-list">
                    <div class="detail-text-row">
                        <span>Tanggal pembuatan BA</span>
                        <strong>{{ $charge->tgl_pembuatan_ba?->translatedFormat('d M Y') ?? '-' }}</strong>
                    </div>
                    <div class="detail-text-row">
                        <span>Tanggal paraf PM</span>
                        <strong>{{ $charge->tgl_paraf_pm?->translatedFormat('d M Y') ?? '-' }}</strong>
                    </div>
                    <div class="detail-text-row">
                        <span>Tanggal submit dokumen</span>
                        <strong>{{ $charge->tgl_submit_dokumen?->translatedFormat('d M Y') ?? '-' }}</strong>
                    </div>
                    <div class="detail-text-row">
                        <span>Tanggal permintaan invoice</span>
                        <strong>{{ $charge->tgl_permintaan_invoice?->translatedFormat('d M Y') ?? '-' }}</strong>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
