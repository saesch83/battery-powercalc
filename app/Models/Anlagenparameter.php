<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Anlagenparameter extends Model
{
    use HasFactory;

    protected $table = 'usv-batterieparameter';

    public function anlage(): BelongsTo
    {
        return $this->belongsTo(Anlage::class, 'id_usv', 'id');
    }
}
