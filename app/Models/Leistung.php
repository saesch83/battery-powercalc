<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Leistung extends Model
{
    use HasFactory;

    protected $table = 'batterieleistungen';

    public function typ(): BelongsTo
    {
        return $this->belongsTo(Typ::class, 'batterietyp_id', 'id');
    }
}
