<x-app-layout>
    <div class="app-shell">
        <main class="main-content">
            <header class="topbar">
                <div>
                    <span class="eyebrow">Reset Password</span>
                </div>
            </header>

            <section class="history-panel edit-panel" aria-labelledby="form-title" style="max-width: 700px;">
                <div class="section-heading">
                        <span class="eyebrow">RESET PASSWORD</span>
                        <h2 id="form-title">User: {{ $user->employ_name }}</h2>
                    </div>
                </div>

                <form method="POST" action="{{route('user.reset.password', $user->employ_id) }}" class="charge-form">
                    @csrf
                    @method('PUT')

                    <!-- Password -->
                    <div class="field-group" style="margin-top: 1rem;">
                        <label for="password">Password Lama<span>*</span></label>
                        <input id="password" name="password" type="password" placeholder="">
                        <x-input-error :messages="$errors->get('password')" class="mt-2" style="color: #dc2626; font-size: 0.875rem;" />
                    </div>  
                    
                    <!-- New Password -->
                    <div class="field-group" style="margin-top: 1rem;">
                        <label for="newpassword">Password Baru <span>*</span></label>
                        <input id="newpassword" name="newpassword" type="password" placeholder="">
                        <x-input-error :messages="$errors->get('newpassword')" class="mt-2" style="color: #dc2626; font-size: 0.875rem;" />
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
