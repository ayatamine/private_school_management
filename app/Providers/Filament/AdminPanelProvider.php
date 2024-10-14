<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use App\Filament\Auth\Login;
use Filament\Support\Colors\Color;
use Filament\Navigation\NavigationItem;
use Filament\Http\Middleware\Authenticate;
use App\Filament\Pages\StudentTransportation;
use App\Filament\Resources\FinanceAccountResource;
use Filament\FontProviders\GoogleFontProvider;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use App\Filament\Resources\SchoolSettingResource;
use App\Filament\Resources\StudentResource;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(Login::class)
            ->colors([
                'primary' => Color::Amber,
            ])
            ->font('Noto Kufi Arabic', provider: GoogleFontProvider::class)
            ->brandLogo(asset('images/brandlogo.png'))
            ->favicon(asset('images/brandlogo.png'))
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->navigationItems([
                NavigationItem::make('school_settings')
                    ->label(trans('main.school_settings'))
                    ->icon('icon-school')
                    ->url(fn (): string => SchoolSettingResource::getUrl('edit',[1])),
                NavigationItem::make('finance_account')
                    ->label(trans_choice('main.add_finance_account',2))
                    ->icon('icon-finance_accounts')
                    ->group(trans('main.finance_settings'))
                    ->url(fn (): string => FinanceAccountResource::getUrl('create')),
                NavigationItem::make('add_student')
                    ->label(trans('main.add_student'))
                    ->icon('heroicon-o-plus')
                    ->group(trans('main.student_settings'))
                    ->parentItem(trans('main.student_registration'))
                    ->url(fn (): string => StudentResource::getUrl('create')),
            ])
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
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
                \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make()
            ]);
    }
}
