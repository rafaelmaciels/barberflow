<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlockedTime extends Model
{
    use HasFactory;

    protected $fillable = [
        'barber_id',
        'date',
        'start_time',
        'end_time',
        'reason',
    ];

    /**
     * O bloqueio pode pertencer a um barbeiro específico.
     * Se barber_id for null, o bloqueio se aplica a todos (barbearia fechada).
     */
    public function barber()
    {
        return $this->belongsTo(Barber::class);
    }
}
