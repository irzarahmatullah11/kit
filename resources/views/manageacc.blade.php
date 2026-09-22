<x-app-layout>
    @if (session('success'))
            <div class="alert alert-success" role="status">
                <span class="alert-icon">&#10003;</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-error" role="alert">
                <span class="alert-icon">!</span>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif
    <div class="app-shell">
        <main class="main-content">
            <!-- Header (Topbar) -->
            <header class="topbar">
                <div>
                    <span class="eyebrow">ADMIN PANEL</span>
                    <h1>Daftar Akun Employee</h1>
                </div>
                <div class="topbar-actions">
                    <div class="date-chip">{{ now()->translatedFormat('l, d F Y') }}</div>
                    <!-- Tombol Tambah User (Disabled), menggunakan .icon-button dari app.css -->
                    <a href="{{ route('admin.account.create') }}">
                        <button class="icon-button" type="button" aria-label="Tambah User" title="Tambah User">
                            +
                        </button>
                    </a>
                </div>
            </header>

            <!-- Tabel Data Employee -->
            <section class="history-panel" aria-labelledby="employee-list-title">
                <div class="section-heading history-heading">
                    <div>
                        <span class="eyebrow">DATA AKUN</span>
                        <h2 id="employee-list-title">List Employee (Admin)</h2>
                    </div>
                </div>

                <div class="history-table-wrap">
                    <table class="account-table">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>ID Employee</th>
                                <th>Nama Employee</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($employees as $index => $employee)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $employee->employ_id }}</td>
                                    <td><strong>{{ $employee->employ_name }}</strong></td>
                                    <td>{{ $employee->email }}</td>
                                    <td>{{ $employee->roleData->role_name ?? 'Tidak ada role' }}</td>
                                    <td>
                                        <div style="display: flex; gap: 12px; align-items: center;">
                                            <a href="{{ route('admin.account.edit', $employee->employ_id) }}" class="edit-link" style="background: none; border: none; padding: 0;">Edit</a>

                                            <form action="{{ route('admin.account.delete', $employee->employ_id) }}" method="POST" style="margin: 0;" onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="edit-link" style="background: none; border: none; padding: 0; color: var(--coral); cursor: pointer;">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="padding: 0; border: none;">
                                        <!-- Empty State disamakan dengan gaya app.css -->
                                        <div class="empty-state">
                                            <div class="empty-icon">!</div>
                                            <h3>Belum ada data employee</h3>
                                            <p>Tidak ada data employee ditemukan dalam sistem.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
</x-app-layout>