<footer class="border-t border-white/10 bg-[#111318]">
    <div class="saqr-container grid gap-10 py-14 sm:grid-cols-2 lg:grid-cols-3">
        <div>
            <a href="{{ route('home') }}" class="text-2xl font-black uppercase tracking-[.2em] text-white">SAQR <span class="text-red-500">MOTORS</span></a>
            <p class="mt-4 max-w-xs text-sm leading-7 text-zinc-400">{{ __('saqr_public.footer.tagline') }}</p>
        </div>
        <div>
            <h3 class="font-bold text-white">{{ __('saqr_public.footer.quick_links') }}</h3>
            <div class="mt-5 grid gap-3 text-sm text-zinc-400">
                <a class="transition hover:text-red-400" href="{{ route('motorcycles.index') }}">{{ __('saqr_public.nav.motorcycles') }}</a>
                <a class="transition hover:text-red-400" href="{{ route('motorcycles.index', ['promotion' => 1]) }}">{{ __('saqr_public.nav.promotions') }}</a>
                <a class="transition hover:text-red-400" href="{{ route('home') }}#locations">{{ __('saqr_public.nav.locations') }}</a>
                <a class="transition hover:text-red-400" href="{{ route('home') }}#financial-plans">{{ __('saqr_public.nav.financing') }}</a>
            </div>
        </div>
        <div>
            <h3 class="font-bold text-white">{{ __('saqr_public.footer.follow') }}</h3>
            <div class="mt-5 flex flex-wrap gap-3">
                <a href="{{ config('saqr_public.facebook') }}" target="_blank" rel="noopener noreferrer" aria-label="Facebook" title="Facebook" class="saqr-social-link">
                    <svg viewBox="0 0 24 24" class="size-5" fill="currentColor" aria-hidden="true"><path d="M13.5 21v-8h2.7l.4-3.1h-3.1v-2c0-.9.3-1.5 1.6-1.5h1.7V3.6c-.3 0-1.3-.1-2.5-.1-2.5 0-4.2 1.5-4.2 4.4v2H7.5V13h2.6v8h3.4Z"/></svg>
                </a>
                <a href="{{ config('saqr_public.instagram') }}" target="_blank" rel="noopener noreferrer" aria-label="Instagram" title="Instagram" class="saqr-social-link">
                    <svg viewBox="0 0 24 24" class="size-5" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg>
                </a>
                <a href="{{ config('saqr_public.youtube') }}" target="_blank" rel="noopener noreferrer" aria-label="YouTube" title="YouTube" class="saqr-social-link">
                    <svg viewBox="0 0 24 24" class="size-5" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2.5" y="5" width="19" height="14" rx="4"/><path d="m10 9 5 3-5 3V9Z" fill="currentColor" stroke="none"/></svg>
                </a>
                <a href="{{ config('saqr_public.tiktok') }}" target="_blank" rel="noopener noreferrer" aria-label="TikTok" title="TikTok" class="saqr-social-link">
                    <svg viewBox="0 0 24 24" class="size-5" fill="currentColor" aria-hidden="true"><path d="M16 2h-3v12.25a3.25 3.25 0 1 1-2.75-3.22V8.02A6.25 6.25 0 1 0 16 14.25V8.2a7.2 7.2 0 0 0 4.5 1.55v-3a4.5 4.5 0 0 1-4.5-4.5V2Z"/></svg>
                </a>
            </div>
        </div>
    </div>
    <div class="border-t border-white/10 px-4 py-5 text-center text-xs text-zinc-500">© {{ now()->year }} SAQR Motors. {{ __('saqr_public.footer.rights') }}</div>
</footer>
