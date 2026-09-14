<x-app-layout>
    <div class="app-shell">
        <main class="main-content">
            <header class="topbar">
                <div>
                    <span class="eyebrow">ADMIN PANEL</span>
                    <h1>Edit Data Employee</h1>
                </div>
            </header>

            <section class="history-panel edit-panel" aria-labelledby="form-title" style="max-width: 700px;">
                <div class="section-heading">
                    <div>
                        <span class="eyebrow">FORM EDIT</span>
                        <h2 id="form-title">Edit Profil: {{ $user->employ_name }}</h2>
                    </div>
                </div>

                <!-- Pastikan route 'account.update' sudah Anda buat di web.php -->
                <form method="POST" action="{{route('account.edit.push', $user->employ_id) }}" class="charge-form">
                    @csrf
                    @method('PUT') <!-- Method overriding untuk proses Update -->

                    <!-- Input Nama -->
                    <div class="field-group">
                        <label for="employ_name">Nama Employee <span>*</span></label>
                        <input id="employ_name" name="employ_name" type="text" value="{{ old('employ_name', $user->employ_name) }}" required>
                    </div>

                    <!-- Dropdown Role -->
                    <div class="field-group">
                        <label for="role">Role <span>*</span></label>
                        <select id="role" name="role" required style="width: 100%; padding: 12px 13px; color: var(--ink); border: 1px solid var(--line); border-radius: 6px; outline: none; background: #fbfcfa; font-size: 13px;">
                            <option value="">-- Pilih Role --</option>
                            @foreach($roles as $roleItem)
                                <!-- Akan mengecek otomatis apakah role user sama dengan role yang di-loop agar statusnya 'selected' -->
                                <option value="{{ $roleItem->role_id }}" {{ old('role', $user->role) == $roleItem->role_id ? 'selected' : '' }}>
                                    {{ $roleItem->role_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Input Email -->
                    <div class="field-group">
                        <label for="email">Email <span>*</span></label>
                        <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required>
                    </div>

                    <!-- Password -->
                    <div class="field-group" style="margin-top: 1rem;">
                        <label for="password">Reset Password <span>*</span></label>
                        <input id="password" name="password" type="password" placeholder="boleh kosong">
                        <x-input-error :messages="$errors->get('password')" class="mt-2" style="color: #dc2626; font-size: 0.875rem;" />
                    </div>                    

                    <!-- Tombol Aksi -->
                    <div class="edit-actions">
                        <!-- Sesuaikan dengan route list account Anda -->
                        <a href="{{ url()->previous() }}" class="submit-button" style="background: var(--muted); text-align: center; text-decoration: none; display: inline-block; width: auto; padding: 13px 20px;">
                            Batal
                        </a>
                        <button class="submit-button" type="submit" style="flex: 1;">
                            <span>Simpan Perubahan</span>
                            <span aria-hidden="true">&#8594;</span>
                        </button>
                    </div>
                </form>
            </section>
        </main>
    </div>
</x-app-layout>