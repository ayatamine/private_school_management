<?php

namespace App\Filament\Parents\Resources;

use Closure;
use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\ParentModel;
use Filament\Resources\Resource;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Parents\Resources\ParentModelResource\Pages;
use App\Filament\Parents\Resources\ParentModelResource\RelationManagers;

class ParentModelResource extends Resource
{
    protected static ?string $model = ParentModel::class;

    protected static ?string $navigationIcon = 'icon-parents';
    protected static bool $isScopedToTenant = true;
    public static bool $shouldRegisterNavigation=false;
    public static function getModelLabel():string
    {
        return trans_choice('main.my_profile',1);
    }
    public static function getNavigationLabel():string
    {
        return trans_choice('main.my_profile',1);
    }
    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                  ->columns(2)
                  ->schema([   
                Forms\Components\TextInput::make('full_name')->label(trans('main.full_name'))
                    ->required()
                    ->maxLength(255),
                // Forms\Components\Select::make('relation')->label(trans('main.parent_relation'))
                //     ->options(
                //         [
                //             'father'=>trans('main.father'),'mother'=>trans('main.mother'),'brother'=>trans('main.brother'),'sister'=>trans('main.sister'),'guardian'=>trans('main.guardian'),'other'=>trans('main.other')
                //             ]
                //     )
                //     ->required(),
                Forms\Components\TextInput::make('national_id')->label(trans('main.national_id'))
                    ->required()
                    ->rules([
                        fn (ParentModel $parent): Closure => function (string $attribute, $value, Closure $fail) use ($parent) {
                            if($parent?->id)
                            {
                                if (User::whereNationalId($value)->whereNot('id',$parent->user_id)->first() ) {
                                    $fail(trans('main.national_id_used_before'));
                                }
                            }else{
                                if (User::whereNationalId($value)->first() ) {
                                    $fail(trans('main.national_id_used_before'));
                                }
                            }
                            
                        },
                    ])
                    ->maxLength(10),           
                Forms\Components\TextInput::make('phone_number')->label(trans('main.phone_number'))
                    ->required()
                    // ->unique(table:'users',ignoreRecord: true)
                    ->maxLength(13),   
                // Forms\Components\Select::make(name: 'gender')->label(trans('main.gender'))
                //     ->options(['male'=>trans('main.male'), 'id'=>trans('main.female')])
                //     ->required(),        
                Forms\Components\TextInput::make('email')->label(trans('main.email'))
                    ->email()     
                    ->maxLength(255),        
                Forms\Components\TextInput::make('password')->label(trans('main.password'))->hint(trans('main.you_can_change_password'))
                    ->maxLength(255)        
                    ->hiddenOn('view'),
                Forms\Components\Select::make('relation')->label(trans('main.parent_relation'))
                    ->options(
                        [
                            'father'=>trans('main.father'),'mother'=>trans('main.mother'),'brother'=>trans('main.brother'),'sister'=>trans('main.sister'),'guardian'=>trans('main.guardian'),'other'=>trans('main.other')
                            ]
                    )
                    ->disabled(),  
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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
            'index' => Pages\ListParentModels::route('/'),
            'create' => Pages\CreateParentModel::route('/create'),
            'edit' => Pages\EditParentModel::route('/{record}/edit'),
            'view' => Pages\ViewParentProfile::route('/{record}'),
        ];
    }
}
