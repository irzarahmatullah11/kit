<x-guest-layout>
    <!-- Session Status disamakan dengan gaya alert Source 2 -->
    @if (session('status'))
        <div class="alert alert-success" role="status" style="margin-bottom: 1.5rem;">
            <span class="alert-icon">&#10003;</span>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    <!-- Heading disamakan dengan gaya modal form -->
    <div class="section-heading" style="margin-bottom: 1.5rem; justify-content: center; text-align: center;">
        <div>
            <h2 id="form-title" style="font-size: 1.5rem;">Log In</h2>
        </div>
    </div>

    <!-- Form disesuaikan dengan class .charge-form -->
    <form method="POST" action="{{ route('login') }}" class="charge-form">
        @csrf

        <!-- Email Address -->
        <div class="field-group">
            <label for="email">Email <span>*</span></label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="Contoh: nama@email.com" required autofocus autocomplete="username">
            <x-input-error :messages="$errors->get('email')" class="mt-2" style="color: #dc2626; font-size: 0.875rem;" />
        </div>

        <!-- Password -->
        <div class="field-group" style="margin-top: 1rem;">
            <label for="password">Password <span>*</span></label>
            <input id="password" name="password" type="password" placeholder="Masukkan password Anda" required autocomplete="current-password">
            <x-input-error :messages="$errors->get('password')" class="mt-2" style="color: #dc2626; font-size: 0.875rem;" />
        </div>

        <!-- Actions -->
        <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 2rem;">

            <!-- Button disamakan dengan class .submit-button dari Source 2 -->
            <button class="submit-button" type="submit" style="margin-top: 0; width: auto;">
                <span>Log in</span>
                <span aria-hidden="true">&#8594;</span>
            </button>
        </div>
    </form>
</x-guest-layout>