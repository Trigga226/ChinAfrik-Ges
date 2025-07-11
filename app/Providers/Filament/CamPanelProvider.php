<?php

namespace App\Providers\Filament;

use App\Filament\Cam\Pages\MonDashboardCamion;
use App\Filament\Cam\Resources\PointageCamionResource\Widgets\PointageChart;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use Filament\View\PanelsRenderHook;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Joaopaulolndev\FilamentGeneralSettings\FilamentGeneralSettingsPlugin;
use Leandrocfe\FilamentApexCharts\FilamentApexChartsPlugin;
use Raseldev99\FilamentMessages\FilamentMessagesPlugin;
use Saade\FilamentFullCalendar\FilamentFullCalendarPlugin;

class CamPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('cam')
            ->path('camion')
            ->colors([
                'primary' => Color::Indigo,
            ])->brandName("Kosboura")
            ->brandLogo('/logo2.png')
            ->maxContentWidth(MaxWidth::Full)
            ->discoverResources(in: app_path('Filament/Cam/Resources'), for: 'App\\Filament\\Cam\\Resources')
            ->discoverPages(in: app_path('Filament/Cam/Pages'), for: 'App\\Filament\\Cam\\Pages')
            ->pages([
                MonDashboardCamion::class,
            ])
            ->spa()
            ->discoverWidgets(in: app_path('Filament/Cam/Widgets'), for: 'App\\Filament\\Cam\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
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
            ])
            ->plugins([
                FilamentShieldPlugin::make(),
                FilamentMessagesPlugin::make(),
                FilamentGeneralSettingsPlugin::make()
                    ->canAccess(true )
                    ->setSort(3)
                    ->setIcon('heroicon-o-cog')
                    ->setNavigationGroup('Administration')
                    ->setTitle('Parametrage')
                    ->setNavigationLabel('Parametrage'),
                \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make()
                    ->gridColumns([
                        'default' => 1,
                        'sm' => 2,
                        'lg' => 3
                    ])
                    ->sectionColumnSpan(1)
                    ->checkboxListColumns([
                        'default' => 1,
                        'sm' => 2,
                        'lg' => 4,
                    ])
                    ->resourceCheckboxListColumns([
                        'default' => 1,
                        'sm' => 2,
                    ]),
                FilamentFullCalendarPlugin::make()
                    ->schedulerLicenseKey('')
                    ->selectable(true)
                    ->editable(false)
                    ->timezone('UTC')
                    ->locale('fr')
                    ->plugins(['dayGrid', 'timeGrid',])
                    ->config([]),
                FilamentApexChartsPlugin::make(),
                //new Accounting()
            ]);
    }
}
