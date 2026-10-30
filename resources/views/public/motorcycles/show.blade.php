@extends('layouts.public')
@section('title', $motorcycle->brand->name . ' ' . $motorcycle->name . ' | SAQR Motors')
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags($motorcycle->description ?: $motorcycle->name), 155))
@section('content')
@php
    $gallery = $motorcycle->images->map(fn ($image) => [
        'url' => str_starts_with($image->path, 'images/')
            ? asset($image->path)
            : \Illuminate\Support\Facades\Storage::disk('public')->url($image->path),
        'alt' => $image->alt_text ?: $motorcycle->name,
    ])->values();
    $firstImage = $gallery->first()['url'] ?? \App\Services\PublicCatalogService::imageUrl($motorcycle);
    $promotion = $motorcycle->activePromotion;
    $newPrice = \App\Services\PublicCatalogService::discountedPrice($motorcycle);
    $canBook = $motorcycle->availability_status === \App\Enums\MotorcycleAvailability::Available && $locations->isNotEmpty();
    $spec = $motorcycle->specification;
    $specFields = [
        'engine_cc' => __('saqr_public.specs.engine_cc'),
        'engine_type' => __('saqr_public.specs.engine_type'),
        'transmission' => __('saqr_public.specs.transmission'),
        'power_hp' => __('saqr_public.specs.power_hp'),
        'torque_nm' => __('saqr_public.specs.torque_nm'),
        'weight_kg' => __('saqr_public.specs.weight_kg'),
        'fuel_capacity_liters' => __('saqr_public.specs.fuel_capacity'),
        'seat_height_mm' => __('saqr_public.specs.seat_height'),
    ];
