<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use App\Filament\Auth\Login;
use App\Filament\Student\Resources\ReceiptVoucherResource;
use App\Filament\Student\Resources\StudentResource;
use Filament\Support\Colors\Color;
use Filament\Navigation\NavigationItem;
use Filament\Http\Middleware\Authenticate;
use Filament\FontProviders\GoogleFontProvider;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;

class StudentPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('student')
            ->path('students')
            ->login(Login::class)
            ->colors([
                'primary' => Color::Blue,
            ])
            ->font('Noto Kufi Arabic', provider: GoogleFontProvider::class)
            ->brandLogo(asset('images/brandlogo.png'))
            ->favicon(asset('images/brandlogo.png'))
            ->discoverResources(in: app_path('Filament/Student/Resources'), for: 'App\\Filament\\Student\\Resources')
            ->discoverPages(in: app_path('Filament/Student/Pages'), for: 'App\\Filament\\Student\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->navigationItems([
                NavigationItem::make('my_profile')
                    ->label(trans('main.my_profile'))
                    ->icon('icon-student_profile')
                    ->url(fn (): string => StudentResource::getUrl('view',[auth()->user()->student->id])),
                NavigationItem::make('my_payments')
                    ->label(trans('main.my_payments'))
                    ->icon('icon-receipt_voucher')
                    ->url(fn (): string => ReceiptVoucherResource::getUrl('index')),
            ])
            ->discoverWidgets(in: app_path('Filament/Student/Widgets'), for: 'App\\Filament\\Student\\Widgets')
            ->widgets([
                // Widgets\AccountWidget::class,
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
                'is_student'
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->viteTheme('resources/css/filament/admin/theme.css');
    }
}
