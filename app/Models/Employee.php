<?php

namespace App\Models;

use Filament\Panel;
use App\Models\User;
use App\Models\Department;
use App\Models\Designation;
use App\Models\EmploymentDuration;
use Filament\Models\Contracts\HasName;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Models\Contracts\FilamentUser;
use Spatie\Permission\Traits\HasPermissions;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends Model implements FilamentUser, HasName
{
    use HasFactory,HasRoles,HasPermissions;
    protected $guard_name = 'web';
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
    public function getFilamentName(): string
    {
        return $this->username;
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'user_id',
        'first_name',
        'middle_name',
        'third_name',
        'last_name',
        // 'department_id',
        // 'designation_id',
        'gender',
        'joining_date',
        'nationality',
        'identity_type',
        'identity_expire_date',
        //new attr
        'birth_date',
        'social_status',
        'study_degree',
        'study_speciality',
        'national_address',
        'iban',
        'documents',
        'termination_reason',
        'termination_date',
        'termination_document',
        'terminated_by',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        // 'department_id' => 'integer',
        // 'designation_id' => 'integer',
        'joining_date' => 'date',
        'identiry_expire_date' => 'date',
        'birth_date' => 'date',
        'termination_date' => 'date',
        'terminated_by' => 'integer',
        'documents' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class,'user_id','id');
    }

    public function employmentDuration(): HasMany
    {
        return $this->hasMany(EmploymentDuration::class);
    }
    // public function department(): BelongsTo
    // {
    //     return $this->belongsTo(Department::class);
    // }

    // public function designation(): BelongsTo
    // {
    //     return $this->belongsTo(Designation::class)->whereIsActive(true);
    // }
}
