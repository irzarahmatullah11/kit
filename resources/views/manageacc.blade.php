<x-app-layout>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>Daftar Akun Employee (Admin)</h3>
            
            <!-- Tombol Tambah User (Disabled) -->
            <button type="button" class="btn btn-primary" disabled>
                + Tambah User
            </button>
        </div>

        <div class="card">
            <div class="card-body p-0 text-nowrap table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center" width="5%">No</th>
                            <th>ID Employee</th>
                            <th>Nama Employee</th>
                            <th>Email</th>
                            <th>Password</th>
                            <th>Role</th>
                            <th class="text-center" width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employees as $index => $employee)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>{{ $employee->employ_id }}</td>
                                <td>{{ $employee->employ_name }}</td>
                                <td>{{ $employee->email }}</td>
                                
                                <!-- Menampilkan kolom password -->
                                <td class="text-truncate" style="max-width: 150px;" title="{{ $employee->password }}">
                                    {{ $employee->password }}
                                </td>
                                
                                <!-- Mengambil role_name dari relasi tabel role -->
                                <td>{{ $employee->role->role_name ?? 'Tidak ada role' }}</td>
                                <td class="text-center">
                                    <!-- Tombol Edit dan Hapus (Disabled) -->
                                    <button type="button" class="btn btn-sm btn-warning" disabled>Edit</button>
                                    <button type="button" class="btn btn-sm btn-danger" disabled>Hapus</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-3">
                                    Tidak ada data employee ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>