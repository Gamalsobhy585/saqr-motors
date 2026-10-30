
@props(['motorcycle'])

@php
    $promotion = $motorcycle->activePromotion;

    $image = \App\Services\PublicCatalogService::imageUrl($motorcycle);

    $originalPrice = $motorcycle->starting_price !== null
        ? (float) $motorcycle->starting_price
        : null;

    $finalPrice = \App\Services\PublicCatalogService::discountedPrice($motorcycle);

    $discount = (float) ($promotion?->discount_percent ?? 0);

    $hasDiscount = $promotion !== null
        && $originalPrice !== null
        && $finalPrice !== null
        && $discount > 0
        && $finalPrice < $originalPrice;

    $motorcycleUrl = route('motorcycles.show', [
        'brand' => $motorcycle->brand->slug,
        'motorcycle' => $motorcycle->slug,
    ]);
@endphp

<article
    class="group flex h-full flex-col overflow-hidden rounded-2xl
           border border-white/10 bg-[#191b21]
           transition duration-300
           hover:-translate-y-1 hover:border-red-500/50
           hover:shadow-[0_18px_50px_rgba(220,38,38,.08)]"
>
    <a
        href="{{ $motorcycleUrl }}"
        class="flex h-full flex-col"
        aria-label="{{ $motorcycle->brand->name }} {{ $motorcycle->name }}"
    >
        {{-- Motorcycle Image --}}
        <div
            class="relative flex aspect-[4/3] items-center
                   justify-center overflow-hidden
                   bg-gradient-to-br from-[#252932] to-[#101116]"
        >
            @if($image)
                <img
                    src="{{ $image }}"
                    alt="{{ $motorcycle->brand->name }} {{ $motorcycle->name }}"
                    loading="lazy"
                    decoding="async"
                    class="h-full w-full object-contain p-3
                           transition duration-500 group-hover:scale-105
                           sm:p-4"
                >
            @else
                <span
                    class="text-center text-2xl font-black italic
                           tracking-wider text-zinc-600"
                >
                    SAQR MOTORS
                </span>
            @endif

            {{-- Active Promotion Badge --}}
            @if($hasDiscount)
                <span
                    class="absolute start-4 top-4 rounded-lg
                           border border-red-400/20 bg-red-600
                           px-3 py-1.5 text-xs font-black
                           text-white shadow-lg"
                    dir="ltr"
                >
                    -{{ rtrim(rtrim(number_format($discount, 2), '0'), '.') }}%
                </span>
            @endif
        </div>

        {{-- Motorcycle Information --}}
        <div class="flex flex-1 flex-col p-4 sm:p-5">

            <div
                class="flex items-center justify-between
                       gap-2 text-xs font-bold uppercase
                       tracking-wider text-zinc-400"
            >
                <span>{{ $motorcycle->brand->name }}</span>

                <span>
                    {{ __('saqr_public.categories.' . strtolower($motorcycle->category->name)) }}
                </span>
            </div>

            <h3 class="mt-3 text-xl font-black text-white">
                {{ $motorcycle->name }}
            </h3>

            {{-- Pricing --}}
            <div
                class="mt-auto flex items-end justify-between gap-3
                       border-t border-white/10 pt-5"
                style="margin-top: auto;"
            >
                <div class="min-w-0">

                    <p class="mb-1 text-xs text-zinc-400">
                        {{ __('saqr_public.labels.starting_from') }}
                    </p>

                    @if($originalPrice !== null)

                        @if($hasDiscount)

                            {{-- Original price --}}
                            <p
                                class="text-sm font-medium
                                       text-zinc-400 line-through
                                       decoration-red-400/70"
                                dir="ltr"
                            >
                                {{ number_format($originalPrice) }}
                                {{ $motorcycle->currency_code }}
                            </p>

                            {{-- Discounted price --}}
                            <p
                                class="mt-1 text-xl font-black
                                       tracking-tight text-red-400
                                       sm:text-2xl"
                                dir="ltr"
                            >
                                {{ number_format($finalPrice) }}
                                {{ $motorcycle->currency_code }}
                            </p>

                        @else

                            {{-- Normal price --}}
                            <p
                                class="mt-1 text-lg font-black text-white"
                                dir="ltr"
                            >
                                {{ number_format($originalPrice) }}
                                {{ $motorcycle->currency_code }}
                            </p>

                        @endif

                    @else

                        <p class="mt-1 font-semibold text-white">
                            {{ __('saqr_public.labels.ask_price') }}
                        </p>

                    @endif
                </div>

                {{-- Details button appearance --}}
                <span
                    class="shrink-0 rounded-lg bg-white/10
                           px-3 py-2 text-sm font-semibold text-white
                           transition group-hover:bg-red-600"
                >
                    {{ __('saqr_public.actions.details') }}
                </span>
            </div>

        </div>
    </a>
</article>
