@props(['brands'])
<section id="brands" class="saqr-section">
    <div class="saqr-container">
        <x-public.section-heading :title="__('saqr_public.sections.brands')" :subtitle="__('saqr_public.sections.brands_subtitle')" />
        <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-5">
            @foreach($brands as $brand)
                <a href="{{ route('motorcycles.index', ['brand' => $brand->slug]) }}" class="group rounded-2xl border border-white/10 bg-[#191b21] p-5 text-center transition hover:-translate-y-1 hover:border-red-500/60">
                    <div class="mx-auto flex h-24 items-center justify-center">
                        @if($brand->logo_url)
                            <img src="{{ $brand->logo_url }}" alt="{{ $brand->name }}" loading="lazy" class="max-h-20 max-w-full object-contain transition group-hover:scale-105">
                        @else
                            <span class="text-xl font-black text-zinc-500">{{ $brand->name }}</span>
                        @endif
                    </div>
                    <h3 class="mt-5 font-black text-white">{{ $brand->name }}</h3>
                    <p class="mt-1 text-xs text-zinc-400">{{ $brand->published_motorcycles_count }} {{ __('saqr_public.labels.motorcycles_count') }}</p>
                </a>
            @endforeach
        </div>
    </div>
</section>
