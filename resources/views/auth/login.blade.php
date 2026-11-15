<x-guest-layout>
    <div class="mb-6">
        <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-blue-800">{{ __('Account access') }}</p>
        <h1 class="mt-2 text-3xl font-black tracking-tight text-[#111b48]">{{ __('Welcome back') }}</h1>
        <p class="mt-2 text-sm leading-6 text-gray-600">{{ __('Sign in to continue to your KickHub account.') }}</p>
    </div>

    <x-auth-session-status class="mb-5 rounded-xl border border-emerald-100 bg-emerald-50 p-3 text-sm text-emerald-800" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email address')" class="text-sm font-bold text-[#111b48]" />
            <x-text-input id="email" class="mt-2 block w-full rounded-xl bg-gray-50 px-4 py-3 text-sm shadow-sm focus:border-blue-800 focus:ring-blue-800" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="you@example.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" :value="__('Password')" class="text-sm font-bold text-[#111b48]" />
            <x-text-input id="password" class="mt-2 block w-full rounded-xl bg-gray-50 px-4 py-3 text-sm shadow-sm focus:border-blue-800 focus:ring-blue-800" type="password" name="password" required autocomplete="current-password" placeholder="{{ __('Enter your password') }}" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex flex-wrap items-center justify-between gap-3">
            <label for="remember_me" class="inline-flex min-h-10 items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-[#111b48] shadow-sm focus:ring-blue-800" name="remember">
                <span class="ms-2 text-sm font-medium text-gray-600">{{ __('Remember me') }}</span>
            </label>
            @if (Route::has('password.request'))
                <a class="rounded-md text-sm font-bold text-blue-800 underline-offset-4 transition hover:text-blue-950 hover:underline focus:outline-none focus:ring-2 focus:ring-blue-700 focus:ring-offset-2" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif
        </div>

        <x-primary-button class="min-h-12 w-full justify-center rounded-xl bg-[#111b48] px-5 py-3 text-sm font-extrabold normal-case tracking-normal shadow-lg shadow-blue-950/15 transition hover:bg-blue-900 focus:bg-blue-900 focus:ring-blue-700 active:bg-[#0b1236]">
            {{ __('Sign in to KickHub') }}
        </x-primary-button>
    </form>

</x-guest-layout>
