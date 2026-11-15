<x-guest-layout>
    <div class="mb-6">
        <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-blue-800">{{ __('Account security') }}</p>
        <h1 class="mt-2 text-3xl font-black tracking-tight text-[#111b48]">{{ __('Verify your email') }}</h1>
        <p class="mt-2 text-sm leading-6 text-gray-600">
            {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 rounded-xl border border-emerald-100 bg-emerald-50 p-3 text-sm font-semibold text-emerald-800">
            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
        </div>
    @endif

    <div class="mt-5 flex flex-wrap items-center justify-between gap-3">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <x-primary-button>
                    {{ __('Resend Verification Email') }}
                </x-primary-button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="rounded-md text-sm font-semibold text-blue-800 underline-offset-4 transition hover:text-blue-950 hover:underline focus:outline-none focus:ring-2 focus:ring-blue-700 focus:ring-offset-2">
                {{ __('Log Out') }}
            </button>
        </form>
    </div>
</x-guest-layout>
