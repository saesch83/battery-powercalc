<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Anlage extends Model
{
    use HasFactory;

    protected $table = 'usv-anlagen';

    public function anlagenleistungen(): HasMany
    {
        return $this->hasMany(Anlagenleistung::class, 'id_usv', 'id');
    }

    public function anlagenparameter(): HasMany
    {
        return $this->hasMany(Anlagenparameter::class, 'id_usv', 'id');
    }
}
