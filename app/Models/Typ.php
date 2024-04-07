<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Typ extends Model
{
    use HasFactory;

    protected $table = 'batterietyp';

    protected $appends = ['hersteller'];

    public function getHerstellerAttribute() {
        return Hersteller::find($this->id_hersteller);
    }

    public function leistungen(): HasMany
    {
        return $this->hasMany(Leistung::class, 'batterietyp_id', 'id');
    }

    public function hersteller(): BelongsTo
    {
        return $this->belongsTo(Hersteller::class, 'id_hersteller', 'id');
    }
}
