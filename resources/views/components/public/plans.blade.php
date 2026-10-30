@props(['plans'])
<section id="financial-plans" class="saqr-section bg-[#14161c]">
    <div class="saqr-container">
        <x-public.section-heading :title="__('saqr_public.sections.financing')" :subtitle="__('saqr_public.sections.financing_subtitle')" />
        <div class="grid gap-5 md:grid-cols-3">
            @forelse($plans as $plan)
                <article class="relative overflow-hidden rounded-2xl border border-white/10 bg-[#1f2129] p-7">
                    <div class="absolute end-0 top-0 h-1 w-24 bg-red-500"></div>
                    <p class="text-xs font-bold uppercase tracking-widest text-red-400">{{ __('saqr_public.labels.installment_plan') }}</p>
                    <h3 class="mt-3 text-xl font-black">{{ $plan->localized_name }}</h3>
                    <p class="mt-3 min-h-16 text-sm leading-6 text-zinc-400">{{ $plan->localized_description }}</p>
                    <div class="mt-7 grid grid-cols-2 gap-4 border-t border-white/10 pt-6">
                        <div><div class="text-xs text-zinc-400">{{ __('saqr_public.labels.down_payment') }}</div><div class="mt-1 text-3xl font-black text-red-400" dir="ltr">{{ (float)$plan->down_payment_percent }}%</div></div>
                        <div><div class="text-xs text-zinc-400">{{ __('saqr_public.labels.duration') }}</div><div class="mt-1 text-3xl font-black">{{ $plan->installment_months }} <span class="text-xs font-medium">{{ __('saqr_public.labels.months') }}</span></div></div>
                    </div>
                </article>
            @empty
                <p class="text-zinc-400">{{ __('saqr_public.labels.no_plans') }}</p>
            @endforelse
        </div>
        <p class="mt-6 text-xs text-zinc-500">{{ __('saqr_public.labels.finance_disclaimer') }}</p>
    </div>
</section>
