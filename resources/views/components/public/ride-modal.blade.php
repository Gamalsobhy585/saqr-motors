@props(['motorcycle', 'locations', 'rideSlots'])
<div x-cloak x-show="modal==='ride'" x-transition.opacity class="fixed inset-0 z-[100] flex items-center justify-center overflow-y-auto bg-black/80 p-4" role="presentation" x-on:click.self="modal=null">
    <section role="dialog" aria-modal="true" aria-labelledby="ride-modal-title" class="my-auto w-full max-w-xl rounded-2xl border border-white/15 bg-[#1b1d24] p-6 shadow-2xl sm:p-8">
        <div class="flex items-start justify-between gap-5">
            <div><p class="text-xs font-bold uppercase tracking-[.2em] text-red-400">{{ $motorcycle->brand->name }} · {{ $motorcycle->name }}</p><h2 id="ride-modal-title" class="mt-2 text-2xl font-black">{{ __('saqr_public.actions.book_ride') }}</h2></div>
            <button type="button" x-on:click="modal=null" aria-label="{{ __('saqr_public.actions.close') }}" class="rounded-lg border border-white/10 px-3 py-1.5 text-xl text-zinc-300 hover:text-white">×</button>
        </div>
        <p class="mt-3 text-sm leading-6 text-zinc-400">{{ __('saqr_public.labels.ride_note') }}</p>
        <form method="POST" action="{{ route('motorcycles.book', ['motorcycle' => $motorcycle->uuid]) }}" class="mt-7 grid gap-4" x-data="{ sending: false }" x-on:submit="sending=true">
            @csrf
            <input type="hidden" name="form_type" value="ride">
            <label class="grid gap-1.5 text-sm">{{ __('saqr_public.fields.name') }}<input x-ref="rideName" class="saqr-input" type="text" name="ride_name" maxlength="120" autocomplete="name" required value="{{ old('ride_name') }}">@error('ride_name')<span class="text-xs text-red-400">{{ $message }}</span>@enderror</label>
            <div class="grid gap-4 sm:grid-cols-2">
                <label class="grid gap-1.5 text-sm">{{ __('saqr_public.fields.email') }}<input class="saqr-input" type="email" name="ride_email" maxlength="255" autocomplete="email" required value="{{ old('ride_email') }}">@error('ride_email')<span class="text-xs text-red-400">{{ $message }}</span>@enderror</label>
                <label class="grid gap-1.5 text-sm">{{ __('saqr_public.fields.phone') }}<input class="saqr-input" type="tel" name="ride_phone" autocomplete="tel" required placeholder="01012345678" value="{{ old('ride_phone') }}">@error('ride_phone')<span class="text-xs text-red-400">{{ $message }}</span>@enderror</label>
            </div>
            <label class="grid gap-1.5 text-sm">{{ __('saqr_public.fields.location') }}<select class="saqr-input" name="ride_location_id" required><option value="">{{ __('saqr_public.labels.choose_location') }}</option>
                @foreach($locations as $location)<option value="{{ $location->id }}" @selected((string)old('ride_location_id') === (string)$location->id)>{{ $location->display_name }} — {{ $location->cityRelation?->display_name }}</option>@endforeach
            </select>@error('ride_location_id')<span class="text-xs text-red-400">{{ $message }}</span>@enderror</label>
            <div class="grid gap-4 sm:grid-cols-2">
                <label class="grid gap-1.5 text-sm">{{ __('saqr_public.fields.date') }}<input class="saqr-input" type="date" name="ride_date" min="{{ today()->toDateString() }}" required value="{{ old('ride_date') }}">@error('ride_date')<span class="text-xs text-red-400">{{ $message }}</span>@enderror</label>
                <label class="grid gap-1.5 text-sm">{{ __('saqr_public.fields.time') }}<select class="saqr-input" name="ride_time" required><option value="">{{ __('saqr_public.labels.choose_time') }}</option>@foreach($rideSlots as $time)<option value="{{ $time }}" @selected(old('ride_time') === $time)>{{ $time }}</option>@endforeach</select>@error('ride_time')<span class="text-xs text-red-400">{{ $message }}</span>@enderror</label>
            </div>
            <label class="grid gap-1.5 text-sm">{{ __('saqr_public.fields.notes') }}<textarea class="saqr-input" rows="2" maxlength="1000" name="ride_notes">{{ old('ride_notes') }}</textarea>@error('ride_notes')<span class="text-xs text-red-400">{{ $message }}</span>@enderror</label>
            <button type="submit" x-bind:disabled="sending" class="saqr-button-primary mt-2 w-full disabled:opacity-60"><span x-text="sending ? @js(__('saqr_public.actions.sending')) : @js(__('saqr_public.actions.send_booking'))">{{ __('saqr_public.actions.send_booking') }}</span></button>
        </form>
    </section>
</div>
