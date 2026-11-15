<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <section class="relative isolate overflow-hidden rounded-3xl bg-gradient-to-r from-[#0b1236] via-[#172b67] to-[#3157a4] px-6 py-6 text-white shadow-xl sm:px-8">
                <div class="pointer-events-none absolute -right-10 -top-20 -z-10 h-56 w-56 rounded-full bg-cyan-300/20 blur-3xl" aria-hidden="true"></div>
                <p class="text-xs font-extrabold uppercase tracking-[0.18em] text-amber-300">{{ __('KICKHUB · ACCOUNT') }}</p>
                <h1 class="mt-2 text-2xl font-black sm:text-3xl">{{ __('Profile settings') }}</h1>
                <p class="mt-2 text-sm text-blue-100">{{ __('Manage your account details, security and account access.') }}</p>
            </section>

            <div class="rounded-3xl border border-white bg-white/95 p-4 shadow-lg shadow-blue-950/5 sm:p-8">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="rounded-3xl border border-white bg-white/95 p-4 shadow-lg shadow-blue-950/5 sm:p-8">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="rounded-3xl border border-red-100 bg-white/95 p-4 shadow-lg shadow-blue-950/5 sm:p-8">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
