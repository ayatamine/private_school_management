<?php

namespace App\Filament\Pages;

use Filament\Forms;
use Filament\Tables;
use Filament\Pages\Page;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class StudentTransportation extends Page  implements HasForms, HasTable
{
    use InteractsWithTable;    use InteractsWithForms;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.student-transportation';

    protected function getTableQuery(): \Illuminate\Database\Eloquent\Builder
    {
        // Convert DB facade query to a query builder instance
        return DB::table('student_transportation'); // Returns a query builder, not a collection
    }
    
    public  function table(Table $table): Table
    {
        return $table
        ->relationship(fn (): BelongsToMany => $this->category->products())
        ->inverseRelationship('categories')
        ->columns([
            TextColumn::make('name'),
        ]);
    }
}
