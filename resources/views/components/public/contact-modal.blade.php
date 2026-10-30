@props(['motorcycle'])
<div x-cloak x-show="modal==='contact'" x-transition.opacity class="fixed inset-0 z-[100] flex items-center justify-center overflow-y-auto bg-black/80 p-4" role="presentation" x-on:click.self="modal=null">
    <section role="dialog" aria-modal="true" aria-labelledby="contact-modal-title" class="my-auto w-full max-w-xl rounded-2xl border border-white/15 bg-[#1b1d24] p-6 shadow-2xl sm:p-8">
        <div class="flex items-start justify-between gap-5">
            <div><p class="text-xs font-bold uppercase tracking-[.2em] text-red-400">{{ $motorcycle->brand->name }} · {{ $motorcycle->name }}</p><h2 id="contact-modal-title" class="mt-2 text-2xl font-black">{{ __('saqr_public.actions.contact_us') }}</h2></div>
            <button type="button" x-on:click="modal=null" aria-label="{{ __('saqr_public.actions.close') }}" class="rounded-lg border border-white/10 px-3 py-1.5 text-xl text-zinc-300 hover:text-white">×</button>
        </div>
        <form method="POST" action="{{ route('motorcycles.contact', ['motorcycle' => $motorcycle->uuid]) }}" class="mt-7 grid gap-4" x-data="{ sending: false }" x-on:submit="sending=true">
            @csrf
            <input type="hidden" name="form_type" value="contact">
            <label class="grid gap-1.5 text-sm">{{ __('saqr_public.fields.name') }}<input class="saqr-input" type="text" name="contact_name" autocomplete="name" maxlength="120" required value="{{ old('contact_name') }}">@error('contact_name')<span class="text-xs text-red-400">{{ $message }}</span>@enderror</label>
            <div class="grid gap-4 sm:grid-cols-2">
                <label class="grid gap-1.5 text-sm">{{ __('saqr_public.fields.email') }}<input class="saqr-input" type="email" name="contact_email" autocomplete="email" maxlength="255" required value="{{ old('contact_email') }}">@error('contact_email')<span class="text-xs text-red-400">{{ $message }}</span>@enderror</label>
                <label class="grid gap-1.5 text-sm">{{ __('saqr_public.fields.phone') }}<input class="saqr-input" type="tel" name="contact_phone" autocomplete="tel" required placeholder="01012345678" value="{{ old('contact_phone') }}">@error('contact_phone')<span class="text-xs text-red-400">{{ $message }}</span>@enderror</label>
            </div>
            <label class="grid gap-1.5 text-sm">{{ __('saqr_public.fields.message') }}<textarea class="saqr-input" rows="5" maxlength="3000" name="contact_message" required>{{ old('contact_message') }}</textarea>@error('contact_message')<span class="text-xs text-red-400">{{ $message }}</span>@enderror</label>
            <button type="submit" x-bind:disabled="sending" class="saqr-button-primary mt-2 w-full disabled:opacity-60"><span x-text="sending ? @js(__('saqr_public.actions.sending')) : @js(__('saqr_public.actions.send_message'))">{{ __('saqr_public.actions.send_message') }}</span></button>
        </form>
    </section>
</div>
