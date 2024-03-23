<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Anlage;

class Anlagenleistung extends Model
{
    use HasFactory;

    protected $table = 'usv-leistungen';

    public function anlage(): BelongsTo
    {
        return $this->belongsTo(Anlage::class, 'id_usv', 'id');
    }

    public function getAnlageAttribute() {
        return Anlage::find($this->id_usv);
    }

    protected $appends = ['anlage'];
}
