<?php

namespace App\Filament\Resources\ValueAddedTaxResource\Pages;

use App\Filament\Resources\ValueAddedTaxResource;
use Filament\Resources\Pages\Page;

class UpdateTaxNumber extends Page
{
    protected static string $resource = ValueAddedTaxResource::class;

    protected static string $view = 'filament.resources.value-added-tax-resource.pages.update-tax-number';
}
