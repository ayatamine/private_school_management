<?php

namespace App\Providers;
use App\Models\AcademicYear;
use Illuminate\Support\Facades\Cache;
use App\Http\Responses\LogoutResponse;
use Illuminate\Support\ServiceProvider;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse as LogoutResponseContract;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
         $this->app->bind(LogoutResponseContract::class, LogoutResponse::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Cache::rememberForever('default_academic_year_id', fn () => AcademicYear::where('is_default', true)->value('id'));
    }
}
