<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anlagenleistung extends Model
{
    use HasFactory;

    protected $table = 'usv-leistungen';

    public function anlage(): BelongsTo
    {
        return $this->belongsTo(Anlage::class, 'id_usv', 'id');
    }
}
