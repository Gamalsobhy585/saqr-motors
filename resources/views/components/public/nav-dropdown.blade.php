@props(['name', 'label'])
<div class="relative" x-on:keydown.escape.window="open = null">
    <button
        type="button"
        x-on:click="open = open === @js($name) ? null : @js($name)"
        x-on:click.outside="if (open === @js($name)) open = null"
        :aria-expanded="open === @js($name)"
        aria-haspopup="true"
        class="saqr-nav-link group inline-flex items-center gap-1.5"
    >
        {{ $label }}
        <svg class="size-4 text-zinc-500 transition duration-200 group-hover:text-red-400" :class="open === @js($name) && 'rotate-180'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m6 9 6 6 6-6" /></svg>
    </button>
    <div
        x-cloak
        x-show="open === @js($name)"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 -translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-1"
        class="absolute start-0 top-full z-50 mt-3 min-w-60 max-w-80 rounded-2xl border border-white/15 bg-[#1b1e24] p-2 shadow-[0_22px_50px_rgba(0,0,0,.65)]"
    >
        {{ $slot }}
    </div>
</div>
