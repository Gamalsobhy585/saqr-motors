@php
    // Promotions are the current offers/events maintained in the Promotions module.
    $navPromotions = \App\Models\Promotion::query()
        ->current()
        ->whereHas('brand', fn ($q) => $q->where('is_active', true))
        ->where(function ($query) {
            $query->whereHas('motorcycle', fn ($q) => $q->where('is_published', true))
                ->orWhere(function ($q) {
                    $q->whereNull('motorcycle_id')
                        ->whereHas('brand.motorcycles', fn ($bikes) => $bikes->where('is_published', true));
                });
        })
        ->orderBy('sort_order')
        ->limit(6)
        ->get();
@endphp
<header x-data="{ menuOpen: false, open: null }" class="sticky top-0 z-50 border-b border-white/10 bg-[#0c0d11]/95 shadow-lg shadow-black/10 backdrop-blur-xl">
    <nav aria-label="{{ __('saqr_public.nav.navigation') }}" class="saqr-container flex min-h-20 items-center justify-between gap-3 py-3">
        <a href="{{ route('home') }}" class="flex shrink-0 items-center gap-3" aria-label="SAQR Motors">
            <img src="{{ asset('images/logos/saqr-dark.png') }}" class="h-11 w-auto max-w-24 object-contain" alt="SAQR Motors">
            <span class="hidden text-sm font-black uppercase tracking-[.3em] text-white 2xl:block">SAQR</span>
        </a>
        <div class="hidden items-center gap-4 text-sm font-semibold text-zinc-300 xl:flex 2xl:gap-5">
            <a class="saqr-nav-link" href="{{ route('home') }}">{{ __('saqr_public.nav.home') }}</a>
            <a class="saqr-nav-link" href="{{ route('motorcycles.index') }}">{{ __('saqr_public.nav.motorcycles') }}</a>

            <x-public.nav-dropdown name="prices" :label="__('saqr_public.nav.prices')">
                @foreach([100000, 150000, 200000] as $price)
                    <a href="{{ route('motorcycles.index', ['price_under' => $price]) }}" class="saqr-dropdown-link" dir="auto">{{ __('saqr_public.nav.under_price', ['amount' => number_format($price)]) }}</a>
                @endforeach
            </x-public.nav-dropdown>

            <x-public.nav-dropdown name="promotions" :label="__('saqr_public.nav.promotions')">
                <a href="{{ route('motorcycles.index', ['promotion' => 1]) }}" class="saqr-dropdown-link font-bold text-red-300">{{ __('saqr_public.nav.all_offers') }}</a>
                @if($navPromotions->isNotEmpty())
                    <div class="my-2 border-t border-white/10"></div>
                    <p class="px-3 pb-1 text-[11px] font-bold uppercase tracking-wide text-zinc-500">{{ __('saqr_public.nav.current_events') }}</p>
                    @foreach($navPromotions as $promotion)
                        <a href="{{ route('motorcycles.index', ['promotion_id' => $promotion->id]) }}" class="saqr-dropdown-link">{{ $promotion->localized_title }}</a>
                    @endforeach
                @endif
            </x-public.nav-dropdown>

            <x-public.nav-dropdown name="categories" :label="__('saqr_public.nav.categories')">
                @foreach(\App\Enums\MotorcycleCategory::cases() as $category)
                    <a href="{{ route('motorcycles.index', ['category' => $category->value]) }}" class="saqr-dropdown-link">{{ __('saqr_public.categories.' . strtolower($category->name)) }}</a>
                @endforeach
            </x-public.nav-dropdown>

            <a class="saqr-nav-link" href="{{ route('home') }}#services">{{ __('saqr_public.nav.services') }}</a>
            <a class="saqr-nav-link" href="{{ route('home') }}#locations">{{ __('saqr_public.nav.locations') }}</a>
            <a class="saqr-nav-link" href="{{ route('home') }}#financial-plans">{{ __('saqr_public.nav.financing') }}</a>
        </div>
        <div class="flex shrink-0 items-center gap-2">
            <form action="{{ route('locale.switch') }}" method="POST">
                @csrf
                <input type="hidden" name="locale" value="{{ app()->isLocale('ar') ? 'en' : 'ar' }}">
                <button type="submit" class="rounded-xl border border-white/20 px-3.5 py-2 text-xs font-bold text-white transition hover:border-red-500 hover:bg-white/5 focus-visible:outline-2 focus-visible:outline-red-500" aria-label="{{ app()->isLocale('ar') ? 'Switch to English' : 'التبديل إلى العربية' }}">
                    {{ app()->isLocale('ar') ? 'English' : 'العربية' }}
                </button>
            </form>
            <button type="button" x-on:click="menuOpen=!menuOpen" :aria-expanded="menuOpen" aria-controls="saqr-mobile-menu" aria-label="{{ __('saqr_public.nav.menu') }}" class="rounded-xl border border-white/20 p-2 text-white xl:hidden">
                <svg x-show="!menuOpen" viewBox="0 0 24 24" class="size-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16" /></svg>
                <svg x-cloak x-show="menuOpen" viewBox="0 0 24 24" class="size-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18" /></svg>
            </button>
        </div>
    </nav>
    <div id="saqr-mobile-menu" x-cloak x-show="menuOpen" x-transition class="max-h-[75vh] overflow-y-auto border-t border-white/10 bg-[#14161b] px-5 py-5 xl:hidden">
        <div class="mx-auto grid max-w-7xl gap-1 text-sm">
            <a class="saqr-mobile-link" href="{{ route('home') }}">{{ __('saqr_public.nav.home') }}</a>
            <a class="saqr-mobile-link" href="{{ route('motorcycles.index') }}">{{ __('saqr_public.nav.motorcycles') }}</a>
            <details class="saqr-mobile-details">
                <summary class="saqr-mobile-link">{{ __('saqr_public.nav.prices') }} <span aria-hidden="true">⌄</span></summary>
                <div class="grid gap-1 ps-4">
                    @foreach([100000, 150000, 200000] as $price)
                        <a class="saqr-mobile-link" href="{{ route('motorcycles.index', ['price_under' => $price]) }}">{{ __('saqr_public.nav.under_price', ['amount' => number_format($price)]) }}</a>
                    @endforeach
                </div>
            </details>
            <details class="saqr-mobile-details">
                <summary class="saqr-mobile-link">{{ __('saqr_public.nav.promotions') }} <span aria-hidden="true">⌄</span></summary>
                <div class="grid gap-1 ps-4">
                    <a class="saqr-mobile-link" href="{{ route('motorcycles.index', ['promotion' => 1]) }}">{{ __('saqr_public.nav.all_offers') }}</a>
                    @foreach($navPromotions as $promotion)
                        <a class="saqr-mobile-link" href="{{ route('motorcycles.index', ['promotion_id' => $promotion->id]) }}">{{ $promotion->localized_title }}</a>
                    @endforeach
                </div>
            </details>
            <details class="saqr-mobile-details">
                <summary class="saqr-mobile-link">{{ __('saqr_public.nav.categories') }} <span aria-hidden="true">⌄</span></summary>
                <div class="grid gap-1 ps-4">
                    @foreach(\App\Enums\MotorcycleCategory::cases() as $category)
                        <a class="saqr-mobile-link" href="{{ route('motorcycles.index', ['category' => $category->value]) }}">{{ __('saqr_public.categories.' . strtolower($category->name)) }}</a>
                    @endforeach
                </div>
            </details>
            <a class="saqr-mobile-link" href="{{ route('home') }}#services">{{ __('saqr_public.nav.services') }}</a>
            <a class="saqr-mobile-link" href="{{ route('home') }}#locations">{{ __('saqr_public.nav.locations') }}</a>
            <a class="saqr-mobile-link" href="{{ route('home') }}#financial-plans">{{ __('saqr_public.nav.financing') }}</a>
        </div>
    </div>
</header>