@endphp
<section x-data="{ activeImage: @js($firstImage), modal: @js(old('form_type') === 'ride' ? 'ride' : (old('form_type') === 'contact' ? 'contact' : null)) }"
         x-effect="document.body.style.overflow = modal ? 'hidden' : ''"
         x-on:keydown.escape.window="modal=null"
         class="saqr-container py-10 sm:py-14">
    <nav aria-label="Breadcrumb" class="mb-8 flex flex-wrap items-center gap-3 text-sm text-zinc-400">
        <a class="hover:text-red-400" href="{{ route('home') }}">{{ __('saqr_public.nav.home') }}</a><span>/</span>
        <a class="hover:text-red-400" href="{{ route('motorcycles.index', ['brand' => $motorcycle->brand->slug]) }}">{{ $motorcycle->brand->name }}</a><span>/</span>
        <span class="text-white">{{ $motorcycle->name }}</span>
    </nav>

    @if(session('success'))
        <div role="status" class="mb-8 rounded-xl border border-emerald-500/40 bg-emerald-500/10 px-5 py-4 text-sm text-emerald-200">{{ session('success') }}</div>
    @endif

    <div class="grid gap-10 lg:grid-cols-[minmax(0,1.3fr)_minmax(0,1fr)]">
        <div>
            <div class="flex aspect-[5/4] items-center justify-center overflow-hidden rounded-3xl border border-white/10 bg-[radial-gradient(ellipse_at_center,#2a2e39_0%,#15161c_70%)]">
                @if($firstImage)
                    <img x-bind:src="activeImage" src="{{ $firstImage }}" alt="{{ $motorcycle->name }}" class="h-full w-full object-contain p-6" />
                @else
                    <span class="text-4xl font-black italic text-zinc-600">SAQR MOTORS</span>
                @endif
            </div>
            @if($gallery->count() > 1)
                <div class="mt-4 grid grid-cols-3 gap-3">
                    @foreach($gallery->take(3) as $photo)
                        <button type="button" x-on:click="activeImage=@js($photo['url'])" :class="activeImage===@js($photo['url']) ? 'border-red-500' : 'border-white/10'" class="aspect-[5/3] overflow-hidden rounded-xl border-2 bg-[#20232c] p-2 transition hover:border-red-500">
                            <img src="{{ $photo['url'] }}" alt="{{ $photo['alt'] }}" loading="lazy" class="h-full w-full object-contain">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>
        <div class="lg:py-3">
            <span class="text-xs font-black uppercase tracking-[.25em] text-red-400">{{ $motorcycle->brand->name }}</span>
            <h1 class="mt-4 text-4xl font-black leading-tight sm:text-5xl">{{ $motorcycle->name }}</h1>
            <div class="mt-5 flex flex-wrap gap-2">
                <span class="rounded-full border border-white/15 px-3 py-1 text-xs text-zinc-300">{{ __('saqr_public.categories.' . strtolower($motorcycle->category->name)) }}</span>
                @if($motorcycle->model_year)<span class="rounded-full border border-white/15 px-3 py-1 text-xs text-zinc-300">{{ $motorcycle->model_year }}</span>@endif
                <span class="rounded-full border border-white/15 px-3 py-1 text-xs text-zinc-300">{{ __('saqr_public.availability.' . strtolower($motorcycle->availability_status->name)) }}</span>
            </div>
            @if($promotion)
                <div class="mt-8 rounded-xl border border-red-500/30 bg-red-600/10 px-5 py-4">
                    <p class="text-xs font-semibold text-red-400">{{ __('saqr_public.labels.promotion') }} · -{{ (float)$promotion->discount_percent }}%</p>
                    <p class="mt-1 text-lg font-bold">{{ $promotion->localized_title }}</p>
                </div>
            @endif
            <div class="mt-8 border-b border-white/10 pb-7">
                <div class="text-sm text-zinc-400">{{ __('saqr_public.labels.starting_from') }}</div>
                @if($motorcycle->starting_price !== null)
                    @if($promotion)
                        <p class="mt-2 text-sm text-zinc-500 line-through">{{ number_format((float)$motorcycle->starting_price) }} {{ $motorcycle->currency_code }}</p>
                    @endif
                    <p class="mt-1 text-4xl font-black text-white" dir="ltr">{{ number_format($newPrice ?? (float)$motorcycle->starting_price) }} <span class="text-base text-zinc-400">{{ $motorcycle->currency_code }}</span></p>
                @else
                    <p class="mt-2 text-2xl font-bold text-white">{{ __('saqr_public.labels.ask_price') }}</p>
                @endif
            </div>
            <p class="mt-7 whitespace-pre-line leading-8 text-zinc-300">{{ $motorcycle->description }}</p>
            <div class="mt-9 grid gap-3 sm:grid-cols-2">
                <button type="button" x-on:click="modal='ride'" @disabled(! $canBook) class="saqr-button-primary disabled:cursor-not-allowed disabled:opacity-40">{{ __('saqr_public.actions.book_ride') }}</button>
                <button type="button" x-on:click="modal='contact'" class="saqr-button-outline">{{ __('saqr_public.actions.contact_us') }}</button>
            </div>
            @unless($canBook)
                <p class="mt-3 text-sm text-zinc-400">{{ __('saqr_public.labels.booking_unavailable') }}</p>
            @endunless
        </div>
    </div>

    @if($spec)
        <div class="mt-16 rounded-2xl border border-white/10 bg-[#191b21] p-6 sm:p-9">
            <h2 class="mb-6 text-2xl font-black">{{ __('saqr_public.sections.specifications') }}</h2>
            <div class="grid gap-x-10 sm:grid-cols-2">
                @foreach($specFields as $attribute => $label)
                    @if($spec->{$attribute} !== null && $spec->{$attribute} !== '')
                        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-white/10 py-4 text-sm"><span class="text-zinc-400">{{ $label }}</span><span class="font-semibold text-white">{{ $spec->{$attribute} }}</span></div>
                    @endif
                @endforeach
                @foreach((is_array($spec->additional_specs) ? $spec->additional_specs : []) as $label => $value)
                    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-white/10 py-4 text-sm"><span class="text-zinc-400">{{ $label }}</span><span class="font-semibold">{{ $value }}</span></div>
                @endforeach
            </div>
        </div>
    @endif
    <x-public.ride-modal :motorcycle="$motorcycle" :locations="$locations" :ride-slots="$rideSlots" />
    <x-public.contact-modal :motorcycle="$motorcycle" />
</section>
@endsection
