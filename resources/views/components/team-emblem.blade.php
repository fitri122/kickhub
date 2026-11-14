@props(['team'])

@php
    $words = array_filter(
        preg_split('/\s+/', trim($team->nama_pasukan)) ?: [],
        static fn (string $word): bool => strtoupper($word) !== 'KV',
    );
    $words = array_values($words);
    $initials = count($words) === 1
        ? strtoupper(substr($words[0], 0, 2))
        : collect($words)
            ->take(2)
            ->map(static fn (string $word): string => strtoupper(substr($word, 0, 1)))
            ->implode('');
    $logoUrl = str_starts_with($team->logo ?? '', 'http') || str_starts_with($team->logo ?? '', 'images/')
        ? asset($team->logo)
        : asset('storage/'.$team->logo);
@endphp

<div class="grid h-20 w-20 shrink-0 place-items-center overflow-hidden rounded-full border border-gray-200 bg-white p-2 shadow-sm sm:h-24 sm:w-24">
    @if ($team->logo)
        <img
            src="{{ $logoUrl }}"
            alt="{{ __(':team logo', ['team' => $team->nama_pasukan]) }}"
            class="h-full w-full object-contain"
            loading="lazy"
        >
    @else
        <span
            role="img"
            aria-label="{{ __(':team initials', ['team' => $team->nama_pasukan]) }}"
            class="grid h-full w-full place-items-center rounded-full bg-blue-50 text-lg font-extrabold tracking-wide text-[#111b48]"
        >
            {{ $initials }}
        </span>
    @endif
</div>
