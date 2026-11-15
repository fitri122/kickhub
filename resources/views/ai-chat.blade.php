<x-app-layout>
    <div
        x-data="{
            draft: '',
            messages: [],
            suggestions: [
                'Boleh terangkan peraturan offside dengan ringkas?',
                'Apa berlaku apabila pemain dapat kad merah?',
                'Bagaimana kiraan mata peringkat kumpulan?',
                'Terangkan formasi 4-3-3 dengan mudah.'
            ],
            sendMessage() {
                const question = this.draft.trim();

                if (!question) {
                    return;
                }

                this.messages.push({ role: 'user', text: question });
                this.messages.push({
                    role: 'assistant',
                    text: 'UI chat ini belum disambungkan kepada Gemini. Jawapan AI akan dipaparkan di sini selepas integrasi dibuat.'
                });
                this.draft = '';
            }
        }"
        class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:py-10"
    >
        <section class="relative isolate overflow-hidden rounded-3xl bg-gradient-to-br from-[#111b48] via-[#1c3476] to-[#355ab0] px-6 py-8 text-white shadow-xl sm:px-10 sm:py-10" aria-labelledby="ai-chat-title">
            <div class="pointer-events-none absolute -right-12 -top-24 -z-10 h-72 w-72 rounded-full bg-cyan-300/20 blur-3xl" aria-hidden="true"></div>
            <div class="pointer-events-none absolute -bottom-28 left-1/3 -z-10 h-64 w-64 rounded-full bg-indigo-400/20 blur-3xl" aria-hidden="true"></div>
            <div class="relative flex flex-col gap-8 sm:flex-row sm:items-center sm:justify-between">
                <div class="max-w-2xl">
                    <p class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-3 py-1.5 text-xs font-bold uppercase tracking-[0.16em] text-cyan-100">
                        <span class="h-2 w-2 rounded-full bg-emerald-300" aria-hidden="true"></span>
                        {{ __('Your football companion') }}
                    </p>
                    <h1 id="ai-chat-title" class="mt-4 text-3xl font-black tracking-tight sm:text-4xl lg:text-5xl">
                        {{ __('The beautiful game,') }} <span class="text-amber-300">{{ __('made clearer.') }}</span>
                    </h1>
                    <p class="mt-4 max-w-xl text-base leading-7 text-blue-100 sm:text-lg">
                        {{ __('Got a question about football? Ask about rules, tactics, positions, or how the tournament works — in your own words.') }}
                    </p>
                    <div class="mt-6 flex flex-wrap items-center gap-3">
                        <span class="inline-flex items-center gap-2 rounded-xl border border-white/15 bg-white/10 px-3 py-2 text-sm font-semibold">
                            <svg class="h-5 w-5 text-cyan-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3.75 13.9 9l5.35 1.9L13.9 12.8 12 18.15l-1.9-5.35L4.75 10.9 10.1 9 12 3.75Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="m19 15 .8 2.2L22 18l-2.2.8L19 21l-.8-2.2L16 18l2.2-.8L19 15Z" />
                            </svg>
                            {{ __('Google Gemini AI') }}
                        </span>
                        <span class="text-sm text-blue-100">{{ __('Open to everyone · No sign-in required') }}</span>
                    </div>
                </div>
                <div class="hidden h-36 w-36 shrink-0 items-center justify-center rounded-[2rem] border border-white/15 bg-white/10 shadow-inner backdrop-blur sm:flex lg:mr-8" aria-hidden="true">
                    <svg class="h-20 w-20 text-cyan-200" viewBox="0 0 80 80" fill="none">
                        <path d="M40 8 48 30l22 8-22 8-8 22-8-22-22-8 22-8 8-22Z" stroke="currentColor" stroke-width="3" stroke-linejoin="round" />
                        <path d="m62 51 4 11 11 4-11 4-4 11-4-11-11-4 11-4 4-11Z" fill="currentColor" />
                    </svg>
                </div>
            </div>
        </section>

        <div class="mt-6 grid gap-6 lg:grid-cols-[minmax(0,1fr)_20rem]">
            <section class="flex min-h-[34rem] flex-col overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm" aria-label="{{ __('Chat with KickHub AI') }}">
                <header class="flex items-center justify-between gap-4 border-b border-gray-100 px-5 py-4 sm:px-7">
                    <div class="flex items-center gap-3">
                        <div class="grid h-11 w-11 place-items-center rounded-2xl bg-gradient-to-br from-blue-900 to-indigo-600 text-white shadow-sm">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3.75 13.9 9l5.35 1.9L13.9 12.8 12 18.15l-1.9-5.35L4.75 10.9 10.1 9 12 3.75Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="m19 15 .8 2.2L22 18l-2.2.8L19 21l-.8-2.2L16 18l2.2-.8L19 15Z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-extrabold text-gray-900">{{ __('KickHub AI') }}</h2>
                            <p class="text-xs font-medium text-gray-500">{{ __('Football rules, tactics & more') }}</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center gap-2 rounded-full bg-amber-50 px-3 py-1.5 text-xs font-bold text-amber-800">
                        <span class="h-2 w-2 rounded-full bg-amber-500" aria-hidden="true"></span>
                        {{ __('Preview') }}
                    </span>
                </header>

                <div class="flex flex-1 flex-col justify-end gap-5 p-5 sm:p-7" aria-live="polite">
                    <div class="flex items-start gap-3">
                        <div class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-blue-50 text-blue-800">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3.75 13.9 9l5.35 1.9L13.9 12.8 12 18.15l-1.9-5.35L4.75 10.9 10.1 9 12 3.75Z" />
                            </svg>
                        </div>
                        <div class="max-w-xl rounded-2xl rounded-tl-md bg-gray-50 px-4 py-3 text-sm leading-6 text-gray-700">
                            <p class="font-bold text-gray-900">{{ __('Hi, football fan!') }}</p>
                            <p class="mt-1">{{ __('I can help explain the rules, break down tactics, or answer questions about the tournament. What would you like to know?') }}</p>
                        </div>
                    </div>

                    <template x-for="(message, index) in messages" :key="index">
                        <div class="flex items-start gap-3" :class="message.role === 'user' ? 'flex-row-reverse' : ''">
                            <div
                                class="grid h-9 w-9 shrink-0 place-items-center rounded-xl"
                                :class="message.role === 'user' ? 'bg-[#111b48] text-white' : 'bg-blue-50 text-blue-800'"
                                aria-hidden="true"
                            >
                                <svg x-show="message.role === 'assistant'" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3.75 13.9 9l5.35 1.9L13.9 12.8 12 18.15l-1.9-5.35L4.75 10.9 10.1 9 12 3.75Z" />
                                </svg>
                                <svg x-show="message.role === 'user'" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 7.5a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.5 20.25a7.5 7.5 0 0 1 15 0" />
                                </svg>
                            </div>
                            <p
                                class="max-w-xl whitespace-pre-wrap rounded-2xl px-4 py-3 text-sm leading-6"
                                :class="message.role === 'user' ? 'rounded-tr-md bg-[#111b48] text-white' : 'rounded-tl-md bg-gray-50 text-gray-700'"
                                x-text="message.text"
                            ></p>
                        </div>
                    </template>
                </div>

                <form @submit.prevent="sendMessage()" class="border-t border-gray-100 p-4 sm:p-6">
                    <label for="ai-prompt" class="sr-only">{{ __('Your question') }}</label>
                    <div class="flex items-end gap-3 rounded-2xl border border-gray-200 bg-gray-50 p-2 transition focus-within:border-blue-400 focus-within:bg-white focus-within:ring-4 focus-within:ring-blue-100">
                        <textarea
                            id="ai-prompt"
                            x-model="draft"
                            @keydown.enter.prevent="if (!$event.shiftKey) sendMessage()"
                            rows="1"
                            maxlength="1000"
                            placeholder="{{ __('Ask about football rules, tactics, or the tournament...') }}"
                            class="max-h-32 min-h-11 flex-1 resize-y border-0 bg-transparent px-3 py-3 text-sm text-gray-900 placeholder:text-gray-400 focus:ring-0"
                        ></textarea>
                        <button
                            type="submit"
                            class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#111b48] text-white shadow-sm transition hover:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-700 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-40"
                            :disabled="!draft.trim()"
                            aria-label="{{ __('Send message') }}"
                        >
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m5 12 14-7-4.5 14-2.5-6.5L5 12Z" />
                            </svg>
                        </button>
                    </div>
                    <p class="mt-3 text-center text-xs leading-5 text-gray-500">{{ __('Gemini connection is not active yet. This is a UI preview; no messages are sent to an AI service.') }}</p>
                </form>
            </section>

            <aside class="space-y-5">
                <section class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6" aria-labelledby="suggestions-title">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-widest text-blue-800">{{ __('Need a starting point?') }}</p>
                            <h2 id="suggestions-title" class="mt-1 text-lg font-extrabold text-gray-900">{{ __('Try asking') }}</h2>
                        </div>
                        <span class="grid h-10 w-10 place-items-center rounded-xl bg-amber-50 text-amber-700" aria-hidden="true">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3.75 13.9 9l5.35 1.9L13.9 12.8 12 18.15l-1.9-5.35L4.75 10.9 10.1 9 12 3.75Z" />
                            </svg>
                        </span>
                    </div>
                    <div class="mt-5 grid gap-3">
                        <template x-for="suggestion in suggestions" :key="suggestion">
                            <button
                                type="button"
                                @click="draft = suggestion; $nextTick(() => document.getElementById('ai-prompt').focus())"
                                class="group flex w-full items-start justify-between gap-3 rounded-xl border border-gray-200 bg-white p-3 text-left text-sm font-medium leading-5 text-gray-700 transition hover:border-blue-300 hover:bg-blue-50 hover:text-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-700 focus:ring-offset-2"
                            >
                                <span x-text="suggestion"></span>
                                <span class="shrink-0 text-gray-400 transition group-hover:translate-x-0.5 group-hover:text-blue-700" aria-hidden="true">&rarr;</span>
                            </button>
                        </template>
                    </div>
                </section>

                <section class="rounded-3xl border border-blue-100 bg-blue-50/70 p-5 sm:p-6" aria-labelledby="topics-title">
                    <h2 id="topics-title" class="font-extrabold text-gray-900">{{ __('Your game, your questions.') }}</h2>
                    <p class="mt-2 text-sm leading-6 text-gray-600">{{ __('From first-time fans to seasoned players, ask in Malay or English and explore the game at your own pace.') }}</p>
                    <div class="mt-4 flex flex-wrap gap-2">
                        @foreach (['Rules', 'Tactics', 'Positions', 'Tournament'] as $topic)
                            <span class="rounded-full border border-blue-100 bg-white px-3 py-1.5 text-xs font-bold text-blue-900">{{ __($topic) }}</span>
                        @endforeach
                    </div>
                </section>

                <a href="{{ route('schedule') }}" class="flex items-center justify-between gap-4 rounded-2xl bg-[#111b48] p-5 text-white transition hover:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-700 focus:ring-offset-2">
                    <span>
                        <span class="block text-xs font-bold uppercase tracking-widest text-blue-200">{{ __('Want match details?') }}</span>
                        <span class="mt-1 block font-extrabold">{{ __('Browse the schedule') }}</span>
                    </span>
                    <span class="text-xl" aria-hidden="true">&rarr;</span>
                </a>
            </aside>
        </div>
    </div>
</x-app-layout>
