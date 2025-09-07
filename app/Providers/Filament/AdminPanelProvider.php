<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
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
            ->brandName('Choose Your Cart')
            ->brandLogo(asset('images/logo.png'))
            ->brandLogoHeight('2rem')
            ->favicon(public_path('favicon.ico'))
            ->colors([
                'primary' => Color::Blue,
                'gray' => Color::Slate,
                'success' => Color::Green,
                'warning' => Color::Orange,
                'danger' => Color::Red,
            ])
            ->font('Inter')
            ->darkMode(false)
            ->maxContentWidth('full')
            ->sidebarCollapsibleOnDesktop()
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->databaseNotifications()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                \App\Filament\Widgets\EcommerceStatsWidget::class,
                \App\Filament\Widgets\SalesChartWidget::class,
                \App\Filament\Widgets\LowStockProductsWidget::class,
                \App\Filament\Widgets\TopProductsWidget::class,
            ])
            ->navigationGroups([
                'Catalog',
                'Sales', 
                'Content',
                'Analytics',
                'System',
            ])
            ->renderHook(
                'panels::body.end',
                fn (): string => view('filament.footer')->render()
            )
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
