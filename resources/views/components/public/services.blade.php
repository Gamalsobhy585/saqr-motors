@props(['services'])
<section id="services" class="saqr-section">
    <div class="saqr-container">
        <x-public.section-heading :title="__('saqr_public.sections.services')" :url="Route::has('services.index') ? route('services.index') : null" :link-text="__('saqr_public.actions.all_services')" />
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            @foreach($services as $service)
                <article class="overflow-hidden rounded-2xl border border-white/10 bg-[#191b21]">
                    <div class="flex aspect-[5/3] items-center justify-center overflow-hidden bg-[#252932]">
                        @if($service->image_url)<img src="{{ $service->image_url }}" alt="{{ $service->localized_name }}" loading="lazy" class="h-full w-full object-cover">
                        @else <span class="text-lg font-black text-zinc-600">SAQR SERVICE</span> @endif
                    </div>
                    <div class="p-5"><h3 class="text-lg font-bold">{{ $service->localized_name }}</h3><p class="mt-3 line-clamp-3 text-sm leading-6 text-zinc-400">{{ $service->localized_description }}</p></div>
                </article>
            @endforeach
        </div>
    </div>
</section>
