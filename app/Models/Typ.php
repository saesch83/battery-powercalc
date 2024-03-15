<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Typ extends Model
{
    use HasFactory;

    protected $table = 'batterietyp';

    public function leistungen(): HasMany
    {
        return $this->hasMany(Leistung::class, 'batterietyp_id', 'id');
    }

    public function hersteller(): BelongsTo
    {
        return $this->belongsTo(Hersteller::class, 'id_hersteller', 'id');
    }
}
