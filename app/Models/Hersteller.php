<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hersteller extends Model
{
    use HasFactory;
    
    protected $table = 'batteriehersteller';

    public function typen(): HasMany
    {
        return $this->hasMany(Typ::class, 'id_hersteller', 'id');
    }
}
