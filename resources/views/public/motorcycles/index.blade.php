@extends('layouts.public')
@section('title', __('saqr_public.sections.catalog') . ' | SAQR Motors')
@section('content')
<section class="border-b border-white/10 bg-gradient-to-br from-[#221318] via-[#15171d] to-[#0c0d11] py-16">
    <div class="saqr-container">
        <p class="text-xs font-bold uppercase tracking-[.3em] text-red-400">SAQR MOTORS</p>
        <h1 class="mt-4 text-4xl font-black sm:text-5xl">{{ __('saqr_public.sections.catalog') }}</h1>
        <p class="mt-4 max-w-xl text-zinc-400">{{ __('saqr_public.sections.catalog_subtitle') }}</p>
    </div>
</section>
<div class="saqr-container grid gap-8 py-12 lg:grid-cols-[290px_1fr]">
    <aside class="self-start rounded-2xl border border-white/10 bg-[#191b21] p-6 lg:sticky lg:top-28">
        <div class="flex items-center justify-between gap-3"><h2 class="text-lg font-black">{{ __('saqr_public.labels.filters') }}</h2><a href="{{ route('motorcycles.index') }}" class="text-xs font-semibold text-red-400 hover:underline">{{ __('saqr_public.actions.reset') }}</a></div>
        <form method="GET" action="{{ route('motorcycles.index') }}" class="mt-6 grid gap-5">
            <label class="grid gap-2 text-sm font-medium">{{ __('saqr_public.labels.brand') }}
                <select name="brand" class="saqr-input"><option value="">{{ __('saqr_public.labels.all_brands') }}</option>
                    @foreach($brands as $brand)
                        <option value="{{ $brand->slug }}" @selected(($filters['brand'] ?? '') === $brand->slug)>{{ $brand->name }}</option>
                    @endforeach
                </select>
            </label>
            <label class="grid gap-2 text-sm font-medium">{{ __('saqr_public.labels.category') }}
                <select name="category" class="saqr-input"><option value="">{{ __('saqr_public.labels.all_categories') }}</option>
                    @foreach(\App\Enums\MotorcycleCategory::cases() as $category)
                        <option value="{{ $category->value }}" @selected((string)($filters['category'] ?? '') === (string)$category->value)>{{ __('saqr_public.categories.' . strtolower($category->name)) }}</option>
                    @endforeach
                </select>
            </label>
            <label class="grid gap-2 text-sm font-medium">{{ __('saqr_public.labels.price_presets') }}
                <select name="price_under" class="saqr-input">
                    <option value="">{{ __('saqr_public.labels.any_price') }}</option>
                    @foreach([100000, 150000, 200000] as $threshold)
                        <option value="{{ $threshold }}" @selected((string)($filters['price_under'] ?? '') === (string)$threshold)>
                            {{ __('saqr_public.nav.under_price', ['amount' => number_format($threshold)]) }}
                        </option>
                    @endforeach
                </select>
            </label>
            <div class="grid grid-cols-2 gap-3">
                <label class="grid gap-2 text-sm font-medium">{{ __('saqr_public.labels.price_min') }}<input class="saqr-input min-w-0" type="number" inputmode="decimal" min="0" step="0.01" name="min_price" value="{{ $filters['min_price'] ?? '' }}" placeholder="0"></label>
                <label class="grid gap-2 text-sm font-medium">{{ __('saqr_public.labels.price_max') }}<input class="saqr-input min-w-0" type="number" inputmode="decimal" min="0" step="0.01" name="max_price" value="{{ $filters['max_price'] ?? '' }}" placeholder="EGP"></label>
            </div>
            <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-white/10 px-3 py-3 text-sm"><input type="checkbox" name="promotion" value="1" @checked((string)($filters['promotion'] ?? '') === '1') class="accent-red-600"><span>{{ __('saqr_public.labels.promotions_only') }}</span></label>
            <label class="grid gap-2 text-sm font-medium">{{ __('saqr_public.labels.promotion_event') }}
                <select name="promotion_id" class="saqr-input">
                    <option value="">{{ __('saqr_public.nav.all_offers') }}</option>
                    @foreach($promotions as $promotion)
                        <option value="{{ $promotion->id }}" @selected((string)($filters['promotion_id'] ?? '') === (string)$promotion->id)>{{ $promotion->localized_title }}</option>
                    @endforeach
                </select>
            </label>

            <label class="grid gap-2 text-sm font-medium">{{ __('saqr_public.labels.sort') }}
                <select name="sort" class="saqr-input">
                    @foreach(['latest', 'price_asc', 'price_desc'] as $sort)
                        <option value="{{ $sort }}" @selected(($filters['sort'] ?? 'latest') === $sort)>{{ __('saqr_public.sort.' . $sort) }}</option>
                    @endforeach
                </select>
            </label>
            <button type="submit" class="saqr-button-primary w-full">{{ __('saqr_public.actions.apply_filters') }}</button>
        </form>
    </aside>
    <div>
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3"><h2 class="text-xl font-bold">{{ __('saqr_public.labels.results') }}</h2><p class="text-sm text-zinc-400">{{ $motorcycles->total() }} {{ __('saqr_public.labels.motorcycles_count') }}</p></div>
        <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
            @forelse($motorcycles as $motorcycle)
                <x-public.motorcycle-card :motorcycle="$motorcycle" />
            @empty
                <div class="col-span-full rounded-xl border border-white/10 p-12 text-center text-zinc-400">{{ __('saqr_public.labels.no_results') }}</div>
            @endforelse
        </div>
        <div class="mt-8">{{ $motorcycles->links() }}</div>
    </div>
</div>
@endsection
