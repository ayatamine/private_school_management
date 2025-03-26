<?php

namespace App\Filament\Resources\TuitionFeeReportsResource\Pages;

use App\Filament\Resources\TuitionFeeReportsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTuitionFeeReports extends ListRecords
{
    protected static string $resource = TuitionFeeReportsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            print_table_resrource_list('print_tuition_fees_reports',route('print_pdf',['type'=>"tuition_fees_reports"]))
        ];
    }
}
