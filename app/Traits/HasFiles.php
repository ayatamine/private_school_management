<?php

namespace App\Traits;

use App\Models\File;
use Illuminate\Database\Eloquent\Relations\MorphMany;


Trait HasFiles
{
    public function files() :MorphMany {
        return $this->morphMany(File::class, 'fileable'); 
    }
}