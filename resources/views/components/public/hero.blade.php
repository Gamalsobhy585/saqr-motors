
@php
    $heroPath = 'images/hero/hero-bg.jpg';
    $hasHeroImage = is_file(public_path($heroPath));
@endphp

<section
    class="saqr-hero relative isolate flex min-h-[620px] items-center overflow-hidden bg-[#0c0d11] sm:min-h-[680px] lg:min-h-[760px]"
    aria-labelledby="saqr-hero-title"
>
    {{-- Hero background image --}}
    @if ($hasHeroImage)
        <img
            src="{{ asset($heroPath) }}"
            alt=""
            fetchpriority="high"
            decoding="async"
            class="pointer-events-none absolute inset-0 z-0 h-full w-full object-cover object-center"
        >
    @else
        {{-- Fallback when the image is unavailable --}}
        <div
            aria-hidden="true"
            class="pointer-events-none absolute inset-0 z-0 bg-[radial-gradient(circle_at_75%_40%,#54212d_0%,#1d1c24_40%,#0c0d11_90%)]"
        ></div>
    @endif

    {{-- Direction-aware dark overlay --}}
    <div
        aria-hidden="true"
        class="pointer-events-none absolute inset-0 z-10 bg-[linear-gradient(90deg,rgba(7,8,12,.94)_0%,rgba(7,8,12,.78)_38%,rgba(7,8,12,.35)_75%,rgba(7,8,12,.15)_100%)] rtl:bg-[linear-gradient(270deg,rgba(7,8,12,.94)_0%,rgba(7,8,12,.78)_38%,rgba(7,8,12,.35)_75%,rgba(7,8,12,.15)_100%)]"
    ></div>

    {{-- Bottom gradient for transition into the next section --}}
    <div
        aria-hidden="true"
        class="pointer-events-none absolute inset-0 z-10 bg-[linear-gradient(0deg,rgba(12,13,17,1)_0%,rgba(12,13,17,.35)_20%,transparent_55%)]"
    ></div>

    {{-- Subtle SAQR red accent --}}
    <div
        aria-hidden="true"
        class="pointer-events-none absolute -end-52 -top-36 z-10 size-[32rem] rounded-full border border-red-500/15 shadow-[0_0_130px_70px_rgba(220,38,38,.08)]"
    ></div>

    {{-- Hero content --}}
    <div class="saqr-container relative z-20 w-full py-24 sm:py-28 lg:py-36">
        <div class="max-w-3xl">

            {{-- Brand badge --}}
            <p
                class="mb-7 inline-flex items-center gap-3 rounded-full border border-red-500/40 bg-red-600/10 px-4 py-2 text-xs font-extrabold uppercase tracking-[.22em] text-red-300 backdrop-blur-sm"
            >
                <span
                    class="size-2 shrink-0 rounded-full bg-red-500 shadow-[0_0_12px_rgba(239,68,68,.8)]"
                ></span>

                SAQR MOTORS
            </p>

            {{-- Heading --}}
            <h1
                id="saqr-hero-title"
                class="text-5xl font-black uppercase leading-[1.06] tracking-tight text-white drop-shadow-lg sm:text-7xl lg:text-[5.75rem]"
            >
                {{ __('saqr_public.hero.headline_one') }}

                <span class="block text-red-500">
                    {{ __('saqr_public.hero.headline_two') }}
                </span>
            </h1>

            {{-- Description --}}
<p class="mt-8 max-w-xl text-base font-medium leading-8
          text-[#E8D5B5] drop-shadow-[0_2px_5px_rgba(0,0,0,0.95)]
          sm:text-xl sm:leading-9">
    {{ __('saqr_public.hero.description') }}
</p>

            {{-- Actions --}}
            <div class="mt-10 flex flex-wrap gap-3 sm:gap-4">

                <a
                    href="{{ route('motorcycles.index') }}"
                    class="saqr-button-primary gap-2"
                >
                    {{ __('saqr_public.actions.explore_motorcycles') }}

                    <svg
                        class="size-4 rtl:rotate-180"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        aria-hidden="true"
                    >
                        <path d="M5 12h14" />
                        <path d="m13 6 6 6-6 6" />
                    </svg>
                </a>

                <a
                    href="{{ route('motorcycles.index', ['promotion' => 1]) }}"
                    class="saqr-button-outline backdrop-blur-sm"
                >
                    {{ __('saqr_public.nav.promotions') }}
                </a>

            </div>
        </div>
    </div>

    {{-- Bottom separator --}}
    <div
        aria-hidden="true"
        class="pointer-events-none absolute inset-x-0 bottom-0 z-20 h-px bg-gradient-to-r from-transparent via-red-600/30 to-transparent"
    ></div>
</section>
