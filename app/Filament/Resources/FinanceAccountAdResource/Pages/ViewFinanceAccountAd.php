<?php

namespace App\Filament\Resources\FinanceAccountAdResource\Pages;

use App\Filament\Resources\FinanceAccountAdResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewFinanceAccountAd extends ViewRecord
{
    protected static string $resource = FinanceAccountAdResource::class;
    protected static string $view = 'filament.resources.finance-accounts.pages.view-finance-account-ad';
}
