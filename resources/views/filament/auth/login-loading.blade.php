
@once
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.hook('commit', ({ succeed }) => {
                succeed(({ effect, effects }) => {
                    if ((effect ?? effects ?? {}).redirect) {
                        document.body.classList.add('saqr-redirecting');
                    }
                });
            });
        });

        // Block re-submits (click or Enter) while redirecting
        document.addEventListener('submit', (e) => {
            if (document.body.classList.contains('saqr-redirecting')) {
                e.preventDefault();
                e.stopImmediatePropagation();
            }
        }, true);
    </script>
@endonce
<div
    wire:loading.delay.flex
    wire:target="authenticate"
    role="status"
    aria-live="polite"
    class="saqr-login-loading"
>
    <x-lucide-loader-circle
        class="h-5 w-5 animate-spin"
    />

    <span>
        {{ __('saqr.auth.signing_in') }}
    </span>
</div>
