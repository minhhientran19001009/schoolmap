<?php

namespace App\Providers\Filament;

use App\Filament\School\Resources\ManagedSchools\ManagedSchoolResource;
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
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class SchoolPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('school')
            ->path('truong')
            ->login()
            ->passwordReset()
            ->spa()
            ->colors([
                'primary' => Color::Blue,
            ])
            ->font('Roboto')
            ->brandName('Cổng thông tin Nhà trường')
            ->homeUrl(fn (): string => ManagedSchoolResource::getUrl())
            ->sidebarCollapsibleOnDesktop()
            ->renderHook(
                PanelsRenderHook::BODY_START,
                fn () => view('filament.components.loading-bar')
            )
            ->discoverResources(
                in: app_path('Filament/School/Resources'),
                for: 'App\\Filament\\School\\Resources',
            )
            ->pages([
                Dashboard::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
