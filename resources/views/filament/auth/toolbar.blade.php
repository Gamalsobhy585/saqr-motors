
<div
    class="saqr-auth-toolbar {{ ($insideTopbar ?? false) ? 'saqr-auth-toolbar--panel' : 'saqr-auth-toolbar--floating' }}"
    dir="ltr"
>
    {{-- Language switcher --}}
    <form
        method="POST"
        action="{{ route('locale.switch') }}"
    >
        @csrf

        <label for="saqr-locale" class="sr-only">
            Language
        </label>

        <select
            id="saqr-locale"
            name="locale"
            onchange="this.form.submit()"
            class="saqr-toolbar-control"
        >
            <option
                value="en"
                @selected(app()->getLocale() === 'en')
            >
                English
            </option>

            <option
                value="ar"
                @selected(app()->getLocale() === 'ar')
            >
                العربية
            </option>
        </select>
    </form>

    {{-- Single Alpine theme toggle --}}
    <div
        x-data="{
            theme: localStorage.getItem('theme') || 'light',

            setTheme(value) {
                this.theme = value;

                localStorage.setItem('theme', value);

                document.documentElement.classList.toggle(
                    'dark',
                    value === 'dark'
                );
            },

            toggleTheme() {
                this.setTheme(
                    this.theme === 'dark' ? 'light' : 'dark'
                );
            }
        }"
        x-init="setTheme(theme)"
        class="saqr-theme-controls"
    >
        <button
            type="button"
            class="saqr-theme-button"
            x-on:click="toggleTheme()"
            x-bind:aria-label="
                theme === 'dark'
                    ? 'Switch to light theme'
                    : 'Switch to dark theme'
            "
            x-bind:title="
                theme === 'dark'
                    ? 'Light theme'
                    : 'Dark theme'
            "
        >
            <span x-show="theme === 'light'">
                <x-lucide-moon class="h-5 w-5" />
            </span>

            <span x-show="theme === 'dark'" x-cloak>
                <x-lucide-sun class="h-5 w-5" />
            </span>
        </button>
    </div>

</div>
