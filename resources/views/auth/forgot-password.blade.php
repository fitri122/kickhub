<x-guest-layout>
    <div class="mb-6">
        <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-blue-800">{{ __('Account recovery') }}</p>
        <h1 class="mt-2 text-3xl font-black tracking-tight text-[#111b48]">{{ __('Reset your password') }}</h1>
        <p class="mt-2 text-sm leading-6 text-gray-600">
            {{ __('Enter your email and we will send you a secure link to choose a new password.') }}
        </p>
    </div>

    <x-auth-session-status class="mb-5 rounded-xl border border-emerald-100 bg-emerald-50 p-3 text-sm text-emerald-800" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email address')" class="text-sm font-bold text-[#111b48]" />
            <x-text-input id="email" class="mt-2 block w-full rounded-xl bg-gray-50 px-4 py-3 text-sm shadow-sm focus:border-blue-800 focus:ring-blue-800" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="you@example.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <x-primary-button class="min-h-12 w-full justify-center rounded-xl bg-[#111b48] px-5 py-3 text-sm font-extrabold normal-case tracking-normal shadow-lg shadow-blue-950/15 transition hover:bg-blue-900 focus:bg-blue-900 focus:ring-blue-700 active:bg-[#0b1236]">
            {{ __('Send reset link') }}
        </x-primary-button>
    </form>

    <div class="mt-6 border-t border-gray-100 pt-5 text-center">
        <a href="{{ route('login') }}" class="inline-flex min-h-10 items-center gap-2 rounded-md text-sm font-bold text-blue-800 transition hover:text-blue-950 focus:outline-none focus:ring-2 focus:ring-blue-700 focus:ring-offset-2">
            <span aria-hidden="true">&larr;</span>
            {{ __('Back to sign in') }}
        </a>
    </div>
</x-guest-layout>
