<?php

namespace App\Providers\Filament;

use App\Filament\Pages\MonDashboardBourse;
use App\Filament\Resources\UserResource;
use Filament\Support\Enums\MaxWidth;
use Leandrocfe\FilamentApexCharts\FilamentApexChartsPlugin;
use Xoshbin\JmeryarAccounting\Accounting;
use CharrafiMed\GlobalSearchModal\GlobalSearchModalPlugin;
use DiogoGPinto\AuthUIEnhancer\AuthUIEnhancerPlugin;
use Filament\Http\Middleware\Authenticate;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
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
use Illuminate\Support\Facades\Storage;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Joaopaulolndev\FilamentGeneralSettings\FilamentGeneralSettingsPlugin;
use Joaopaulolndev\FilamentGeneralSettings\Models\GeneralSetting;
use Raseldev99\FilamentMessages\FilamentMessagesPlugin;
use Saade\FilamentFullCalendar\FilamentFullCalendarPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {

        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->sidebarCollapsibleOnDesktop()
            ->brandLogo(function () {
                $setting=GeneralSetting::find(1);
                if(!is_null($setting)){
                   if (!is_null($setting->site_logo)){
                       return Storage::url($setting->site_logo);
                   }else{
                       return null;
                   }
                }else{
                    return null;
                }
            })
            ->favicon(function () {
                $setting=GeneralSetting::find(1);
                if(!is_null($setting)){
                    if (!is_null($setting->site_favicon)){
                        return Storage::url($setting->site_favicon);
                    }else{
                        return null;
                    }
                }else{
                    return null;
                }
            })
            ->brandName(function () {
                $setting=GeneralSetting::find(1);
                if(!is_null($setting)){
                    return $setting->site_name;
                }else{
                    return "KoboSoft";
                }
            })
            ->maxContentWidth(MaxWidth::Full)
            ->font("Poppins")
            ->login()
            ->theme(asset('css/filament/admin/theme.css'))
            ->databaseNotifications()
            ->databaseNotificationsPolling('5s')
            ->colors([
                'primary' => Color::Red,
            ])
            ->databaseTransactions()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                MonDashboardBourse::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
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
                    ->canAccess(fn() => auth()->user()->hasRole('super_admin') )
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
            ])
            ;
    }
}
