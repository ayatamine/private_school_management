<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Section;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\UserResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;

class UserResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    public static function getNavigationGroup():string
    {
        return trans('main.users_settings');
    }
    public static function getModelLabel():string
    {
        return trans_choice('main.user',1);
    }
    public static function getNavigationLabel():string
    {
        return trans_choice('main.user',2);
    }

    public static function getPluralModelLabel():string
    {
        return trans_choice('main.user',2);
    }
    public static function shouldRegisterNavigation(): bool
    {
        return  (employeeHasPermission('view_in_menu_user'));
    }
    public static function getPermissionPrefixes(): array
    {
        return [
            'view_in_menu',
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'ban',
        ];
    }
    public static function form(Form $form): Form
    {
      
        return $form
            ->schema([
                Section::make()
                    ->columns(2)
                    ->schema(
                        [
                            Forms\Components\TextInput::make('username')->label(trans('main.username')),
                            Forms\Components\TextInput::make('national_id')->label(trans('main.national_id'))
                                    ->unique(table:'users',ignoreRecord: true)
                                    ->maxLength(10)
                                    ->required(),
                            Forms\Components\TextInput::make('email')->label(trans('main.email'))
                                    ->unique(table:'users',ignoreRecord: true)
                                    ->required(),
                            Forms\Components\TextInput::make('phone_number')->label(trans('main.phone_number'))
                                    ->unique(table:'users',ignoreRecord: true)
                                    ->maxLength(13)
                                    ->required(),
                            Forms\Components\TextInput::make('password')->label(trans('main.password'))
                                    ->password(),
                            Forms\Components\Select::make(name: 'gender')->label(trans('main.gender'))
                                    ->options(['male'=>trans('main.male'), 'id'=>trans('main.female')])
                                    ->required(), 
                            // Forms\Components\CheckboxList::make('roles')
                            //     ->relationship('roles', 'name')
                            //     ->searchable(),
                            // Forms\Components\Select::make('permissions')
                            //     ->relationship('permissions', 'name')
                            //     ->multiple()
                            //     ->preload()
                            //     // ->formatStateUsing(fn($permission)=> trans(''))
                            //     ->columnSpanFull()
                            //     ->searchable(),
                        ]
                    )
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(User::whereDoesntHave('parent')->whereDoesntHave('student')->whereDoesntHave('employee'))
            ->columns([
                Tables\Columns\TextColumn::make('username')->label(trans('main.username'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('national_id')->label(trans('main.national_id'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')->label(trans('main.email'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone_number')->label(trans('main.phone_number'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('gender')->label(trans('main.gender'))
                    ->formatStateUsing(fn (string $state) => trans("main.$state"))
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')->label(trans('main.registration_date'))
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('ban_user')
                ->visible(fn(User $record)=>($record->is_banned == null &&  (employeeHasPermission('ban_user'))))
                ->label(trans('main.ban_user'))
                ->icon('icon-close')
                ->color('danger')
                ->requiresConfirmation()
                ->action(function(User $record){
                    $record->update([
                        'is_banned'=>true,
                        'banned_at'=>now(),
                    ]);

                    Notification::make()
                        ->title(trans('main.user_banned_successfully'))
                        ->icon('heroicon-o-document-text')
                        ->iconColor('success')
                        ->send();
                }),
                Tables\Actions\Action::make('unban_user')
                ->visible(fn(User $record)=>($record->is_banned != null && employeeHasPermission('unban_user')))
                ->label(trans('main.unban_user'))
                ->icon('heroicon-o-check')
                ->color('success')
                ->requiresConfirmation()
                ->action(function(User $record){
                    $record->update([
                        'is_banned'=>false,
                        'banned_at'=>null,
                    ]);

                    Notification::make()
                        ->title(trans('main.user_unbanned_successfully'))
                        ->icon('heroicon-o-document-text')
                        ->iconColor('success')
                        ->send();
                }),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                FilamentExportBulkAction::make('export')->label(trans('main.print'))->color('info')
                ->extraViewData([
                    'table_header' => trans('main.menu').' '.trans_choice('main.tuition_fee',2)
                ])->disableXlsx(),
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
            'view' => Pages\ViewUser::route('/{record}'),
        ];
    }
   
}
