<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use App\Filament\Auth\Login;
use Filament\Support\Colors\Color;
use Illuminate\Support\Facades\Blade;
use Filament\Navigation\NavigationItem;
use Filament\Http\Middleware\Authenticate;
use App\Filament\Resources\EmployeeResource;
use App\Filament\Resources\TransferResource;
use Filament\FontProviders\GoogleFontProvider;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use App\Filament\Resources\NewestStudentResource;
use App\Filament\Resources\SchoolSettingResource;
use App\Filament\Resources\FinanceAccountResource;
use App\Filament\Resources\FinanceAccountAdResource;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Filament\RenderHooks\PanelsRenderHook;

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
            ->profile()
            ->passwordReset()
            ->brandLogo(asset('images/brandlogo.png'))
            ->favicon(asset('images/brandlogo.png'))
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->renderHook(
                'panels.topbar.end',
                fn (): string => Blade::render('@livewire(\'change-default-academic-year\')'),
            )
            ->navigationItems([
                NavigationItem::make('current_academic_year')
                ->label(trans('main.current_academic_year'))
                ->icon('heroicon-o-calendar')
                ->url(fn (): string => '#')
                ->visible(false),
                NavigationItem::make('my_profile')
                    ->label(trans('main.my_profile'))
                    ->icon('icon-employees')
                    ->visible(fn()=>auth()->user()?->employee != null && !auth()->user()->hasRole('super_admin'))
                    ->url(fn (): string => EmployeeResource::getUrl('view',[auth()->user()?->employee?->id])),
                NavigationItem::make('school_settings')
                    ->label(trans('main.school_settings'))
                    ->icon('icon-school')
                    ->group(trans('main.settings'))
                    ->visible(fn()=>employeeHasPermission('view_any_school::setting'))
                    ->url(fn (): string => SchoolSettingResource::getUrl('edit',[1])),
                NavigationItem::make('add_student')
                    ->label(trans('main.add_student'))
                    ->icon('heroicon-o-plus')
                    ->group(trans('main.student_settings'))
                    ->parentItem(trans('main.student_registration'))
                    ->visible(fn()=>employeeHasPermission('create_newest::student'))
                    ->url(fn (): string => NewestStudentResource::getUrl('create')),
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
                'is_administrator',
                'can_see_employees_page',
                'ensure_employee_view_roles_page',
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->plugins([
                \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make()
            ])
            ->viteTheme('resources/css/filament/admin/theme.css');
    }
}
