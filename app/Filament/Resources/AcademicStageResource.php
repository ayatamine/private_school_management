<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AcademicStageResource\Pages;
use App\Filament\Resources\AcademicStageResource\RelationManagers;
use App\Models\AcademicStage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AcademicStageResource extends Resource
{
    protected static ?string $model = AcademicStage::class;

    protected static ?string $navigationIcon = 'icon-academic_stage';
    public static function getNavigationGroup():string
    {
        return trans('main.academic_settings');
    }
    public static function getModelLabel():string
    {
        return trans_choice('main.academic_stage',1);
    }
    public static function getNavigationLabel():string
    {
        return trans_choice('main.academic_stage',2);
    }

    public static function getPluralModelLabel():string
    {
        return trans_choice('main.academic_stage',2);
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->label(trans(key: 'main.name'))
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label(trans('main.name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('updated_at')->label(trans('main.updated_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAcademicStages::route('/'),
            'create' => Pages\CreateAcademicStage::route('/create'),
            'edit' => Pages\EditAcademicStage::route('/{record}/edit'),
        ];
    }
}
