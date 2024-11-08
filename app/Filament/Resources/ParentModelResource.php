<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ParentModelResource\Pages;
use App\Filament\Resources\ParentModelResource\RelationManagers;
use App\Models\ParentModel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;

class ParentModelResource extends Resource
{
    protected static ?string $model = ParentModel::class;

    protected static ?string $navigationIcon = 'icon-parents';
    // public static function getNavigationGroup():string
    // {
    //     return trans('main.employee_settings');
    // }
    public static function getModelLabel():string
    {
        return trans_choice('main.parent',1);
    }
    public static function getNavigationLabel():string
    {
        return trans_choice('main.parent',2);
    }

    public static function getPluralModelLabel():string
    {
        return trans_choice('main.parent',2);
    }
   
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                
                Forms\Components\TextInput::make('full_name')->label(trans('main.full_name'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('relation')->label(trans('main.parent_relation'))
                    ->options(
                        [
                            'father'=>'الأب','mother'=>'الأم','brother'=>'الأخ','sister'=>'الأخت','guardian'=>'وصي','other'=>'اخرى'
                        ]
                    )
                    ->required(),
                Forms\Components\TextInput::make('national_id')->label(trans('main.national_id'))
                    ->required()
                    ->unique(table:'users',ignoreRecord: true)
                    ->maxLength(10),           
                Forms\Components\TextInput::make('phone_number')->label(trans('main.phone_number'))
                    ->required()
                    ->unique(table:'users',ignoreRecord: true)
                    ->maxLength(13),   
                Forms\Components\Select::make(name: 'gender')->label(trans('main.gender'))
                    ->options(['male'=>'ذكر', 'id'=>'أنثى'])
                    ->required(),        
                Forms\Components\TextInput::make('password')->label(trans('main.password'))->hint(trans('main.you_can_change_password'))
                    ->maxLength(255),        
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
               
                Tables\Columns\TextColumn::make('full_name')->label(trans('main.full_name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('relation')->label(trans('main.relation'))
                    ->formatStateUsing(fn (string $state) => trans("main.$state"))
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.national_id')->label(trans('main.national_id'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.phone_number')->label(trans('main.phone_number'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.gender')->label(trans('main.gender'))
                    ->formatStateUsing(fn (string $state) => trans("main.$state"))
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')->label(trans('main.created_at'))
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')->label(trans('main.updated_at'))
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                FilamentExportBulkAction::make('export')->label(trans('main.print'))->color('info')
                ->extraViewData([
                    'table_header' => trans('main.menu').' '.trans_choice('main.parent',2)
                ])->disableXlsx(),
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
            'index' => Pages\ListParentModels::route('/'),
            'create' => Pages\CreateParentModel::route('/create'),
            'edit' => Pages\EditParentModel::route('/{record}/edit'),
        ];
    }
}
