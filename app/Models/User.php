<?php

namespace App\Models;

use App\Models\ParentModel;
use App\Models\Student;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends  Authenticatable
{
    use HasFactory;

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
    ];

    public function getNameAttribute()
    {
            if($this->student) return $this->student?->first_name .''. $this->student?->last_name ;
            if($this->employee) return $this->employee?->first_name .''. $this->employee?->last_name ;
            if($this->parent) return $this->parent?->first_name .''. $this->parent?->last_name ;
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
