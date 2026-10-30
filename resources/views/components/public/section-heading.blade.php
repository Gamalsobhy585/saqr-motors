@props(['eyebrow' => 'SAQR MOTORS', 'title', 'subtitle' => null, 'url' => null, 'linkText' => null])
<div class="mb-8 flex flex-wrap items-end justify-between gap-4">
    <div class="max-w-2xl">
        <p class="mb-3 text-xs font-black uppercase tracking-[.28em] text-red-500">{{ $eyebrow }}</p>
        <h2 class="text-3xl font-black tracking-tight text-white sm:text-4xl">{{ $title }}</h2>
        @if($subtitle)<p class="mt-3 leading-7 text-zinc-400">{{ $subtitle }}</p>@endif
    </div>
    @if($url)<a href="{{ $url }}" class="inline-flex items-center rounded-lg border border-white/20 px-5 py-2.5 text-sm font-semibold transition hover:border-red-500 hover:text-white">{{ $linkText ?? __('saqr_public.actions.discover_more') }}</a>@endif
</div>
