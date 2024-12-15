<?php

namespace App\Models;

use Filament\Panel;
use App\Models\Student;
use App\Models\Employee;
use App\Models\ParentModel;
use Illuminate\Support\Facades\Auth;
use Filament\Models\Contracts\HasName;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasOne;
use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasPermissions;

class User extends  Authenticatable implements FilamentUser, HasName
{
    use HasFactory, HasRoles,SoftDeletes, HasPermissions;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'national_id',
        'username',
        'email',
        'password',
        'is_admin',
        'phone_number',
        'gender',
        'deleted_at',
        'is_banned',
        'banned_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'password' => 'hashed',
        'is_admin' => 'boolean',
        'is_banned' => 'boolean',
    ];
    public function canAccessPanel(Panel $panel): bool
    {
        if($this->is_banned )
        {
            Notification::make()
                        ->title(trans('main.you_were_banned'))
                        ->icon('heroicon-o-document-text')
                        ->iconColor('info')
                        ->send();
        }
        return $this->is_banned == false;
    }
    public function username():Attribute
    {
           
            return Attribute::make(
                get: function ($value) {
                    if($this->student) return $this->student?->first_name .''. $this->student?->middle_name ;
                    if($this->employee) return $this->employee?->first_name .''. $this->employee?->last_name ;
                    if($this->parent) return $this->parent?->full_name ;
                    return $value;
                }
            );
    }
    public function getFilamentName(): string
    {
        return $this->username;
    }

    public function parent(): HasOne
    {
        return $this->hasOne(ParentModel::class,'user_id','id');
    }
    public function student(): HasOne
    {
        return $this->hasOne(Student::class,'user_id','id');
    }
    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class,'user_id','id');
    }

}
