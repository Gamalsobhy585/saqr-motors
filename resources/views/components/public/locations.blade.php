@props(['locations'])
<section id="locations" class="saqr-section bg-[#14161c]">
    <div class="saqr-container">
        <x-public.section-heading :title="__('saqr_public.sections.locations')" :subtitle="__('saqr_public.sections.locations_subtitle')" />
        <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
            @forelse($locations as $location)
                <article class="rounded-2xl border border-white/10 bg-[#1f2129] p-6">
                    <div class="mb-4 inline-flex h-11 w-11 items-center justify-center rounded-xl bg-red-600/15 text-red-400" aria-hidden="true">⌖</div>
                    <h3 class="text-lg font-bold">{{ $location->display_name }}</h3>
                    <p class="mt-2 text-sm text-zinc-400">{{ $location->cityRelation?->display_name }}</p>
                    <p class="mt-3 text-sm leading-6 text-zinc-400">{{ $location->display_address }}</p>
                    @if($location->google_maps_url)
                        <a href="{{ $location->google_maps_url }}" target="_blank" rel="noopener noreferrer" class="mt-5 inline-block text-sm font-bold text-red-400 hover:underline">{{ __('saqr_public.actions.open_map') }} ↗</a>
                    @endif
                </article>
            @empty
                <p class="text-zinc-400">{{ __('saqr_public.labels.no_locations') }}</p>
            @endforelse
        </div>
    </div>
</section>
