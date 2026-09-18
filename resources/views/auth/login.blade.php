<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button id="login-submit" class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>

    <section class="mt-6 rounded-xl border border-slate-200 bg-slate-50 p-4 shadow-sm" aria-labelledby="demo-accounts-title">
        <div class="mb-3">
            <h2 id="demo-accounts-title" class="text-sm font-semibold text-slate-800">Demo Accounts</h2>
            <p class="mt-1 text-xs text-slate-500">ជ្រើសរើសគណនីសាកល្បង ដើម្បីបំពេញព័ត៌មានចូលប្រើដោយស្វ័យប្រវត្តិ។</p>
        </div>

        <div class="grid gap-2 sm:grid-cols-2">
            <button type="button"
                onclick="fillDemoAccount('owner@demo.com', 'password123')"
                class="rounded-lg border border-indigo-200 bg-white px-3 py-3 text-left text-sm font-medium text-indigo-700 transition hover:border-indigo-300 hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                👤 ចូលជាម្ចាស់ហាង <span class="block mt-1 text-xs font-normal text-indigo-500">Shop Owner</span>
            </button>

            <button type="button"
                onclick="fillDemoAccount('cashier@demo.com', 'password123')"
                class="rounded-lg border border-emerald-200 bg-white px-3 py-3 text-left text-sm font-medium text-emerald-700 transition hover:border-emerald-300 hover:bg-emerald-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                💳 ចូលជាអ្នកគិតលុយ <span class="block mt-1 text-xs font-normal text-emerald-600">Cashier</span>
            </button>
        </div>
    </section>

    <script>
        function fillDemoAccount(email, password) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = password;
            document.getElementById('login-submit')?.focus();
        }
    </script>
</x-guest-layout>
