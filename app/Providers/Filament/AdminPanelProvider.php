<?php

namespace App\Providers\Filament;

use AchyutN\FilamentLogViewer\FilamentLogViewer;
use App\Filament\Resources\Packages\Widgets\SeatOccupancyWidget;
use App\Filament\Widgets\RecentBookingsWidget;
use App\Filament\Widgets\RevenueChartWidget;
use App\Filament\Widgets\RevenueWidget;
use App\Filament\Widgets\UpcomingTripsWidget;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Illuminate\Contracts\View\View;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use ShuvroRoy\FilamentSpatieLaravelBackup\FilamentSpatieLaravelBackupPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandLogo(fn (): HtmlString => new HtmlString(
                '<div style="display: flex; align-items: center; gap: 10px;">'.
                    '<img src="'.asset('assets/heaven-logo.jpg').'" alt="'.config('app.name').'" style="height: 45px;">'.
                    '<span style="font-size: 16px; font-weight: 700;">'.config('app.name').'</span>'.
                    '</div>',
            ))
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            // ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                RevenueWidget::class,
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
            ->readOnlyRelationManagersOnResourceViewPagesByDefault(false)
            ->topNavigation(false)
            ->sidebarCollapsibleOnDesktop()
            ->navigationGroups([
                NavigationGroup::make()
                    ->label(__('عملاء وحجوزات'))
                    ->collapsible()
                    ->collapsed(),
                NavigationGroup::make()
                    ->label(__('الموردون والفنادق والرحلات'))
                    ->collapsible()
                    ->collapsed(),
                NavigationGroup::make()
                    ->label(__('الوكلاء والفروع'))
                    ->collapsible()
                    ->collapsed(),
                NavigationGroup::make()
                    ->label(__('الموارد البشرية'))
                    ->collapsible()
                    ->collapsed(),
                NavigationGroup::make()
                    ->label(__('المحتوى'))
                    ->collapsible()
                    ->collapsed(),
                NavigationGroup::make()
                    ->label(__('المحاسبة'))
                    ->collapsible()
                    ->collapsed(),
                NavigationGroup::make()
                    ->label(__('الخزينة والسندات'))
                    ->collapsible()
                    ->collapsed(),
                NavigationGroup::make()
                    ->label(__('التقارير المالية'))
                    ->collapsible()
                    ->collapsed(),
                NavigationGroup::make()
                    ->label(__('الإعدادات'))
                    ->collapsible()
                    ->collapsed(),
            ])
            ->renderHook(
                PanelsRenderHook::GLOBAL_SEARCH_BEFORE,
                fn (): View => view('filament.topbar.quick-links'),
            )
            ->plugins([
                FilamentLogViewer::make()
                    ->registerNavigation(true)
                    ->navigationGroup('الإعدادات')
                    ->navigationIcon('heroicon-o-document-text')
                    ->navigationLabel('Log-Viewer')
                    ->navigationSort(10)
                    ->navigationUrl('/logs')
                    ->pollingTime(null),
                FilamentSpatieLaravelBackupPlugin::make()
                    ->navigationIcon('heroicon-o-cpu-chip')
                    ->navigationLabel('Backups-نسخ احتياطى')
                    ->navigationGroup('الإعدادات')
                    ->navigationSort(3)
                    ->usingPolingInterval('10000s'),
            ]);
    }
}
