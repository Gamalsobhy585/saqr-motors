
@extends('layouts.public')

@section('title', 'SAQR Motors | ' . __('saqr_public.nav.home'))

@section('content')

    {{-- Hero Section --}}
    <x-public.hero />

    {{-- Brands Section --}}
    <x-public.brands :brands="$brands" />

    {{-- Featured Motorcycles Section --}}
    @if($motorcycles->isNotEmpty())
        <section class="saqr-section bg-[#111318]">
            <div class="saqr-container">

                <x-public.section-heading
                    :title="__('saqr_public.sections.featured')"
                    :subtitle="__('saqr_public.sections.featured_subtitle')"
                    :url="route('motorcycles.index')"
                />

                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($motorcycles as $motorcycle)
                        <x-public.motorcycle-card
                            :motorcycle="$motorcycle"
                        />
                    @endforeach
                </div>

                <div class="mt-10 text-center">
                    <a
                        href="{{ route('motorcycles.index') }}"
                        class="saqr-button-outline"
                    >
                        {{ __('saqr_public.actions.discover_more') }}
                    </a>
                </div>

            </div>
        </section>
    @endif

    {{-- Financial Plans Section --}}
    <x-public.plans :plans="$plans" />

    {{-- Services Section --}}
    <x-public.services :services="$services" />

    {{-- Locations Section --}}
    <x-public.locations :locations="$locations" />

@endsection
