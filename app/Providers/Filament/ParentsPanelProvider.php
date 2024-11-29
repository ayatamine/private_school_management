<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use App\Filament\Auth\Login;
use App\Filament\Parents\Resources\ParentModelResource;
use App\Filament\Parents\Resources\ReceiptVoucherResource;
use App\Filament\Parents\Resources\StudentResource;
use App\Models\ReceiptVoucher;
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

class ParentsPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('parents')
            ->path('parents')
            ->login(Login::class)
            ->colors([
                'primary' => Color::Slate,
            ])
            ->font('Noto Kufi Arabic', provider: GoogleFontProvider::class)
            ->brandLogo(asset('images/brandlogo.png'))
            ->favicon(asset('images/brandlogo.png'))
            ->discoverResources(in: app_path('Filament/Parents/Resources'), for: 'App\\Filament\\Parents\\Resources')
            ->discoverPages(in: app_path('Filament/Parents/Pages'), for: 'App\\Filament\\Parents\\Pages')
            ->navigationItems([
                NavigationItem::make('my_profile')
                    ->label(trans('main.my_profile'))
                    ->icon('icon-parents')
                    ->isActiveWhen(fn()=>request()->segment(2) == "parent-models")
                    ->url(fn (): string => ParentModelResource::getUrl('view',[auth()->user()?->parent?->id])),
                NavigationItem::make('children')
                    ->label(trans('main.children'))
                    ->icon('icon-students')
                    ->isActiveWhen(fn()=>request()->segment(2) == "students")
                    ->url(fn (): string => StudentResource::getUrl('index')),
                NavigationItem::make('receipt_voucher')
                    ->label(trans_choice('main.receipt_voucher',2))
                    ->icon('icon-receipt_voucher')
                    ->isActiveWhen(fn()=>request()->segment(2) == "receipt-vouchers")
                    ->url(fn (): string => ReceiptVoucherResource::getUrl('index')),
            ])
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Parents/Widgets'), for: 'App\\Filament\\Parents\\Widgets')
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
                'is_parent'
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->viteTheme('resources/css/filament/admin/theme.css');
    }
}
