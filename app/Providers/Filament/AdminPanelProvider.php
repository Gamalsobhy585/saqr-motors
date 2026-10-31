<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\EditProfile;
use App\Filament\Widgets\SaqrOverviewStats;
use App\Http\Middleware\SetLocale;
use Filament\Auth\Pages\Login;
use Filament\Enums\ThemeMode;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
           
        ->default()
        ->id('admin')
        ->path('admin')
        ->login()
        ->passwordReset()

        ->brandName('SAQR Motors')

        ->colors([
            'primary' => Color::hex('#DC2626'),
        ])

        ->viteTheme(
            'resources/css/filament/admin/theme.css'
        )

        ->brandLogo(
            asset('images/logos/saqr-light.png')
        )

        ->darkModeBrandLogo(
            asset('images/logos/saqr-dark.png')
        )

        ->brandLogoHeight('4rem')

        ->darkMode(true)
        ->defaultThemeMode(ThemeMode::Light)
        ->themeSwitcher(false)
        ->globalSearch(false)
        ->profile(EditProfile::class, isSimple: false)
        ->favicon(asset('images/logos/saqr-favicon.png'))

       

            ->renderHook(
                PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE,
                fn () => view('filament.auth.preferences')
            )
        ->renderHook(
            PanelsRenderHook::BODY_START,
            fn () => request()->routeIs('filament.admin.auth.login')
                ? view('filament.auth.toolbar', [
                    'insideTopbar' => false,
                ])
                : ''
        )
        ->renderHook(
            PanelsRenderHook::USER_MENU_BEFORE,
            fn () => view('filament.auth.toolbar', [
                'insideTopbar' => true,
            ])
        )

   

            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                SaqrOverviewStats::class,

            ])

            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])

            // Keep locale handling persistent for Livewire requests.
            ->middleware([
                SetLocale::class,
            ], isPersistent: true)

            // Keep authentication enforced on Livewire requests.
            ->authMiddleware([
                Authenticate::class,
            ], isPersistent: true);

    }
}
