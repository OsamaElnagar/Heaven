<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\BookingsOverviewWidget;
use App\Filament\Widgets\RecentBookingsWidget;
use App\Filament\Widgets\RevenueChartWidget;
use App\Filament\Widgets\RevenueWidget;
use App\Filament\Widgets\SeatOccupancyWidget;
use App\Filament\Widgets\UpcomingTripsWidget;
use App\Filament\Widgets\VisaStatusWidget;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
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
            ->brandName('HEAVEN')
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                BookingsOverviewWidget::class,
                RevenueWidget::class,
                VisaStatusWidget::class,
                RevenueChartWidget::class,
                SeatOccupancyWidget::class,
                UpcomingTripsWidget::class,
                RecentBookingsWidget::class,
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
            ->authMiddleware([
                Authenticate::class,
            ])
            ->topNavigation(false)
            ->navigationGroups([
                NavigationGroup::make()
                    ->label(__('حجاج وحجوزات'))
                    ->collapsible()
                    ->collapsed(),
                NavigationGroup::make()
                    ->label(__('الموارد البشرية'))
                    ->collapsible()
                    ->collapsed(),
                NavigationGroup::make()
                    ->label(__('الموردون والفنادق'))
                    ->collapsible()
                    ->collapsed(),
                NavigationGroup::make()
                    ->label(__('الرحلات والباقات'))
                    ->collapsible()
                    ->collapsed(),
                NavigationGroup::make()
                    ->label(__('التأشيرات'))
                    ->collapsible()
                    ->collapsed(),
                NavigationGroup::make()
                    ->label(__('المحتوى'))
                    ->collapsible()
                    ->collapsed(),
                NavigationGroup::make()
                    ->label(__('التقارير'))
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
