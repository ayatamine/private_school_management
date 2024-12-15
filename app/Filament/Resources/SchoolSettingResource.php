<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\SchoolSetting;
use Filament\Resources\Resource;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\SchoolSettingResource\Pages;
use App\Filament\Resources\SchoolSettingResource\RelationManagers;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;

class SchoolSettingResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = SchoolSetting::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    public static function shouldRegisterNavigation():bool 
    {
        return false;
    }
    public static function getModelLabel():string
    {
        return trans('main.school_settings');
    }
    public static function getNavigationLabel():string
    {
        return trans('main.school_settings');
    }

    public static function getPluralModelLabel():string
    {
        return trans('main.school_settings');
    }
    
    public static function getPermissionPrefixes(): array
    {
        return [
            'view_in_menu',
            'view',
            'update',            
        ];
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('')
                ->schema([
                Forms\Components\TextInput::make('title')->label(trans('main.school_title'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone_number')->label(trans('main.phone_number'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')->label(trans('main.email'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('address')->label(trans('main.address'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('website')->label(trans('main.website'))
                    ->url(),
                Forms\Components\TextInput::make('permit_number')->label(trans('main.permit_number'))
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('commercial_register_number')->label(trans('main.commercial_register_number'))
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('added_value_tax_number')->label(trans('main.tax_number'))
                    ->required()
                    ->numeric(),
                Forms\Components\FileUpload::make('logo')->label(trans('main.logo'))
                    ->required(),
                Forms\Components\FileUpload::make('stamp')->label(trans('main.stamp'))
                    ->required(),
                // Forms\Components\TextInput::make('new_registration_number_start')->label(trans('main.title'))
                //     ->required()
                //     ->maxLength(255),
            ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('website')
                    ->searchable(),
                Tables\Columns\TextColumn::make('permit_number')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('commercial_register_number')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('added_value_tax_number')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('logo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('stamp')
                    ->searchable(),
                Tables\Columns\TextColumn::make('new_registration_number_start')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
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
            'index' => Pages\ListSchoolSettings::route('/'),
            'create' => Pages\CreateSchoolSetting::route('/create'),
            'edit' => Pages\EditSchoolSetting::route('/{record}/edit'),
        ];
    }
}
